<?php

use App\Models\Event;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Carbon;
use Laravel\Jetstream\Features;

test('user can be deleted when they own a team with events and related data', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    // Create event with the team
    $event = Event::factory()->create([
        'team_id' => $team->id,
        'created_by_id' => $user->id,
        'date_from' => Carbon::today(),
        'date_to' => Carbon::today()->addDays(2),
    ]);

    // Create recipe owned by the team
    $recipe = Recipe::factory()->create([
        'team_id' => $team->id,
    ]);

    // Create meal linked to event and recipe
    $meal = new Meal([
        'title' => 'Breakfast',
        'date' => Carbon::today(),
    ]);
    $meal->event()->associate($event);
    $meal->save();
    $meal->recipes()->attach($recipe->id);

    // Create participant group owned by the team
    $participantGroup = ParticipantGroup::factory()->create([
        'team_id' => $team->id,
    ]);
    $event->participantGroups()->attach($participantGroup->id, ['quantity' => 10]);

    // Attempt to delete user
    app(\Laravel\Jetstream\Contracts\DeletesUsers::class)->delete($user);

    // Assert user and all related data are deleted
    expect($user->fresh())->toBeNull();
    expect($team->fresh())->toBeNull();
    expect($event->fresh())->toBeNull();
    expect($recipe->fresh())->toBeNull();
    expect($meal->fresh())->toBeNull();
    expect($participantGroup->fresh())->toBeNull();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('user can be deleted when they own multiple teams with data', function () {
    $user = User::factory()->withPersonalTeam()->create();

    // Create second team
    $secondTeam = Team::factory()->create([
        'user_id' => $user->id,
        'personal_team' => false,
    ]);

    // Create events in both teams
    $event1 = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
    ]);

    $event2 = Event::factory()->create([
        'team_id' => $secondTeam->id,
        'created_by_id' => $user->id,
    ]);

    // Create recipes in both teams
    $recipe1 = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);
    $recipe2 = Recipe::factory()->create(['team_id' => $secondTeam->id]);

    // Delete user
    app(\Laravel\Jetstream\Contracts\DeletesUsers::class)->delete($user);

    // Assert everything is deleted
    expect($user->fresh())->toBeNull();
    expect($user->currentTeam->fresh())->toBeNull();
    expect($secondTeam->fresh())->toBeNull();
    expect($event1->fresh())->toBeNull();
    expect($event2->fresh())->toBeNull();
    expect($recipe1->fresh())->toBeNull();
    expect($recipe2->fresh())->toBeNull();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

test('user can be deleted when team has no events or recipes', function () {
    $user = User::factory()->withPersonalTeam()->create();
    $team = $user->currentTeam;

    // Delete user (team has no events, recipes, or participant groups)
    app(\Laravel\Jetstream\Contracts\DeletesUsers::class)->delete($user);

    // Assert user and team are deleted
    expect($user->fresh())->toBeNull();
    expect($team->fresh())->toBeNull();
})->skip(function () {
    return ! Features::hasAccountDeletionFeatures();
}, 'Account deletion is not enabled.');

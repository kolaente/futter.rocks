<?php

use App\Models\Event;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
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

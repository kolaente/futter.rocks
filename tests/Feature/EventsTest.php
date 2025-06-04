<?php

use App\Livewire\Events\View;
use App\Models\Event;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\ShoppingTour;
use App\Models\User;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

it('can duplicate an event with all associated data', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
    ]);

    $group = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);
    $event->participantGroups()->attach($group->id, ['quantity' => 5]);

    $recipe1 = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);
    $recipe2 = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);

    $meal = new Meal([
        'title' => 'Breakfast',
        'date' => Carbon::today(),
    ]);
    $meal->event()->associate($event);
    $meal->save();
    $meal->recipes()->attach([$recipe1->id, $recipe2->id]);

    $tour = new ShoppingTour(['date' => Carbon::today()]);
    $tour->event()->associate($event);
    $tour->save();

    livewire(View::class, ['event' => $event])
        ->callAction('duplicate')
        ->assertHasNoActionErrors();

    assertDatabaseCount('events', 2);

    $newEvent = Event::orderBy('id', 'desc')->first();
    expect($newEvent->id)->not->toBe($event->id);
    expect($newEvent->title)->toBe($event->title.__(' - Duplicate'));

    assertDatabaseHas('event_participant_group', [
        'event_id' => $newEvent->id,
        'participant_group_id' => $group->id,
        'quantity' => 5,
    ]);

    assertDatabaseHas('meals', [
        'event_id' => $newEvent->id,
        'title' => 'Breakfast',
    ]);

    $newMeal = Meal::where('event_id', $newEvent->id)->first();
    assertDatabaseHas('meal_recipe', [
        'meal_id' => $newMeal->id,
        'recipe_id' => $recipe1->id,
    ]);
    assertDatabaseHas('shopping_tours', [
        'event_id' => $newEvent->id,
    ]);
});

<?php

use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\Ingredient;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('calculates ingredients for an event with proper units', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
    ]);

    $group1 = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 1,
    ]);
    $group2 = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 2,
    ]);

    $event->participantGroups()->attach($group1, ['quantity' => 10]);
    $event->participantGroups()->attach($group2, ['quantity' => 5]);

    $recipe = Recipe::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $ingGrams = Ingredient::factory()->createQuietly();
    $ingPieces = Ingredient::factory()->createQuietly();

    $recipe->ingredients()->attach($ingGrams->id, [
        'quantity' => 100,
        'unit' => Unit::Grams,
    ]);
    $recipe->ingredients()->attach($ingPieces->id, [
        'quantity' => 1,
        'unit' => Unit::Pieces,
    ]);

    $recipe->load('ingredients');
    foreach ($recipe->ingredients as $ingredient) {
        $ingredient->unit = $ingredient->pivot->unit;
    }

    $calculated = $recipe->getCalculatedIngredientsForEvent($event);

    $gramsItem = collect($calculated)->firstWhere('ingredient.id', $ingGrams->id);
    expect($gramsItem['quantity'])->toBe(2.0)
        ->and($gramsItem['ingredient']->unit)->toBe(Unit::Grams);

    $piecesItem = collect($calculated)->firstWhere('ingredient.id', $ingPieces->id);
    expect($piecesItem['quantity'])->toBe(20.0)
        ->and($piecesItem['ingredient']->unit)->toBe(Unit::Pieces);
});

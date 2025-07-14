<?php

use App\Models\Enums\IngredientCategory;
use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\ShoppingTour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('puts shelf stable ingredients into first shopping tour', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
        'date_from' => '2024-01-01',
        'date_to' => '2024-01-03',
    ]);

    $tour = new ShoppingTour(['date' => '2024-01-02']);
    $tour->event()->associate($event);
    $tour->save();

    $group = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 1,
    ]);
    $event->participantGroups()->attach($group->id, ['quantity' => 1]);

    $ingredient = Ingredient::factory()->createQuietly([
        'category' => IngredientCategory::OTHER,
    ]);

    $recipe = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);
    $recipe->ingredients()->attach($ingredient->id, [
        'quantity' => 1,
        'unit' => Unit::Pieces,
    ]);

    $meal = new Meal([
        'title' => 'Dinner',
        'date' => '2024-01-03',
    ]);
    $meal->event()->associate($event);
    $meal->save();
    $meal->recipes()->attach($recipe->id);

    $list = $event->fresh()->getShoppingList();

    expect(isset($list[0]))->toBeTrue();
    $found = collect($list[0][IngredientCategory::OTHER->value])
        ->firstWhere('ingredient.id', $ingredient->id);
    expect($found['quantity'])->toBe(1.0);
    $tourList = $list[$tour->id] ?? [];
    $foundLater = collect($tourList[IngredientCategory::OTHER->value] ?? [])
        ->firstWhere('ingredient.id', $ingredient->id);
    expect($foundLater)->toBeNull();
});

it('respects fresh ingredient attribute setting when enabled', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
        'date_from' => '2024-01-01',
        'date_to' => '2024-01-03',
        'use_fresh_ingredient_attribute' => true,
    ]);

    $tour = new ShoppingTour(['date' => '2024-01-02']);
    $tour->event()->associate($event);
    $tour->save();

    $group = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 1,
    ]);
    $event->participantGroups()->attach($group->id, ['quantity' => 1]);

    $freshIngredient = Ingredient::factory()->createQuietly([
        'category' => IngredientCategory::DAIRY_EGGS,
    ]);

    $recipe = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);
    $recipe->ingredients()->attach($freshIngredient->id, [
        'quantity' => 1,
        'unit' => Unit::Pieces,
    ]);

    $meal = new Meal([
        'title' => 'Dinner',
        'date' => '2024-01-03',
    ]);
    $meal->event()->associate($event);
    $meal->save();
    $meal->recipes()->attach($recipe->id);

    $list = $event->fresh()->getShoppingList();

    expect(isset($list[$tour->id]))->toBeTrue();
    $found = collect($list[$tour->id][IngredientCategory::DAIRY_EGGS->value])
        ->firstWhere('ingredient.id', $freshIngredient->id);
    expect($found['quantity'])->toBe(1.0);

    $firstTourList = $list[0] ?? [];
    $foundInFirstTour = collect($firstTourList[IngredientCategory::DAIRY_EGGS->value] ?? [])
        ->firstWhere('ingredient.id', $freshIngredient->id);
    expect($foundInFirstTour)->toBeNull();
});

it('ignores fresh ingredient attribute when disabled', function () {
    $user = User::factory()->withCurrentTeam()->create();
    actingAs($user);

    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
        'date_from' => '2024-01-01',
        'date_to' => '2024-01-03',
        'use_fresh_ingredient_attribute' => false,
    ]);

    $tour = new ShoppingTour(['date' => '2024-01-02']);
    $tour->event()->associate($event);
    $tour->save();

    $group = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 1,
    ]);
    $event->participantGroups()->attach($group->id, ['quantity' => 1]);

    $freshIngredient = Ingredient::factory()->createQuietly([
        'category' => IngredientCategory::DAIRY_EGGS,
    ]);

    $recipe = Recipe::factory()->create(['team_id' => $user->currentTeam->id]);
    $recipe->ingredients()->attach($freshIngredient->id, [
        'quantity' => 1,
        'unit' => Unit::Pieces,
    ]);

    $meal = new Meal([
        'title' => 'Dinner',
        'date' => '2024-01-03',
    ]);
    $meal->event()->associate($event);
    $meal->save();
    $meal->recipes()->attach($recipe->id);

    $list = $event->fresh()->getShoppingList();

    expect(isset($list[0]))->toBeTrue();
    $found = collect($list[0][IngredientCategory::DAIRY_EGGS->value])
        ->firstWhere('ingredient.id', $freshIngredient->id);
    expect($found['quantity'])->toBe(1.0);

    $tourList = $list[$tour->id] ?? [];
    $foundLater = collect($tourList[IngredientCategory::DAIRY_EGGS->value] ?? [])
        ->firstWhere('ingredient.id', $freshIngredient->id);
    expect($foundLater)->toBeNull();
});

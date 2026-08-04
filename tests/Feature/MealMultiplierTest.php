<?php

use App\Livewire\Events\ListMeals;
use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

uses(RefreshDatabase::class);

function createEventWithRecipe(User $user): array
{
    $event = Event::factory()->create([
        'team_id' => $user->currentTeam->id,
        'created_by_id' => $user->id,
    ]);

    $group = ParticipantGroup::factory()->create([
        'team_id' => $user->currentTeam->id,
        'food_factor' => 1,
    ]);
    $event->participantGroups()->attach($group, ['quantity' => 20]);

    $recipe = Recipe::factory()->create([
        'team_id' => $user->currentTeam->id,
        'servings' => 4,
    ]);

    $ingredient = Ingredient::factory()->createQuietly()->fresh();
    $recipe->ingredients()->attach($ingredient->id, [
        'quantity' => 100,
        'unit' => Unit::Grams,
    ]);

    return [$event, $recipe, $ingredient];
}

function createMealFor(Event $event, Recipe $recipe, ?float $multiplier = null): Meal
{
    $meal = new Meal([
        'title' => 'Lunch',
        'date' => $event->date_from,
        'multiplier' => $multiplier,
    ]);
    $meal->event_id = $event->id;
    $meal->save();
    $meal->recipes()->attach($recipe);

    return $meal;
}

function shoppingListQuantity(Event $event, Ingredient $ingredient): float
{
    $list = $event->fresh()->getShoppingList();

    return collect($list[0][$ingredient->category->value])
        ->firstWhere('ingredient.id', $ingredient->id)['quantity'];
}

describe('Meal multiplier', function () {

    it('scales shopping list quantities by meal multiplier', function (float $multiplier, float $expected) {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        [$event, $recipe, $ingredient] = createEventWithRecipe($user);
        createMealFor($event, $recipe, $multiplier);

        // baseline without multiplier: 100 g * 20 people / 4 servings = 500 g
        expect(shoppingListQuantity($event, $ingredient))->toBe($expected);
    })->with([
        'doubles' => [2.0, 1000.0],
        'halves' => [0.5, 250.0],
    ]);

    it('treats null and explicit 1 identically in shopping list', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        [$event, $recipe, $ingredient] = createEventWithRecipe($user);
        $meal = createMealFor($event, $recipe, null);

        $withNull = shoppingListQuantity($event, $ingredient);

        $meal->update(['multiplier' => 1]);

        expect(shoppingListQuantity($event, $ingredient))->toBe($withNull);
    });

    it('scales only the contribution of the meal with a multiplier', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        [$event, $recipe, $ingredient] = createEventWithRecipe($user);
        createMealFor($event, $recipe, 0.5);
        createMealFor($event, $recipe, null);

        // 500 g * 0.5 + 500 g = 750 g
        expect(shoppingListQuantity($event, $ingredient))->toBe(750.0);
    });

    it('respects meal multiplier in calculated recipe ingredients', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        [$event, $recipe, $ingredient] = createEventWithRecipe($user);
        $meal = createMealFor($event, $recipe, 2);

        $calculated = $recipe->getCalculatedIngredientsForEvent($event, $meal);

        $item = collect($calculated)->firstWhere('ingredient.id', $ingredient->id);
        expect($item['quantity'])->toBe(1000.0)
            ->and($item['unit'])->toBe(Unit::Grams);
    });

    it('calculates without meal argument as before', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        [$event, $recipe, $ingredient] = createEventWithRecipe($user);
        createMealFor($event, $recipe, 2);

        $calculated = $recipe->getCalculatedIngredientsForEvent($event);

        $item = collect($calculated)->firstWhere('ingredient.id', $ingredient->id);
        expect($item['quantity'])->toBe(500.0);
    });

    describe('form', function () {

        it('saves the multiplier', function () {
            $user = User::factory()->withCurrentTeam()->create();
            actingAs($user);

            [$event, $recipe] = createEventWithRecipe($user);

            livewire(ListMeals::class, ['event' => $event])
                ->callTableAction('create', data: [
                    'title' => 'Dinner',
                    'date' => $event->date_from->format('Y-m-d'),
                    'multiplier' => 1.5,
                    'recipes' => [$recipe->id],
                ])
                ->assertHasNoTableActionErrors();

            assertDatabaseHas('meals', [
                'event_id' => $event->id,
                'title' => 'Dinner',
                'multiplier' => 1.5,
            ]);
        });

        it('stores empty multiplier as null', function () {
            $user = User::factory()->withCurrentTeam()->create();
            actingAs($user);

            [$event, $recipe] = createEventWithRecipe($user);

            livewire(ListMeals::class, ['event' => $event])
                ->callTableAction('create', data: [
                    'title' => 'Dinner',
                    'date' => $event->date_from->format('Y-m-d'),
                    'multiplier' => null,
                    'recipes' => [$recipe->id],
                ])
                ->assertHasNoTableActionErrors();

            assertDatabaseHas('meals', [
                'event_id' => $event->id,
                'title' => 'Dinner',
                'multiplier' => null,
            ]);
        });

        it('rejects invalid multipliers', function (mixed $value) {
            $user = User::factory()->withCurrentTeam()->create();
            actingAs($user);

            [$event, $recipe] = createEventWithRecipe($user);

            livewire(ListMeals::class, ['event' => $event])
                ->callTableAction('create', data: [
                    'title' => 'Dinner',
                    'date' => $event->date_from->format('Y-m-d'),
                    'multiplier' => $value,
                    'recipes' => [$recipe->id],
                ])
                ->assertHasTableActionErrors(['multiplier']);
        })->with([
            'zero' => [0],
            'negative' => [-1],
        ]);
    });
});

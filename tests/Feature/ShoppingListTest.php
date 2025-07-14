<?php

use App\Models\AdditionalShoppingItem;
use App\Models\Enums\IngredientCategory;
use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

describe('Shopping list', function () {

    it('generates shopping lists for seeded events', function () {
        $this->seed(DatabaseSeeder::class);

        $user = User::first();
        actingAs($user);

        $event = Event::first();
        $tourIds = $event->shoppingTours()->orderBy('date')->pluck('id')->toArray();

        $list = $event->fresh()->getShoppingList();

        $first = collect($list[0][IngredientCategory::BAKERY->value])
            ->firstWhere('ingredient.title', 'Brot');
        $second = collect($list[$tourIds[0]][IngredientCategory::BAKERY->value])
            ->firstWhere('ingredient.title', 'Brot');
        $third = collect($list[$tourIds[1]][IngredientCategory::BAKERY->value])
            ->firstWhere('ingredient.title', 'Brot');

        expect($first['quantity'])->toBe(18.2)
            ->and($second['quantity'])->toBe(15.6)
            ->and($third['quantity'])->toBe(13.9);
    });

    it('includes manual shopping items in shopping list', function () {
        $user = User::factory()->withCurrentTeam()->create();
        actingAs($user);

        $event = Event::factory()->create([
            'team_id' => $user->currentTeam->id,
            'created_by_id' => $user->id,
        ]);

        $tour = $event->shoppingTours()->create(['date' => $event->date_from]);

        AdditionalShoppingItem::factory()->createQuietly([
            'event_id' => $event->id,
            'shopping_tour_id' => $tour->id,
            'title' => 'Tape',
            'quantity' => 2,
            'unit' => Unit::Pieces,
            'category' => IngredientCategory::OTHER,
        ]);

        AdditionalShoppingItem::factory()->createQuietly([
            'event_id' => $event->id,
            'shopping_tour_id' => null,
            'title' => 'Rope',
            'quantity' => 3,
            'unit' => Unit::Pieces,
            'category' => IngredientCategory::OTHER,
        ]);

        $list = $event->fresh()->getShoppingList();

        $withTour = collect($list[$tour->id][IngredientCategory::OTHER->value])
            ->firstWhere('ingredient.title', 'Tape');
        $withoutTour = collect($list[0][IngredientCategory::OTHER->value])
            ->firstWhere('ingredient.title', 'Rope');

        expect($withTour['quantity'])->toBe(2.0)
            ->and($withoutTour['quantity'])->toBe(3.0);
    });

    describe('Freshness', function () {

        beforeEach(function () {
            $this->seed();
        });

        it('respects fresh ingredient attribute setting when enabled', function () {
            // Use the seeded test user
            $user = User::where('email', 'test@example.com')->first();
            actingAs($user);

            // Use the seeded Sommerlager event and enable fresh ingredient attribute
            $event = Event::where('title', 'Sommerlager')->first();
            $event->update(['use_fresh_ingredient_attribute' => true]);

            // Get the seeded shopping tours
            $tours = $event->shoppingTours()->orderBy('date')->get();

            $list = $event->fresh()->getShoppingList();

            // Check that fresh ingredients are distributed to shopping tours closer to meal dates
            $freshCategories = [
                IngredientCategory::BAKERY,
                IngredientCategory::DAIRY_EGGS,
                IngredientCategory::FROZEN,
                IngredientCategory::FRUIT_VEGETABLES,
                IngredientCategory::MEAT_SEAFOOD,
                IngredientCategory::OTHER,
            ];

            // Find fresh ingredients in later shopping tours (not the first one)
            $foundFreshInLaterTours = false;

            foreach ($tours as $tour) {
                if (isset($list[$tour->id])) {
                    foreach ($freshCategories as $category) {
                        if (isset($list[$tour->id][$category->value]) && ! empty($list[$tour->id][$category->value])) {
                            $foundFreshInLaterTours = true;
                            break 2;
                        }
                    }
                }
            }

            expect($foundFreshInLaterTours)->toBeTrue();

            // Check that shelfable goods do not show up in later shopping tours
            $shelfableCategories = [
                IngredientCategory::BEVERAGES,
                IngredientCategory::SNACKS,
                IngredientCategory::CONDIMENTS,
                IngredientCategory::BAKING,
                IngredientCategory::SPICES,
                IngredientCategory::CANNED_GOODS,
                IngredientCategory::SPREAD,
                IngredientCategory::GRAINS_CEREALS,
            ];

            $foundShelfableInLaterTours = false;

            foreach ($tours->skip(1) as $tour) { // Skip first tour, check later ones
                if (isset($list[$tour->id])) {
                    foreach ($shelfableCategories as $category) {
                        if (isset($list[$tour->id][$category->value]) && ! empty($list[$tour->id][$category->value])) {
                            $foundShelfableInLaterTours = true;
                            break 2;
                        }
                    }
                }
            }

            expect($foundShelfableInLaterTours)->toBeFalse();
        });

        it('ignores fresh ingredient attribute when disabled', function () {
            // Use the seeded test user
            $user = User::where('email', 'test@example.com')->first();
            actingAs($user);

            // Use the seeded Sommerlager event and disable fresh ingredient attribute
            $event = Event::where('title', 'Sommerlager')->first();
            $event->update(['use_fresh_ingredient_attribute' => false]);

            // Get the seeded shopping tours
            $tours = $event->shoppingTours()->orderBy('date')->get();

            $list = $event->fresh()->getShoppingList();

            // When fresh ingredient attribute is disabled, all ingredients should go to first shopping tour
            expect(isset($list[0]))->toBeTrue();

            // Check that all ingredient categories appear in the first tour when freshness is ignored
            $allCategories = IngredientCategory::cases();
            $foundInFirstTour = false;

            foreach ($allCategories as $category) {
                if (isset($list[0][$category->value]) && ! empty($list[0][$category->value])) {
                    $foundInFirstTour = true;
                    break;
                }
            }

            expect($foundInFirstTour)->toBeTrue();
        });
    });
});

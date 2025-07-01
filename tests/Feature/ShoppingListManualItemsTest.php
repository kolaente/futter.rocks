<?php

use App\Models\AdditionalShoppingItem;
use App\Models\Enums\IngredientCategory;
use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\User;

use function Pest\Laravel\actingAs;

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

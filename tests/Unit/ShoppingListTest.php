<?php

use App\Models\Enums\IngredientCategory;
use App\Models\Event;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('generates shopping lists for seeded events', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::first();
    actingAs($user);

    $event = Event::first();
    $tourIds = $event->shoppingTours()->orderBy('date')->pluck('id')->toArray();

    $list = $event->fresh()->getShoppingList();

    $first = collect($list[0][IngredientCategory::FRUIT_VEGETABLES->value])
        ->firstWhere('ingredient.title', 'Brot');
    $second = collect($list[$tourIds[0]][IngredientCategory::FRUIT_VEGETABLES->value])
        ->firstWhere('ingredient.title', 'Brot');
    $third = collect($list[$tourIds[1]][IngredientCategory::FRUIT_VEGETABLES->value])
        ->firstWhere('ingredient.title', 'Brot');

    expect($first['quantity'])->toBe(18.2)
        ->and($second['quantity'])->toBe(15.6)
        ->and($third['quantity'])->toBe(13.9);
});

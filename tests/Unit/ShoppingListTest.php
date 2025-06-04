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

    $first = collect($list[0][IngredientCategory::OTHER->value])
        ->firstWhere('ingredient.title', 'Brot');
    $second = collect($list[$tourIds[0]][IngredientCategory::OTHER->value])
        ->firstWhere('ingredient.title', 'Brot');
    $third = collect($list[$tourIds[1]][IngredientCategory::OTHER->value])
        ->firstWhere('ingredient.title', 'Brot');

    expect($first['quantity'])->toBe(18191.25)
        ->and($second['quantity'])->toBe(15592.5)
        ->and($third['quantity'])->toBe(13860.0);
});

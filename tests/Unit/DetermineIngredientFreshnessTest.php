<?php

use App\Models\Enums\IngredientCategory;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('marks fresh categories as fresh', function () {
    $ingredient = Ingredient::factory()->createQuietly([
        'category' => IngredientCategory::DAIRY_EGGS,
    ]);

    expect($ingredient->is_fresh)->toBeTrue();
});

it('marks shelf-stable categories as not fresh', function () {
    $ingredient = Ingredient::factory()->createQuietly([
        'category' => IngredientCategory::CANNED_GOODS,
    ]);

    expect($ingredient->is_fresh)->toBeFalse();
});

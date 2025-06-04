<?php

use App\Models\Enums\Unit;
use App\Utils\RoundIngredients;

it('converts grams to kilos when quantity exceeds one kilo', function () {
    $ingredient = new stdClass;
    $ingredient->unit = Unit::Grams;

    $rounded = RoundIngredients::round([
        'ingredient' => $ingredient,
        'quantity' => 1500,
    ]);

    expect($rounded['quantity'])->toBe(1.5)
        ->and($rounded['ingredient']->unit)->toBe(Unit::Kilos);
});

it('rounds gram quantities to the nearest ten under one kilo', function () {
    $ingredient = new stdClass;
    $ingredient->unit = Unit::Grams;

    $rounded = RoundIngredients::round([
        'ingredient' => $ingredient,
        'quantity' => 543,
    ]);

    expect($rounded['quantity'])->toBe(540.0)
        ->and($rounded['ingredient']->unit)->toBe(Unit::Grams);
});

it('converts milliliters to liters when quantity exceeds one liter', function () {
    $ingredient = new stdClass;
    $ingredient->unit = Unit::Milliliters;

    $rounded = RoundIngredients::round([
        'ingredient' => $ingredient,
        'quantity' => 1250,
    ]);

    expect($rounded['quantity'])->toBe(1.3)
        ->and($rounded['ingredient']->unit)->toBe(Unit::Liters);
});

it('rounds piece quantities above five up to halves', function () {
    $ingredient = new stdClass;
    $ingredient->unit = Unit::Pieces;

    $rounded = RoundIngredients::round([
        'ingredient' => $ingredient,
        'quantity' => 7.2,
    ]);

    expect($rounded['quantity'])->toBe(7.5)
        ->and($rounded['ingredient']->unit)->toBe(Unit::Pieces);
});

it('rounds small piece quantities to one decimal place', function () {
    $ingredient = new stdClass;
    $ingredient->unit = Unit::Pieces;

    $rounded = RoundIngredients::round([
        'ingredient' => $ingredient,
        'quantity' => 4.24,
    ]);

    expect($rounded['quantity'])->toBe(4.2)
        ->and($rounded['ingredient']->unit)->toBe(Unit::Pieces);
});

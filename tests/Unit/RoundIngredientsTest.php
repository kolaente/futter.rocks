<?php

use App\Models\Enums\Unit;
use App\Utils\RoundIngredients;

it('converts grams to kilos when quantity exceeds one kilo', function () {
    $rounded = RoundIngredients::round([
        'unit' => Unit::Grams,
        'quantity' => 1500,
    ]);

    expect($rounded['quantity'])->toBe(1.5)
        ->and($rounded['unit'])->toBe(Unit::Kilos);
});

it('rounds gram quantities to the nearest ten under one kilo', function () {
    $rounded = RoundIngredients::round([
        'unit' => Unit::Grams,
        'quantity' => 543,
    ]);

    expect($rounded['quantity'])->toBe(540.0)
        ->and($rounded['unit'])->toBe(Unit::Grams);
});

it('converts milliliters to liters when quantity exceeds one liter', function () {
    $rounded = RoundIngredients::round([
        'unit' => Unit::Milliliters,
        'quantity' => 1250,
    ]);

    expect($rounded['quantity'])->toBe(1.3)
        ->and($rounded['unit'])->toBe(Unit::Liters);
});

it('rounds piece quantities above five up to halves', function () {
    $rounded = RoundIngredients::round([
        'unit' => Unit::Pieces,
        'quantity' => 7.2,
    ]);

    expect($rounded['quantity'])->toBe(7.5)
        ->and($rounded['unit'])->toBe(Unit::Pieces);
});

it('rounds small piece quantities to one decimal place', function () {
    $rounded = RoundIngredients::round([
        'unit' => Unit::Pieces,
        'quantity' => 4.24,
    ]);

    expect($rounded['quantity'])->toBe(4.2)
        ->and($rounded['unit'])->toBe(Unit::Pieces);
});

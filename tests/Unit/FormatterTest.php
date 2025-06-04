<?php

use App\Formatter;
use Illuminate\Support\Facades\App;

uses(Tests\TestCase::class);

it('formats numbers according to the app locale', function () {
    App::setLocale('de');

    $formatter = new Formatter;

    expect($formatter->format(1234.56))->toBe('1.234,56');
});

it('formats numbers for the en locale', function () {
    App::setLocale('en');

    $formatter = new Formatter;

    expect($formatter->format(1234.56))->toBe('1,234.56');
});

it('reuses the same NumberFormatter instance', function () {
    $formatter = new Formatter;

    $instanceOne = $formatter->getFormatter();
    $instanceTwo = $formatter->getFormatter();

    expect($instanceOne)->toBe($instanceTwo);
});

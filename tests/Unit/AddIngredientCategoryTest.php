<?php

use App\Jobs\AddIngredientCategory;
use App\Models\Enums\IngredientCategory;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('assigns category returned by the service', function () {
    config(['app.google_api_key' => 'test']);

    Http::fake([
        'https://generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'DAIRY_EGGS'],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $ingredient = Ingredient::factory()->createQuietly(['title' => 'milk']);
    $ingredient->refresh();

    expect($ingredient->category)->toBe(IngredientCategory::OTHER);

    $job = new AddIngredientCategory($ingredient);

    expect($job->backoff)->toBe([30, 120]);

    $job->handle();
    $ingredient->refresh();

    expect($ingredient->category)->toBe(IngredientCategory::DAIRY_EGGS);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'models/gemini-3.6-flash:generateContent')
            && $request['generationConfig']['thinkingConfig'] === ['thinkingLevel' => 'minimal'];
    });
});

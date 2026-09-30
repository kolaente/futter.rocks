<?php

use App\Jobs\AddIngredientCategory;
use App\Models\Enums\IngredientCategory;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Choice;
use Laravel\Ai\Prompts\ClassificationPrompt;
use Laravel\Ai\Responses\Data\ChoiceAnswer;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('assigns category returned by the classifier', function () {
    Classification::fake(fn () => [
        'category' => new ChoiceAnswer('DAIRY_EGGS', ['DAIRY_EGGS' => 0.9, 'OTHER' => 0.1], 0.9),
    ]);

    $ingredient = Ingredient::factory()->createQuietly(['title' => 'milk']);
    $ingredient->refresh();

    expect($ingredient->category)->toBe(IngredientCategory::OTHER);

    $job = new AddIngredientCategory($ingredient);

    expect($job->backoff)->toBe([30, 120]);

    $job->handle();
    $ingredient->refresh();

    expect($ingredient->category)->toBe(IngredientCategory::DAIRY_EGGS);

    Classification::assertClassified(function (ClassificationPrompt $prompt) {
        $question = $prompt->questions['category'];

        return $prompt->state === 'milk'
            && $prompt->provider->name() === 'typesafe'
            && $question instanceof Choice
            && array_keys($question->options) === array_column(IngredientCategory::cases(), 'name');
    });
});

<?php

namespace App\Services;

use App\Models\Enums\IngredientCategory;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IngredientCategoryService
{
    public function getCategory(string $ingredient): IngredientCategory
    {
        $names = [];
        $bullets = '';
        foreach (IngredientCategory::cases() as $case) {
            $names[] = $case->name;
            $bullets .= '- '.$case->name.': '.$case->getPromptHint()."\n";
        }

        $systemPrompt = <<<EOT
You are an expert in food categorization for a shopping list. Classify the given food ingredient into exactly one of the categories below. Ingredient names will be in German or English; classify based on the underlying food.

{$bullets}
Common pantry staples almost always fit a specific category — prefer it over OTHER.
EOT;

        $apiKey = config('app.google_api_key');

        if (empty($apiKey)) {
            throw new Exception('Google API key is not configured.');
        }

        $model = 'gemini-flash-latest';

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $ingredient]]],
            ],
            'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
            'generationConfig' => [
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
                'responseMimeType' => 'text/x.enum',
                'responseSchema' => [
                    'type' => 'STRING',
                    'enum' => $names,
                ],
            ],
        ];

        try {
            $response = Http::withOptions(['timeout' => 60])
                ->post('https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.$apiKey, $payload)
                ->throw();
        } catch (\Exception $e) {
            Log::error('Google AI API request failed', [
                'error' => $e->getMessage(),
                'model' => $model,
                'ingredient' => $ingredient,
            ]);
            throw $e;
        }

        $name = trim((string) $response->json('candidates.0.content.parts.0.text'));

        if ($name === '') {
            Log::error('Google AI response format error or empty content.', ['response' => $response->body()]);

            return IngredientCategory::OTHER;
        }

        foreach (IngredientCategory::cases() as $case) {
            if ($case->name === $name) {
                return $case;
            }
        }

        Log::warning('Google AI returned unknown category name.', [
            'name' => $name,
            'ingredient' => $ingredient,
        ]);

        return IngredientCategory::OTHER;
    }
}

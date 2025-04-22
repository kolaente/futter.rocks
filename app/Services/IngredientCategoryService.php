<?php

namespace App\Services;

use App\Models\Enums\IngredientCategory;
use Illuminate\Support\Facades\Http;

class IngredientCategoryService
{
    public function getCategory(string $ingredient): IngredientCategory
    {
        $categoriesList = '';
        foreach (IngredientCategory::cases() as $case) {
            $categoriesList .= $case->name.' ('.$case->getLabel().') = '.$case->value."\n";
        }

        $systemPrompt = <<<EOT
You are a food expert. The user will provide you with an ingredient used in cooking a recipe.
Your task is to find the category of the ingredient from this list:

{$categoriesList}
Only reply with the category (using the number) of the ingredient, nothing else. No yapping. Reply with 0 if you're not sure what category the ingredient belongs to.
EOT;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.config('app.groq_api_key'),
        ])
            ->throw()
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $ingredient,
                    ],
                ],
                'model' => 'meta-llama/llama-4-maverick-17b-128e-instruct',
                'temperature' => 1,
                'max_completion_tokens' => 1024,
                'top_p' => 1,
                'stream' => false,
                'stop' => null,
            ]);

        $categoryValue = intval(trim($response->json('choices.0.message.content')));

        return IngredientCategory::tryFrom($categoryValue) ?? IngredientCategory::OTHER;
    }
}

<?php

namespace App\Services;

use App\Models\Enums\IngredientCategory;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IngredientCategoryService
{
    private function extractContentFromTags(string $input, string $tag): string
    {
        $pattern = "/<$tag>(.*?)<\/$tag>/s";
        if (preg_match($pattern, $input, $matches)) {
            return trim($matches[1]);
        }

        return '';
    }

    public function getCategory(string $ingredient): IngredientCategory
    {
        $categoriesList = '';
        foreach (IngredientCategory::cases() as $case) {
            $categoriesList .= $case->name.' ('.$case->getLabel().') = '.$case->value."\n";
        }

        $systemPrompt = <<<EOT
You are an expert in food categorization. Your task is to categorize a given food ingredient into one of the provided categories. If you're not entirely sure which category the ingredient belongs to, you should respond with "0".

Here is the list of categories:
<categories>
{$categoriesList}
</categories>

The food ingredient to categorize is:
<ingredient>{$ingredient}</ingredient>

Instructions:
1. Carefully review the list of categories provided.
2. Consider the given food ingredient and its characteristics.
3. Determine which category best fits the ingredient based on your expert knowledge.
4. If you're confident about the category, provide your answer in the format specified below.
5. If you're not entirely sure which category the ingredient belongs to, respond with "other".
6. Use the number of the category from the provided list in your response.

Provide your reasoning in the following format:
<reason>
[explain your reasoning for the chosen category here]
</reason>

Please provide your answer in the following format:
<category>
[Insert the chosen category or "other" here]
</category>

Only reply with the single category you're most sure of. Do not reply with multiple categories.

Remember, if you're not entirely sure about the categorization, it's better to respond with "0" rather than making an uncertain guess.
EOT;

        $model = 'gemini-2.5-flash-preview-04-17';
        $apiKey = config('app.google_api_key');

        if (empty($apiKey)) {
            throw new Exception('Google API key is not configured.');
        }

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $ingredient]]],
            ],
            'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
            'generationConfig' => [
                'thinkingConfig' => [
                    'thinkingBudget' => 0,
                ],
                'responseMimeType' => 'text/plain',
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

        $result = $response->json('candidates.0.content.parts.0.text');

        if (empty($result)) {
            Log::error('Google AI response format error or empty content.', ['response' => $response->body()]);

            return IngredientCategory::OTHER;
        }

        $category = $this->extractContentFromTags($result, 'category');

        return IngredientCategory::tryFrom(intval(trim($category))) ?? IngredientCategory::OTHER;
    }
}

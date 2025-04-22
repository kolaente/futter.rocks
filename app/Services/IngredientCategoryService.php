<?php

namespace App\Services;

use App\Models\Enums\IngredientCategory;
use Illuminate\Support\Facades\Http;

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

        $result = $response->json('choices.0.message.content');

        $category = $this->extractContentFromTags($result, 'category');

        return IngredientCategory::tryFrom(intval(trim($category))) ?? IngredientCategory::OTHER;
    }
}

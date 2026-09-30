<?php

namespace App\Services;

use App\Models\Enums\IngredientCategory;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Choice;

class IngredientCategoryService
{
    public function getCategory(string $ingredient): IngredientCategory
    {
        $options = [];
        foreach (IngredientCategory::cases() as $case) {
            $options[$case->name] = $case->getPromptHint();
        }

        $response = Classification::of($ingredient)
            ->question('category', new Choice(
                'Which shopping list category does this food ingredient belong to? The name may be German or English; classify based on the underlying food. Common pantry staples almost always fit a specific category, prefer it over OTHER.',
                $options,
            ))
            ->timeout(10)
            ->classify();

        return collect(IngredientCategory::cases())
            ->firstWhere('name', $response['category']->choice) ?? IngredientCategory::OTHER;
    }
}

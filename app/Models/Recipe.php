<?php

namespace App\Models;

use App\Models\Enums\Unit;
use App\Models\Scopes\CurrentTeam;
use App\Services\RecipeParser;
use App\Utils\RoundIngredients;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

#[ScopedBy(CurrentTeam::class)]
class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'imported_from_url',
        'team_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Recipe $recipe) {
            $recipe->ingredients()->detach();
            $recipe->meals()->detach();
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->using(IngredientRecipe::class)
            ->withPivot(['quantity', 'unit']);
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class);
    }

    public static function importFromUrl(string $url, int $teamId): self
    {
        $parsed = RecipeParser::fetchRecipeFromUrl($url);

        $servings = floatval($parsed['recipeYield'] ?? $parsed['nutrition']['servingSize']);

        $recipe = self::create([
            'title' => $parsed['name'],
            'imported_from_url' => $url,
            'team_id' => $teamId,
        ]);

        foreach ($parsed['recipeIngredient'] as $ing) {

            $parts = explode(' ', $ing);

            $title = null;
            if (count($parts) >= 3) {
                $copy = [...$parts];
                $title = implode(' ', array_splice($copy, 2));
            }

            if ($title === null) {
                continue;
            }

            $quantity = match ($parts[0]) {
                '¼' => 0.25,
                '½' => 0.5,
                '¾' => 0.75,
                '⅓' => 0.333333,
                default => floatval($parts[0]),
            };

            if ($quantity === 0.0) {
                continue;
            }

            $unit = Unit::fromString($parts[1]);
            if (strtolower($parts[1]) === 'tasse' || strtolower($parts[1]) === 'tassen') {
                $unit = Unit::Grams;
                $quantity = $quantity * 150;
            }

            if (strtolower($parts[1]) === 'el') {
                $unit = Unit::Milliliters;
                $quantity = $quantity * 15;
            }

            if (strtolower($parts[1]) === 'tl') {
                $unit = Unit::Milliliters;
                $quantity = $quantity * 5;
            }

            if ($unit === null) {
                $title = trim($parts[1]) . ' ' . $title;
                $unit = Unit::Pieces;
            }

            $title = str_replace(['/', '(', ')'], '', $title);
            $ingredient = Ingredient::firstOrCreate([
                'title' => trim($title),
            ]);

            $recipe->ingredients()->attach($ingredient, [
                'quantity' => $quantity / $servings,
                'unit' => $unit,
            ]);
        }

        return $recipe;
    }

    public function getCalculatedIngredientsForEvent(Event $event)
    {
        $list = [];
        foreach ($this->ingredients as $ingredient) {

            $key = $ingredient->id . '_'.$ingredient->pivot->unit->value;

            if (!isset($list[$ingredient->id])) {
                $list[$key] = [
                    'ingredient' => $ingredient,
                    'quantity' => 0,
                    'unit' => $ingredient->pivot->unit,
                ];
            }

            foreach ($event->participantGroups as $group) {
                $list[$key]['quantity'] += $group->pivot->quantity * $group->food_factor * $ingredient->pivot->quantity;
            }
        }

        foreach ($list as $id => $item) {
            $list[$id] = RoundIngredients::round($item);
        }

        return $list;
    }
}

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

        $recipe->addIngredientsFromText($parsed['recipeIngredient'], $servings);

        return $recipe;
    }

    public function addIngredientsFromText(array $lines, int $servings = 1)
    {
        foreach ($lines as $ing) {
            // Use regex to parse quantity+unit+title pattern
            if (preg_match('/^(\d+(?:\.\d+)?)\s*([a-zA-Z]+)\s+(.+)$/', $ing, $matches)) {
                $quantityStr = $matches[1];
                $unitStr = $matches[2];
                $title = trim($matches[3]);
            } else {
                // Fallback to original space-based parsing
                $parts = explode(' ', $ing);

                $title = null;
                if (count($parts) >= 3) {
                    $copy = [...$parts];
                    $title = implode(' ', array_splice($copy, 2));
                }

                if ($title === null) {
                    continue;
                }

                $quantityStr = $parts[0];
                $unitStr = $parts[1];
            }

            $quantity = match ($quantityStr) {
                '¼' => 0.25,
                '½' => 0.5,
                '¾' => 0.75,
                '⅓' => 0.333333,
                default => floatval($quantityStr),
            };

            if ($quantity === 0.0) {
                continue;
            }

            $unit = Unit::fromString($unitStr);
            if (strtolower($unitStr) === 'tasse' || strtolower($unitStr) === 'tassen') {
                $unit = Unit::Grams;
                $quantity = $quantity * 150;
            }

            if (strtolower($unitStr) === 'el') {
                $unit = Unit::Milliliters;
                $quantity = $quantity * 15;
            }

            if (strtolower($unitStr) === 'tl') {
                $unit = Unit::Milliliters;
                $quantity = $quantity * 5;
            }

            if ($unit === null) {
                $title = trim($unitStr).' '.$title;
                $unit = Unit::Pieces;
            }

            $title = str_replace(['/', '(', ')'], '', $title);
            $ingredient = Ingredient::firstOrCreate([
                'title' => trim($title),
            ]);

            $this->ingredients()->attach($ingredient, [
                'quantity' => $quantity / $servings,
                'unit' => $unit,
            ]);
        }
    }

    public function getCalculatedIngredientsForEvent(Event $event)
    {
        $list = [];
        $ingredients = $this->ingredients()->withoutGlobalScope(CurrentTeam::class)->get();
        $participantGroups = $event->participantGroups()->withoutGlobalScope(CurrentTeam::class)->get();

        foreach ($ingredients as $ingredient) {

            $key = $ingredient->id.'_'.$ingredient->pivot->unit->value;

            if (! isset($list[$ingredient->id])) {
                $list[$key] = [
                    'ingredient' => $ingredient,
                    'quantity' => 0,
                    'unit' => $ingredient->pivot->unit,
                ];
            }

            foreach ($participantGroups as $group) {
                $list[$key]['quantity'] += $group->pivot->quantity * $group->food_factor * $ingredient->pivot->quantity;
            }
        }

        foreach ($list as $id => $item) {
            $list[$id] = RoundIngredients::round($item);
        }

        uasort($list, function ($a, $b) {
            return strcasecmp($a['ingredient']->title, $b['ingredient']->title);
        });

        return $list;
    }
}

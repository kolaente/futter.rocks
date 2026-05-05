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
        'servings',
    ];

    protected function casts(): array
    {
        return [
            'servings' => 'integer',
        ];
    }

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

        $rawYield = $parsed['recipeYield'] ?? $parsed['nutrition']['servingSize'] ?? null;
        if (is_array($rawYield)) {
            $rawYield = $rawYield[0] ?? null;
        }
        $servings = max(1, (int) round(floatval($rawYield)));

        $recipe = self::create([
            'title' => $parsed['name'],
            'imported_from_url' => $url,
            'team_id' => $teamId,
            'servings' => $servings,
        ]);

        $recipe->addIngredientsFromText($parsed['recipeIngredient']);

        return $recipe;
    }

    public static function parseIngredientsFromText(array $lines): array
    {
        $errors = [];
        $ingredients = [];

        foreach ($lines as $lineIndex => $ing) {
            $ing = trim($ing);

            // Skip empty lines
            if (empty($ing)) {
                continue;
            }

            // Use regex to parse quantity+unit+title pattern
            if (preg_match('/^(\d+(?:\.\d+)?)\s*(\p{L}+)\s+(.+)$/u', $ing, $matches)) {
                $quantityStr = $matches[1];
                $unitStr = $matches[2];
                $title = trim($matches[3]);
            } elseif (preg_match('/^(\d+(?:\.\d+)?)\s+(.+)$/', $ing, $matches)) {
                // quantity + title (no unit), e.g. "2 Zwiebeln"
                $quantityStr = $matches[1];
                $unitStr = '';
                $title = trim($matches[2]);
            } else {
                // Fallback to original space-based parsing
                $parts = explode(' ', $ing);

                $title = null;
                if (count($parts) >= 3) {
                    $copy = [...$parts];
                    $title = implode(' ', array_splice($copy, 2));
                }

                if ($title === null) {
                    $errors[] = [
                        'line' => $lineIndex + 1,
                        'content' => $ing,
                        'error' => __('Unable to parse ingredient. Please use format: "quantity unit ingredient" (e.g., "200ml water" or "1 kg sugar")'),
                    ];

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
                $errors[] = [
                    'line' => $lineIndex + 1,
                    'content' => $ing,
                    'error' => __('Invalid quantity ":quantity". Please use a valid number (e.g., 1, 2.5, ¼, ½, ¾, or ⅓)', ['quantity' => $quantityStr]),
                ];

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

            $ingredients[] = [
                'title' => trim($title),
                'quantity' => $quantity,
                'unit' => $unit,
            ];
        }

        return [$ingredients, $errors];
    }

    public function addIngredientsFromText(array $lines): array
    {
        [$ingredients, $errors] = self::parseIngredientsFromText($lines);

        // Only save to database if recipe is persisted
        if ($this->exists) {
            foreach ($ingredients as $ingredientData) {
                $ingredient = Ingredient::firstOrCreate([
                    'title' => $ingredientData['title'],
                ]);

                $this->ingredients()->attach($ingredient, [
                    'quantity' => $ingredientData['quantity'],
                    'unit' => $ingredientData['unit'],
                ]);
            }
        }

        return $errors;
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
                $list[$key]['quantity'] += $group->pivot->quantity * $group->food_factor * $ingredient->pivot->quantity / $this->servings;
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

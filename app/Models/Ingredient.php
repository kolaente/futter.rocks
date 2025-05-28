<?php

namespace App\Models;

use App\Jobs\AddIngredientCategory;
use App\Models\Enums\IngredientCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
    ];

    protected static function booted(): void
    {
        static::created(function (self $ingredient) {
            AddIngredientCategory::dispatch($ingredient);
        });
        static::updated(function (self $ingredient) {
            AddIngredientCategory::dispatch($ingredient);
        });
    }

    protected function casts(): array
    {
        return [
            'category' => IngredientCategory::class,
        ];
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->using(IngredientRecipe::class)
            ->withPivot(['quantity', 'unit']);
    }
}

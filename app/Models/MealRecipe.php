<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MealRecipe extends Pivot
{
    public $incrementing = true;

    public $timestamps = false;

    protected $table = 'meal_recipe';

    protected $fillable = [
        'recipe_id',
        'multiplier',
    ];

    protected function casts(): array
    {
        return [
            'multiplier' => 'float',
        ];
    }

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}

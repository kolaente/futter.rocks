<?php

namespace App\Models;

use App\Models\Enums\Unit;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientRecipe extends Pivot
{
    protected function casts(): array
    {
        return [
            'unit' => Unit::class,
        ];
    }
}

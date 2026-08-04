<?php

namespace App\Models;

use App\Observers\MealObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(MealObserver::class)]
class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (self $meal) {
            $meal->recipes()->detach();
        });
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class)
            ->using(MealRecipe::class)
            ->withPivot('multiplier');
    }

    public function mealRecipes(): HasMany
    {
        return $this->hasMany(MealRecipe::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}

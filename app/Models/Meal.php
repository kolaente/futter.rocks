<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'multiplier',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'multiplier' => 'float',
        ];
    }

    public function getEffectiveMultiplier(): float
    {
        return $this->multiplier ?? 1.0;
    }

    protected static function booted(): void
    {
        static::deleting(function (self $meal) {
            $meal->recipes()->detach();
        });
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class);
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

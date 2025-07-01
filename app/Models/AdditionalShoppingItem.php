<?php

namespace App\Models;

use App\Jobs\AddAdditionalShoppingItemCategory;
use App\Models\Enums\IngredientCategory;
use App\Models\Enums\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalShoppingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'shopping_tour_id',
        'title',
        'quantity',
        'unit',
    ];

    protected static function booted(): void
    {
        static::created(function (self $item) {
            AddAdditionalShoppingItemCategory::dispatch($item);
        });
        static::updated(function (self $item) {
            AddAdditionalShoppingItemCategory::dispatch($item);
        });
    }

    protected function casts(): array
    {
        return [
            'unit' => Unit::class,
            'category' => IngredientCategory::class,
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function shoppingTour(): BelongsTo
    {
        return $this->belongsTo(ShoppingTour::class);
    }
}

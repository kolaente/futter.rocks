<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingTour extends Model
{
    protected $fillable = [
        'date',
        'is_stock_up',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_stock_up' => 'boolean',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

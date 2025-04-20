<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingTour extends Model
{
    protected $fillable = [
        'date',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingTour extends Model
{
    protected $fillable = [
        'date',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

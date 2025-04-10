<?php

namespace App\Models;

use App\Models\Scopes\CurrentTeam;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ScopedBy(CurrentTeam::class)]
class ParticipantGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'food_factor',
        'grouping_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('quantity');
    }
}

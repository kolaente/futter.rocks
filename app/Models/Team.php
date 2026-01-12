<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Team $team) {
            // Delete all events (which cascades to meals, shopping tours, etc.)
            $team->events()->withoutGlobalScopes()->each(fn ($event) => $event->delete());

            // Delete all recipes (which detaches ingredients and meals)
            $team->recipes()->withoutGlobalScopes()->each(fn ($recipe) => $recipe->delete());

            // Delete all participant groups (which detaches from events)
            $team->participantGroups()->withoutGlobalScopes()->each(fn ($participantGroup) => $participantGroup->delete());
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function participantGroups(): HasMany
    {
        return $this->hasMany(ParticipantGroup::class);
    }
}

<?php

namespace App\Models;

use App\Utils\RoundIngredients;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date_from',
        'date_to',
        'created_by_id',
        'grouping_id',
    ];

    protected static function booted()
    {
        static::creating(function (Event $event) {
            $event->share_id = Str::uuid();
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function participantGroups(): BelongsToMany
    {
        return $this->belongsToMany(ParticipantGroup::class)
            ->withPivot('quantity');
    }

    public function shoppingTours()
    {
        return $this->hasMany(ShoppingTour::class);
    }

    public function getMealsByDate(): EloquentCollection|Collection|array
    {
        return $this->meals()
            ->orderBy('date')
            ->with('recipes')
            ->get()
            ->groupBy('date');
    }

    public function getShoppingList(): array
    {
        /*
         * This whole could be an sql query:
         *
 select i.id, i.title, i.unit, sum(ir.quantity * epg.quantity * pg.food_factor) as total
 from meals m
          left join events e on e.id = m.event_id
          left join meal_recipe mr on m.id = mr.meal_id
          left join ingredient_recipe ir on mr.recipe_id = ir.recipe_id
          left join ingredients i on ir.ingredient_id = i.id
          left join event_participant_group epg on e.id = epg.event_id
          left join participant_groups pg on epg.participant_group_id = pg.id
 where e.id = 1 and i.id is not null
 group by i.id, i.title, i.unit;
         */

        $list = [];

        $currentShoppingTour = new ShoppingTour();
        $currentShoppingTour->id = 0;
        $currentShoppingTour->date = $this->date_from;

        $allShoppingTours = $this->shoppingTours()->orderBy('date')->get();

        foreach ($this->meals()->orderBy('date')->get() as $meal) {
            $firstShoppingTour = $allShoppingTours->first();
            if ($firstShoppingTour !== null && $firstShoppingTour->date < $meal->date) {
                $currentShoppingTour = $allShoppingTours->shift();
            }
            foreach ($meal->recipes as $recipe) {
                foreach ($recipe->ingredients as $ingredient) {
                    if (!isset($list[$currentShoppingTour->id][$ingredient->id])) {
                        $list[$currentShoppingTour->id][$ingredient->id] = [
                            'ingredient' => $ingredient,
                            'quantity' => 0,
                        ];
                    }

                    foreach ($this->participantGroups as $group) {
                        $list[$currentShoppingTour->id][$ingredient->id]['quantity'] += $group->pivot->quantity * $group->food_factor * $ingredient->pivot->quantity;
                    }
                }
            }
        }

        foreach ($list as $shoppingTourId => $tourList) {
            foreach ($tourList as $id => $item) {
                $list[$shoppingTourId][$id] = RoundIngredients::round($item);
            }
        }

        return $list;
    }
}

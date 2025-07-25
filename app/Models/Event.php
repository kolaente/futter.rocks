<?php

namespace App\Models;

use App\Models\Enums\IngredientCategory;
use App\Models\Scopes\CurrentTeam;
use App\Utils\RoundIngredients;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[ScopedBy(CurrentTeam::class)]
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date_from',
        'date_to',
        'use_fresh_ingredient_attribute',
        'created_by_id',
        'team_id',
    ];

    protected static function booted()
    {
        static::creating(function (Event $event) {
            $event->share_id = Str::uuid();
        });
        static::deleting(function (Event $event) {
            DB::table('meal_recipe')
                ->whereIn('meal_id', $event->meals->pluck('id'))
                ->delete();
            $event->meals()->delete();
            $event->shoppingTours()->delete();
            $event->additionalShoppingItems()->delete();
            $event->participantGroups()->detach();
        });
    }

    protected function casts(): array
    {
        return [
            'date_to' => 'date',
            'date_from' => 'date',
            'use_fresh_ingredient_attribute' => 'boolean',
        ];
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

    public function additionalShoppingItems(): HasMany
    {
        return $this->hasMany(AdditionalShoppingItem::class);
    }

    public function duplicate(User $user): self
    {
        $copy = $this->replicate();
        $copy->share_id = null;
        $copy->title = $this->title.__(' - Duplicate');
        $copy->created_by_id = $user->id;
        $copy->save();

        foreach ($this->participantGroups as $group) {
            $copy->participantGroups()->attach($group->id, [
                'quantity' => $group->pivot->quantity,
            ]);
        }

        foreach ($this->meals as $meal) {
            $newMeal = $meal->replicate();
            $newMeal->event()->associate($copy);
            $newMeal->save();

            $newMeal->recipes()->attach($meal->recipes->pluck('id'));
        }

        foreach ($this->shoppingTours as $tour) {
            $newTour = $tour->replicate();
            $newTour->event()->associate($copy);
            $newTour->save();
        }

        return $copy;
    }

    public function durationDays(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date_from->diffInDays($this->date_to) + 1,
        );
    }

    public function durationString(): Attribute
    {
        return Attribute::make(
            get: fn () => __(':from to :to, :days', [
                'from' => $this->date_from->translatedFormat(__('j F Y')),
                'to' => $this->date_to->translatedFormat(__('j F Y')),
                'days' => trans_choice(':count day|:count days', $this->duration_days),
            ]),
        );
    }

    public function getMealsByDate()
    {
        return $this->meals()
            ->orderBy('date')
            ->with([
                'recipes' => function ($query) {
                    $query->withoutGlobalScope(CurrentTeam::class);
                },
            ])
            ->get()
            ->groupBy('date')
            ->map(fn ($meals) => $meals->sortBy(function ($item) {
                $order = [
                    'Frühstück' => 1,
                    'Mittag' => 2,
                    'Mittagessen' => 2,
                    'Abend' => 3,
                    'Abendessen' => 3,
                ];

                return $order[$item->title] ?? 4;
            }));
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

        $currentShoppingTour = new ShoppingTour;
        $currentShoppingTour->id = 0;
        $currentShoppingTour->date = $this->date_from;

        $allShoppingTours = $this->shoppingTours()->orderBy('date')->get();

        foreach ($this->meals()->orderBy('date')->get() as $meal) {
            $firstShoppingTour = $allShoppingTours->first();
            if ($firstShoppingTour !== null && $firstShoppingTour->date < $meal->date) {
                $currentShoppingTour = $allShoppingTours->shift();
            }
            foreach ($meal->recipes()->withoutGlobalScope(CurrentTeam::class)->get() as $recipe) {
                foreach ($recipe->ingredients as $ingredient) {

                    $key = $ingredient->id.'_'.$ingredient->pivot->unit->value;
                    $targetTour = $currentShoppingTour->id;
                    if ($this->use_fresh_ingredient_attribute) {
                        $targetTour = $ingredient->is_fresh ? $currentShoppingTour->id : 0;
                    }

                    if (! isset($list[$targetTour][$key])) {
                        $list[$targetTour][$key] = [
                            'ingredient' => $ingredient,
                            'quantity' => 0,
                            'unit' => $ingredient->pivot->unit,
                        ];
                    }

                    foreach ($this->participantGroups()->withoutGlobalScope(CurrentTeam::class)->get() as $group) {
                        $list[$targetTour][$key]['quantity'] += $group->pivot->quantity * $group->food_factor * $ingredient->pivot->quantity;
                    }
                }
            }
        }

        foreach ($list as $shoppingTourId => $tourList) {
            foreach ($tourList as $id => $item) {
                $list[$shoppingTourId][$id] = RoundIngredients::round($item);
            }
        }

        foreach ($this->additionalShoppingItems as $item) {
            $tourId = $item->shopping_tour_id ?? 0;
            $list[$tourId][] = [
                'ingredient' => $item,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
            ];
        }

        foreach ($list as $shoppingTourId => $tourList) {
            uasort($list[$shoppingTourId], fn ($a, $b) => strnatcasecmp($a['ingredient']->title, $b['ingredient']->title));
            $list[$shoppingTourId] = collect($list[$shoppingTourId])
                ->groupBy('ingredient.category')
                ->sortKeysUsing(fn ($category1, $category2) => strcasecmp(
                    IngredientCategory::from($category1)->getLabel(),
                    IngredientCategory::from($category2)->getLabel(),
                ))
                ->toArray();
        }

        return $list;
    }
}

<?php

namespace App\Observers;

use App\Models\Meal;

class MealObserver
{
    public function creating(Meal $meal): void
    {
        if ($meal->position === null) {
            $meal->position = $this->nextPositionFor($meal);
        }
    }

    public function updating(Meal $meal): void
    {
        if ($meal->isDirty('date')) {
            $meal->position = $this->nextPositionFor($meal);
        }
    }

    private function nextPositionFor(Meal $meal): int
    {
        return Meal::query()
            ->where('event_id', $meal->event_id)
            ->whereKeyNot($meal->getKey())
            ->whereDate('date', $meal->date)
            ->max('position') + 1;
    }
}

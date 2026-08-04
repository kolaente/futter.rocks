<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Meal;
use App\Services\PdfGenerator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MealPlan extends Component
{
    use AuthorizesRequests;

    public Event $event;

    public bool $editing = false;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function toggleEditing(): void
    {
        $this->authorize('update', $this->event);

        $this->editing = ! $this->editing;
    }

    public function reorderMeal(int $mealId, int $position, string $date): void
    {
        $this->authorize('update', $this->event);

        /** @var Meal $meal */
        $meal = $this->event->meals()->findOrFail($mealId);
        $targetDate = Carbon::parse($date);

        abort_unless($targetDate->betweenIncluded($this->event->date_from, $this->event->date_to), 422);

        DB::transaction(function () use ($meal, $position, $targetDate) {
            $sourceDate = $meal->date;
            $meal->date = $targetDate;

            $targetDayMeals = $this->event->meals()
                ->whereDate('date', $targetDate)
                ->whereKeyNot($meal->getKey())
                ->orderBy('position')
                ->get();
            $targetDayMeals->splice(max(0, $position), 0, [$meal]);
            $this->renumber($targetDayMeals);

            if (! $sourceDate->isSameDay($targetDate)) {
                $this->renumber(
                    $this->event->meals()->whereDate('date', $sourceDate)->orderBy('position')->get(),
                );
            }
        });

        unset($this->mealsByDate);
    }

    public function moveMeal(int $mealId, int $direction): void
    {
        $this->authorize('update', $this->event);

        /** @var Meal $meal */
        $meal = $this->event->meals()->findOrFail($mealId);

        /** @var ?Meal $neighbor */
        $neighbor = $this->event->meals()
            ->whereDate('date', $meal->date)
            ->when(
                $direction > 0,
                fn ($query) => $query->where('position', '>', $meal->position)->orderBy('position'),
                fn ($query) => $query->where('position', '<', $meal->position)->orderByDesc('position'),
            )
            ->first();

        if ($neighbor === null) {
            return;
        }

        DB::transaction(function () use ($meal, $neighbor) {
            [$meal->position, $neighbor->position] = [$neighbor->position, $meal->position];
            $meal->saveQuietly();
            $neighbor->saveQuietly();
        });

        unset($this->mealsByDate);
    }

    /**
     * @param  Collection<int, Meal>  $meals
     */
    private function renumber(Collection $meals): void
    {
        foreach ($meals->values() as $index => $meal) {
            $meal->position = $index + 1;
            if ($meal->isDirty()) {
                $meal->saveQuietly();
            }
        }
    }

    #[Computed]
    public function mealsByDate()
    {
        return $this->event->getMealsByDate();
    }

    public function download(PdfGenerator $generator)
    {
        $url = route('shared.event.meal-plan', ['shareId' => $this->event->share_id, 'fullPlan' => true]);
        $pdf = $generator->fromUrl($url, true);

        return response()->streamDownload(fn () => print ($pdf), $this->event->title.' '.__('Meal Plan').'.pdf');
    }

    public function render()
    {
        return view('livewire.events.meal-plan')
            ->title(__('Meal plan for :event', ['event' => $this->event->title]));
    }
}

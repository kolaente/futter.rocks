<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Services\PdfGenerator;
use Livewire\Attributes\Computed;
use Livewire\Component;

class MealPlan extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
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

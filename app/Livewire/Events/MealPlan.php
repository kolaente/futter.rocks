<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
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
        return $this->event->meals()
            ->orderBy('date')
            ->with('recipes')
            ->get()
            ->groupBy('date')
            ->map(fn($meals) => $meals->sortBy(function ($item) {
                $order = [
                    'Frühstück' => 1,
                    'Mittag' => 2,
                    'Abendessen' => 3
                ];

                return $order[$item->title] ?? 4;
            }));
    }

    public function download()
    {
        Pdf::setOption([
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'orientation' => 'portrait',
        ]);

        $pdf = Pdf::loadView('pdf.meal-plan', [
            'event' => $this->event,
            'mealsByDate' => $this->mealsByDate,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->event->title . ' ' . __('Meal Plan') . '.pdf');
    }

    public function render()
    {
        return view('livewire.events.meal-plan')
            ->title(__('Meal plan for :event', ['event' => $this->event->title]));
    }
}

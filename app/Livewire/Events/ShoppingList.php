<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class ShoppingList extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function download()
    {
        Pdf::setOption([
            'dpi' => 300,
            'defaultFont' => 'sans-serif',
            'orientation' => 'portrait',
        ]);

        $pdf = Pdf::loadView('helper.shopping-list', [
            'event' => $this->event,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->event->title . ' ' . __('Shopping list') . '.pdf');
    }

    public function render()
    {
        return view('livewire.events.shopping-list')
            ->title(__('Shopping list for :event', ['event' => $this->event->title]));
    }
}

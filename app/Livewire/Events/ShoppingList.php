<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Services\PdfGenerator;
use Livewire\Component;

class ShoppingList extends Component
{
    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    public function download(PdfGenerator $generator)
    {
        $url = route('shared.event.shopping-list', ['shareId' => $this->event->share_id]);
        $pdf = $generator->fromUrl($url);

        return response()->streamDownload(fn () => print ($pdf), $this->event->title.' '.__('Shopping list').'.pdf');
    }

    public function render()
    {
        return view('livewire.events.shopping-list')
            ->title(__('Shopping list for :event', ['event' => $this->event->title]));
    }
}

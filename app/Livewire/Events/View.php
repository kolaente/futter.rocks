<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Livewire\Component;

class View extends Component
{
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function render()
    {
        return view('livewire.event-view')
            ->title($this->event->title);
    }
}

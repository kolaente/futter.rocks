<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class View extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function deleteAction()
    {
        return DeleteAction::make('delete')
            ->requiresConfirmation()
            ->record($this->event)
            ->modalHeading(__('Delete Event'))
            ->successRedirectUrl(route('events.list'));
    }

    public function duplicateEvent(): void
    {
        $newEvent = $this->event->duplicate(auth()->user());

        $this->redirect(route('events.edit', ['event' => $newEvent]), true);
    }

    public function render()
    {
        return view('livewire.events.view')
            ->title($this->event->title);
    }
}

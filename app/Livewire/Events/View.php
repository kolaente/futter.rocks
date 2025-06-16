<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
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

    public function duplicateEvent()
    {
        Gate::authorize('create', Event::class);

        $newEvent = $this->event->duplicate();

        Notification::make()
            ->title(__('Event duplicated successfully'))
            ->success()
            ->send();

        return redirect()->route('events.view', ['event' => $newEvent]);
    }

    public function duplicateEventAction(): Action
    {
        return Action::make('duplicateEventAction')
            ->label(__('Duplicate'))
            ->color('info')
            ->icon('heroicon-o-document-duplicate')
            ->requiresConfirmation()
            ->modalHeading(__('Duplicate event'))
            ->modalSubheading(__('Are you sure you want to duplicate this event? The title will be prefixed with "Copy of " and all related data will be duplicated as well.'))
            ->modalButton(__('Duplicate'))
            ->action('duplicateEvent');
    }

    public function deleteAction()
    {
        return DeleteAction::make('delete')
            ->requiresConfirmation()
            ->record($this->event)
            ->successRedirectUrl(route('events.list'));
    }

    public function render()
    {
        return view('livewire.events.view')
            ->title($this->event->title);
    }
}

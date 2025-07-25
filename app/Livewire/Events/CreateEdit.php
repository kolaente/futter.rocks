<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public ?Event $event = null;

    public function mount(Request $request): void
    {
        $eventId = $request->get('event');

        if ($eventId) {
            $this->event = Event::findOrFail($eventId);
        }

        $this->preFillForm();
    }

    private function preFillForm()
    {
        if ($this->event === null) {
            $this->form->fill();

            return;
        }

        $this->form->fill([
            'title' => $this->event->title,
            'description' => $this->event->description,
            'date_from' => $this->event->date_from,
            'date_to' => $this->event->date_to,
            'use_fresh_ingredient_attribute' => $this->event->use_fresh_ingredient_attribute,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label(__('Description')),
                Forms\Components\DatePicker::make('date_from')
                    ->label(__('Start Date'))
                    ->required(),
                Forms\Components\DatePicker::make('date_to')
                    ->label(__('End Date'))
                    ->required(),
                Forms\Components\Toggle::make('use_fresh_ingredient_attribute')
                    ->label(__('Buy shelf-able good before the event'))
                    ->helperText(__('When enabled, all non-fresh ingredients will be put on the shopping tour before the event. This allows faster shopping tours during the event (because only fresh items need to be bought) at the expense of using more storage.'))
                    ->default(true),
            ])
            ->statePath('data');
    }

    public function store()
    {
        if ($this->event === null) {
            $this->event = new Event;
            $this->event->created_by_id = Auth::user()->id;
            $this->event->team_id = Auth::user()->currentTeam->id;
        }

        $this->event->fill($this->form->getState());

        $this->event->save();

        $this->redirect(route('events.view', ['event' => $this->event]), true);
    }

    public function render()
    {
        return view('livewire.events.create-edit')
            ->title($this->event === null
                ? __('Create Event')
                : __('Edit :item', ['item' => $this->event->title]));
    }
}

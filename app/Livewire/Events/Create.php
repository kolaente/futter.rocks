<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
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
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        $event = Event::create([
            ...$this->form->getState(),
            'team_id' => Auth::user()->currentTeam->id,
            'created_by_id' => Auth::user()->id,
        ]);

        dd($event);
    }

    public function render()
    {
        return view('livewire.events.create')
            ->title(__('Create Event'));
    }
}

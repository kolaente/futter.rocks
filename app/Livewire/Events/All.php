<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class All extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Event::query())
            ->defaultSort('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->sortable()
                    ->action(fn(Event $record) => $this->redirect(route('events.view', ['event' => $record]), true))
                    ->label(__('Title')),
                Tables\Columns\TextColumn::make('date_from')
                    ->sortable()
                    ->action(fn(Event $record) => $this->redirect(route('events.view', ['event' => $record]), true))
                    ->formatStateUsing(fn($state) => $state->translatedFormat(__('j F Y')))
                    ->label(__('Start Date')),
                Tables\Columns\TextColumn::make('date_to')
                    ->action(fn(Event $record) => $this->redirect(route('events.view', ['event' => $record]), true))
                    ->formatStateUsing(fn($state) => $state->translatedFormat(__('j F Y')))
                    ->label(__('End Date')),
            ]);
    }

    public function render()
    {
        return view('livewire.events.all')
            ->title(__('Events'));
    }
}

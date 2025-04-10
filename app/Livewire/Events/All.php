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

    public function render()
    {
        return view('livewire.event-list')
            ->title(__('Events'));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Event::query())
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label(__('View Event'))
                    ->url(fn(Event $record) => route('events.view', ['event' => $record])),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title')),
                Tables\Columns\TextColumn::make('date_from')
                    ->label(__('Start Date')),
                Tables\Columns\TextColumn::make('date_to')
                    ->label(__('End Date')),
            ]);
    }
}

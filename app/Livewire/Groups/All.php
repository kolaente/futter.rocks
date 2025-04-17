<?php

namespace App\Livewire\Groups;

use App\Models\Event;
use App\Models\ParticipantGroup;
use App\Models\Recipe;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class All extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(ParticipantGroup::query())
            ->defaultSort('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->action(fn(ParticipantGroup $record) => $this->redirect(route('participant-groups.edit', ['group' => $record]), true))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('food_factor')
                    ->action(fn(ParticipantGroup $record) => $this->redirect(route('participant-groups.edit', ['group' => $record]), true))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]);
    }

    public function render(): View
    {
        return view('livewire.groups.all')
            ->title(__('Groups'));
    }
}

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
                    ->label(__('Title'))
                    ->action(fn(ParticipantGroup $record) => $this->redirect(route('participant-groups.edit', ['group' => $record]), true))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('food_factor')
                    ->label(__('Food Factor'))
                    ->action(fn(ParticipantGroup $record) => $this->redirect(route('participant-groups.edit', ['group' => $record]), true))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.groups.all')
            ->title(__('Groups'));
    }
}

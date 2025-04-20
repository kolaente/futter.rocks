<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class All extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Recipe::query())
            ->defaultSort('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->action(fn (Recipe $record) => $this->redirect(route('recipes.view', ['recipe' => $record]), true))
                    ->sortable()
                    ->searchable()
                    ->label(__('Title')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.recipes.all')
            ->title(__('Recipes'));
    }
}

<?php

namespace App\Livewire\Recipes;

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
            ->query(Recipe::query())
            ->defaultSort('title')
            ->recordUrl(fn(Recipe $record) => route('recipes.view', ['recipe' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('title')
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
        return view('livewire.recipes.list')
            ->title(__('Recipes'));
    }
}

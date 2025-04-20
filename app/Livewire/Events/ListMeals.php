<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\Meal;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ListMeals extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function table(Table $table): Table
    {
        $form = [
            Forms\Components\TextInput::make('title')
                ->label(__('Title'))
                ->required()
                ->maxLength(255),
            Forms\Components\DatePicker::make('date')
                ->label(__('Date'))
                ->minDate(fn() => $this->event->date_from)
                ->maxDate(fn() => $this->event->date_to)
                ->required(),
            Forms\Components\Select::make('recipes')
                ->label(__('Recipes'))
                ->required()
                ->multiple()
                ->relationship(name: 'recipes', titleAttribute: 'title'),
        ];

        return $table
            ->relationship(fn(): HasMany => $this->event->meals())
            ->inverseRelationship('event')
            ->recordTitleAttribute('title')
            ->heading(fn() => __('Meals'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title')),
                Tables\Columns\TextColumn::make('date')
                    ->formatStateUsing(fn($state) => $state->translatedFormat(__('j F Y')))
                    ->label(__('Date')),
                Tables\Columns\TextColumn::make('recipes.title')
                    ->label(__('Recipes')),
            ])
            ->defaultSort('date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form($form),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->form($form),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.events.list-meals');
    }
}

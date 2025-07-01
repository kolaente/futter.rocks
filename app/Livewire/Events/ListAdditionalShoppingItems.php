<?php

namespace App\Livewire\Events;

use App\Models\Enums\Unit;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

class ListAdditionalShoppingItems extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public Event $event;

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    protected function getShoppingTourOptions(): array
    {
        $options = $this->event->shoppingTours()->orderBy('date')->get()
            ->pluck('date', 'id')
            ->map(fn ($d) => $d->translatedFormat(__('j F Y')))
            ->toArray();

        return [null => __('Before the event')] + $options;
    }

    public function table(Table $table): Table
    {
        $form = [
            Forms\Components\TextInput::make('title')
                ->label(__('Title'))
                ->required(),
            Forms\Components\TextInput::make('quantity')
                ->label(__('Quantity'))
                ->numeric()
                ->required(),
            Forms\Components\Select::make('unit')
                ->label(__('Unit'))
                ->options(Unit::getLocalizedOptionsArray())
                ->required(),
            Forms\Components\Select::make('shopping_tour_id')
                ->label(__('Shopping Date'))
                ->options(fn () => $this->getShoppingTourOptions())
                ->dehydrateStateUsing(fn ($state) => empty($state) ? null : $state)
                ->visible(fn () => $this->event->shoppingTours()->exists()),
        ];

        return $table
            ->relationship(fn (): HasMany => $this->event->additionalShoppingItems())
            ->inverseRelationship('event')
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title')),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity')),
                Tables\Columns\TextColumn::make('unit')
                    ->formatStateUsing(fn ($state) => Unit::from($state)->getShortLabel())
                    ->label(__('Unit')),
                Tables\Columns\TextColumn::make('shoppingTour.date')
                    ->label(__('Shopping Date'))
                    ->formatStateUsing(fn ($state) => $state?->translatedFormat(__('j F Y')) ?? __('Before the event'))
                    ->visible(fn () => $this->event->shoppingTours()->exists()),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->form($form),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->form($form),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.events.list-additional-shopping-items');
    }
}

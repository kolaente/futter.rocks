<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\ShoppingTour;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ListShoppingTours extends Component implements HasForms, HasTable
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
        return $table
            ->relationship(fn (): HasMany => $this->event->shoppingTours())
            ->inverseRelationship('event')
            ->modifyQueryUsing(function (Builder $query) {
                // Virtual "before the event" tour, always shown. Column order must
                // match the shopping_tours table definition. Wrapped as subquery so
                // qualified sort columns still resolve.
                $virtualTour = DB::query()->selectRaw(
                    '0 as id, ? as event_id, ? as date, null as created_at, null as updated_at, true as is_stock_up',
                    [$this->event->id, $this->event->date_from->subDay()->toDateString()],
                );

                return ShoppingTour::query()->fromSub(
                    $query->getQuery()->union($virtualTour),
                    'shopping_tours',
                );
            })
            ->defaultSort('date')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn ($state, ShoppingTour $record) => $record->id === 0
                        ? __('Before the event')
                        : $state->translatedFormat(__('j F Y')))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_stock_up')
                    ->label(__('Stock-up tour'))
                    ->boolean()
                    ->visible(fn () => $this->event->use_fresh_ingredient_attribute),
            ])
            ->checkIfRecordIsSelectableUsing(fn (ShoppingTour $record) => $record->id !== 0)
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('Create shopping tour'))
                    ->form($this->getTourForm()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(__('Edit shopping tour'))
                    ->form($this->getTourForm())
                    ->hidden(fn (ShoppingTour $record) => $record->id === 0),
                Tables\Actions\DeleteAction::make()
                    ->modalHeading(__('Delete shopping tour'))
                    ->hidden(fn (ShoppingTour $record) => $record->id === 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    private function getTourForm(): array
    {
        return [
            Forms\Components\DatePicker::make('date')
                ->label(__('Date'))
                ->minDate(fn () => $this->event->date_from)
                ->maxDate(fn () => $this->event->date_to)
                ->required(),
            Forms\Components\Toggle::make('is_stock_up')
                ->label(__('Buy shelf-stable ingredients for the rest of the event on this tour'))
                ->helperText(__('All shelf-stable ingredients needed after this tour will be bought here, until the next stock-up tour. Ingredients needed before this tour are still bought before the event.'))
                ->default(false)
                ->visible(fn () => $this->event->use_fresh_ingredient_attribute),
        ];
    }

    public function render(): View
    {
        return view('livewire.events.list-shopping-tours');
    }
}

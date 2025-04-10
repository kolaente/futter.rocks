<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;
use Illuminate\Contracts\View\View;

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
            ->relationship(fn(): HasMany => $this->event->shoppingTours())
            ->inverseRelationship('event')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->form([
                        Forms\Components\DatePicker::make('date')
                            ->label(__('Date'))
                            ->minDate(fn() => $this->event->date_from)
                            ->maxDate(fn() => $this->event->date_to)
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.events.list-shopping-tours');
    }
}

<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Livewire\Component;

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
                ->minDate(fn () => $this->event->date_from)
                ->maxDate(fn () => $this->event->date_to)
                ->required(),
            Forms\Components\Select::make('recipes')
                ->label(__('Recipes'))
                ->required()
                ->multiple()
                ->relationship(name: 'recipes', titleAttribute: 'title'),
        ];

        return $table
            ->relationship(fn (): HasMany => $this->event->meals())
            ->inverseRelationship('event')
            ->recordTitleAttribute('title')
            ->heading(fn () => __('Meals'))
            ->emptyStateHeading(__('No meals yet'))
            ->emptyStateDescription(__('Create a meal on the top right.'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->formatStateUsing(fn ($state) => $state->translatedFormat(__('j F Y')))
                    ->label(__('Date')),
                Tables\Columns\TextColumn::make('recipes.title')
                    ->label(__('Recipes')),
            ])
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')
                            ->label(__('Date'))
                            ->minDate(fn () => $this->event->date_from)
                            ->maxDate(fn () => $this->event->date_to),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when($data['date'], fn (Builder $query, $date): Builder => $query->whereDate('date', $date)))
                    ->indicateUsing(fn (array $data) => $data['date'] ? Carbon::parse($data['date'])->translatedFormat(__('j F Y')) : null),
            ])
            ->defaultSort('date')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading(__('Create meal'))
                    ->form($form),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(__('Edit meal'))
                    ->form($form),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.events.list-meals');
    }
}

<?php

namespace App\Livewire\Events;

use App\Formatter;
use App\Models\Event;
use App\Models\Meal;
use App\Models\Recipe;
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
            Forms\Components\Repeater::make('mealRecipes')
                ->label(__('Recipes'))
                ->relationship()
                ->schema([
                    Forms\Components\Select::make('recipe_id')
                        ->label(__('Recipe'))
                        ->options(fn () => Recipe::orderBy('title')->pluck('title', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('multiplier')
                        ->label(__('Multiplier'))
                        ->numeric()
                        ->rule('gt:0')
                        ->nullable()
                        ->placeholder('1')
                        ->helperText(__('Scales all quantities of this recipe in this meal. Leave empty for no change.')),
                ])
                ->columns(2)
                ->addActionLabel(__('Add recipe'))
                ->minItems(1)
                ->required(),
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
                Tables\Columns\TextColumn::make('recipes')
                    ->getStateUsing(fn (Meal $record) => $record->recipes
                        ->map(function ($recipe) {
                            $multiplier = $recipe->pivot->multiplier;

                            return $recipe->title.($multiplier === null || $multiplier == 1 ? '' : ' ×'.(new Formatter)->format($multiplier));
                        })
                        ->all())
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

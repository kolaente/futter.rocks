<?php

namespace App\Livewire\Recipes;

use App\Models\Enums\Unit;
use App\Models\Ingredient;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class Create extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Forms\Components\Repeater::make('ingredients')
                    ->label(__('Ingredients'))
                    ->schema([
                        Forms\Components\Select::make('ingredient')
                            ->searchable()
                            ->getSearchResultsUsing(function(string $search) {
                                $results = Ingredient::where('title', 'like', '%'.$search.'%')->get();

                                $res = $results
                                    ->pluck('title','id')
                                    ->toArray();

                                $exactMatch = $results->first(fn($option) => $option->title === $search);
                                if (!$exactMatch) {
                                    $res[$search] = $search;
                                }

                                return $res;
                            }),
                        // FIXME: should show unit in search result title
                        Forms\Components\Select::make('unit')
                            ->label(__('Unit'))
                            ->options(Unit::class)
                            ->required(),
                        Forms\Components\TextInput::make('quantity')
                            ->label(__('Quantity'))
                            ->numeric()
                            ->minValue(0.0001)
                            ->required(),
                    ])
                ->required(),
            ])
            ->statePath('data')
            ->model(Recipe::class);
    }

    public function create()
    {
        $recipe = Recipe::create([
            ...$this->form->getState(),
            'team_id' => Auth::user()->currentTeam->id,
        ]);

        foreach ($this->form->getState()['ingredients'] as $ig) {

            $attributes = [
                'unit' => $ig['unit'],
            ];

            if (is_numeric($ig['ingredient'])) {
                $attributes['id'] = $ig['ingredient'];
            } else {
                $attributes['title'] = $ig['ingredient'];
            }

            // FIXME: prefetch
            $ingredient = Ingredient::firstOrCreate($attributes);

            $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ig['quantity'],
            ]);
        }

        return to_route('recipes.view', ['recipe' => $recipe]);
    }

    public function render(): View
    {
        return view('livewire.recipes.create')
            ->title(__('Create Recipe'));
    }
}

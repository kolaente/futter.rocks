<?php

namespace App\Livewire\Recipes;

use App\Models\Enums\Unit;
use App\Models\Ingredient;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateEdit extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public ?Recipe $recipe = null;

    public function mount(Request $request): void
    {
        $recipeId = $request->get('recipe');

        if ($recipeId) {
            $this->recipe = Recipe::findOrFail($recipeId);
        }

        $this->preFillForm();
    }

    private function preFillForm()
    {
        if ($this->recipe === null) {
            $this->form->fill();

            return;
        }

        $ingredients = [];

        foreach ($this->recipe->ingredients as $ingredient) {
            $ingredients[] = [
                'ingredient' => $ingredient->title,
                'unit' => $ingredient->pivot->unit,
                'quantity' => $ingredient->pivot->quantity,
            ];
        }

        $this->form->fill([
            'title' => $this->recipe->title,
            'ingredients' => $ingredients,
        ]);
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
                    ->addActionLabel(__('Add Ingredient'))
                    ->schema([
                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\Select::make('ingredient')
                                ->label(__('Ingredient'))
                                ->searchable()
                                ->required()
                                ->getSearchResultsUsing(function (string $search) {
                                    $results = Ingredient::where('title', 'like', '%'.$search.'%')->get();

                                    $res = $results
                                        ->pluck('title', 'id')
                                        ->toArray();

                                    $exactMatch = $results->first(fn ($option) => $option->title === $search);
                                    if (! $exactMatch) {
                                        $res[$search] = $search;
                                    }

                                    return $res;
                                }),
                            Forms\Components\Select::make('unit')
                                ->label(__('Unit'))
                                ->options(Unit::getLocalizedOptionsArray())
                                ->required(),
                            Forms\Components\TextInput::make('quantity')
                                ->label(__('Quantity'))
                                ->numeric()
                                ->minValue(0.0001)
                                ->required(),
                        ]),
                    ])
                    ->required(),
            ])
            ->statePath('data')
            ->model(Recipe::class);
    }

    public function store()
    {
        DB::transaction(function () {
            if ($this->recipe === null) {
                $this->recipe = new Recipe;
                $this->recipe->team_id = Auth::user()->currentTeam->id;
            }

            $this->recipe->title = $this->form->getState()['title'];
            $this->recipe->save();

            $this->recipe->ingredients()->detach();

            foreach ($this->form->getState()['ingredients'] as $ig) {
                // FIXME: prefetch
                if (is_numeric($ig['ingredient'])) {
                    $ingredient = Ingredient::where('id', $ig['ingredient'])
                        ->first();
                } else {
                    $ingredient = Ingredient::firstOrCreate(['title' => $ig['ingredient']]);
                }

                $this->recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => $ig['quantity'],
                    'unit' => $ig['unit'],
                ]);
            }

            $this->redirect(route('recipes.view', ['recipe' => $this->recipe]), true);
        });
    }

    public function render(): View
    {
        return view('livewire.recipes.create-edit')
            ->title($this->recipe === null
                ? __('Create Recipe')
                : __('Edit :item', ['item' => $this->recipe->title]));
    }
}

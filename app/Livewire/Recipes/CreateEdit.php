<?php

namespace App\Livewire\Recipes;

use App\Models\Enums\Unit;
use App\Models\Event;
use App\Models\Ingredient;
use App\Models\Recipe;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Livewire\Component;
use Illuminate\Contracts\View\View;

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
                'unit' => $ingredient->unit,
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
                    ->schema([
                        Forms\Components\Select::make('ingredient')
                            ->searchable()
                            ->required()
                            ->getSearchResultsUsing(function (string $search) {
                                $results = Ingredient::where('title', 'like', '%' . $search . '%')->get();

                                $res = $results
                                    ->pluck('title', 'id')
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

    public function store()
    {
        DB::transaction(function () {
            if ($this->recipe === null) {
                $this->recipe = new Recipe;
            }
            $this->recipe->title = $this->form->getState()['title'];
            $this->recipe->team_id = Auth::user()->currentTeam->id;
            $this->recipe->save();

            $this->recipe->ingredients()->detach();

            foreach ($this->form->getState()['ingredients'] as $ig) {
                // FIXME: prefetch
                if (is_numeric($ig['ingredient'])) {
                    $ingredient = Ingredient::where('id', $ig['ingredient'])
                        ->first();

                    if ($ingredient->unit->value !== $ig['unit']) {
                        $ingredient = $ingredient->replicate(['unit']);
                        $ingredient->unit = $ig['unit'];
                        $ingredient->save();
                    }
                } else {
                    $ingredient = Ingredient::where('title', $ig['ingredient'])
                        ->where('unit', $ig['unit'])
                        ->first();

                    if ($ingredient === null) {
                        $ingredient = Ingredient::create([
                            'title' => $ig['ingredient'],
                            'unit' => $ig['unit'],
                        ]);
                    }
                }

                $this->recipe->ingredients()->attach($ingredient->id, [
                    'quantity' => $ig['quantity'],
                ]);
            }
        });

        return to_route('recipes.view', ['recipe' => $this->recipe]);
    }

    public function render(): View
    {
        return view('livewire.recipes.create-edit')
            ->title($this->recipe === null
                ? __('Create Recipe')
                : __('Edit :recipe', ['recipe' => $this->recipe->title]));
    }
}

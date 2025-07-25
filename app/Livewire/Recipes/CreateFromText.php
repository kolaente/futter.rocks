<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateFromText extends Component implements HasActions, HasForms
{
    use \Filament\Actions\Concerns\InteractsWithActions;
    use \Filament\Forms\Concerns\InteractsWithForms;

    public array $recipeTextErrors = [];

    public function createFromTextAction(): Action
    {
        return Action::make('createFromText')
            ->label(__('Create from text'))
            ->color('gray')
            ->form([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->required(),
                Textarea::make('recipe_text')
                    ->label(__('Ingredients List'))
                    ->autosize()
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                // Clear previous errors
                                $this->recipeTextErrors = [];

                                if (empty($value)) {
                                    return;
                                }

                                [$ingredients, $errors] = Recipe::parseIngredientsFromText(explode("\n", $value));

                                if (! empty($errors)) {
                                    $fail(__('Some lines could not be parsed.'));
                                    $this->recipeTextErrors = $errors;
                                }
                            };
                        },
                    ]),
            ])
            ->modalContentFooter(view('livewire.recipes.create-from-text-modal-errors', ['errors' => $this->recipeTextErrors]))
            ->action(function (array $data) {
                $recipe = Recipe::create([
                    'title' => $data['title'],
                    'team_id' => Auth::user()->currentTeam->id,
                ]);

                $recipe->addIngredientsFromText(explode("\n", $data['recipe_text']));

                Notification::make()
                    ->success()
                    ->title(__('Recipe Imported Successfully!'))
                    ->body(__('Please check if all ingredients were imported correctly.'))
                    ->send();

                return to_route('recipes.view', ['recipe' => $recipe]);
            });
    }

    public function render()
    {
        return view('livewire.recipes.create-from-text');
    }
}

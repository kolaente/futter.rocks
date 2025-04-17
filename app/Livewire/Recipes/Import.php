<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Import extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    public function importAction(): Action
    {
        return Action::make('import')
                ->label(__('Import'))
                ->color('gray')
                ->form([
                    TextInput::make('url')
                        ->label(__('URL'))
                        ->url()
                        ->required()
                ])
                ->action(function (array $data) {
                    $recipe = Recipe::importFromUrl($data['url'], Auth::user()->currentTeam->id);

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
        return view('livewire.recipes.import');
    }
}

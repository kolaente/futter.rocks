<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;

class View extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public Recipe $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe
            ->where('id', $recipe->id)
            ->with('ingredients')
            ->first();
    }

    public function deleteAction()
    {
        return DeleteAction::make('delete')
            ->requiresConfirmation()
            ->record($this->recipe)
            ->successRedirectUrl(route('recipes.list'));
    }


    public function render()
    {
        return view('livewire.recipes.view')
            ->title($this->recipe->title);
    }
}

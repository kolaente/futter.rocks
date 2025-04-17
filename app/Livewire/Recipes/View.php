<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use Livewire\Component;

class View extends Component
{
    public Recipe $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe->with('ingredients')->first();
    }

    public function render()
    {
        return view('livewire.recipes.view')
            ->title($this->recipe->title);
    }
}

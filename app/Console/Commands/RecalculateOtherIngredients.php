<?php

namespace App\Console\Commands;

use App\Jobs\AddIngredientCategory;
use App\Models\Enums\IngredientCategory;
use App\Models\Ingredient;
use Illuminate\Console\Command;

class RecalculateOtherIngredients extends Command
{
    protected $signature = 'app:recalculate-other-ingredients';

    protected $description = 'Recategorize ingredients currently in the OTHER category';

    public function handle()
    {
        $ingredients = Ingredient::where('category', IngredientCategory::OTHER)->get();

        $this->info("Found {$ingredients->count()} ingredients with OTHER category to recategorize.");

        $this->withProgressBar($ingredients, function ($ingredient) {
            AddIngredientCategory::dispatch($ingredient);
        });

        $this->newLine(2);
        $this->info('All jobs dispatched successfully.');
    }
}

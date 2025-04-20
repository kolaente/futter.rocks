<?php

namespace App\Jobs;

use App\Models\Ingredient;
use App\Services\IngredientCategoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddIngredientCategory implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Ingredient $ingredient,
    ) {}

    public function handle(): void
    {
        $service = resolve(IngredientCategoryService::class);
        $this->ingredient->category = $service->getCategory($this->ingredient->title);
        $this->ingredient->save();
    }
}

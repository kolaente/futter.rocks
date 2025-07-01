<?php

namespace App\Jobs;

use App\Models\AdditionalShoppingItem;
use App\Services\IngredientCategoryService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddAdditionalShoppingItemCategory implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(private readonly AdditionalShoppingItem $item) {}

    public function handle(): void
    {
        $service = resolve(IngredientCategoryService::class);
        $this->item->category = $service->getCategory($this->item->title);
        $this->item->save();
    }
}

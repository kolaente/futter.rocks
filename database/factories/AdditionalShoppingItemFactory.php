<?php

namespace Database\Factories;

use App\Models\AdditionalShoppingItem;
use App\Models\Enums\Unit;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AdditionalShoppingItemFactory extends Factory
{
    protected $model = AdditionalShoppingItem::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'shopping_tour_id' => null,
            'title' => $this->faker->word(),
            'quantity' => $this->faker->randomFloat(2, 1, 5),
            'unit' => Unit::Pieces,
            'category' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}

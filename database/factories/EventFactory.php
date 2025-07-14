<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => null,
            'date_from' => Carbon::today(),
            'date_to' => Carbon::today()->addDay(),
            'use_fresh_ingredient_attribute' => true,
            'created_by_id' => User::factory(),
            'team_id' => Team::factory(),
        ];
    }
}

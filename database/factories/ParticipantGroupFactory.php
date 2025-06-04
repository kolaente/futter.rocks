<?php

namespace Database\Factories;

use App\Models\ParticipantGroup;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipantGroupFactory extends Factory
{
    protected $model = ParticipantGroup::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'food_factor' => $this->faker->randomFloat(2, 0.5, 2),
            'team_id' => Team::factory(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Curso;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curso>
 */
class CursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'curso' => fake()->unique()->randomElement([
                'INICIAL 1 A',
                'INICIAL 2 A',
                '1ERO EGB A',
                '2DO EGB A',
                '3ERO EGB A',
                '4TO EGB A',
                '5TO EGB A',
                '6TO EGB A',
                '7MO EGB A',
                '1ERO INFORMATICA A',
                '2DO INFORMATICA A',
                '3ERO INFORMATICA A',
            ]).' '.fake()->unique()->numberBetween(1, 999),
            'team_id' => Team::factory(),
        ];
    }
}

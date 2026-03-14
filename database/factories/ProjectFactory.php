<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Project
 */
class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Project',
            'description' => $this->faker->sentence(),
            'status' => 'active',
            'client_id' => Client::factory(),
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
        ];
    }
}

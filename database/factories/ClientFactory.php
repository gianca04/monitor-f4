<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo Client
 */
class ClientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_name' => fake()->company(),
            'description' => fake()->optional()->text(100),
            'contact_phone' => fake()->optional()->phoneNumber(),
            'contact_email' => fake()->safeEmail(),
        ];
    }
}

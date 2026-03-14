<?php

namespace Database\Factories;

use App\Models\ProjectRequirement;
use App\Models\Project;
use App\Enums\RequirementType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo ProjectRequirement
 */
class ProjectRequirementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProjectRequirement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'requirementable_id' => 1,
            'requirementable_type' => 'App\Models\Tool',
            'dispatch_guide_id' => null,
            'type' => RequirementType::CONSUMIBLE,
            'quantity' => fake()->numberBetween(1, 100),
            'price_unit' => fake()->numberBetween(10, 1000),
            'product_name' => fake()->word() . ' Product',
            'unit_name' => 'UND',
            'requirement_type' => 'Suministro',
            'comments' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Estado para requerimientos con cantidad específica
     */
    public function withQuantity(int $quantity): self
    {
        return $this->state([
            'quantity' => $quantity,
        ]);
    }

    /**
     * Estado para requerimientos con precio unitario específico
     */
    public function withPrice(float $price): self
    {
        return $this->state([
            'price_unit' => $price,
        ]);
    }
}

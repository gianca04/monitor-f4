<?php

namespace Database\Factories;

use App\Models\QuoteWarehouseDetail;
use App\Models\ProjectRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo QuoteWarehouseDetail
 */
class QuoteWarehouseDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuoteWarehouseDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_requirement_id' => ProjectRequirement::factory(),
            'quote_warehouse_id' => null,
            'attended_quantity' => fake()->numberBetween(1, 100),
            'comment' => fake()->optional()->sentence(),
            'location_origin_id' => null,
            'location_destination_id' => null,
            'additional_cost' => fake()->optional()->randomFloat(2, 0, 500),
            'cost_description' => fake()->optional()->sentence(),
            'tool_unit_id' => null,
        ];
    }

    /**
     * Estado con cantidad atendida específica
     */
    public function withAttendedQuantity(int $quantity): self
    {
        return $this->state([
            'attended_quantity' => $quantity,
        ]);
    }

    /**
     * Estado con costo adicional
     */
    public function withAdditionalCost(float $cost): self
    {
        return $this->state([
            'additional_cost' => $cost,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\DispatchTransaction;
use App\Models\ProjectRequirement;
use App\Models\User;
use App\Enums\DispatchSourceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory para el modelo DispatchTransaction
 */
class DispatchTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DispatchTransaction::class;

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
            'employee_id' => User::factory(),
            'quantity' => fake()->numberBetween(1, 50),
            'location_origin_id' => null,
            'location_destination_id' => null,
            'additional_cost' => fake()->optional()->randomFloat(2, 0, 500),
            'cost_description' => fake()->optional()->sentence(),
            'comment' => fake()->optional()->sentence(),
            'tool_unit_id' => null,
            'source_type' => DispatchSourceType::WAREHOUSE,
            'source_reference' => null,
            'dispatch_date' => now(),
        ];
    }

    /**
     * Estado para transacciones desde almacén
     */
    public function warehouse(): self
    {
        return $this->state([
            'source_type' => DispatchSourceType::WAREHOUSE,
        ]);
    }

    /**
     * Estado para transacciones desde proveedor
     */
    public function provider(): self
    {
        return $this->state([
            'source_type' => DispatchSourceType::PROVIDER,
        ]);
    }

    /**
     * Estado para transacciones externas
     */
    public function external(): self
    {
        return $this->state([
            'source_type' => DispatchSourceType::EXTERNAL,
        ]);
    }

    /**
     * Estado con cantidad específica
     */
    public function withQuantity(int $quantity): self
    {
        return $this->state([
            'quantity' => $quantity,
        ]);
    }

    /**
     * Estado sin referencia a empleado
     */
    public function withoutEmployee(): self
    {
        return $this->state([
            'employee_id' => null,
        ]);
    }
}

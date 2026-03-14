<?php

namespace Tests\Unit;

use App\Models\ProjectRequirement;
use App\Models\DispatchTransaction;
use App\Enums\DispatchSourceType;
use Tests\TestCase;

/**
 * Test unitario para el modelo ProjectRequirement
 *
 * Valida:
 * - Agregados calculados (subtotal, total_dispatched, remaining_quantity, total_cost)
 * - Método isFullyDispatched()
 * - Relaciones con DispatchTransaction
 */
class ProjectRequirementTest extends TestCase
{

    /**
     * Test: El subtotal se calcula correctamente (quantity * price_unit)
     */
    public function test_subtotal_is_calculated_correctly(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->withPrice(50.00)
            ->create();

        $expected = 100 * 50.00; // 5000.00
        $this->assertEquals($expected, $projectRequirement->subtotal);
    }

    /**
     * Test: El subtotal suma decimales correctamente
     */
    public function test_subtotal_with_decimal_values(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(33)
            ->withPrice(33.33)
            ->create();

        $expected = round(33 * 33.33, 2); // 1099.89
        $this->assertEquals($expected, $projectRequirement->subtotal);
    }

    /**
     * Test: total_dispatched retorna 0 cuando no hay entregas
     */
    public function test_total_dispatched_is_zero_when_no_transactions(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        $this->assertEquals(0, $projectRequirement->total_dispatched);
    }

    /**
     * Test: total_dispatched suma correctamente múltiples transacciones
     */
    public function test_total_dispatched_sums_all_transactions(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        // Crear 3 transacciones: 40 + 35 + 20 = 95
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(40)
            ->warehouse()
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(35)
            ->provider()
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(20)
            ->external()
            ->create();

        $this->assertEquals(95, $projectRequirement->total_dispatched);
    }

    /**
     * Test: remaining_quantity se calcula correctamente
     */
    public function test_remaining_quantity_calculation(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(40)
            ->create();

        $expected = 100 - 40; // 60
        $this->assertEquals($expected, $projectRequirement->remaining_quantity);
    }

    /**
     * Test: remaining_quantity es 0 cuando está completamente entregado
     */
    public function test_remaining_quantity_is_zero_when_fully_dispatched(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(100)
            ->create();

        $this->assertEquals(0, $projectRequirement->remaining_quantity);
    }

    /**
     * Test: total_cost suma los costos adicionales de entregas
     */
    public function test_total_cost_sums_additional_costs(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        // Crear transacciones con diferentes costos: 100 + 150 + 50 = 300
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(40)
            ->state(['additional_cost' => 100.00])
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(35)
            ->state(['additional_cost' => 150.00])
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(20)
            ->state(['additional_cost' => 50.00])
            ->create();

        $this->assertEquals(300.00, $projectRequirement->total_cost);
    }

    /**
     * Test: total_cost retorna 0 cuando no hay costos adicionales
     */
    public function test_total_cost_is_zero_when_no_additional_costs(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(50)
            ->state(['additional_cost' => null])
            ->create();

        $this->assertEquals(0, $projectRequirement->total_cost);
    }

    /**
     * Test: isFullyDispatched() retorna true cuando cantidad entregada >= cantidad requerida
     */
    public function test_is_fully_dispatched_returns_true(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(100)
            ->create();

        $this->assertTrue($projectRequirement->isFullyDispatched());
    }

    /**
     * Test: isFullyDispatched() retorna true cuando se excede la cantidad
     */
    public function test_is_fully_dispatched_returns_true_when_exceeded(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(150)
            ->create();

        $this->assertTrue($projectRequirement->isFullyDispatched());
    }

    /**
     * Test: isFullyDispatched() retorna false cuando hay cantidad pendiente
     */
    public function test_is_fully_dispatched_returns_false(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(50)
            ->create();

        $this->assertFalse($projectRequirement->isFullyDispatched());
    }

    /**
     * Test: isFullyDispatched() retorna false cuando no hay entregas
     */
    public function test_is_fully_dispatched_returns_false_when_no_transactions(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->create();

        $this->assertFalse($projectRequirement->isFullyDispatched());
    }

    /**
     * Test: La relación dispatchTransactions() está correctamente configurada
     */
    public function test_dispatch_transactions_relationship(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->create();

        $transaction1 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->create();

        $transaction2 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->create();

        DispatchTransaction::factory()->create(); // Transacción para otro requerimiento

        $transactions = $projectRequirement->dispatchTransactions;

        $this->assertCount(2, $transactions);
        $this->assertTrue($transactions->contains($transaction1));
        $this->assertTrue($transactions->contains($transaction2));
    }

    /**
     * Test: Escenario real - Requerimiento atendido desde múltiples fuentes
     *
     * Caso: Se requieren 60 unidades.
     * - Almacén entrega 40 (costo: $100)
     * - Proveedor entrega 20 (costo: $150)
     * Total despachado: 60 unidades
     * Total costo: $250
     */
    public function test_real_scenario_multiple_sources(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(60)
            ->withPrice(10.00)
            ->create();

        // Entrega desde almacén
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->state(['additional_cost' => 100.00])
            ->create();

        // Entrega desde proveedor
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(20)
            ->state(['additional_cost' => 150.00])
            ->create();

        // Aserciones
        $this->assertEquals(60, $projectRequirement->total_dispatched);
        $this->assertEquals(0, $projectRequirement->remaining_quantity);
        $this->assertEquals(250.00, $projectRequirement->total_cost);
        $this->assertTrue($projectRequirement->isFullyDispatched());
        $this->assertEquals(600.00, $projectRequirement->subtotal); // 60 * 10
    }

    /**
     * Test: Escenario real - Atención parcial
     *
     * Caso: Se requieren 100 unidades.
     * - Almacén entrega 40 (costo: $80)
     * - Proveedor entrega 35 (costo: $140)
     * Total despachado: 75 unidades
     * Pendiente: 25 unidades
     */
    public function test_real_scenario_partial_dispatch(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->withPrice(5.00)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->state(['additional_cost' => 80.00])
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(35)
            ->state(['additional_cost' => 140.00])
            ->create();

        // Aserciones
        $this->assertEquals(75, $projectRequirement->total_dispatched);
        $this->assertEquals(25, $projectRequirement->remaining_quantity);
        $this->assertEquals(220.00, $projectRequirement->total_cost);
        $this->assertFalse($projectRequirement->isFullyDispatched());
        $this->assertEquals(500.00, $projectRequirement->subtotal); // 100 * 5
    }
}

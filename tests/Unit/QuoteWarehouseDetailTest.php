<?php

namespace Tests\Unit;

use App\Models\QuoteWarehouseDetail;
use App\Models\ProjectRequirement;
use App\Models\DispatchTransaction;
use App\Enums\DispatchSourceType;
use Tests\TestCase;

/**
 * Test unitario para el modelo QuoteWarehouseDetail (Refactorizado)
 *
 * Valida:
 * - Compatibilidad hacia atrás
 * - Método helper dispatchTransactions()
 * - Filtrado por source_type
 * - Relación con ProjectRequirement
 */
class QuoteWarehouseDetailTest extends TestCase
{

    /**
     * Test: El método dispatchTransactions() retorna las entregas desde almacén
     */
    public function test_dispatch_transactions_relationship(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        // Crear un QuoteWarehouseDetail asociado
        $detail = QuoteWarehouseDetail::factory()
            ->for($projectRequirement)
            ->create();

        // Crear transacciones de diferentes tipos
        $warehouseTransaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->create();

        $providerTransaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(35)
            ->create();

        $externalTransaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->external()
            ->withQuantity(25)
            ->create();

        // El método debe retornar solo transacciones desde almacén
        $warehouseTransactions = $detail->dispatchTransactions();

        $this->assertCount(1, $warehouseTransactions);
        $this->assertTrue($warehouseTransactions->first()->source_type->equals(DispatchSourceType::WAREHOUSE));
        $this->assertEquals($warehouseTransaction->id, $warehouseTransactions->first()->id);
    }

    /**
     * Test: El método dispatchTransactions() filtra correctamente múltiples transacciones
     */
    public function test_dispatch_transactions_filters_warehouse_only(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(200)
            ->create();

        $detail = QuoteWarehouseDetail::factory()
            ->for($projectRequirement)
            ->create();

        // Crear múltiples transacciones desde almacén
        $warehouse1 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->create();

        $warehouse2 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(30)
            ->create();

        // Crear transacciones de otros tipos (NO deben aparecer)
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(50)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->external()
            ->withQuantity(20)
            ->create();

        $warehouseTransactions = $detail->dispatchTransactions();

        // Debe retornar solo 2 (las del almacén)
        $this->assertCount(2, $warehouseTransactions);
        $this->assertTrue(
            $warehouseTransactions->contains(fn ($t) => $t->id === $warehouse1->id)
        );
        $this->assertTrue(
            $warehouseTransactions->contains(fn ($t) => $t->id === $warehouse2->id)
        );
    }

    /**
     * Test: El método dispatchTransactions() retorna query vacía sin ProjectRequirement
     */
    public function test_dispatch_transactions_empty_without_project_requirement(): void
    {
        $detail = QuoteWarehouseDetail::factory()
            ->state(['project_requirement_id' => null])
            ->create();

        $transactions = $detail->dispatchTransactions();

        $this->assertCount(0, $transactions);
    }

    /**
     * Test: Compatibilidad hacia atrás - projectRequirement sigue funcionando
     */
    public function test_project_requirement_relationship_backward_compat(): void
    {
        $projectRequirement = ProjectRequirement::factory()->create();
        
        $detail = QuoteWarehouseDetail::factory()
            ->for($projectRequirement)
            ->create();

        $this->assertInstanceOf(ProjectRequirement::class, $detail->projectRequirement);
        $this->assertEquals($projectRequirement->id, $detail->projectRequirement->id);
    }

    /**
     * Test: Escenario real - Registro consolidado de almacén
     *
     * Un QuoteWarehouseDetail tiene:
     * - 2 transacciones desde almacén (40 + 30 = 70 unidades)
     * - 1 transacción desde proveedor (no aparece en dispatchTransactions)
     * - 1 transacción externa (no aparece en dispatchTransactions)
     */
    public function test_real_scenario_warehouse_consolidation(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(200)
            ->withPrice(50.00)
            ->create();

        $detail = QuoteWarehouseDetail::factory()
            ->for($projectRequirement)
            ->create();

        // Transacciones del almacén
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->state(['additional_cost' => 100.00])
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(30)
            ->state(['additional_cost' => 75.00])
            ->create();

        // Transacciones de otros orígenes
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(80)
            ->state(['additional_cost' => 200.00])
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->external()
            ->withQuantity(50)
            ->state(['additional_cost' => 150.00])
            ->create();

        // Verificar que dispatchTransactions() solo devuelve del almacén
        $warehouseTransactions = $detail->dispatchTransactions();
        
        $this->assertCount(2, $warehouseTransactions);
        
        $totalWarehouseQuantity = $warehouseTransactions->sum('quantity');
        $this->assertEquals(70, $totalWarehouseQuantity);
        
        $totalWarehouseCost = $warehouseTransactions->sum('additional_cost');
        $this->assertEquals(175.00, $totalWarehouseCost);

        // Pero el requerimiento tiene TODAS las entregas
        $this->assertEquals(200, $projectRequirement->total_dispatched);
        $this->assertEquals(525.00, $projectRequirement->total_cost);
    }

    /**
     * Test: El QuoteWarehouseDetail es un filtro útil para consultas de almacén
     */
    public function test_quote_warehouse_detail_as_warehouse_filter(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        $detail = QuoteWarehouseDetail::factory()
            ->for($projectRequirement)
            ->create();

        // Crear transacciones mixtas
        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(35)
            ->create();

        DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(15)
            ->create();

        // Usar dispatchTransactions() como filtro
        $result = $detail->dispatchTransactions()
            ->where('quantity', '>=', 20)
            ->get();

        // Debe retornar solo la primer transacción del almacén (40 >= 20)
        $this->assertCount(1, $result);
        $this->assertEquals(40, $result->first()->quantity);
    }
}

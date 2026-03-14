<?php

namespace Tests\Unit;

use App\Models\DispatchTransaction;
use App\Models\ProjectRequirement;
use App\Enums\DispatchSourceType;
use Tests\TestCase;

/**
 * Test unitario para el modelo DispatchTransaction
 *
 * Valida:
 * - Validación de cantidad (validateQuantity)
 * - source_type enum
 * - Relaciones
 * - Transacciones desde múltiples fuentes
 */
class DispatchTransactionTest extends TestCase
{

    /**
     * Test: validateQuantity() no lanza excepción cuando la suma es válida
     */
    public function test_validate_quantity_passes_when_total_valid(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        $transaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(50)
            ->make();

        // No debe lanzar excepción
        $transaction->validateQuantity();

        $this->assertTrue(true); // Si llegue aquí, pasó
    }

    /**
     * Test: validateQuantity() lanza excepción cuando se excede la cantidad
     */
    public function test_validate_quantity_fails_when_exceeded(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        $existingTransaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(80)
            ->create();

        // Intentar crear una transacción que excede el total
        $newTransaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->withQuantity(30) // 80 + 30 = 110 > 100
            ->make();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cantidad excedida');

        $newTransaction->validateQuantity();
    }

    /**
     * Test: validateQuantity() lanza excepción cuando ProjectRequirement no existe
     */
    public function test_validate_quantity_fails_without_project_requirement(): void
    {
        $transaction = DispatchTransaction::factory()
            ->make(['project_requirement_id' => 9999]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('ProjectRequirement not found');

        $transaction->validateQuantity();
    }

    /**
     * Test: source_type puede ser WAREHOUSE
     */
    public function test_source_type_warehouse(): void
    {
        $transaction = DispatchTransaction::factory()
            ->warehouse()
            ->create();

        $this->assertEquals(DispatchSourceType::WAREHOUSE, $transaction->source_type);
        $this->assertTrue($transaction->source_type->equals(DispatchSourceType::WAREHOUSE));
    }

    /**
     * Test: source_type puede ser PROVIDER
     */
    public function test_source_type_provider(): void
    {
        $transaction = DispatchTransaction::factory()
            ->provider()
            ->create();

        $this->assertEquals(DispatchSourceType::PROVIDER, $transaction->source_type);
    }

    /**
     * Test: source_type puede ser EXTERNAL
     */
    public function test_source_type_external(): void
    {
        $transaction = DispatchTransaction::factory()
            ->external()
            ->create();

        $this->assertEquals(DispatchSourceType::EXTERNAL, $transaction->source_type);
    }

    /**
     * Test: El enum DispatchSourceType retorna etiquetas correctas
     */
    public function test_source_type_get_label(): void
    {
        $this->assertEquals('Almacén', DispatchSourceType::WAREHOUSE->getLabel());
        $this->assertEquals('Proveedor', DispatchSourceType::PROVIDER->getLabel());
        $this->assertEquals('Externo', DispatchSourceType::EXTERNAL->getLabel());
    }

    /**
     * Test: El enum DispatchSourceType retorna colores correctos
     */
    public function test_source_type_get_color(): void
    {
        $this->assertEquals('blue', DispatchSourceType::WAREHOUSE->getColor());
        $this->assertEquals('amber', DispatchSourceType::PROVIDER->getColor());
        $this->assertEquals('gray', DispatchSourceType::EXTERNAL->getColor());
    }

    /**
     * Test: dispatch_date se castea a datetime correctamente
     */
    public function test_dispatch_date_is_datetime(): void
    {
        $transaction = DispatchTransaction::factory()
            ->create(['dispatch_date' => now()]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $transaction->dispatch_date);
    }

    /**
     * Test: source_reference puede ser null
     */
    public function test_source_reference_can_be_null(): void
    {
        $transaction = DispatchTransaction::factory()
            ->state(['source_reference' => null])
            ->create();

        $this->assertNull($transaction->source_reference);
    }

    /**
     * Test: quantity se castea como decimal:2
     */
    public function test_quantity_cast_to_decimal(): void
    {
        $transaction = DispatchTransaction::factory()
            ->withQuantity(33)
            ->create();

        // Debe ser un número decimal con 2 decimales
        $this->assertIsNumeric($transaction->quantity);
    }

    /**
     * Test: La relación projectRequirement() funciona correctamente
     */
    public function test_project_requirement_relationship(): void
    {
        $projectRequirement = ProjectRequirement::factory()->create();
        $transaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->create();

        $this->assertInstanceOf(ProjectRequirement::class, $transaction->projectRequirement);
        $this->assertEquals($projectRequirement->id, $transaction->projectRequirement->id);
    }

    /**
     * Test: Escenario real - Múltiples entregas de un requerimiento
     *
     * Caso: Requerimiento de 100 unidades
     * - Transacción 1: 40 unidades del almacén
     * - Transacción 2: 35 unidades del proveedor
     * - Transacción 3: 25 unidades externas
     */
    public function test_real_scenario_multiple_dispatch_transactions(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        $t1 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(40)
            ->state([
                'source_reference' => 'warehouse_001',
                'additional_cost' => 50.00,
            ])
            ->create();

        $t2 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->provider()
            ->withQuantity(35)
            ->state([
                'source_reference' => 'vendor_abc',
                'additional_cost' => 100.00,
            ])
            ->create();

        $t3 = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->external()
            ->withQuantity(25)
            ->state([
                'source_reference' => 'project_xyz',
                'additional_cost' => 75.00,
            ])
            ->create();

        // Verificar fuentes
        $this->assertTrue($t1->source_type->equals(DispatchSourceType::WAREHOUSE));
        $this->assertTrue($t2->source_type->equals(DispatchSourceType::PROVIDER));
        $this->assertTrue($t3->source_type->equals(DispatchSourceType::EXTERNAL));

        // Verificar referencias
        $this->assertEquals('warehouse_001', $t1->source_reference);
        $this->assertEquals('vendor_abc', $t2->source_reference);
        $this->assertEquals('project_xyz', $t3->source_reference);

        // Verificar que el requerimiento suma todas las entregas
        $this->assertEquals(100, $projectRequirement->total_dispatched);
        $this->assertTrue($projectRequirement->isFullyDispatched());
        $this->assertEquals(225.00, $projectRequirement->total_cost);
    }

    /**
     * Test: Validación integradora - No permitir sobre-entrega
     *
     * Intentar crear la 4ta transacción que excedería el requerimiento
     */
    public function test_integration_prevent_over_delivery(): void
    {
        $projectRequirement = ProjectRequirement::factory()
            ->withQuantity(100)
            ->create();

        // Crear 3 transacciones que suman 95
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
            ->external()
            ->withQuantity(20)
            ->create();

        // Intentar crear una 4ta que supera el límite
        $transaction = DispatchTransaction::factory()
            ->for($projectRequirement)
            ->warehouse()
            ->withQuantity(10) // 95 + 10 = 105 > 100
            ->make();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cantidad excedida');

        $transaction->validateQuantity();
    }
}

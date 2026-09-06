<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InvoiceService;
use App\Services\PurchaseOrderService;
use App\Services\SalesOrderService;
use App\Services\StockAdjustmentService;
use App\Services\StockTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class InventoryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Warehouse $warehouseA;

    private Warehouse $warehouseB;

    private Product $product;

    private Supplier $supplier;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->warehouseA = Warehouse::factory()->create(['code' => 'A']);
        $this->warehouseB = Warehouse::factory()->create(['code' => 'B']);
        $this->product = Product::factory()->create(['cost_price' => 5, 'selling_price' => 10, 'reorder_level' => 5]);
        $this->supplier = Supplier::factory()->create();
        $this->customer = Customer::factory()->create();
    }

    public function test_receiving_a_purchase_order_increases_stock_and_generates_an_invoice(): void
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-000001',
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouseA->id,
            'status' => PurchaseOrder::STATUS_ORDERED,
            'order_date' => now(),
            'subtotal' => 50,
            'total' => 50,
        ]);

        $item = $po->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_cost' => 5,
            'line_total' => 50,
        ]);

        app(PurchaseOrderService::class)->receive($po, [$item->id => 10]);

        $po->refresh();

        $this->assertSame(PurchaseOrder::STATUS_RECEIVED, $po->status);
        $this->assertSame(10, $this->product->fresh()->stockIn($this->warehouseA->id));
        $this->assertNotNull($po->invoice);
        $this->assertSame(Invoice::TYPE_PURCHASE, $po->invoice->type);
        $this->assertEquals(50.0, (float) $po->invoice->total);
    }

    public function test_partial_receiving_leaves_the_order_partially_received(): void
    {
        $po = PurchaseOrder::create([
            'po_number' => 'PO-000002',
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouseA->id,
            'status' => PurchaseOrder::STATUS_ORDERED,
            'order_date' => now(),
            'subtotal' => 100,
            'total' => 100,
        ]);

        $item = $po->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_cost' => 5,
            'line_total' => 50,
        ]);

        app(PurchaseOrderService::class)->receive($po, [$item->id => 4]);

        $po->refresh();

        $this->assertSame(PurchaseOrder::STATUS_PARTIALLY_RECEIVED, $po->status);
        $this->assertSame(4, $this->product->fresh()->stockIn($this->warehouseA->id));
        $this->assertFalse($po->invoice()->exists());
    }

    public function test_fulfilling_a_sales_order_decreases_stock_and_generates_an_invoice(): void
    {
        $this->giveStock($this->warehouseA, 20);

        $so = SalesOrder::create([
            'so_number' => 'SO-000001',
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouseA->id,
            'status' => SalesOrder::STATUS_CONFIRMED,
            'order_date' => now(),
            'subtotal' => 60,
            'total' => 60,
        ]);

        $item = $so->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 6,
            'unit_price' => 10,
            'line_total' => 60,
        ]);

        app(SalesOrderService::class)->fulfill($so, [$item->id => 6]);

        $so->refresh();

        $this->assertSame(SalesOrder::STATUS_FULFILLED, $so->status);
        $this->assertSame(14, $this->product->fresh()->stockIn($this->warehouseA->id));
        $this->assertNotNull($so->invoice);
        $this->assertSame(Invoice::TYPE_SALES, $so->invoice->type);
    }

    public function test_fulfilling_more_than_available_stock_is_rejected_without_oversending(): void
    {
        $this->giveStock($this->warehouseA, 3);

        $so = SalesOrder::create([
            'so_number' => 'SO-000002',
            'customer_id' => $this->customer->id,
            'warehouse_id' => $this->warehouseA->id,
            'status' => SalesOrder::STATUS_CONFIRMED,
            'order_date' => now(),
            'subtotal' => 50,
            'total' => 50,
        ]);

        $item = $so->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 10,
            'line_total' => 50,
        ]);

        $this->expectException(RuntimeException::class);

        try {
            app(SalesOrderService::class)->fulfill($so, [$item->id => 5]);
        } finally {
            $this->assertSame(3, $this->product->fresh()->stockIn($this->warehouseA->id));
            $this->assertSame(0, $item->fresh()->fulfilled_quantity);
        }
    }

    public function test_stock_transfer_moves_quantity_between_warehouses(): void
    {
        $this->giveStock($this->warehouseA, 15);

        app(StockTransferService::class)->create(
            [
                'from_warehouse_id' => $this->warehouseA->id,
                'to_warehouse_id' => $this->warehouseB->id,
                'transfer_date' => now()->toDateString(),
            ],
            [['product_id' => $this->product->id, 'quantity' => 5]],
        );

        $this->assertSame(10, $this->product->fresh()->stockIn($this->warehouseA->id));
        $this->assertSame(5, $this->product->fresh()->stockIn($this->warehouseB->id));
    }

    public function test_stock_adjustment_applies_positive_and_negative_changes(): void
    {
        $this->giveStock($this->warehouseA, 10);

        app(StockAdjustmentService::class)->create(
            [
                'warehouse_id' => $this->warehouseA->id,
                'reason' => 'Cycle count correction',
                'adjustment_date' => now()->toDateString(),
            ],
            [['product_id' => $this->product->id, 'quantity_change' => -3]],
        );

        $this->assertSame(7, $this->product->fresh()->stockIn($this->warehouseA->id));
    }

    public function test_recording_payments_updates_invoice_status(): void
    {
        $invoice = Invoice::create([
            'invoice_number' => 'SINV-000001',
            'type' => Invoice::TYPE_SALES,
            'customer_id' => $this->customer->id,
            'invoice_date' => now(),
            'subtotal' => 100,
            'total' => 100,
            'status' => Invoice::STATUS_UNPAID,
        ]);

        $invoiceService = app(InvoiceService::class);

        $invoiceService->recordPayment($invoice, ['amount' => 40, 'payment_date' => now()->toDateString()]);
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $invoice->fresh()->status);

        $invoiceService->recordPayment($invoice, ['amount' => 60, 'payment_date' => now()->toDateString()]);
        $this->assertSame(Invoice::STATUS_PAID, $invoice->fresh()->status);

        $this->expectException(RuntimeException::class);
        $invoiceService->recordPayment($invoice, ['amount' => 1, 'payment_date' => now()->toDateString()]);
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    private function giveStock(Warehouse $warehouse, int $quantity): void
    {
        $this->product->warehouses()->attach($warehouse->id, ['quantity' => $quantity]);
    }
}

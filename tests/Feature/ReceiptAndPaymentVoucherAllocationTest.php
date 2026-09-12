<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Services\PaymentVoucherService;
use App\Services\ReceiptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ReceiptAndPaymentVoucherAllocationTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;

    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = Customer::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    private function salesInvoice(float $total): Invoice
    {
        return Invoice::create([
            'invoice_number' => 'SINV-'.random_int(100000, 999999),
            'type' => Invoice::TYPE_SALES,
            'customer_id' => $this->customer->id,
            'invoice_date' => now(),
            'subtotal' => $total,
            'total' => $total,
            'status' => Invoice::STATUS_UNPAID,
        ]);
    }

    private function purchaseInvoice(float $total): Invoice
    {
        return Invoice::create([
            'invoice_number' => 'PINV-'.random_int(100000, 999999),
            'type' => Invoice::TYPE_PURCHASE,
            'supplier_id' => $this->supplier->id,
            'invoice_date' => now(),
            'subtotal' => $total,
            'total' => $total,
            'status' => Invoice::STATUS_UNPAID,
        ]);
    }

    public function test_a_receipt_can_be_allocated_across_two_invoices_and_leave_a_balance_on_account(): void
    {
        $invoiceA = $this->salesInvoice(60);
        $invoiceB = $this->salesInvoice(50);

        $receiptService = app(ReceiptService::class);
        $receipt = $receiptService->recordReceipt([
            'customer_id' => $this->customer->id,
            'amount' => 100,
            'receipt_date' => now()->toDateString(),
        ]);

        $this->assertSame('RCPT-000001', $receipt->receipt_number);
        $this->assertSame(100.0, $receipt->unallocatedAmount());

        $receiptService->allocate($receipt, $invoiceA, 60);
        $this->assertSame(Invoice::STATUS_PAID, $invoiceA->fresh()->status);
        $this->assertSame(40.0, $receipt->fresh()->unallocatedAmount());

        $receiptService->allocate($receipt, $invoiceB, 30);
        $this->assertSame(Invoice::STATUS_PARTIALLY_PAID, $invoiceB->fresh()->status);
        $this->assertSame(20.0, $invoiceB->fresh()->balance());

        // The remaining 10 sits unallocated on the customer's account --
        // that's a valid end state, not an error.
        $this->assertSame(10.0, $receipt->fresh()->unallocatedAmount());
    }

    public function test_allocating_more_than_the_receipts_unallocated_balance_is_rejected(): void
    {
        $invoice = $this->salesInvoice(100);

        $receiptService = app(ReceiptService::class);
        $receipt = $receiptService->recordReceipt([
            'customer_id' => $this->customer->id,
            'amount' => 30,
            'receipt_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $receiptService->allocate($receipt, $invoice, 50);
    }

    public function test_allocating_more_than_the_invoices_balance_is_rejected_even_if_the_receipt_covers_it(): void
    {
        $invoice = $this->salesInvoice(40);

        $receiptService = app(ReceiptService::class);
        $receipt = $receiptService->recordReceipt([
            'customer_id' => $this->customer->id,
            'amount' => 100,
            'receipt_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $receiptService->allocate($receipt, $invoice, 50);
    }

    public function test_a_receipt_cannot_be_allocated_to_another_customers_invoice(): void
    {
        $otherCustomer = Customer::factory()->create();
        $invoice = Invoice::create([
            'invoice_number' => 'SINV-900001',
            'type' => Invoice::TYPE_SALES,
            'customer_id' => $otherCustomer->id,
            'invoice_date' => now(),
            'subtotal' => 50,
            'total' => 50,
            'status' => Invoice::STATUS_UNPAID,
        ]);

        $receiptService = app(ReceiptService::class);
        $receipt = $receiptService->recordReceipt([
            'customer_id' => $this->customer->id,
            'amount' => 50,
            'receipt_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $receiptService->allocate($receipt, $invoice, 50);
    }

    public function test_a_receipt_cannot_be_allocated_to_a_purchase_invoice(): void
    {
        $invoice = $this->purchaseInvoice(50);

        $receiptService = app(ReceiptService::class);
        $receipt = $receiptService->recordReceipt([
            'customer_id' => $this->customer->id,
            'amount' => 50,
            'receipt_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $receiptService->allocate($receipt, $invoice, 50);
    }

    public function test_a_payment_voucher_can_be_allocated_across_two_bills_and_leave_a_balance_unallocated(): void
    {
        $billA = $this->purchaseInvoice(70);
        $billB = $this->purchaseInvoice(40);

        $voucherService = app(PaymentVoucherService::class);
        $voucher = $voucherService->recordVoucher([
            'supplier_id' => $this->supplier->id,
            'amount' => 100,
            'payment_date' => now()->toDateString(),
        ]);

        $this->assertSame('PV-000001', $voucher->voucher_number);

        $voucherService->allocate($voucher, $billA, 70);
        $this->assertSame(Invoice::STATUS_PAID, $billA->fresh()->status);

        $voucherService->allocate($voucher, $billB, 20);
        $this->assertSame(20.0, $billB->fresh()->balance());
        $this->assertSame(10.0, $voucher->fresh()->unallocatedAmount());
    }

    public function test_allocating_more_than_the_vouchers_unallocated_balance_is_rejected(): void
    {
        $bill = $this->purchaseInvoice(100);

        $voucherService = app(PaymentVoucherService::class);
        $voucher = $voucherService->recordVoucher([
            'supplier_id' => $this->supplier->id,
            'amount' => 30,
            'payment_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $voucherService->allocate($voucher, $bill, 50);
    }

    public function test_a_voucher_cannot_be_allocated_to_a_sales_invoice(): void
    {
        $invoice = $this->salesInvoice(50);

        $voucherService = app(PaymentVoucherService::class);
        $voucher = $voucherService->recordVoucher([
            'supplier_id' => $this->supplier->id,
            'amount' => 50,
            'payment_date' => now()->toDateString(),
        ]);

        $this->expectException(RuntimeException::class);
        $voucherService->allocate($voucher, $invoice, 50);
    }

    public function test_authenticated_user_can_record_and_allocate_a_receipt_through_the_web_ui(): void
    {
        $user = \App\Models\User::factory()->create();
        $invoice = $this->salesInvoice(50);

        $this->actingAs($user)
            ->post(route('receipts.store'), [
                'customer_id' => $this->customer->id,
                'amount' => 50,
                'receipt_date' => now()->toDateString(),
                'method' => 'cash',
            ])
            ->assertRedirect();

        $receipt = \App\Models\Receipt::first();
        $this->assertNotNull($receipt);

        $this->actingAs($user)
            ->post(route('receipts.allocate', $receipt), [
                'invoice_id' => $invoice->id,
                'amount' => 50,
            ])
            ->assertRedirect(route('receipts.show', $receipt));

        $this->assertSame(Invoice::STATUS_PAID, $invoice->fresh()->status);
    }
}

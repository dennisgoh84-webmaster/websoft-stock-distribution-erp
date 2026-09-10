<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function createFromPurchaseOrder(PurchaseOrder $purchaseOrder, ?int $userId = null): Invoice
    {
        if ($purchaseOrder->invoice()->exists()) {
            return $purchaseOrder->invoice;
        }

        return DB::transaction(function () use ($purchaseOrder, $userId) {
            $subtotal = $purchaseOrder->items->sum(fn ($item) => $item->quantity * $item->unit_cost);

            $invoice = Invoice::create([
                'invoice_number' => DocumentNumber::generate('invoice', 'PINV'),
                'type' => Invoice::TYPE_PURCHASE,
                'supplier_id' => $purchaseOrder->supplier_id,
                'user_id' => $userId,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total' => $subtotal,
                'status' => Invoice::STATUS_UNPAID,
            ]);

            $purchaseOrder->invoice()->save($invoice);

            foreach ($purchaseOrder->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_cost,
                    'line_total' => $item->quantity * $item->unit_cost,
                ]);
            }

            return $invoice;
        });
    }

    public function createFromSalesOrder(SalesOrder $salesOrder, ?int $userId = null): Invoice
    {
        if ($salesOrder->invoice()->exists()) {
            return $salesOrder->invoice;
        }

        return DB::transaction(function () use ($salesOrder, $userId) {
            $subtotal = $salesOrder->items->sum(fn ($item) => $item->quantity * $item->unit_price);

            $invoice = Invoice::create([
                'invoice_number' => DocumentNumber::generate('invoice', 'SINV'),
                'type' => Invoice::TYPE_SALES,
                'customer_id' => $salesOrder->customer_id,
                'user_id' => $userId,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total' => $subtotal,
                'status' => Invoice::STATUS_UNPAID,
            ]);

            $salesOrder->invoice()->save($invoice);

            foreach ($salesOrder->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item->product_id,
                    'description' => $item->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->quantity * $item->unit_price,
                ]);
            }

            return $invoice;
        });
    }

    public function recordPayment(Invoice $invoice, array $data, ?int $userId = null): Payment
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new RuntimeException('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use ($invoice, $data, $amount, $userId) {
            // Lock the invoice row before re-checking the balance: without
            // this, two simultaneous payments could each read the same
            // stale balance, both pass the overpayment check, and both
            // commit — over-paying the invoice and corrupting amount_paid
            // via a lost update.
            $invoice = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();
            $balance = $invoice->balance();

            if ($amount > $balance + 0.001) {
                throw new RuntimeException("Payment of {$amount} exceeds the outstanding balance of {$balance}.");
            }

            $payment = Payment::create([
                'payment_number' => DocumentNumber::generate('payment', 'PAY'),
                'invoice_id' => $invoice->id,
                'user_id' => $userId,
                'amount' => $amount,
                'payment_date' => $data['payment_date'] ?? now()->toDateString(),
                'method' => $data['method'] ?? Payment::METHOD_CASH,
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->amount_paid = (float) $invoice->amount_paid + $amount;
            $invoice->status = $invoice->balance() <= 0.001
                ? Invoice::STATUS_PAID
                : Invoice::STATUS_PARTIALLY_PAID;
            $invoice->save();

            return $payment;
        });
    }
}

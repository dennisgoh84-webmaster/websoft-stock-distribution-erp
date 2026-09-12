<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\ReceiptAllocation;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Records money received from a Customer independently of any one
 * invoice, then knocks it off against one or more of that customer's
 * outstanding sales invoices. See Receipt's docblock for why this exists
 * alongside InvoiceService::recordPayment rather than replacing it.
 */
class ReceiptService
{
    public function __construct(protected InvoiceService $invoiceService) {}

    public function recordReceipt(array $data, ?int $userId = null): Receipt
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new RuntimeException('Receipt amount must be greater than zero.');
        }

        return Receipt::create([
            'receipt_number' => DocumentNumber::generate('receipt', 'RCPT'),
            'customer_id' => $data['customer_id'],
            'user_id' => $userId,
            'amount' => $amount,
            'receipt_date' => $data['receipt_date'] ?? now()->toDateString(),
            'method' => $data['method'] ?? Receipt::METHOD_CASH,
            'reference_no' => $data['reference_no'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function allocate(Receipt $receipt, Invoice $invoice, float $amount, ?int $userId = null): ReceiptAllocation
    {
        if ($amount <= 0) {
            throw new RuntimeException('Allocation amount must be greater than zero.');
        }

        if ($invoice->type !== Invoice::TYPE_SALES || $invoice->customer_id !== $receipt->customer_id) {
            throw new RuntimeException('A Receipt can only be allocated to a sales invoice belonging to the same customer.');
        }

        return DB::transaction(function () use ($receipt, $invoice, $amount, $userId) {
            // Lock the receipt row before re-checking its unallocated
            // balance — same lost-update risk InvoiceService::applyAmount
            // guards against on the invoice side, here for two concurrent
            // allocations of the same receipt. Locked first, then the
            // invoice (via applyAmount): every caller that locks both
            // acquires them in this order, so the two never deadlock
            // against each other.
            $receipt = Receipt::whereKey($receipt->id)->lockForUpdate()->firstOrFail();
            $unallocated = $receipt->unallocatedAmount();

            if ($amount > $unallocated + 0.001) {
                throw new RuntimeException("Allocation of {$amount} exceeds the receipt's unallocated balance of {$unallocated}.");
            }

            $this->invoiceService->applyAmount($invoice, $amount);

            return ReceiptAllocation::create([
                'receipt_id' => $receipt->id,
                'invoice_id' => $invoice->id,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        });
    }
}

<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PaymentVoucher;
use App\Models\PaymentVoucherAllocation;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Records money paid to a Supplier independently of any one invoice,
 * then knocks it off against one or more of that supplier's outstanding
 * purchase invoices. The AP mirror of ReceiptService.
 */
class PaymentVoucherService
{
    public function __construct(protected InvoiceService $invoiceService) {}

    public function recordVoucher(array $data, ?int $userId = null): PaymentVoucher
    {
        $amount = (float) $data['amount'];

        if ($amount <= 0) {
            throw new RuntimeException('Payment voucher amount must be greater than zero.');
        }

        return PaymentVoucher::create([
            'voucher_number' => DocumentNumber::generate('payment_voucher', 'PV'),
            'supplier_id' => $data['supplier_id'],
            'user_id' => $userId,
            'amount' => $amount,
            'payment_date' => $data['payment_date'] ?? now()->toDateString(),
            'method' => $data['method'] ?? PaymentVoucher::METHOD_CASH,
            'reference_no' => $data['reference_no'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function allocate(PaymentVoucher $voucher, Invoice $invoice, float $amount, ?int $userId = null): PaymentVoucherAllocation
    {
        if ($amount <= 0) {
            throw new RuntimeException('Allocation amount must be greater than zero.');
        }

        if ($invoice->type !== Invoice::TYPE_PURCHASE || $invoice->supplier_id !== $voucher->supplier_id) {
            throw new RuntimeException('A Payment Voucher can only be allocated to a purchase invoice belonging to the same supplier.');
        }

        return DB::transaction(function () use ($voucher, $invoice, $amount, $userId) {
            // Same lock ordering as ReceiptService::allocate: the voucher
            // row first, then the invoice (via applyAmount).
            $voucher = PaymentVoucher::whereKey($voucher->id)->lockForUpdate()->firstOrFail();
            $unallocated = $voucher->unallocatedAmount();

            if ($amount > $unallocated + 0.001) {
                throw new RuntimeException("Allocation of {$amount} exceeds the voucher's unallocated balance of {$unallocated}.");
            }

            $this->invoiceService->applyAmount($invoice, $amount);

            return PaymentVoucherAllocation::create([
                'payment_voucher_id' => $voucher->id,
                'invoice_id' => $invoice->id,
                'user_id' => $userId,
                'amount' => $amount,
            ]);
        });
    }
}

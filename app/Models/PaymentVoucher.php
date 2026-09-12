<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Money paid to a Supplier, recorded independently of any one invoice.
 * Allocated ("knocked off") against one or more of that supplier's
 * outstanding purchase invoices via PaymentVoucherAllocation -- the AP
 * mirror of Receipt/ReceiptAllocation. See App\Services\PaymentVoucherService.
 */
class PaymentVoucher extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const METHOD_CHEQUE = 'cheque';

    public const METHOD_OTHER = 'other';

    protected $fillable = [
        'voucher_number',
        'supplier_id',
        'user_id',
        'amount',
        'payment_date',
        'method',
        'reference_no',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentVoucherAllocation::class);
    }

    public function allocatedAmount(): float
    {
        return round((float) $this->allocations()->sum('amount'), 2);
    }

    public function unallocatedAmount(): float
    {
        return round((float) $this->amount - $this->allocatedAmount(), 2);
    }

    public function allocationStatus(): string
    {
        $unallocated = $this->unallocatedAmount();

        if ($unallocated <= 0.001) {
            return 'fully_allocated';
        }

        if ($unallocated >= (float) $this->amount - 0.001) {
            return 'unallocated';
        }

        return 'partially_allocated';
    }
}

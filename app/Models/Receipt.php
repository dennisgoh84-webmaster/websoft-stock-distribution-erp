<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Money received from a Customer, recorded independently of any one
 * invoice. Allocated ("knocked off") against one or more of that
 * customer's outstanding sales invoices via ReceiptAllocation -- see
 * App\Services\ReceiptService. Money not yet allocated sits on the
 * customer's account (unallocatedAmount() > 0); that's expected, not an
 * error, mirroring how the Payment model still handles the common case
 * of paying one invoice in full directly.
 */
class Receipt extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';

    public const METHOD_BANK_TRANSFER = 'bank_transfer';

    public const METHOD_CHEQUE = 'cheque';

    public const METHOD_OTHER = 'other';

    protected $fillable = [
        'receipt_number',
        'customer_id',
        'user_id',
        'amount',
        'receipt_date',
        'method',
        'reference_no',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'receipt_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ReceiptAllocation::class);
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

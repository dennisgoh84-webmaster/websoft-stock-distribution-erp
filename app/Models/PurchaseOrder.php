<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUS_ORDERED = 'ordered';

    public const STATUS_PARTIALLY_RECEIVED = 'partially_received';

    public const STATUS_RECEIVED = 'received';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'po_number',
        'supplier_id',
        'warehouse_id',
        'user_id',
        'status',
        'order_date',
        'expected_date',
        'notes',
        'subtotal',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_date' => 'date',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'source');
    }

    public function isFullyReceived(): bool
    {
        return $this->items->every(fn (PurchaseOrderItem $item) => $item->received_quantity >= $item->quantity);
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_ORDERED], true);
    }
}

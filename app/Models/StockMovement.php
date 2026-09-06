<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    public const TYPE_PURCHASE_IN = 'purchase_in';

    public const TYPE_SALE_OUT = 'sale_out';

    public const TYPE_TRANSFER_IN = 'transfer_in';

    public const TYPE_TRANSFER_OUT = 'transfer_out';

    public const TYPE_ADJUSTMENT = 'adjustment';

    public const TYPE_INITIAL = 'initial';

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'user_id',
        'notes',
    ];

    public static function typeLabels(): array
    {
        return [
            self::TYPE_PURCHASE_IN => 'Purchase Receipt',
            self::TYPE_SALE_OUT => 'Sale Fulfillment',
            self::TYPE_TRANSFER_IN => 'Transfer In',
            self::TYPE_TRANSFER_OUT => 'Transfer Out',
            self::TYPE_ADJUSTMENT => 'Stock Adjustment',
            self::TYPE_INITIAL => 'Initial Stock',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'category_id',
        'unit_id',
        'cost_price',
        'selling_price',
        'reorder_level',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Total quantity on hand across all warehouses.
     */
    public function totalStock(): int
    {
        return (int) $this->warehouses()->sum('product_warehouse.quantity');
    }

    public function stockIn(int $warehouseId): int
    {
        $pivot = $this->warehouses()->where('warehouse_id', $warehouseId)->first();

        return (int) ($pivot?->pivot?->quantity ?? 0);
    }

    public function isLowStock(): bool
    {
        return $this->totalStock() <= $this->reorder_level;
    }
}

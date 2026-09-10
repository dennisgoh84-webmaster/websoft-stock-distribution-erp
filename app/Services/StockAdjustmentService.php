<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockAdjustmentService
{
    public function __construct(protected StockService $stockService) {}

    /**
     * @param  array<int, array{product_id: int, quantity_change: int}>  $items
     */
    public function create(array $attributes, array $items, ?int $userId = null): StockAdjustment
    {
        $items = array_values(array_filter($items, fn ($item) => (int) $item['quantity_change'] !== 0));

        if (empty($items)) {
            throw new RuntimeException('A stock adjustment needs at least one non-zero line.');
        }

        // Generated before the transaction below opens — see
        // App\Support\DocumentNumber on why that matters for speed.
        $adjustmentNumber = DocumentNumber::generate('stock_adjustment', 'ADJ');

        return DB::transaction(function () use ($attributes, $items, $userId, $adjustmentNumber) {
            $adjustment = StockAdjustment::create([
                ...$attributes,
                'adjustment_number' => $adjustmentNumber,
                'user_id' => $userId,
            ]);

            $adjustment->loadMissing('warehouse');

            $products = Product::whereIn('id', collect($items)->pluck('product_id'))->get()->keyBy('id');

            $now = now();
            StockAdjustmentItem::insert(
                collect($items)->map(fn ($item) => [
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'quantity_change' => (int) $item['quantity_change'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );

            foreach ($items as $item) {
                $this->stockService->move(
                    product: $products[$item['product_id']],
                    warehouse: $adjustment->warehouse,
                    quantity: (int) $item['quantity_change'],
                    type: StockMovement::TYPE_ADJUSTMENT,
                    reference: $adjustment,
                    notes: "{$adjustment->adjustment_number}: {$adjustment->reason}",
                    userId: $userId,
                );
            }

            return $adjustment;
        });
    }
}

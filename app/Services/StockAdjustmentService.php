<?php

namespace App\Services;

use App\Models\StockAdjustment;
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

        return DB::transaction(function () use ($attributes, $items, $userId) {
            $adjustment = StockAdjustment::create([
                ...$attributes,
                'adjustment_number' => DocumentNumber::generate(StockAdjustment::class, 'ADJ'),
                'user_id' => $userId,
            ]);

            $adjustment->loadMissing('warehouse');

            foreach ($items as $item) {
                $quantityChange = (int) $item['quantity_change'];

                $adjustmentItem = $adjustment->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity_change' => $quantityChange,
                ]);

                $this->stockService->move(
                    product: $adjustmentItem->product,
                    warehouse: $adjustment->warehouse,
                    quantity: $quantityChange,
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

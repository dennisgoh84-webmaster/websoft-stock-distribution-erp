<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Support\DocumentNumber;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockTransferService
{
    public function __construct(protected StockService $stockService) {}

    /**
     * @param  array<int, array{product_id: int, quantity: int}>  $items
     */
    public function create(array $attributes, array $items, ?int $userId = null): StockTransfer
    {
        if ($attributes['from_warehouse_id'] === $attributes['to_warehouse_id']) {
            throw new RuntimeException('Source and destination warehouses must be different.');
        }

        if (empty($items)) {
            throw new RuntimeException('A stock transfer needs at least one item.');
        }

        // Generated before the transaction below opens — see
        // App\Support\DocumentNumber on why that matters for speed.
        $transferNumber = DocumentNumber::generate('stock_transfer', 'TRF');

        return DB::transaction(function () use ($attributes, $items, $userId, $transferNumber) {
            $transfer = StockTransfer::create([
                ...$attributes,
                'transfer_number' => $transferNumber,
                'user_id' => $userId,
                'status' => StockTransfer::STATUS_PENDING,
            ]);

            $now = now();
            StockTransferItem::insert(
                collect($items)->map(fn ($item) => [
                    'stock_transfer_id' => $transfer->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all()
            );

            return $this->complete($transfer, $userId);
        });
    }

    public function complete(StockTransfer $transfer, ?int $userId = null): StockTransfer
    {
        if ($transfer->status !== StockTransfer::STATUS_PENDING) {
            throw new RuntimeException('This transfer has already been processed.');
        }

        return DB::transaction(function () use ($transfer, $userId) {
            $transfer->loadMissing('items.product', 'fromWarehouse', 'toWarehouse');

            foreach ($transfer->items as $item) {
                $this->stockService->move(
                    product: $item->product,
                    warehouse: $transfer->fromWarehouse,
                    quantity: -$item->quantity,
                    type: StockMovement::TYPE_TRANSFER_OUT,
                    reference: $transfer,
                    notes: "Transfer {$transfer->transfer_number} to {$transfer->toWarehouse->name}",
                    userId: $userId,
                );

                $this->stockService->move(
                    product: $item->product,
                    warehouse: $transfer->toWarehouse,
                    quantity: $item->quantity,
                    type: StockMovement::TYPE_TRANSFER_IN,
                    reference: $transfer,
                    notes: "Transfer {$transfer->transfer_number} from {$transfer->fromWarehouse->name}",
                    userId: $userId,
                );
            }

            $transfer->update(['status' => StockTransfer::STATUS_COMPLETED]);

            return $transfer;
        });
    }
}

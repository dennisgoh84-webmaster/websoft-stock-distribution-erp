<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\StockTransfer;
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

        return DB::transaction(function () use ($attributes, $items, $userId) {
            $transfer = StockTransfer::create([
                ...$attributes,
                'transfer_number' => DocumentNumber::generate(StockTransfer::class, 'TRF'),
                'user_id' => $userId,
                'status' => StockTransfer::STATUS_PENDING,
            ]);

            foreach ($items as $item) {
                $transfer->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

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

<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PurchaseOrderService
{
    public function __construct(
        protected StockService $stockService,
        protected InvoiceService $invoiceService,
    ) {}

    /**
     * Receive stock against a purchase order.
     *
     * @param  array<int, int>  $quantities  [purchase_order_item_id => quantity received now]
     */
    public function receive(PurchaseOrder $purchaseOrder, array $quantities, ?int $userId = null): PurchaseOrder
    {
        if (! in_array($purchaseOrder->status, [
            PurchaseOrder::STATUS_ORDERED,
            PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
        ], true)) {
            throw new RuntimeException('Only ordered or partially received purchase orders can be received.');
        }

        return DB::transaction(function () use ($purchaseOrder, $quantities, $userId) {
            $purchaseOrder->loadMissing('items.product', 'warehouse');

            foreach ($purchaseOrder->items as $item) {
                $requested = (int) ($quantities[$item->id] ?? 0);
                $qty = min($requested, $item->remainingQuantity());

                if ($qty <= 0) {
                    continue;
                }

                $this->stockService->move(
                    product: $item->product,
                    warehouse: $purchaseOrder->warehouse,
                    quantity: $qty,
                    type: StockMovement::TYPE_PURCHASE_IN,
                    reference: $purchaseOrder,
                    notes: "Received against {$purchaseOrder->po_number}",
                    userId: $userId,
                );

                $item->increment('received_quantity', $qty);
            }

            $purchaseOrder->refresh()->load('items');
            $purchaseOrder->status = $purchaseOrder->isFullyReceived()
                ? PurchaseOrder::STATUS_RECEIVED
                : PurchaseOrder::STATUS_PARTIALLY_RECEIVED;
            $purchaseOrder->save();

            if ($purchaseOrder->status === PurchaseOrder::STATUS_RECEIVED) {
                $this->invoiceService->createFromPurchaseOrder($purchaseOrder, $userId);
            }

            return $purchaseOrder;
        });
    }

    public function cancel(PurchaseOrder $purchaseOrder): PurchaseOrder
    {
        if (! $purchaseOrder->canCancel()) {
            throw new RuntimeException('This purchase order can no longer be cancelled.');
        }

        $purchaseOrder->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

        return $purchaseOrder;
    }
}

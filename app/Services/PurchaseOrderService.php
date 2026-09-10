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
        return DB::transaction(function () use ($purchaseOrder, $quantities, $userId) {
            // Lock the order row for the rest of this transaction: this
            // serializes concurrent receive() calls against the same PO
            // (e.g. two staff submitting the receiving form at once) so the
            // status check below, the item increments, and the final
            // status/invoice decision all act on a consistent snapshot.
            $purchaseOrder = PurchaseOrder::whereKey($purchaseOrder->id)->lockForUpdate()->firstOrFail();
            $purchaseOrder->loadMissing('items.product', 'warehouse');

            if (! in_array($purchaseOrder->status, [
                PurchaseOrder::STATUS_ORDERED,
                PurchaseOrder::STATUS_PARTIALLY_RECEIVED,
            ], true)) {
                throw new RuntimeException('Only ordered or partially received purchase orders can be received.');
            }

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

            // Re-eager-load items.product (not just items — refresh() drops
            // relations, and InvoiceService needs each item's product name)
            // so createFromPurchaseOrder() below doesn't lazy-load the
            // product once per line item.
            $purchaseOrder->refresh()->load('items.product');
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
        return DB::transaction(function () use ($purchaseOrder) {
            // Same lock as receive(): stops a cancel racing a concurrent
            // receive on the same order.
            $purchaseOrder = PurchaseOrder::whereKey($purchaseOrder->id)->lockForUpdate()->firstOrFail();

            if (! $purchaseOrder->canCancel()) {
                throw new RuntimeException('This purchase order can no longer be cancelled.');
            }

            $purchaseOrder->update(['status' => PurchaseOrder::STATUS_CANCELLED]);

            return $purchaseOrder;
        });
    }
}

<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SalesOrderService
{
    public function __construct(
        protected StockService $stockService,
        protected InvoiceService $invoiceService,
    ) {}

    /**
     * Fulfill (ship) stock against a sales order.
     *
     * @param  array<int, int>  $quantities  [sales_order_item_id => quantity fulfilled now]
     */
    public function fulfill(SalesOrder $salesOrder, array $quantities, ?int $userId = null): SalesOrder
    {
        if (! in_array($salesOrder->status, [
            SalesOrder::STATUS_CONFIRMED,
            SalesOrder::STATUS_PARTIALLY_FULFILLED,
        ], true)) {
            throw new RuntimeException('Only confirmed or partially fulfilled sales orders can be fulfilled.');
        }

        return DB::transaction(function () use ($salesOrder, $quantities, $userId) {
            $salesOrder->loadMissing('items.product', 'warehouse');

            foreach ($salesOrder->items as $item) {
                $requested = (int) ($quantities[$item->id] ?? 0);
                $qty = min($requested, $item->remainingQuantity());

                if ($qty <= 0) {
                    continue;
                }

                $this->stockService->move(
                    product: $item->product,
                    warehouse: $salesOrder->warehouse,
                    quantity: -$qty,
                    type: StockMovement::TYPE_SALE_OUT,
                    reference: $salesOrder,
                    notes: "Fulfilled against {$salesOrder->so_number}",
                    userId: $userId,
                );

                $item->increment('fulfilled_quantity', $qty);
            }

            $salesOrder->refresh()->load('items');
            $salesOrder->status = $salesOrder->isFullyFulfilled()
                ? SalesOrder::STATUS_FULFILLED
                : SalesOrder::STATUS_PARTIALLY_FULFILLED;
            $salesOrder->save();

            if ($salesOrder->status === SalesOrder::STATUS_FULFILLED) {
                $this->invoiceService->createFromSalesOrder($salesOrder, $userId);
            }

            return $salesOrder;
        });
    }

    public function cancel(SalesOrder $salesOrder): SalesOrder
    {
        if (! $salesOrder->canCancel()) {
            throw new RuntimeException('This sales order can no longer be cancelled.');
        }

        $salesOrder->update(['status' => SalesOrder::STATUS_CANCELLED]);

        return $salesOrder;
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Move stock for a product in a warehouse and record it on the ledger.
     *
     * Safe under concurrent callers: the product/warehouse balance row is
     * row-locked (SELECT ... FOR UPDATE) for the duration of the update, so
     * two simultaneous movements against the same product+warehouse are
     * serialized instead of racing on a stale read (which would otherwise
     * lose an update or let stock go negative under load). This requires a
     * real RDBMS with row-level locking (PostgreSQL/MySQL) — SQLite ignores
     * lockForUpdate() and should only be used for low-concurrency/local use.
     *
     * @param  int  $quantity  Positive to increase stock, negative to decrease it.
     */
    public function move(
        Product $product,
        Warehouse $warehouse,
        int $quantity,
        string $type,
        ?Model $reference = null,
        ?string $notes = null,
        ?int $userId = null,
    ): StockMovement {
        if ($quantity === 0) {
            throw new RuntimeException('Stock movement quantity cannot be zero.');
        }

        return DB::transaction(function () use ($product, $warehouse, $quantity, $type, $reference, $notes, $userId) {
            $pivot = $this->lockPivotRow($product->id, $warehouse->id);
            $currentQuantity = (int) $pivot->quantity;
            $newQuantity = $currentQuantity + $quantity;

            if ($newQuantity < 0) {
                throw new RuntimeException(
                    "Insufficient stock for \"{$product->name}\" at {$warehouse->name}: ".
                    "have {$currentQuantity}, requested ".abs($quantity).'.'
                );
            }

            DB::table('product_warehouse')
                ->where('id', $pivot->id)
                ->update(['quantity' => $newQuantity, 'updated_at' => now()]);

            return StockMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'type' => $type,
                'quantity' => $quantity,
                'balance_after' => $newQuantity,
                'reference_type' => $reference?->getMorphClass(),
                'reference_id' => $reference?->getKey(),
                'user_id' => $userId,
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Fetch the product/warehouse balance row locked for the current
     * transaction, creating it (at zero) first if it doesn't exist yet.
     *
     * The create-if-missing step is itself race-safe: if two transactions
     * both find no row and both try to insert, the table's unique
     * (product_id, warehouse_id) constraint lets only one succeed — the
     * loser simply falls through to re-select, which then blocks on the
     * winner's row lock until it commits.
     */
    private function lockPivotRow(int $productId, int $warehouseId): object
    {
        $pivot = DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();

        if ($pivot) {
            return $pivot;
        }

        try {
            DB::table('product_warehouse')->insert([
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (QueryException $e) {
            // Lost the race to create the row — another concurrent
            // transaction inserted it first, which is fine: fall through
            // and lock its row instead.
        }

        return DB::table('product_warehouse')
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->lockForUpdate()
            ->first();
    }
}

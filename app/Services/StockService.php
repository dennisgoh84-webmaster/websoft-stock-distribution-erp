<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class StockService
{
    /**
     * Move stock for a product in a warehouse and record it on the ledger.
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

        $pivot = $product->warehouses()->where('warehouse_id', $warehouse->id)->first();
        $currentQuantity = (int) ($pivot?->pivot?->quantity ?? 0);
        $newQuantity = $currentQuantity + $quantity;

        if ($newQuantity < 0) {
            throw new RuntimeException(
                "Insufficient stock for \"{$product->name}\" at {$warehouse->name}: ".
                "have {$currentQuantity}, requested ".abs($quantity).'.'
            );
        }

        if ($pivot) {
            $product->warehouses()->updateExistingPivot($warehouse->id, ['quantity' => $newQuantity]);
        } else {
            $product->warehouses()->attach($warehouse->id, ['quantity' => $newQuantity]);
        }

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
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(StockService $stockService): void
    {
        $categories = collect(['Beverages', 'Snacks', 'Household', 'Electronics'])
            ->mapWithKeys(fn ($name) => [$name => Category::firstOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name]
            )]);

        $units = collect([
            ['name' => 'Piece', 'short_name' => 'pcs'],
            ['name' => 'Box', 'short_name' => 'box'],
            ['name' => 'Carton', 'short_name' => 'ctn'],
            ['name' => 'Kilogram', 'short_name' => 'kg'],
        ])->mapWithKeys(fn ($unit) => [$unit['name'] => Unit::firstOrCreate(['name' => $unit['name']], $unit)]);

        $mainWarehouse = Warehouse::firstOrCreate(
            ['code' => 'MAIN'],
            ['name' => 'Main Warehouse', 'address' => '1 Distribution Way', 'phone' => '555-0100', 'is_active' => true]
        );

        $northWarehouse = Warehouse::firstOrCreate(
            ['code' => 'NORTH'],
            ['name' => 'North Depot', 'address' => '22 North Road', 'phone' => '555-0200', 'is_active' => true]
        );

        collect([
            ['name' => 'Acme Supplies Co.', 'code' => 'SUP-001', 'contact_person' => 'John Baker', 'email' => 'sales@acmesupplies.test', 'phone' => '555-1001'],
            ['name' => 'Global Goods Ltd.', 'code' => 'SUP-002', 'contact_person' => 'Maria Lopez', 'email' => 'orders@globalgoods.test', 'phone' => '555-1002'],
        ])->each(fn ($supplier) => Supplier::firstOrCreate(['code' => $supplier['code']], [...$supplier, 'is_active' => true]));

        collect([
            ['name' => 'Riverside Mart', 'code' => 'CUS-001', 'contact_person' => 'Alex Chen', 'email' => 'purchasing@riversidemart.test', 'phone' => '555-2001', 'credit_limit' => 5000],
            ['name' => 'Hilltop Retailers', 'code' => 'CUS-002', 'contact_person' => 'Priya Nair', 'email' => 'orders@hilltopretail.test', 'phone' => '555-2002', 'credit_limit' => 3000],
        ])->each(fn ($customer) => Customer::firstOrCreate(['code' => $customer['code']], [...$customer, 'is_active' => true]));

        $products = [
            ['sku' => 'BEV-001', 'name' => 'Sparkling Water 500ml', 'category' => 'Beverages', 'unit' => 'Piece', 'cost_price' => 0.40, 'selling_price' => 0.90, 'reorder_level' => 50, 'stock' => 200],
            ['sku' => 'BEV-002', 'name' => 'Orange Juice 1L', 'category' => 'Beverages', 'unit' => 'Piece', 'cost_price' => 1.10, 'selling_price' => 2.20, 'reorder_level' => 30, 'stock' => 120],
            ['sku' => 'SNK-001', 'name' => 'Potato Chips 150g', 'category' => 'Snacks', 'unit' => 'Piece', 'cost_price' => 0.60, 'selling_price' => 1.30, 'reorder_level' => 40, 'stock' => 15],
            ['sku' => 'SNK-002', 'name' => 'Mixed Nuts 200g', 'category' => 'Snacks', 'unit' => 'Box', 'cost_price' => 2.00, 'selling_price' => 3.75, 'reorder_level' => 20, 'stock' => 80],
            ['sku' => 'HH-001', 'name' => 'Dish Soap 750ml', 'category' => 'Household', 'unit' => 'Piece', 'cost_price' => 0.85, 'selling_price' => 1.95, 'reorder_level' => 25, 'stock' => 60],
            ['sku' => 'HH-002', 'name' => 'Paper Towels (6-pack)', 'category' => 'Household', 'unit' => 'Carton', 'cost_price' => 3.20, 'selling_price' => 5.50, 'reorder_level' => 15, 'stock' => 10],
            ['sku' => 'ELE-001', 'name' => 'AA Batteries (4-pack)', 'category' => 'Electronics', 'unit' => 'Box', 'cost_price' => 1.50, 'selling_price' => 3.00, 'reorder_level' => 20, 'stock' => 90],
            ['sku' => 'ELE-002', 'name' => 'USB-C Cable 1m', 'category' => 'Electronics', 'unit' => 'Piece', 'cost_price' => 1.80, 'selling_price' => 4.50, 'reorder_level' => 15, 'stock' => 45],
        ];

        foreach ($products as $data) {
            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'category_id' => $categories[$data['category']]->id,
                    'unit_id' => $units[$data['unit']]->id,
                    'cost_price' => $data['cost_price'],
                    'selling_price' => $data['selling_price'],
                    'reorder_level' => $data['reorder_level'],
                    'is_active' => true,
                ]
            );

            if (! $product->stockMovements()->where('warehouse_id', $mainWarehouse->id)->exists()) {
                $stockService->move(
                    product: $product,
                    warehouse: $mainWarehouse,
                    quantity: $data['stock'],
                    type: StockMovement::TYPE_INITIAL,
                    notes: 'Opening stock balance',
                );
            }
        }

        $this->command?->info('Demo data seeded: categories, units, warehouses ('.$mainWarehouse->name.', '.$northWarehouse->name.'), suppliers, customers, and products.');
    }
}

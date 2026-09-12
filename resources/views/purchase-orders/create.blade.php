<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('New Purchase Order') }}</h2>
    </x-slot>

    <div class="py-6" x-data="purchaseOrderForm({{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'cost_price' => (float) $p->cost_price])->values() }})">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                <form method="POST" action="{{ route('purchase-orders.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="supplier_id" :value="__('Supplier')" />
                            <select id="supplier_id" name="supplier_id" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                                <option value="">{{ __('Select supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="warehouse_id" :value="__('Receiving Warehouse')" />
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                                <option value="">{{ __('Select warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="order_date" :value="__('Order Date')" />
                            <x-text-input id="order_date" name="order_date" type="date" class="mt-1 block w-full" value="{{ old('order_date', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('order_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="expected_date" :value="__('Expected Date')" />
                            <x-text-input id="expected_date" name="expected_date" type="date" class="mt-1 block w-full" value="{{ old('expected_date') }}" />
                            <x-input-error :messages="$errors->get('expected_date')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-slate-700">{{ __('Items') }}</h3>
                            <x-secondary-button type="button" @click="addItem()">{{ __('Add Item') }}</x-secondary-button>
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mb-2" />

                        <table class="min-w-full divide-y divide-slate-200 border">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-28">{{ __('Quantity') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ __('Unit Cost') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ __('Line Total') }}</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="applyDefaultCost(item)" class="block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm" required>
                                                <option value="">{{ __('Select product') }}</option>
                                                <template x-for="product in products" :key="product.id">
                                                    <option :value="product.id" x-text="product.name + ' (' + product.sku + ')'"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="1" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm text-right" required>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="0" step="0.01" :name="'items['+index+'][unit_cost]'" x-model.number="item.unit_cost" class="block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm text-right" required>
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm" x-text="lineTotal(item).toFixed(2)"></td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="border-t bg-slate-50">
                                    <td colspan="3" class="px-3 py-2 text-right text-sm font-semibold text-slate-700">{{ __('Total') }}</td>
                                    <td class="px-3 py-2 text-right text-sm font-semibold" x-text="grandTotal().toFixed(2)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Create Purchase Order') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function purchaseOrderForm(products) {
            return {
                products,
                items: [{ product_id: '', quantity: 1, unit_cost: 0 }],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_cost: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                applyDefaultCost(item) {
                    const product = this.products.find(p => p.id == item.product_id);
                    if (product) {
                        item.unit_cost = product.cost_price;
                    }
                },
                lineTotal(item) {
                    return (Number(item.quantity) || 0) * (Number(item.unit_cost) || 0);
                },
                grandTotal() {
                    return this.items.reduce((sum, item) => sum + this.lineTotal(item), 0);
                },
            };
        }
    </script>
</x-app-layout>

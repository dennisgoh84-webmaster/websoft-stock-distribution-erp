<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Sales Order') }}</h2>
    </x-slot>

    <div class="py-6" x-data="salesOrderForm({{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku, 'selling_price' => (float) $p->selling_price])->values() }})">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('sales-orders.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="customer_id" :value="__('Customer')" />
                            <select id="customer_id" name="customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Select customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('customer_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="warehouse_id" :value="__('Fulfilling Warehouse')" />
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
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
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-gray-700">{{ __('Items') }}</h3>
                            <x-secondary-button type="button" @click="addItem()">{{ __('Add Item') }}</x-secondary-button>
                        </div>

                        <x-input-error :messages="$errors->get('items')" class="mb-2" />

                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Product') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">{{ __('Quantity') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">{{ __('Unit Price') }}</th>
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-32">{{ __('Line Total') }}</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="applyDefaultPrice(item)" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                <option value="">{{ __('Select product') }}</option>
                                                <template x-for="product in products" :key="product.id">
                                                    <option :value="product.id" x-text="product.name + ' (' + product.sku + ')'"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="1" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-right" required>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="0" step="0.01" :name="'items['+index+'][unit_price]'" x-model.number="item.unit_price" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-right" required>
                                        </td>
                                        <td class="px-3 py-2 text-right text-sm" x-text="lineTotal(item).toFixed(2)"></td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="border-t bg-gray-50">
                                    <td colspan="3" class="px-3 py-2 text-right text-sm font-semibold text-gray-700">{{ __('Total') }}</td>
                                    <td class="px-3 py-2 text-right text-sm font-semibold" x-text="grandTotal().toFixed(2)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Create Sales Order') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function salesOrderForm(products) {
            return {
                products,
                items: [{ product_id: '', quantity: 1, unit_price: 0 }],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, unit_price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
                applyDefaultPrice(item) {
                    const product = this.products.find(p => p.id == item.product_id);
                    if (product) {
                        item.unit_price = product.selling_price;
                    }
                },
                lineTotal(item) {
                    return (Number(item.quantity) || 0) * (Number(item.unit_price) || 0);
                },
                grandTotal() {
                    return this.items.reduce((sum, item) => sum + this.lineTotal(item), 0);
                },
            };
        }
    </script>
</x-app-layout>

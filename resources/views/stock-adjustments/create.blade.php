<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Stock Adjustment') }}</h2>
    </x-slot>

    <div class="py-6" x-data="stockAdjustmentForm({{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku])->values() }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('stock-adjustments.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="warehouse_id" :value="__('Warehouse')" />
                            <select id="warehouse_id" name="warehouse_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Select warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('warehouse_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="reason" :value="__('Reason')" />
                            <x-text-input id="reason" name="reason" type="text" class="mt-1 block w-full" value="{{ old('reason') }}" placeholder="e.g. Damaged goods, cycle count" required />
                            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="adjustment_date" :value="__('Date')" />
                            <x-text-input id="adjustment_date" name="adjustment_date" type="date" class="mt-1 block w-full" value="{{ old('adjustment_date', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('adjustment_date')" class="mt-2" />
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
                                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase w-40">{{ __('Quantity Change') }}</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <select :name="'items['+index+'][product_id]'" x-model="item.product_id" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                                                <option value="">{{ __('Select product') }}</option>
                                                <template x-for="product in products" :key="product.id">
                                                    <option :value="product.id" x-text="product.name + ' (' + product.sku + ')'"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" :name="'items['+index+'][quantity_change]'" x-model.number="item.quantity_change" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm text-right" placeholder="+5 or -5" required>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <p class="mt-2 text-xs text-gray-500">{{ __('Use a positive number to increase stock, negative to decrease it.') }}</p>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Record Adjustment') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function stockAdjustmentForm(products) {
            return {
                products,
                items: [{ product_id: '', quantity_change: 0 }],
                addItem() {
                    this.items.push({ product_id: '', quantity_change: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    }
                },
            };
        }
    </script>
</x-app-layout>

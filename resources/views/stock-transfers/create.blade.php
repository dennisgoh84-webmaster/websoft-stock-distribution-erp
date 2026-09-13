<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('New Stock Transfer') }}</h2>
    </x-slot>

    <div class="py-6" x-data="stockTransferForm({{ $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku])->values() }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                <form method="POST" action="{{ route('stock-transfers.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="from_warehouse_id" :value="__('From Warehouse')" />
                            <select id="from_warehouse_id" name="from_warehouse_id" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm" required>
                                <option value="">{{ __('Select warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected(old('from_warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('from_warehouse_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="to_warehouse_id" :value="__('To Warehouse')" />
                            <select id="to_warehouse_id" name="to_warehouse_id" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm" required>
                                <option value="">{{ __('Select warehouse') }}</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" @selected(old('to_warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('to_warehouse_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="transfer_date" :value="__('Transfer Date')" />
                            <x-text-input id="transfer_date" name="transfer_date" type="date" class="mt-1 block w-full" value="{{ old('transfer_date', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('transfer_date')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">{{ old('notes') }}</textarea>
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
                                    <th class="px-3 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ __('Quantity') }}</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-t">
                                        <td class="px-3 py-2">
                                            <select :name="'items['+index+'][product_id]'" x-model="item.product_id" class="block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm" required>
                                                <option value="">{{ __('Select product') }}</option>
                                                <template x-for="product in products" :key="product.id">
                                                    <option :value="product.id" x-text="product.name + ' (' + product.sku + ')'"></option>
                                                </template>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="number" min="1" :name="'items['+index+'][quantity]'" x-model.number="item.quantity" class="block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm text-right" required>
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">&times;</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <p class="text-xs text-slate-500">{{ __('Transfers apply immediately: stock moves out of the source warehouse and into the destination warehouse.') }}</p>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Complete Transfer') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function stockTransferForm(products) {
            return {
                products,
                items: [{ product_id: '', quantity: 1 }],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1 });
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

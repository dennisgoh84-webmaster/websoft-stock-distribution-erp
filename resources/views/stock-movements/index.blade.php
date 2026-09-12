<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Stock Ledger') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm ring-1 ring-slate-900/5">
                <div>
                    <x-input-label for="product_id" :value="__('Product')" />
                    <select id="product_id" name="product_id" class="mt-1 block border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="warehouse_id" :value="__('Warehouse')" />
                    <select id="warehouse_id" name="warehouse_id" class="mt-1 block border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" @selected(request('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" class="mt-1 block border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($types as $value => $label)
                            <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
                @if (request()->hasAny(['product_id', 'warehouse_id', 'type']))
                    <a href="{{ route('stock-movements.index') }}" class="text-sm text-slate-500 hover:underline">{{ __('Clear') }}</a>
                @endif
            </form>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Qty') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('By') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $movement->product->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->warehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $types[$movement->type] ?? $movement->type }}</td>
                                <td class="px-6 py-4 text-sm text-right {{ $movement->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ $movement->balance_after }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->user?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock movements found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $movements->links() }}</div>
        </div>
    </div>
</x-app-layout>

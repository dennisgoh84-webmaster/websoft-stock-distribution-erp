<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ $product->name }} <span class="text-slate-400 font-normal">({{ $product->sku }})</span></h2>
            <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-amber-700 transition ease-in-out duration-150">
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-2 sm:grid-cols-5 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('Category') }}</span> {{ $product->category?->name ?? '—' }}</div>
                <div><span class="text-slate-500 block">{{ __('Unit') }}</span> {{ $product->unit?->name ?? '—' }}</div>
                <div><span class="text-slate-500 block">{{ __('Cost Price') }}</span> {{ number_format($product->cost_price, 2) }}</div>
                <div><span class="text-slate-500 block">{{ __('Selling Price') }}</span> {{ number_format($product->selling_price, 2) }}</div>
                <div>
                    <span class="text-slate-500 block">{{ __('Total Stock') }}</span>
                    <span class="{{ $product->isLowStock() ? 'text-red-600 font-semibold' : '' }}">{{ $product->totalStock() }}</span>
                    @if ($product->isLowStock())
                        <span class="text-xs text-red-500">({{ __('low, reorder at') }} {{ $product->reorder_level }})</span>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Stock by Warehouse') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($product->warehouses as $warehouse)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('warehouses.show', $warehouse) }}" class="hover:underline">{{ $warehouse->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ $warehouse->pivot->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Stock Movements') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Qty') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $movement->warehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ \App\Models\StockMovement::typeLabels()[$movement->type] ?? $movement->type }}</td>
                                <td class="px-6 py-4 text-sm text-right {{ $movement->quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity >= 0 ? '+' : '' }}{{ $movement->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ $movement->balance_after }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock movements yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">{{ $movements->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

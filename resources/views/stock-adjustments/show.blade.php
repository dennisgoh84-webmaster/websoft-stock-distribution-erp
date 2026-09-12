<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Stock Adjustment') }} {{ $stockAdjustment->adjustment_number }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('Warehouse') }}</span> {{ $stockAdjustment->warehouse->name }}</div>
                <div><span class="text-slate-500 block">{{ __('Reason') }}</span> {{ $stockAdjustment->reason }}</div>
                <div><span class="text-slate-500 block">{{ __('Date') }}</span> {{ $stockAdjustment->adjustment_date->format('Y-m-d') }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Items') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Quantity Change') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($stockAdjustment->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $item->product->name }}</td>
                                <td class="px-6 py-4 text-sm text-right {{ $item->quantity_change >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item->quantity_change >= 0 ? '+' : '' }}{{ $item->quantity_change }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($stockAdjustment->notes)
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 text-sm text-slate-600">
                    <span class="text-slate-500 block mb-1">{{ __('Notes') }}</span>
                    {{ $stockAdjustment->notes }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

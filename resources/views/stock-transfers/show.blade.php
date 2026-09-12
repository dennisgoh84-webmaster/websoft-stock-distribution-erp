<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
            {{ __('Stock Transfer') }} {{ $stockTransfer->transfer_number }}
            <x-status-badge :status="$stockTransfer->status" />
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('From') }}</span> {{ $stockTransfer->fromWarehouse->name }}</div>
                <div><span class="text-slate-500 block">{{ __('To') }}</span> {{ $stockTransfer->toWarehouse->name }}</div>
                <div><span class="text-slate-500 block">{{ __('Date') }}</span> {{ $stockTransfer->transfer_date->format('Y-m-d') }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Items') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($stockTransfer->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $item->product->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($stockTransfer->notes)
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 text-sm text-slate-600">
                    <span class="text-slate-500 block mb-1">{{ __('Notes') }}</span>
                    {{ $stockTransfer->notes }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
                {{ __('Sales Order') }} {{ $salesOrder->so_number }}
                <x-status-badge :status="$salesOrder->status" />
            </h2>
            @if ($salesOrder->canCancel())
                <form method="POST" action="{{ route('sales-orders.cancel', $salesOrder) }}" onsubmit="return confirm('Cancel this sales order?');">
                    @csrf
                    <x-danger-button>{{ __('Cancel Order') }}</x-danger-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('Customer') }}</span> <a href="{{ route('customers.show', $salesOrder->customer) }}" class="hover:underline">{{ $salesOrder->customer->name }}</a></div>
                <div><span class="text-slate-500 block">{{ __('Warehouse') }}</span> {{ $salesOrder->warehouse->name }}</div>
                <div><span class="text-slate-500 block">{{ __('Order Date') }}</span> {{ $salesOrder->order_date->format('Y-m-d') }}</div>
            </div>

            @if ($salesOrder->invoice)
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-sm flex justify-between items-center">
                    <span>{{ __('Invoice') }} <a href="{{ route('invoices.show', $salesOrder->invoice) }}" class="font-semibold hover:underline">{{ $salesOrder->invoice->invoice_number }}</a> {{ __('has been generated for this order.') }}</span>
                    <x-status-badge :status="$salesOrder->invoice->status" />
                </div>
            @endif

            <form method="POST" action="{{ route('sales-orders.fulfill', $salesOrder) }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Items') }}</div>
                    <table class="app-table min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Ordered') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Fulfilled') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Unit Price') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Line Total') }}</th>
                                @if (in_array($salesOrder->status, [\App\Models\SalesOrder::STATUS_CONFIRMED, \App\Models\SalesOrder::STATUS_PARTIALLY_FULFILLED]))
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ __('Fulfill Now') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach ($salesOrder->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-slate-900">
                                        {{ $item->product->name }}
                                        <span class="block text-xs text-slate-400">{{ __('in stock at warehouse') }}: {{ $item->product->stockIn($salesOrder->warehouse_id) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ $item->fulfilled_quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($item->line_total, 2) }}</td>
                                    @if (in_array($salesOrder->status, [\App\Models\SalesOrder::STATUS_CONFIRMED, \App\Models\SalesOrder::STATUS_PARTIALLY_FULFILLED]))
                                        <td class="px-6 py-4 text-right">
                                            @if ($item->remainingQuantity() > 0)
                                                <input type="number" min="0" max="{{ $item->remainingQuantity() }}" name="quantities[{{ $item->id }}]" value="0" class="w-24 text-right border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                                            @else
                                                <span class="text-xs text-slate-400">{{ __('complete') }}</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t bg-slate-50">
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-semibold text-slate-700">{{ __('Total') }}</td>
                                <td class="px-6 py-3 text-right text-sm font-semibold">{{ number_format($salesOrder->total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if (in_array($salesOrder->status, [\App\Models\SalesOrder::STATUS_CONFIRMED, \App\Models\SalesOrder::STATUS_PARTIALLY_FULFILLED]))
                    <div class="flex justify-end mt-4">
                        <x-primary-button>{{ __('Fulfill Order') }}</x-primary-button>
                    </div>
                @endif
            </form>

            @if ($salesOrder->notes)
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 text-sm text-slate-600">
                    <span class="text-slate-500 block mb-1">{{ __('Notes') }}</span>
                    {{ $salesOrder->notes }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
                {{ __('Purchase Order') }} {{ $purchaseOrder->po_number }}
                <x-status-badge :status="$purchaseOrder->status" />
            </h2>
            @if ($purchaseOrder->canCancel())
                <form method="POST" action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" onsubmit="return confirm('Cancel this purchase order?');">
                    @csrf
                    <x-danger-button>{{ __('Cancel Order') }}</x-danger-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('Supplier') }}</span> <a href="{{ route('suppliers.show', $purchaseOrder->supplier) }}" class="hover:underline">{{ $purchaseOrder->supplier->name }}</a></div>
                <div><span class="text-slate-500 block">{{ __('Warehouse') }}</span> {{ $purchaseOrder->warehouse->name }}</div>
                <div><span class="text-slate-500 block">{{ __('Order Date') }}</span> {{ $purchaseOrder->order_date->format('Y-m-d') }}</div>
                <div><span class="text-slate-500 block">{{ __('Expected Date') }}</span> {{ $purchaseOrder->expected_date?->format('Y-m-d') ?? '—' }}</div>
            </div>

            @if ($purchaseOrder->invoice)
                <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 text-sm flex justify-between items-center">
                    <span>{{ __('Invoice') }} <a href="{{ route('invoices.show', $purchaseOrder->invoice) }}" class="font-semibold hover:underline">{{ $purchaseOrder->invoice->invoice_number }}</a> {{ __('has been generated for this order.') }}</span>
                    <x-status-badge :status="$purchaseOrder->invoice->status" />
                </div>
            @endif

            <form method="POST" action="{{ route('purchase-orders.receive', $purchaseOrder) }}">
                @csrf
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Items') }}</div>
                    <table class="app-table min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Ordered') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Received') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Unit Cost') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Line Total') }}</th>
                                @if (in_array($purchaseOrder->status, [\App\Models\PurchaseOrder::STATUS_ORDERED, \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED]))
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ __('Receive Now') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach ($purchaseOrder->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-slate-900">{{ $item->product->name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ $item->received_quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-500 text-right">{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($item->line_total, 2) }}</td>
                                    @if (in_array($purchaseOrder->status, [\App\Models\PurchaseOrder::STATUS_ORDERED, \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED]))
                                        <td class="px-6 py-4 text-right">
                                            @if ($item->remainingQuantity() > 0)
                                                <input type="number" min="0" max="{{ $item->remainingQuantity() }}" name="quantities[{{ $item->id }}]" value="0" class="w-24 text-right border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm">
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
                                <td class="px-6 py-3 text-right text-sm font-semibold">{{ number_format($purchaseOrder->total, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if (in_array($purchaseOrder->status, [\App\Models\PurchaseOrder::STATUS_ORDERED, \App\Models\PurchaseOrder::STATUS_PARTIALLY_RECEIVED]))
                    <div class="flex justify-end mt-4">
                        <x-primary-button>{{ __('Receive Stock') }}</x-primary-button>
                    </div>
                @endif
            </form>

            @if ($purchaseOrder->notes)
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 text-sm text-slate-600">
                    <span class="text-slate-500 block mb-1">{{ __('Notes') }}</span>
                    {{ $purchaseOrder->notes }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

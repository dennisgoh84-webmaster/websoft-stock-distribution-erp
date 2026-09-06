<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('Products') }}</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ $productCount }}</div>
                    <div class="text-xs text-gray-400">{{ __('across') }} {{ $warehouseCount }} {{ __('warehouses') }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('Pending Purchase Orders') }}</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ $pendingPurchaseOrders }}</div>
                    <a href="{{ route('purchase-orders.index') }}" class="text-xs text-indigo-600 hover:underline">{{ __('View all') }}</a>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('Pending Sales Orders') }}</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ $pendingSalesOrders }}</div>
                    <a href="{{ route('sales-orders.index') }}" class="text-xs text-indigo-600 hover:underline">{{ __('View all') }}</a>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">{{ __('Revenue This Month') }}</div>
                    <div class="text-2xl font-semibold text-gray-800">{{ number_format($revenueThisMonth, 2) }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700 flex justify-between items-center">
                        {{ __('Low Stock Products') }}
                        <a href="{{ route('products.index') }}" class="text-xs text-indigo-600 hover:underline font-normal">{{ __('View all products') }}</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Product') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Stock') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Reorder Level') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-red-600 font-semibold text-right">{{ $product->totalStock() }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500 text-right">{{ $product->reorder_level }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-sm text-gray-500 text-center">{{ __('No products are low on stock.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700 flex justify-between items-center">
                        {{ __('Outstanding Invoices') }}
                        <a href="{{ route('invoices.index') }}" class="text-xs text-indigo-600 hover:underline font-normal">{{ __('View all invoices') }}</a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Invoice') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Party') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($outstandingInvoices->take(10) as $invoice)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $invoice->party()?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ number_format($invoice->balance(), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-sm text-gray-500 text-center">{{ __('No outstanding invoices.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($outstandingInvoices->isNotEmpty())
                            <tfoot>
                                <tr class="border-t bg-gray-50">
                                    <td colspan="2" class="px-6 py-2 text-right text-sm font-semibold text-gray-700">{{ __('Total Outstanding') }}</td>
                                    <td class="px-6 py-2 text-right text-sm font-semibold">{{ number_format($outstandingBalance, 2) }}</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

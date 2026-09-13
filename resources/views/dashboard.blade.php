<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 text-amber-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-sm text-slate-500">{{ __('Products') }}</div>
                            <div class="text-2xl font-semibold text-slate-900">{{ $productCount }}</div>
                            <div class="text-xs text-slate-400">{{ __('across') }} {{ $warehouseCount }} {{ __('warehouses') }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-amber-50 text-amber-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-sm text-slate-500">{{ __('Pending Purchase Orders') }}</div>
                            <div class="text-2xl font-semibold text-slate-900">{{ $pendingPurchaseOrders }}</div>
                            <a href="{{ route('purchase-orders.index') }}" class="text-xs text-amber-700 hover:underline">{{ __('View all') }}</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50 text-blue-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.836l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.87-4.788 2.202-7.391a1.125 1.125 0 00-1.11-1.226H5.25m0 0L4.72 3.836M7.5 14.25L4.72 3.836m0 0L4.5 3" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-sm text-slate-500">{{ __('Pending Sales Orders') }}</div>
                            <div class="text-2xl font-semibold text-slate-900">{{ $pendingSalesOrders }}</div>
                            <a href="{{ route('sales-orders.index') }}" class="text-xs text-amber-700 hover:underline">{{ __('View all') }}</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <span class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50 text-green-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-sm text-slate-500">{{ __('Revenue This Month') }}</div>
                            <div class="text-2xl font-semibold text-slate-900">{{ number_format($revenueThisMonth, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900 flex justify-between items-center">
                        {{ __('Low Stock Products') }}
                        <a href="{{ route('products.index') }}" class="text-xs text-amber-700 hover:underline font-normal">{{ __('View all products') }}</a>
                    </div>
                    <table class="app-table min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Reorder Level') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($lowStockProducts as $product)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-slate-900">
                                        <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right"><x-stock-level :product="$product" /></td>
                                    <td class="px-6 py-3 text-sm text-slate-500 text-right">{{ $product->reorder_level }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-sm text-slate-500 text-center">{{ __('No products are low on stock.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                    <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-900 flex justify-between items-center">
                        {{ __('Outstanding Invoices') }}
                        <a href="{{ route('invoices.index') }}" class="text-xs text-amber-700 hover:underline font-normal">{{ __('View all invoices') }}</a>
                    </div>
                    <table class="app-table min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Invoice') }}</th>
                                <th class="px-6 py-2 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Party') }}</th>
                                <th class="px-6 py-2 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @forelse ($outstandingInvoices->take(10) as $invoice)
                                <tr>
                                    <td class="px-6 py-3 text-sm text-slate-900">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-slate-500">{{ $invoice->party()?->name ?? '—' }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-900 text-right">{{ number_format($invoice->balance(), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-3 text-sm text-slate-500 text-center">{{ __('No outstanding invoices.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($outstandingInvoices->isNotEmpty())
                            <tfoot>
                                <tr class="border-t bg-slate-50">
                                    <td colspan="2" class="px-6 py-2 text-right text-sm font-semibold text-slate-700">{{ __('Total Outstanding') }}</td>
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

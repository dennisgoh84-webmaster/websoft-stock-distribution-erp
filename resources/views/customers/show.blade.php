<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ $customer->name }} <span class="text-slate-400 font-normal">({{ $customer->code }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div><span class="text-slate-500">{{ __('Contact') }}:</span> {{ $customer->contact_person ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Email') }}:</span> {{ $customer->email ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Phone') }}:</span> {{ $customer->phone ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Credit Limit') }}:</span> {{ number_format($customer->credit_limit, 2) }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Sales Orders') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('SO #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($salesOrders as $so)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('sales-orders.show', $so) }}" class="hover:underline">{{ $so->so_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $so->order_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$so->status" /></td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($so->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No sales orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">{{ $salesOrders->links() }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Invoices') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Invoice #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$invoice->status" /></td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($invoice->balance(), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No invoices yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

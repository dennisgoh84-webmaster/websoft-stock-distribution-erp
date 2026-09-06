<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $customer->name }} <span class="text-gray-400 font-normal">({{ $customer->code }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div><span class="text-gray-500">{{ __('Contact') }}:</span> {{ $customer->contact_person ?: '—' }}</div>
                <div><span class="text-gray-500">{{ __('Email') }}:</span> {{ $customer->email ?: '—' }}</div>
                <div><span class="text-gray-500">{{ __('Phone') }}:</span> {{ $customer->phone ?: '—' }}</div>
                <div><span class="text-gray-500">{{ __('Credit Limit') }}:</span> {{ number_format($customer->credit_limit, 2) }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700">{{ __('Sales Orders') }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SO #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($salesOrders as $so)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ route('sales-orders.show', $so) }}" class="hover:underline">{{ $so->so_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $so->order_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$so->status" /></td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($so->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No sales orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">{{ $salesOrders->links() }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700">{{ __('Invoices') }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Invoice #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$invoice->status" /></td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($invoice->balance(), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No invoices yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-3">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice') }} {{ $invoice->invoice_number }}
            <x-status-badge :status="$invoice->status" />
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div><span class="text-gray-500 block">{{ __('Type') }}</span> {{ str($invoice->type)->title() }}</div>
                <div>
                    <span class="text-gray-500 block">{{ __('Party') }}</span>
                    @if ($invoice->type === 'sales' && $invoice->customer)
                        <a href="{{ route('customers.show', $invoice->customer) }}" class="hover:underline">{{ $invoice->customer->name }}</a>
                    @elseif ($invoice->supplier)
                        <a href="{{ route('suppliers.show', $invoice->supplier) }}" class="hover:underline">{{ $invoice->supplier->name }}</a>
                    @else
                        —
                    @endif
                </div>
                <div><span class="text-gray-500 block">{{ __('Invoice Date') }}</span> {{ $invoice->invoice_date->format('Y-m-d') }}</div>
                <div><span class="text-gray-500 block">{{ __('Due Date') }}</span> {{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</div>
            </div>

            @if ($invoice->source)
                <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-sm">
                    {{ __('Generated from') }}
                    @if ($invoice->type === 'sales')
                        <a href="{{ route('sales-orders.show', $invoice->source) }}" class="font-semibold hover:underline">{{ $invoice->source->so_number }}</a>
                    @else
                        <a href="{{ route('purchase-orders.show', $invoice->source) }}" class="font-semibold hover:underline">{{ $invoice->source->po_number }}</a>
                    @endif
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700">{{ __('Items') }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Description') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Qty') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Unit Price') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Line Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $item->description ?? $item->product?->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t bg-gray-50">
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-gray-500">{{ __('Subtotal') }}</td>
                            <td class="px-6 py-2 text-right text-sm">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-gray-500">{{ __('Tax') }}</td>
                            <td class="px-6 py-2 text-right text-sm">{{ number_format($invoice->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-2 text-right text-sm font-semibold text-gray-700">{{ __('Total') }}</td>
                            <td class="px-6 py-2 text-right text-sm font-semibold">{{ number_format($invoice->total, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-2 text-right text-sm text-gray-500">{{ __('Paid') }}</td>
                            <td class="px-6 py-2 text-right text-sm">{{ number_format($invoice->amount_paid, 2) }}</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td colspan="3" class="px-6 py-2 text-right text-sm font-semibold text-gray-700">{{ __('Balance Due') }}</td>
                            <td class="px-6 py-2 text-right text-sm font-semibold">{{ number_format($invoice->balance(), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700">{{ __('Payments') }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Payment #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Method') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoice->payments as $payment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_number }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ str($payment->method)->replace('_', ' ')->title() }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No payments recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (in_array($invoice->status, [\App\Models\Invoice::STATUS_UNPAID, \App\Models\Invoice::STATUS_PARTIALLY_PAID]))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">{{ __('Record Payment') }}</h3>
                    <form method="POST" action="{{ route('invoices.pay', $invoice) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div>
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $invoice->balance() }}" class="mt-1 block w-full" value="{{ old('amount', $invoice->balance()) }}" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="payment_date" :value="__('Date')" />
                            <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" value="{{ old('payment_date', now()->toDateString()) }}" required />
                        </div>
                        <div>
                            <x-input-label for="method" :value="__('Method')" />
                            <select id="method" name="method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="cash">{{ __('Cash') }}</option>
                                <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                <option value="cheque">{{ __('Cheque') }}</option>
                                <option value="other">{{ __('Other') }}</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="reference_no" :value="__('Reference No.')" />
                            <x-text-input id="reference_no" name="reference_no" type="text" class="mt-1 block w-full" value="{{ old('reference_no') }}" />
                        </div>
                        <div class="sm:col-span-4">
                            <x-primary-button>{{ __('Record Payment') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

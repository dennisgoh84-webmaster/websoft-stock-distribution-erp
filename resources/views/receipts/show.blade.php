<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">
            {{ __('Receipt') }} {{ $receipt->receipt_number }}
            <x-status-badge :status="$receipt->allocationStatus()" />
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-4">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg p-4">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                <div>
                    <span class="text-slate-500 block">{{ __('Customer') }}</span>
                    <a href="{{ route('customers.show', $receipt->customer) }}" class="hover:underline">{{ $receipt->customer->name }}</a>
                </div>
                <div><span class="text-slate-500 block">{{ __('Receipt Date') }}</span> {{ $receipt->receipt_date->format('Y-m-d') }}</div>
                <div><span class="text-slate-500 block">{{ __('Method') }}</span> {{ str($receipt->method)->replace('_', ' ')->title() }}</div>
                <div><span class="text-slate-500 block">{{ __('Reference No.') }}</span> {{ $receipt->reference_no ?? '—' }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500 block">{{ __('Amount Received') }}</span> <span class="text-lg font-semibold">{{ number_format($receipt->amount, 2) }}</span></div>
                <div><span class="text-slate-500 block">{{ __('Allocated') }}</span> <span class="text-lg">{{ number_format($receipt->allocatedAmount(), 2) }}</span></div>
                <div><span class="text-slate-500 block">{{ __('Unallocated (on account)') }}</span> <span class="text-lg font-semibold">{{ number_format($receipt->unallocatedAmount(), 2) }}</span></div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Applied To') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Invoice') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($receipt->allocations as $allocation)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('invoices.show', $allocation->invoice) }}" class="hover:underline">{{ $allocation->invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $allocation->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($allocation->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('Not yet allocated to any invoice.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($receipt->unallocatedAmount() > 0.001)
                <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6">
                    <h3 class="font-semibold text-slate-700 mb-4">{{ __('Allocate to an Invoice') }}</h3>

                    @if ($openInvoices->isEmpty())
                        <p class="text-sm text-slate-500">{{ __('This customer has no outstanding sales invoices to allocate against.') }}</p>
                    @else
                        <form method="POST" action="{{ route('receipts.allocate', $receipt) }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                            @csrf
                            <div>
                                <x-input-label for="invoice_id" :value="__('Invoice')" />
                                <select id="invoice_id" name="invoice_id" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" required>
                                    <option value="">{{ __('Select invoice') }}</option>
                                    @foreach ($openInvoices as $invoice)
                                        <option value="{{ $invoice->id }}" @selected(old('invoice_id') == $invoice->id)>
                                            {{ $invoice->invoice_number }} ({{ number_format($invoice->balance(), 2) }} {{ __('due') }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('invoice_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="amount" :value="__('Amount')" />
                                <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" max="{{ $receipt->unallocatedAmount() }}" class="mt-1 block w-full" value="{{ old('amount') }}" required />
                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            </div>
                            <div>
                                <x-primary-button>{{ __('Allocate') }}</x-primary-button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Invoices') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm ring-1 ring-slate-900/5">
                <div>
                    <x-input-label for="type" :value="__('Type')" />
                    <select id="type" name="type" class="mt-1 block border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        <option value="sales" @selected(request('type') === 'sales')>{{ __('Sales') }}</option>
                        <option value="purchase" @selected(request('type') === 'purchase')>{{ __('Purchase') }}</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach (['unpaid', 'partially_paid', 'paid', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->replace('_', ' ')->title() }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Invoice #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Party') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Total') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Balance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="hover:underline">{{ $invoice->invoice_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ str($invoice->type)->title() }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->party()?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$invoice->status" /></td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($invoice->total, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($invoice->balance(), 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No invoices yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $invoices->links() }}</div>
        </div>
    </div>
</x-app-layout>

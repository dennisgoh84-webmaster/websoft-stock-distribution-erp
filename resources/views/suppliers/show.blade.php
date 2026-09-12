<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ $supplier->name }} <span class="text-slate-400 font-normal">({{ $supplier->code }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500">{{ __('Contact') }}:</span> {{ $supplier->contact_person ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Email') }}:</span> {{ $supplier->email ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Phone') }}:</span> {{ $supplier->phone ?: '—' }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Purchase Orders') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('PO #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($purchaseOrders as $po)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('purchase-orders.show', $po) }}" class="hover:underline">{{ $po->po_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $po->order_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$po->status" /></td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($po->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No purchase orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $purchaseOrders->links() }}</div>
        </div>
    </div>
</x-app-layout>

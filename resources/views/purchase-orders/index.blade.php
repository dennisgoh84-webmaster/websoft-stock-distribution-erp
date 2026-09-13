<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Purchase Orders') }}</h2>
            <a href="{{ route('purchase-orders.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-amber-700 transition ease-in-out duration-150">
                {{ __('New Purchase Order') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm ring-1 ring-slate-900/5">
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 block border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach (['ordered', 'partially_received', 'received', 'cancelled'] as $status)
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('PO #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Warehouse') }}</th>
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
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $po->supplier->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $po->warehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $po->order_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$po->status" /></td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($po->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No purchase orders yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $purchaseOrders->links() }}</div>
        </div>
    </div>
</x-app-layout>

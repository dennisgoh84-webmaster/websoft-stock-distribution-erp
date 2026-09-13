<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Stock Transfers') }}</h2>
            <a href="{{ route('stock-transfers.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-amber-700 transition ease-in-out duration-150">
                {{ __('New Transfer') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Transfer #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('From') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('To') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($stockTransfers as $transfer)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('stock-transfers.show', $transfer) }}" class="hover:underline">{{ $transfer->transfer_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $transfer->fromWarehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $transfer->toWarehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $transfer->transfer_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$transfer->status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock transfers yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $stockTransfers->links() }}</div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Stock Adjustments') }}</h2>
            <a href="{{ route('stock-adjustments.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-amber-700 transition ease-in-out duration-150">
                {{ __('New Adjustment') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Adjustment #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Warehouse') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Reason') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($stockAdjustments as $adjustment)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('stock-adjustments.show', $adjustment) }}" class="hover:underline">{{ $adjustment->adjustment_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $adjustment->warehouse->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $adjustment->reason }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $adjustment->adjustment_date->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock adjustments yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $stockAdjustments->links() }}</div>
        </div>
    </div>
</x-app-layout>

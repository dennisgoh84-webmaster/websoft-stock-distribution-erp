<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Receipts') }}</h2>
            <x-primary-button onclick="window.location='{{ route('receipts.create') }}'">{{ __('Record Receipt') }}</x-primary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm">
                <div>
                    <x-input-label for="customer_id" :value="__('Customer')" />
                    <select id="customer_id" name="customer_id" class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Receipt #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Customer') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Unallocated') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($receipts as $receipt)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ route('receipts.show', $receipt) }}" class="hover:underline">{{ $receipt->receipt_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $receipt->customer->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $receipt->receipt_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($receipt->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($receipt->unallocatedAmount(), 2) }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$receipt->allocationStatus()" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No receipts recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $receipts->links() }}</div>
        </div>
    </div>
</x-app-layout>

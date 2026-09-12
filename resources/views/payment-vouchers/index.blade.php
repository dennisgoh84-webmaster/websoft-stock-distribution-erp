<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Payment Vouchers') }}</h2>
            <x-primary-button onclick="window.location='{{ route('payment-vouchers.create') }}'">{{ __('Record Payment Voucher') }}</x-primary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-lg p-4">{{ session('status') }}</div>
            @endif

            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm">
                <div>
                    <x-input-label for="supplier_id" :value="__('Supplier')" />
                    <select id="supplier_id" name="supplier_id" class="mt-1 block border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Voucher #') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Supplier') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Amount') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Unallocated') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($vouchers as $voucher)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ route('payment-vouchers.show', $voucher) }}" class="hover:underline">{{ $voucher->voucher_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $voucher->supplier->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $voucher->payment_date->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($voucher->amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($voucher->unallocatedAmount(), 2) }}</td>
                                <td class="px-6 py-4 text-sm"><x-status-badge :status="$voucher->allocationStatus()" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No payment vouchers recorded yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $vouchers->links() }}</div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $warehouse->name }} <span class="text-gray-400 font-normal">({{ $warehouse->code }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-gray-500">{{ __('Address') }}:</span> {{ $warehouse->address ?: '—' }}</div>
                <div><span class="text-gray-500">{{ __('Phone') }}:</span> {{ $warehouse->phone ?: '—' }}</div>
                <div><span class="text-gray-500">{{ __('Status') }}:</span> {{ $warehouse->is_active ? __('Active') : __('Inactive') }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 font-semibold text-gray-700">{{ __('Stock on Hand') }}</div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('SKU') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($stock as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ $product->pivot->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-gray-500 text-center">{{ __('No stock recorded here yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $stock->links() }}</div>
        </div>
    </div>
</x-app-layout>

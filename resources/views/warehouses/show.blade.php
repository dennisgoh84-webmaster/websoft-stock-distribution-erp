<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ $warehouse->name }} <span class="text-slate-400 font-normal">({{ $warehouse->code }})</span></h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl p-6 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><span class="text-slate-500">{{ __('Address') }}:</span> {{ $warehouse->address ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Phone') }}:</span> {{ $warehouse->phone ?: '—' }}</div>
                <div><span class="text-slate-500">{{ __('Status') }}:</span> {{ $warehouse->is_active ? __('Active') : __('Inactive') }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <div class="px-6 py-4 border-b border-slate-100 font-semibold text-slate-700">{{ __('Stock on Hand') }}</div>
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('SKU') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($stock as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ $product->pivot->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No stock recorded here yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $stock->links() }}</div>
        </div>
    </div>
</x-app-layout>

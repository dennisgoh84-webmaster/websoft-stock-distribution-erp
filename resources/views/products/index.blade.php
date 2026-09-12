<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Products') }}</h2>
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-indigo-500 transition ease-in-out duration-150">
                {{ __('New Product') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap gap-3 items-end bg-white p-4 rounded-lg shadow-sm ring-1 ring-slate-900/5">
                <div>
                    <x-input-label for="search" :value="__('Search')" />
                    <x-text-input id="search" name="search" type="text" class="mt-1" value="{{ request('search') }}" placeholder="Name or SKU" />
                </div>
                <div>
                    <x-input-label for="category_id" :value="__('Category')" />
                    <select id="category_id" name="category_id" class="mt-1 block border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
                @if (request()->hasAny(['search', 'category_id']))
                    <a href="{{ route('products.index') }}" class="text-sm text-slate-500 hover:underline">{{ __('Clear') }}</a>
                @endif
            </form>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('SKU') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Category') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Selling Price') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Stock') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $product->sku }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900">
                                    <a href="{{ route('products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $product->category?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-900 text-right">{{ number_format($product->selling_price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-right">
                                    <span class="{{ $product->isLowStock() ? 'text-red-600 font-semibold' : 'text-slate-900' }}">
                                        {{ $product->totalStock() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No products found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $products->links() }}</div>
        </div>
    </div>
</x-app-layout>

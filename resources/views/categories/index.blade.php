<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Categories') }}</h2>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-amber-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-amber-700 transition ease-in-out duration-150">
                {{ __('New Category') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Products') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $category->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $category->products_count }}</td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a href="{{ route('categories.edit', $category) }}" class="text-amber-700 hover:text-amber-900">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No categories yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

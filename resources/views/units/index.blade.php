<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Units') }}</h2>
            <a href="{{ route('units.create') }}" class="inline-flex items-center gap-x-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white shadow-sm hover:bg-indigo-500 transition ease-in-out duration-150">
                {{ __('New Unit') }}
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Short Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Products') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($units as $unit)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $unit->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $unit->short_name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $unit->products_count }}</td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a href="{{ route('units.edit', $unit) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                    <form method="POST" action="{{ route('units.destroy', $unit) }}" class="inline" onsubmit="return confirm('Delete this unit?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-sm text-slate-500 text-center">{{ __('No units yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $units->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

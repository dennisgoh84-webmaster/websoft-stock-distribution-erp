<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-900 leading-tight tracking-tight">{{ __('Users') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-slate-900/5 sm:rounded-xl">
                <table class="app-table min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Role') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm text-slate-900">{{ $user->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $user->email }}</td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $user->roles->pluck('name')->join(', ') ?: '—' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('users.update-role', $user) }}" class="flex justify-end gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}" @selected($user->hasRole($role->name))>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-secondary-button type="submit">{{ __('Update') }}</x-secondary-button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-app-layout>

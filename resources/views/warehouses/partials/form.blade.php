@php $warehouse = $warehouse ?? null; @endphp

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $warehouse->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="code" :value="__('Code')" />
    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code', $warehouse->code ?? '') }}" required />
    <x-input-error :messages="$errors->get('code')" class="mt-2" />
</div>

<div>
    <x-input-label for="address" :value="__('Address')" />
    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" value="{{ old('address', $warehouse->address ?? '') }}" />
    <x-input-error :messages="$errors->get('address')" class="mt-2" />
</div>

<div>
    <x-input-label for="phone" :value="__('Phone')" />
    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone', $warehouse->phone ?? '') }}" />
    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
</div>

<div class="flex items-center">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        {{ old('is_active', $warehouse->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="ms-2 text-sm text-slate-600">{{ __('Active') }}</label>
</div>

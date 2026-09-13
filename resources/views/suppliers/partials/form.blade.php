@php $supplier = $supplier ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $supplier->name ?? '') }}" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" :value="__('Code')" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" value="{{ old('code', $supplier->code ?? '') }}" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="contact_person" :value="__('Contact Person')" />
        <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" />
        <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $supplier->email ?? '') }}" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="phone" :value="__('Phone')" />
        <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" value="{{ old('phone', $supplier->phone ?? '') }}" />
        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" value="{{ old('address', $supplier->address ?? '') }}" />
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>
</div>

<div class="flex items-center">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-amber-700 shadow-sm focus:ring-amber-500"
        {{ old('is_active', $supplier->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="ms-2 text-sm text-slate-600">{{ __('Active') }}</label>
</div>

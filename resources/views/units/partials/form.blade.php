@php $unit = $unit ?? null; @endphp

<div>
    <x-input-label for="name" :value="__('Name')" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $unit->name ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="short_name" :value="__('Short Name (e.g. pcs, kg)')" />
    <x-text-input id="short_name" name="short_name" type="text" class="mt-1 block w-full" value="{{ old('short_name', $unit->short_name ?? '') }}" required />
    <x-input-error :messages="$errors->get('short_name')" class="mt-2" />
</div>

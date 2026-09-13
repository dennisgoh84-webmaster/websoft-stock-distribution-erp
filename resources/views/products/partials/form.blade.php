@php $product = $product ?? null; @endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="sku" :value="__('SKU')" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full" value="{{ old('sku', $product->sku ?? '') }}" required autofocus />
        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $product->name ?? '') }}" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="category_id" :value="__('Category')" />
        <select id="category_id" name="category_id" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
            <option value="">{{ __('None') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="unit_id" :value="__('Unit')" />
        <select id="unit_id" name="unit_id" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">
            <option value="">{{ __('None') }}</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected(old('unit_id', $product->unit_id ?? '') == $unit->id)>{{ $unit->name }} ({{ $unit->short_name }})</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cost_price" :value="__('Cost Price')" />
        <x-text-input id="cost_price" name="cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('cost_price', $product->cost_price ?? 0) }}" required />
        <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="selling_price" :value="__('Selling Price')" />
        <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ old('selling_price', $product->selling_price ?? 0) }}" required />
        <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="reorder_level" :value="__('Reorder Level')" />
        <x-text-input id="reorder_level" name="reorder_level" type="number" min="0" class="mt-1 block w-full" value="{{ old('reorder_level', $product->reorder_level ?? 0) }}" required />
        <x-input-error :messages="$errors->get('reorder_level')" class="mt-2" />
        <p class="mt-1 text-xs text-slate-500">{{ __('Product is flagged as low stock at or below this quantity.') }}</p>
    </div>
</div>

<div>
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm">{{ old('description', $product->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="flex items-center">
    <input id="is_active" name="is_active" type="checkbox" value="1" class="rounded border-slate-300 text-amber-700 shadow-sm focus:ring-amber-500"
        {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="ms-2 text-sm text-slate-600">{{ __('Active') }}</label>
</div>

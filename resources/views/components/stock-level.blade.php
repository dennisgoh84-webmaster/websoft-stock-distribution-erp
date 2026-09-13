@props(['product'])

@if ($product->isLowStock())
    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
        {{ $product->totalStock() }}
    </span>
@else
    <span class="text-slate-900">{{ $product->totalStock() }}</span>
@endif

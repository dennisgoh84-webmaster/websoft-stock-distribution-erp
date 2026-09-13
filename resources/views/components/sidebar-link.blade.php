@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-amber-600 text-white'
    : 'flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition ease-in-out duration-150';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

@props(['status'])

@php
$colors = [
    'draft' => 'bg-gray-100 text-gray-600',
    'ordered' => 'bg-blue-100 text-blue-800',
    'confirmed' => 'bg-blue-100 text-blue-800',
    'pending' => 'bg-yellow-100 text-yellow-800',
    'partially_received' => 'bg-yellow-100 text-yellow-800',
    'partially_fulfilled' => 'bg-yellow-100 text-yellow-800',
    'partially_paid' => 'bg-yellow-100 text-yellow-800',
    'received' => 'bg-green-100 text-green-800',
    'fulfilled' => 'bg-green-100 text-green-800',
    'completed' => 'bg-green-100 text-green-800',
    'paid' => 'bg-green-100 text-green-800',
    'unpaid' => 'bg-red-100 text-red-800',
    'cancelled' => 'bg-gray-100 text-gray-500 line-through',
];

$classes = $colors[$status] ?? 'bg-gray-100 text-gray-600';
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 text-xs font-medium rounded-full whitespace-nowrap {$classes}"]) }}>
    {{ str($status)->replace('_', ' ')->title() }}
</span>

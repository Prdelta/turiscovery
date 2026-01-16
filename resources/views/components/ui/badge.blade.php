@props(['variant' => 'primary', 'class' => ''])

@php
    $variants = [
        'primary' => 'bg-blue-100 text-blue-700',
        'secondary' => 'bg-slate-100 text-slate-700',
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-orange-100 text-orange-800',
        'danger' => 'bg-red-100 text-red-700',
        'purple' => 'bg-purple-100 text-purple-700',
        'glass' => 'bg-white/90 backdrop-blur text-slate-800',
    ];

    $style = $variants[$variant] ?? $variants['primary'];
@endphp

<span
    {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold $style $class"]) }}>
    {{ $slot }}
</span>

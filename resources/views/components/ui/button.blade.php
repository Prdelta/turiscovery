@props(['variant' => 'primary', 'type' => 'button', 'href' => null, 'icon' => null, 'class' => ''])

@php
    $baseStyles =
        'inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $variants = [
        'primary' => 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500 shadow-sm shadow-blue-600/20',
        'secondary' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 focus:ring-slate-500',
        'outline' => 'bg-transparent border border-slate-300 text-slate-700 hover:bg-slate-50 focus:ring-slate-500',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500 shadow-sm shadow-red-600/20',
        'warning' =>
            'bg-orange-500 text-white hover:bg-orange-600 focus:ring-orange-500 shadow-sm shadow-orange-500/20',
        'ghost' => 'bg-transparent text-slate-600 hover:text-slate-900 hover:bg-slate-100',
    ];

    $style = $variants[$variant] ?? $variants['primary'];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => "$baseStyles $style $class"]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 mr-2"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => "$baseStyles $style $class"]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4 mr-2"></i>
        @endif
        {{ $slot }}
    </button>
@endif

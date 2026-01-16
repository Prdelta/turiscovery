@props(['class' => '', 'padding' => 'p-6'])

<div
    {{ $attributes->merge(['class' => "bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 $padding $class"]) }}>
    {{ $slot }}
</div>

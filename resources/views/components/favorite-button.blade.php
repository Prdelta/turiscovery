@props(['active' => false, 'class' => ''])

<button
    {{ $attributes->merge(['class' => 'rounded-full flex items-center justify-center transition-transform hover:scale-110 shadow-sm focus:outline-none ' . ($active ? 'text-red-500 bg-red-50 hover:bg-red-100' : 'text-slate-400 bg-white/90 hover:text-red-500 hover:bg-white') . " $class"]) }}>
    <i data-lucide="heart" class="w-5 h-5 {{ $active ? 'fill-current' : '' }}"></i>
</button>

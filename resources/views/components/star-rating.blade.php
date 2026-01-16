@props(['rating' => 0, 'size' => 4, 'class' => ''])

<div {{ $attributes->merge(['class' => "flex items-center $class"]) }}>
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= floor($rating))
            {{-- Estrella llena --}}
            <i data-lucide="star" class="w-{{ $size }} h-{{ $size }} fill-yellow-400 text-yellow-400"></i>
        @elseif ($i == ceil($rating) && $rating - floor($rating) >= 0.5)
            {{-- Media estrella (Aprox visualmente o llena si preferimos simplificar. Lucide no tiene half-star nativo simple sin custom svg manipulation usually, usaremos llena si >0.5 para simplificar visualmente o gris si no) --}}
            {{-- Para simplificar con Lucide, usaremos logica standard: si es > x.5 cuenta como llena o podemos usar un color mas claro --}}
            <i data-lucide="star" class="w-{{ $size }} h-{{ $size }} fill-yellow-400 text-yellow-400"></i>
        @else
            {{-- Estrella vacía --}}
            <i data-lucide="star" class="w-{{ $size }} h-{{ $size }} text-slate-300 fill-slate-100"></i>
        @endif
    @endfor
</div>

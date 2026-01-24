@props(['title', 'subtitle', 'badge' => null, 'image'])

<section class="hero text-center hero-overlay bg-cover bg-center" style="background-image: url('{{ $image }}');">
    <div class="container mx-auto px-4">
        @if($badge)
            <span class="badge {{ $badge['class'] ?? 'badge-primary' }} mb-2 hidden md:inline-flex">{{ $badge['text'] }}</span>
        @endif
        <h1 class="fade-in text-white mb-4 text-3xl md:text-4xl font-bold text-shadow">{{ $title }}</h1>
        <p class="fade-in text-gray-200 mb-8 text-lg md:text-xl max-w-2xl mx-auto">
            {{ $subtitle }}
        </p>
        @isset($slot)
            {{ $slot }}
        @endisset
    </div>
</section>

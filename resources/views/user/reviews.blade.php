@extends('layouts.dashboard')

@section('title', 'Mis Reseñas')
@section('page-title', 'Mis Reseñas')
@section('page-subtitle', 'Historial de tus opiniones y valoraciones')

@section('content')
    <div class="fade-in max-w-4xl">
        @if ($reviews->count() > 0)
            <div class="space-y-6">
                @foreach ($reviews as $review)
                    <div class="card p-6 bg-white border border-gray-200">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 rounded-lg bg-gray-200 bg-cover bg-center"
                                    style="background-image: url('{{ $review->reviewable->image_url ?? 'https://via.placeholder.com/150' }}')">
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-800 text-lg">
                                        {{ $review->reviewable->name ?? ($review->reviewable->title ?? 'Sin título') }}
                                    </h3>
                                    <p class="text-sm text-slate-500 mb-1">
                                        {{ class_basename($review->reviewable_type) }} •
                                        {{ $review->created_at->diffForHumans() }}
                                    </p>
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i data-lucide="star"
                                                class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : 'fill-current text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="relative">
                                <button class="text-slate-400 hover:text-slate-600">
                                    <i data-lucide="more-vertical" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-lg">
                            @if ($review->title)
                                <h4 class="font-semibold text-slate-800 text-sm mb-1">{{ $review->title }}</h4>
                            @endif
                            <p class="text-slate-600 text-sm leading-relaxed">
                                {{ $review->comment }}
                            </p>
                        </div>

                        <div class="mt-4 flex justify-end gap-3">
                            <button class="text-sm text-slate-500 hover:text-blue-600 font-medium">Editar</button>
                            <button class="text-sm text-slate-500 hover:text-red-600 font-medium">Eliminar</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $reviews->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="message-square" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-2">No has escrito reseñas todavía</h3>
                <p class="text-slate-500 mb-6">Comparte tu experiencia con la comunidad de Turiscovery.</p>
                <a href="/" class="btn btn-primary">Explorar Destinos</a>
            </div>
        @endif
    </div>
@endsection

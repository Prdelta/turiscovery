@extends('layouts.dashboard')

@section('title', 'Mis Experiencias')
@section('page-title', 'Mis Experiencias')
@section('page-subtitle', 'Gestiona los tours y actividades turísticas.')

@section('content')
    <div class="mb-6 flex justify-between items-center fade-in">
        <div class="flex gap-4">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                <input type="text" placeholder="Buscar experiencias..."
                    class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all w-64">
            </div>
            <select
                class="px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                <option value="">Todas las categorías</option>
                <option value="aventura">Aventura</option>
                <option value="cultural">Cultural</option>
                <option value="gastronomia">Gastronómico</option>
            </select>
        </div>

        <a href="{{ route('experiencias.create') }}" class="btn btn-primary gap-2">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Nueva Experiencia
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-100 flex items-center gap-3 fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 fade-in">
        @forelse($experiencias as $experiencia)
            <article class="card group hover:shadow-lg transition-all duration-300 border border-gray-100">
                <div class="relative h-48 overflow-hidden rounded-t-xl bg-gray-100">
                    <img src="{{ $experiencia->image_url ?? 'https://via.placeholder.com/400x300?text=Experiencia' }}"
                        alt="{{ $experiencia->title }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3">
                        <span class="badge badge-success shadow-sm backdrop-blur-sm bg-white/90">
                            S/ {{ number_format($experiencia->price, 2) }}
                        </span>
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold text-lg text-slate-800 line-clamp-1 mb-1">{{ $experiencia->title }}
                            </h3>
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                {{ $experiencia->location }}
                            </div>
                        </div>
                    </div>

                    <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $experiencia->description }}</p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            {{ $experiencia->duration }}
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('experiencias.edit', $experiencia->id) }}"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </a>
                            <form action="{{ route('experiencias.destroy', $experiencia->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirm('¿Estás seguro de eliminar esta experiencia?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div
                class="col-span-full py-12 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                <i data-lucide="compass" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                <p class="text-lg font-medium text-slate-600">No tienes experiencias registradas</p>
                <p class="text-sm">¡Comienza creando tu primera experiencia turística!</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $experiencias->links() }}
    </div>
@endsection

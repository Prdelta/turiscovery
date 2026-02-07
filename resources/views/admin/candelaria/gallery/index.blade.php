@extends('layouts.dashboard')

@section('title', 'Galería Histórica')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Galería Histórica de la Candelaria</h1>
                <p class="text-slate-500">Gestiona las fotografías históricas de la festividad.</p>
            </div>
            <a href="{{ route('admin.candelaria.gallery.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nueva Fotografía
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Grid de Fotografías -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($galleries as $gallery)
                <div class="card overflow-hidden group hover:shadow-lg transition-shadow">
                    <!-- Imagen -->
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        <!-- Badge de año -->
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full">
                            <span class="text-sm font-semibold text-slate-700">{{ $gallery->year }}</span>
                        </div>

                        <!-- Badge de estado -->
                        <div class="absolute top-3 right-3">
                            @if($gallery->is_active)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    Activo
                                </span>
                            @else
                                <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full">
                                    Inactivo
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-4">
                        <h3 class="text-lg font-bold text-slate-800 mb-2 line-clamp-2">{{ $gallery->title }}</h3>
                        <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ $gallery->description }}</p>

                        <!-- Metadata -->
                        <div class="flex items-center justify-between text-xs text-slate-500 mb-4 pb-4 border-b">
                            <span class="flex items-center">
                                <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                {{ $gallery->user->name }}
                            </span>
                            <span class="flex items-center">
                                <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                {{ $gallery->created_at->format('d/m/Y') }}
                            </span>
                        </div>

                        <!-- Acciones -->
                        <div class="flex gap-2">
                            <a href="{{ route('admin.candelaria.gallery.edit', $gallery) }}"
                                class="btn btn-outline text-sm flex-1 flex items-center justify-center">
                                <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                Editar
                            </a>
                            <form action="{{ route('admin.candelaria.gallery.destroy', $gallery) }}"
                                method="POST" class="flex-1"
                                onsubmit="return confirm('¿Estás seguro de eliminar esta fotografía?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-sm w-full flex items-center justify-center">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="card p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="image" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-slate-800 mb-2">No hay fotografías en la galería</h3>
                        <p class="text-slate-500 mb-4">Comienza agregando la primera fotografía histórica de la festividad.</p>
                        <a href="{{ route('admin.candelaria.gallery.create') }}" class="btn btn-primary inline-flex items-center">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                            Agregar Primera Fotografía
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($galleries->hasPages())
            <div class="mt-6">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
@endpush

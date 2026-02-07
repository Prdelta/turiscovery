@extends('layouts.dashboard')

@section('title', 'Danzas Tradicionales')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Danzas Tradicionales</h1>
                <p class="text-slate-500">Gestiona las danzas de la Festividad de la Candelaria.</p>
            </div>
            <a href="{{ route('admin.candelaria.danzas.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nueva Danza
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="card p-4 bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-blue-600 font-medium">Total Danzas</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-200 rounded-full flex items-center justify-center">
                        <i data-lucide="music" class="w-6 h-6 text-blue-700"></i>
                    </div>
                </div>
            </div>

            <div class="card p-4 bg-gradient-to-br from-purple-50 to-purple-100 border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-purple-600 font-medium">Mestizas</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['mestizas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-200 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🎭</span>
                    </div>
                </div>
            </div>

            <div class="card p-4 bg-gradient-to-br from-green-50 to-green-100 border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-medium">Autóctonas</p>
                        <p class="text-2xl font-bold text-green-900">{{ $stats['autoctonas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-200 rounded-full flex items-center justify-center">
                        <span class="text-2xl">🗿</span>
                    </div>
                </div>
            </div>

            <div class="card p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-600 font-medium">Destacadas</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $stats['destacadas'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-200 rounded-full flex items-center justify-center">
                        <i data-lucide="star" class="w-6 h-6 text-yellow-700"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card p-4 mb-6 bg-white border border-gray-200 flex flex-wrap gap-4 items-center">
            <form method="GET" class="flex flex-wrap gap-4 items-center w-full">
                <select name="type" class="form-select rounded-lg border-gray-300 text-slate-600"
                    onchange="this.form.submit()">
                    <option value="">Todos los tipos</option>
                    <option value="mestiza" {{ request('type') === 'mestiza' ? 'selected' : '' }}>Mestizas</option>
                    <option value="autoctona" {{ request('type') === 'autoctona' ? 'selected' : '' }}>Autóctonas</option>
                </select>

                <select name="featured" class="form-select rounded-lg border-gray-300 text-slate-600"
                    onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Solo destacadas</option>
                </select>

                @if(request()->hasAny(['type', 'featured']))
                    <a href="{{ route('admin.candelaria.danzas.index') }}" class="text-sm text-primary hover:underline">
                        Limpiar filtros
                    </a>
                @endif
            </form>
        </div>

        <!-- Tabla de Danzas -->
        <div class="card bg-white border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 font-semibold text-slate-600 text-sm">Danza</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Tipo</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Región</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Destacada</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Estado</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($danzas as $danza)
                            <tr class="hover:bg-gray-50">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        @if($danza->image_url)
                                            <img src="{{ $danza->image_url }}" alt="{{ $danza->name }}"
                                                class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                                <span class="text-2xl">{{ $danza->type === 'mestiza' ? '🎭' : '🗿' }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $danza->name }}</p>
                                            <p class="text-xs text-slate-500">Agregado por {{ $danza->user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($danza->type === 'mestiza')
                                        <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-2 py-1 rounded-full">
                                            🎭 Mestiza
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                            🗿 Autóctona
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-600">{{ $danza->region ?? 'N/A' }}</td>
                                <td class="p-4">
                                    @if($danza->is_featured)
                                        <span class="text-yellow-500 flex items-center">
                                            <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        </span>
                                    @else
                                        <span class="text-gray-300">
                                            <i data-lucide="star" class="w-4 h-4"></i>
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    @if($danza->is_active)
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                            Activo
                                        </span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-2 py-1 rounded-full">
                                            Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <div class="flex gap-2 justify-end">
                                        <a href="{{ route('admin.candelaria.danzas.edit', $danza) }}"
                                            class="text-blue-600 hover:text-blue-800" title="Editar">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.candelaria.danzas.destroy', $danza) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar esta danza?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i data-lucide="music" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-slate-800 mb-1">No hay danzas registradas</h3>
                                        <p class="mb-4">Comienza agregando la primera danza tradicional.</p>
                                        <a href="{{ route('admin.candelaria.danzas.create') }}" class="btn btn-outline text-sm">
                                            Agregar Danza
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        @if($danzas->hasPages())
            <div class="mt-6">
                {{ $danzas->links() }}
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

@extends('layouts.dashboard')

@section('title', 'Gestión de Socios')

@section('content')
    <div class="fade-in">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Gestión de Socios</h1>
                <p class="text-slate-500">Administra las cuentas de socios de la plataforma</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.socios.supervision') }}" class="btn btn-outline">
                    <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2"></i>
                    Panel de Supervisión
                </a>
                <a href="{{ route('admin.socios.create') }}" class="btn btn-primary">
                    <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                    Crear Nuevo Socio
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="card p-6 bg-gradient-to-br from-blue-50 to-white border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Total Socios</span>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $socios->total() }}</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-green-50 to-white border border-green-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Socios Activos</span>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ \App\Models\User::where('role', 'socio')->where('is_active', true)->count() }}</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-red-50 to-white border border-red-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Socios Inactivos</span>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="user-x" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ \App\Models\User::where('role', 'socio')->where('is_active', false)->count() }}</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-purple-50 to-white border border-purple-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Nuevos (Este mes)</span>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ \App\Models\User::where('role', 'socio')->whereMonth('created_at', now()->month)->count() }}</p>
            </div>
        </div>

        <!-- Tabla de Socios -->
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                @if($socios->count() > 0)
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Socio
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Contacto
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Fecha Registro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Locales
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($socios as $socio)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($socio->avatar)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $socio->avatar }}" alt="{{ $socio->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold text-sm">{{ substr($socio->name, 0, 2) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-slate-900">{{ $socio->name }}</div>
                                                @if($socio->bio)
                                                    <div class="text-sm text-slate-500 truncate max-w-xs">{{ $socio->bio }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $socio->email }}</div>
                                        @if($socio->phone)
                                            <div class="text-sm text-slate-500">{{ $socio->phone }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-slate-900">{{ $socio->created_at->format('d/m/Y') }}</div>
                                        <div class="text-sm text-slate-500">{{ $socio->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $socio->locales()->count() }} locales
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($socio->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                                Activo
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="toggleStatus({{ $socio->id }}, {{ $socio->is_active ? 'true' : 'false' }})"
                                                class="text-{{ $socio->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $socio->is_active ? 'orange' : 'green' }}-900"
                                                title="{{ $socio->is_active ? 'Desactivar' : 'Activar' }}"
                                                id="toggle-btn-{{ $socio->id }}">
                                                <i data-lucide="{{ $socio->is_active ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                            </button>
                                            <a href="{{ route('admin.socios.edit', $socio) }}"
                                                class="text-blue-600 hover:text-blue-900" title="Editar">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.socios.destroy', $socio) }}" method="POST"
                                                class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este socio?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-100 rounded-full mb-4">
                            <i data-lucide="users" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-slate-900 mb-2">No hay socios registrados</h3>
                        <p class="text-slate-500 mb-4">Comienza creando la primera cuenta de socio</p>
                        <a href="{{ route('admin.socios.create') }}" class="btn btn-primary">
                            <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                            Crear Primer Socio
                        </a>
                    </div>
                @endif
            </div>

            @if($socios->hasPages())
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                    {{ $socios->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });

        async function toggleStatus(socioId, currentStatus) {
            const action = currentStatus ? 'desactivar' : 'activar';

            if (!confirm(`¿Estás seguro de ${action} esta cuenta de socio?`)) {
                return;
            }

            try {
                const response = await axios.post(`/admin/socios/${socioId}/toggle-status`);

                if (response.data.success) {
                    // Mostrar mensaje de éxito
                    alert(response.data.message);

                    // Recargar la página para ver los cambios
                    location.reload();
                } else {
                    alert(response.data.message || 'Error al cambiar el estado');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cambiar el estado de la cuenta');
            }
        }
    </script>
@endpush

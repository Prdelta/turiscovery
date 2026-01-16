@extends('layouts.dashboard')

@section('title', 'Mis Locales')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Mis Locales Listados</h1>
                <p class="text-slate-500">Gestiona los negocios que has registrado en la plataforma.</p>
            </div>
            <a href="{{ url('/dashboard/locales/create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nuevo Local
            </a>
        </div>

        <!-- Filtros / Búsqueda -->
        <div class="card p-4 mb-6 bg-white border border-gray-200 flex flex-wrap gap-4 items-center justify-between">
            <div class="relative max-w-sm w-full">
                <i data-lucide="search" class="absolute left-3 top-2.5 w-5 h-5 text-slate-400"></i>
                <input type="text" placeholder="Buscar por nombre..."
                    class="form-input w-full pl-10 rounded-lg border-gray-300">
            </div>
            <select class="form-select rounded-lg border-gray-300 text-slate-600">
                <option value="all">Todos los estados</option>
                <option value="active">Activos</option>
                <option value="inactive">Inactivos</option>
            </select>
        </div>

        <!-- Tabla -->
        <div class="card bg-white border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 font-semibold text-slate-600 text-sm">Local</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Categoría</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Ubicación</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm">Estado</th>
                            <th class="p-4 font-semibold text-slate-600 text-sm text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Empty State Placeholder -->
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="store" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-800 mb-1">No tienes locales registrados</h3>
                                    <p class="mb-4">Comienza registrando tu primer negocio para atraer clientes.</p>
                                    <a href="{{ url('/dashboard/locales/create') }}" class="btn btn-outline text-sm">
                                        Registrar Local
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-gray-200 flex justify-between items-center text-sm text-slate-500">
                <span>Mostrando 0 de 0 locales</span>
                <div class="flex gap-2">
                    <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50" disabled>Anterior</button>
                    <button class="px-3 py-1 border rounded hover:bg-gray-50 disabled:opacity-50"
                        disabled>Siguiente</button>
                </div>
            </div>
        </div>
    </div>
@endsection

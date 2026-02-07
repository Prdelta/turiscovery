@extends('layouts.dashboard')

@section('title', 'Panel de Supervisión')

@section('content')
    <div class="fade-in">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Panel de Supervisión General</h1>
                <p class="text-slate-500">Monitoreo completo de socios y actividad de la plataforma</p>
            </div>
            <a href="{{ route('admin.socios.index') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Socios
            </a>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="card p-6 bg-gradient-to-br from-blue-50 to-white border border-blue-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Total Socios</span>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['total_socios'] }}</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-green-50 to-white border border-green-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Activos</span>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['activos'] }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $stats['total_socios'] > 0 ? round(($stats['activos'] / $stats['total_socios']) * 100) : 0 }}% del total</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-red-50 to-white border border-red-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Inactivos</span>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="user-x" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['inactivos'] }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $stats['total_socios'] > 0 ? round(($stats['inactivos'] / $stats['total_socios']) * 100) : 0 }}% del total</p>
            </div>

            <div class="card p-6 bg-gradient-to-br from-purple-50 to-white border border-purple-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-slate-600">Nuevos (Este mes)</span>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['nuevos_mes'] }}</p>
            </div>
        </div>

        <!-- Estadísticas de Contenido -->
        <div class="card p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center">
                <i data-lucide="bar-chart" class="w-5 h-5 mr-2"></i>
                Contenido en la Plataforma
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <i data-lucide="store" class="w-8 h-8 mx-auto mb-2 text-blue-600"></i>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_locales'] }}</p>
                    <p class="text-xs text-slate-500">Locales</p>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <i data-lucide="calendar" class="w-8 h-8 mx-auto mb-2 text-green-600"></i>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_eventos'] }}</p>
                    <p class="text-xs text-slate-500">Eventos</p>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <i data-lucide="percent" class="w-8 h-8 mx-auto mb-2 text-orange-600"></i>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_promociones'] }}</p>
                    <p class="text-xs text-slate-500">Promociones</p>
                </div>
                <div class="text-center p-4 bg-slate-50 rounded-lg">
                    <i data-lucide="compass" class="w-8 h-8 mx-auto mb-2 text-purple-600"></i>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_experiencias'] }}</p>
                    <p class="text-xs text-slate-500">Experiencias</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Socios Más Activos -->
            <div class="card">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-lg flex items-center">
                        <i data-lucide="trophy" class="w-5 h-5 mr-2 text-yellow-600"></i>
                        Top 10 Socios Más Activos
                    </h3>
                </div>
                <div class="p-4">
                    @if($sociosMasActivos->count() > 0)
                        <div class="space-y-3">
                            @foreach($sociosMasActivos as $socio)
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg hover:bg-slate-100 transition">
                                    <div class="flex items-center gap-3">
                                        @if($socio->avatar)
                                            <img src="{{ $socio->avatar }}" class="w-10 h-10 rounded-full" alt="{{ $socio->name }}">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">{{ substr($socio->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $socio->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $socio->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-blue-600">{{ $socio->locales_count }}</p>
                                        <p class="text-xs text-slate-500">locales</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-400 py-8">No hay socios activos</p>
                    @endif
                </div>
            </div>

            <!-- Socios Inactivos -->
            <div class="card">
                <div class="p-4 bg-red-50 border-b border-red-200">
                    <h3 class="font-bold text-lg flex items-center text-red-800">
                        <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                        Socios Desactivados
                    </h3>
                </div>
                <div class="p-4">
                    @if($sociosInactivos->count() > 0)
                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($sociosInactivos as $socio)
                                <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                                                <span class="text-red-600 font-semibold text-xs">{{ substr($socio->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <p class="font-medium text-slate-800 text-sm">{{ $socio->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $socio->email }}</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">
                                            Inactivo
                                        </span>
                                    </div>
                                    @if($socio->deactivated_at)
                                        <div class="text-xs text-slate-600 mt-2 pl-10">
                                            <p>Desactivado: {{ $socio->deactivated_at->format('d/m/Y H:i') }}</p>
                                            @if($socio->deactivatedBy)
                                                <p>Por: {{ $socio->deactivatedBy->name }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-slate-400 py-8">No hay socios desactivados</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad Reciente -->
        <div class="card">
            <div class="p-4 bg-slate-50 border-b border-slate-200">
                <h3 class="font-bold text-lg flex items-center">
                    <i data-lucide="activity" class="w-5 h-5 mr-2 text-blue-600"></i>
                    Actividad Reciente
                </h3>
            </div>
            <div class="p-4">
                @if($actividadReciente->count() > 0)
                    <div class="space-y-2">
                        @foreach($actividadReciente as $socio)
                            <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-lg transition">
                                <div class="flex items-center gap-3">
                                    @if($socio->avatar)
                                        <img src="{{ $socio->avatar }}" class="w-8 h-8 rounded-full" alt="{{ $socio->name }}">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center">
                                            <span class="text-slate-600 font-semibold text-xs">{{ substr($socio->name, 0, 2) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-slate-800 text-sm">{{ $socio->name }}</p>
                                        <p class="text-xs text-slate-500">Última actividad: {{ $socio->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @if($socio->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">Activo</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Inactivo</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-slate-400 py-8">No hay actividad reciente</p>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
@endpush

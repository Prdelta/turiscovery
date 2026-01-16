@extends('layouts.dashboard')

@section('title', 'Mis Promociones')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Mis Promociones</h1>
                <p class="text-slate-500">Gestiona tus cupones y ofertas activas.</p>
            </div>
            <a href="{{ url('/dashboard/promociones/create') }}" class="btn btn-warning text-white">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nueva Promoción
            </a>
        </div>

        <!-- Empty State -->
        <div class="card bg-white border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="tag" class="w-8 h-8 text-orange-400"></i>
            </div>
            <h3 class="text-lg font-medium text-slate-800 mb-2">No tienes promociones activas</h3>
            <p class="text-slate-500 mb-6">Crea ofertas especiales para aumentar tus ventas.</p>
            <a href="{{ url('/dashboard/promociones/create') }}"
                class="btn btn-outline text-orange-600 border-orange-600 hover:bg-orange-600 hover:text-white">
                Crear Primera Promoción
            </a>
        </div>
    </div>
@endsection

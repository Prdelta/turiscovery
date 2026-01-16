@extends('layouts.dashboard')

@section('title', 'Mis Eventos')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Mis Eventos</h1>
                <p class="text-slate-500">Administra tu calendario de eventos.</p>
            </div>
            <a href="{{ url('/dashboard/eventos/create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Nuevo Evento
            </a>
        </div>

        <!-- Empty State -->
        <div class="card bg-white border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar-off" class="w-8 h-8 text-purple-400"></i>
            </div>
            <h3 class="text-lg font-medium text-slate-800 mb-2">No tienes eventos programados</h3>
            <p class="text-slate-500 mb-6">Crea eventos para atraer a turistas en fechas específicas.</p>
            <a href="{{ url('/dashboard/eventos/create') }}" class="btn btn-outline">
                Crear Primer Evento
            </a>
        </div>
    </div>
@endsection

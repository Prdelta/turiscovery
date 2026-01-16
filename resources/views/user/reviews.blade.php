@extends('layouts.dashboard')

@section('title', 'Mis Reseñas')
@section('page-title', 'Mis Reseñas')
@section('page-subtitle', 'Historial de tus opiniones y valoraciones')

@section('content')
    <div class="fade-in max-w-4xl">

        <div class="space-y-6">
            <!-- Reseña Card 1 -->
            <div class="card p-6 bg-white border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-lg bg-gray-200 bg-cover bg-center"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Hotel Los Uros</h3>
                            <p class="text-sm text-slate-500 mb-1">Hospedaje • Hace 2 días</p>
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <button class="text-slate-400 hover:text-slate-600"><i data-lucide="more-vertical"
                                class="w-5 h-5"></i></button>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-slate-800 text-sm mb-1">Buena atención, pero podría mejorar el wifi</h4>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        La habitación estaba limpia y la vista al lago es increíble. Sin embargo, la conexión a internet era
                        un poco inestable en las noches. El desayuno buffet estuvo delicioso.
                    </p>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button class="text-sm text-slate-500 hover:text-blue-600 font-medium">Editar</button>
                    <button class="text-sm text-slate-500 hover:text-red-600 font-medium">Eliminar</button>
                </div>
            </div>

            <!-- Reseña Card 2 -->
            <div class="card p-6 bg-white border border-gray-200">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-lg bg-gray-200 bg-cover bg-center"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Tour Sillustani</h3>
                            <p class="text-sm text-slate-500 mb-1">Experiencia • Hace 1 semana</p>
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-slate-800 text-sm mb-1">¡Mágico!</h4>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Las chullpas son impresionantes y el guía explicó todo muy detalladamente. Recomiendo ir al
                        atardecer para las mejores fotos.
                    </p>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button class="text-sm text-slate-500 hover:text-blue-600 font-medium">Editar</button>
                    <button class="text-sm text-slate-500 hover:text-red-600 font-medium">Eliminar</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <section class="hero text-center"
        style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1535905557558-afc4877a26fc?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
        <div class="container">
            <span class="badge badge-warning mb-2 md:inline-flex hidden"
                style="background: rgba(255,193,7,0.2); color: #ffc107; border: 1px solid rgba(255,193,7,0.3); backdrop-filter: blur(5px);">
                Patrimonio Cultural Inmaterial de la Humanidad
            </span>
            <h1 class="fade-in text-white mb-4"
                style="font-size: 3.5rem; font-weight: 800; text-shadow: 0 4px 6px rgba(0,0,0,0.3);">
                Festividad de la Virgen de la Candelaria
            </h1>
            <p class="fade-in text-gray-200 mb-8"
                style="font-size: 1.25rem; max-width: 800px; margin-left: auto; margin-right: auto; animation-delay: 0.2s;">
                La manifestación cultural más grande del Perú. Una explosión de fe, danza y tradición que une al mundo en
                Puno.
            </p>
            <div class="fade-in" style="animation-delay: 0.4s; display: flex; gap: 1rem; justify-content: center;">
                <a href="#historia" class="btn btn-primary">Descubrir Historia</a>
                <a href="#cronograma" class="btn btn-outline text-white border-white hover:bg-white hover:text-black">Ver
                    Cronograma</a>
            </div>
        </div>
    </section>

    <section id="historia" class="section">
        <div class="container">
            <div class="grid grid-2" style="align-items: center; gap: 4rem;">
                <div>
                    <span class="badge badge-info mb-2">HISTORIA Y TRADICIÓN</span>
                    <h2 class="mb-4">Una Fe que Mueve Montañas</h2>
                    <p class="mb-4">
                        La Festividad de la Virgen de la Candelaria, celebrada cada mes de febrero en la ciudad de Puno, es
                        una de las festividades religiosas más significativas de Sudamérica.
                    </p>
                    <p class="mb-4">
                        Reconocida por la UNESCO como Patrimonio Cultural Inmaterial de la Humanidad, esta celebración reúne
                        a más de 170 conjuntos de danzas, 40 mil danzarines y músicos, y miles de visitantes de todo el
                        mundo.
                    </p>
                    <div class="card p-4 bg-blue-50 border-blue-100" style="border-left: 4px solid var(--color-primary);">
                        <div style="display: flex; gap: 1rem;">
                            <i data-lucide="info" style="color: var(--color-primary); flex-shrink: 0;"></i>
                            <p class="mb-0 text-sm">
                                <strong>Dato Curioso:</strong> La imagen de la Virgen de la Candelaria es conocida
                                cariñosamente como la "Mamita Candelaria" y es venerada tanto en templos católicos como en
                                rituales andinos.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="grid grid-2" style="gap: 1rem;">
                    <img src="/img/candelaria_hist.png"
                        class="rounded-xl shadow-lg hover:scale-105 transition-transform duration-300"
                        style="height: 240px; width: 100%; object-fit: cover;">
                    <img src="/img/evt_candelaria.png"
                        class="rounded-xl shadow-lg hover:scale-105 transition-transform duration-300"
                        style="height: 240px; width: 100%; object-fit: cover; margin-top: 2rem;">
                </div>
            </div>
        </div>
    </section>

    <section id="cronograma" class="section bg-slate-50">
        <div class="container">
            <div class="text-center mb-12">
                <span class="badge badge-primary mb-2">AGENDA 2026</span>
                <h2>Actividades Principales</h2>
                <p style="max-width: 600px; margin: 0 auto;">No te pierdas los momentos clave de la festividad.</p>
            </div>

            <div class="grid grid-3">
                <!-- Dia 1 -->
                <div class="card p-0 overflow-hidden hover:shadow-lg transition-shadow">
                    <div style="background: var(--color-primary); padding: 1rem; color: white; text-align: center;">
                        <span style="font-size: 0.9rem; opacity: 0.9;">2 DE FEBRERO</span>
                        <h3 style="color: white; margin: 0; font-size: 1.5rem;">Día Central</h3>
                    </div>
                    <div class="p-6">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li class="mb-3 pb-3 border-b border-slate-100 flex gap-3">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>06:00 AM - Misa de Alva</span>
                            </li>
                            <li class="mb-3 pb-3 border-b border-slate-100 flex gap-3">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>10:00 AM - Misa Central</span>
                            </li>
                            <li class="flex gap-3">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>02:00 PM - Procesión</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Concurso Danzas Autoctonas -->
                <div class="card p-0 overflow-hidden hover:shadow-lg transition-shadow">
                    <div style="background: var(--color-secondary); padding: 1rem; color: white; text-align: center;">
                        <span style="font-size: 0.9rem; opacity: 0.9;">3-4 DE FEBRERO</span>
                        <h3 style="color: white; margin: 0; font-size: 1.5rem;">Danzas Autóctonas</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Concurso regional de danzas autóctonas en el Estadio Enrique Torres Belón.
                        </p>
                        <a href="/eventos" class="btn btn-outline w-full justify-center">Ver Detalles</a>
                    </div>
                </div>

                <!-- Concurso Trajes de Luces -->
                <div class="card p-0 overflow-hidden hover:shadow-lg transition-shadow">
                    <div style="background: var(--color-accent); padding: 1rem; color: white; text-align: center;">
                        <span style="font-size: 0.9rem; opacity: 0.9;">9-11 DE FEBRERO</span>
                        <h3 style="color: white; margin: 0; font-size: 1.5rem;">Trajes de Luces</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">
                            Concurso de danzas en traje de luces. El espectáculo más colorido del año.
                        </p>
                        <a href="/eventos" class="btn btn-outline w-full justify-center">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
@endpush

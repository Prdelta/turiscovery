@extends('layouts.app')

@section('content')
    <!-- Hero Section con Carousel -->
    <section class="hero relative flex items-center justify-center min-h-[700px] overflow-hidden"
        style="background-color: #000 !important;">

        <div id="candelaria-carousel" class="absolute inset-0 w-full h-full z-0">
            <img src="{{ asset('images/puno_dancers.png') }}" alt="Danzantes de la Candelaria"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-100">

            <img src="/img/candelaria_hist.png" alt="Historia Candelaria"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0">

            <img src="/img/evt_candelaria.png" alt="Evento Candelaria"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0">

            <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/50 to-black/70 z-10"></div>
        </div>

        <div class="container relative z-20 text-center text-white px-4">
            <div class="inline-flex mb-6 animate-pulse-soft">
                <span class="badge bg-gradient-to-r from-yellow-400 to-orange-500 text-slate-900 px-8 py-3 text-sm font-black shadow-2xl border-2 border-white/40">
                    <i data-lucide="award" class="w-5 h-5 inline mr-2"></i>
                    PATRIMONIO CULTURAL INMATERIAL DE LA HUMANIDAD - UNESCO
                </span>
            </div>

            <h1 class="fade-in text-white text-4xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight text-shadow-strong">
                Festividad de la Virgen<br/>
                <span class="bg-gradient-to-r from-yellow-300 via-orange-400 to-red-400 bg-clip-text text-transparent">de la Candelaria</span>
            </h1>

            <p class="fade-in text-white/95 text-lg md:text-xl lg:text-2xl max-w-4xl mx-auto mb-8 leading-relaxed text-shadow font-medium" style="animation-delay: 0.2s;">
                La manifestación cultural más grande del Perú. Una explosión de fe, danza y tradición que une al mundo en Puno.
            </p>

            <!-- Stats impactantes -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mb-10 fade-in" style="animation-delay: 0.3s;">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-4xl font-black text-yellow-300 mb-1">170+</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Conjuntos</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-4xl font-black text-yellow-300 mb-1">40K+</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Danzarines</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-4xl font-black text-yellow-300 mb-1">300</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Años de Tradición</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-4xl font-black text-yellow-300 mb-1">200K+</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Visitantes</div>
                </div>
            </div>

            <div class="fade-in flex flex-wrap gap-4 justify-center" style="animation-delay: 0.4s;">
                <a href="#danzas" class="btn btn-secondary font-bold px-8 py-4 shadow-2xl transform hover:scale-105 transition-all">
                    <i data-lucide="music" class="w-5 h-5"></i>
                    Ver Danzas
                </a>
                <a href="#cronograma" class="btn btn-outline text-white border-2 border-white hover:bg-white hover:text-black font-bold px-8 py-4 shadow-xl transition-all">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Cronograma 2026
                </a>
                <a href="/experiencias" class="btn bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-slate-900 font-black px-8 py-4 shadow-2xl transform hover:scale-105 transition-all">
                    <i data-lucide="ticket" class="w-5 h-5"></i>
                    Reservar Tours
                </a>
            </div>
        </div>

        <!-- Wave decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-white to-transparent z-10"></div>
    </section>

    <!-- Sección Historia Mejorada -->
    <section id="historia" class="section" style="background: white;">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-flex items-center gap-2 badge badge-info mb-4 px-5 py-2 text-sm font-bold">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                        HISTORIA Y TRADICIÓN
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6" style="color: var(--color-text);">
                        Una Fe que Mueve Montañas
                    </h2>
                    <p class="text-lg mb-4 leading-relaxed" style="color: var(--color-text-light);">
                        La Festividad de la Virgen de la Candelaria, celebrada cada mes de febrero en la ciudad de Puno, es
                        una de las festividades religiosas más significativas de Sudamérica.
                    </p>
                    <p class="text-lg mb-6 leading-relaxed" style="color: var(--color-text-light);">
                        Reconocida por la UNESCO como <strong style="color: var(--color-primary);">Patrimonio Cultural Inmaterial de la Humanidad</strong>, esta celebración reúne
                        a más de 170 conjuntos de danzas, 40 mil danzarines y músicos, y miles de visitantes de todo el
                        mundo.
                    </p>

                    <!-- Datos curiosos -->
                    <div class="space-y-4">
                        <div class="card p-5 border-l-4 hover:shadow-lg transition-shadow" style="border-left-color: var(--color-primary); background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--color-primary);">
                                    <i data-lucide="heart" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold mb-1" style="color: var(--color-text);">La Mamita Candelaria</p>
                                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">
                                        La imagen es venerada tanto en templos católicos como en rituales andinos, fusionando dos cosmovisiones.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card p-5 border-l-4 hover:shadow-lg transition-shadow" style="border-left-color: var(--color-secondary); background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                            <div class="flex gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--color-secondary);">
                                    <i data-lucide="globe" class="w-5 h-5 text-white"></i>
                                </div>
                                <div>
                                    <p class="font-bold mb-1" style="color: var(--color-text);">Reconocimiento Mundial</p>
                                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">
                                        Declarada Patrimonio Cultural Inmaterial de la Humanidad por UNESCO en 2014.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <img src="/img/candelaria_hist.png" alt="Historia de la Candelaria"
                        class="rounded-2xl shadow-2xl hover:scale-105 transition-transform duration-300 w-full h-64 object-cover">
                    <img src="/img/evt_candelaria.png" alt="Evento Candelaria"
                        class="rounded-2xl shadow-2xl hover:scale-105 transition-transform duration-300 w-full h-64 object-cover mt-8">
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Historia Detallada -->
    <section class="section" style="background: white;">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="badge bg-slate-100 text-slate-700 mb-4 px-5 py-2 text-sm font-bold border border-slate-200">
                    HISTORIA COMPLETA
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">
                    El Origen de una Tradición Milenaria
                </h2>
                <p class="max-w-4xl mx-auto text-lg leading-relaxed mb-8" style="color: var(--color-text-light);">
                    Un viaje a través de más de 400 años de fe, cultura y tradición que han convertido a Puno en la Capital Folklórica del Perú.
                </p>
                <button id="toggleTimeline" class="btn btn-outline px-6 py-3 font-bold">
                    Ver Línea de Tiempo Histórica
                </button>
            </div>

            <!-- Timeline Histórica (Colapsable) -->
            <div id="timelineContent" class="hidden relative max-w-5xl mx-auto mb-16 mt-8">
                <!-- Línea central -->
                <div class="absolute left-1/2 transform -translate-x-1/2 w-1 h-full hidden lg:block bg-slate-200"></div>

                <!-- Eventos Timeline -->
                <div class="space-y-12">
                    <!-- 1583 - Llegada de la imagen -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="lg:text-right">
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border border-slate-200">
                                <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    AÑO 1583
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">Llegada de la Virgen</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    La imagen de la Virgen de la Candelaria fue traída desde España durante el proceso de evangelización del virreinato del Perú. Los colonizadores españoles introdujeron la devoción a la Virgen María en las comunidades andinas del altiplano puneño.
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Época Colonial
                                </span>
                            </div>
                        </div>
                        <div class="hidden lg:block"></div>
                    </div>

                    <!-- Siglo XVII - La Leyenda -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="hidden lg:block"></div>
                        <div>
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border border-slate-200">
                                <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    SIGLO XVII
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">La Leyenda del Milagro</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    La tradición cuenta que durante un conflicto con invasores, los pobladores puneños invocaron a la Virgen de la Candelaria. Según la leyenda, la Virgen apareció con una espada en la mano, protegiendo a la ciudad de Puno. Este evento fortaleció la devoción popular.
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Protectora de Puno
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Siglo XVI-XVII - Sincretismo -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="lg:text-right">
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border border-slate-200">
                                <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    SIGLOS XVI-XVII
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">Sincretismo Cultural</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    Las danzas reflejan la fusión única entre tradiciones católicas europeas y la cosmovisión andina prehispánica. Los pueblos Quechua y Aymara adaptaron sus rituales ancestrales, creando una celebración que honra tanto a la Virgen como a la Pachamama (Madre Tierra).
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Fusión de Dos Mundos
                                </span>
                            </div>
                        </div>
                        <div class="hidden lg:block"></div>
                    </div>

                    <!-- Siglo XX - Organización -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="hidden lg:block"></div>
                        <div>
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border border-slate-200">
                                <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    SIGLO XX
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">Institucionalización</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    Se formaron las tres federaciones regionales de conjuntos folklóricos que organizan la festividad: autóctonas, mestizas y trajes de luces. Estas instituciones preservan las técnicas tradicionales mediante ensayos musicales, talleres coreográficos y transmisión de conocimientos a las nuevas generaciones.
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Organización Comunitaria
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2014 - UNESCO -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="lg:text-right">
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border-2 border-slate-300">
                                <span class="inline-block bg-slate-900 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    27 NOVIEMBRE 2014
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">Reconocimiento UNESCO</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    La Festividad de la Virgen de la Candelaria fue inscrita en la Lista Representativa del Patrimonio Cultural Inmaterial de la Humanidad por UNESCO. Este reconocimiento internacional destaca su valor como expresión cultural que fortalece la identidad colectiva y promueve el respeto por la diversidad cultural.
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Patrimonio de la Humanidad
                                </span>
                            </div>
                        </div>
                        <div class="hidden lg:block"></div>
                    </div>

                    <!-- Actualidad -->
                    <div class="grid lg:grid-cols-2 gap-8 items-center">
                        <div class="hidden lg:block"></div>
                        <div>
                            <div class="card p-8 hover:shadow-lg transition-all bg-white border border-slate-200">
                                <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-4">
                                    ACTUALIDAD
                                </span>
                                <h3 class="text-xl font-bold mb-3" style="color: var(--color-text);">Una Tradición Viva</h3>
                                <p class="text-sm leading-relaxed mb-4" style="color: var(--color-text-light);">
                                    Hoy en día, la festividad congrega a más de 200,000 visitantes anualmente. Puneños emigrantes regresan desde todo el mundo para participar, fortaleciendo los lazos culturales. La celebración involucra actos religiosos, festivos y culturales que mantienen viva la identidad cultural del altiplano.
                                </p>
                                <span class="inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold">
                                    Proyección Internacional
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección Sincretismo Cultural -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
                <!-- Elementos Católicos -->
                <div class="card p-8 bg-white border border-slate-200 hover:shadow-lg transition-all">
                    <div class="mb-6">
                        <span class="inline-block bg-slate-800 text-white px-3 py-1 rounded text-xs font-bold mb-3">
                            TRADICIÓN EUROPEA
                        </span>
                        <h3 class="text-xl font-bold" style="color: var(--color-text);">Elementos Católicos</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Misas y Procesiones</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Celebraciones litúrgicas católicas tradicionales</p>
                        </li>
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Veneración Mariana</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Devoción a la Virgen María bajo advocación de Candelaria</p>
                        </li>
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Santoral Cristiano</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Celebración el 2 de febrero, día de la Purificación</p>
                        </li>
                        <li>
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Imágenes Religiosas</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Iconografía católica en altares y vestimentas</p>
                        </li>
                    </ul>
                </div>

                <!-- Elementos Andinos -->
                <div class="card p-8 bg-white border border-slate-200 hover:shadow-lg transition-all">
                    <div class="mb-6">
                        <span class="inline-block bg-slate-800 text-white px-3 py-1 rounded text-xs font-bold mb-3">
                            TRADICIÓN ANDINA
                        </span>
                        <h3 class="text-xl font-bold" style="color: var(--color-text);">Elementos Andinos</h3>
                    </div>
                    <ul class="space-y-3">
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Rituales a la Pachamama</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Ceremonias de purificación y ofrendas a la Madre Tierra</p>
                        </li>
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Danzas Ancestrales</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Coreografías que representan cosmovisión Quechua y Aymara</p>
                        </li>
                        <li class="pb-3 border-b border-slate-100">
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Instrumentos Autóctonos</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Zampoñas, quenas, bombos y otros instrumentos andinos</p>
                        </li>
                        <li>
                            <p class="font-bold text-sm mb-1" style="color: var(--color-text);">Simbolismo Textil</p>
                            <p class="text-xs" style="color: var(--color-text-light);">Tejidos con iconografía andina y colores simbólicos</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Legado UNESCO -->
            <div class="card p-10 text-center bg-slate-50 border border-slate-200">
                <div class="max-w-4xl mx-auto">
                    <span class="inline-block bg-slate-800 text-white px-4 py-2 rounded text-xs font-bold mb-6">
                        PATRIMONIO CULTURAL INMATERIAL DE LA HUMANIDAD
                    </span>
                    <h3 class="text-2xl md:text-3xl font-bold mb-6" style="color: var(--color-text);">
                        Más que una Festividad: Un Legado para el Mundo
                    </h3>
                    <p class="text-base leading-relaxed mb-8" style="color: var(--color-text-light);">
                        La UNESCO reconoció que esta festividad "fortalece la identidad cultural colectiva de las comunidades Quechua y Aymara, y promueve el respeto por la diversidad cultural y la creatividad humana". La celebración involucra actos religiosos, festivos y culturales que tienen sus raíces en tradiciones católicas y elementos simbólicos de la cosmovisión andina.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white rounded-lg p-6 border border-slate-200">
                            <div class="text-3xl font-bold mb-2" style="color: var(--color-text);">170</div>
                            <div class="text-xs font-semibold text-slate-600">Conjuntos Folklóricos</div>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-slate-200">
                            <div class="text-3xl font-bold mb-2" style="color: var(--color-text);">40,000</div>
                            <div class="text-xs font-semibold text-slate-600">Danzarines y Músicos</div>
                        </div>
                        <div class="bg-white rounded-lg p-6 border border-slate-200">
                            <div class="text-3xl font-bold mb-2" style="color: var(--color-text);">2</div>
                            <div class="text-xs font-semibold text-slate-600">Etnias Principales (Quechua y Aymara)</div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 italic">
                        Fuente: UNESCO - Lista Representativa del Patrimonio Cultural Inmaterial de la Humanidad
                    </p>
                </div>
            </div>

            <!-- Galería Histórica por Años -->
            <div class="mt-16">
                <div class="text-center mb-12">
                    <span class="badge bg-slate-100 text-slate-700 mb-4 px-5 py-2 text-sm font-bold border border-slate-200">
                        GALERÍA HISTÓRICA
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">
                        La Candelaria a Través de los Años
                    </h2>
                    <p class="max-w-3xl mx-auto text-base leading-relaxed" style="color: var(--color-text-light);">
                        Revive momentos memorables de la festividad capturados a lo largo del tiempo.
                    </p>
                </div>

                <!-- Filtros por Año -->
                <div class="flex flex-wrap gap-2 justify-center mb-8" id="yearFilters">
                    <button class="year-filter active px-4 py-2 rounded bg-slate-800 text-white text-sm font-bold hover:bg-slate-700 transition-colors" data-year="all">
                        Todos los Años
                    </button>
                    <!-- Los años se cargarán dinámicamente -->
                </div>

                <!-- Grid de Fotos -->
                <div id="galleryGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="col-span-full text-center py-12 text-slate-400">
                        <p>No hay imágenes en la galería aún.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Danzas Principales -->
    <section id="danzas" class="section" style="background: white;">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="badge bg-slate-100 text-slate-700 mb-4 px-5 py-2 text-sm font-bold border border-slate-200">
                    DANZAS REPRESENTATIVAS
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">
                    Las Danzas de la Candelaria
                </h2>
                <p class="max-w-3xl mx-auto text-base leading-relaxed" style="color: var(--color-text-light);">
                    Conoce las danzas que roban el corazón de miles de espectadores cada año.
                </p>
            </div>

            <div id="danzasGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="col-span-full text-center py-12 text-slate-400">
                    <p>Cargando danzas...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Cronograma Mejorada -->
    <section id="cronograma" class="section" style="background: var(--color-bg-light);">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 badge badge-primary mb-4 px-5 py-2 text-sm font-bold">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    AGENDA 2026
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">
                    Actividades Principales
                </h2>
                <p class="max-w-3xl mx-auto text-lg leading-relaxed" style="color: var(--color-text-light);">
                    No te pierdas los momentos clave de la festividad más importante del Perú.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Dia Central -->
                <div class="card p-0 overflow-hidden hover:shadow-2xl transition-all border-2" style="border-color: var(--color-primary);">
                    <div class="p-6 text-center text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
                        <span class="text-sm font-bold opacity-90 uppercase tracking-wide">2 DE FEBRERO</span>
                        <h3 class="text-2xl font-black text-white mt-2 mb-1">Día Central</h3>
                        <p class="text-sm text-white/80">Celebración Principal</p>
                    </div>
                    <div class="p-6 bg-white">
                        <ul class="space-y-4">
                            <li class="flex gap-3 items-start pb-3 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--color-primary-light);">
                                    <i data-lucide="sunrise" class="w-4 h-4" style="color: var(--color-primary);"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm" style="color: var(--color-text);">06:00 AM</p>
                                    <p class="text-xs" style="color: var(--color-text-light);">Misa de Alva</p>
                                </div>
                            </li>
                            <li class="flex gap-3 items-start pb-3 border-b border-slate-100">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--color-primary-light);">
                                    <i data-lucide="church" class="w-4 h-4" style="color: var(--color-primary);"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm" style="color: var(--color-text);">10:00 AM</p>
                                    <p class="text-xs" style="color: var(--color-text-light);">Misa Central</p>
                                </div>
                            </li>
                            <li class="flex gap-3 items-start">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" style="background: var(--color-primary-light);">
                                    <i data-lucide="flag" class="w-4 h-4" style="color: var(--color-primary);"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm" style="color: var(--color-text);">02:00 PM</p>
                                    <p class="text-xs" style="color: var(--color-text-light);">Procesión de la Virgen</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Danzas Autóctonas -->
                <div class="card p-0 overflow-hidden hover:shadow-2xl transition-all border-2" style="border-color: var(--color-secondary);">
                    <div class="p-6 text-center text-white" style="background: linear-gradient(135deg, var(--color-secondary) 0%, #5cc76b 100%);">
                        <span class="text-sm font-bold opacity-90 uppercase tracking-wide">3-4 DE FEBRERO</span>
                        <h3 class="text-2xl font-black text-white mt-2 mb-1">Danzas Autóctonas</h3>
                        <p class="text-sm text-white/80">Concurso Regional</p>
                    </div>
                    <div class="p-6 bg-white">
                        <p class="text-sm leading-relaxed mb-6" style="color: var(--color-text-light);">
                            Concurso regional de danzas autóctonas en el Estadio Enrique Torres Belón. Apreciación de danzas tradicionales del altiplano.
                        </p>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-xs" style="color: var(--color-text-light);">
                                <i data-lucide="map-pin" class="w-4 h-4" style="color: var(--color-secondary);"></i>
                                <span>Estadio Torres Belón</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs" style="color: var(--color-text-light);">
                                <i data-lucide="ticket" class="w-4 h-4" style="color: var(--color-secondary);"></i>
                                <span>Entrada con ticket</span>
                            </div>
                        </div>
                        <a href="/eventos" class="btn btn-outline w-full justify-center">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Ver Detalles
                        </a>
                    </div>
                </div>

                <!-- Trajes de Luces -->
                <div class="card p-0 overflow-hidden hover:shadow-2xl transition-all border-2" style="border-color: var(--color-accent);">
                    <div class="p-6 text-center text-white" style="background: linear-gradient(135deg, var(--color-accent) 0%, #e89b1f 100%);">
                        <span class="text-sm font-bold opacity-90 uppercase tracking-wide">9-11 DE FEBRERO</span>
                        <h3 class="text-2xl font-black text-white mt-2 mb-1">Trajes de Luces</h3>
                        <p class="text-sm text-white/80">El Gran Espectáculo</p>
                    </div>
                    <div class="p-6 bg-white">
                        <p class="text-sm leading-relaxed mb-6" style="color: var(--color-text-light);">
                            Concurso de danzas en traje de luces. El espectáculo más colorido y esperado del año con miles de danzarines.
                        </p>
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center gap-2 text-xs" style="color: var(--color-text-light);">
                                <i data-lucide="sparkles" class="w-4 h-4" style="color: var(--color-accent);"></i>
                                <span>Trajes espectaculares</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs" style="color: var(--color-text-light);">
                                <i data-lucide="camera" class="w-4 h-4" style="color: var(--color-accent);"></i>
                                <span>Evento fotogénico</span>
                            </div>
                        </div>
                        <a href="/eventos" class="btn btn-outline w-full justify-center">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Información Práctica -->
    <section class="section" style="background: white;">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <span class="inline-flex items-center gap-2 badge badge-info mb-4 px-5 py-2 text-sm font-bold">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    GUÍA DEL VIAJERO
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">
                    Información Práctica
                </h2>
                <p class="max-w-3xl mx-auto text-lg leading-relaxed" style="color: var(--color-text-light);">
                    Todo lo que necesitas saber para disfrutar al máximo tu visita a la Candelaria.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Mejor época para visitar -->
                <div class="card p-8 border-l-4 hover:shadow-xl transition-shadow" style="border-left-color: var(--color-primary); background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);">
                    <div class="flex gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--color-primary);">
                            <i data-lucide="calendar-check" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2" style="color: var(--color-text);">Mejor Época para Visitar</h3>
                            <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">
                                La festividad se celebra del 2 al 11 de febrero. Recomendamos llegar al menos 2 días antes para aclimatarse a la altura (3,800 msnm).
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cómo llegar -->
                <div class="card p-8 border-l-4 hover:shadow-xl transition-shadow" style="border-left-color: var(--color-secondary); background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                    <div class="flex gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--color-secondary);">
                            <i data-lucide="plane" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2" style="color: var(--color-text);">Cómo Llegar</h3>
                            <p class="text-sm leading-relaxed mb-2" style="color: var(--color-text-light);">
                                <strong>Aeropuerto:</strong> Aeropuerto Inca Manco Cápac (Juliaca), a 45 min de Puno.
                            </p>
                            <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">
                                <strong>Bus:</strong> Desde Lima, Cusco o Arequipa.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dónde Hospedarse -->
                <div class="card p-8 border-l-4 hover:shadow-xl transition-shadow" style="border-left-color: var(--color-accent); background: linear-gradient(135deg, #fffbf0 0%, #ffffff 100%);">
                    <div class="flex gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--color-accent);">
                            <i data-lucide="hotel" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2" style="color: var(--color-text);">Dónde Hospedarse</h3>
                            <p class="text-sm leading-relaxed mb-3" style="color: var(--color-text-light);">
                                Reserva con anticipación. Durante la Candelaria, los hoteles se llenan rápidamente.
                            </p>
                            <a href="/locales" class="text-sm font-bold inline-flex items-center gap-2" style="color: var(--color-accent);">
                                <span>Ver Hoteles Disponibles</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recomendaciones -->
                <div class="card p-8 border-l-4 hover:shadow-xl transition-shadow" style="border-left-color: var(--color-info-dark); background: linear-gradient(135deg, #f0f9fb 0%, #ffffff 100%);">
                    <div class="flex gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background: var(--color-info-dark);">
                            <i data-lucide="lightbulb" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold mb-2" style="color: var(--color-text);">Recomendaciones</h3>
                            <ul class="text-sm space-y-1" style="color: var(--color-text-light);">
                                <li>• Lleva ropa abrigada (noches frías)</li>
                                <li>• Protector solar y sombrero</li>
                                <li>• Hidratación constante</li>
                                <li>• Cámara con batería extra</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Final -->
            <div class="card border-0 p-10 text-center" style="background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);">
                <h3 class="text-2xl md:text-3xl font-black text-white mb-4">
                    ¿Listo para Vivir la Candelaria?
                </h3>
                <p class="text-white/95 text-lg mb-8 max-w-2xl mx-auto">
                    Explora nuestros tours y experiencias especiales para la festividad más grande del Perú.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="/experiencias" class="btn btn-secondary font-bold px-8 py-4 shadow-2xl transform hover:scale-105 transition-all">
                        <i data-lucide="compass" class="w-5 h-5"></i>
                        Ver Tours Disponibles
                    </a>
                    <a href="/promociones" class="btn bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-slate-900 font-black px-8 py-4 shadow-2xl transform hover:scale-105 transition-all">
                        <i data-lucide="gift" class="w-5 h-5"></i>
                        Ver Promociones
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Carousel para el hero
            const slides = document.querySelectorAll('#candelaria-carousel .carousel-slide');
            let currentSlide = 0;

            function nextSlide() {
                slides[currentSlide].style.opacity = '0';
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].style.opacity = '1';
            }

            setInterval(nextSlide, 5000);

            // Toggle Timeline
            const toggleBtn = document.getElementById('toggleTimeline');
            const timelineContent = document.getElementById('timelineContent');

            if (toggleBtn && timelineContent) {
                toggleBtn.addEventListener('click', () => {
                    timelineContent.classList.toggle('hidden');
                    toggleBtn.textContent = timelineContent.classList.contains('hidden')
                        ? 'Ver Línea de Tiempo Histórica'
                        : 'Ocultar Línea de Tiempo';
                });
            }

            // Cargar Galería y Danzas
            loadGallery();
            loadDanzas();
        });

        async function loadGallery() {
            try {
                const response = await axios.get('/api/candelaria-gallery');
                const galleryGrid = document.getElementById('galleryGrid');
                const yearFilters = document.getElementById('yearFilters');

                console.log('Gallery Response:', response.data);

                if (response.data.success && response.data.data && response.data.data.length > 0) {
                    const photos = response.data.data;

                    // Extraer años únicos
                    const years = [...new Set(photos.map(photo => photo.year))].sort((a, b) => b - a);

                    // Crear filtros de año
                    years.forEach(year => {
                        const btn = document.createElement('button');
                        btn.className = 'year-filter px-4 py-2 rounded bg-slate-200 text-slate-700 text-sm font-bold hover:bg-slate-300 transition-colors';
                        btn.dataset.year = year;
                        btn.textContent = year;
                        btn.addEventListener('click', () => filterGallery(year));
                        yearFilters.appendChild(btn);
                    });

                    // Renderizar galería
                    renderGallery(photos);

                    // Event listeners para filtros
                    document.querySelectorAll('.year-filter').forEach(btn => {
                        btn.addEventListener('click', function() {
                            document.querySelectorAll('.year-filter').forEach(b => {
                                b.classList.remove('active', 'bg-slate-800', 'text-white');
                                b.classList.add('bg-slate-200', 'text-slate-700');
                            });
                            this.classList.add('active', 'bg-slate-800', 'text-white');
                            this.classList.remove('bg-slate-200', 'text-slate-700');

                            const year = this.dataset.year;
                            if (year === 'all') {
                                renderGallery(photos);
                            } else {
                                const filtered = photos.filter(p => p.year == year);
                                renderGallery(filtered);
                            }
                        });
                    });
                } else {
                    console.warn('No gallery photos found');
                    galleryGrid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400"><p>No hay imágenes en la galería aún.</p></div>';
                }
            } catch (error) {
                console.error('Error cargando galería:', error);
                const galleryGrid = document.getElementById('galleryGrid');
                galleryGrid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500"><p>Error al cargar la galería. Por favor recarga la página.</p></div>';
            }
        }

        function renderGallery(photos) {
            const galleryGrid = document.getElementById('galleryGrid');

            if (!photos || photos.length === 0) {
                galleryGrid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400"><p>No hay imágenes para este año.</p></div>';
                return;
            }

            console.log('Rendering', photos.length, 'photos');

            // Usar DOM API en lugar de innerHTML para prevenir XSS
            galleryGrid.innerHTML = '';

            photos.forEach(photo => {
                const cardDiv = document.createElement('div');
                cardDiv.className = 'card overflow-hidden hover:shadow-xl transition-all group border border-slate-200';

                const imageDiv = document.createElement('div');
                imageDiv.className = 'aspect-video overflow-hidden bg-slate-100';

                const img = document.createElement('img');
                img.src = photo.image_url || '';
                img.alt = photo.title || '';
                img.className = 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-500';
                img.onerror = function() {
                    this.src = '/images/placeholder.jpg';
                    console.error('Failed to load image:', photo.image_url);
                };

                imageDiv.appendChild(img);

                const contentDiv = document.createElement('div');
                contentDiv.className = 'p-4 bg-white';

                const yearBadge = document.createElement('span');
                yearBadge.className = 'inline-block bg-slate-800 text-white px-2 py-1 rounded text-xs font-bold mb-2';
                yearBadge.textContent = photo.year || '';

                const title = document.createElement('h4');
                title.className = 'font-bold text-sm mb-1';
                title.style.color = 'var(--color-text)';
                title.textContent = photo.title || '';

                contentDiv.appendChild(yearBadge);
                contentDiv.appendChild(title);

                if (photo.description) {
                    const desc = document.createElement('p');
                    desc.className = 'text-xs text-slate-500 line-clamp-2';
                    desc.textContent = photo.description;
                    contentDiv.appendChild(desc);
                }

                cardDiv.appendChild(imageDiv);
                cardDiv.appendChild(contentDiv);
                galleryGrid.appendChild(cardDiv);
            });
        }

        function filterGallery(year) {
            // Implementado en los event listeners arriba
        }

        async function loadDanzas() {
            try {
                const response = await axios.get('/api/candelaria-danzas');
                const danzasGrid = document.getElementById('danzasGrid');

                console.log('Danzas Response:', response.data);

                if (response.data.success && response.data.data && response.data.data.length > 0) {
                    const danzas = response.data.data;

                    console.log('Rendering', danzas.length, 'danzas');

                    // Usar DOM API en lugar de innerHTML
                    danzasGrid.innerHTML = '';

                    danzas.forEach(danza => {
                        const article = document.createElement('article');
                        article.className = 'card overflow-hidden hover:shadow-lg transition-all bg-white border border-slate-200';

                        // Imagen o inicial
                        const imageDiv = document.createElement('div');
                        if (danza.image_url) {
                            imageDiv.className = 'aspect-video overflow-hidden bg-slate-100';
                            const img = document.createElement('img');
                            img.src = danza.image_url;
                            img.alt = danza.name || '';
                            img.className = 'w-full h-full object-cover hover:scale-105 transition-transform duration-300';
                            img.onerror = function() {
                                this.parentElement.innerHTML = '<div class="w-full h-full flex items-center justify-center"><span class="text-4xl font-bold text-slate-300">' + (danza.name ? danza.name.charAt(0) : '?') + '</span></div>';
                            };
                            imageDiv.appendChild(img);
                        } else {
                            imageDiv.className = 'aspect-video overflow-hidden bg-slate-100 flex items-center justify-center';
                            const initial = document.createElement('span');
                            initial.className = 'text-4xl font-bold text-slate-300';
                            initial.textContent = danza.name ? danza.name.charAt(0) : '?';
                            imageDiv.appendChild(initial);
                        }

                        // Contenido
                        const contentDiv = document.createElement('div');
                        contentDiv.className = 'p-6';

                        // Badges
                        const badgesDiv = document.createElement('div');
                        badgesDiv.className = 'mb-3';

                        const typeBadge = document.createElement('span');
                        typeBadge.className = 'inline-block bg-slate-800 text-white px-3 py-1 rounded text-xs font-bold';
                        typeBadge.textContent = danza.type === 'autoctona' ? 'Danza Autóctona' : 'Danza Mestiza';
                        badgesDiv.appendChild(typeBadge);

                        if (danza.region) {
                            const regionBadge = document.createElement('span');
                            regionBadge.className = 'inline-block bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs font-semibold ml-2';
                            regionBadge.textContent = danza.region;
                            badgesDiv.appendChild(regionBadge);
                        }

                        // Título
                        const title = document.createElement('h3');
                        title.className = 'text-xl font-bold mb-3';
                        title.style.color = 'var(--color-text)';
                        title.textContent = danza.name || '';

                        // Descripción
                        const desc = document.createElement('p');
                        desc.className = 'text-sm leading-relaxed mb-4 line-clamp-3';
                        desc.style.color = 'var(--color-text-light)';
                        desc.textContent = danza.description || '';

                        contentDiv.appendChild(badgesDiv);
                        contentDiv.appendChild(title);
                        contentDiv.appendChild(desc);

                        // Características (opcional)
                        if (danza.characteristics) {
                            const charDiv = document.createElement('div');
                            charDiv.className = 'pt-3 border-t border-slate-100';
                            const charP = document.createElement('p');
                            charP.className = 'text-xs text-slate-500 line-clamp-2';
                            charP.textContent = danza.characteristics;
                            charDiv.appendChild(charP);
                            contentDiv.appendChild(charDiv);
                        }

                        article.appendChild(imageDiv);
                        article.appendChild(contentDiv);
                        danzasGrid.appendChild(article);
                    });
                } else {
                    console.warn('No danzas found');
                    danzasGrid.innerHTML = '<div class="col-span-full text-center py-12 text-slate-400"><p>No hay danzas registradas aún.</p></div>';
                }
            } catch (error) {
                console.error('Error cargando danzas:', error);
                document.getElementById('danzasGrid').innerHTML = '<div class="col-span-full text-center py-12 text-red-500"><p>Error al cargar danzas. Por favor recarga la página.</p></div>';
            }
        }
    </script>
@endpush

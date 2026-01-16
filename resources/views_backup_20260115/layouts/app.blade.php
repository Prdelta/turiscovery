<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Turiscovery - Descubre Puno')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans text-gray-800 bg-gray-50 flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav x-data="{ mobileMenuOpen: false, scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 20)"
        :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-md py-2' : 'bg-transparent py-4'"
        class="fixed top-0 left-0 w-full z-50 transition-all duration-300">
        <div class="container mx-auto px-4 md:px-6">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 group">
                    <span
                        class="text-3xl filter drop-shadow-sm group-hover:scale-110 transition-transform duration-300">🏔️</span>
                    <span class="font-heading font-bold text-2xl tracking-tight"
                        :class="scrolled ? 'text-gray-900' : 'text-white'">
                        Turiscovery
                    </span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-8">
                    <ul class="flex items-center gap-6 font-medium">
                        <li><a href="/candelaria" class="nav-link hover:text-primary transition-colors"
                                :class="scrolled ? 'text-gray-600' : 'text-white/90 hover:text-white'">Candelaria</a>
                        </li>
                        <li><a href="/experiencias" class="nav-link hover:text-primary transition-colors"
                                :class="scrolled ? 'text-gray-600' : 'text-white/90 hover:text-white'">Experiencias</a>
                        </li>
                        <li><a href="/eventos" class="nav-link hover:text-primary transition-colors"
                                :class="scrolled ? 'text-gray-600' : 'text-white/90 hover:text-white'">Eventos</a></li>
                        <li><a href="/promociones" class="nav-link hover:text-primary transition-colors"
                                :class="scrolled ? 'text-gray-600' : 'text-white/90 hover:text-white'">Promociones</a>
                        </li>
                        <li><a href="/locales" class="nav-link hover:text-primary transition-colors"
                                :class="scrolled ? 'text-gray-600' : 'text-white/90 hover:text-white'">Locales</a></li>
                    </ul>

                    <div class="flex items-center gap-3 pl-6 border-l"
                        :class="scrolled ? 'border-gray-200' : 'border-white/20'">
                        @auth
                            <a href="/dashboard"
                                class="px-5 py-2 rounded-full font-semibold transition-all transform hover:-translate-y-0.5"
                                :class="scrolled ? 'bg-primary text-white shadow-lg shadow-primary/30 hover:bg-primary-dark' :
                                    'bg-white text-primary hover:bg-gray-100'">
                                Dashboard
                            </a>
                        @else
                            <a href="/login" class="font-medium hover:underline transition-all"
                                :class="scrolled ? 'text-gray-600' : 'text-white'">
                                Ingresar
                            </a>
                            <a href="/register"
                                class="px-5 py-2 rounded-full font-semibold transition-all transform hover:-translate-y-0.5"
                                :class="scrolled ? 'bg-primary text-white shadow-lg shadow-primary/30 hover:bg-primary-dark' :
                                    'bg-white text-primary hover:bg-gray-100'">
                                Registrarse
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-2xl focus:outline-none"
                    :class="scrolled ? 'text-gray-800' : 'text-white'">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" x-transition
            class="fixed inset-0 z-50 bg-gray-900/95 backdrop-blur-sm lg:hidden flex flex-col items-center justify-center space-y-8"
            style="display: none;">
            <button @click="mobileMenuOpen = false" class="absolute top-6 right-6 text-white text-3xl">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <a href="/" class="text-3xl font-bold text-white mb-8">Turiscovery</a>

            <nav class="flex flex-col items-center gap-6 text-xl text-white/90">
                <a href="/candelaria" class="hover:text-primary transition-colors">Candelaria</a>
                <a href="/experiencias" class="hover:text-primary transition-colors">Experiencias</a>
                <a href="/eventos" class="hover:text-primary transition-colors">Eventos</a>
                <a href="/promociones" class="hover:text-primary transition-colors">Promociones</a>
                <a href="/locales" class="hover:text-primary transition-colors">Locales</a>
            </nav>

            <div class="flex flex-col items-center gap-4 mt-8 w-full px-10">
                @auth
                    <a href="/dashboard"
                        class="w-full text-center py-3 bg-primary rounded-xl text-white font-bold shadow-lg">Ir al
                        Dashboard</a>
                    <form action="/logout" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full text-center py-3 border border-white/30 rounded-xl text-white font-medium hover:bg-white/10">Cerrar
                            Sesión</button>
                    </form>
                @else
                    <a href="/login"
                        class="w-full text-center py-3 border border-white/30 rounded-xl text-white font-medium hover:bg-white/10">Iniciar
                        Sesión</a>
                    <a href="/register"
                        class="w-full text-center py-3 bg-white text-primary rounded-xl font-bold">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-0">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                <!-- Brand -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-3xl">🏔️</span>
                        <span class="font-heading font-bold text-2xl">Turiscovery</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Tu guía definitiva para descubrir la magia, cultura y tradiciones de Puno, Perú. La capital del
                        folklore te espera.
                    </p>
                    <div class="flex gap-4 pt-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-all transform hover:-translate-y-1">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-pink-600 hover:text-white transition-all transform hover:-translate-y-1">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-blue-400 hover:text-white transition-all transform hover:-translate-y-1">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                    </div>
                </div>

                <!-- Links -->
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-l-4 border-primary pl-3">Descubre</h4>
                    <ul class="space-y-3">
                        <li><a href="/candelaria"
                                class="text-gray-400 hover:text-primary transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-chevron-right text-xs"></i> Candelaria</a></li>
                        <li><a href="/experiencias"
                                class="text-gray-400 hover:text-primary transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-chevron-right text-xs"></i> Experiencias</a></li>
                        <li><a href="/eventos"
                                class="text-gray-400 hover:text-primary transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-chevron-right text-xs"></i> Eventos</a></li>
                        <li><a href="/locales"
                                class="text-gray-400 hover:text-primary transition-colors flex items-center gap-2"><i
                                    class="fa-solid fa-chevron-right text-xs"></i> Guía de Locales</a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-l-4 border-secondary pl-3">Contacto</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-gray-400">
                            <i class="fa-solid fa-location-dot mt-1 text-primary"></i>
                            <span>Jr. Lima 123, Puno, Perú</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fa-solid fa-phone text-primary"></i>
                            <span>+51 951 123 456</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-400">
                            <i class="fa-solid fa-envelope text-primary"></i>
                            <span>hola@turiscovery.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h4 class="font-bold text-lg mb-6 text-white border-l-4 border-accent pl-3">Boletín</h4>
                    <p class="text-gray-400 text-sm mb-4">Suscríbete para recibir las mejores ofertas y novedades.</p>
                    <form class="space-y-3">
                        <input type="email" placeholder="Tu correo electrónico"
                            class="w-full px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-colors">
                        <button type="submit"
                            class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2.5 rounded-lg transition-colors shadow-lg shadow-primary/20">
                            Suscribirse
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} Turiscovery. Todos los derechos reservados.</p>
                <div class="flex gap-6 text-sm text-gray-500">
                    <a href="#" class="hover:text-white transition-colors">Privacidad</a>
                    <a href="#" class="hover:text-white transition-colors">Términos</a>
                    <a href="#" class="hover:text-white transition-colors">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>

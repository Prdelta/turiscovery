@extends('layouts.app')

@section('title', 'Registrarse - Turiscovery')

@section('content')
    <div class="container" style="max-width: 500px; margin-top: 4rem; margin-bottom: 4rem;">
        <div class="card" style="padding: 2rem;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Crear Cuenta</h2>

            <div id="error-message"
                style="display: none; background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            </div>

            <form id="register-form">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nombre Completo</label>
                    <input type="text" id="name" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                    <input type="email" id="email" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contraseña</label>
                    <input type="password" id="password" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Mínimo 8 caracteres,
                        incluir mayúsculas, minúsculas, números y símbolos</p>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tipo de Cuenta</label>
                    <select id="role"
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        <option value="tourist">🧳 Turista</option>
                        <option value="socio">🏢 Socio / Empresa</option>
                    </select>
                    <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Los socios pueden
                        publicar locales, eventos y promociones</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">
                    Crear Cuenta
                </button>
            </form>

            <div style="text-align: center; margin: 1.5rem 0;">
                <p style="color: var(--text-secondary);">o regístrate con</p>
            </div>

            <a href="/api/auth/google" class="btn"
                style="width: 100%; background: white; color: var(--text-primary); border: 2px solid #e5e7eb; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                    <path fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                    <path fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                    <path fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                </svg>
                Continuar con Google
            </a>

            <div style="text-align: center; margin-top: 2rem;">
                <p style="color: var(--text-secondary);">¿Ya tienes cuenta? <a href="/login"
                        style="color: var(--primary); font-weight: 600;">Inicia sesión</a></p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            const role = document.getElementById('role').value;
            const errorDiv = document.getElementById('error-message');

            try {
                const response = await api.register({
                    name,
                    email,
                    password,
                    password_confirmation,
                    role
                });

                if (response.success) {
                    showNotification('¡Cuenta creada exitosamente!', 'success');

                    // Redirect based on role
                    if (role === 'socio') {
                        window.location.href = '/dashboard';
                    } else {
                        window.location.href = '/';
                    }
                }
            } catch (error) {
                errorDiv.style.display = 'block';

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else {
                    errorDiv.textContent = 'Error al crear la cuenta. Por favor, intenta nuevamente.';
                }
            }
        });
    </script>
@endpush

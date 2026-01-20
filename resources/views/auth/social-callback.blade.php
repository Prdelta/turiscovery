<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando inicio de sesión...</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex items-center justify-center h-screen">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"
            style="border-color: #5E5CE8;"></div>
        <h2 class="text-xl font-semibold text-gray-700">Autenticando...</h2>
        <p class="text-gray-500">Por favor espere un momento.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const token = urlParams.get('token');
            const role = urlParams.get('role');
            const error = urlParams.get('error');

            if (error) {
                alert('Error de autenticación: ' + error);
                window.location.href = '/login';
                return;
            }

            if (token) {
                // Store auth data
                localStorage.setItem('auth_token', token);
                if (role) {
                    localStorage.setItem('user_role', role);
                }

                // Redirect based on role
                if (role === 'admin' || role === 'socio') {
                    window.location.href = '/dashboard';
                } else {
                    window.location.href = '/';
                }
            } else {
                window.location.href = '/login';
            }
        });
    </script>
</body>

</html>

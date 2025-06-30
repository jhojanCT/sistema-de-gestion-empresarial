<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FIMI') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

        <style>
            :root {
                --primary-color: #3498db;
                --secondary-color: #2c3e50;
                --success-color: #2ecc71;
                --danger-color: #e74c3c;
                --warning-color: #f1c40f;
                --info-color: #1abc9c;
                --light-color: #ecf0f1;
                --dark-color: #2c3e50;
            }

            body {
                font-family: 'Inter', sans-serif;
                min-height: 100vh;
                display: flex;
                align-items: center;
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                color: white;
            }

            .auth-container {
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
                padding: 2rem;
            }

            .auth-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .auth-header {
                text-align: center;
                margin-bottom: 2rem;
            }

            .auth-logo {
                width: 80px;
                height: 80px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1rem;
            }

            .auth-logo i {
                font-size: 2.5rem;
                color: var(--primary-color);
            }

            .auth-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            .auth-subtitle {
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.9rem;
            }

            .form-control {
                background: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 10px;
                color: white;
                padding: 0.75rem 1rem;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                background: rgba(255, 255, 255, 0.15);
                border-color: var(--primary-color);
                box-shadow: none;
                color: white;
            }

            .form-control::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            .btn-primary {
                background: var(--primary-color);
                border: none;
                border-radius: 10px;
                padding: 0.75rem 1.5rem;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                background: #2980b9;
                transform: translateY(-2px);
            }

            .auth-footer {
                text-align: center;
                margin-top: 2rem;
                color: rgba(255, 255, 255, 0.7);
                font-size: 0.9rem;
            }

            .auth-footer a {
                color: var(--primary-color);
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .auth-footer a:hover {
                color: #2980b9;
            }

            .alert {
                border-radius: 10px;
                border: none;
                background: rgba(255, 255, 255, 0.1);
                color: white;
            }

            .alert-success {
                background: rgba(46, 204, 113, 0.2);
                border-left: 4px solid var(--success-color);
            }

            .alert-danger {
                background: rgba(231, 76, 60, 0.2);
                border-left: 4px solid var(--danger-color);
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo">
                        <i class="fas fa-wine-bottle"></i>
                    </div>
                    <h1 class="auth-title">{{ config('app.name', 'FIMI') }}</h1>
                    <p class="auth-subtitle">Sistema de Gestión</p>
                </div>

                @yield('content')

                <div class="auth-footer">
                    @yield('footer')
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>

        <script>
            $(document).ready(function(){
                // Inicializar tooltips
                $('[data-toggle="tooltip"]').tooltip();
                
                // Mostrar mensajes de sesión con SweetAlert2
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: '{{ session('error') }}',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                @endif
            });
        </script>
    </body>
</html>

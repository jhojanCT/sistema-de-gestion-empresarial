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
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        @stack('styles')

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        @stack('scripts')

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
                background-color: #f8f9fa;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .wrapper {
                display: flex;
                min-height: 100vh;
            }

            .main-content {
                flex: 1;
                margin-left: 16rem; /* 256px - w-64 */
                padding: 20px;
                transition: margin-left 0.3s ease;
            }

            .main-content.expanded {
                margin-left: 5rem; /* 80px - w-20 */
            }

            .card {
                border: none;
                border-radius: 10px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
            }

            .card:hover {
                transform: translateY(-5px);
            }

            .btn {
                border-radius: 8px;
                padding: 8px 16px;
                font-weight: 500;
                transition: all 0.3s ease;
            }

            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
            }

            .btn-primary:hover {
                background-color: #2980b9;
                border-color: #2980b9;
                transform: translateY(-2px);
            }

            .alert {
                border-radius: 10px;
                border: none;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .table {
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .table thead th {
                background-color: var(--secondary-color);
                color: white;
                border: none;
                font-weight: 600;
            }

            .table tbody tr:hover {
                background-color: rgba(52, 152, 219, 0.1);
            }

            .form-control {
                border-radius: 8px;
                border: 1px solid #ddd;
                padding: 10px 15px;
            }

            .form-control:focus {
                border-color: var(--primary-color);
                box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            }

            .badge {
                padding: 6px 12px;
                border-radius: 20px;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .main-content {
                    margin-left: 0;
                }
                .main-content.expanded {
                    margin-left: 0;
                }
            }

            @media print {
                body {
                    padding: 0 !important;
                    margin: 0 !important;
                    background-color: white !important;
                }
                
                .wrapper {
                    display: block !important;
                }
                
                .main-content {
                    margin: 0 !important;
                    padding: 0 !important;
                }
                
                nav, 
                .sidebar,
                .d-print-none,
                .no-print {
                    display: none !important;
                }
                
                .container-fluid {
                    width: 100% !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }
                
                .card {
                    border: none !important;
                    box-shadow: none !important;
                    margin: 0 !important;
                }
                
                .card-body {
                    padding: 0 !important;
                }
                
                .table {
                    width: 100% !important;
                    margin: 0 !important;
                    border: 1px solid #ddd !important;
                }
                
                .table td, 
                .table th {
                    padding: 0.5rem !important;
                }
                
                @page {
                    size: auto;
                    margin: 1cm;
                }
                
                * {
                    -webkit-print-color-adjust: exact !important;
                    color-adjust: exact !important;
                    print-color-adjust: exact !important;
                }
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <nav class="d-print-none">
                @include('layouts.navigation')
            </nav>
            
            <main class="main-content" id="main-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </main>
        </div>

        <script>
            // Inicializar tooltips
            $(document).ready(function(){
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

                // Ajustar el contenido principal cuando el sidebar cambia
                const mainContent = document.getElementById('main-content');
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    mainContent.classList.add('expanded');
                }

                // Escuchar cambios en el sidebar
                window.addEventListener('storage', function(e) {
                    if (e.key === 'sidebarCollapsed') {
                        if (e.newValue === 'true') {
                            mainContent.classList.add('expanded');
                        } else {
                            mainContent.classList.remove('expanded');
                        }
                    }
                });
            });
        </script>
    </body>
</html>
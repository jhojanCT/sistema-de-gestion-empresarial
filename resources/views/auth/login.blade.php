<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIMI - Iniciar Sesión</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    <style>
        :root {
            --bs-primary: #0061F5;
            --bs-primary-rgb: 0, 97, 245;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            margin: 2rem;
        }

        .card {
            border: none;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border-radius: 16px;
        }

        .logo-container {
            width: 85px;
            height: 85px;
            background: linear-gradient(145deg, var(--bs-primary) 0%, #0043ac 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            box-shadow: 0 8px 24px rgba(0, 97, 245, 0.2);
            transition: all 0.3s ease;
        }

        .logo-container:hover {
            transform: translateY(-5px) scale(1.02);
        }

        .form-control {
            border: 2px solid #e9ecef;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            border-radius: 12px;
            transition: all 0.2s ease;
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: var(--bs-primary);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), 0.1);
        }

        .form-floating > label {
            padding: 0.8rem 1rem;
            color: #6c757d;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.85) translateY(-1rem) translateX(0.15rem);
            color: var(--bs-primary);
        }

        .btn-primary {
            background: linear-gradient(145deg, var(--bs-primary) 0%, #0043ac 100%);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 97, 245, 0.25);
            transition: all 0.3s ease;
            border-radius: 12px;
            padding: 0.8rem;
            font-weight: 600;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 97, 245, 0.35);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-check-input {
            border-radius: 6px;
            border: 2px solid #dee2e6;
        }

        .form-check-input:checked {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .form-check-label {
            font-size: 0.95rem;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-left: 0.5rem;
        }

        .alert {
            border: none;
            background: rgba(25, 135, 84, 0.1);
            border-radius: 12px;
            border-left: 4px solid #198754;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.6s ease-out forwards;
        }

        .app-title {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            background: linear-gradient(145deg, #1a1a1a, #404040);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .app-subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 1rem;
            }
            
            .card-body {
                padding: 2rem !important;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card">
        <div class="card-body p-4 p-md-5">
            <!-- Logo y título -->
            <div class="text-center mb-4">
                <div class="logo-container mb-4">
                    <i class="fas fa-industry text-white" style="font-size: 2.5rem;"></i>
                </div>
                <h1 class="app-title">FIMI</h1>
                <p class="app-subtitle">Sistema de Gestión Industrial</p>
            </div>

            <!-- Alerta de estado -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <div class="form-floating">
                        <input type="email" 
                               class="form-control form-control-lg @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               placeholder="nombre@ejemplo.com"
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        <label for="email">
                            <i class="fas fa-envelope me-2 text-secondary"></i>
                            Correo Electrónico
                        </label>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="mb-4">
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña"
                               required>
                        <label for="password">
                            <i class="fas fa-lock me-2 text-secondary"></i>
                            Contraseña
                        </label>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Recordar sesión -->
                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember">
                        <label class="form-check-label text-secondary" for="remember">
                            Recordar sesión
                        </label>
                    </div>
                </div>

                <!-- Botón de inicio de sesión -->
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Iniciar Sesión
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="text-center mt-4">
                <small class="text-secondary">© {{ date('Y') }} FIMI · Todos los derechos reservados</small>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Validación del formulario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
});
</script>

</body>
</html>
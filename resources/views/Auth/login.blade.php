<!DOCTYPE html>
<html lang="es" class="login-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Gestión</title>
    
    <!-- Font Awesome para iconos - versión más reciente y estable -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Bootstrap solo para funcionalidades específicas (modales, toasts) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- CSS personalizado para login -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    
    <!-- Estilos adicionales para corregir compatibilidad -->
    <style>
        /* Correcciones para el toggle de contraseña */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--login-text-light);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.2s;
            z-index: 2;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle:hover {
            color: var(--login-text-color);
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .login-input-icon {
            position: relative;
        }
        
        .login-input-icon input[type="password"],
        .login-input-icon input[type="text"] {
            padding-right: 48px;
        }
        
        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .login-btn:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
            background-color: var(--login-primary-color) !important;
        }
        
        .login-form-control:focus {
            border-color: var(--login-primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .login-form-control.is-invalid {
            border-color: var(--login-danger-color);
        }
        
        .login-form-control.is-invalid:focus {
            border-color: var(--login-danger-color);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        
        .login-error {
            color: var(--login-danger-color);
            font-size: 0.875rem;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .login-alert {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Mejorar la accesibilidad */
        .login-form-control:focus-visible {
            outline: 2px solid var(--login-primary-color);
            outline-offset: 2px;
        }
        
        .password-toggle:focus-visible {
            outline: 2px solid var(--login-primary-color);
            outline-offset: 2px;
        }
        
        /* Responsive improvements */
        @media (max-width: 480px) {
            .login-card {
                padding: 24px 20px;
                margin: 10px;
            }
            
            .login-logo i {
                font-size: 3rem;
            }
            
            .login-logo h1 {
                font-size: 1.8rem;
            }
            
            .login-form-control {
                font-size: 16px; /* Previene zoom en iOS */
            }
        }
        
        /* Loading state improvements */
        .login-btn .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-logo">
            <i class="fas fa-building"></i>
            <h1>ServiMuni</h1>
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <h2>Iniciar Sesión</h2>
                <p>Ingresa tus credenciales para acceder</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                @error('email')
                    <div class="login-alert login-alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        <span>{{ $message }}</span>
                    </div>
                @enderror

                @error('password')
                    <div class="login-alert login-alert-danger">
                        <i class="fas fa-exclamation-circle"></i> 
                        <span>{{ $message }}</span>
                    </div>
                @enderror
                
                <div class="login-form-group">
                    <label for="email" class="login-form-label">Correo Electrónico</label>
                    <div class="login-input-icon">
                        <i class="fas fa-envelope"></i>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               class="login-form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               placeholder="Ingresa tu correo">
                    </div>
                    @error('email')
                        <div class="login-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="login-form-group">
                    <label for="password" class="login-form-label">Contraseña</label>
                    <div class="login-input-icon">
                        <i class="fas fa-lock"></i>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               class="login-form-control @error('password') is-invalid @enderror" 
                               required 
                               placeholder="Ingresa tu contraseña">
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="login-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div class="login-form-group">
                    <button type="submit" class="login-btn" id="submitBtn">
                        <span id="btnContent">
                            <i class="fas fa-sign-in-alt"></i>
                            <span id="btnText">Iniciar Sesión</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript para funcionalidades interactivas -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Referencias a elementos
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnContent = document.getElementById('btnContent');
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            const emailField = document.getElementById('email');

            // Toggle para mostrar/ocultar contraseña
            if (togglePassword && passwordField && toggleIcon) {
                togglePassword.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isPassword = passwordField.type === 'password';
                    passwordField.type = isPassword ? 'text' : 'password';
                    toggleIcon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
                });
            }

            // Validación básica
            function validateForm() {
                const email = emailField.value.trim();
                const password = passwordField.value.trim();
                
                const isEmailValid = email.includes('@') && email.length >= 5;
                const isPasswordValid = password.length >= 3;
                
                submitBtn.disabled = !(isEmailValid && isPasswordValid);
                
                return isEmailValid && isPasswordValid;
            }

            // Validar mientras se escribe
            if (emailField) emailField.addEventListener('input', validateForm);
            if (passwordField) passwordField.addEventListener('input', validateForm);

            // Manejar envío del formulario
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                        return;
                    }

                    // Mostrar estado de carga
                    submitBtn.disabled = true;
                    btnContent.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Iniciando sesión...</span>';
                    
                    // Restaurar el botón después de un tiempo si hay errores
                    setTimeout(() => {
                        if (document.querySelector('.login-alert-danger')) {
                            submitBtn.disabled = false;
                            btnContent.innerHTML = '<i class="fas fa-sign-in-alt"></i> <span>Iniciar Sesión</span>';
                        }
                    }, 3000);
                });
            }

            // Animación de entrada para la card
            const loginCard = document.querySelector('.login-card');
            if (loginCard) {
                loginCard.style.opacity = '0';
                loginCard.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    loginCard.style.transition = 'all 0.5s ease';
                    loginCard.style.opacity = '1';
                    loginCard.style.transform = 'translateY(0)';
                }, 100);
            }

            // Atajos de teclado
            document.addEventListener('keydown', function(e) {
                // Enter para enviar formulario desde cualquier campo
                if (e.key === 'Enter' && (e.target === emailField || e.target === passwordField)) {
                    e.preventDefault();
                    if (validateForm()) {
                        loginForm.submit();
                    }
                }
            });

            // Validación inicial
            validateForm();

            // Enfocar el campo de email al cargar
            if (emailField) {
                emailField.focus();
            }

            // Limpiar mensajes de error después de 5 segundos
            const errorAlerts = document.querySelectorAll('.login-alert-danger');
            if (errorAlerts.length > 0) {
                setTimeout(() => {
                    errorAlerts.forEach(alert => {
                        alert.style.transition = 'opacity 0.5s ease';
                        alert.style.opacity = '0';
                        setTimeout(() => {
                            alert.remove();
                        }, 500);
                    });
                }, 5000);
            }
        });

        // Función para manejar errores de conexión
        window.addEventListener('online', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                document.getElementById('btnContent').innerHTML = '<i class="fas fa-sign-in-alt"></i> <span>Iniciar Sesión</span>';
            }
        });

        window.addEventListener('offline', function() {
            const alerts = document.querySelectorAll('.login-alert-danger');
            if (alerts.length === 0) {
                const form = document.getElementById('loginForm');
                const alert = document.createElement('div');
                alert.className = 'login-alert login-alert-danger';
                alert.innerHTML = '<i class="fas fa-wifi"></i> <span>Sin conexión a internet. Verifica tu conexión.</span>';
                form.insertBefore(alert, form.firstChild);
            }
        });
    </script>
</body>
</html>
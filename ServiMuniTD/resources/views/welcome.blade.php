<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Gestión Municipal</title>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Sistema de Gestión Municipal</h1>
                <p class="hero-subtitle">Una plataforma completa para la gestión eficiente de los recursos y servicios municipales, diseñada para mejorar la experiencia tanto de funcionarios como de ciudadanos.</p>
                <div>
                    <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>
                    <a href="#features" class="btn btn-outline">Conocer Más</a>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features-section">
        <div class="container">
            <h2 class="section-title">Características Principales</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="fas fa-sitemap feature-icon"></i>
                    <h3 class="feature-title">Gestión de Departamentos</h3>
                    <p class="feature-text">Administra de manera eficiente la estructura organizacional del municipio, creando y asignando departamentos según las necesidades.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-users feature-icon"></i>
                    <h3 class="feature-title">Administración de Personal</h3>
                    <p class="feature-text">Controla la información de los funcionarios, sus roles y permisos dentro del sistema de forma segura y organizada.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-tasks feature-icon"></i>
                    <h3 class="feature-title">Seguimiento de Proyectos</h3>
                    <p class="feature-text">Monitorea el avance de los proyectos municipales, asigna tareas y establece plazos para una gestión eficaz.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-file-alt feature-icon"></i>
                    <h3 class="feature-title">Gestión Documental</h3>
                    <p class="feature-text">Almacena y organiza documentos importantes, facilitando su búsqueda y consulta cuando sea necesario.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-chart-line feature-icon"></i>
                    <h3 class="feature-title">Reportes y Estadísticas</h3>
                    <p class="feature-text">Genera informes detallados sobre el desempeño de diferentes áreas para la toma de decisiones estratégicas.</p>
                </div>
                
                <div class="feature-card">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3 class="feature-title">Seguridad Avanzada</h3>
                    <p class="feature-text">Protege la información sensible con múltiples niveles de seguridad y control de acceso basado en roles.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">¿Listo para comenzar?</h2>
            <p class="cta-text">Inicia sesión en el sistema para acceder a todas las funcionalidades y aprovechar al máximo las herramientas que tenemos para ti.</p>
            <a href="{{ route('login') }}" class="btn" style="background-color: var(--primary-color); color: white;">Iniciar Sesión Ahora</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <i class="fas fa-building"></i>
                        <span class="footer-logo-text">ServiMuni</span>
                    </div>
                    <p class="footer-text">Una solución integral para la gestión municipal, diseñada para optimizar procesos internos y mejorar la atención al ciudadano.</p>
                </div>
                
                <div class="footer-links">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                        <li><a href="#features">Características</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h3>Contacto</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-envelope"></i> info@servimuni.cl</a></li>
                        <li><a href="#"><i class="fas fa-phone"></i> +56 9 1234 5678</a></li>
                        <li><a href="#"><i class="fas fa-map-marker-alt"></i> Valparaíso, Chile</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                &copy; 2025 ServiMuni. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>
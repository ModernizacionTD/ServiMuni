<!DOCTYPE html>
<html lang="es" class="welcome-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Sistema de Gestión Municipal</title>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Bootstrap solo para funcionalidades específicas (modales, toasts) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="welcome-page">
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Sistema de Gestión Municipal</h1>
                <p class="hero-subtitle">Una plataforma completa para la gestión eficiente de los recursos y servicios municipales, diseñada para mejorar la experiencia tanto de funcionarios como de ciudadanos.</p>
                <div>
                    <a href="{{ route('login') }}" class="welcome-btn">Iniciar Sesión</a>
                    <a href="#features" class="welcome-btn welcome-btn-outline">Conocer Más</a>
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
            <a href="{{ route('login') }}" class="welcome-btn" style="background-color: var(--welcome-primary-color); color: white;">Iniciar Sesión Ahora</a>
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

    <!-- JavaScript adicional para interactividad -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling para enlaces internos
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Animación de entrada para las cards de características
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Aplicar observer a las feature cards
            document.querySelectorAll('.feature-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                observer.observe(card);
            });

            // Efecto parallax sutil en el hero
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                const parallax = document.querySelector('.hero-section');
                if (parallax) {
                    const speed = scrolled * 0.5;
                    parallax.style.transform = `translateY(${speed}px)`;
                }
            });

            // Animación de conteo para estadísticas (si se agregan en el futuro)
            function animateValue(element, start, end, duration) {
                let startTimestamp = null;
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    element.innerHTML = Math.floor(progress * (end - start) + start);
                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };
                window.requestAnimationFrame(step);
            }

            // Efecto hover mejorado para los botones
            document.querySelectorAll('.welcome-btn').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // Efecto de typing para el título (opcional)
            function typeWriter(element, text, speed = 100) {
                let i = 0;
                element.innerHTML = '';
                function type() {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        setTimeout(type, speed);
                    }
                }
                type();
            }

            // Activar efecto typing en el título principal (comentado por defecto)
            // const heroTitle = document.querySelector('.hero-title');
            // if (heroTitle) {
            //     const originalText = heroTitle.textContent;
            //     typeWriter(heroTitle, originalText, 80);
            // }

            // Mostrar/ocultar elementos en scroll
            window.addEventListener('scroll', function() {
                const scrolled = window.pageYOffset;
                
                // Fade in/out del header cuando se hace scroll
                const hero = document.querySelector('.hero-section');
                if (hero) {
                    const heroHeight = hero.offsetHeight;
                    const opacity = Math.max(0, 1 - (scrolled / heroHeight));
                    hero.style.opacity = opacity;
                }
            });

            // Prevenir flash de contenido sin estilo
            document.body.style.visibility = 'visible';
        });

        // Función para manejar errores de carga de imágenes (si se agregan)
        function handleImageError(img) {
            img.style.display = 'none';
            console.warn('Error loading image:', img.src);
        }

        // Función para lazy loading de contenido adicional
        function lazyLoad() {
            const lazyElements = document.querySelectorAll('[data-lazy]');
            
            if ('IntersectionObserver' in window) {
                const lazyObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const lazyElement = entry.target;
                            lazyElement.src = lazyElement.dataset.lazy;
                            lazyElement.classList.remove('lazy');
                            lazyObserver.unobserve(lazyElement);
                        }
                    });
                });

                lazyElements.forEach(function(lazyElement) {
                    lazyObserver.observe(lazyElement);
                });
            }
        }

        // Inicializar lazy loading si hay elementos
        if (document.querySelectorAll('[data-lazy]').length > 0) {
            lazyLoad();
        }
    </script>
</body>
</html>
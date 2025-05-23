<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Gestión')</title>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <!-- Bootstrap solo para funcionalidades específicas (modales, toasts) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<!-- Sidebar -->
<aside class="sidebar-app" id="sidebar-app">
    <div class="sidebar-header-app">
        <a href="{{ route('dashboard') }}" class="logo-app">
            <i class="fa-solid fa-building-columns"></i>
            <span>ServiMuni</span>
        </a>
    </div>

    <div class="sidebar-content-app">
        <div class="nav-section-app">
            <h5 class="nav-section-title-app">Navegación</h5>
            <ul class="nav-items-app">
                <li class="nav-item-app">
                    <a href="{{ route('dashboard') }}" class="nav-link-app {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <!-- Nuevo enlace para Ingresar Solicitudes -->
                <li class="nav-item-app">
                    <a href="{{ route('buscar.usuario') }}" class="nav-link-app {{ request()->routeIs('buscar.usuario') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Ingresar Solicitud</span>
                    </a>
                </li>
                
                @if(session('user_rol') == 'admin')
                <li class="nav-item-app">
                    <a href="{{ route('admin') }}" class="nav-link-app {{ request()->routeIs('admin') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Panel Admin</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- Agregar una nueva sección para Gestión -->
        <div class="nav-section-app">
            <h5 class="nav-section-title-app">Gestión</h5>
            <ul class="nav-items-app">
                <li class="nav-item-app">
                    <a href="{{ route('solicitudes.index') }}" class="nav-link-app {{ request()->routeIs('solicitudes.index') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Solicitudes</span>
                    </a>
                </li>
                
                <li class="nav-item-app">
                    <a href="{{ route('usuarios.index') }}" class="nav-link-app {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                
                @if(session('user_rol') == 'admin')
                <li class="nav-item-app">
                    <a href="{{ route('funcionarios.index') }}" class="nav-link-app {{ request()->routeIs('funcionarios.index') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Funcionarios</span>
                    </a>
                </li>
                
                <li class="nav-item-app">
                    <a href="{{ route('departamentos.index') }}" class="nav-link-app {{ request()->routeIs('departamentos.index') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Departamentos</span>
                    </a>
                </li>
                
                <li class="nav-item-app">
                    <a href="{{ route('requerimientos.index') }}" class="nav-link-app {{ request()->routeIs('requerimientos.index') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i>
                        <span>Requerimientos</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</aside>

<!-- Main Content -->
<div class="main-content-app">
    <header class="main-header-app">
        <div class="header-left-app">
            <button class="menu-toggle-app" id="menu-toggle-app">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="page-title-app">@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="header-right-app">
            <div class="user-dropdown-app" id="user-dropdown-app">
                <div class="user-info-app">
                    <div class="user-avatar-app">
                        @php
                            $nombre = session('user_nombre', 'Usuario');
                            $palabras = explode(' ', trim($nombre));
                            if (count($palabras) === 1) {
                                $iniciales = strlen($palabras[0]) >= 2 
                                    ? strtoupper(substr($palabras[0], 0, 2))
                                    : strtoupper($palabras[0][0] . $palabras[0][0]);
                            } else {
                                $iniciales = strtoupper($palabras[0][0] . $palabras[1][0]);
                            }
                        @endphp
                        {{ $iniciales }}
                    </div>
                    <div class="user-details-app">
                        <span class="user-name-app">{{ session('user_nombre') }}</span>
                        <span class="user-role-app">
                            <i class="fas fa-circle" style="font-size: 0.5rem; color: #10b981; margin-right: 4px;"></i>
                            {{ ucfirst(session('user_rol')) }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-toggle-app" id="dropdown-arrow-app"></i>
                </div>
                
                <div class="dropdown-menu-app" id="dropdown-menu-app">
                    <!-- Header del dropdown -->
                    <div class="dropdown-header-app">
                        <div class="dropdown-avatar-app">
                            {{ $iniciales }}
                        </div>
                        <div class="dropdown-user-info-app">
                            <div class="dropdown-user-name-app">{{ session('user_nombre') }}</div>
                            <div class="dropdown-user-email-app">{{ session('user_email', 'admin@servimuni.cl') }}</div>
                            <div class="dropdown-user-role-app">
                                <span class="role-badge-app role-{{ session('user_rol') }}-app">
                                    @if(session('user_rol') == 'admin')
                                        <i class="fas fa-crown"></i> Administrador
                                    @elseif(session('user_rol') == 'gestor')
                                        <i class="fas fa-user-cog"></i> Gestor
                                    @elseif(session('user_rol') == 'tecnico')
                                        <i class="fas fa-tools"></i> Técnico
                                    @elseif(session('user_rol') == 'orientador')
                                        <i class="fas fa-compass"></i> Orientador
                                    @else
                                        <i class="fas fa-user"></i> {{ ucfirst(session('user_rol')) }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Separador -->
                    <div class="dropdown-divider-app"></div>
                    
                    <!-- Opciones principales -->
                    <div class="dropdown-section-app">
                        <a href="{{ route('funcionarios.profile') }}" class="dropdown-item-app">
                            <div class="dropdown-item-icon-app">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="dropdown-item-content-app">
                                <span class="dropdown-item-title-app">Mi Perfil</span>
                                <span class="dropdown-item-subtitle-app">Ver y editar información personal</span>
                            </div>
                            <i class="fas fa-chevron-right dropdown-item-arrow-app"></i>
                        </a>
                        
                        <a href="{{ route('funcionarios.showChangePassword') }}" class="dropdown-item-app">
                            <div class="dropdown-item-icon-app">
                                <i class="fas fa-key"></i>
                            </div>
                            <div class="dropdown-item-content-app">
                                <span class="dropdown-item-title-app">Cambiar Contraseña</span>
                                <span class="dropdown-item-subtitle-app">Actualizar credenciales de acceso</span>
                            </div>
                            <i class="fas fa-chevron-right dropdown-item-arrow-app"></i>
                        </a>
                        
                        <div class="dropdown-item-app theme-toggle-app" id="theme-toggle-app">
                            <div class="dropdown-item-icon-app">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="dropdown-item-content-app">
                                <span class="dropdown-item-title-app">Tema</span>
                                <span class="dropdown-item-subtitle-app">Cambiar apariencia</span>
                            </div>
                            <div class="theme-switch-app">
                                <input type="checkbox" id="theme-checkbox-app" class="theme-checkbox-app">
                                <label for="theme-checkbox-app" class="theme-label-app">
                                    <span class="theme-sun-app"><i class="fas fa-sun"></i></span>
                                    <span class="theme-moon-app"><i class="fas fa-moon"></i></span>
                                    <span class="theme-slider-app"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Separador -->
                    <div class="dropdown-divider-app"></div>
                    
                    <!-- Configuración -->
                    <div class="dropdown-section-app">
                        <a href="{{ route('dashboard') }}" class="dropdown-item-app">
                            <div class="dropdown-item-icon-app">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="dropdown-item-content-app">
                                <span class="dropdown-item-title-app">Dashboard</span>
                                <span class="dropdown-item-subtitle-app">Volver al panel principal</span>
                            </div>
                            <i class="fas fa-chevron-right dropdown-item-arrow-app"></i>
                        </a>
                        
                        @if(session('user_rol') == 'admin')
                        <a href="{{ route('admin') }}" class="dropdown-item-app">
                            <div class="dropdown-item-icon-app">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="dropdown-item-content-app">
                                <span class="dropdown-item-title-app">Configuración</span>
                                <span class="dropdown-item-subtitle-app">Panel de administración</span>
                            </div>
                            <i class="fas fa-chevron-right dropdown-item-arrow-app"></i>
                        </a>
                        @endif
                    </div>
                    
                    <!-- Separador -->
                    <div class="dropdown-divider-app"></div>
                    
                    <!-- Cerrar sesión -->
                    <div class="dropdown-section-app">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form-app">
                            @csrf
                            <button type="submit" class="dropdown-item-app logout-item-app">
                                <div class="dropdown-item-icon-app">
                                    <i class="fas fa-sign-out-alt"></i>
                                </div>
                                <div class="dropdown-item-content-app">
                                    <span class="dropdown-item-title-app">Cerrar Sesión</span>
                                    <span class="dropdown-item-subtitle-app">Salir del sistema</span>
                                </div>
                                <i class="fas fa-chevron-right dropdown-item-arrow-app"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Footer del dropdown -->
                    <div class="dropdown-footer-app">
                        <div class="version-info-app">
                            <span>ServiMuni v2.0</span>
                            <span class="build-info-app">Build {{ date('Y.m.d') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="content-wrapper-app">
        @yield('content')
    </div>
</div>

<script>
// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - inicializando scripts de layout');
    
    // Obtener referencias a elementos importantes
    const menuToggle = document.getElementById('menu-toggle-app');
    const sidebar = document.getElementById('sidebar-app');
    const mainContent = document.querySelector('.main-content-app');
    
    // Función de ayuda para determinar si estamos en modo móvil
    function isMobile() {
        return window.innerWidth <= 991;
    }
    
    // Agregar evento click al botón de toggle (si existe)
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (isMobile()) {
                sidebar.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                if (mainContent) {
                    mainContent.classList.toggle('sidebar-collapsed-app');
                }
                
                // Guardar preferencia
                if (sidebar.classList.contains('collapsed')) {
                    localStorage.setItem('sidebarCollapsed', 'true');
                } else {
                    localStorage.setItem('sidebarCollapsed', 'false');
                }
            }
        });
    }
    
    // Cerrar el sidebar al hacer clic fuera en móvil
    document.addEventListener('click', function(event) {
        if (isMobile() && 
            sidebar && 
            menuToggle && 
            !sidebar.contains(event.target) && 
            !menuToggle.contains(event.target) && 
            sidebar.classList.contains('show')) {
            sidebar.classList.remove('show');
        }
    });
    
    // Manejar el dropdown de usuario
    const userDropdown = document.getElementById('user-dropdown-app');
    const dropdownMenu = document.getElementById('dropdown-menu-app');
    const dropdownArrow = document.getElementById('dropdown-arrow-app');
    
    if (userDropdown && dropdownMenu) {
        userDropdown.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isShowing = dropdownMenu.classList.contains('show');
            
            // Cerrar todos los dropdowns primero
            document.querySelectorAll('.dropdown-menu-app.show').forEach(menu => {
                menu.classList.remove('show');
            });
            
            // Rotar flecha y mostrar/ocultar menú
            if (!isShowing) {
                dropdownMenu.classList.add('show');
                if (dropdownArrow) {
                    dropdownArrow.style.transform = 'rotate(180deg)';
                }
            } else {
                if (dropdownArrow) {
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            }
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function(event) {
            if (!userDropdown.contains(event.target) && 
                dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
                if (dropdownArrow) {
                    dropdownArrow.style.transform = 'rotate(0deg)';
                }
            }
        });
    }
    
    // Restaurar estado del sidebar
    if (!isMobile() && sidebar && mainContent) {
        const savedState = localStorage.getItem('sidebarCollapsed');
        
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed-app');
        }
    }
    
    // Manejar redimensionamiento de ventana
    window.addEventListener('resize', function() {
        if (!isMobile() && sidebar) {
            sidebar.classList.remove('show');
        }
    });
    
    // Theme toggle functionality (si se implementa en el futuro)
    const themeToggle = document.getElementById('theme-checkbox-app');
    if (themeToggle) {
        // Cargar tema guardado
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            themeToggle.checked = true;
            document.body.classList.add('dark-theme');
        }
        
        themeToggle.addEventListener('change', function() {
            if (this.checked) {
                document.body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            }
        });
    }
});

// Función para mostrar notificaciones del sistema
function showSystemNotification(message, type = 'info', duration = 5000) {
    // Crear contenedor si no existe
    let container = document.getElementById('system-notifications');
    if (!container) {
        container = document.createElement('div');
        container.id = 'system-notifications';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 350px;
        `;
        document.body.appendChild(container);
    }
    
    // Crear notificación
    const notification = document.createElement('div');
    notification.style.cssText = `
        background: ${type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#06b6d4'};
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    `;
    
    const icon = type === 'error' ? 'exclamation-circle' : 
                 type === 'success' ? 'check-circle' : 
                 type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center;">
            <i class="fas fa-${icon}" style="margin-right: 8px;"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; padding: 0; margin-left: 10px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remover
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, duration);
}

// Función para confirmar acciones importantes
function confirmAction(message, callback) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10001;
    `;
    
    modal.innerHTML = `
        <div style="background: white; padding: 24px; border-radius: 12px; max-width: 400px; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 3rem; margin-bottom: 16px;"></i>
            <h3 style="margin-bottom: 12px; color: #1e293b;">Confirmar Acción</h3>
            <p style="color: #64748b; margin-bottom: 24px;">${message}</p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <button onclick="this.closest('[style*=\"position: fixed\"]').remove()" style="padding: 8px 16px; border: 1px solid #e2e8f0; background: white; color: #64748b; border-radius: 6px; cursor: pointer;">
                    Cancelar
                </button>
                <button onclick="(${callback.toString()})(); this.closest('[style*=\"position: fixed\"]').remove()" style="padding: 8px 16px; border: none; background: #ef4444; color: white; border-radius: 6px; cursor: pointer;">
                    Confirmar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Cerrar con Escape
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            modal.remove();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}
</script>

<!-- Estilos adicionales para el theme toggle -->
<style>
.theme-switch-app {
    margin-left: auto;
}

.theme-checkbox-app {
    display: none;
}

.theme-label-app {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 50px;
    height: 24px;
    background: #e2e8f0;
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: background-color 0.3s;
}

.theme-checkbox-app:checked + .theme-label-app {
    background: #374151;
}

.theme-sun-app,
.theme-moon-app {
    font-size: 0.7rem;
    padding: 2px;
    color: #6b7280;
}

.theme-slider-app {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: transform 0.3s;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.theme-checkbox-app:checked + .theme-label-app .theme-slider-app {
    transform: translateX(26px);
}

/* Responsive para el dropdown en móviles */
@media (max-width: 480px) {
    .dropdown-menu-app {
        position: fixed;
        top: 70px;
        left: 10px;
        right: 10px;
        width: auto;
        min-width: auto;
    }
    
    .dropdown-header-app {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .dropdown-avatar-app {
        width: 60px;
        height: 60px;
        font-size: 1.4rem;
    }
}
</style>
</body>
</html>
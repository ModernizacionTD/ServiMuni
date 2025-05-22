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
</head>
<body>
<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="logo">
            <i class="fa-solid fa-building-columns"></i>
            <span>ServiMuni</span>
        </a>
    </div>

    <div class="sidebar-content">
        <div class="nav-section">
            <h5 class="nav-section-title">Navegación</h5>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <!-- Nuevo enlace para Ingresar Solicitudes -->
                <li class="nav-item">
                    <a href="{{ route('buscar.usuario') }}" class="nav-link {{ request()->routeIs('buscar.usuario') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Ingresar Solicitud</span>
                    </a>
                </li>
                
                @if(session('user_rol') == 'admin')
                <li class="nav-item">
                    <a href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Panel Admin</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>

        <!-- Agregar una nueva sección para Gestión -->
        <div class="nav-section">
            <h5 class="nav-section-title">Gestión</h5>
            <ul class="nav-items">
                <li class="nav-item">
                    <a href="{{ route('solicitudes.index') }}" class="nav-link {{ request()->routeIs('solicitudes.index') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Solicitudes</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.index') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                
                @if(session('user_rol') == 'admin')
                <li class="nav-item">
                    <a href="{{ route('funcionarios.index') }}" class="nav-link {{ request()->routeIs('funcionarios.index') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i>
                        <span>Funcionarios</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('departamentos.index') }}" class="nav-link {{ request()->routeIs('departamentos.index') ? 'active' : '' }}">
                        <i class="fas fa-sitemap"></i>
                        <span>Departamentos</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ route('requerimientos.index') }}" class="nav-link {{ request()->routeIs('requerimientos.index') ? 'active' : '' }}">
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
    <div class="main-content">
        <header class="main-header">
            <div class="header-left">
                <button class="menu-toggle" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="header-right">
    <div class="user-dropdown-app" id="user-dropdown">
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
                <div class="status-indicator online"></div>
            </div>
            <div class="user-details-app">
                <span class="user-name-app">{{ session('user_nombre') }}</span>
                <span class="user-role-app">
                    <i class="fas fa-circle role-indicator"></i>
                    {{ ucfirst(session('user_rol')) }}
                </span>
            </div>
            <i class="fas fa-chevron-down dropdown-toggle" id="dropdown-arrow"></i>
        </div>
        
        <div class="dropdown-menu" id="dropdown-menu">
            <!-- Header del dropdown -->
            <div class="dropdown-header">
                <div class="dropdown-avatar">
                    {{ $iniciales }}
                </div>
                <div class="dropdown-user-info">
                    <div class="dropdown-user-name">{{ session('user_nombre') }}</div>
                    <div class="dropdown-user-email">{{ session('user_email', 'admin@servimuni.cl') }}</div>
                    <div class="dropdown-user-role">
                        <span class="role-badge role-{{ session('user_rol') }}">
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
            <div class="dropdown-divider"></div>
            
            <!-- Opciones principales -->
            <div class="dropdown-section">
                <a href="{{ route('funcionarios.profile') }}" class="dropdown-item">
                    <div class="dropdown-item-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="dropdown-item-content">
                        <span class="dropdown-item-title">Mi Perfil</span>
                        <span class="dropdown-item-subtitle">Ver y editar información personal</span>
                    </div>
                    <i class="fas fa-chevron-right dropdown-item-arrow"></i>
                </a>
                
                <a href="{{ route('funcionarios.showChangePassword') }}" class="dropdown-item">
                    <div class="dropdown-item-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <div class="dropdown-item-content">
                        <span class="dropdown-item-title">Cambiar Contraseña</span>
                        <span class="dropdown-item-subtitle">Actualizar credenciales de acceso</span>
                    </div>
                    <i class="fas fa-chevron-right dropdown-item-arrow"></i>
                </a>
                
                <div class="dropdown-item theme-toggle" id="theme-toggle">
                    <div class="dropdown-item-icon">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="dropdown-item-content">
                        <span class="dropdown-item-title">Tema</span>
                        <span class="dropdown-item-subtitle">Cambiar apariencia</span>
                    </div>
                    <div class="theme-switch">
                        <input type="checkbox" id="theme-checkbox" class="theme-checkbox">
                        <label for="theme-checkbox" class="theme-label">
                            <span class="theme-sun"><i class="fas fa-sun"></i></span>
                            <span class="theme-moon"><i class="fas fa-moon"></i></span>
                            <span class="theme-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Separador -->
            <div class="dropdown-divider"></div>
            
            <!-- Configuración -->
            <div class="dropdown-section">
                <a href="{{ route('dashboard') }}" class="dropdown-item">
                    <div class="dropdown-item-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="dropdown-item-content">
                        <span class="dropdown-item-title">Dashboard</span>
                        <span class="dropdown-item-subtitle">Volver al panel principal</span>
                    </div>
                    <i class="fas fa-chevron-right dropdown-item-arrow"></i>
                </a>
                
                @if(session('user_rol') == 'admin')
                <a href="{{ route('admin') }}" class="dropdown-item">
                    <div class="dropdown-item-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="dropdown-item-content">
                        <span class="dropdown-item-title">Configuración</span>
                        <span class="dropdown-item-subtitle">Panel de administración</span>
                    </div>
                    <i class="fas fa-chevron-right dropdown-item-arrow"></i>
                </a>
                @endif
            </div>
            
            <!-- Separador -->
            <div class="dropdown-divider"></div>
            
            <!-- Cerrar sesión -->
            <div class="dropdown-section">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" class="dropdown-item logout-item">
                        <div class="dropdown-item-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="dropdown-item-content">
                            <span class="dropdown-item-title">Cerrar Sesión</span>
                            <span class="dropdown-item-subtitle">Salir del sistema</span>
                        </div>
                        <i class="fas fa-chevron-right dropdown-item-arrow"></i>
                    </button>
                </form>
            </div>
            
            <!-- Footer del dropdown -->
            <div class="dropdown-footer">
                <div class="version-info">
                    <span>ServiMuni v2.0</span>
                    <span class="build-info">Build {{ date('Y.m.d') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
        </header>

        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <script>
        // Código directo con una espera 
        setTimeout(function() {
            var toggleButton = document.querySelector('.menu-toggle');
            var sidebar = document.querySelector('.sidebar');
            var mainContent = document.querySelector('.main-content');
            
            if (toggleButton && sidebar && mainContent) {
                toggleButton.onclick = function() {
                    if (window.innerWidth <= 991) {
                        sidebar.classList.toggle('show');
                    } else {
                        sidebar.classList.toggle('collapsed');
                        mainContent.classList.toggle('sidebar-collapsed');
                    }
                    return false;
                };
            }
        }, 500);
    </script>

    <script>
    // Esperar a que el DOM esté completamente cargado
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM cargado - inicializando scripts');
        
        // Obtener referencias a elementos importantes
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        
        // Verificar si los elementos existen y mostrar en consola para depuración
        console.log('Elemento botón toggle:', menuToggle);
        console.log('Elemento sidebar:', sidebar);
        console.log('Elemento main content:', mainContent);
        
        // Función de ayuda para determinar si estamos en modo móvil
        function isMobile() {
            return window.innerWidth <= 991;
        }
        
        // Agregar evento click al botón de toggle (si existe)
        if (menuToggle) {
            console.log('Agregando evento click al botón toggle');
            
            menuToggle.addEventListener('click', function(e) {
                console.log('Botón toggle clickeado');
                
                if (isMobile()) {
                    console.log('Modo móvil - toggling clase show');
                    sidebar.classList.toggle('show');
                } else {
                    console.log('Modo desktop - toggling clase collapsed');
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('sidebar-collapsed');
                    
                    // Guardar preferencia
                    if (sidebar.classList.contains('collapsed')) {
                        localStorage.setItem('sidebarCollapsed', 'true');
                    } else {
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                }
            });
        }
        
        // Resto de eventos para cerrar el sidebar al hacer clic fuera en móvil
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
        
        // Manejar submenús
        const settingsToggle = document.querySelector('.settings-toggle');
        if (settingsToggle) {
            settingsToggle.addEventListener('click', function(e) {
                e.preventDefault();
                this.classList.toggle('active');
                const submenu = document.getElementById('settings-submenu');
                if (submenu) submenu.classList.toggle('active');
            });
        }
        
        // Manejar el dropdown de usuario
        const userDropdown = document.getElementById('user-dropdown');
        const dropdownMenu = document.getElementById('dropdown-menu');
        
        if (userDropdown && dropdownMenu) {
            userDropdown.addEventListener('click', function(e) {
                dropdownMenu.classList.toggle('show');
            });
            
            document.addEventListener('click', function(event) {
                if (!userDropdown.contains(event.target) && 
                    dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
        
        // Restaurar estado del sidebar
        if (!isMobile() && sidebar && mainContent) {
            const savedState = localStorage.getItem('sidebarCollapsed');
            
            if (savedState === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }
        }
    });
</script>
</body>
</html>
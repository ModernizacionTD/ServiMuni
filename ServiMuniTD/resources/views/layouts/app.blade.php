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
                <div class="user-dropdown" id="user-dropdown">
                    <div class="user-info">
                        <div class="user-avatar">
                            {{ substr(session('user_nombre'), 0, 1) }}
                        </div>
                        <div class="user-details">
                            <span class="user-name">{{ session('user_nombre') }}</span>
                            <span class="user-role">{{ session('user_rol') }}</span>
                        </div>
                        <i class="fas fa-chevron-down dropdown-toggle"></i>
                    </div>
                    <div class="dropdown-menu" id="dropdown-menu">
                        <a href="{{ route('funcionarios.profile') }}" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>Mi Perfil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" class="dropdown-item logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
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
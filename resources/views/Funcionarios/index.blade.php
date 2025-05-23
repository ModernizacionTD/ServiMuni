@extends('layouts.app')

@section('title', 'Gestión de Funcionarios - Sistema de Gestión')

@section('page-title', 'Gestión de Funcionarios')

@section('content')
<div class="table-view-container filter-view-container staff-view-container">
    <link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-users me-2"></i>Lista de Funcionarios
            </h3>
            <a href="{{ route('funcionarios.create') }}" class="btn btn-add filter-add-btn">
                <i class="fas fa-plus"></i> Nuevo Funcionario
            </a>
        </div>
        
        <!-- Barra de filtros horizontal -->
        <div class="filters-bar">
            <div class="filters-container">
                <!-- Búsqueda -->
                <div class="filter-item search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control filter-search-input" placeholder="Buscar funcionario...">
                    </div>
                </div>
                
                <!-- Filtro por rol -->
                <div class="filter-item">
                    <select id="rolFilter" class="form-select filter-select">
                        <option value="">Todos los roles</option>
                        <option value="admin">Administrador</option>
                        <option value="desarrollador">Desarrollador</option>
                        <option value="orientador">Orientador</option>
                        <option value="gestor">Gestor</option>
                        <option value="tecnico">Técnico</option>
                    </select>
                </div>
                
                <!-- Filtro por departamento -->
                <div class="filter-item">
                    <select id="departamentoFilter" class="form-select filter-select">
                        <option value="">Todos los departamentos</option>
                        @if(isset($departamentos))
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento['id'] }}">{{ $departamento['nombre'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <!-- Botón reset -->
                <div class="filter-item">
                    <button type="button" class="btn btn-outline-secondary filter-reset-btn" id="clearFiltersBtn">
                        <i class="fas fa-sync-alt"></i> Restablecer filtros
                    </button>
                </div>
            </div>
            
            <!-- Chips de filtros activos -->
            <div class="active-filters-chips" id="activeFiltersContainer"></div>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="funcionariosTable" class="table data-table">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="20%">Nombre</th>
                            <th width="22%">Email</th>
                            <th width="15%">Departamento</th>
                            <th width="12%">Rol</th>
                            <th width="8%">Estado</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($funcionarios as $funcionario)
                            <tr class="staff-user-row" 
                                data-id="{{ $funcionario['id'] }}"
                                data-nombre="{{ $funcionario['nombre'] }}"
                                data-email="{{ $funcionario['email'] }}"
                                data-rol="{{ $funcionario['rol'] }}"
                                data-departamento-id="{{ $funcionario['departamento_id'] ?? '' }}"
                                data-es-propio="{{ session('user_id') == $funcionario['id'] ? 'true' : 'false' }}">
                                <td><strong>{{ $funcionario['id'] }}</strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div class="user-avatar-small">
                                            @php
                                                $nombre = $funcionario['nombre'];
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
                                        <div style="flex: 1;">
                                            <div class="user-name">{{ $funcionario['nombre'] }}</div>
                                            @if(session('user_id') == $funcionario['id'])
                                                <small class="text-primary" style="font-size: 0.75rem;">
                                                    <i class="fas fa-user-circle"></i> Tú
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $funcionario['email'] }}" class="staff-email-link contact-link">
                                        <i class="fas fa-envelope"></i>
                                        {{ $funcionario['email'] }}
                                    </a>
                                </td>
                                <td>
                                    @php
                                        $departamentoNombre = 'Sin asignar';
                                        if(isset($funcionario['departamento_id']) && isset($departamentos)) {
                                            foreach($departamentos as $departamento) {
                                                if($departamento['id'] == $funcionario['departamento_id']) {
                                                    $departamentoNombre = $departamento['nombre'];
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="persona-badge persona-natural">{{ $departamentoNombre }}</span>
                                </td>
                                <td>
                                    <span class="status-badge 
                                          @if($funcionario['rol'] == 'admin') status-danger
                                          @elseif($funcionario['rol'] == 'desarrollador') bg-info
                                          @elseif($funcionario['rol'] == 'orientador') bg-warning
                                          @elseif($funcionario['rol'] == 'gestor') bg-primary
                                          @elseif($funcionario['rol'] == 'tecnico') status-success
                                          @else status-secondary @endif">
                                        {{ ucfirst($funcionario['rol']) }}
                                    </span>
                                </td>
                                <td>
                                    @if(session('user_id') == $funcionario['id'])
                                        <span class="status-badge bg-primary">
                                            <i class="fas fa-user-circle"></i> Activo (Tú)
                                        </span>
                                    @else
                                        <span class="status-badge status-success">
                                            <i class="fas fa-check-circle"></i> Activo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions staff-action-buttons">
                                        <button type="button" class="btn btn-sm btn-info action-btn btn-view view-details" title="Ver detalles" 
                                            data-id="{{ $funcionario['id'] }}"
                                            data-email="{{ $funcionario['email'] }}"
                                            data-nombre="{{ $funcionario['nombre'] }}"
                                            data-rol="{{ $funcionario['rol'] }}"
                                            data-departamento-id="{{ $funcionario['departamento_id'] ?? '' }}"
                                            data-departamento-nombre="{{ $departamentoNombre }}"
                                            data-es-propio="{{ session('user_id') == $funcionario['id'] ? 'true' : 'false' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('funcionarios.edit', $funcionario['id']) }}" class="btn btn-sm btn-primary action-btn btn-edit" title="Editar funcionario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(session('user_id') != $funcionario['id'])
                                            <button type="button" class="btn btn-sm btn-danger action-btn btn-delete" title="Eliminar funcionario" 
                                                onclick="confirmarEliminacion('{{ $funcionario['id'] }}', '{{ $funcionario['nombre'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            
                                            <form id="delete-form-{{ $funcionario['id'] }}" action="{{ route('funcionarios.destroy', $funcionario['id']) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary action-btn" title="No puedes eliminar tu propia cuenta" disabled>
                                                <i class="fas fa-shield-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="table-empty-state staff-empty-state">
                                        <i class="fas fa-users"></i>
                                        <p class="table-empty-state-text staff-empty-state-text">No hay funcionarios registrados en el sistema</p>
                                        <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Añadir Funcionario
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="table-pagination staff-pagination-container">
                <div class="pagination-info staff-pagination-info">
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($funcionarios) }}</span> de <span class="fw-bold">{{ count($funcionarios) }}</span> funcionarios
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del funcionario -->
    <div class="user-details-panel details-panel" id="userDetailsPanel">
        <div class="user-details-modal">
            <div class="user-details-header">
                <h3><i class="fas fa-user-tie me-2"></i>Detalles del Funcionario</h3>
                <button type="button" class="detail-close-btn" id="closeDetailsBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="user-profile-header">
                <div class="user-avatar">
                    <span id="userInitials"></span>
                </div>
                <div class="user-info">
                    <h3 id="userName"></h3>
                    <p id="userRole" class="user-type"></p>
                </div>
            </div>
            
            <div class="user-details-container">
                <div class="details-section staff-details-section">
                    <h4 class="section-title staff-section-title"><i class="fas fa-id-card"></i> Información Personal</h4>
                    <div class="details-grid staff-details-grid">
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">ID</span>
                            <span class="detail-value staff-detail-value" id="userId"></span>
                        </div>
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Nombre Completo</span>
                            <span class="detail-value staff-detail-value" id="userNameDetail"></span>
                        </div>
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Email</span>
                            <a class="detail-value staff-detail-value staff-email-link contact-link" id="userEmail"></a>
                        </div>
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Departamento</span>
                            <span class="detail-value staff-detail-value" id="userDepartamento"></span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section staff-details-section">
                    <h4 class="section-title staff-section-title"><i class="fas fa-briefcase"></i> Información Laboral</h4>
                    <div class="details-grid staff-details-grid">
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Rol del Sistema</span>
                            <span class="detail-value staff-detail-value" id="userRoleDetail"></span>
                        </div>
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Estado</span>
                            <span class="detail-value staff-detail-value" id="userEstado"></span>
                        </div>
                        <div class="detail-item staff-detail-item">
                            <span class="detail-label staff-detail-label">Tipo de Cuenta</span>
                            <span class="detail-value staff-detail-value" id="userTipoCuenta"></span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section staff-details-section" id="permisosSection">
                    <h4 class="section-title staff-section-title"><i class="fas fa-shield-alt"></i> Permisos del Sistema</h4>
                    <div id="permisosContainer" class="staff-permisos-container">
                        <!-- Aquí se mostrarán los permisos según el rol -->
                    </div>
                </div>
            </div>
            
            <div class="panel-actions staff-details-actions">
                <button type="button" class="btn btn-secondary" id="closePanelBtn">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="editUserBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Funcionario
                </a>
                <a href="#" id="profileBtn" class="btn btn-success" style="display: none;">
                    <i class="fas fa-user-circle"></i> Ver Mi Perfil
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar-small {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.user-name {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9rem;
    line-height: 1.3;
    margin: 0;
}

.staff-permiso-item {
    background-color: var(--bg-light);
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}

.staff-permiso-item i {
    color: var(--primary-color);
}

.status-badge {
    font-weight: 500;
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 4px;
}

.bg-info {
    background-color: #06b6d4 !important;
    color: white;
}

.bg-warning {
    background-color: #f59e0b !important;
    color: white;
}

.bg-primary {
    background-color: var(--primary-color) !important;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const rolValue = document.getElementById('rolFilter').value;
        const departamentoValue = document.getElementById('departamentoFilter').value;
        
        const rows = document.querySelectorAll('.staff-user-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            let mostrar = true;
            
            // Filtro de búsqueda general
            if (searchValue) {
                const texto = row.textContent.toLowerCase();
                if (!texto.includes(searchValue)) {
                    mostrar = false;
                }
            }
            
            // Filtro por rol
            if (rolValue && row.getAttribute('data-rol') !== rolValue) {
                mostrar = false;
            }
            
            // Filtro por departamento
            if (departamentoValue && row.getAttribute('data-departamento-id') !== departamentoValue) {
                mostrar = false;
            }
            
            // Mostrar u ocultar fila
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibleCount++;
        });
        
        // Actualizar contador
        document.getElementById('resultCount').textContent = visibleCount;
        
        // Actualizar filtros activos
        actualizarFiltrosActivos();
    }
    
    // Función para actualizar los chips de filtros activos
    function actualizarFiltrosActivos() {
        const container = document.getElementById('activeFiltersContainer');
        container.innerHTML = '';
        
        // Búsqueda general
        const searchValue = document.getElementById('searchInput').value;
        if (searchValue.trim()) {
            agregarChipFiltro(container, 'Búsqueda', `"${searchValue}"`, () => {
                document.getElementById('searchInput').value = '';
                aplicarFiltros();
            });
        }
        
        // Rol
        const rol = document.getElementById('rolFilter');
        if (rol.value) {
            agregarChipFiltro(container, 'Rol', rol.options[rol.selectedIndex].text, () => {
                rol.value = '';
                aplicarFiltros();
            });
        }
        
        // Departamento
        const departamento = document.getElementById('departamentoFilter');
        if (departamento.value) {
            agregarChipFiltro(container, 'Departamento', departamento.options[departamento.selectedIndex].text, () => {
                departamento.value = '';
                aplicarFiltros();
            });
        }
    }
    
    // Función para agregar un chip de filtro activo
    function agregarChipFiltro(container, tipo, valor, onRemove) {
        const chip = document.createElement('div');
        chip.className = 'filter-chip';
        chip.innerHTML = `
            <span>${tipo}: ${valor}</span>
            <button type="button" class="filter-chip-remove">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        chip.querySelector('.filter-chip-remove').addEventListener('click', onRemove);
        container.appendChild(chip);
    }
    
    // Event listeners para los filtros
    document.getElementById('searchInput').addEventListener('input', aplicarFiltros);
    document.getElementById('rolFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('departamentoFilter').addEventListener('change', aplicarFiltros);
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('rolFilter').value = '';
        document.getElementById('departamentoFilter').value = '';
        aplicarFiltros();
    });
    
    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES =====
    const userDetailsPanel = document.getElementById('userDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closePanelBtn = document.getElementById('closePanelBtn');
    
    function showUserDetails() {
        userDetailsPanel.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideUserDetails() {
        userDetailsPanel.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Mostrar panel de detalles al hacer clic en el ojo
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Extraer datos del funcionario
            const funcionarioData = {
                id: this.getAttribute('data-id'),
                email: this.getAttribute('data-email'),
                nombre: this.getAttribute('data-nombre'),
                rol: this.getAttribute('data-rol'),
                departamentoId: this.getAttribute('data-departamento-id'),
                departamentoNombre: this.getAttribute('data-departamento-nombre'),
                esPropio: this.getAttribute('data-es-propio') === 'true'
            };
            
            // Llenar datos en el panel
            const iniciales = obtenerIniciales(funcionarioData.nombre);
            document.getElementById('userInitials').textContent = iniciales;
            document.getElementById('userName').textContent = funcionarioData.nombre;
            
            // Configurar la badge de rol con color apropiado
            const roleBadge = document.getElementById('userRole');
            roleBadge.textContent = ucFirst(funcionarioData.rol);
            roleBadge.className = 'status-badge'; // Reset clases
            
            switch(funcionarioData.rol) {
                case 'admin':
                    roleBadge.classList.add('status-danger');
                    break;
                case 'desarrollador':
                    roleBadge.classList.add('bg-info');
                    break;
                case 'orientador':
                    roleBadge.classList.add('bg-warning');
                    break;
                case 'gestor':
                    roleBadge.classList.add('bg-primary');
                    break;
                case 'tecnico':
                    roleBadge.classList.add('status-success');
                    break;
                default:
                    roleBadge.classList.add('status-secondary');
            }
            
            // Detalles del funcionario
            document.getElementById('userId').textContent = funcionarioData.id;
            document.getElementById('userNameDetail').textContent = funcionarioData.nombre;
            
            const userEmail = document.getElementById('userEmail');
            userEmail.textContent = funcionarioData.email;
            userEmail.href = `mailto:${funcionarioData.email}`;
            
            // Departamento
            document.getElementById('userDepartamento').textContent = funcionarioData.departamentoNombre || 'Sin asignar';
            
            document.getElementById('userRoleDetail').textContent = ucFirst(funcionarioData.rol);
            
            // Estado y tipo de cuenta
            const estadoElement = document.getElementById('userEstado');
            const tipoCuentaElement = document.getElementById('userTipoCuenta');
            
            if (funcionarioData.esPropio) {
                estadoElement.innerHTML = '<span class="status-badge bg-primary"><i class="fas fa-user-circle"></i> Activo (Tú)</span>';
                tipoCuentaElement.innerHTML = '<span class="status-badge bg-info">Tu cuenta personal</span>';
            } else {
                estadoElement.innerHTML = '<span class="status-badge status-success"><i class="fas fa-check-circle"></i> Activo</span>';
                tipoCuentaElement.innerHTML = '<span class="status-badge status-secondary">Cuenta de otro funcionario</span>';
            }
            
            // Mostrar permisos según el rol
            mostrarPermisos(funcionarioData.rol);
            
            // Configurar botones
            document.getElementById('editUserBtn').href = `/funcionarios/${funcionarioData.id}/edit`;
            
            const profileBtn = document.getElementById('profileBtn');
            if (funcionarioData.esPropio) {
                profileBtn.style.display = 'inline-flex';
                profileBtn.href = '{{ route("funcionarios.profile") }}';
            } else {
                profileBtn.style.display = 'none';
            }
            
            // Mostrar el panel
            showUserDetails();
        });
    });
    
    // Cerrar panel de detalles
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', hideUserDetails);
    }
    
    if (closePanelBtn) {
        closePanelBtn.addEventListener('click', hideUserDetails);
    }
    
    // Cerrar al hacer clic en el fondo del modal
    if (userDetailsPanel) {
        userDetailsPanel.addEventListener('click', function(event) {
            if (event.target === userDetailsPanel) {
                hideUserDetails();
            }
        });
    }
    
    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && userDetailsPanel.classList.contains('show')) {
            hideUserDetails();
        }
    });
    
    // Inicializar tooltips
    const tooltipTriggerList = document.querySelectorAll('[title]');
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        }
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar al funcionario "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}

// Función para obtener iniciales
function obtenerIniciales(nombre) {
    if (!nombre) return '??';
    
    const palabras = nombre.trim().split(/\s+/); // Dividir por uno o más espacios
    
    if (palabras.length === 1) {
        // Si solo hay una palabra, tomar las dos primeras letras
        const palabra = palabras[0];
        if (palabra.length >= 2) {
            return (palabra.charAt(0) + palabra.charAt(1)).toUpperCase();
        } else {
            return (palabra.charAt(0) + palabra.charAt(0)).toUpperCase();
        }
    } else {
        // Si hay dos o más palabras, tomar la primera letra de las dos primeras palabras
        return (palabras[0].charAt(0) + palabras[1].charAt(0)).toUpperCase();
    }
}

// Función para primera letra en mayúscula
function ucFirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

// Función para mostrar permisos según el rol
function mostrarPermisos(rol) {
    const permisosContainer = document.getElementById('permisosContainer');
    permisosContainer.innerHTML = '';
    
    let permisos = [];
    
    switch(rol) {
        case 'admin':
            permisos = [
                { icon: 'fas fa-users-cog', nombre: 'Gestión completa de usuarios' },
                { icon: 'fas fa-user-shield', nombre: 'Gestión de funcionarios' },
                { icon: 'fas fa-sitemap', nombre: 'Gestión de departamentos' },
                { icon: 'fas fa-clipboard-list', nombre: 'Gestión de requerimientos' },
                { icon: 'fas fa-cogs', nombre: 'Configuración del sistema' },
                { icon: 'fas fa-shield-alt', nombre: 'Acceso administrativo completo' }
            ];
            break;
        case 'desarrollador':
            permisos = [
                { icon: 'fas fa-code', nombre: 'Desarrollo y mantenimiento' },
                { icon: 'fas fa-database', nombre: 'Acceso a base de datos' },
                { icon: 'fas fa-bug', nombre: 'Resolución de errores' },
                { icon: 'fas fa-cogs', nombre: 'Configuración técnica' }
            ];
            break;
        case 'orientador':
            permisos = [
                { icon: 'fas fa-users', nombre: 'Orientación a usuarios' },
                { icon: 'fas fa-clipboard-list', nombre: 'Gestión de solicitudes' },
                { icon: 'fas fa-phone', nombre: 'Atención telefónica' },
                { icon: 'fas fa-info-circle', nombre: 'Información general' }
            ];
            break;
        case 'gestor':
            permisos = [
                { icon: 'fas fa-tasks', nombre: 'Gestión de proyectos' },
                { icon: 'fas fa-clipboard-list', nombre: 'Seguimiento de solicitudes' },
                { icon: 'fas fa-chart-line', nombre: 'Reportes de gestión' },
                { icon: 'fas fa-users', nombre: 'Coordinación de equipos' }
            ];
            break;
        case 'tecnico':
            permisos = [
                { icon: 'fas fa-tools', nombre: 'Trabajo técnico' },
                { icon: 'fas fa-clipboard-check', nombre: 'Ejecución de tareas' },
                { icon: 'fas fa-wrench', nombre: 'Mantenimiento técnico' },
                { icon: 'fas fa-hard-hat', nombre: 'Trabajo de campo' }
            ];
            break;
        default:
            permisos = [
                { icon: 'fas fa-user', nombre: 'Acceso básico al sistema' }
            ];
    }
    
    permisos.forEach(permiso => {
        const permisoItem = document.createElement('div');
        permisoItem.className = 'staff-permiso-item';
        permisoItem.innerHTML = `<i class="${permiso.icon}"></i> ${permiso.nombre}`;
        permisosContainer.appendChild(permisoItem);
    });
}
</script>
@endsection
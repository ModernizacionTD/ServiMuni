@extends('layouts.app')

@section('title', 'Gestión de Funcionarios - Sistema de Gestión')

@section('page-title', 'Gestión de Funcionarios')

@section('content')
<link rel="stylesheet" href="{{ asset('css/funcionario.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<div class="card">
    <div class="card-header">
        <div class="header-filters mt-3">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar funcionario...">
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('funcionarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Funcionario
            </a>
        </div>  
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
            <table id="funcionariosTable" class="table table-hover">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="25%">Email</th>
                        <th width="25%">Nombre</th>
                        <th width="15%">Rol</th>
                        <th width="15%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($funcionarios as $funcionario)
                        <tr class="user-row">
                            <td>{{ $funcionario['id'] }}</td>
                            <td>
                                <a href="mailto:{{ $funcionario['email'] }}" class="email-link">
                                    {{ $funcionario['email'] }}
                                </a>
                            </td>
                            <td>{{ $funcionario['nombre'] }}</td>
                            <td>
                                <span class="badge 
                                      @if($funcionario['rol'] == 'admin') bg-danger
                                      @elseif($funcionario['rol'] == 'funcionario') bg-primary
                                      @else bg-success @endif">
                                    {{ ucfirst($funcionario['rol']) }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('funcionarios.edit', $funcionario['id']) }}" class="btn btn-sm btn-primary" title="Editar funcionario">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-info view-details" title="Ver detalles" 
                                        data-id="{{ $funcionario['id'] }}"
                                        data-email="{{ $funcionario['email'] }}"
                                        data-nombre="{{ $funcionario['nombre'] }}"
                                        data-rol="{{ $funcionario['rol'] }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if(session('user_id') != $funcionario['id'])
                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar" 
                                        onclick="confirmarEliminacion('{{ $funcionario['id'] }}', '{{ $funcionario['nombre'] }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <form id="delete-form-{{ $funcionario['id'] }}" action="{{ route('funcionarios.destroy', $funcionario['id']) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @else
                                    <button type="button" class="btn btn-sm btn-secondary" title="No puedes eliminar tu propia cuenta" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="empty-state-text">No hay funcionarios registrados en el sistema</p>
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
        
        <!-- Paginación (futura implementación) -->
        <div class="pagination-container mt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Mostrando <span class="fw-bold">{{ count($funcionarios) }}</span> funcionarios
                </div>
                <div class="pagination-controls">
                    <!-- Aquí se puede agregar la paginación cuando se implemente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de detalles del funcionario (aparece al hacer clic en el ojo) -->
<div class="user-details-panel" id="userDetailsPanel" style="display: none;">
    <div class="user-details-header">
        <h3>Detalles del Funcionario</h3>
        <button type="button" class="btn btn-sm btn-light" id="closeDetailsBtn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="user-profile-header">
        <div class="user-avatar">
            <span id="userInitials"></span>
        </div>
        <div class="user-info">
            <h3 id="userName"></h3>
            <p>
                <span id="userRole" class="badge"></span>
            </p>
        </div>
    </div>
    
    <div class="user-details-container">
        <div class="details-section">
            <h4 class="section-title"><i class="fas fa-id-card"></i> Información del Funcionario</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">ID</span>
                    <span class="detail-value" id="userId"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email</span>
                    <a class="detail-value email-link" id="userEmail"></a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nombre</span>
                    <span class="detail-value" id="userNameDetail"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Rol</span>
                    <span class="detail-value" id="userRoleDetail"></span>
                </div>
            </div>
        </div>
        
        <div class="details-section" id="permisosSection">
            <h4 class="section-title"><i class="fas fa-shield-alt"></i> Permisos</h4>
            <div id="permisosContainer">
                <!-- Aquí se mostrarán los permisos según el rol -->
            </div>
        </div>
    </div>
    
    <div class="details-actions">
        <button type="button" class="btn btn-secondary" id="closePanelBtn">Cerrar</button>
        <a href="#" id="editUserBtn" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar Funcionario
        </a>
        @if(session('user_rol') == 'admin')
        <a href="#" id="resetPasswordBtn" class="btn btn-warning">
            <i class="fas fa-key"></i> Restablecer Contraseña
        </a>
        @endif
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de forma segura
    try {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = document.querySelectorAll('[title]');
            [].slice.call(tooltipTriggerList).forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    } catch (error) {
        console.error("Error al inicializar tooltips:", error);
    }

    // Funcionalidad de búsqueda
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('.user-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    }
    
    // Panel de detalles de funcionario
    const userDetailsPanel = document.getElementById('userDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closePanelBtn = document.getElementById('closePanelBtn');
    
    // Mostrar panel de detalles al hacer clic en el ojo
    if (viewButtons.length > 0 && userDetailsPanel) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                // Extraer datos del funcionario
                const id = this.getAttribute('data-id');
                const email = this.getAttribute('data-email');
                const nombre = this.getAttribute('data-nombre');
                const rol = this.getAttribute('data-rol');
                
                // Iniciales para el avatar
                const iniciales = nombre.charAt(0).toUpperCase();
                
                // Llenar datos en el panel
                document.getElementById('userInitials').textContent = iniciales;
                document.getElementById('userName').textContent = nombre;
                
                // Configurar la badge de rol con color apropiado
                const roleBadge = document.getElementById('userRole');
                roleBadge.textContent = ucFirst(rol);
                roleBadge.className = 'badge'; // Reset clases
                
                if (rol === 'admin') {
                    roleBadge.classList.add('bg-danger');
                } else if (rol === 'funcionario') {
                    roleBadge.classList.add('bg-primary');
                } else {
                    roleBadge.classList.add('bg-success');
                }
                
                // Detalles del funcionario
                document.getElementById('userId').textContent = id;
                
                const userEmail = document.getElementById('userEmail');
                userEmail.textContent = email;
                userEmail.href = `mailto:${email}`;
                
                document.getElementById('userNameDetail').textContent = nombre;
                document.getElementById('userRoleDetail').textContent = ucFirst(rol);
                
                // Mostrar permisos según el rol
                mostrarPermisos(rol);
                
                // Configurar botón de editar
                document.getElementById('editUserBtn').href = `/funcionarios/${id}/edit`;
                
                // Configurar botón de restablecer contraseña si existe
                const resetBtn = document.getElementById('resetPasswordBtn');
                if (resetBtn) {
                    resetBtn.href = `/funcionarios/${id}/reset-password`;
                }
                
                // Mostrar el panel
                userDetailsPanel.style.display = 'block';
                
                // Desplazar la página hasta el panel
                userDetailsPanel.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }
    
    // Cerrar panel de detalles
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', function() {
            userDetailsPanel.style.display = 'none';
        });
    }
    
    if (closePanelBtn) {
        closePanelBtn.addEventListener('click', function() {
            userDetailsPanel.style.display = 'none';
        });
    }
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar al funcionario ${nombre}?`)) {
        document.getElementById(`delete-form-${id}`).submit();
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
    
    if (rol === 'admin') {
        permisos = [
            { icon: 'fas fa-users-cog', nombre: 'Gestión de Usuarios' },
            { icon: 'fas fa-user-shield', nombre: 'Gestión de Funcionarios' },
            { icon: 'fas fa-sitemap', nombre: 'Gestión de Departamentos' },
            { icon: 'fas fa-clipboard-list', nombre: 'Gestión de Requerimientos' },
            { icon: 'fas fa-cogs', nombre: 'Configuración del Sistema' }
        ];
    } else if (rol === 'funcionario') {
        permisos = [
            { icon: 'fas fa-clipboard-list', nombre: 'Ver Requerimientos' },
            { icon: 'fas fa-sitemap', nombre: 'Ver Departamentos' },
            { icon: 'fas fa-user', nombre: 'Gestión de Perfil' }
        ];
    } else {
        permisos = [
            { icon: 'fas fa-clipboard-list', nombre: 'Ver Requerimientos' },
            { icon: 'fas fa-user', nombre: 'Gestión de Perfil' }
        ];
    }
    
    permisos.forEach(permiso => {
        const permisoItem = document.createElement('div');
        permisoItem.className = 'permiso-item';
        permisoItem.innerHTML = `<i class="${permiso.icon}"></i> ${permiso.nombre}`;
        permisosContainer.appendChild(permisoItem);
    });
}
</script>
@endsection
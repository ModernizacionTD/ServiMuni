@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Sistema de Gestión')

@section('page-title', 'Gestión de Usuarios')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}">
<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">

<div class="card">
    <div class="card-header">
                <div class="header-filters mt-3">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
            </div>
        </div>

        <div class="header-actions">
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Usuario
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
            <table id="usuariosTable" class="table table-hover">
                <thead>
                    <tr>
                        <th width="10%">RUT</th>
                        <th width="10%">Persona</th>
                        <th width="15%">Nombre</th>
                        <th width="15%">Apellidos</th>
                        <th width="15%">Email</th>
                        <th width="10%">Teléfono</th>
                        <th width="15%">Dirección</th>
                        <th width="10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr class="user-row">
                            <td>{{ $usuario['rut'] }}</td>
                            <td>{{ $usuario['tipo_persona'] }}</td>
                            <td>{{ $usuario['nombre'] }}</td>
                            <td>{{ $usuario['apellidos'] }}</td>
                            <td>
                                <a href="mailto:{{ $usuario['email'] }}" class="email-link">
                                    {{ $usuario['email'] }}
                                </a>
                            </td>
                            <td>
                                <a href="tel:{{ $usuario['telefono'] }}" class="phone-link">
                                    {{ $usuario['telefono'] }}
                                </a>
                            </td>
                            <td class="address-cell">{{ $usuario['direccion'] }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('usuarios.edit', $usuario['rut']) }}" class="btn btn-sm btn-primary" title="Editar usuario">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-info view-details" title="Ver detalles" 
                                        data-rut="{{ $usuario['rut'] }}"
                                        data-nombre="{{ $usuario['nombre'] }}"
                                        data-apellidos="{{ $usuario['apellidos'] }}"
                                        data-tipo="{{ $usuario['tipo_persona'] }}"
                                        data-uso-ns="{{ $usuario['uso_ns'] }}"
                                        data-nombre-social="{{ $usuario['nombre_social'] }}"
                                        data-nacimiento="{{ $usuario['fecha_nacimiento'] }}"
                                        data-genero="{{ $usuario['genero'] }}"
                                        data-telefono="{{ $usuario['telefono'] }}"
                                        data-telefono2="{{ $usuario['telefono_2'] }}"
                                        data-email="{{ $usuario['email'] }}"
                                        data-email2="{{ $usuario['email_2'] }}"
                                        data-direccion="{{ $usuario['direccion'] }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar" 
                                        onclick="confirmarEliminacion('{{ $usuario['rut'] }}', '{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <form id="delete-form-{{ $usuario['rut'] }}" action="{{ route('usuarios.destroy', $usuario['rut']) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="empty-state-text">No hay usuarios registrados en el sistema</p>
                                    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Añadir Usuario
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
                    Mostrando <span class="fw-bold">{{ count($usuarios) }}</span> usuarios
                </div>
                <div class="pagination-controls">
                    <!-- Aquí se puede agregar la paginación cuando se implemente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de detalles del usuario (aparece al hacer clic en el ojo) -->
<div class="user-details-panel" id="userDetailsPanel" style="display: none;">
    <div class="user-details-header">
        <h3>Detalles del Usuario</h3>
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
            <p id="userType" class="user-type"></p>
        </div>
    </div>
    
    <div class="user-details-container">
        <div class="details-section">
            <h4 class="section-title"><i class="fas fa-id-card"></i> Información Personal</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">RUT</span>
                    <span class="detail-value" id="userRut"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Fecha de Nacimiento</span>
                    <span class="detail-value" id="userBirthdate"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Género</span>
                    <span class="detail-value" id="userGender"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Uso de Nombre Social</span>
                    <span class="detail-value" id="userUseNS"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nombre Social</span>
                    <span class="detail-value" id="userSocialName"></span>
                </div>
            </div>
        </div>
        
        <div class="details-section">
            <h4 class="section-title"><i class="fas fa-phone-alt"></i> Información de Contacto</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Email Principal</span>
                    <a class="detail-value email-link" id="userEmail"></a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email Alternativo</span>
                    <a class="detail-value email-link" id="userEmail2"></a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Teléfono Principal</span>
                    <a class="detail-value phone-link" id="userPhone"></a>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Teléfono Alternativo</span>
                    <a class="detail-value phone-link" id="userPhone2"></a>
                </div>
                <div class="detail-item full-width">
                    <span class="detail-label">Dirección</span>
                    <span class="detail-value" id="userAddress"></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="details-actions">
        <button type="button" class="btn btn-secondary" id="closePanelBtn">Cerrar</button>
        <a href="#" id="editUserBtn" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar Usuario
        </a>
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
    
    // Panel de detalles de usuario
    const userDetailsPanel = document.getElementById('userDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closePanelBtn = document.getElementById('closePanelBtn');
    
    // Debug para verificar elementos
    console.log("Panel de detalles:", userDetailsPanel ? "Encontrado" : "No encontrado");
    console.log("Botones de vista:", viewButtons.length);
    
    // Mostrar panel de detalles al hacer clic en el ojo
    if (viewButtons.length > 0 && userDetailsPanel) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                console.log("Botón de ojo clickeado");
                
                // Extraer datos del usuario
                const rut = this.getAttribute('data-rut');
                const nombre = this.getAttribute('data-nombre');
                const apellidos = this.getAttribute('data-apellidos');
                const tipo = this.getAttribute('data-tipo');
                const usoNS = this.getAttribute('data-uso-ns');
                const nombreSocial = this.getAttribute('data-nombre-social');
                const nacimiento = this.getAttribute('data-nacimiento');
                const genero = this.getAttribute('data-genero');
                const telefono = this.getAttribute('data-telefono');
                const telefono2 = this.getAttribute('data-telefono2');
                const email = this.getAttribute('data-email');
                const email2 = this.getAttribute('data-email2');
                const direccion = this.getAttribute('data-direccion');
                
                // Llenar datos en el panel
                document.getElementById('userInitials').textContent = obtenerIniciales(nombre, apellidos);
                document.getElementById('userName').textContent = `${nombre} ${apellidos}`;
                document.getElementById('userType').textContent = tipo;
                document.getElementById('userRut').textContent = rut;
                document.getElementById('userBirthdate').textContent = formatearFecha(nacimiento);
                document.getElementById('userGender').textContent = genero;
                document.getElementById('userUseNS').textContent = usoNS;
                document.getElementById('userSocialName').textContent = nombreSocial;
                
                const userEmail = document.getElementById('userEmail');
                userEmail.textContent = email;
                userEmail.href = `mailto:${email}`;
                
                const userEmail2 = document.getElementById('userEmail2');
                userEmail2.textContent = email2;
                userEmail2.href = `mailto:${email2}`;
                
                const userPhone = document.getElementById('userPhone');
                userPhone.textContent = telefono;
                userPhone.href = `tel:${telefono}`;
                
                const userPhone2 = document.getElementById('userPhone2');
                userPhone2.textContent = telefono2;
                userPhone2.href = `tel:${telefono2}`;
                
                document.getElementById('userAddress').textContent = direccion;
                
                // Configurar botón de editar
                document.getElementById('editUserBtn').href = `/usuarios/${rut}/edit`;
                
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
    if (confirm(`¿Estás seguro de que deseas eliminar al usuario ${nombre}?`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}

// Función para obtener iniciales
function obtenerIniciales(nombre, apellidos) {
    return (nombre.charAt(0) + (apellidos ? apellidos.charAt(0) : '')).toUpperCase();
}

// Función para formatear fecha
function formatearFecha(fechaStr) {
    if (!fechaStr) return '-';
    
    try {
        const fecha = new Date(fechaStr);
        return fecha.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (e) {
        return fechaStr;
    }
}
</script>
@endsection
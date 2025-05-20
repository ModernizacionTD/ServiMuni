@extends('layouts.app')

@section('title', 'Gestión de Requerimientos - Sistema de Gestión')

@section('page-title', 'Gestión de Requerimientos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
<link rel="stylesheet" href="{{ asset('css/requerimientos.css') }}">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">


<div class="card">
    <div class="card-header">
        <div class="header-actions">
            <h2 class="card-title"><i class="fas fa-clipboard-list me-2"></i>Requerimientos del Sistema</h2>
            <a href="{{ route('requerimientos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Requerimiento
            </a>
        </div>
        
        <div class="header-filters mt-3">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Buscar requerimiento...">
            </div>
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
            <table id="requerimientosTable" class="table table-hover">
                <thead>
                    <tr>
                        <th width="8%">ID</th>
                        <th width="15%">Departamento</th>
                        <th width="17%">Nombre</th>
                        <th width="20%">Descripción</th>
                        <th width="6%">Privado</th>
                        <th width="6%">Publico</th>
                        <th width="8%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requerimientos as $requerimiento)
                        <tr class="user-row">
                            <td>{{ $requerimiento['id_requerimiento'] }}</td>
                            <td>
                                @foreach($departamentos as $departamento)
                                    @if($departamento['id'] == $requerimiento['departamento_id'])
                                        {{ $departamento['nombre'] }}
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $requerimiento['nombre'] }}</td>
                            <td>{{ Str::limit($requerimiento['descripcion_req'], 50) }}</td>
                            <td>
                                <span class="badge {{ $requerimiento['privado'] ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $requerimiento['privado'] ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $requerimiento['publico'] ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $requerimiento['publico'] ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('requerimientos.edit', $requerimiento['id_requerimiento']) }}" class="btn btn-sm btn-primary" title="Editar requerimiento">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <button type="button" class="btn btn-sm btn-info view-details" title="Ver detalles" 
                                        data-id="{{ $requerimiento['id_requerimiento'] }}"
                                        data-departamento-id="{{ $requerimiento['departamento_id'] }}"
                                        data-nombre="{{ $requerimiento['nombre'] }}"
                                        data-descripcion-req="{{ $requerimiento['descripcion_req'] }}"
                                        data-descripcion-precio="{{ $requerimiento['descripcion_precio'] }}"
                                        data-privado="{{ $requerimiento['privado'] ? 'Sí' : 'No' }}"
                                        data-publico="{{ $requerimiento['publico'] ? 'Sí' : 'No' }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-sm btn-danger" title="Eliminar" 
                                        onclick="confirmarEliminacion('{{ $requerimiento['id_requerimiento'] }}', '{{ $requerimiento['nombre'] }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    
                                    <form id="delete-form-{{ $requerimiento['id_requerimiento'] }}" action="{{ route('requerimientos.destroy', $requerimiento['id_requerimiento']) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p class="empty-state-text">No hay requerimientos registrados en el sistema</p>
                                    <a href="{{ route('requerimientos.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Añadir Requerimiento
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
                    Mostrando <span class="fw-bold">{{ count($requerimientos) }}</span> requerimientos
                </div>
                <div class="pagination-controls">
                    <!-- Aquí se puede agregar la paginación cuando se implemente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel de detalles del requerimiento (aparece al hacer clic en el ojo) -->
<div class="user-details-panel" id="userDetailsPanel" style="display: none;">
    <div class="user-details-header">
        <h3>Detalles del Requerimiento</h3>
        <button type="button" class="btn btn-sm btn-light" id="closeDetailsBtn">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="user-profile-header">
        <div class="user-avatar">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="user-info">
            <h3 id="reqNombre"></h3>
            <p id="reqDepartamento" class="user-type"></p>
        </div>
    </div>
    
    <div class="user-details-container">
        <div class="details-section">
            <h4 class="section-title"><i class="fas fa-info-circle"></i> Información del Requerimiento</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">ID</span>
                    <span class="detail-value" id="reqId"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Departamento</span>
                    <span class="detail-value" id="reqDepartamentoName"></span>
                </div>
                <div class="detail-item full-width">
                    <span class="detail-label">Descripción del Requerimiento</span>
                    <span class="detail-value" id="reqDescripcion"></span>
                </div>
                <div class="detail-item full-width">
                    <span class="detail-label">Información de Precio</span>
                    <span class="detail-value" id="reqPrecio"></span>
                </div>
            </div>
        </div>
        
        <div class="details-section">
            <h4 class="section-title"><i class="fas fa-cog"></i> Configuración</h4>
            <div class="details-grid">
                <div class="detail-item">
                    <span class="detail-label">Privado</span>
                    <span class="detail-value" id="reqPrivado"></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Público</span>
                    <span class="detail-value" id="reqPublico"></span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="details-actions">
        <button type="button" class="btn btn-secondary" id="closePanelBtn">Cerrar</button>
        <a href="#" id="editReqBtn" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar Requerimiento
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
    
    // Panel de detalles de requerimiento
    const userDetailsPanel = document.getElementById('userDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closePanelBtn = document.getElementById('closePanelBtn');
    
    // Mostrar panel de detalles al hacer clic en el ojo
    if (viewButtons.length > 0 && userDetailsPanel) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                console.log("Botón de ojo clickeado");
                
                // Extraer datos del requerimiento
                const id = this.getAttribute('data-id');
                const departamentoId = this.getAttribute('data-departamento-id');
                const nombre = this.getAttribute('data-nombre');
                const descripcion = this.getAttribute('data-descripcion-req');
                const precio = this.getAttribute('data-descripcion-precio');
                const privado = this.getAttribute('data-privado');
                const publico = this.getAttribute('data-publico');
                
                // Llenar datos en el panel
                document.getElementById('reqId').textContent = id;
                document.getElementById('reqNombre').textContent = nombre;
                
                // Buscar el nombre del departamento
                const departamentoNombre = obtenerNombreDepartamento(departamentoId);
                document.getElementById('reqDepartamento').textContent = departamentoNombre;
                document.getElementById('reqDepartamentoName').textContent = departamentoNombre;
                
                document.getElementById('reqDescripcion').textContent = descripcion;
                document.getElementById('reqPrecio').textContent = precio;
                document.getElementById('reqPrivado').textContent = privado;
                document.getElementById('reqPublico').textContent = publico;
                
                // Configurar botón de editar
                document.getElementById('editReqBtn').href = `/requerimientos/${id}/edit`;
                
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
    
    // Función para obtener el nombre del departamento por su ID
    function obtenerNombreDepartamento(departamentoId) {
        // Esta función debería buscar en la lista de departamentos del servidor
        // Como simplificación, vamos a buscar en los elementos de la tabla
        const departamentoCell = document.querySelector(`tr[data-departamento-id="${departamentoId}"] td:nth-child(2)`);
        if (departamentoCell) {
            return departamentoCell.textContent.trim();
        }
        return `Departamento ${departamentoId}`;
    }
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el requerimiento "${nombre}"?`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection
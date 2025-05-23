@extends('layouts.app')

@section('title', 'Gestión de Requerimientos - Sistema de Gestión')

@section('page-title', 'Gestión de Requerimientos')

@section('content')
<div class="table-view-container filter-view-container">
    <link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-clipboard-list me-2"></i>Lista de Requerimientos
            </h3>
            <a href="{{ route('requerimientos.create') }}" class="btn btn-add filter-add-btn">
                <i class="fas fa-plus"></i> Nuevo Requerimiento
            </a>
        </div>
        
        <!-- Barra de filtros horizontal -->
        <div class="filters-bar">
            <div class="filters-container">
                <!-- Búsqueda -->
                <div class="filter-item search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control filter-search-input" placeholder="Buscar requerimiento...">
                    </div>
                </div>
                
                <!-- Filtro por departamento -->
                <div class="filter-item">
                    <select id="departamentoFilter" class="form-select filter-select">
                        <option value="">Todos los departamentos</option>
                        @foreach($departamentos as $departamento)
                            <option value="{{ $departamento['id'] }}">{{ $departamento['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtro por tipo de acceso -->
                <div class="filter-item">
                    <select id="tipoAccesoFilter" class="form-select filter-select">
                        <option value="">Tipo de acceso</option>
                        <option value="ambos">Privado y Público</option>
                        <option value="solo_privado">Solo Privado</option>
                        <option value="solo_publico">Solo Público</option>
                        <option value="ninguno">Sin acceso</option>
                    </select>
                </div>
                
                <!-- Filtro por estado -->
                <div class="filter-item">
                    <select id="estadoFilter" class="form-select filter-select">
                        <option value="">Estado</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
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
                <table id="requerimientosTable" class="table data-table">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="15%">Departamento</th>
                            <th width="17%">Nombre</th>
                            <th width="25%">Descripción</th>
                            <th width="8%">Privado</th>
                            <th width="8%">Público</th>
                            <th width="19%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requerimientos as $requerimiento)
                            <tr class="requirement-row" 
                                data-departamento-id="{{ $requerimiento['departamento_id'] }}"
                                data-privado="{{ $requerimiento['privado'] ? '1' : '0' }}"
                                data-publico="{{ $requerimiento['publico'] ? '1' : '0' }}"
                                data-tipo-acceso="{{ ($requerimiento['privado'] && $requerimiento['publico']) ? 'ambos' : (($requerimiento['privado'] && !$requerimiento['publico']) ? 'solo_privado' : ((!$requerimiento['privado'] && $requerimiento['publico']) ? 'solo_publico' : 'ninguno')) }}"
                                data-estado="{{ ($requerimiento['privado'] || $requerimiento['publico']) ? 'activo' : 'inactivo' }}">
                                <td><strong>{{ $requerimiento['id_requerimiento'] }}</strong></td>
                                <td>
                                    @php
                                        $departamentoNombre = 'No asignado';
                                        foreach($departamentos as $departamento) {
                                            if($departamento['id'] == $requerimiento['departamento_id']) {
                                                $departamentoNombre = $departamento['nombre'];
                                                break;
                                            }
                                        }
                                    @endphp
                                    <span class="persona-badge persona-natural">{{ $departamentoNombre }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="requirement-icon">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <span class="requirement-name">{{ $requerimiento['nombre'] }}</span>
                                    </div>
                                </td>
                                <td class="address-cell">{{ $requerimiento['descripcion_req'] }}</td>
                                <td>
                                    <span class="status-badge {{ $requerimiento['privado'] ? 'status-success' : 'status-secondary' }}">
                                        <i class="fas {{ $requerimiento['privado'] ? 'fa-check' : 'fa-times' }}"></i>
                                        {{ $requerimiento['privado'] ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $requerimiento['publico'] ? 'status-success' : 'status-secondary' }}">
                                        <i class="fas {{ $requerimiento['publico'] ? 'fa-check' : 'fa-times' }}"></i>
                                        {{ $requerimiento['publico'] ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="btn btn-sm btn-info action-btn btn-view view-details" title="Ver detalles" 
                                            data-id="{{ $requerimiento['id_requerimiento'] }}"
                                            data-departamento-id="{{ $requerimiento['departamento_id'] }}"
                                            data-departamento-nombre="{{ $departamentoNombre }}"
                                            data-nombre="{{ $requerimiento['nombre'] }}"
                                            data-descripcion-req="{{ $requerimiento['descripcion_req'] }}"
                                            data-descripcion-precio="{{ $requerimiento['descripcion_precio'] }}"
                                            data-privado="{{ $requerimiento['privado'] ? 'Sí' : 'No' }}"
                                            data-publico="{{ $requerimiento['publico'] ? 'Sí' : 'No' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('requerimientos.edit', $requerimiento['id_requerimiento']) }}" class="btn btn-sm btn-primary action-btn btn-edit" title="Editar requerimiento">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-danger action-btn btn-delete" title="Eliminar" 
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
                                <td colspan="7">
                                    <div class="table-empty-state">
                                        <i class="fas fa-clipboard-list"></i>
                                        <p class="table-empty-state-text">No hay requerimientos registrados en el sistema</p>
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
            
            <div class="table-pagination">
                <div class="pagination-info">
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($requerimientos) }}</span> de <span class="fw-bold">{{ count($requerimientos) }}</span> requerimientos
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del requerimiento -->
    <div class="user-details-panel details-panel" id="userDetailsPanel">
        <div class="user-details-modal">
            <div class="user-details-header">
                <h3><i class="fas fa-clipboard-list me-2"></i>Detalles del Requerimiento</h3>
                <button type="button" class="detail-close-btn" id="closeDetailsBtn">
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
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Nombre del Requerimiento</span>
                            <span class="detail-value" id="reqNombreDetail"></span>
                        </div>
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Descripción del Requerimiento</span>
                            <span class="detail-value" id="reqDescripcion"></span>
                        </div>
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Información de Precio</span>
                            <span class="detail-value" id="reqPrecio"></span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-cog"></i> Configuración de Acceso</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Acceso Privado</span>
                            <span class="detail-value" id="reqPrivado"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Acceso Público</span>
                            <span class="detail-value" id="reqPublico"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Estado General</span>
                            <span class="detail-value" id="reqEstado"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tipo de Acceso</span>
                            <span class="detail-value" id="reqTipoAcceso"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel-actions">
                <button type="button" class="btn btn-secondary" id="closePanelBtn">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="editReqBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Requerimiento
                </a>
                <a href="{{ route('solicitudes.create') }}" class="btn btn-success" id="createSolicitudBtn">
                    <i class="fas fa-plus"></i> Crear Solicitud
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.requirement-icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(56, 103, 214, 0.1);
    border-radius: 6px;
    color: var(--primary-color);
    flex-shrink: 0;
    font-size: 0.9rem;
}

.requirement-name {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9rem;
    line-height: 1.3;
}

.status-badge {
    font-weight: 500;
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const departamentoValue = document.getElementById('departamentoFilter').value;
        const tipoAccesoValue = document.getElementById('tipoAccesoFilter').value;
        const estadoValue = document.getElementById('estadoFilter').value;
        
        const rows = document.querySelectorAll('.requirement-row');
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
            
            // Filtro por departamento
            if (departamentoValue && row.getAttribute('data-departamento-id') !== departamentoValue) {
                mostrar = false;
            }
            
            // Filtro por tipo de acceso
            if (tipoAccesoValue && row.getAttribute('data-tipo-acceso') !== tipoAccesoValue) {
                mostrar = false;
            }
            
            // Filtro por estado
            if (estadoValue && row.getAttribute('data-estado') !== estadoValue) {
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
        
        // Departamento
        const departamento = document.getElementById('departamentoFilter');
        if (departamento.value) {
            agregarChipFiltro(container, 'Departamento', departamento.options[departamento.selectedIndex].text, () => {
                departamento.value = '';
                aplicarFiltros();
            });
        }
        
        // Tipo de acceso
        const tipoAcceso = document.getElementById('tipoAccesoFilter');
        if (tipoAcceso.value) {
            agregarChipFiltro(container, 'Tipo de Acceso', tipoAcceso.options[tipoAcceso.selectedIndex].text, () => {
                tipoAcceso.value = '';
                aplicarFiltros();
            });
        }
        
        // Estado
        const estado = document.getElementById('estadoFilter');
        if (estado.value) {
            agregarChipFiltro(container, 'Estado', estado.options[estado.selectedIndex].text, () => {
                estado.value = '';
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
    document.getElementById('departamentoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('tipoAccesoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('estadoFilter').addEventListener('change', aplicarFiltros);
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('departamentoFilter').value = '';
        document.getElementById('tipoAccesoFilter').value = '';
        document.getElementById('estadoFilter').value = '';
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
            
            // Extraer datos del requerimiento
            const requerimientoData = {
                id: this.getAttribute('data-id'),
                departamentoId: this.getAttribute('data-departamento-id'),
                departamentoNombre: this.getAttribute('data-departamento-nombre'),
                nombre: this.getAttribute('data-nombre'),
                descripcionReq: this.getAttribute('data-descripcion-req'),
                descripcionPrecio: this.getAttribute('data-descripcion-precio'),
                privado: this.getAttribute('data-privado'),
                publico: this.getAttribute('data-publico')
            };
            
            // Llenar datos en el panel
            document.getElementById('reqNombre').textContent = requerimientoData.nombre;
            document.getElementById('reqDepartamento').textContent = requerimientoData.departamentoNombre;
            document.getElementById('reqId').textContent = requerimientoData.id;
            document.getElementById('reqDepartamentoName').textContent = requerimientoData.departamentoNombre;
            document.getElementById('reqNombreDetail').textContent = requerimientoData.nombre;
            document.getElementById('reqDescripcion').textContent = requerimientoData.descripcionReq;
            document.getElementById('reqPrecio').textContent = requerimientoData.descripcionPrecio || 'No especificado';
            
            // Configurar badges de acceso
            const privadoElement = document.getElementById('reqPrivado');
            const publicoElement = document.getElementById('reqPublico');
            const estadoElement = document.getElementById('reqEstado');
            const tipoAccesoElement = document.getElementById('reqTipoAcceso');
            
            // Privado
            if (requerimientoData.privado === 'Sí') {
                privadoElement.innerHTML = '<span class="status-badge status-success"><i class="fas fa-check"></i> Habilitado</span>';
            } else {
                privadoElement.innerHTML = '<span class="status-badge status-secondary"><i class="fas fa-times"></i> Deshabilitado</span>';
            }
            
            // Público
            if (requerimientoData.publico === 'Sí') {
                publicoElement.innerHTML = '<span class="status-badge status-success"><i class="fas fa-check"></i> Habilitado</span>';
            } else {
                publicoElement.innerHTML = '<span class="status-badge status-secondary"><i class="fas fa-times"></i> Deshabilitado</span>';
            }
            
            // Estado general y tipo de acceso
            if (requerimientoData.privado === 'Sí' && requerimientoData.publico === 'Sí') {
                estadoElement.innerHTML = '<span class="status-badge status-success">Activo</span>';
                tipoAccesoElement.innerHTML = '<span class="status-badge bg-info">Privado y Público</span>';
            } else if (requerimientoData.privado === 'Sí' && requerimientoData.publico === 'No') {
                estadoElement.innerHTML = '<span class="status-badge bg-warning">Parcialmente Activo</span>';
                tipoAccesoElement.innerHTML = '<span class="status-badge bg-primary">Solo Privado</span>';
            } else if (requerimientoData.privado === 'No' && requerimientoData.publico === 'Sí') {
                estadoElement.innerHTML = '<span class="status-badge bg-warning">Parcialmente Activo</span>';
                tipoAccesoElement.innerHTML = '<span class="status-badge bg-success">Solo Público</span>';
            } else {
                estadoElement.innerHTML = '<span class="status-badge status-secondary">Inactivo</span>';
                tipoAccesoElement.innerHTML = '<span class="status-badge status-danger">Sin Acceso</span>';
            }
            
            // Configurar botón de editar
            document.getElementById('editReqBtn').href = `/requerimientos/${requerimientoData.id}/edit`;
            
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
    
    // Aplicar filtros iniciales
    aplicarFiltros();
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el requerimiento "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection
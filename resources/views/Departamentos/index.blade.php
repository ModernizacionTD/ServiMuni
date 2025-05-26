@extends('layouts.app')

@section('title', 'Gestión de Departamentos - Sistema de Gestión')

@section('page-title', 'Gestión de Departamentos')

@section('content')
<div class="table-view-container filter-view-container">
    <link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
    <link rel="stylesheet" href="{{ asset('css/button.css') }}">

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-sitemap me-2"></i>Lista de Departamentos
            </h3>
            <a href="{{ route('departamentos.create') }}" class="btn btn-header">
                <i class="fas fa-plus"></i> Nuevo Departamento
            </a>
        </div>
        
        <!-- Barra de filtros horizontal -->
        <div class="filters-bar">
            <div class="filters-container">
                <!-- Búsqueda -->
                <div class="filter-item search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control filter-search-input" placeholder="Buscar departamento...">
                    </div>
                </div>
                
                <!-- Filtro por estado -->
                <div class="filter-item">
                    <select id="estadoFilter" class="form-select filter-select">
                        <option value="">Todos los estados</option>
                        <option value="activo">Con requerimientos</option>
                        <option value="inactivo">Sin requerimientos</option>
                    </select>
                </div>
                
                <!-- Filtro por cantidad de requerimientos -->
                <div class="filter-item">
                    <select id="cantidadFilter" class="form-select filter-select">
                        <option value="">Todos</option>
                        <option value="0">Sin requerimientos</option>
                        <option value="1-5">1-5 requerimientos</option>
                        <option value="6-10">6-10 requerimientos</option>
                        <option value="11+">Más de 10</option>
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
                <table id="departamentosTable" class="table data-table">
                    <thead>
                        <tr>
                            <th width="15%">ID</th>
                            <th width="40%">Nombre del Departamento</th>
                            <th width="20%">Requerimientos Asociados</th>
                            <th width="25%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departamentos as $departamento)
                            @php
                                // Contar requerimientos asociados
                                $requerimientosCount = 0;
                                if(isset($requerimientos)) {
                                    foreach($requerimientos as $requerimiento) {
                                        if($requerimiento['departamento_id'] == $departamento['id']) {
                                            $requerimientosCount++;
                                        }
                                    }
                                }
                            @endphp
                            
                            <tr class="department-row" 
                                data-id="{{ $departamento['id'] }}"
                                data-nombre="{{ $departamento['nombre'] }}"
                                data-primera-letra="{{ strtoupper(substr($departamento['nombre'], 0, 1)) }}"
                                data-longitud="{{ strlen($departamento['nombre']) }}"
                                data-requerimientos-count="{{ $requerimientosCount }}"
                                data-estado="{{ $requerimientosCount > 0 ? 'activo' : 'inactivo' }}">
                                <td><strong>{{ $departamento['id'] }}</strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="department-icon">
                                            <i class="fas fa-sitemap"></i>
                                        </div>
                                        <div style="flex: 1;">
                                            <span class="department-name">{{ $departamento['nombre'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($requerimientosCount > 0)
                                        <span class="status-badge status-success">
                                            <i class="fas fa-clipboard-list me-1"></i>
                                            {{ $requerimientosCount }} requerimiento{{ $requerimientosCount > 1 ? 's' : '' }}
                                        </span>
                                    @else
                                        <span class="status-badge status-secondary">
                                            <i class="fas fa-circle-minus me-1"></i>
                                            Sin requerimientos
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="btn btn-sm btn-info action-btn btn-view view-details" title="Ver detalles" 
                                            data-id="{{ $departamento['id'] }}"
                                            data-nombre="{{ $departamento['nombre'] }}"
                                            data-requerimientos-count="{{ $requerimientosCount }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('departamentos.edit', $departamento['id']) }}" class="btn btn-sm btn-primary action-btn btn-edit" title="Editar departamento">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($requerimientosCount == 0)
                                            <button type="button" class="btn btn-sm btn-danger action-btn btn-delete" title="Eliminar" 
                                                onclick="confirmarEliminacion('{{ $departamento['id'] }}', '{{ $departamento['nombre'] }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            
                                            <form id="delete-form-{{ $departamento['id'] }}" action="{{ route('departamentos.destroy', $departamento['id']) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-secondary action-btn" title="No se puede eliminar: tiene requerimientos asociados" disabled>
                                                <i class="fas fa-shield-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="table-empty-state">
                                        <i class="fas fa-sitemap"></i>
                                        <p class="table-empty-state-text">No hay departamentos registrados en el sistema</p>
                                        <a href="{{ route('departamentos.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Añadir Departamento
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
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($departamentos) }}</span> de <span class="fw-bold">{{ count($departamentos) }}</span> departamentos
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del departamento -->
    <div class="user-details-panel details-panel" id="userDetailsPanel">
        <div class="user-details-modal">
            <div class="user-details-header">
                <h3><i class="fas fa-sitemap me-2"></i>Detalles del Departamento</h3>
                <button type="button" class="detail-close-btn" id="closeDetailsBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="user-profile-header">
                <div class="user-avatar">
                    <i class="fas fa-sitemap"></i>
                </div>
                <div class="user-info">
                    <h3 id="deptNombre"></h3>
                    <p id="deptInfo" class="user-type"></p>
                </div>
            </div>
            
            <div class="user-details-container">
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-info-circle"></i> Información del Departamento</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">ID</span>
                            <span class="detail-value" id="deptId"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nombre</span>
                            <span class="detail-value" id="deptNombreDetail"></span>
                        </div>
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Descripción</span>
                            <span class="detail-value">Departamento municipal encargado de gestionar diversos requerimientos y servicios ciudadanos.</span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-clipboard-list"></i> Requerimientos Asociados</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Cantidad de Requerimientos</span>
                            <span class="detail-value" id="deptRequerimientosCount"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Estado</span>
                            <span class="detail-value" id="deptEstado"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Capacidad</span>
                            <span class="detail-value" id="deptCapacidad"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Prioridad</span>
                            <span class="detail-value" id="deptPrioridad"></span>
                        </div>
                    </div>
                    <div class="mt-3" id="requerimientosList">
                        <!-- Aquí se mostrarán los requerimientos asociados -->
                    </div>
                </div>
            </div>
            
            <div class="panel-actions">
                <button type="button" class="btn btn-secondary" id="closePanelBtn">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="editDeptBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Departamento
                </a>
                <a href="{{ route('requerimientos.create') }}" class="btn btn-success" id="addRequerimientoBtn">
                    <i class="fas fa-plus"></i> Añadir Requerimiento
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.department-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(56, 103, 214, 0.1);
    border-radius: 6px;
    color: var(--primary-color);
    flex-shrink: 0;
}

.department-name {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9rem;
    line-height: 1.3;
}

.requerimiento-item {
    background-color: var(--bg-light);
    padding: 8px 12px;
    border-radius: 6px;
    margin-bottom: 8px;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.requerimiento-item i {
    color: var(--primary-color);
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
    // Función para verificar si la cantidad está en el rango
    function cantidadEnRango(cantidad, rango) {
        if (rango === '0') return cantidad === 0;
        if (rango === '1-5') return cantidad >= 1 && cantidad <= 5;
        if (rango === '6-10') return cantidad >= 6 && cantidad <= 10;
        if (rango === '11+') return cantidad > 10;
        return true;
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const estadoValue = document.getElementById('estadoFilter').value;
        const cantidadValue = document.getElementById('cantidadFilter').value;
        
        const rows = document.querySelectorAll('.department-row');
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
            
            // Filtro por estado
            if (estadoValue && row.getAttribute('data-estado') !== estadoValue) {
                mostrar = false;
            }
            
            // Filtro por cantidad de requerimientos
            if (cantidadValue) {
                const cantidad = parseInt(row.getAttribute('data-requerimientos-count'));
                if (!cantidadEnRango(cantidad, cantidadValue)) {
                    mostrar = false;
                }
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
        
        // Estado
        const estado = document.getElementById('estadoFilter');
        if (estado.value) {
            agregarChipFiltro(container, 'Estado', estado.options[estado.selectedIndex].text, () => {
                estado.value = '';
                aplicarFiltros();
            });
        }
        
        // Cantidad
        const cantidad = document.getElementById('cantidadFilter');
        if (cantidad.value) {
            agregarChipFiltro(container, 'Cantidad', cantidad.options[cantidad.selectedIndex].text, () => {
                cantidad.value = '';
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
    document.getElementById('estadoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('cantidadFilter').addEventListener('change', aplicarFiltros);
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('estadoFilter').value = '';
        document.getElementById('cantidadFilter').value = '';
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
            
            // Extraer datos del departamento
            const departamentoData = {
                id: this.getAttribute('data-id'),
                nombre: this.getAttribute('data-nombre'),
                requerimientosCount: parseInt(this.getAttribute('data-requerimientos-count'))
            };
            
            // Llenar datos en el panel
            document.getElementById('deptNombre').textContent = departamentoData.nombre;
            document.getElementById('deptInfo').textContent = `${departamentoData.requerimientosCount} requerimiento${departamentoData.requerimientosCount !== 1 ? 's' : ''}`;
            document.getElementById('deptId').textContent = departamentoData.id;
            document.getElementById('deptNombreDetail').textContent = departamentoData.nombre;
            document.getElementById('deptRequerimientosCount').textContent = departamentoData.requerimientosCount;
            
            // Estado del departamento
            const estadoElement = document.getElementById('deptEstado');
            if (departamentoData.requerimientosCount > 0) {
                estadoElement.innerHTML = '<span class="status-badge status-success"><i class="fas fa-check-circle"></i> Activo</span>';
            } else {
                estadoElement.innerHTML = '<span class="status-badge status-secondary"><i class="fas fa-pause-circle"></i> Sin requerimientos</span>';
            }
            
            // Capacidad y prioridad (simulados)
            const capacidadElement = document.getElementById('deptCapacidad');
            const prioridadElement = document.getElementById('deptPrioridad');
            
            if (departamentoData.requerimientosCount <= 5) {
                capacidadElement.innerHTML = '<span class="status-badge status-success">Baja carga</span>';
                prioridadElement.innerHTML = '<span class="status-badge status-success">Normal</span>';
            } else if (departamentoData.requerimientosCount <= 10) {
                capacidadElement.innerHTML = '<span class="status-badge bg-warning">Carga media</span>';
                prioridadElement.innerHTML = '<span class="status-badge bg-warning">Media</span>';
            } else {
                capacidadElement.innerHTML = '<span class="status-badge status-danger">Alta carga</span>';
                prioridadElement.innerHTML = '<span class="status-badge status-danger">Alta</span>';
            }
            
            // Mostrar lista de requerimientos (simulada)
            const requerimientosList = document.getElementById('requerimientosList');
            requerimientosList.innerHTML = '';
            
            if (departamentoData.requerimientosCount > 0) {
                // Aquí deberías obtener los requerimientos reales del departamento
                // Por ahora mostramos un mensaje genérico
                for (let i = 1; i <= Math.min(departamentoData.requerimientosCount, 3); i++) {
                    const reqItem = document.createElement('div');
                    reqItem.className = 'requerimiento-item';
                    reqItem.innerHTML = `
                        <i class="fas fa-clipboard-list"></i>
                        Requerimiento #${i} del departamento
                    `;
                    requerimientosList.appendChild(reqItem);
                }
                
                if (departamentoData.requerimientosCount > 3) {
                    const moreItem = document.createElement('div');
                    moreItem.className = 'requerimiento-item';
                    moreItem.innerHTML = `
                        <i class="fas fa-ellipsis-h"></i>
                        Y ${departamentoData.requerimientosCount - 3} requerimiento${departamentoData.requerimientosCount - 3 > 1 ? 's' : ''} más...
                    `;
                    requerimientosList.appendChild(moreItem);
                }
            } else {
                requerimientosList.innerHTML = '<p class="text-muted">Este departamento no tiene requerimientos asociados.</p>';
            }
            
            // Configurar botón de editar
            document.getElementById('editDeptBtn').href = `/departamentos/${departamentoData.id}/edit`;
            
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
    if (confirm(`¿Estás seguro de que deseas eliminar el departamento "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection
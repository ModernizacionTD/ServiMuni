@extends('layouts.app')

@section('title', 'Gestión de Requerimientos - Sistema de Gestión')

@section('page-title', 'Gestión de Requerimientos')

@section('content')

<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
<link rel="stylesheet" href="{{ asset('css/filtros.css') }}">

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lista de Requerimientos</h3>
        <a href="{{ route('requerimientos.create') }}" class="btn btn-add">
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar requerimiento...">
                </div>
            </div>
            
            <!-- Filtro por departamento -->
            <div class="filter-item">
                <select id="departamentoFilter" class="form-select">
                    <option value="">Todos los departamentos</option>
                    @foreach($departamentos as $departamento)
                        <option value="{{ $departamento['id'] }}">{{ $departamento['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filtro por privado -->
            <div class="filter-item">
                <select id="privadoFilter" class="form-select">
                    <option value="">Filtrar por privado</option>
                    <option value="1">Privado</option>
                    <option value="0">No privado</option>
                </select>
            </div>
            
            <!-- Filtro por público -->
            <div class="filter-item">
                <select id="publicoFilter" class="form-select">
                    <option value="">Filtrar por público</option>
                    <option value="1">Público</option>
                    <option value="0">No público</option>
                </select>
            </div>
            
            <!-- Filtro por disponibilidad -->
            <div class="filter-item">
                <select id="disponibilidadFilter" class="form-select">
                    <option value="">Filtrar por disponibilidad</option>
                    <option value="ambos">Privado y Público</option>
                    <option value="solo_privado">Solo Privado</option>
                    <option value="solo_publico">Solo Público</option>
                    <option value="ninguno">Ninguno</option>
                </select>
            </div>
            
            <!-- Botón reset -->
            <div class="filter-item">
                <button type="button" class="btn btn-outline-secondary" id="clearFiltersBtn">
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
            <table id="requerimientosTable" class="table">
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
                        <tr class="user-row" 
                            data-departamento-id="{{ $requerimiento['departamento_id'] }}"
                            data-privado="{{ $requerimiento['privado'] ? '1' : '0' }}"
                            data-publico="{{ $requerimiento['publico'] ? '1' : '0' }}">
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
                            <td>{{ $requerimiento['nombre'] }}</td>
                            <td class="address-cell">{{ $requerimiento['descripcion_req'] }}</td>
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
                                    <button type="button" class="btn btn-sm btn-info view-details" title="Ver detalles" 
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
                                    
                                    <a href="{{ route('requerimientos.edit', $requerimiento['id_requerimiento']) }}" class="btn btn-sm btn-primary" title="Editar requerimiento">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
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
                            <td colspan="7">
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
        
        <div class="pagination-container mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagination-info">
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($requerimientos) }}</span> de <span class="fw-bold">{{ count($requerimientos) }}</span> requerimientos
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalles del requerimiento -->
<div class="user-details-panel" id="userDetailsPanel">
    <div class="user-details-modal">
        <div class="user-details-header">
            <h3><i class="fas fa-clipboard-list me-2"></i>Detalles del Requerimiento</h3>
            <button type="button" class="btn" id="closeDetailsBtn">
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
            <button type="button" class="btn btn-secondary" id="closePanelBtn">
                <i class="fas fa-times"></i> Cerrar
            </button>
            <a href="#" id="editReqBtn" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Requerimiento
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const departamentoValue = document.getElementById('departamentoFilter').value;
        const privadoValue = document.getElementById('privadoFilter').value;
        const publicoValue = document.getElementById('publicoFilter').value;
        const disponibilidadValue = document.getElementById('disponibilidadFilter').value;
        
        const rows = document.querySelectorAll('.user-row');
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
            
            // Filtro por privado
            if (privadoValue && row.getAttribute('data-privado') !== privadoValue) {
                mostrar = false;
            }
            
            // Filtro por público
            if (publicoValue && row.getAttribute('data-publico') !== publicoValue) {
                mostrar = false;
            }
            
            // Filtro por disponibilidad
            if (disponibilidadValue) {
                const privado = row.getAttribute('data-privado') === '1';
                const publico = row.getAttribute('data-publico') === '1';
                
                switch(disponibilidadValue) {
                    case 'ambos':
                        if (!(privado && publico)) mostrar = false;
                        break;
                    case 'solo_privado':
                        if (!(privado && !publico)) mostrar = false;
                        break;
                    case 'solo_publico':
                        if (!(!privado && publico)) mostrar = false;
                        break;
                    case 'ninguno':
                        if (!((!privado && !publico))) mostrar = false;
                        break;
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
        
        // Departamento
        const departamento = document.getElementById('departamentoFilter');
        if (departamento.value) {
            agregarChipFiltro(container, 'Departamento', departamento.options[departamento.selectedIndex].text, () => {
                departamento.value = '';
                aplicarFiltros();
            });
        }
        
        // Privado
        const privado = document.getElementById('privadoFilter');
        if (privado.value) {
            agregarChipFiltro(container, 'Privado', privado.options[privado.selectedIndex].text, () => {
                privado.value = '';
                aplicarFiltros();
            });
        }
        
        // Público
        const publico = document.getElementById('publicoFilter');
        if (publico.value) {
            agregarChipFiltro(container, 'Público', publico.options[publico.selectedIndex].text, () => {
                publico.value = '';
                aplicarFiltros();
            });
        }
        
        // Disponibilidad
        const disponibilidad = document.getElementById('disponibilidadFilter');
        if (disponibilidad.value) {
            agregarChipFiltro(container, 'Disponibilidad', disponibilidad.options[disponibilidad.selectedIndex].text, () => {
                disponibilidad.value = '';
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
    document.getElementById('privadoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('publicoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('disponibilidadFilter').addEventListener('change', aplicarFiltros);
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('departamentoFilter').value = '';
        document.getElementById('privadoFilter').value = '';
        document.getElementById('publicoFilter').value = '';
        document.getElementById('disponibilidadFilter').value = '';
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
            document.getElementById('reqDescripcion').textContent = requerimientoData.descripcionReq;
            document.getElementById('reqPrecio').textContent = requerimientoData.descripcionPrecio;
            document.getElementById('reqPrivado').textContent = requerimientoData.privado;
            document.getElementById('reqPublico').textContent = requerimientoData.publico;
            
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
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el requerimiento "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection
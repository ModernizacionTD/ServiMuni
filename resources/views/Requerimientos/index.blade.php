@extends('layouts.app')

@section('title', 'Gestión de Requerimientos - Sistema de Gestión')

@section('page-title', 'Gestión de Requerimientos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
<link rel="stylesheet" href="{{ asset('css/filtros.css') }}">


<div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Lista de Requerimientos</h3>
        <a href="{{ route('requerimientos.create') }}" class="btn btn-primary">
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Buscar usuario...">
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
            
            <!-- Filtro por tipo ubicación privado -->
            <div class="filter-item">
                <select id="privadoFilter" class="form-select">
                    <option value="">Filtrar por privado</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <!-- Filtro por tipo ubicación pública -->
            <div class="filter-item">
                <select id="publicoFilter" class="form-select">
                    <option value="">Filtrar por público</option>
                    <option value="1">Sí</option>
                    <option value="0">No</option>
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
                        <tr class="user-row" 
                            data-departamento-id="{{ $requerimiento['departamento_id'] }}"
                            data-privado="{{ $requerimiento['privado'] ? '1' : '0' }}"
                            data-publico="{{ $requerimiento['publico'] ? '1' : '0' }}">
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
    console.log("DOM cargado, inicializando filtros...");
    
    // Variables para los filtros
    const searchInput = document.getElementById('searchInput');
    const departamentoFilter = document.getElementById('departamentoFilter');
    const privadoFilter = document.getElementById('privadoFilter');
    const publicoFilter = document.getElementById('publicoFilter');
    const resetFiltersBtn = document.getElementById('resetFilters');
    const rows = document.querySelectorAll('.user-row');
    
    // Verificar que los elementos existen
    console.log("Elementos de filtro encontrados:", {
        searchInput: !!searchInput,
        departamentoFilter: !!departamentoFilter,
        privadoFilter: !!privadoFilter,
        publicoFilter: !!publicoFilter,
        resetFiltersBtn: !!resetFiltersBtn,
        rowsCount: rows.length
    });
    
    // Verificar que los datos están presentes en las filas
    if (rows.length > 0) {
        const firstRow = rows[0];
        console.log("Ejemplo de datos de fila:", {
            departamentoId: firstRow.getAttribute('data-departamento-id'),
            privado: firstRow.getAttribute('data-privado'),
            publico: firstRow.getAttribute('data-publico')
        });
    }
    
    // Función para aplicar todos los filtros
    function applyFilters() {
        console.log("Aplicando filtros...");
        
        const searchValue = searchInput ? searchInput.value.toLowerCase() : '';
        const departamentoValue = departamentoFilter ? departamentoFilter.value : '';
        const privadoValue = privadoFilter ? privadoFilter.value : '';
        const publicoValue = publicoFilter ? publicoFilter.value : '';
        
        console.log("Filtros actuales:", {
            search: searchValue,
            departamento: departamentoValue,
            privado: privadoValue,
            publico: publicoValue
        });
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const departamentoId = row.getAttribute('data-departamento-id');
            const privado = row.getAttribute('data-privado');
            const publico = row.getAttribute('data-publico');
            
            // Comprobar si la fila cumple con todos los filtros activos
            const matchesSearch = searchValue === '' || textContent.includes(searchValue);
            const matchesDepartamento = departamentoValue === '' || departamentoId === departamentoValue;
            const matchesPrivado = privadoValue === '' || privado === privadoValue;
            const matchesPublico = publicoValue === '' || publico === publicoValue;
            
            const shouldDisplay = matchesSearch && matchesDepartamento && matchesPrivado && matchesPublico;
            
            // Para depuración, solo mostrar unas pocas filas
            if (visibleCount < 3 || !shouldDisplay) {
                console.log(`Fila ${visibleCount+1}:`, {
                    departamentoId,
                    privado,
                    publico,
                    matchesSearch,
                    matchesDepartamento, 
                    matchesPrivado,
                    matchesPublico,
                    shouldDisplay
                });
            }
            
            // Mostrar u ocultar fila según los filtros
            row.style.display = shouldDisplay ? '' : 'none';
            
            if (shouldDisplay) {
                visibleCount++;
            }
        });
        
        console.log(`Total de filas visibles: ${visibleCount} de ${rows.length}`);
        
        // Actualizar contador de resultados
        updateResultCount();
    }
    
    // Función para actualizar el contador de resultados visibles
    function updateResultCount() {
        // Contar filas que no están ocultas
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none').length;
        const paginationInfo = document.querySelector('.pagination-info');
        
        if (paginationInfo) {
            paginationInfo.innerHTML = `Mostrando <span class="fw-bold">${visibleRows}</span> de <span class="fw-bold">${rows.length}</span> requerimientos`;
        }
    }
    
    // Función para restablecer todos los filtros
    function resetFilters() {
        console.log("Restableciendo filtros...");
        
        if (searchInput) searchInput.value = '';
        if (departamentoFilter) departamentoFilter.value = '';
        if (privadoFilter) privadoFilter.value = '';
        if (publicoFilter) publicoFilter.value = '';
        
        rows.forEach(row => {
            row.style.display = '';
        });
        
        updateResultCount();
    }
    
    // Vincular eventos
    if (searchInput) {
        console.log("Vinculando evento 'input' al campo de búsqueda");
        searchInput.addEventListener('input', function() {
            console.log("Evento input detectado en búsqueda:", this.value);
            applyFilters();
        });
    }
    
    if (departamentoFilter) {
        console.log("Vinculando evento 'change' al filtro de departamento");
        departamentoFilter.addEventListener('change', function() {
            console.log("Evento change detectado en departamento:", this.value);
            applyFilters();
        });
    }
    
    if (privadoFilter) {
        console.log("Vinculando evento 'change' al filtro de privado");
        privadoFilter.addEventListener('change', function() {
            console.log("Evento change detectado en privado:", this.value);
            applyFilters();
        });
    }
    
    if (publicoFilter) {
        console.log("Vinculando evento 'change' al filtro de público");
        publicoFilter.addEventListener('change', function() {
            console.log("Evento change detectado en público:", this.value);
            applyFilters();
        });
    }
    
    if (resetFiltersBtn) {
        console.log("Vinculando evento 'click' al botón de reset");
        resetFiltersBtn.addEventListener('click', resetFilters);
    }
    
    // Inicializar el contador
    updateResultCount();
    
    console.log("Inicialización de filtros completada");
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'Gestión de Unidades - ServiMuni')

@section('page-title', 'Gestión de Unidades')

@section('content')
<div class="table-view-container filter-view-container">
    <link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
    <link rel="stylesheet" href="{{ asset('css/button.css') }}">

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-building me-2"></i>Lista de Unidades
            </h3>
            <a href="{{ route('unidades.create') }}" class="btn btn-header">
                <i class="fas fa-plus"></i> Nueva Unidad
            </a>
        </div>
        
        <!-- Barra de filtros horizontal -->
        <div class="filters-bar">
            <div class="filters-container">
                <!-- Búsqueda -->
                <div class="filter-item search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control filter-search-input" placeholder="Buscar unidad...">
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
                <table id="unidadesTable" class="table data-table">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="35%">Nombre</th>
                            <th width="35%">Departamento</th>
                            <th width="20%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($unidades as $unidad)
                            <tr class="unidad-row" 
                                data-id="{{ $unidad['id_unidad'] }}"
                                data-nombre="{{ $unidad['nombre'] }}"
                                data-departamento-id="{{ $unidad['departamento_id'] }}">
                                <td><strong>{{ $unidad['id_unidad'] }}</strong></td>
                                <td>
                                    <div class="unidad-nombre">
                                        <i class="fas fa-building text-primary me-2"></i>
                                        <strong>{{ $unidad['nombre'] }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $departamentoNombre = 'Sin asignar';
                                        foreach($departamentos as $departamento) {
                                            if($departamento['id'] == $unidad['departamento_id']) {
                                                $departamentoNombre = $departamento['nombre'];
                                                break;
                                            }
                                        }
                                    @endphp
                                    <span class="persona-badge persona-natural">{{ $departamentoNombre }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="btn btn-sm btn-info action-btn btn-view view-details" title="Ver detalles" 
                                            data-id="{{ $unidad['id_unidad'] }}"
                                            data-nombre="{{ $unidad['nombre'] }}"
                                            data-departamento-id="{{ $unidad['departamento_id'] }}"
                                            data-departamento-nombre="{{ $departamentoNombre }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('unidades.edit', $unidad['id_unidad']) }}" class="btn btn-sm btn-primary action-btn btn-edit" title="Editar unidad">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-danger action-btn btn-delete" title="Eliminar unidad" 
                                            onclick="confirmarEliminacion('{{ $unidad['id_unidad'] }}', '{{ $unidad['nombre'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $unidad['id_unidad'] }}" action="{{ route('unidades.destroy', $unidad['id_unidad']) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="table-empty-state">
                                        <i class="fas fa-building"></i>
                                        <p class="table-empty-state-text">No hay unidades registradas en el sistema</p>
                                        <a href="{{ route('unidades.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Añadir Unidad
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
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($unidades) }}</span> de <span class="fw-bold">{{ count($unidades) }}</span> unidades
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles de la unidad -->
    <div class="user-details-panel details-panel" id="unidadDetailsPanel">
        <div class="user-details-modal">
            <div class="user-details-header">
                <h3><i class="fas fa-building me-2"></i>Detalles de la Unidad</h3>
                <button type="button" class="detail-close-btn" id="closeDetailsBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="user-profile-header">
                <div class="user-avatar">
                    <i class="fas fa-building"></i>
                </div>
                <div class="user-info">
                    <h3 id="unidadNombre"></h3>
                    <p id="unidadDepartamento" class="user-type"></p>
                </div>
            </div>
            
            <div class="user-details-container">
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-info-circle"></i> Información General</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">ID de la Unidad</span>
                            <span class="detail-value" id="unidadId"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nombre</span>
                            <span class="detail-value" id="unidadNombreDetail"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Departamento</span>
                            <span class="detail-value" id="unidadDepartamentoDetail"></span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-users"></i> Funcionarios Asignados</h4>
                    <div id="tecnicosContainer" class="details-list">
                        <div class="details-loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando Funcionarios...
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel-actions">
                <button type="button" class="btn btn-secondary" id="closePanelBtn">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="editUnidadBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Unidad
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.unidad-nombre {
    display: flex;
    align-items: center;
}

.details-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.details-list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 6px;
    background-color: var(--bg-light);
}

.details-list-item i {
    color: var(--primary-color);
}

.details-list-empty {
    padding: 15px;
    text-align: center;
    background-color: var(--bg-light);
    border-radius: 6px;
    color: var(--text-light);
}

.details-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    color: var(--text-light);
}

.details-loading i {
    margin-right: 10px;
}

/* Badges para roles de funcionarios */
.badge {
    display: inline-block;
    padding: 4px 8px;
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: 4px;
    text-transform: uppercase;
}

.badge-admin {
    background-color: #dc2626;
    color: white;
}

.badge-desarrollador {
    background-color: #7c3aed;
    color: white;
}

.badge-orientador {
    background-color: #059669;
    color: white;
}

.badge-gestor {
    background-color: #0ea5e9;
    color: white;
}

.badge-tecnico {
    background-color: #ea580c;
    color: white;
}

/* Mejorar el espaciado de los elementos de funcionarios */
.details-list-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border-radius: 6px;
    background-color: var(--bg-light);
    margin-bottom: 8px;
}

.details-list-item i {
    color: var(--primary-color);
    margin-top: 2px;
    font-size: 1.1rem;
}

.details-list-item > div {
    flex: 1;
}

.details-list-item strong {
    display: block;
    margin-bottom: 4px;
}

.details-list-item small {
    color: var(--text-light);
    display: block;
    margin-bottom: 6px;
}

/* Headers de secciones en el modal */
.details-section h6 {
    color: #0ea5e9;
    font-weight: 600;
    border-bottom: 2px solid #e0f2fe;
    padding-bottom: 5px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const departamentoValue = document.getElementById('departamentoFilter').value;
        
        const rows = document.querySelectorAll('.unidad-row');
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
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('departamentoFilter').value = '';
        aplicarFiltros();
    });
    
    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES =====
    const unidadDetailsPanel = document.getElementById('unidadDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closeDetailsBtn = document.getElementById('closeDetailsBtn');
    const closePanelBtn = document.getElementById('closePanelBtn');
    
    function showUnidadDetails() {
        unidadDetailsPanel.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideUnidadDetails() {
        unidadDetailsPanel.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Cargar funcionarios de una unidad (CORREGIDO)
    async function cargarFuncionarios(unidadId) {
        const tecnicosContainer = document.getElementById('tecnicosContainer');
        tecnicosContainer.innerHTML = `
            <div class="details-loading">
                <i class="fas fa-spinner fa-spin"></i> Cargando funcionarios...
            </div>
        `;
        
        try {
            console.log('Cargando funcionarios para unidad ID:', unidadId);
            
            // CAMBIAMOS LA URL DE LA API
            const response = await fetch(`/api/unidades/${unidadId}/funcionarios`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const funcionarios = await response.json();
            console.log('Funcionarios obtenidos:', funcionarios);
            
            if (funcionarios.length === 0) {
                tecnicosContainer.innerHTML = `
                    <div class="details-list-empty">
                        <i class="fas fa-info-circle"></i> No hay funcionarios asignados a esta unidad
                    </div>
                `;
                return;
            }
            
            // Mostrar la lista de funcionarios (TODOS, no solo técnicos)
            tecnicosContainer.innerHTML = '';
            
            // Separar por roles para mejor visualización
            const tecnicos = funcionarios.filter(f => f.rol === 'tecnico');
            const gestores = funcionarios.filter(f => f.rol === 'gestor');
            const otros = funcionarios.filter(f => f.rol !== 'tecnico' && f.rol !== 'gestor');
            
            // Función para crear elemento de funcionario
            function crearElementoFuncionario(funcionario) {
                const funcionarioItem = document.createElement('div');
                funcionarioItem.className = 'details-list-item';
                
                // Icono según el rol
                let icono = 'fas fa-user';
                if (funcionario.rol === 'tecnico') icono = 'fas fa-user-cog';
                else if (funcionario.rol === 'gestor') icono = 'fas fa-user-tie';
                else if (funcionario.rol === 'admin') icono = 'fas fa-user-shield';
                
                funcionarioItem.innerHTML = `
                    <i class="${icono}"></i>
                    <div>
                        <strong>${funcionario.nombre}</strong>
                        <div><small>${funcionario.email}</small></div>
                        <div><span class="badge badge-${funcionario.rol}">${formatearRol(funcionario.rol)}</span></div>
                    </div>
                `;
                return funcionarioItem;
            }
            
            // Función para formatear rol
            function formatearRol(rol) {
                const roles = {
                    'admin': 'Administrador',
                    'desarrollador': 'Desarrollador',
                    'orientador': 'Orientador',
                    'gestor': 'Gestor',
                    'tecnico': 'Técnico'
                };
                return roles[rol] || rol;
            }
            
            // Agregar técnicos
            if (tecnicos.length > 0) {
                const tecnicosHeader = document.createElement('h6');
                tecnicosHeader.innerHTML = '<i class="fas fa-user-cog"></i> Técnicos';
                tecnicosHeader.style.marginTop = '15px';
                tecnicosHeader.style.marginBottom = '10px';
                tecnicosHeader.style.color = '#0ea5e9';
                tecnicosContainer.appendChild(tecnicosHeader);
                
                tecnicos.forEach(tecnico => {
                    tecnicosContainer.appendChild(crearElementoFuncionario(tecnico));
                });
            }
            
            // Agregar gestores
            if (gestores.length > 0) {
                const gestoresHeader = document.createElement('h6');
                gestoresHeader.innerHTML = '<i class="fas fa-user-tie"></i> Gestores';
                gestoresHeader.style.marginTop = '15px';
                gestoresHeader.style.marginBottom = '10px';
                gestoresHeader.style.color = '#0ea5e9';
                tecnicosContainer.appendChild(gestoresHeader);
                
                gestores.forEach(gestor => {
                    tecnicosContainer.appendChild(crearElementoFuncionario(gestor));
                });
            }
            
            // Agregar otros roles
            if (otros.length > 0) {
                const otrosHeader = document.createElement('h6');
                otrosHeader.innerHTML = '<i class="fas fa-users"></i> Otros Funcionarios';
                otrosHeader.style.marginTop = '15px';
                otrosHeader.style.marginBottom = '10px';
                otrosHeader.style.color = '#0ea5e9';
                tecnicosContainer.appendChild(otrosHeader);
                
                otros.forEach(funcionario => {
                    tecnicosContainer.appendChild(crearElementoFuncionario(funcionario));
                });
            }
            
        } catch (error) {
            console.error('Error completo:', error);
            tecnicosContainer.innerHTML = `
                <div class="details-list-empty">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <div>
                        <strong>Error al cargar funcionarios</strong>
                        <div><small>${error.message}</small></div>
                    </div>
                </div>
            `;
        }
    }
    
    // Mostrar panel de detalles al hacer clic en el ojo
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Extraer datos de la unidad
            const unidadData = {
                id: this.getAttribute('data-id'),
                nombre: this.getAttribute('data-nombre'),
                departamentoId: this.getAttribute('data-departamento-id'),
                departamentoNombre: this.getAttribute('data-departamento-nombre')
            };
            
            console.log('Datos de la unidad:', unidadData);
            
            // Llenar datos en el panel
            document.getElementById('unidadNombre').textContent = unidadData.nombre;
            document.getElementById('unidadDepartamento').textContent = unidadData.departamentoNombre;
            document.getElementById('unidadId').textContent = unidadData.id;
            document.getElementById('unidadNombreDetail').textContent = unidadData.nombre;
            document.getElementById('unidadDepartamentoDetail').textContent = unidadData.departamentoNombre;
            
            // Cargar funcionarios asignados a la unidad
            cargarFuncionarios(unidadData.id);
            
            // Configurar botón de editar
            document.getElementById('editUnidadBtn').href = `/unidades/${unidadData.id}/edit`;
            
            // Mostrar el panel
            showUnidadDetails();
        });
    });
    
    // Cerrar panel de detalles
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', hideUnidadDetails);
    }
    
    if (closePanelBtn) {
        closePanelBtn.addEventListener('click', hideUnidadDetails);
    }
    
    // Cerrar al hacer clic en el fondo del modal
    if (unidadDetailsPanel) {
        unidadDetailsPanel.addEventListener('click', function(event) {
            if (event.target === unidadDetailsPanel) {
                hideUnidadDetails();
            }
        });
    }
    
    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && unidadDetailsPanel.classList.contains('show')) {
            hideUnidadDetails();
        }
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar la unidad "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}
</script>
@endsection

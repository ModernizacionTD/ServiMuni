@extends('layouts.app')

@section('title', 'Editar Unidad - ServiMuni')

@section('page-title', 'Editar Unidad')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="form-view-container">
    <div class="card">
        <div class="form-card-header">
            <h2 class="form-card-title">
                <i class="fas fa-building-circle-check"></i>Editar Unidad
            </h2>
            <div class="form-header-actions">
                <a href="{{ route('unidades.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="form-card-body">
            @if(session('error'))
                <div class="form-alert form-alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                </div>
            @endif
            
            @if(session('success'))
                <div class="form-alert form-alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Éxito:</strong> {{ session('success') }}
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('unidades.update', $unidad['id_unidad']) }}" id="unidadEditForm" novalidate>
                @csrf
                @method('PUT')
                
                <!-- Sección: Información Básica -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-info-circle"></i>Información de la Unidad
                    </h3>
                    
                    <div class="form-group">
                        <label for="id_unidad" class="form-label">ID de la Unidad</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-hashtag"></i>
                            </span>
                            <input type="text" 
                                   id="id_unidad" 
                                   class="form-control" 
                                   value="{{ $unidad['id_unidad'] }}" 
                                   readonly
                                   disabled>
                        </div>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            El ID de la unidad no puede ser modificado
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre" class="form-label required">Nombre de la Unidad</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-building"></i>
                            </span>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $unidad['nombre']) }}" 
                                   placeholder="Nombre de la unidad o sección"
                                   required>
                        </div>
                        @error('nombre')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Ingrese un nombre descriptivo para la unidad
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="departamento_id" class="form-label required">Departamento</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-sitemap"></i>
                            </span>
                            <select id="departamento_id" 
                                    name="departamento_id" 
                                    class="form-select @error('departamento_id') is-invalid @enderror" 
                                    required>
                                <option value="">Seleccione un departamento...</option>
                                @foreach($departamentos as $departamento)
                                    <option value="{{ $departamento['id'] }}" {{ old('departamento_id', $unidad['departamento_id']) == $departamento['id'] ? 'selected' : '' }}>
                                        {{ $departamento['nombre'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('departamento_id')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Seleccione el departamento al que pertenece esta unidad
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Gestión de Funcionarios -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-users"></i>Gestión de Funcionarios
                    </h3>
                    
                    <div class="funcionarios-management-container">
                        <div class="funcionarios-tabs">
                            <div class="tab-header">
                                <button type="button" class="tab-btn active" data-tab="current-tab">
                                    <i class="fas fa-user-check"></i> Funcionarios Asignados
                                </button>
                                <button type="button" class="tab-btn" data-tab="add-tab">
                                    <i class="fas fa-user-plus"></i> Agregar Funcionarios
                                </button>
                            </div>
                            
                            <div class="tab-content">
                                <!-- Tab: Funcionarios Actuales -->
                                <div class="tab-pane active" id="current-tab">
                                    <div class="current-funcionarios-container">
                                        <div class="funcionarios-toolbar">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">
                                                    <i class="fas fa-filter"></i>
                                                </span>
                                                <input type="text" 
                                                       id="currentSearch" 
                                                       class="form-control" 
                                                       placeholder="Buscar funcionario...">
                                                <button type="button" class="btn btn-outline-secondary" id="clearCurrentSearch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="current-funcionarios-list" id="currentFuncionariosList">
                                            <div class="funcionarios-loading">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando funcionarios asignados...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tab: Agregar Funcionarios -->
                                <div class="tab-pane" id="add-tab">
                                    <div class="add-funcionarios-container">
                                        <div class="funcionarios-toolbar">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                                <input type="text" 
                                                       id="addSearch" 
                                                       class="form-control" 
                                                       placeholder="Buscar funcionario por nombre o email...">
                                                <button type="button" class="btn btn-outline-secondary" id="clearAddSearch">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            
                                            <div class="funcionarios-filters mb-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showAllFuncionarios" checked>
                                                    <label class="form-check-label" for="showAllFuncionarios">
                                                        Todos los funcionarios
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showTecnicos">
                                                    <label class="form-check-label" for="showTecnicos">
                                                        Solo técnicos
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" id="showGestores">
                                                    <label class="form-check-label" for="showGestores">
                                                        Solo gestores
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="add-funcionarios-list" id="addFuncionariosList">
                                            <div class="funcionarios-loading">
                                                <i class="fas fa-spinner fa-spin"></i> Cargando funcionarios disponibles...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="hiddenFuncionariosInputs"></div>
                    </div>
                </div>
                
                <!-- Acciones del Formulario -->
                <div class="form-actions">
                    <a href="{{ route('unidades.index') }}" class="form-btn form-btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    
                    <button type="submit" class="form-btn form-btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> 
                        <span>Actualizar Unidad</span>
                        <div class="form-spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para el formulario de edición de unidades */
.form-card-header {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px 8px 0 0;
}

.form-card-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-header-actions .btn {
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    transition: all 0.2s;
}

.form-header-actions .btn:hover {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
}

.form-alert-success {
    background-color: #ecfdf5;
    border-left: 4px solid #10b981;
    color: #065f46;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-success i {
    color: #10b981;
    font-size: 1.2rem;
    margin-top: 2px;
}

.form-alert-danger {
    background-color: #fef2f2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-danger i {
    color: #ef4444;
    font-size: 1.2rem;
    margin-top: 2px;
}

.form-alert-info {
    background-color: #eff6ff;
    border-left: 4px solid #3b82f6;
    color: #1e40af;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-info i {
    color: #3b82f6;
    font-size: 1.2rem;
    margin-top: 2px;
}

/* Destacar que es un formulario de edición */
.form-view-container .card {
    border-top: 4px solid #0ea5e9;
}

/* Estilo para campos requeridos en edición */
.form-label.required::after {
    content: " *";
    color: #ef4444;
}

/* Estilos para la gestión de funcionarios */
.funcionarios-management-container {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.funcionarios-tabs {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-header {
    display: flex;
    background-color: white;
    border-bottom: 1px solid #e2e8f0;
}

.tab-btn {
    flex: 1;
    background: none;
    border: none;
    padding: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.tab-btn.active {
    color: #0ea5e9;
    border-bottom: 3px solid #0ea5e9;
    background-color: #f0f9ff;
}

.tab-content {
    background-color: white;
    min-height: 400px;
}

.tab-pane {
    display: none;
    padding: 20px;
}

.tab-pane.active {
    display: block;
}

.funcionarios-toolbar {
    margin-bottom: 15px;
}

.current-funcionarios-list,
.add-funcionarios-list {
    max-height: 400px;
    overflow-y: auto;
    padding: 5px;
}

.funcionarios-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
    color: #64748b;
}

.funcionario-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background-color: #f8fafc;
}

.funcionario-item:hover {
    background-color: #f1f5f9;
}

.funcionario-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #0ea5e9;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-weight: 600;
}

.funcionario-info {
    flex: 1;
}

.funcionario-nombre {
    font-weight: 600;
    margin-bottom: 2px;
}

.funcionario-email {
    font-size: 0.85rem;
    color: #64748b;
}

.funcionario-rol {
    font-size: 0.75rem;
    padding: 2px 6px;
    border-radius: 4px;
    background-color: #f1f5f9;
    color: #475569;
    margin-left: 8px;
}

.funcionario-action {
    opacity: 0;
    transition: opacity 0.2s;
}

.funcionario-item:hover .funcionario-action {
    opacity: 1;
}

.no-funcionarios {
    text-align: center;
    color: #64748b;
    padding: 20px;
}

.no-funcionarios i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

.funcionario-action button {
    background: none;
    border: none;
    color: #64748b;
    cursor: pointer;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.funcionario-action button:hover {
    background-color: #e2e8f0;
    color: #0f172a;
}

.funcionario-action button.add-btn {
    color: #10b981;
}

.funcionario-action button.add-btn:hover {
    background-color: #d1fae5;
}

.funcionario-action button.remove-btn {
    color: #ef4444;
}

.funcionario-action button.remove-btn:hover {
    background-color: #fee2e2;
}

/* Mejorar visualización en móviles */
@media (max-width: 768px) {
    .form-card-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .form-header-actions {
        width: 100%;
        justify-content: center;
    }
    
    .tab-btn {
        font-size: 0.85rem;
        padding: 10px;
    }
    
    .tab-btn i {
        margin-right: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const form = document.getElementById('unidadEditForm');
    const nombreInput = document.getElementById('nombre');
    const departamentoSelect = document.getElementById('departamento_id');
    const submitBtn = document.getElementById('submitBtn');
    const hiddenFuncionariosInputs = document.getElementById('hiddenFuncionariosInputs');
    
    // Referencias para la gestión de tabs
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Referencias para la búsqueda y filtros
    const currentSearch = document.getElementById('currentSearch');
    const clearCurrentSearch = document.getElementById('clearCurrentSearch');
    const addSearch = document.getElementById('addSearch');
    const clearAddSearch = document.getElementById('clearAddSearch');
    const showAllFuncionarios = document.getElementById('showAllFuncionarios');
    const showTecnicos = document.getElementById('showTecnicos');
    const showGestores = document.getElementById('showGestores');
    
    // Referencias para las listas de funcionarios
    const currentFuncionariosList = document.getElementById('currentFuncionariosList');
    const addFuncionariosList = document.getElementById('addFuncionariosList');
    
    // Listas de funcionarios
    let currentFuncionarios = [];
    let availableFuncionarios = [];
    let allFuncionarios = [];
    
    // ID de la unidad actual
    const unidadId = '{{ $unidad['id_unidad'] }}';
    
    // Cargar funcionarios
    cargarFuncionarios();
    
    // Función para cambiar entre tabs
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Desactivar todos los botones y tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Activar el botón y tab seleccionados
            this.classList.add('active');
            const tabId = this.dataset.tab;
            document.getElementById(tabId).classList.add('active');
        });
    });
    
    // Cargar funcionarios (actuales y disponibles)
    async function cargarFuncionarios() {
        try {
            // Obtener todos los funcionarios del sistema
            const allFuncionariosData = [
                @foreach($funcionarios as $funcionario)
                {
                    id: "{{ $funcionario['id'] }}",
                    nombre: "{{ $funcionario['nombre'] }}",
                    email: "{{ $funcionario['email'] }}",
                    rol: "{{ $funcionario['rol'] }}",
                    departamento_id: "{{ $funcionario['departamento_id'] ?? '' }}",
                    unidad_id: "{{ $funcionario['unidad_id'] ?? '' }}"
                },
                @endforeach
            ];
            
            allFuncionarios = allFuncionariosData;
            
            // Filtrar funcionarios actuales y disponibles
            currentFuncionarios = allFuncionarios.filter(f => f.unidad_id === unidadId);
            availableFuncionarios = allFuncionarios.filter(f => !f.unidad_id || f.unidad_id === '');
            
            // Renderizar listas
            renderizarFuncionariosActuales();
            renderizarFuncionariosDisponibles();
            
            // Actualizar inputs ocultos con los funcionarios actuales
            actualizarInputsOcultos();
            
        } catch (error) {
            console.error('Error al cargar funcionarios:', error);
            
            currentFuncionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar funcionarios: ${error.message}</p>
                </div>
            `;
            
            addFuncionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar funcionarios: ${error.message}</p>
                </div>
            `;
        }
    }
    
    // Renderizar funcionarios actuales
    function renderizarFuncionariosActuales() {
        let filteredFuncionarios = [...currentFuncionarios];
        
        // Filtrar por búsqueda
        if (currentSearch.value.trim()) {
            const searchTerm = currentSearch.value.trim().toLowerCase();
            filteredFuncionarios = filteredFuncionarios.filter(funcionario => {
                return funcionario.nombre.toLowerCase().includes(searchTerm) || 
                       funcionario.email.toLowerCase().includes(searchTerm);
            });
        }
        
        // Renderizar la lista
        if (filteredFuncionarios.length === 0) {
            currentFuncionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-user-slash"></i>
                    <p>No hay funcionarios asignados a esta unidad</p>
                </div>
            `;
            return;
        }
        
        currentFuncionariosList.innerHTML = '';
        filteredFuncionarios.forEach(funcionario => {
            const iniciales = obtenerIniciales(funcionario.nombre);
            
            const funcionarioItem = document.createElement('div');
            funcionarioItem.className = 'funcionario-item';
            funcionarioItem.dataset.id = funcionario.id;
            funcionarioItem.innerHTML = `
                <div class="funcionario-avatar">${iniciales}</div>
                <div class="funcionario-info">
                    <div class="funcionario-nombre">${funcionario.nombre}</div>
                    <div class="funcionario-email">${funcionario.email}</div>
                </div>
                <div class="funcionario-rol">${formatearRol(funcionario.rol)}</div>
                <div class="funcionario-action">
                    <button type="button" class="remove-btn" title="Quitar de la unidad">
                        <i class="fas fa-user-minus"></i>
                    </button>
                </div>
            `;
            
            // Evento para quitar funcionario
            funcionarioItem.querySelector('.remove-btn').addEventListener('click', () => {
                quitarFuncionario(funcionario);
            });
            
            currentFuncionariosList.appendChild(funcionarioItem);
        });
    }
    
    // Renderizar funcionarios disponibles
    function renderizarFuncionariosDisponibles() {
        let filteredFuncionarios = [...availableFuncionarios];
        
        // Filtrar por búsqueda
        if (addSearch.value.trim()) {
            const searchTerm = addSearch.value.trim().toLowerCase();
            filteredFuncionarios = filteredFuncionarios.filter(funcionario => {
                return funcionario.nombre.toLowerCase().includes(searchTerm) || 
                       funcionario.email.toLowerCase().includes(searchTerm);
            });
        }
        
        // Filtrar por rol
        if (showTecnicos.checked && !showGestores.checked) {
            filteredFuncionarios = filteredFuncionarios.filter(f => f.rol === 'tecnico');
        } else if (!showTecnicos.checked && showGestores.checked) {
            filteredFuncionarios = filteredFuncionarios.filter(f => f.rol === 'gestor');
        }
        
        // Renderizar la lista
        if (filteredFuncionarios.length === 0) {
            addFuncionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-user-slash"></i>
                    <p>No hay funcionarios disponibles con los filtros aplicados</p>
                </div>
            `;
            return;
        }
        
        addFuncionariosList.innerHTML = '';
        filteredFuncionarios.forEach(funcionario => {
            const iniciales = obtenerIniciales(funcionario.nombre);
            
            const funcionarioItem = document.createElement('div');
            funcionarioItem.className = 'funcionario-item';
            funcionarioItem.dataset.id = funcionario.id;
            funcionarioItem.innerHTML = `
                <div class="funcionario-avatar">${iniciales}</div>
                <div class="funcionario-info">
                    <div class="funcionario-nombre">${funcionario.nombre}</div>
                    <div class="funcionario-email">${funcionario.email}</div>
                </div>
                <div class="funcionario-rol">${formatearRol(funcionario.rol)}</div>
                <div class="funcionario-action">
                    <button type="button" class="add-btn" title="Agregar a la unidad">
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            `;
            
            // Evento para agregar funcionario
            funcionarioItem.querySelector('.add-btn').addEventListener('click', () => {
                agregarFuncionario(funcionario);
            });
            
            addFuncionariosList.appendChild(funcionarioItem);
        });
    }
    
    // Quitar funcionario de la unidad
    function quitarFuncionario(funcionario) {
        // Confirmar eliminación
        if (!confirm(`¿Está seguro que desea quitar a ${funcionario.nombre} de esta unidad?`)) {
            return;
        }
        
        // Quitar de la lista de actuales
        currentFuncionarios = currentFuncionarios.filter(f => f.id !== funcionario.id);
        
        // Agregar a la lista de disponibles
        const funcionarioModificado = {...funcionario, unidad_id: ''};
        availableFuncionarios.push(funcionarioModificado);
        
        // Actualizar las listas
        renderizarFuncionariosActuales();
        renderizarFuncionariosDisponibles();
        
        // Actualizar inputs ocultos
        actualizarInputsOcultos();
    }
    
    // Agregar funcionario a la unidad
    function agregarFuncionario(funcionario) {
        // Quitar de la lista de disponibles
        availableFuncionarios = availableFuncionarios.filter(f => f.id !== funcionario.id);
        
        // Agregar a la lista de actuales
        const funcionarioModificado = {...funcionario, unidad_id: unidadId};
        currentFuncionarios.push(funcionarioModificado);
        
        // Actualizar las listas
        renderizarFuncionariosActuales();
        renderizarFuncionariosDisponibles();
        
        // Actualizar inputs ocultos
        actualizarInputsOcultos();
    }
    
    // Actualizar inputs ocultos con los funcionarios actuales
    function actualizarInputsOcultos() {
        hiddenFuncionariosInputs.innerHTML = '';
        
        currentFuncionarios.forEach((funcionario, index) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `funcionarios[${index}]`;
            input.value = funcionario.id;
            hiddenFuncionariosInputs.appendChild(input);
        });
    }
    
    // Formatear rol para mostrar
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
    
    // Eventos para búsqueda y filtros
    currentSearch.addEventListener('input', renderizarFuncionariosActuales);
    clearCurrentSearch.addEventListener('click', () => {
        currentSearch.value = '';
        renderizarFuncionariosActuales();
    });
    
    addSearch.addEventListener('input', renderizarFuncionariosDisponibles);
    clearAddSearch.addEventListener('click', () => {
        addSearch.value = '';
        renderizarFuncionariosDisponibles();
    });
    
    showAllFuncionarios.addEventListener('change', function() {
        if (this.checked) {
            showTecnicos.checked = false;
            showGestores.checked = false;
        }
        renderizarFuncionariosDisponibles();
    });
    
    showTecnicos.addEventListener('change', function() {
        if (this.checked) {
            showAllFuncionarios.checked = false;
            if (showGestores.checked) {
                showAllFuncionarios.checked = true;
                showTecnicos.checked = false;
                showGestores.checked = false;
            }
        } else if (!showGestores.checked) {
            showAllFuncionarios.checked = true;
        }
        renderizarFuncionariosDisponibles();
    });
    
    showGestores.addEventListener('change', function() {
        if (this.checked) {
            showAllFuncionarios.checked = false;
            if (showTecnicos.checked) {
                showAllFuncionarios.checked = true;
                showTecnicos.checked = false;
                showGestores.checked = false;
            }
        } else if (!showTecnicos.checked) {
            showAllFuncionarios.checked = true;
        }
        renderizarFuncionariosDisponibles();
    });
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });

    function validateField(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        }
    }

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Enviando formulario de edición...');
        
        let isValid = true;
        
        // Validar campos requeridos
        inputs.forEach(campo => {
            if (!validateField(campo)) {
                isValid = false;
            }
        });

        if (isValid) {
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'Actualizando...';
            submitBtn.querySelector('.form-spinner').style.display = 'inline-block';
            
            console.log('Enviando datos al servidor...');
            console.log('Funcionarios asignados:', currentFuncionarios);
            
            // Enviar formulario
            this.submit();
        } else {
            console.log('Formulario tiene errores');
            
            // Scroll al primer error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });
});
</script>
@endsection
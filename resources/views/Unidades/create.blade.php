@extends('layouts.app')

@section('title', 'Crear Unidad - ServiMuni')

@section('page-title', 'Crear Unidad')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="form-view-container">
    <div class="card">
        <div class="form-card-header">
            <h2 class="form-card-title">
                <i class="fas fa-building-circle-plus"></i>Crear Nueva Unidad
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
            
            <form method="POST" action="{{ route('unidades.store') }}" id="unidadForm" novalidate>
                @csrf
                
                <!-- Sección: Información Básica -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-info-circle"></i>Información de la Unidad
                    </h3>
                    
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
                                   value="{{ old('nombre') }}" 
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
                                    <option value="{{ $departamento['id'] }}" {{ old('departamento_id') == $departamento['id'] ? 'selected' : '' }}>
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
                
                <!-- Sección: Asignación de Funcionarios -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-users"></i>Asignación de Funcionarios
                    </h3>
                    
                    <div class="funcionarios-selection-container">
                        <div class="funcionarios-search">
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       id="funcionarioSearch" 
                                       class="form-control" 
                                       placeholder="Buscar funcionario por nombre o email...">
                                <button type="button" class="btn btn-outline-secondary" id="clearFuncionarioSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
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
                        
                        <div class="funcionarios-list-container">
                            <div class="funcionarios-list">
                                <div class="funcionarios-loading">
                                    <i class="fas fa-spinner fa-spin"></i> Cargando funcionarios disponibles...
                                </div>
                                <div id="funcionariosList"></div>
                            </div>
                            
                            <div class="funcionarios-selected">
                                <h5 class="mb-3">Funcionarios Seleccionados</h5>
                                <div id="selectedFuncionariosList" class="selected-funcionarios-list">
                                    <div class="no-funcionarios-selected">
                                        <i class="fas fa-info-circle"></i>
                                        <p>No hay funcionarios seleccionados</p>
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
                        <span>Guardar Unidad</span>
                        <div class="form-spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para el formulario de unidades */
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

/* Destacar que es un formulario de creación */
.form-view-container .card {
    border-top: 4px solid #0ea5e9;
}

/* Estilo para campos requeridos */
.form-label.required::after {
    content: " *";
    color: #ef4444;
}

/* Estilos para la sección de funcionarios */
.funcionarios-selection-container {
    background-color: #f8fafc;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.funcionarios-list-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-top: 15px;
}

.funcionarios-list, .funcionarios-selected {
    background-color: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    min-height: 300px;
    max-height: 400px;
    overflow-y: auto;
}

.funcionarios-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100px;
    color: #64748b;
}

.funcionarios-loading i {
    margin-right: 8px;
}

.funcionario-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.funcionario-item:hover {
    background-color: #f1f5f9;
}

.funcionario-item.selected {
    background-color: #e0f2fe;
    border-left: 4px solid #0ea5e9;
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

.no-funcionarios-selected {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100px;
    color: #64748b;
    text-align: center;
}

.no-funcionarios-selected i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

.selected-funcionario-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 8px;
    background-color: #f1f5f9;
    border-left: 4px solid #0ea5e9;
}

.selected-funcionario-item .funcionario-action {
    opacity: 1;
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
    
    .funcionarios-list-container {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const form = document.getElementById('unidadForm');
    const nombreInput = document.getElementById('nombre');
    const departamentoSelect = document.getElementById('departamento_id');
    const submitBtn = document.getElementById('submitBtn');
    
    // Referencias para la selección de funcionarios
    const funcionarioSearch = document.getElementById('funcionarioSearch');
    const clearFuncionarioSearch = document.getElementById('clearFuncionarioSearch');
    const showAllFuncionarios = document.getElementById('showAllFuncionarios');
    const showTecnicos = document.getElementById('showTecnicos');
    const showGestores = document.getElementById('showGestores');
    const funcionariosList = document.getElementById('funcionariosList');
    const selectedFuncionariosList = document.getElementById('selectedFuncionariosList');
    const hiddenFuncionariosInputs = document.getElementById('hiddenFuncionariosInputs');
    
    // Lista de todos los funcionarios y funcionarios seleccionados
    let allFuncionarios = [];
    let selectedFuncionarios = [];
    
    // Escuchar cambios en el select de departamento
    departamentoSelect.addEventListener('change', function() {
        const departamentoId = this.value;
        
        if (departamentoId) {
            cargarFuncionariosPorDepartamento(departamentoId);
        } else {
            // Limpiar funcionarios si no hay departamento seleccionado
            allFuncionarios = [];
            selectedFuncionarios = [];
            renderizarFuncionarios();
            renderizarSeleccionados();
        }
    });
    
    // Función para cargar funcionarios por departamento
    async function cargarFuncionariosPorDepartamento(departamentoId) {
        try {
            // Mostrar loading
            funcionariosList.innerHTML = `
                <div class="funcionarios-loading">
                    <i class="fas fa-spinner fa-spin"></i> Cargando funcionarios del departamento...
                </div>
            `;
            
            // Llamada a la API
            const response = await fetch(`/api/departamentos/${departamentoId}/funcionarios`);
            
            if (!response.ok) {
                throw new Error('Error al cargar funcionarios');
            }
            
            const funcionarios = await response.json();
            
            console.log('Funcionarios cargados:', funcionarios);
            
            allFuncionarios = funcionarios;
            
            // Limpiar funcionarios seleccionados previos
            selectedFuncionarios = [];
            
            renderizarFuncionarios();
            renderizarSeleccionados();
            
        } catch (error) {
            console.error('Error al cargar funcionarios:', error);
            funcionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Error al cargar funcionarios: ${error.message}</p>
                </div>
            `;
        }
    }
    
    // Renderizar lista de funcionarios disponibles
    function renderizarFuncionarios() {
        // Filtrar funcionarios según los filtros aplicados
        let funcionariosFiltrados = [...allFuncionarios];
        
        // Filtrar por búsqueda
        if (funcionarioSearch.value.trim()) {
            const searchTerm = funcionarioSearch.value.trim().toLowerCase();
            funcionariosFiltrados = funcionariosFiltrados.filter(funcionario => {
                return funcionario.nombre.toLowerCase().includes(searchTerm) || 
                       funcionario.email.toLowerCase().includes(searchTerm);
            });
        }
        
        // Filtrar por rol
        if (showTecnicos.checked && !showGestores.checked) {
            funcionariosFiltrados = funcionariosFiltrados.filter(f => f.rol === 'tecnico');
        } else if (!showTecnicos.checked && showGestores.checked) {
            funcionariosFiltrados = funcionariosFiltrados.filter(f => f.rol === 'gestor');
        }
        
        // Excluir funcionarios ya seleccionados
        funcionariosFiltrados = funcionariosFiltrados.filter(f => 
            !selectedFuncionarios.some(sf => sf.id === f.id)
        );
        
        // Renderizar la lista
        if (funcionariosFiltrados.length === 0) {
            let mensaje = 'No hay funcionarios disponibles';
            
            if (!departamentoSelect.value) {
                mensaje = 'Selecciona un departamento para ver los funcionarios disponibles';
            } else if (funcionarioSearch.value.trim() || showTecnicos.checked || showGestores.checked) {
                mensaje = 'No hay funcionarios disponibles con los filtros aplicados';
            } else if (allFuncionarios.length === 0) {
                mensaje = 'No hay funcionarios disponibles en este departamento';
            } else if (selectedFuncionarios.length === allFuncionarios.length) {
                mensaje = 'Todos los funcionarios del departamento ya han sido seleccionados';
            }
            
            funcionariosList.innerHTML = `
                <div class="no-funcionarios">
                    <i class="fas fa-info-circle"></i>
                    <p>${mensaje}</p>
                </div>
            `;
            return;
        }
        
        funcionariosList.innerHTML = '';
        funcionariosFiltrados.forEach(funcionario => {
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
                        <i class="fas fa-plus-circle"></i>
                    </button>
                </div>
            `;
            
            // Evento para agregar funcionario
            funcionarioItem.querySelector('.add-btn').addEventListener('click', () => {
                agregarFuncionario(funcionario);
            });
            
            funcionariosList.appendChild(funcionarioItem);
        });
    }
    
    // Renderizar lista de funcionarios seleccionados
    function renderizarSeleccionados() {
        if (selectedFuncionarios.length === 0) {
            selectedFuncionariosList.innerHTML = `
                <div class="no-funcionarios-selected">
                    <i class="fas fa-info-circle"></i>
                    <p>No hay funcionarios seleccionados</p>
                </div>
            `;
            return;
        }
        
        selectedFuncionariosList.innerHTML = '';
        selectedFuncionarios.forEach(funcionario => {
            const iniciales = obtenerIniciales(funcionario.nombre);
            
            const funcionarioItem = document.createElement('div');
            funcionarioItem.className = 'selected-funcionario-item';
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
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            `;
            
            // Evento para quitar funcionario
            funcionarioItem.querySelector('.remove-btn').addEventListener('click', () => {
                quitarFuncionario(funcionario.id);
            });
            
            selectedFuncionariosList.appendChild(funcionarioItem);
        });
        
        // Actualizar inputs ocultos para enviar al servidor
        actualizarInputsOcultos();
    }
    
    // Agregar funcionario a la selección
    function agregarFuncionario(funcionario) {
        // Verificar si ya está seleccionado
        if (selectedFuncionarios.some(f => f.id === funcionario.id)) {
            return;
        }
        
        // Agregar a la lista de seleccionados
        selectedFuncionarios.push(funcionario);
        
        // Actualizar listas
        renderizarFuncionarios();
        renderizarSeleccionados();
    }
    
    // Quitar funcionario de la selección
    function quitarFuncionario(id) {
        // Quitar de la lista de seleccionados
        selectedFuncionarios = selectedFuncionarios.filter(f => f.id !== id);
        
        // Actualizar listas
        renderizarFuncionarios();
        renderizarSeleccionados();
    }
    
    // Actualizar inputs ocultos para enviar los funcionarios seleccionados
    function actualizarInputsOcultos() {
        hiddenFuncionariosInputs.innerHTML = '';
        
        selectedFuncionarios.forEach((funcionario, index) => {
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
        
        const palabras = nombre.trim().split(/\s+/);
        
        if (palabras.length === 1) {
            const palabra = palabras[0];
            if (palabra.length >= 2) {
                return (palabra.charAt(0) + palabra.charAt(1)).toUpperCase();
            } else {
                return (palabra.charAt(0) + palabra.charAt(0)).toUpperCase();
            }
        } else {
            return (palabras[0].charAt(0) + palabras[1].charAt(0)).toUpperCase();
        }
    }
    
    // Eventos para los filtros de funcionarios
    funcionarioSearch.addEventListener('input', renderizarFuncionarios);
    
    clearFuncionarioSearch.addEventListener('click', () => {
        funcionarioSearch.value = '';
        renderizarFuncionarios();
    });
    
    showAllFuncionarios.addEventListener('change', function() {
        if (this.checked) {
            showTecnicos.checked = false;
            showGestores.checked = false;
        }
        renderizarFuncionarios();
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
        renderizarFuncionarios();
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
        renderizarFuncionarios();
    });
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input, select');
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
        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        } else {
            field.classList.remove('is-invalid');
            if (field.value.trim()) {
                field.classList.add('is-valid');
            }
            return true;
        }
    }

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Enviando formulario...');
        
        let isValid = true;
        
        // Validar campos requeridos
        const camposRequeridos = [nombreInput, departamentoSelect];
        
        camposRequeridos.forEach(campo => {
            if (!validateField(campo)) {
                isValid = false;
            }
        });

        console.log('Formulario válido:', isValid);

        if (isValid) {
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'Guardando...';
            submitBtn.querySelector('.form-spinner').style.display = 'inline-block';
            
            console.log('Enviando datos al servidor...');
            console.log('Funcionarios seleccionados:', selectedFuncionarios);
            
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

    // Auto-focus en el primer campo
    nombreInput.focus();
    
    // Mostrar mensaje inicial
    funcionariosList.innerHTML = `
        <div class="no-funcionarios">
            <i class="fas fa-info-circle"></i>
            <p>Selecciona un departamento para ver los funcionarios disponibles</p>
        </div>
    `;
});
</script>
@endsection
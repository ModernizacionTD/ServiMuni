@extends('layouts.app')

@section('title', 'Crear Requerimiento - ServiMuni')

@section('page-title', 'Crear Requerimiento')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="form-view-container">
    <div class="card">
        <div class="form-card-header">
            <h2 class="form-card-title">
                <i class="fas fa-clipboard-list"></i>Crear Nuevo Requerimiento
            </h2>
            <div class="form-header-actions">
                <a href="{{ route('requerimientos.index') }}" class="btn btn-sm btn-outline-secondary">
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
            
            <form method="POST" action="{{ route('requerimientos.store') }}" id="requerimientoCreateForm" novalidate>
                @csrf
                
                <!-- Sección: Información Básica -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-info-circle"></i>Información Básica
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="departamento_id" class="form-label required">Departamento</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <select id="departamento_id" 
                                            name="departamento_id" 
                                            class="form-select @error('departamento_id') is-invalid @enderror" 
                                            required>
                                        <option value="">Seleccionar departamento...</option>
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
                                    Seleccione el departamento responsable del requerimiento
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="nombre" class="form-label required">Nombre del Requerimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <input type="text" 
                                           id="nombre" 
                                           name="nombre" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           value="{{ old('nombre') }}" 
                                           placeholder="Nombre descriptivo del requerimiento"
                                           maxlength="30"
                                           required>
                                </div>
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Máximo 30 caracteres. <span id="nombreCount">0</span>/30
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Descripciones -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-file-alt"></i>Descripciones
                    </h3>
                    
                    <div class="form-group">
                        <label for="descripcion_req" class="form-label required">Descripción del Requerimiento</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-align-left"></i>
                            </span>
                            <textarea id="descripcion_req" 
                                      name="descripcion_req" 
                                      class="form-textarea @error('descripcion_req') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Describa detalladamente el requerimiento, sus características y requisitos..."
                                      maxlength="255"
                                      required>{{ old('descripcion_req') }}</textarea>
                        </div>
                        @error('descripcion_req')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Máximo 255 caracteres. <span id="descripcionReqCount">0</span>/255
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion_precio" class="form-label required">Descripción de Precio</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <textarea id="descripcion_precio" 
                                      name="descripcion_precio" 
                                      class="form-textarea @error('descripcion_precio') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Describa la estructura de precios, costos asociados, formas de pago..."
                                      maxlength="255"
                                      required>{{ old('descripcion_precio') }}</textarea>
                        </div>
                        @error('descripcion_precio')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Máximo 255 caracteres. <span id="descripcionPrecioCount">0</span>/255
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Configuración de Acceso -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-shield-alt"></i>Configuración de Acceso
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           id="privado" 
                                           name="privado" 
                                           class="form-check-input @error('privado') is-invalid @enderror" 
                                           value="1" 
                                           {{ old('privado') ? 'checked' : '' }}>
                                    <label for="privado" class="form-check-label">
                                        <i class="fas fa-lock me-2"></i>Acceso Privado
                                    </label>
                                </div>
                                @error('privado')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Solo usuarios autorizados pueden acceder
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           id="publico" 
                                           name="publico" 
                                           class="form-check-input @error('publico') is-invalid @enderror" 
                                           value="1" 
                                           {{ old('publico') ? 'checked' : '' }}>
                                    <label for="publico" class="form-check-label">
                                        <i class="fas fa-globe me-2"></i>Acceso Público
                                    </label>
                                </div>
                                @error('publico')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Cualquier usuario puede acceder
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alerta de configuración de acceso -->
                    <div class="form-alert form-alert-info" id="accessAlert" style="display: none;">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Nota:</strong> <span id="accessMessage"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Acciones del Formulario -->
                <div class="form-actions">
                    <a href="{{ route('requerimientos.index') }}" class="form-btn form-btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    
                    <button type="submit" class="form-btn form-btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> 
                        <span>Crear Requerimiento</span>
                        <div class="form-spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const form = document.getElementById('requerimientoCreateForm');
    const submitBtn = document.getElementById('submitBtn');
    const nombreInput = document.getElementById('nombre');
    const descripcionReqTextarea = document.getElementById('descripcion_req');
    const descripcionPrecioTextarea = document.getElementById('descripcion_precio');
    const privadoCheckbox = document.getElementById('privado');
    const publicoCheckbox = document.getElementById('publico');
    const accessAlert = document.getElementById('accessAlert');
    const accessMessage = document.getElementById('accessMessage');
    
    // Contadores de caracteres
    const nombreCount = document.getElementById('nombreCount');
    const descripcionReqCount = document.getElementById('descripcionReqCount');
    const descripcionPrecioCount = document.getElementById('descripcionPrecioCount');
    
    // Función para actualizar contador de caracteres
    function updateCharCounter(input, counter, maxLength) {
        const currentLength = input.value.length;
        counter.textContent = currentLength;
        counter.parentElement.style.color = currentLength > maxLength * 0.9 ? '#ef4444' : '#64748b';
    }
    
    // Configurar contadores de caracteres
    nombreInput.addEventListener('input', function() {
        updateCharCounter(this, nombreCount, 30);
    });
    
    descripcionReqTextarea.addEventListener('input', function() {
        updateCharCounter(this, descripcionReqCount, 255);
    });
    
    descripcionPrecioTextarea.addEventListener('input', function() {
        updateCharCounter(this, descripcionPrecioCount, 255);
    });
    
    // Función para actualizar mensaje de acceso
    function updateAccessMessage() {
        const isPrivado = privadoCheckbox.checked;
        const isPublico = publicoCheckbox.checked;
        
        if (isPrivado && isPublico) {
            accessAlert.style.display = 'block';
            accessMessage.textContent = 'El requerimiento tendrá acceso tanto privado como público.';
            accessAlert.className = 'form-alert form-alert-warning';
        } else if (isPrivado) {
            accessAlert.style.display = 'block';
            accessMessage.textContent = 'Solo usuarios autorizados podrán ver este requerimiento.';
            accessAlert.className = 'form-alert form-alert-info';
        } else if (isPublico) {
            accessAlert.style.display = 'block';
            accessMessage.textContent = 'Todos los usuarios podrán ver este requerimiento.';
            accessAlert.className = 'form-alert form-alert-success';
        } else {
            accessAlert.style.display = 'block';
            accessMessage.textContent = 'Se recomienda seleccionar al menos una opción de acceso para el requerimiento.';
            accessAlert.className = 'form-alert form-alert-warning';
        }
    }
    
    // Configurar eventos para checkboxes de acceso
    privadoCheckbox.addEventListener('change', updateAccessMessage);
    publicoCheckbox.addEventListener('change', updateAccessMessage);
    
    // Inicializar mensaje de acceso
    updateAccessMessage();
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input, select, textarea');
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
        } else if (field.type === 'email' && field.value && !isValidEmail(field.value)) {
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

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Manejar envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('Enviando formulario de creación de requerimiento...');
        
        let isValid = true;
        
        // Validar campos requeridos
        const camposRequeridos = [
            document.getElementById('departamento_id'),
            nombreInput,
            descripcionReqTextarea,
            descripcionPrecioTextarea
        ];
        
        camposRequeridos.forEach(campo => {
            if (campo && !validateField(campo)) {
                isValid = false;
            }
        });
        
        // Validar que al menos una opción de acceso esté seleccionada (opcional)
        if (!privadoCheckbox.checked && !publicoCheckbox.checked) {
            console.log('Advertencia: No se ha seleccionado ninguna opción de acceso');
        }

        console.log('Formulario válido:', isValid);

        if (isValid) {
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'Creando...';
            submitBtn.querySelector('.form-spinner').style.display = 'inline-block';
            
            console.log('Enviando datos al servidor...');
            
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
    document.getElementById('departamento_id').focus();
    
    // Inicializar contadores
    updateCharCounter(nombreInput, nombreCount, 30);
    updateCharCounter(descripcionReqTextarea, descripcionReqCount, 255);
    updateCharCounter(descripcionPrecioTextarea, descripcionPrecioCount, 255);
});
</script>

<style>
/* Estilos específicos para formulario de creación de requerimientos */
.form-card-header {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    background-color: #ecfeff;
    border-left: 4px solid #06b6d4;
    color: #155e75;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-info i {
    color: #06b6d4;
    font-size: 1.2rem;
    margin-top: 2px;
}

.form-alert-warning {
    background-color: #fffbeb;
    border-left: 4px solid #f59e0b;
    color: #92400e;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-warning i {
    color: #f59e0b;
    font-size: 1.2rem;
    margin-top: 2px;
}

/* Destacar que es un formulario de creación */
.form-view-container .card {
    border-top: 4px solid #10b981;
}

/* Estilos para switches mejorados */
.form-check-input {
    width: 3em;
    height: 1.5em;
    margin-top: 0;
    cursor: pointer;
}

.form-check-label {
    margin-left: 8px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.form-switch {
    padding-left: 3.5em;
}

/* Animación suave para mostrar/ocultar alertas */
.form-alert {
    transition: all 0.3s ease;
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
}
</style>
@endsection
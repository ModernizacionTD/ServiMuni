@extends('layouts.app')

@section('title', 'Crear Usuario - ServiMuni')

@section('page-title', 'Crear Usuario')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="form-view-container">
    <div class="card">
        <div class="form-card-header">
            <h2 class="form-card-title">
                <i class="fas fa-user-plus"></i>Crear Nuevo Usuario
            </h2>
            <div class="form-header-actions">
                <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
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
            
            <form method="POST" action="{{ route('usuarios.store') }}" id="userForm" novalidate>
                @csrf
                
                <!-- Sección: Información Básica -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-id-card"></i>Información Básica
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="rut" class="form-label required">RUT</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card"></i>
                                    </span>
                                    <input type="text" 
                                           id="rut" 
                                           name="rut" 
                                           class="form-control @error('rut') is-invalid @enderror" 
                                           value="{{ old('rut', request('rut')) }}" 
                                           placeholder="12.345.678-9"
                                           maxlength="12"
                                           required>
                                </div>
                                @error('rut')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Formato: 12.345.678-9 (con puntos y guión)
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="tipo_persona" class="form-label required">Tipo de Persona</label>
                                <select id="tipo_persona" 
                                        name="tipo_persona" 
                                        class="form-select @error('tipo_persona') is-invalid @enderror" 
                                        required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="Natural" {{ old('tipo_persona') == 'Natural' ? 'selected' : '' }}>
                                        <i class="fas fa-user"></i> Persona Natural
                                    </option>
                                    <option value="Jurídica" {{ old('tipo_persona') == 'Jurídica' ? 'selected' : '' }}>
                                        <i class="fas fa-building"></i> Persona Jurídica
                                    </option>
                                </select>
                                @error('tipo_persona')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos para Persona Natural -->
                <div id="camposPersonaNatural" class="form-section" style="display: none;">
                    <h3 class="form-section-title">
                        <i class="fas fa-user"></i>Datos Personales
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="nombre" class="form-label required">Nombre(s)</label>
                                <input type="text" 
                                       id="nombre" 
                                       name="nombre" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}" 
                                       placeholder="Ingrese el nombre"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="apellidos" class="form-label required">Apellidos</label>
                                <input type="text" 
                                       id="apellidos" 
                                       name="apellidos" 
                                       class="form-control @error('apellidos') is-invalid @enderror" 
                                       value="{{ old('apellidos') }}" 
                                       placeholder="Apellido paterno y materno"
                                       required>
                                @error('apellidos')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="uso_ns" class="form-label required">¿Usa Nombre Social?</label>
                                <div class="form-help">
                                    <span class="form-help-icon">?</span>
                                    <div class="form-help-tooltip">
                                        Nombre por el cual la persona prefiere ser identificada socialmente
                                    </div>
                                </div>
                                <select id="uso_ns" 
                                        name="uso_ns" 
                                        class="form-select @error('uso_ns') is-invalid @enderror" 
                                        required>
                                    <option value="No" {{ old('uso_ns', 'No') == 'No' ? 'selected' : '' }}>No</option>
                                    <option value="Sí" {{ old('uso_ns') == 'Sí' ? 'selected' : '' }}>Sí</option>
                                </select>
                                @error('uso_ns')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-2" id="campoNombreSocial" style="{{ old('uso_ns') == 'Sí' ? '' : 'display: none;' }}">
                            <div class="form-group">
                                <label for="nombre_social" class="form-label">Nombre Social</label>
                                <input type="text" 
                                       id="nombre_social" 
                                       name="nombre_social" 
                                       class="form-control @error('nombre_social') is-invalid @enderror" 
                                       value="{{ old('nombre_social') }}" 
                                       placeholder="Nombre social preferido">
                                @error('nombre_social')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="fecha_nacimiento" class="form-label required">Fecha de Nacimiento</label>
                                <input type="date" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                       value="{{ old('fecha_nacimiento') }}" 
                                       max="{{ date('Y-m-d') }}"
                                       required>
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="genero" class="form-label required">Género</label>
                                <select id="genero" 
                                        name="genero" 
                                        class="form-select @error('genero') is-invalid @enderror" 
                                        required>
                                    <option value="">Seleccionar género...</option>
                                    <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Transmasculino" {{ old('genero') == 'Transmasculino' ? 'selected' : '' }}>Transmasculino</option>
                                    <option value="Transfemenino" {{ old('genero') == 'Transfemenino' ? 'selected' : '' }}>Transfemenino</option>
                                    <option value="No decir" {{ old('genero') == 'No decir' ? 'selected' : '' }}>Prefiero no decir</option>
                                </select>
                                @error('genero')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Campos para Persona Jurídica -->
                <div id="camposPersonaJuridica" class="form-section" style="display: none;">
                    <h3 class="form-section-title">
                        <i class="fas fa-building"></i>Datos de la Empresa
                    </h3>
                    
                    <div class="form-group">
                        <label for="nombre_juridica" class="form-label required">Nombre o Razón Social</label>
                        <input type="text" 
                               id="nombre_juridica" 
                               name="nombre" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" 
                               placeholder="Ingrese la razón social completa"
                               required>
                        @error('nombre')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <!-- Sección: Información de Contacto -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-phone"></i>Información de Contacto
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="telefono" class="form-label required">Teléfono Principal</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" 
                                           id="telefono" 
                                           name="telefono" 
                                           class="form-control @error('telefono') is-invalid @enderror" 
                                           value="{{ old('telefono') }}" 
                                           placeholder="+56 9 1234 5678"
                                           required>
                                </div>
                                @error('telefono')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="telefono_2" class="form-label">Teléfono Alternativo</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" 
                                           id="telefono_2" 
                                           name="telefono_2" 
                                           class="form-control @error('telefono_2') is-invalid @enderror" 
                                           value="{{ old('telefono_2') }}" 
                                           placeholder="+56 2 1234 5678">
                                </div>
                                @error('telefono_2')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="email" class="form-label required">Email Principal</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}" 
                                           placeholder="usuario@ejemplo.com"
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="email_2" class="form-label">Email Alternativo</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" 
                                           id="email_2" 
                                           name="email_2" 
                                           class="form-control @error('email_2') is-invalid @enderror" 
                                           value="{{ old('email_2') }}" 
                                           placeholder="alternativo@ejemplo.com">
                                </div>
                                @error('email_2')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="direccion" class="form-label required">Dirección Completa</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <input type="text" 
                                   id="direccion" 
                                   name="direccion" 
                                   class="form-control @error('direccion') is-invalid @enderror" 
                                   value="{{ old('direccion') }}" 
                                   placeholder="Calle, número, comuna, ciudad"
                                   required>
                        </div>
                        @error('direccion')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Incluya calle, número, comuna y ciudad para una mejor ubicación
                        </div>
                    </div>
                </div>
                
                <!-- Acciones del Formulario -->
                <div class="form-actions">
                    <a href="{{ route('usuarios.index') }}" class="form-btn form-btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    
                    <button type="submit" class="form-btn form-btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> 
                        <span>Guardar Usuario</span>
                        <div class="form-spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos adicionales para el formulario de edición */
.form-card-header {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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

/* Destacar que es un formulario de edición */
.form-view-container .card {
    border-top: 4px solid #f59e0b;
}

/* Estilo para campos requeridos en edición */
.form-label.required::after {
    content: " *";
    color: #ef4444;
}

/* Animación suave para mostrar/ocultar secciones */
.form-section {
    transition: all 0.3s ease;
}

/* Estilo para botones del header */
.form-header-actions {
    display: flex;
    gap: 8px;
    align-items: center;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const form = document.getElementById('userForm');
    const tipoPersonaSelect = document.getElementById('tipo_persona');
    const camposPersonaNatural = document.getElementById('camposPersonaNatural');
    const camposPersonaJuridica = document.getElementById('camposPersonaJuridica');
    const rutInput = document.getElementById('rut');
    const submitBtn = document.getElementById('submitBtn');
    
    // Campos específicos de persona natural
    const nombreInput = document.getElementById('nombre');
    const apellidosInput = document.getElementById('apellidos');
    const usoNsSelect = document.getElementById('uso_ns');
    const nombreSocialInput = document.getElementById('nombre_social');
    const campoNombreSocial = document.getElementById('campoNombreSocial');
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    const generoSelect = document.getElementById('genero');
    
    // Formateo de RUT
    rutInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9kK]/g, '');
        
        if (value.length > 1) {
            // Separar cuerpo y dígito verificador
            let cuerpo = value.slice(0, -1);
            let dv = value.slice(-1);
            
            // Formatear cuerpo con puntos
            if (cuerpo.length > 3) {
                cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            
            // Combinar con guión
            value = cuerpo + '-' + dv;
        }
        
        e.target.value = value;
        
        // Validar RUT si está completo
        if (value.length >= 11) {
            validateRUT(value);
        } else {
            clearRutValidation();
        }
    });

    // Validación de RUT
    function validateRUT(rut) {
        const cleanRut = rut.replace(/[.-]/g, '');
        const cuerpo = cleanRut.slice(0, -1);
        const dv = cleanRut.slice(-1).toLowerCase();
        
        if (calculateDV(cuerpo) === dv) {
            rutInput.classList.remove('is-invalid');
            rutInput.classList.add('is-valid');
            return true;
        } else {
            rutInput.classList.remove('is-valid');
            rutInput.classList.add('is-invalid');
            return false;
        }
    }

    // Calcular dígito verificador
    function calculateDV(rut) {
        let sum = 0;
        let multiplier = 2;
        
        for (let i = rut.length - 1; i >= 0; i--) {
            sum += parseInt(rut[i]) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }
        
        const remainder = sum % 11;
        const dv = 11 - remainder;
        
        if (dv === 11) return '0';
        if (dv === 10) return 'k';
        return dv.toString();
    }

    function clearRutValidation() {
        rutInput.classList.remove('is-valid', 'is-invalid');
    }

    // Manejar cambio de tipo de persona
    tipoPersonaSelect.addEventListener('change', function() {
        const tipoPersona = this.value;
        
        console.log('Tipo de persona seleccionado:', tipoPersona);
        
        if (tipoPersona === 'Natural') {
            camposPersonaNatural.style.display = 'block';
            camposPersonaJuridica.style.display = 'none';
            
            // Hacer requeridos los campos específicos
            setRequiredFields(true);
            
            // Limpiar campos de persona jurídica
            const nombreJuridica = document.getElementById('nombre_juridica');
            if (nombreJuridica) {
                nombreJuridica.value = '';
                nombreJuridica.required = false;
            }
            
        } else if (tipoPersona === 'Jurídica') {
            camposPersonaNatural.style.display = 'none';
            camposPersonaJuridica.style.display = 'block';
            
            // Quitar requeridos de campos de persona natural
            setRequiredFields(false);
            
            // Hacer required el nombre jurídico
            const nombreJuridica = document.getElementById('nombre_juridica');
            if (nombreJuridica) {
                nombreJuridica.required = true;
            }
            
            // Limpiar campos de persona natural
            if (nombreInput) nombreInput.value = '';
            if (apellidosInput) apellidosInput.value = '';
            if (fechaNacimientoInput) fechaNacimientoInput.value = '';
            if (generoSelect) generoSelect.value = '';
            if (nombreSocialInput) nombreSocialInput.value = '';
            usoNsSelect.value = 'No';
            campoNombreSocial.style.display = 'none';
            
        } else {
            camposPersonaNatural.style.display = 'none';
            camposPersonaJuridica.style.display = 'none';
            setRequiredFields(false);
        }
    });

    // Manejar uso de nombre social
    usoNsSelect.addEventListener('change', function() {
        if (this.value === 'Sí') {
            campoNombreSocial.style.display = 'block';
            nombreSocialInput.required = true;
        } else {
            campoNombreSocial.style.display = 'none';
            nombreSocialInput.required = false;
            nombreSocialInput.value = '';
        }
    });

    // Función para establecer campos requeridos
    function setRequiredFields(required) {
        if (nombreInput) nombreInput.required = required;
        if (apellidosInput) apellidosInput.required = required;
        if (usoNsSelect) usoNsSelect.required = required;
        if (fechaNacimientoInput) fechaNacimientoInput.required = required;
        if (generoSelect) generoSelect.required = required;
    }

    // Inicializar estado basado en valor actual
    if (tipoPersonaSelect.value) {
        tipoPersonaSelect.dispatchEvent(new Event('change'));
    }

    if (usoNsSelect.value === 'Sí') {
        usoNsSelect.dispatchEvent(new Event('change'));
    }

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
        
        console.log('Enviando formulario...');
        
        let isValid = true;
        const tipoPersona = tipoPersonaSelect.value;
        
        // Validar campos básicos siempre
        const camposBasicos = [rutInput, tipoPersonaSelect, document.getElementById('telefono'), 
                              document.getElementById('email'), document.getElementById('direccion')];
        
        camposBasicos.forEach(campo => {
            if (campo && campo.hasAttribute('required') && !validateField(campo)) {
                isValid = false;
            }
        });
        
        // Validar campos específicos según tipo de persona
        if (tipoPersona === 'Natural') {
            const camposNaturales = [nombreInput, apellidosInput, fechaNacimientoInput, generoSelect, usoNsSelect];
            camposNaturales.forEach(campo => {
                if (campo && campo.hasAttribute('required') && !validateField(campo)) {
                    isValid = false;
                }
            });
            
            // Validar nombre social si es requerido
            if (usoNsSelect.value === 'Sí' && nombreSocialInput && !validateField(nombreSocialInput)) {
                isValid = false;
            }
        } else if (tipoPersona === 'Jurídica') {
            const nombreJuridica = document.getElementById('nombre_juridica');
            if (nombreJuridica && !validateField(nombreJuridica)) {
                isValid = false;
            }
        }

        // Validar RUT específicamente
        if (rutInput.value && !validateRUT(rutInput.value)) {
            isValid = false;
        }

        console.log('Formulario válido:', isValid);

        if (isValid) {
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'Guardando...';
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
    rutInput.focus();
});
</script>
@endsection
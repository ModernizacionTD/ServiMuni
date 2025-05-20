@extends('layouts.app')

@section('title', 'Crear Usuario - Sistema de Gestión')

@section('page-title', 'Crear Usuario')

@section('content')

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" id="rut" name="rut" class="form-control @error('rut') is-invalid @enderror" 
                               value="{{ old('rut') }}" required>
                        @error('rut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="tipo_persona" class="form-label">Tipo de Persona</label>
                        <select id="tipo_persona" name="tipo_persona" class="form-control @error('tipo_persona') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="Natural" {{ old('tipo_persona') == 'Natural' ? 'selected' : '' }}>Persona Natural</option>
                            <option value="Jurídica" {{ old('tipo_persona') == 'Jurídica' ? 'selected' : '' }}>Persona Jurídica</option>
                        </select>
                        @error('tipo_persona')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Campos para Persona Natural -->
            <div id="camposPersonaNatural">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="apellidos" class="form-label">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" 
                                value="{{ old('apellidos') }}" required>
                            @error('apellidos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="uso_ns" class="form-label">Uso de Nombre Social</label>
                            <select id="uso_ns" name="uso_ns" class="form-control @error('uso_ns') is-invalid @enderror" required>
                                <option value="No" {{ old('uso_ns') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Sí" {{ old('uso_ns') == 'Sí' ? 'selected' : '' }}>Sí</option>
                            </select>
                            @error('uso_ns')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6" id="campoNombreSocial" style="{{ old('uso_ns') == 'Sí' ? '' : 'display: none;' }}">
                        <div class="form-group mb-3">
                            <label for="nombre_social" class="form-label">Nombre Social</label>
                            <input type="text" id="nombre_social" name="nombre_social" class="form-control @error('nombre_social') is-invalid @enderror" 
                                value="{{ old('nombre_social') }}">
                            @error('nombre_social')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror" 
                                value="{{ old('fecha_nacimiento') }}" required>
                            @error('fecha_nacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="genero" class="form-label">Género</label>
                            <select id="genero" name="genero" class="form-control @error('genero') is-invalid @enderror" required>
                                <option value="">Seleccionar...</option>
                                <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="Transmasculino" {{ old('genero') == 'Transmasculino' ? 'selected' : '' }}>Transmasculino</option>
                                <option value="Transfemenino" {{ old('genero') == 'Transfemenino' ? 'selected' : '' }}>Transfemenino</option>
                                <option value="No decir" {{ old('genero') == 'No decir' ? 'selected' : '' }}>Prefiero no decir</option>
                            </select>
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Campos para Persona Jurídica -->
            <div id="camposPersonaJuridica" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label for="nombre_juridica" class="form-label">Nombre o Razón Social</label>
                            <input type="text" id="nombre_juridica" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                value="{{ old('nombre') }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Información de Contacto</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="telefono" class="form-label">Teléfono Principal</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono') }}" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="telefono_2" class="form-label">Teléfono Alternativo</label>
                        <input type="tel" id="telefono_2" name="telefono_2" class="form-control @error('telefono_2') is-invalid @enderror" 
                               value="{{ old('telefono_2') }}">
                        @error('telefono_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email Principal</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email_2" class="form-label">Email Alternativo</label>
                        <input type="email" id="email_2" name="email_2" class="form-control @error('email_2') is-invalid @enderror" 
                               value="{{ old('email_2') }}">
                        @error('email_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                       value="{{ old('direccion') }}" required>
                @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 5px;
}

.invalid-feedback {
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const tipoPersonaSelect = document.getElementById('tipo_persona');
    const camposPersonaNatural = document.getElementById('camposPersonaNatural');
    const camposPersonaJuridica = document.getElementById('camposPersonaJuridica');
    
    // Campos específicos de persona natural
    const apellidosInput = document.getElementById('apellidos');
    const usoNsSelect = document.getElementById('uso_ns');
    const nombreSocialInput = document.getElementById('nombre_social');
    const campoNombreSocial = document.getElementById('campoNombreSocial');
    const fechaNacimientoInput = document.getElementById('fecha_nacimiento');
    const generoSelect = document.getElementById('genero');
    
    // Función para manejar el cambio de tipo de persona
    function handleTipoPersonaChange() {
        const tipoPersona = tipoPersonaSelect.value;
        
        if (tipoPersona === 'Natural') {
            camposPersonaNatural.style.display = 'block';
            camposPersonaJuridica.style.display = 'none';
            
            // Hacer requeridos los campos específicos de persona natural
            apellidosInput.required = true;
            usoNsSelect.required = true;
            fechaNacimientoInput.required = true;
            generoSelect.required = true;
            
            // Verificar si debe mostrar el campo de nombre social
            handleUsoNsChange();
            
        } else if (tipoPersona === 'Jurídica') {
            camposPersonaNatural.style.display = 'none';
            camposPersonaJuridica.style.display = 'block';
            
            // Quitar requeridos de los campos específicos de persona natural
            apellidosInput.required = false;
            usoNsSelect.required = false;
            fechaNacimientoInput.required = false;
            generoSelect.required = false;
            nombreSocialInput.required = false;
            
        } else {
            // Si no se ha seleccionado ningún tipo, ocultar ambos conjuntos de campos
            camposPersonaNatural.style.display = 'none';
            camposPersonaJuridica.style.display = 'none';
        }
    }
    
    // Función para manejar el cambio en "Uso de Nombre Social"
    function handleUsoNsChange() {
        if (usoNsSelect.value === 'Sí') {
            campoNombreSocial.style.display = 'block';
            nombreSocialInput.required = true;
        } else {
            campoNombreSocial.style.display = 'none';
            nombreSocialInput.required = false;
            nombreSocialInput.value = ''; // Limpiar el valor si no se usa
        }
    }
    
    // Escuchar cambios en el select de tipo de persona
    tipoPersonaSelect.addEventListener('change', handleTipoPersonaChange);
    
    // Escuchar cambios en el select de uso de nombre social
    usoNsSelect.addEventListener('change', handleUsoNsChange);
    
    // Ejecutar una vez al cargar la página para establecer el estado inicial
    handleTipoPersonaChange();
    
    // Si hay un valor previo seleccionado (por ejemplo, en caso de error de validación)
    if (tipoPersonaSelect.value) {
        handleTipoPersonaChange();
    }
});
</script>
@endsection
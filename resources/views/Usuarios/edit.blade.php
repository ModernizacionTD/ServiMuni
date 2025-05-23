@extends('layouts.app')

@section('title', 'Editar Usuario - Sistema de Gestión')

@section('page-title', 'Editar Usuario')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-edit me-2"></i>Editar Usuario</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('usuarios.update', $usuario['rut']) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" id="rut" name="rut" class="form-control @error('rut') is-invalid @enderror" 
                               value="{{ old('rut', $usuario['rut']) }}" required>
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
                            <option value="Natural" {{ old('tipo_persona', $usuario['tipo_persona']) == 'Natural' ? 'selected' : '' }}>Persona Natural</option>
                            <option value="Jurídica" {{ old('tipo_persona', $usuario['tipo_persona']) == 'Jurídica' ? 'selected' : '' }}>Persona Jurídica</option>
                        </select>
                        @error('tipo_persona')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $usuario['nombre']) }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" 
                               value="{{ old('apellidos', $usuario['apellidos']) }}" required>
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
                            <option value="No" {{ old('uso_ns', $usuario['uso_ns']) == 'No' ? 'selected' : '' }}>No</option>
                            <option value="Sí" {{ old('uso_ns', $usuario['uso_ns']) == 'Sí' ? 'selected' : '' }}>Sí</option>
                        </select>
                        @error('uso_ns')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nombre_social" class="form-label">Nombre Social</label>
                        <input type="text" id="nombre_social" name="nombre_social" class="form-control @error('nombre_social') is-invalid @enderror" 
                               value="{{ old('nombre_social', $usuario['nombre_social']) }}">
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
                               value="{{ old('fecha_nacimiento', $usuario['fecha_nacimiento']) }}" required>
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
                            <option value="Masculino" {{ old('genero', $usuario['genero']) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('genero', $usuario['genero']) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="Transmasculino" {{ old('genero', $usuario['genero']) == 'Transmasculino' ? 'selected' : '' }}>Transmasculino</option>
                            <option value="Transfemenino" {{ old('genero', $usuario['genero']) == 'Transfemenino' ? 'selected' : '' }}>Transfemenino</option>
                            <option value="No decir" {{ old('genero', $usuario['genero']) == 'No decir' ? 'selected' : '' }}>Prefiero no decir</option>
                        </select>
                        @error('genero')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <h4 class="mt-4 mb-3">Información de Contacto</h4>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="telefono" class="form-label">Teléfono Principal</label>
                        <input type="tel" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror" 
                               value="{{ old('telefono', $usuario['telefono']) }}" required>
                        @error('telefono')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="telefono_2" class="form-label">Teléfono Alternativo</label>
                        <input type="tel" id="telefono_2" name="telefono_2" class="form-control @error('telefono_2') is-invalid @enderror" 
                               value="{{ old('telefono_2', $usuario['telefono_2']) }}">
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
                               value="{{ old('email', $usuario['email']) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email_2" class="form-label">Email Alternativo</label>
                        <input type="email" id="email_2" name="email_2" class="form-control @error('email_2') is-invalid @enderror" 
                               value="{{ old('email_2', $usuario['email_2']) }}">
                        @error('email_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control @error('direccion') is-invalid @enderror" 
                       value="{{ old('direccion', $usuario['direccion']) }}" required>
                @error('direccion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Usuario
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
@endsection
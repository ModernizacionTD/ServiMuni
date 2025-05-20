@extends('layouts.app')

@section('title', 'Editar Funcionario - Sistema de Gestión')

@section('page-title', 'Editar Funcionario')

@section('content')

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-edit me-2"></i>Editar Funcionario</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('funcionarios.update', $funcionario['id']) }}">
            @csrf
            @method('PUT')
            
            <!-- Información del ID -->
            <div class="form-group mb-4">
                <label for="id" class="form-label">ID del Funcionario</label>
                <input type="text" id="id" class="form-control" value="{{ $funcionario['id'] }}" readonly disabled>
                <input type="hidden" name="id" value="{{ $funcionario['id'] }}">
                <small class="text-muted">El ID no se puede modificar.</small>
            </div>
            
            <!-- Información básica -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Información Básica</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre Completo</label>
                                <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                    value="{{ old('nombre', $funcionario['nombre']) }}" required>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email', $funcionario['email']) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rol" class="form-label">Rol del Sistema</label>
                                <select id="rol" name="rol" class="form-control @error('rol') is-invalid @enderror" 
                                    {{ session('user_rol') != 'admin' ? 'disabled' : '' }} required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="admin" {{ old('rol', $funcionario['rol']) == 'admin' ? 'selected' : '' }}>
                                        Administrador
                                    </option>
                                    <option value="desarrollador" {{ old('rol', $funcionario['rol']) == 'desarrollador' ? 'selected' : '' }}>
                                        Desarrollador
                                    </option>
                                    <option value="orientador" {{ old('rol', $funcionario['rol']) == 'orientador' ? 'selected' : '' }}>
                                        Orientador
                                    </option>
                                    <option value="gestor" {{ old('rol', $funcionario['rol']) == 'gestor' ? 'selected' : '' }}>
                                        Gestor
                                    </option>
                                    <option value="tecnico" {{ old('rol', $funcionario['rol']) == 'tecnico' ? 'selected' : '' }}>
                                        Técnico
                                    </option>
                                </select>
                                @if(session('user_rol') != 'admin')
                                    <input type="hidden" name="rol" value="{{ $funcionario['rol'] }}">
                                    <small class="text-muted">Solo los administradores pueden cambiar roles.</small>
                                @endif
                                @error('rol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sección de Permisos (solo para administradores) -->
            @if(session('user_rol') == 'admin')
            <!-- Seguridad -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Seguridad</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                        placeholder="Dejar en blanco para mantener la contraseña actual">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Solo rellene este campo si desea cambiar la contraseña.</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                        class="form-control" placeholder="Confirme la nueva contraseña">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <span>Los campos marcados como obligatorios son necesarios para guardar los cambios.</span>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Funcionario
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

.card-header {
    font-weight: 600;
    color: #333;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.form-check-input {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    font-weight: 500;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    // Toggle para mostrar/ocultar confirmación de contraseña
    const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
    const passwordConfirmation = document.getElementById('password_confirmation');
    
    if (togglePasswordConfirmation && passwordConfirmation) {
        togglePasswordConfirmation.addEventListener('click', function () {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    // Validación de formulario
    const form = document.querySelector('form');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    if (form && passwordInput && passwordConfirmInput) {
        form.addEventListener('submit', function(event) {
            // Validar que las contraseñas coincidan si se ha introducido una
            if (passwordInput.value !== '' && passwordInput.value !== passwordConfirmInput.value) {
                event.preventDefault();
                
                // Crear alerta de error
                const errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger mt-3';
                errorAlert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden';
                
                // Insertar alerta después del campo de confirmación
                passwordConfirmInput.parentNode.parentNode.appendChild(errorAlert);
                
                // Eliminar la alerta después de 5 segundos
                setTimeout(function() {
                    errorAlert.remove();
                }, 5000);
                
                // Enfocar en el campo de confirmación
                passwordConfirmInput.focus();
            }
        });
    }
});
</script>
@endsection
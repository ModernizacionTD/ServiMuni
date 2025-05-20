@extends('layouts.app')

@section('title', 'Crear Funcionario - Sistema de Gestión')

@section('page-title', 'Crear Funcionario')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-user-plus me-2"></i>Crear Nuevo Funcionario</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('funcionarios.store') }}">
            @csrf
            
            <!-- Información básica -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                                    value="{{ old('nombre') }}" required>
                                <small class="text-muted">Nombre y apellidos del funcionario</small>
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                    value="{{ old('email') }}" required>
                                <small class="text-muted">Este correo se usará para iniciar sesión</small>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rol" class="form-label">Rol del Sistema <span class="text-danger">*</span></label>
                                <select id="rol" name="rol" class="form-control @error('rol') is-invalid @enderror" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="funcionario" {{ old('rol') == 'funcionario' ? 'selected' : '' }}>Funcionario</option>
                                    <option value="usuario" {{ old('rol') == 'usuario' ? 'selected' : '' }}>Usuario</option>
                                </select>
                                <small class="text-muted">Define los permisos que tendrá en el sistema</small>
                                @error('rol')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Credenciales de acceso -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Credenciales de Acceso</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                        required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                        class="form-control" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Repita la contraseña</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Permisos adicionales -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Permisos Adicionales</h5>
                    <small class="text-muted">Los permisos se asignan automáticamente según el rol seleccionado. Aquí puede agregar permisos adicionales.</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_dashboard" name="permisos[dashboard]" class="form-check-input" 
                                    value="1" {{ old('permisos.dashboard') ? 'checked' : '' }}>
                                <label for="perm_dashboard" class="form-check-label">Acceso al Dashboard</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_reportes" name="permisos[reportes]" class="form-check-input" 
                                    value="1" {{ old('permisos.reportes') ? 'checked' : '' }}>
                                <label for="perm_reportes" class="form-check-label">Ver Reportes</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_config" name="permisos[config]" class="form-check-input" 
                                    value="1" {{ old('permisos.config') ? 'checked' : '' }}>
                                <label for="perm_config" class="form-check-label">Configuración del Sistema</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_usuarios" name="permisos[usuarios]" class="form-check-input" 
                                    value="1" {{ old('permisos.usuarios') ? 'checked' : '' }}>
                                <label for="perm_usuarios" class="form-check-label">Gestión de Usuarios</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_departamentos" name="permisos[departamentos]" class="form-check-input" 
                                    value="1" {{ old('permisos.departamentos') ? 'checked' : '' }}>
                                <label for="perm_departamentos" class="form-check-label">Gestión de Departamentos</label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check mb-3">
                                <input type="checkbox" id="perm_requerimientos" name="permisos[requerimientos]" class="form-check-input" 
                                    value="1" {{ old('permisos.requerimientos') ? 'checked' : '' }}>
                                <label for="perm_requerimientos" class="form-check-label">Gestión de Requerimientos</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notificaciones -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bell me-2"></i>Notificaciones</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" id="notificar_creacion" name="notificar_creacion" class="form-check-input" value="1" checked>
                        <label for="notificar_creacion" class="form-check-label">Enviar correo electrónico al funcionario con sus credenciales</label>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Los campos marcados con <span class="text-danger">*</span> son obligatorios.
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Funcionario
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
    padding: 12px 20px;
}

.card-header h5 {
    margin: 0;
    font-size: 1.1rem;
}

.form-check-input {
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #ced4da;
}

.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.text-danger {
    color: #dc3545;
}

.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    margin-top: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    
    if (togglePassword && password) {
        togglePassword.addEventListener('click', function() {
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
        togglePasswordConfirmation.addEventListener('click', function() {
            const type = passwordConfirmation.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmation.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }
    
    // Validación de formulario
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            
            // Validar que las contraseñas coincidan
            if (password.value !== passwordConfirmation.value) {
                event.preventDefault();
                
                // Mostrar mensaje de error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Las contraseñas no coinciden.';
                
                // Insertar después del campo de confirmación de contraseña
                passwordConfirmation.parentNode.parentNode.appendChild(errorDiv);
                
                // Eliminar mensaje después de 5 segundos
                setTimeout(function() {
                    errorDiv.remove();
                }, 5000);
                
                // Enfocar el campo de confirmación
                passwordConfirmation.focus();
                return false;
            }
            
            // Validar longitud mínima de la contraseña
            if (password.value.length < 6) {
                event.preventDefault();
                
                // Mostrar mensaje de error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> La contraseña debe tener al menos 6 caracteres.';
                
                // Insertar después del campo de contraseña
                password.parentNode.parentNode.appendChild(errorDiv);
                
                // Eliminar mensaje después de 5 segundos
                setTimeout(function() {
                    errorDiv.remove();
                }, 5000);
                
                // Enfocar el campo de contraseña
                password.focus();
                return false;
            }
        });
    }
    
    // Manejar cambios en el rol seleccionado
    const rolSelect = document.getElementById('rol');
    
    if (rolSelect) {
        rolSelect.addEventListener('change', function() {
            const rol = this.value;
            
            // Desmarcar todos los checkboxes de permisos
            document.querySelectorAll('input[name^="permisos["]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
            
            // Marcar permisos según el rol seleccionado
            if (rol === 'admin') {
                // Para administradores, marcar todos los permisos
                document.querySelectorAll('input[name^="permisos["]').forEach(function(checkbox) {
                    checkbox.checked = true;
                });
            } else if (rol === 'funcionario') {
                // Para funcionarios, marcar permisos específicos
                document.getElementById('perm_dashboard').checked = true;
                document.getElementById('perm_reportes').checked = true;
                document.getElementById('perm_requerimientos').checked = true;
            } else if (rol === 'usuario') {
                // Para usuarios regulares, marcar permisos básicos
                document.getElementById('perm_dashboard').checked = true;
            }
        });
    }
});
</script>
@endsection
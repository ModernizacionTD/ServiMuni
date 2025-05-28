@extends('layouts.app')

@section('title', 'Mi Cuenta - ServiMuni')

@section('page-title', 'Mi Cuenta')

@section('content')

<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<div class="profile-container">
    <!-- Sección del Perfil -->
    <div class="card profile-card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-user-circle me-2"></i>Información Personal</h2>
        </div>
        <div class="card-body">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ substr($funcionario['nombre'], 0, 1) }}
                </div>
                <div class="profile-details">
                    <h3 class="profile-name">{{ $funcionario['nombre'] }}</h3>
                    <span class="profile-role badge 
                        @if($funcionario['rol'] == 'admin') bg-danger
                        @elseif($funcionario['rol'] == 'funcionario') bg-primary
                        @else bg-success @endif">
                        {{ ucfirst($funcionario['rol']) }}
                    </span>
                </div>
            </div>

            <div class="profile-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i> Correo Electrónico
                        </div>
                        <div class="info-value">{{ $funcionario['email'] }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-shield-alt"></i> Rol del Sistema
                        </div>
                        <div class="info-value">{{ ucfirst($funcionario['rol']) }}</div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-key"></i> Contraseña
                        </div>
                        <div class="info-value">••••••••••
                            <a href="{{ route('funcionarios.showChangePassword') }}" class="btn btn-sm btn-outline-primary ms-3">
                                Cambiar Contraseña
                            </a>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock"></i> Último Acceso
                        </div>
                        <div class="info-value">{{ date('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones -->
    <div class="grid-layout">
        <div class="card action-card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-cog me-2"></i>Acciones de Cuenta</h2>
            </div>
            <div class="card-body">
                <div class="account-actions">
                    <a href="{{ route('funcionarios.edit', session('user_id')) }}" class="action-button">
                        <div class="action-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="action-text">
                            <h4>Editar Perfil</h4>
                            <p>Actualiza tu información personal</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('funcionarios.showChangePassword') }}" class="action-button">
                        <div class="action-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="action-text">
                            <h4>Cambiar Contraseña</h4>
                            <p>Actualiza tu contraseña de acceso</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('dashboard') }}" class="action-button">
                        <div class="action-icon">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <div class="action-text">
                            <h4>Dashboard</h4>
                            <p>Volver a la página principal</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>


    <!-- Preferencias del Sistema -->
    <div class="card settings-card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-sliders-h me-2"></i>Preferencias del Sistema</h2>
        </div>
        <div class="card-body">
            <div class="settings-container">
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Notificaciones por Email</h4>
                        <p>Recibir notificaciones del sistema por correo electrónico</p>
                    </div>
                    <div class="setting-control">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        </div>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Alertas del Sistema</h4>
                        <p>Recibir alertas en el panel de control</p>
                    </div>
                    <div class="setting-control">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="systemAlerts" checked>
                        </div>
                    </div>
                </div>
                
                <div class="setting-item">
                    <div class="setting-info">
                        <h4>Sesión Activa</h4>
                        <p>Mantener sesión iniciada después de cerrar el navegador</p>
                    </div>
                    <div class="setting-control">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="keepSession">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="settings-actions">
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Preferencias
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Para implementar la funcionalidad de cambiar preferencias
    const savePreferencesButton = document.querySelector('.settings-actions .btn');
    
    if (savePreferencesButton) {
        savePreferencesButton.addEventListener('click', function() {
            // Aquí podrías implementar una llamada AJAX para guardar preferencias
            // Por ahora, simplemente mostraremos una alerta
            alert('Preferencias guardadas correctamente');
        });
    }
    
    // Inicializar los interruptores con los valores almacenados en localStorage
    const emailNotifications = document.getElementById('emailNotifications');
    const systemAlerts = document.getElementById('systemAlerts');
    const keepSession = document.getElementById('keepSession');
    
    if (emailNotifications) {
        emailNotifications.checked = localStorage.getItem('emailNotifications') !== 'false';
        emailNotifications.addEventListener('change', function() {
            localStorage.setItem('emailNotifications', this.checked);
        });
    }
    
    if (systemAlerts) {
        systemAlerts.checked = localStorage.getItem('systemAlerts') !== 'false';
        systemAlerts.addEventListener('change', function() {
            localStorage.setItem('systemAlerts', this.checked);
        });
    }
    
    if (keepSession) {
        keepSession.checked = localStorage.getItem('keepSession') === 'true';
        keepSession.addEventListener('change', function() {
            localStorage.setItem('keepSession', this.checked);
        });
    }
});
</script>
@endsection
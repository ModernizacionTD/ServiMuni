@extends('layouts.app')

@section('title', 'Mi Cuenta - ServiMuni')

@section('page-title', 'Mi Cuenta')

@section('content')
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

        <!-- Actividad Reciente -->
        <div class="card activity-card">
            <div class="card-header">
                <h2 class="card-title"><i class="fas fa-history me-2"></i>Actividad Reciente</h2>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    <div class="activity-item">
                        <div class="activity-dot bg-primary"></div>
                        <div class="activity-content">
                            <div class="activity-date">Hoy, {{ date('H:i') }}</div>
                            <p class="activity-text">Inicio de sesión exitoso</p>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-dot bg-success"></div>
                        <div class="activity-content">
                            <div class="activity-date">{{ date('d/m/Y', strtotime('-1 day')) }}</div>
                            <p class="activity-text">Actualización de perfil</p>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-dot bg-info"></div>
                        <div class="activity-content">
                            <div class="activity-date">{{ date('d/m/Y', strtotime('-3 days')) }}</div>
                            <p class="activity-text">Cambio de contraseña</p>
                        </div>
                    </div>
                </div>
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

<style>
/* Contenedor principal */
.profile-container {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* Perfil header */
.profile-header {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 600;
    margin-right: 20px;
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 5px;
}

.profile-role {
    font-size: 0.8rem;
    font-weight: 500;
    padding: 5px 10px;
}

/* Información del perfil */
.profile-info {
    background-color: var(--bg-light);
    border-radius: var(--border-radius);
    padding: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    font-size: 0.85rem;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-label i {
    color: var(--primary-color);
}

.info-value {
    font-weight: 500;
    font-size: 1rem;
}

/* Layout de grid para acciones y actividad */
.grid-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

/* Acciones de cuenta */
.account-actions {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.action-button {
    display: flex;
    align-items: center;
    background-color: var(--bg-light);
    border-radius: var(--border-radius);
    padding: 15px;
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.3s ease;
}

.action-button:hover {
    transform: translateX(5px);
    background-color: #e9ecef;
}

.action-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: var(--primary-color);
    color: white;
    border-radius: 10px;
    margin-right: 15px;
    font-size: 1.2rem;
}

.action-text h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.action-text p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--text-light);
}

/* Actividad Reciente */
.activity-timeline {
    position: relative;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    height: 100%;
    width: 2px;
    background-color: var(--bg-light);
}

.activity-item {
    display: flex;
    margin-bottom: 20px;
    position: relative;
}

.activity-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 15px;
    margin-top: 5px;
    z-index: 1;
}

.activity-content {
    flex: 1;
}

.activity-date {
    font-size: 0.75rem;
    color: var(--text-light);
    margin-bottom: 3px;
}

.activity-text {
    margin: 0;
    font-size: 0.9rem;
}

/* Preferencias */
.settings-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-radius: var(--border-radius);
    background-color: var(--bg-light);
}

.setting-info h4 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}

.setting-info p {
    margin: 5px 0 0;
    font-size: 0.85rem;
    color: var(--text-light);
}

.form-check-input {
    width: 3em;
    height: 1.5em;
}

.settings-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}

/* Responsive */
@media (max-width: 992px) {
    .grid-layout {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .profile-header {
        flex-direction: column;
        text-align: center;
    }
    
    .profile-avatar {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .setting-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .setting-control {
        align-self: flex-start;
    }
}
</style>

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
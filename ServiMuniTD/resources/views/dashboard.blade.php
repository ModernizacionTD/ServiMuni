@extends('layouts.app')

@section('title', 'Dashboard - ServiMuni')

@section('page-title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

<!-- Sección de Bienvenida -->
<div class="welcome-section">
    <div class="welcome-content">
        <h2 class="welcome-title">
            Bienvenido a ServiMuni, {{ session('user_nombre') }}! 👋
        </h2>
        <p class="welcome-subtitle">
            Sistema de toma de requerimientos y gestión de requerimientos digitales.
        </p>
    </div>
    <div class="welcome-date">
        <i class="fas fa-calendar-alt"></i>
        <span>{{ date('l, d F Y') }}</span>
    </div>
</div>

<!-- Estadísticas -->
<div class="stats-grid">

    <!-- Departamentos Card -->
    <div class="stat-card stat-primary">
        <div class="stat-body">
            <div class="stat-icon">
                <i class="fas fa-sitemap"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ count($departamentos) }}</h2>
                <p class="stat-label">Departamentos</p>
                <div class="stat-progress">
                    <div class="stat-chart">
                        <canvas id="departamentosChart" height="40"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <a href="{{ route('departamentos.index') }}" class="stat-link">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- Requerimientos Card -->
    <div class="stat-card stat-warning">
        <div class="stat-body">
            <div class="stat-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ count($requerimientos) }}</h2>
                <p class="stat-label">Requerimientos</p>
                <div class="stat-chart">
                    <canvas id="requerimientosChart" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <a href="{{ route('requerimientos.index') }}" class="stat-link">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- Usuarios Card -->
    <div class="stat-card stat-success">
        <div class="stat-body">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ count($usuarios) }}</h2>
                <p class="stat-label">Usuarios</p>
                <div class="stat-chart">
                    <canvas id="usuariosChart" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <a href="{{ route('usuarios.index') }}" class="stat-link">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- Funcionarios Card -->
    <div class="stat-card stat-info">
        <div class="stat-body">
            <div class="stat-icon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="stat-content">
                <h2 class="stat-number">{{ count($funcionarios) }}</h2>
                <p class="stat-label">Funcionarios</p>
                <div class="user-avatars">
                    @php
                        $displayedAvatars = 0;
                        $maxAvatars = 3;
                    @endphp
                    
                    @foreach($funcionarios as $funcionario)
                        @if($displayedAvatars < $maxAvatars)
                            <div class="avatar">
                                {{ substr($funcionario['nombre'] ?? 'U', 0, 1) }}{{ substr($funcionario['apellidos'] ?? 'S', 0, 1) }}
                            </div>
                            @php $displayedAvatars++; @endphp
                        @endif
                    @endforeach
                    
                    @if(count($funcionarios) > $maxAvatars)
                        <div class="avatar more">+{{ count($funcionarios) - $maxAvatars }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="stat-footer">
            <a href="{{ route('funcionarios.index') }}" class="stat-link">
                Ver todos <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<!-- Área de Contenido Principal -->
<div class="dashboard-grid">
    <!-- Acciones Rápidas -->
    <div class="card action-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-bolt"></i>Acciones Rápidas
            </h2>
        </div>
        <div class="card-body">
            <div class="quick-actions">
                @if(session('user_rol') == 'admin')
                <a href="{{ route('departamentos.create') }}" class="action-item">
                    <div class="action-icon bg-primary">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <div class="action-content">
                        <h6>Nuevo Departamento</h6>
                        <p>Agregar departamento municipal</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('usuarios.create') }}" class="action-item">
                    <div class="action-icon bg-success">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="action-content">
                        <h6>Agregar Usuario</h6>
                        <p>Nuevo funcionario</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
                @endif

                <a href="{{ route('requerimientos.create') }}" class="action-item">
                    <div class="action-icon bg-warning">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="action-content">
                        <h6>Nuevo Requerimiento</h6>
                        <p>Crear solicitud</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('requerimientos.index') }}" class="action-item">
                    <div class="action-icon bg-info">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="action-content">
                        <h6>Ver Requerimientos</h6>
                        <p>Lista completa</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="card activity-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-history"></i>Actividad Reciente
            </h2>
            <span class="badge bg-primary">Hoy</span>
        </div>
        <div class="card-body">
            <div class="activity-timeline">
                @if(count($actividades) > 0)
                    @foreach($actividades as $actividad)
                    <div class="activity-item">
                        <div class="activity-dot {{ $actividad['color'] ?? 'bg-info' }}"></div>
                        <div class="activity-content">
                            <p class="activity-text">
                                {!! $actividad['texto'] !!}
                            </p>
                            <small class="text-muted">{{ $actividad['tiempo'] }}</small>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="activity-item">
                        <div class="activity-dot bg-info"></div>
                        <div class="activity-content">
                            <p class="activity-text">
                                <strong>Departamento de Finanzas</strong> actualizado
                            </p>
                            <small class="text-muted">Hace 2 horas</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-dot bg-warning"></div>
                        <div class="activity-content">
                            <p class="activity-text">
                                Nuevo usuario <strong>Carlos Navarro</strong> agregado
                            </p>
                            <small class="text-muted">Hace 4 horas</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-dot bg-success"></div>
                        <div class="activity-content">
                            <p class="activity-text">
                                <strong>Portal Ciudadano</strong> finalizado
                            </p>
                            <small class="text-muted">Ayer</small>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-dot bg-danger"></div>
                        <div class="activity-content">
                            <p class="activity-text">
                                <strong>Mantenimiento de sistema</strong> programado
                            </p>
                            <small class="text-muted">Hace 2 días</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Gráfico de Estado -->
    <div class="card chart-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-chart-pie"></i>Distribución de Servicios
            </h2>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="serviciosChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color bg-success"></span>
                    <span>Requerimientos Digitales (45%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-warning"></span>
                    <span>Servicios Sociales (30%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-info"></span>
                    <span>Obras Públicas (15%)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color bg-secondary"></span>
                    <span>Otros (10%)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para los gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mini gráfico para departamentos
    const departamentosCtx = document.getElementById('departamentosChart').getContext('2d');
    new Chart(departamentosCtx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                data: [10, 15, 18, 22, {{ count($departamentos) }}],
                borderColor: '#2563eb',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        }
    });
    
    // Mini gráfico para requerimientos
    const requerimientosCtx = document.getElementById('requerimientosChart').getContext('2d');
    new Chart(requerimientosCtx, {
        type: 'line',
        data: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
            datasets: [{
                data: [15, 25, 20, 30, {{ count($requerimientos) }}],
                borderColor: '#ffc107',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: false
                },
                y: {
                    display: false
                }
            }
        }
    });

    // Gráfico de distribución de servicios
    const serviciosCtx = document.getElementById('serviciosChart').getContext('2d');
    new Chart(serviciosCtx, {
        type: 'doughnut',
        data: {
            labels: ['Requerimientos Digitales', 'Servicios Sociales', 'Obras Públicas', 'Otros'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#17a2b8',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endsection
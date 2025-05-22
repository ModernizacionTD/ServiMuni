@extends('layouts.app')

@section('title', 'Dashboard - ServiMuni')

@section('page-title', 'Dashboard')

@section('content')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                                @php
                                    $nombre = $funcionario['nombre'] ?? 'U';
                                    $palabras = explode(' ', trim($nombre));
                                    if (count($palabras) === 1) {
                                        $iniciales = strlen($palabras[0]) >= 2 
                                            ? strtoupper(substr($palabras[0], 0, 2))
                                            : strtoupper($palabras[0][0] . $palabras[0][0]);
                                    } else {
                                        $iniciales = strtoupper($palabras[0][0] . $palabras[1][0]);
                                    }
                                @endphp
                                {{ $iniciales }}
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
                        <h6>Agregar Funcionario</h6>
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
                        <p>Crear requerimiento</p>
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

    <!-- Gráfico de Requerimientos por Departamento -->
    <div class="card chart-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-chart-pie"></i>Requerimientos por Departamento
            </h2>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="serviciosChart"></canvas>
            </div>
            <div class="chart-legend" id="chartLegend">
                <!-- La leyenda se generará dinámicamente -->
            </div>
        </div>
    </div>
</div>

@php
// Procesar datos para el gráfico
$departamentosData = [];
$totalRequerimientos = 0;

// Contar requerimientos por departamento
foreach($departamentos as $departamento) {
    $requerimientosCount = 0;
    foreach($requerimientos as $requerimiento) {
        if(isset($requerimiento['departamento_id']) && $requerimiento['departamento_id'] == $departamento['id']) {
            $requerimientosCount++;
        }
    }
    
    if($requerimientosCount > 0) {
        $departamentosData[] = [
            'nombre' => $departamento['nombre'],
            'count' => $requerimientosCount,
            'id' => $departamento['id']
        ];
        $totalRequerimientos += $requerimientosCount;
    }
}

// Si no hay requerimientos, mostrar mensaje
if(empty($departamentosData)) {
    $departamentosData[] = [
        'nombre' => 'Sin requerimientos',
        'count' => 1,
        'id' => 0
    ];
    $totalRequerimientos = 1;
}

// Ordenar por cantidad (mayor a menor)
usort($departamentosData, function($a, $b) {
    return $b['count'] - $a['count'];
});

// Limitar a los top 6 departamentos para mejor visualización
$departamentosData = array_slice($departamentosData, 0, 6);

// Calcular porcentajes
foreach($departamentosData as &$dept) {
    $dept['porcentaje'] = round(($dept['count'] / $totalRequerimientos) * 100, 1);
}
@endphp

<!-- JavaScript para los gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si Chart.js está disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está cargado');
        return;
    }
    
    // Datos de departamentos desde PHP
    const departamentosData = @json($departamentosData);
    const totalRequerimientos = {{ $totalRequerimientos }};
    
    console.log('Datos de departamentos:', departamentosData);
    console.log('Total requerimientos:', totalRequerimientos);
    
    // Colores para el gráfico
    const colores = [
        '#3b82f6', // Azul
        '#10b981', // Verde
        '#f59e0b', // Amarillo
        '#ef4444', // Rojo
        '#8b5cf6', // Púrpura
        '#06b6d4', // Cian
        '#6b7280'  // Gris
    ];
    
    // Mini gráfico para departamentos
    const departamentosChartElement = document.getElementById('departamentosChart');
    if (departamentosChartElement) {
        const departamentosCtx = departamentosChartElement.getContext('2d');
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
    }
    
    // Mini gráfico para requerimientos
    const requerimientosChartElement = document.getElementById('requerimientosChart');
    if (requerimientosChartElement) {
        const requerimientosCtx = requerimientosChartElement.getContext('2d');
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
    }

    // Gráfico de requerimientos por departamento
    const serviciosChartElement = document.getElementById('serviciosChart');
    if (serviciosChartElement) {
        const serviciosCtx = serviciosChartElement.getContext('2d');
        
        // Preparar datos para el gráfico
        const labels = departamentosData.map(dept => dept.nombre);
        const data = departamentosData.map(dept => dept.count);
        const backgroundColors = departamentosData.map((dept, index) => colores[index % colores.length]);
        
        console.log('Labels:', labels);
        console.log('Data:', data);
        console.log('Background colors:', backgroundColors);
        
        new Chart(serviciosCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dept = departamentosData[context.dataIndex];
                                return `${dept.nombre}: ${dept.count} requerimientos (${dept.porcentaje}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
        
        console.log('Gráfico de servicios creado exitosamente');
    } else {
        console.error('Elemento serviciosChart no encontrado');
    }
    
    // Generar leyenda personalizada
    const legendContainer = document.getElementById('chartLegend');
    if (legendContainer) {
        legendContainer.innerHTML = '';
        
        if (departamentosData.length > 0 && departamentosData[0].nombre !== 'Sin requerimientos') {
            departamentosData.forEach((dept, index) => {
                const legendItem = document.createElement('div');
                legendItem.className = 'legend-item';
                legendItem.innerHTML = `
                    <span class="legend-color" style="background-color: ${colores[index % colores.length]}"></span>
                    <span>${dept.nombre} (${dept.count} - ${dept.porcentaje}%)</span>
                `;
                legendContainer.appendChild(legendItem);
            });
        } else {
            const noDataItem = document.createElement('div');
            noDataItem.className = 'legend-item';
            noDataItem.innerHTML = `
                <span class="legend-color" style="background-color: #6b7280"></span>
                <span>No hay requerimientos registrados</span>
            `;
            legendContainer.appendChild(noDataItem);
        }
    } else {
        console.error('Elemento chartLegend no encontrado');
    }
});
</script>
@endsection
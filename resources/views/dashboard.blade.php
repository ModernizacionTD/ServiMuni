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
            Bienvenido a ServiMuni, {{ $nombre }}! 👋
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

<!-- Estadísticas Principales -->
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
                @if($rol == 'admin')
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
                        <p>Nuevo usuario</p>
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

                <a href="{{ route('buscar.usuario') }}" class="action-item">
                    <div class="action-icon bg-info">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="action-content">
                        <h6>Buscar Usuario</h6>
                        <p>Crear solicitud</p>
                    </div>
                    <i class="fas fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- SECCIÓN DE RENDIMIENTO MENSUAL -->
    <div class="card performance-card">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-chart-line"></i>Rendimiento Mensual
            </h2>
            <div class="performance-badges">
                <span class="badge bg-info">{{ $mesActual }}</span>
                @if($solicitudesVencidas > 0)
                <span class="badge bg-danger">{{ $solicitudesVencidas }} vencidas</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <!-- Métricas principales -->
            <div class="performance-metrics">
                <div class="metric-item">
                    <div class="metric-icon bg-success">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="metric-data">
                        <div class="metric-number">{{ $solicitudesCompletadas }}</div>
                        <div class="metric-label">Completadas</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i> +{{ round(($solicitudesCompletadas / max($totalSolicitudesMes, 1)) * 100) }}%
                        </div>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="metric-data">
                        <div class="metric-number">{{ $solicitudesEnProceso }}</div>
                        <div class="metric-label">En Proceso</div>
                        <div class="metric-change neutral">
                            <i class="fas fa-minus"></i> {{ round(($solicitudesEnProceso / max($totalSolicitudesMes, 1)) * 100) }}%
                        </div>
                    </div>
                </div>
                
                <div class="metric-item">
                    <div class="metric-icon bg-primary">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="metric-data">
                        <div class="metric-number">{{ $solicitudesNuevas }}</div>
                        <div class="metric-label">Nuevas</div>
                        <div class="metric-change positive">
                            <i class="fas fa-arrow-up"></i> {{ $solicitudesHoy }} hoy
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de rendimiento -->
            <div class="performance-chart">
                <canvas id="performanceChart"></canvas>
            </div>
            
            <!-- Resumen de rendimiento -->
            <div class="performance-summary">
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Tiempo Promedio</span>
                            <span class="summary-value">{{ $tiempoPromedio }} días</span>
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-target"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Eficiencia</span>
                            <span class="summary-value {{ $eficiencia >= 80 ? 'text-success' : ($eficiencia >= 60 ? 'text-warning' : 'text-danger') }}">{{ $eficiencia }}%</span>
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Total del Mes</span>
                            <span class="summary-value">{{ $totalSolicitudesMes }}</span>
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="summary-content">
                            <span class="summary-label">Promedio Semanal</span>
                            <span class="summary-value">{{ $promedioSemanal }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alertas de rendimiento -->
            @if($solicitudesVencidas > 0 || $eficiencia < 60)
            <div class="performance-alerts">
                @if($solicitudesVencidas > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Atención:</strong> Hay {{ $solicitudesVencidas }} solicitudes vencidas que requieren seguimiento.
                </div>
                @endif
                
                @if($eficiencia < 60)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Mejora:</strong> La eficiencia está por debajo del 60%. Considera revisar los procesos.
                </div>
                @endif
            </div>
            @endif
            
            <!-- Botón para ver más detalles -->
            <div class="performance-footer">
                <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-chart-bar"></i> Ver Reporte Completo
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="refreshMetrics()">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
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

<!-- CSS adicional para la sección de rendimiento -->
<style>
/* ===== ESTILOS PARA RENDIMIENTO ===== */
.performance-card {
    grid-column: span 1;
}

.performance-badges {
    display: flex;
    gap: 8px;
    align-items: center;
}

.performance-metrics {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    gap: 12px;
}

.metric-item {
    display: flex;
    align-items: center;
    background-color: var(--bg-light);
    padding: 16px;
    border-radius: 12px;
    gap: 12px;
    flex: 1;
    transition: all 0.3s ease;
}

.metric-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.metric-data {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.metric-number {
    font-weight: 700;
    font-size: 1.5rem;
    line-height: 1;
    color: var(--text-color);
}

.metric-label {
    font-size: 0.8rem;
    color: var(--text-light);
    font-weight: 500;
}

.metric-change {
    font-size: 0.7rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 2px;
}

.metric-change.positive {
    color: var(--success-color);
}

.metric-change.negative {
    color: var(--danger-color);
}

.metric-change.neutral {
    color: var(--text-muted);
}

.performance-chart {
    margin: 25px 0;
    height: 120px;
    position: relative;
    background-color: var(--bg-light);
    border-radius: 12px;
    padding: 15px;
}

.performance-summary {
    margin-top: 20px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.summary-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background-color: white;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.summary-icon {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.summary-content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.summary-label {
    font-size: 0.7rem;
    color: var(--text-light);
    font-weight: 500;
}

.summary-value {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-color);
}

.performance-alerts {
    margin: 15px 0;
}

.performance-alerts .alert {
    padding: 10px 12px;
    margin-bottom: 8px;
    font-size: 0.85rem;
}

.performance-footer {
    margin-top: 20px;
    text-align: center;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
    display: flex;
    gap: 10px;
    justify-content: center;
}

/* Responsive para métricas */
@media (max-width: 768px) {
    .performance-metrics {
        flex-direction: column;
        gap: 8px;
    }
    
    .metric-item {
        padding: 12px;
    }
    
    .metric-number {
        font-size: 1.3rem;
    }
    
    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .performance-footer {
        flex-direction: column;
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- JavaScript para los gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si Chart.js está disponible
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está cargado');
        return;
    }
    
    // Datos desde PHP
    const departamentosData = @json($departamentosData ?? []);
    const totalRequerimientos = {{ $totalRequerimientos ?? 1 }};
    const datosRendimiento = @json($datosGraficoRendimiento ?? []);
    
    // Colores para los gráficos
    const colores = [
        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#6b7280'
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
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } }
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
                plugins: { legend: { display: false } },
                scales: { x: { display: false }, y: { display: false } }
            }
        });
    }

    // Gráfico de rendimiento (principal)
    const performanceChartElement = document.getElementById('performanceChart');
    if (performanceChartElement && datosRendimiento.length > 0) {
        const performanceCtx = performanceChartElement.getContext('2d');
        
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: datosRendimiento.map(d => d.fecha),
                datasets: [
                    {
                        label: 'Nuevas',
                        data: datosRendimiento.map(d => d.nuevas),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Completadas',
                        data: datosRendimiento.map(d => d.completadas),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.1)' },
                        ticks: { font: { size: 11 } }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }

    // Gráfico de departamentos (tipo donut)
    const serviciosChartElement = document.getElementById('serviciosChart');
    if (serviciosChartElement) {
        const serviciosCtx = serviciosChartElement.getContext('2d');
        
        const labels = departamentosData.map(dept => dept.nombre);
        const data = departamentosData.map(dept => dept.count);
        const backgroundColors = departamentosData.map((dept, index) => colores[index % colores.length]);
        
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
                    legend: { display: false },
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
    }
    
    // Generar leyenda personalizada para el gráfico de departamentos
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
    }
});

// Función para refrescar métricas
function refreshMetrics() {
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    
    // Animación de carga
    icon.classList.add('fa-spin');
    button.disabled = true;
    
    // Simular llamada AJAX (reemplaza con llamada real a la API)
    fetch('{{ route("dashboard.metrics") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Aquí podrías actualizar los números sin recargar la página
                console.log('Métricas actualizadas:', data);
                // Ejemplo: actualizar un elemento específico
                // document.querySelector('.metric-number').textContent = data.solicitudesCompletadas;
            }
        })
        .catch(error => {
            console.error('Error al actualizar métricas:', error);
        })
        .finally(() => {
            // Restaurar botón
            setTimeout(() => {
                icon.classList.remove('fa-spin');
                button.disabled = false;
            }, 1000);
        });
}
</script>
@endsection
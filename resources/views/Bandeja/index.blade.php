@extends('layouts.app')

@section('title', 'Bandeja de Solicitudes - ServiMuni')

@section('page-title', 'Mi Bandeja de Solicitudes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
<link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/bandeja.css') }}">
<div class="table-view-container filter-view-container">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary">{{ $estadisticas['total'] }}</h5>
                    <p class="card-text">Total Solicitudes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">{{ $estadisticas['pendientes'] }}</h5>
                    <p class="card-text">Pendientes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h5 class="card-title text-info">{{ $estadisticas['en_proceso'] }}</h5>
                    <p class="card-text">En Proceso</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">{{ $estadisticas['vencidas'] }}</h5>
                    <p class="card-text">Vencidas</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-inbox me-2"></i>
                Bandeja: {{ $nombreDepartamento }}
                <span class="badge bg-white text-primary ms-2">{{ count($solicitudes) }}</span>
            </h3>
            <div class="d-flex gap-2">
                <span class="badge bg-info">{{ ucfirst($rol) }}</span>
                <button class="btn btn-sm btn-outline-primary" id="refreshBtn">
                    <i class="fas fa-sync-alt"></i> Actualizar
                </button>
            </div>
        </div>
        
        <!-- Barra de filtros -->
        <div class="filters-bar">
            <form method="GET" action="{{ route('bandeja.index') }}" id="filtrosForm">
                <div class="filters-container">
                    <!-- Búsqueda -->
                    <div class="filter-item search-filter">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="busqueda" class="form-control filter-search-input" 
                                   placeholder="Buscar solicitud..." value="{{ $filtros['busqueda'] }}">
                        </div>
                    </div>
                    
                    <!-- Filtro por estado -->
                    <div class="filter-item">
                        <select name="estado" class="form-select filter-select">
                            <option value="">Todos los estados</option>
                            <option value="Pendiente" {{ $filtros['estado'] == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="En proceso" {{ $filtros['estado'] == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                            <option value="Completado" {{ $filtros['estado'] == 'Completado' ? 'selected' : '' }}>Completado</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por etapa -->
                    <div class="filter-item">
                        <select name="etapa" class="form-select filter-select">
                            <option value="">Todas las etapas</option>
                            <option value="Ingreso" {{ $filtros['etapa'] == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                            <option value="Asignada" {{ $filtros['etapa'] == 'Asignada' ? 'selected' : '' }}>Asignada</option>
                            <option value="En proceso" {{ $filtros['etapa'] == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                            <option value="Revisión" {{ $filtros['etapa'] == 'Revisión' ? 'selected' : '' }}>Revisión</option>
                            <option value="Completada" {{ $filtros['etapa'] == 'Completada' ? 'selected' : '' }}>Completada</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por prioridad -->
                    <div class="filter-item">
                        <select name="prioridad" class="form-select filter-select">
                            <option value="">Todas las prioridades</option>
                            <option value="urgente" {{ $filtros['prioridad'] == 'urgente' ? 'selected' : '' }}>🔴 Urgente (Vencidas)</option>
                            <option value="alta" {{ $filtros['prioridad'] == 'alta' ? 'selected' : '' }}>🟠 Alta (≤3 días)</option>
                            <option value="media" {{ $filtros['prioridad'] == 'media' ? 'selected' : '' }}>🟡 Media (4-7 días)</option>
                            <option value="baja" {{ $filtros['prioridad'] == 'baja' ? 'selected' : '' }}>🟢 Baja (>7 días)</option>
                            <option value="sin_fecha" {{ $filtros['prioridad'] == 'sin_fecha' ? 'selected' : '' }}>⚪ Sin fecha</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por fecha -->
                    <div class="filter-item">
                        <select name="fecha" class="form-select filter-select">
                            <option value="">Todas las fechas</option>
                            <option value="hoy" {{ $filtros['fecha'] == 'hoy' ? 'selected' : '' }}>Hoy</option>
                            <option value="ayer" {{ $filtros['fecha'] == 'ayer' ? 'selected' : '' }}>Ayer</option>
                            <option value="semana" {{ $filtros['fecha'] == 'semana' ? 'selected' : '' }}>Esta semana</option>
                            <option value="mes" {{ $filtros['fecha'] == 'mes' ? 'selected' : '' }}>Este mes</option>
                        </select>
                    </div>
                    
                    <!-- Botones -->
                    <div class="filter-item">
                        <button type="submit" class="btn btn-primary filter-apply-btn">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                    
                    <div class="filter-item">
                        <a href="{{ route('bandeja.index') }}" class="btn btn-outline-secondary filter-reset-btn">
                            <i class="fas fa-sync-alt"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="12%">Usuario</th>
                            <th width="15%">Requerimiento</th>
                            <th width="20%">Descripción</th>
                            <th width="12%">Ubicación</th>
                            <th width="10%">Fecha</th>
                            <th width="8%">Estado</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php
                                $usuario = $usuarios[$solicitud['rut_usuario']] ?? null;
                                $requerimiento = $requerimientos[$solicitud['requerimiento_id']] ?? null;
                                $departamento = null;
                                if ($requerimiento && isset($requerimiento['departamento_id'])) {
                                    $departamento = $departamentos[$requerimiento['departamento_id']] ?? null;
                                }
                                
                                // Calcular prioridad por fecha
                                $prioridad = 'sin-fecha';
                                $diasRestantes = null;
                                if (!empty($solicitud['fecha_estimada_op'])) {
                                    $fechaEstimada = \Carbon\Carbon::parse($solicitud['fecha_estimada_op']);
                                    $diasRestantes = \Carbon\Carbon::now()->diffInDays($fechaEstimada, false);
                                    
                                    if ($diasRestantes < 0) {
                                        $prioridad = 'urgente';
                                    } elseif ($diasRestantes <= 3) {
                                        $prioridad = 'alta';
                                    } elseif ($diasRestantes <= 7) {
                                        $prioridad = 'media';
                                    } else {
                                        $prioridad = 'baja';
                                    }
                                }
                            @endphp
                            
                            <tr class="solicitud-row {{ $prioridad === 'urgente' ? 'table-danger' : '' }}" 
                                data-prioridad="{{ $prioridad }}">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <strong class="text-primary">#{{ $solicitud['id_solicitud'] }}</strong>
                                        @if($prioridad === 'urgente')
                                            <span class="badge bg-danger ms-1" title="Vencida">🔴</span>
                                        @elseif($prioridad === 'alta')
                                            <span class="badge bg-warning ms-1" title="Prioridad Alta">🟠</span>
                                        @endif
                                    </div>
                                    @if($solicitud['providencia'])
                                        <small class="text-info">Prov: {{ $solicitud['providencia'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario)
                                        <div>
                                            <strong>{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $solicitud['rut_usuario'] }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">Usuario no encontrado</span>
                                        <br>
                                        <small>{{ $solicitud['rut_usuario'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($requerimiento)
                                        <div>
                                            <strong class="text-primary">{{ $requerimiento['nombre'] }}</strong>
                                            @if($departamento)
                                                <br>
                                                <small class="badge bg-info">{{ $departamento['nombre'] }}</small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">Req. no especificado</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="descripcion-cell" title="{{ $solicitud['descripcion'] ?? 'Sin descripción' }}">
                                        {{ Str::limit($solicitud['descripcion'] ?? 'Sin descripción', 80) }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($solicitud['localidad'])
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <strong>{{ $solicitud['localidad'] }}</strong>
                                        @endif
                                        @if($solicitud['tipo_ubicacion'])
                                            <br>
                                            <small class="text-muted">{{ $solicitud['tipo_ubicacion'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($solicitud['fecha_ingreso'])
                                            <small>{{ \Carbon\Carbon::parse($solicitud['fecha_ingreso'])->format('d/m/Y') }}</small>
                                        @else
                                            <small class="text-muted">Sin fecha</small>
                                        @endif
                                        
                                        @if($solicitud['fecha_estimada_op'])
                                            <br>
                                            <small class="text-info" title="Fecha estimada">
                                                <i class="far fa-calendar"></i>
                                                {{ \Carbon\Carbon::parse($solicitud['fecha_estimada_op'])->format('d/m') }}
                                                @if($diasRestantes !== null)
                                                    @if($diasRestantes < 0)
                                                        <span class="text-danger">({{ abs($diasRestantes) }}d atraso)</span>
                                                    @elseif($diasRestantes == 0)
                                                        <span class="text-warning">(hoy)</span>
                                                    @else
                                                        <span>({{ $diasRestantes }}d)</span>
                                                    @endif
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <span class="status-badge
                                            @if($solicitud['estado'] == 'Completado') status-success
                                            @elseif($solicitud['estado'] == 'En proceso') bg-primary text-white
                                            @elseif($solicitud['estado'] == 'Pendiente') bg-warning text-dark
                                            @else status-secondary @endif">
                                            {{ $solicitud['estado'] ?? 'Sin estado' }}
                                        </span>
                                        
                                        @if($solicitud['etapa'] && $solicitud['etapa'] !== $solicitud['estado'])
                                            <small class="text-muted">{{ $solicitud['etapa'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Ver detalles -->
                                        <button type="button" class="action-btn btn-view view-solicitud-details" 
                                                title="Ver detalles"
                                                data-id="{{ $solicitud['id_solicitud'] }}"
                                                data-fecha="{{ $solicitud['fecha_ingreso'] ?? '' }}"
                                                data-estado="{{ $solicitud['estado'] ?? '' }}"
                                                data-etapa="{{ $solicitud['etapa'] ?? '' }}"
                                                data-descripcion="{{ $solicitud['descripcion'] ?? '' }}"
                                                data-localidad="{{ $solicitud['localidad'] ?? '' }}"
                                                data-ubicacion="{{ $solicitud['ubicacion'] ?? '' }}"
                                                data-tipo-ubicacion="{{ $solicitud['tipo_ubicacion'] ?? '' }}"
                                                data-providencia="{{ $solicitud['providencia'] ?? '' }}"
                                                data-requerimiento="{{ $requerimiento['nombre'] ?? 'No especificado' }}"
                                                data-departamento="{{ $departamento['nombre'] ?? 'No especificado' }}"
                                                data-usuario="{{ $usuario ? $usuario['nombre'] . ' ' . $usuario['apellidos'] : 'No encontrado' }}"
                                                data-rut="{{ $solicitud['rut_usuario'] ?? '' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- Tomar solicitud (solo si no está asignada) -->
                                        @if(
                                            ($rol == 'gestor' && empty($solicitud['rut_gestor'])) ||
                                            ($rol == 'tecnico' && empty($solicitud['rut_tecnico']))
                                        )
                                            <form method="POST" action="{{ route('bandeja.tomar', $solicitud['id_solicitud']) }}" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="action-btn bg-success text-white" 
                                                        title="Tomar solicitud"
                                                        onclick="return confirm('¿Deseas tomar esta solicitud?')">
                                                    <i class="fas fa-hand-paper"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <!-- Cambiar estado -->
                                        @if(
                                            ($rol == 'gestor' && $solicitud['rut_gestor'] == session('user_id')) ||
                                            ($rol == 'tecnico' && $solicitud['rut_tecnico'] == session('user_id')) ||
                                            $rol == 'admin'
                                        )
                                            <div class="dropdown">
                                                <button class="action-btn btn-warning dropdown-toggle" type="button" 
                                                        data-bs-toggle="dropdown" title="Cambiar estado">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($solicitud['estado'] !== 'En proceso')
                                                        <li>
                                                            <form method="POST" action="{{ route('bandeja.cambiar-estado', $solicitud['id_solicitud']) }}">
                                                                @csrf
                                                                <input type="hidden" name="estado" value="En proceso">
                                                                <input type="hidden" name="etapa" value="En proceso">
                                                                <button class="dropdown-item" type="submit">
                                                                    <i class="fas fa-play text-primary"></i> En proceso
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($solicitud['estado'] !== 'Completado')
                                                        <li>
                                                            <form method="POST" action="{{ route('bandeja.cambiar-estado', $solicitud['id_solicitud']) }}">
                                                                @csrf
                                                                <input type="hidden" name="estado" value="Completado">
                                                                <input type="hidden" name="etapa" value="Completada">
                                                                <button class="dropdown-item" type="submit">
                                                                    <i class="fas fa-check text-success"></i> Completar
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    
                                                    @if($solicitud['estado'] === 'Completado')
                                                        <li>
                                                            <form method="POST" action="{{ route('bandeja.cambiar-estado', $solicitud['id_solicitud']) }}">
                                                                @csrf
                                                                <input type="hidden" name="estado" value="En proceso">
                                                                <input type="hidden" name="etapa" value="En proceso">
                                                                <button class="dropdown-item" type="submit">
                                                                    <i class="fas fa-undo text-info"></i> Reabrir
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        <!-- Editar (solo admin o asignado) -->
                                        @if(
                                            $rol == 'admin' ||
                                            $solicitud['rut_gestor'] == session('user_id') ||
                                            $solicitud['rut_tecnico'] == session('user_id')
                                        )
                                            <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud']) }}" 
                                               class="action-btn btn-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="table-empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <p class="table-empty-state-text">
                                            @if($rol === 'admin')
                                                No hay solicitudes en el sistema
                                            @else
                                                No hay solicitudes para tu departamento: {{ $nombreDepartamento }}
                                            @endif
                                        </p>
                                        <small class="text-muted">
                                            Las solicitudes aparecerán aquí cuando se asignen a tu departamento
                                        </small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="table-pagination">
                <div class="pagination-info">
                    <strong>{{ count($solicitudes) }}</strong> solicitudes en tu bandeja
                    @if($estadisticas['vencidas'] > 0)
                        <span class="text-danger">
                            • <strong>{{ $estadisticas['vencidas'] }}</strong> vencidas
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalles de solicitud -->
<div class="user-details-panel details-panel" id="solicitudDetailsPanel">
    <div class="user-details-modal">
        <div class="user-details-header">
            <h3><i class="fas fa-clipboard-list"></i> Detalles de la Solicitud</h3>
            <button type="button" class="detail-close-btn" id="closeSolicitudPanelBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="user-profile-header">
            <div class="user-avatar">
                <span id="solicitudIcon"><i class="fas fa-clipboard-list"></i></span>
            </div>
            <div class="user-info">
                <h3 id="solicitudTitle"></h3>
                <p id="solicitudSubtitle" class="user-type"></p>
            </div>
        </div>
        
        <div class="user-details-container">
            <div class="details-section">
                <h4 class="section-title"><i class="fas fa-info-circle"></i> Información General</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">ID Solicitud</span>
                        <span class="detail-value" id="solicitudId"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Ingreso</span>
                        <span class="detail-value" id="solicitudFecha"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estado</span>
                        <span class="detail-value" id="solicitudEstado"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Etapa</span>
                        <span class="detail-value" id="solicitudEtapa"></span>
                    </div>
                    <div class="detail-item detail-full-width">
                        <span class="detail-label">Descripción</span>
                        <span class="detail-value" id="solicitudDescripcion"></span>
                    </div>
                </div>
            </div>
            
            <div class="details-section">
                <h4 class="section-title"><i class="fas fa-user"></i> Usuario Solicitante</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Nombre</span>
                        <span class="detail-value" id="solicitudUsuario"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">RUT</span>
                        <span class="detail-value" id="solicitudRut"></span>
                    </div>
                </div>
            </div>
            
            <div class="details-section">
                <h4 class="section-title"><i class="fas fa-building"></i> Departamento y Requerimiento</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Departamento</span>
                        <span class="detail-value" id="solicitudDepartamento"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Requerimiento</span>
                        <span class="detail-value" id="solicitudRequerimiento"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Providencia</span>
                        <span class="detail-value" id="solicitudProvidencia"></span>
                    </div>
                </div>
            </div>
            
            <div class="details-section">
                <h4 class="section-title"><i class="fas fa-map-marker-alt"></i> Ubicación</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Localidad</span>
                        <span class="detail-value" id="solicitudLocalidad"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tipo de Ubicación</span>
                        <span class="detail-value" id="solicitudTipoUbicacion"></span>
                    </div>
                    <div class="detail-item detail-full-width">
                        <span class="detail-label">Dirección</span>
                        <span class="detail-value" id="solicitudUbicacion"></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="panel-actions">
            <button type="button" class="btn btn-secondary" id="closeSolicitudPanelBtn2">
                <i class="fas fa-times"></i> Cerrar
            </button>
            <a href="#" id="editSolicitudBtn" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Solicitud
            </a>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES =====
    const solicitudDetailsPanel = document.getElementById('solicitudDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-solicitud-details');
    const closeSolicitudPanelBtn = document.getElementById('closeSolicitudPanelBtn');
    const closeSolicitudPanelBtn2 = document.getElementById('closeSolicitudPanelBtn2');
    
    function showSolicitudDetails() {
        solicitudDetailsPanel.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideSolicitudDetails() {
        solicitudDetailsPanel.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Mostrar panel de detalles
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            const solicitudData = {
                id: this.getAttribute('data-id'),
                fecha: this.getAttribute('data-fecha'),
                estado: this.getAttribute('data-estado'),
                etapa: this.getAttribute('data-etapa'),
                descripcion: this.getAttribute('data-descripcion'),
                localidad: this.getAttribute('data-localidad'),
                ubicacion: this.getAttribute('data-ubicacion'),
                tipoUbicacion: this.getAttribute('data-tipo-ubicacion'),
                providencia: this.getAttribute('data-providencia'),
                requerimiento: this.getAttribute('data-requerimiento'),
                departamento: this.getAttribute('data-departamento'),
                usuario: this.getAttribute('data-usuario'),
                rut: this.getAttribute('data-rut')
            };
            
            // Llenar datos en el panel
            document.getElementById('solicitudTitle').textContent = `Solicitud #${solicitudData.id}`;
            document.getElementById('solicitudSubtitle').textContent = `${solicitudData.departamento} - ${solicitudData.requerimiento}`;
            document.getElementById('solicitudId').textContent = solicitudData.id || 'Sin ID';
            document.getElementById('solicitudFecha').textContent = solicitudData.fecha ? formatearFecha(solicitudData.fecha) : 'Sin fecha';
            document.getElementById('solicitudEstado').textContent = solicitudData.estado || 'Sin estado';
            document.getElementById('solicitudEtapa').textContent = solicitudData.etapa || 'Sin etapa';
            document.getElementById('solicitudDescripcion').textContent = solicitudData.descripcion || 'Sin descripción';
            document.getElementById('solicitudUsuario').textContent = solicitudData.usuario || 'No encontrado';
            document.getElementById('solicitudRut').textContent = solicitudData.rut || 'Sin RUT';
            document.getElementById('solicitudDepartamento').textContent = solicitudData.departamento || 'No especificado';
            document.getElementById('solicitudRequerimiento').textContent = solicitudData.requerimiento || 'No especificado';
            document.getElementById('solicitudProvidencia').textContent = solicitudData.providencia || 'Sin providencia';
            document.getElementById('solicitudLocalidad').textContent = solicitudData.localidad || 'No especificada';
            document.getElementById('solicitudTipoUbicacion').textContent = solicitudData.tipoUbicacion || 'No especificado';
            document.getElementById('solicitudUbicacion').textContent = solicitudData.ubicacion || 'No especificada';
            
            // Configurar botón de editar
            const editBtn = document.getElementById('editSolicitudBtn');
            if (editBtn && solicitudData.id) {
                editBtn.href = `/solicitudes/${solicitudData.id}/edit`;
            }
            
            showSolicitudDetails();
        });
    });
    
    // Cerrar panel
    if (closeSolicitudPanelBtn) {
        closeSolicitudPanelBtn.addEventListener('click', hideSolicitudDetails);
    }
    
    if (closeSolicitudPanelBtn2) {
        closeSolicitudPanelBtn2.addEventListener('click', hideSolicitudDetails);
    }
    
    // Cerrar con click en fondo
    if (solicitudDetailsPanel) {
        solicitudDetailsPanel.addEventListener('click', function(event) {
            if (event.target === solicitudDetailsPanel) {
                hideSolicitudDetails();
            }
        });
    }
    
    // Cerrar con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && solicitudDetailsPanel.classList.contains('show')) {
            hideSolicitudDetails();
        }
    });

    // ===== ACTUALIZAR PÁGINA =====
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            window.location.reload();
        });
    }

    // ===== AUTO-REFRESH CADA 30 SEGUNDOS =====
    setInterval(function() {
        // Solo auto-refresh si no hay modal abierto
        if (!solicitudDetailsPanel.classList.contains('show')) {
            const currentUrl = new URL(window.location);
            fetch(currentUrl)
                .then(response => response.text())
                .then(html => {
                    // Actualizar solo el contador en el título
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');
                    const newBadge = newDoc.querySelector('.filter-card-title .badge');
                    const currentBadge = document.querySelector('.filter-card-title .badge');
                    
                    if (newBadge && currentBadge && newBadge.textContent !== currentBadge.textContent) {
                        // Mostrar notificación de nuevas solicitudes
                        showNotification('Hay nuevas solicitudes disponibles', 'info');
                    }
                })
                .catch(e => console.log('Error al verificar actualizaciones:', e));
        }
    }, 30000); // 30 segundos
});

// Función para formatear fecha
function formatearFecha(fechaStr) {
    if (!fechaStr || fechaStr === 'N/A') return 'No disponible';
    
    try {
        const fecha = new Date(fechaStr);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        
        return fecha.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    } catch (e) {
        return fechaStr;
    }
}

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    // Crear contenedor si no existe
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 350px;
        `;
        document.body.appendChild(container);
    }
    
    // Crear notificación
    const notification = document.createElement('div');
    notification.style.cssText = `
        background: ${type === 'error' ? '#ef4444' : type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#06b6d4'};
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    `;
    
    const icon = type === 'error' ? 'exclamation-circle' : 
                 type === 'success' ? 'check-circle' : 
                 type === 'warning' ? 'exclamation-triangle' : 'info-circle';
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center;">
            <i class="fas fa-${icon}" style="margin-right: 8px;"></i>
            <span>${message}</span>
        </div>
        <button onclick="this.parentElement.remove()" style="background: none; border: none; color: white; cursor: pointer; padding: 0; margin-left: 10px;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    container.appendChild(notification);
    
    // Animar entrada
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto-remover
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // F5 o Ctrl+R para actualizar
    if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
        e.preventDefault();
        window.location.reload();
    }
    
    // Ctrl+F para enfocar búsqueda
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="busqueda"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    }
});
</script>
@endsection
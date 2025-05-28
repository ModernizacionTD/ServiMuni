@extends('layouts.app')

@section('title', 'Bandeja de Solicitudes - ServiMuni')

@section('page-title', 'Mi Bandeja de Solicitudes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
<link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/bandeja.css') }}">
<link rel="stylesheet" href="{{ asset('css/button.css') }}">

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
                <button class="btn btn-sm btn-header" id="refreshBtn">
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
                            <option value="En curso" {{ $filtros['estado'] == 'En curso' ? 'selected' : '' }}>En curso</option>
                            <option value="Finalizadas" {{ $filtros['estado'] == 'Finalizada' ? 'selected' : '' }}>Finalizadas</option>
                            <option value="Derivadas" {{ $filtros['estado'] == 'Derivada' ? 'selected' : '' }}>Derivadas</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por etapa -->
                    <div class="filter-item">
                        <select name="etapa" class="form-select filter-select">
                            <option value="">Todas las etapas</option>
                            <option value="Por validar ingreso" {{ $filtros['etapa'] == 'Por validar ingreso' ? 'selected' : '' }}>Por validar ingreso</option>
                            <option value="Por derivar a Unidad" {{ $filtros['etapa'] == 'Por derivar a Unidad' ? 'selected' : '' }}>Por derivar a Unidad</option>
                            <option value="En espera de Informe" {{ $filtros['etapa'] == 'En espera de Informe' ? 'selected' : '' }}>En espera de Informe</option>
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
                            <th width="10%">Etapa</th>
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
                                            @elseif($solicitud['estado'] == 'En curso') bg-warning text-dark
                                            @else status-secondary @endif">
                                            {{ $solicitud['estado'] ?? 'Sin estado' }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($solicitud['etapa'])
                                            <span class="status-badge 
                                                @if($solicitud['etapa'] == 'Por validar ingreso') bg-warning text-dark
                                                @else bg-info text-white @endif">
                                                {{ $solicitud['etapa'] }}
                                            </span>
                                        @else
                                            <span class="status-badge bg-warning text-dark">Sin etapa</span>
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
                                                data-fecha-validacion="{{ $solicitud['fecha_validacion'] ?? '' }}"
                                                data-razon-validacion="{{ $solicitud['razon_validacion'] ?? '' }}"
                                                data-derivacion="{{ $solicitud['derivacion'] ?? '' }}"
                                                data-requerimiento="{{ $requerimiento['nombre'] ?? 'No especificado' }}"
                                                data-departamento="{{ $departamento['nombre'] ?? 'No especificado' }}"
                                                data-usuario="{{ $usuario ? $usuario['nombre'] . ' ' . $usuario['apellidos'] : 'No encontrado' }}"
                                                data-rut="{{ $solicitud['rut_usuario'] ?? '' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <!-- Tomar solicitud (solo si no está asignada) -->
                                        @if(
                                            ($rol == 'gestor' && empty($solicitud['rut_gestor'])) ||
                                            ($rol == 'unidad' && empty($solicitud['rut_tecnico']))
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
                                <td colspan="9">
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
            <div class="user-info-container">
                <div class="user-avatar">
                    <span id="solicitudIcon"><i class="fas fa-clipboard-list"></i></span>
                </div>
                <div class="user-info">
                    <h3 id="solicitudTitle"></h3>
                    <p id="solicitudSubtitle" class="user-type"></p>
                </div>
            </div>
            
            <!-- NUEVO: Botones en el header -->
            <div class="header-actions" id="headerValidacionButtons" style="display: none;">
                <button type="button" class="btn btn-success header-btn" id="validarIngresoBtn">
                    <i class="fas fa-check"></i> Validar Ingreso
                </button>
                <button type="button" class="btn btn-warning header-btn" id="reasignarBtn">
                    <i class="fas fa-share"></i> Reasignar
                </button>
            </div>
            <div class="header-actions" id="headerDerivacionButtons" style="display: none;">
                <button type="button" class="btn btn-info header-btn" id="derivarUnidadBtn">
                    <i class="fas fa-building"></i> Derivar a Unidad
                </button>
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
            
            <!-- Sección de Validación (solo si aplica) -->
            <div class="details-section" id="validacionSection" style="display: none;">
                <h4 class="section-title"><i class="fas fa-check-circle"></i> Información de Validación</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Fecha de Validación</span>
                        <span class="detail-value" id="solicitudFechaValidacion"></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estado de Validación</span>
                        <span class="detail-value" id="solicitudEstadoValidacion"></span>
                    </div>
                    <div class="detail-item detail-full-width">
                        <span class="detail-label">Razón</span>
                        <span class="detail-value" id="solicitudRazonValidacion"></span>
                    </div>
                </div>
            </div>
            
            <!-- Sección de Derivación (solo si aplica) -->
            <div class="details-section" id="derivacionSection" style="display: none;">
                <h4 class="section-title"><i class="fas fa-share"></i> Información de Derivación</h4>
                <div class="details-grid">
                    <div class="detail-item detail-full-width">
                        <span class="detail-label">Derivación</span>
                        <span class="detail-value" id="solicitudDerivacion"></span>
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

<!-- Modal de Validación de Ingreso -->
<div class="modal-custom" id="validacionModalCustom">
    <div class="modal-custom-content">
        <div class="modal-custom-header bg-primary">
            <h5>
                <i class="fas fa-check-circle"></i> Validar Ingreso de Solicitud
            </h5>
            <button type="button" class="modal-custom-close" id="closeValidacionModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-custom-body">
            <!-- Información de la solicitud -->
            <div class="modal-info-section">
                <div class="modal-info-row">
                    <div class="modal-info-item">
                        <span class="modal-info-label">Solicitud ID</span>
                        <span class="modal-info-value" id="validacionSolicitudIdDisplay"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Usuario</span>
                        <span class="modal-info-value" id="validacionUsuarioDisplay"></span>
                    </div>
                </div>
            </div>
            
            <form id="validacionFormCustom" method="POST">
                @csrf
                <input type="hidden" id="validacionSolicitudIdInput" name="solicitud_id">
                
                <div class="modal-form-section">
                    <label class="form-label fw-bold mb-3">¿Qué acción deseas realizar?</label>
                    
                    <div class="form-check-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="accion_validacion" id="validarCustom" value="validar" checked>
                            <label class="form-check-label-custom text-success fw-bold" for="validarCustom">
                                <i class="fas fa-check-circle"></i> Validar - Aprobar la solicitud
                            </label>
                            <div class="form-help-text">La solicitud pasará a la etapa "Por derivar a Unidad"</div>
                        </div>
                    </div>
                    
                    <div class="form-check-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="accion_validacion" id="denegarCustom" value="denegar">
                            <label class="form-check-label-custom text-danger fw-bold" for="denegarCustom">
                                <i class="fas fa-times-circle"></i> Denegar - Rechazar la solicitud
                            </label>
                            <div class="form-help-text">Debes proporcionar una razón para el rechazo</div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-form-section" id="razonDenegacionDivCustom" style="display: none;">
                    <label for="razon_validacion_custom" class="form-label fw-bold">Razón de la denegación <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="razon_validacion_custom" name="razon_validacion" rows="4" 
                              placeholder="Explique por qué se está denegando esta solicitud..."></textarea>
                    <div class="form-text">Debe proporcionar una explicación clara del motivo del rechazo.</div>
                </div>
            </form>
        </div>
        
        <div class="modal-custom-footer">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelValidacionBtn">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="confirmarValidacionBtnCustom">
                <i class="fas fa-save"></i> Confirmar Validación
            </button>
        </div>
    </div>
</div>

<!-- Modal de Reasignación -->
<div class="modal-custom" id="reasignacionModalCustom">
    <div class="modal-custom-content">
        <div class="modal-custom-header bg-warning">
            <h5>
                <i class="fas fa-share"></i> Reasignar Solicitud
            </h5>
            <button type="button" class="modal-custom-close" id="closeReasignacionModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-custom-body">
            <!-- Información de la solicitud -->
            <div class="modal-info-section">
                <div class="modal-info-row">
                    <div class="modal-info-item">
                        <span class="modal-info-label">Solicitud ID</span>
                        <span class="modal-info-value" id="reasignacionSolicitudIdDisplay"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Usuario</span>
                        <span class="modal-info-value" id="reasignacionUsuarioDisplay"></span>
                    </div>
                </div>
            </div>
            
            <form id="reasignacionFormCustom" method="POST">
                @csrf
                <input type="hidden" id="reasignacionSolicitudIdInput" name="solicitud_id">
                
                <div class="modal-form-section">
                    <label class="form-label fw-bold mb-3">¿Dónde deseas reasignar la solicitud?</label>
                    
                    <div class="form-check-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_reasignacion" id="internoCustom" value="interno" checked>
                            <label class="form-check-label-custom fw-bold" for="internoCustom">
                                <i class="fas fa-building"></i> Derivación Interna
                            </label>
                            <div class="form-help-text">Reasignar a otro departamento del sistema</div>
                        </div>
                    </div>
                    
                    <div class="form-check-custom">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_reasignacion" id="externoCustom" value="externo">
                            <label class="form-check-label-custom fw-bold" for="externoCustom">
                                <i class="fas fa-external-link-alt"></i> Derivación Externa
                            </label>
                            <div class="form-help-text">Reasignar fuera del sistema</div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-form-section" id="departamentoInternoDivCustom">
                    <label for="departamento_destino_custom" class="form-label fw-bold">Departamento de Destino <span class="text-danger">*</span></label>
                    <select class="form-select" id="departamento_destino_custom" name="departamento_destino">
                        <option value="">Seleccione un departamento...</option>
                        @foreach($departamentos as $dept)
                            <option value="{{ $dept['id'] }}">{{ $dept['nombre'] }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Seleccione el departamento al cual desea reasignar la solicitud.</div>
                </div>
                
                <div class="modal-form-section" id="destinoExternoDivCustom" style="display: none;">
                    <label for="destino_externo_custom" class="form-label fw-bold">Destino Externo <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="destino_externo_custom" name="destino_externo" 
                           placeholder="Ej: Ministerio de Salud, SERVIU, etc.">
                    <div class="form-text">Indique el organismo o entidad externa donde se reasignará la solicitud.</div>
                </div>
                
                <div class="modal-form-section">
                    <label for="motivo_reasignacion_custom" class="form-label fw-bold">Motivo de la Reasignación</label>
                    <textarea class="form-control" id="motivo_reasignacion_custom" name="motivo_reasignacion" rows="3" 
                              placeholder="Opcional: Explique por qué se está reasignando esta solicitud..."></textarea>
                    <div class="form-text">Campo opcional para agregar contexto sobre la reasignación.</div>
                </div>
            </form>
        </div>
        
        <div class="modal-custom-footer">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelReasignacionBtn">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" class="modal-btn modal-btn-warning" id="confirmarReasignacionBtnCustom">
                <i class="fas fa-share"></i> Confirmar Reasignación
            </button>
        </div>
    </div>
</div>

<div class="modal-custom" id="derivacionUnidadModalCustom">
    <div class="modal-custom-content">
        <div class="modal-custom-header bg-info">
            <h5>
                <i class="fas fa-user-cog"></i> Derivar Solicitud a Unidad
            </h5>
            <button type="button" class="modal-custom-close" id="closeDerivacionUnidadModal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-custom-body">
            <!-- Información de la solicitud -->
            <div class="modal-info-section">
                <div class="modal-info-row">
                    <div class="modal-info-item">
                        <span class="modal-info-label">Solicitud ID</span>
                        <span class="modal-info-value" id="derivacionUnidadSolicitudIdDisplay"></span>
                    </div>
                    <div class="modal-info-item">
                        <span class="modal-info-label">Usuario</span>
                        <span class="modal-info-value" id="derivacionUnidadUsuarioDisplay"></span>
                    </div>
                </div>
            </div>
            
            <form id="derivacionUnidadFormCustom" method="POST">
                @csrf
                <input type="hidden" id="derivacionUnidadSolicitudIdInput" name="solicitud_id">
                
                <div class="modal-form-section">
                    <label for="tecnico_asignado" class="form-label fw-bold">Seleccionar Unidad <span class="text-danger">*</span></label>
                    
                    <!-- Tabla de unidades -->
                    <div class="unidades-container">
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead style="position: sticky; top: 0; background: #f8f9fa;">
                                    <tr>
                                        <th width="50px">Seleccionar</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Departamento</th>
                                    </tr>
                                </thead>
                                <tbody id="tecnicosTableBody">
                                    <!-- Los unidades se cargarán dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle"></i> 
                        Seleccione el unidad que se encargará de procesar esta solicitud.
                    </div>
                </div>
                
                <div class="modal-form-section">
                    <label for="observaciones_derivacion" class="form-label fw-bold">Observaciones (Opcional)</label>
                    <textarea class="form-control" id="observaciones_derivacion" name="observaciones_derivacion" rows="3" 
                              placeholder="Agregue cualquier observación o instrucción especial para la unidad..."></textarea>
                    <div class="form-text">Campo opcional para proporcionar contexto adicional a la unidad asignada.</div>
                </div>
            </form>
        </div>
        
        <div class="modal-custom-footer">
            <button type="button" class="modal-btn modal-btn-secondary" id="cancelDerivacionUnidadBtn">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button type="button" class="modal-btn modal-btn-info" id="confirmarDerivacionUnidadBtn">
                <i class="fas fa-user-cog"></i> Confirmar Derivación
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let solicitudActualId = null;
    let solicitudActualEtapa = null;
    let funcionariosUnidades = [];
    
    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES =====
    const solicitudDetailsPanel = document.getElementById('solicitudDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-solicitud-details');
    const closeSolicitudPanelBtn = document.getElementById('closeSolicitudPanelBtn');
    const closeSolicitudPanelBtn2 = document.getElementById('closeSolicitudPanelBtn2');
    const derivarUnidadBtn = document.getElementById('derivarUnidadBtn');
    const derivacionUnidadModalCustom = document.getElementById('derivacionUnidadModalCustom');
    const derivacionUnidadFormCustom = document.getElementById('derivacionUnidadFormCustom');
    const confirmarDerivacionUnidadBtn = document.getElementById('confirmarDerivacionUnidadBtn');
    const tecnicosTableBody = document.getElementById('tecnicosTableBody');
    
    cargarUnidades();

    // Función para cargar unidades
    async function cargarUnidades() {
        try {
            // Aquí deberías hacer una llamada AJAX para obtener los unidades
            // Por ahora, usamos los datos que ya tienes en la vista
            const response = await fetch('/api/funcionarios/unidades');
            if (response.ok) {
                funcionariosUnidades = await response.json();
            }
        } catch (error) {
            console.log('Error al cargar unidades:', error);
            // Fallback: usar datos de la vista si están disponibles
            if (typeof funcionarios !== 'undefined') {
                funcionariosUnidades = Object.values(funcionarios).filter(f => f.rol === 'unidad');
            }
        }
    }

    // Función para mostrar unidades en la tabla
    function mostrarUnidades() {
        if (!funcionariosUnidades || funcionariosUnidades.length === 0) {
            tecnicosTableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="no-unidades-message">
                        <i class="fas fa-user-times"></i>
                        <p>No hay unidades disponibles en el sistema.</p>   
                    </td>
                </tr>
            `;
            return;
        }
        
        const tecnicosHTML = funcionariosUnidades.map(unidad => `
            <tr data-unidad-id="${unidad.id}" onclick="seleccionarUnidad('${unidad.id}')">
                <td>
                    <input type="radio" name="tecnico_asignado" value="${unidad.id}" id="tecnico_${unidad.id}">
                </td>
                <td>
                    <div class="unidad-nombre">${unidad.nombre}</div>
                </td>
                <td>
                    <div class="unidad-email">${unidad.email}</div>
                </td>
                <td>
                    <div class="unidad-departamento">${obtenerNombreDepartamento(unidad.departamento_id)}</div>
                </td>
            </tr>
        `).join('');
        
        tecnicosTableBody.innerHTML = tecnicosHTML;
    }
    
    // Función para seleccionar técnico
    function seleccionarUnidad(tecnicoId) {
        // Quitar selección anterior
        document.querySelectorAll('.unidades-container tr').forEach(tr => {
            tr.classList.remove('selected');
        });
        
        // Seleccionar técnico
        const radio = document.getElementById(`tecnico_${tecnicoId}`);
        if (radio) {
            radio.checked = true;
            radio.closest('tr').classList.add('selected');
        }
    }
    
    // Función helper para obtener nombre del departamento
    function obtenerNombreDepartamento(departamentoId) {
        if (typeof departamentos !== 'undefined' && departamentos[departamentoId]) {
            return departamentos[departamentoId].nombre;
        }
        return 'Sin departamento';
    }
    
    // Abrir modal de derivación a técnico
    if (derivarUnidadBtn) {
        derivarUnidadBtn.addEventListener('click', function() {
            // Llenar datos del modal
            document.getElementById('derivacionUnidadSolicitudIdDisplay').textContent = solicitudActualId;
            document.getElementById('derivacionUnidadUsuarioDisplay').textContent = 
                document.getElementById('solicitudUsuario').textContent;
            document.getElementById('derivacionUnidadSolicitudIdInput').value = solicitudActualId;
            
            // Resetear formulario
            document.querySelectorAll('input[name="tecnico_asignado"]').forEach(radio => {
                radio.checked = false;
            });
            document.querySelectorAll('.unidades-container tr').forEach(tr => {
                tr.classList.remove('selected');
            });
            document.getElementById('observaciones_derivacion').value = '';
            
            // Mostrar unidades
            mostrarUnidades();
            
            showModalCustom(derivacionUnidadModalCustom);
        });
    }
    
    // Cerrar modal de derivación a técnico
    document.getElementById('closeDerivacionUnidadModal')?.addEventListener('click', () => {
        hideModalCustom(derivacionUnidadModalCustom);
    });
    
    document.getElementById('cancelDerivacionUnidadBtn')?.addEventListener('click', () => {
        hideModalCustom(derivacionUnidadModalCustom);
    });
    
    // Confirmar derivación a técnico
    if (confirmarDerivacionUnidadBtn) {
        confirmarDerivacionUnidadBtn.addEventListener('click', function() {
            const tecnicoSeleccionado = document.querySelector('input[name="tecnico_asignado"]:checked');
            
            // Validar que se haya seleccionado un técnico
            if (!tecnicoSeleccionado) {
                alert('Debe seleccionar un técnico para derivar la solicitud.');
                return;
            }
            
            // Obtener datos del técnico seleccionado
            const tecnicoId = tecnicoSeleccionado.value;
            const unidad = funcionariosUnidades.find(t => t.id == tecnicoId);
            const nombreUnidad = unidad ? unidad.nombre : 'Unidad seleccionado';
            
            // Confirmar acción
            if (confirm(`¿Está seguro de que desea derivar esta solicitud a: ${nombreUnidad}?`)) {
                // Enviar formulario
                derivacionUnidadFormCustom.action = `/bandeja/${solicitudActualId}/derivar-unidad`;
                derivacionUnidadFormCustom.submit();
            }
        });
    }
    
    // Cerrar modal de derivación con Escape y click en fondo
    if (derivacionUnidadModalCustom) {
        derivacionUnidadModalCustom.addEventListener('click', function(event) {
            if (event.target === derivacionUnidadModalCustom) {
                hideModalCustom(derivacionUnidadModalCustom);
            }
        });
    }
    
    // Hacer la función global para que funcione el onclick
    window.seleccionarUnidad = seleccionarUnidad;

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
                fechaValidacion: this.getAttribute('data-fecha-validacion'),
                razonValidacion: this.getAttribute('data-razon-validacion'),
                derivacion: this.getAttribute('data-derivacion'),
                requerimiento: this.getAttribute('data-requerimiento'),
                departamento: this.getAttribute('data-departamento'),
                usuario: this.getAttribute('data-usuario'),
                rut: this.getAttribute('data-rut')
            };
            
            // Guardar datos globales
            solicitudActualId = solicitudData.id;
            solicitudActualEtapa = solicitudData.etapa;
            
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
            
            // Mostrar información de validación si existe
            const validacionSection = document.getElementById('validacionSection');
            if (solicitudData.fechaValidacion || solicitudData.razonValidacion) {
                validacionSection.style.display = 'block';
                document.getElementById('solicitudFechaValidacion').textContent = 
                    solicitudData.fechaValidacion ? formatearFecha(solicitudData.fechaValidacion) : 'No disponible';
                
                // Determinar estado de validación
                let estadoValidacion = 'Pendiente';
                if (solicitudData.fechaValidacion) {
                    estadoValidacion = solicitudData.razonValidacion ? 'Denegada' : 'Aprobada';
                }
                document.getElementById('solicitudEstadoValidacion').textContent = estadoValidacion;
                document.getElementById('solicitudRazonValidacion').textContent = 
                    solicitudData.razonValidacion || 'Sin razón especificada';
            } else {
                validacionSection.style.display = 'none';
            }
            
            // Mostrar información de derivación si existe
            const derivacionSection = document.getElementById('derivacionSection');
            if (solicitudData.derivacion) {
                derivacionSection.style.display = 'block';
                document.getElementById('solicitudDerivacion').textContent = solicitudData.derivacion;
            } else {
                derivacionSection.style.display = 'none';
            }
            
            // ===== CONTROL DE BOTONES SEGÚN ETAPA =====
            const headerValidacionButtons = document.getElementById('headerValidacionButtons');
            const headerDerivacionButtons = document.getElementById('headerDerivacionButtons');
            
            // Ocultar todos los botones primero
            headerValidacionButtons.style.display = 'none';
            headerDerivacionButtons.style.display = 'none';
            
            // Mostrar botones según la etapa
            if (solicitudData.etapa === 'Por validar ingreso') {
                headerValidacionButtons.style.display = 'flex';
            } else if (solicitudData.etapa === 'Por derivar a Unidad') {
                headerDerivacionButtons.style.display = 'flex';
            }
            
            // Configurar botón de editar
            const editBtn = document.getElementById('editSolicitudBtn');
            if (editBtn && solicitudData.id) {
                editBtn.href = `/solicitudes/${solicitudData.id}/editar`;
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

    // ===== MODALES PERSONALIZADOS =====
    
    // Variables globales para los modales
    const validacionModalCustom = document.getElementById('validacionModalCustom');
    const reasignacionModalCustom = document.getElementById('reasignacionModalCustom');
    
    // Funciones para mostrar/ocultar modales
    function showModalCustom(modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideModalCustom(modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // ===== MODAL DE VALIDACIÓN PERSONALIZADO =====
    const validarIngresoBtn = document.getElementById('validarIngresoBtn');
    const validacionFormCustom = document.getElementById('validacionFormCustom');
    const confirmarValidacionBtnCustom = document.getElementById('confirmarValidacionBtnCustom');
    
    // Manejar cambios en el tipo de validación
    document.querySelectorAll('input[name="accion_validacion"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const razonDiv = document.getElementById('razonDenegacionDivCustom');
            const razonTextarea = document.getElementById('razon_validacion_custom');
            
            if (this.value === 'denegar') {
                razonDiv.style.display = 'block';
                razonTextarea.required = true;
            } else {
                razonDiv.style.display = 'none';
                razonTextarea.required = false;
                razonTextarea.value = '';
            }
        });
    });
    
    // Abrir modal de validación
    if (validarIngresoBtn) {
        validarIngresoBtn.addEventListener('click', function() {
            // Llenar datos del modal
            document.getElementById('validacionSolicitudIdDisplay').textContent = solicitudActualId;
            document.getElementById('validacionUsuarioDisplay').textContent = 
                document.getElementById('solicitudUsuario').textContent;
            document.getElementById('validacionSolicitudIdInput').value = solicitudActualId;
            
            // Resetear formulario
            document.getElementById('validarCustom').checked = true;
            document.getElementById('razonDenegacionDivCustom').style.display = 'none';
            document.getElementById('razon_validacion_custom').value = '';
            
            showModalCustom(validacionModalCustom);
        });
    }
    
    // Cerrar modal de validación
    document.getElementById('closeValidacionModal')?.addEventListener('click', () => {
        hideModalCustom(validacionModalCustom);
    });
    
    document.getElementById('cancelValidacionBtn')?.addEventListener('click', () => {
        hideModalCustom(validacionModalCustom);
    });
    
    // Confirmar validación
    if (confirmarValidacionBtnCustom) {
        confirmarValidacionBtnCustom.addEventListener('click', function() {
            const accion = document.querySelector('input[name="accion_validacion"]:checked').value;
            const razon = document.getElementById('razon_validacion_custom').value;
            
            // Validar campos requeridos
            if (accion === 'denegar' && !razon.trim()) {
                alert('Debe proporcionar una razón para denegar la solicitud.');
                return;
            }
            
            // Confirmar acción
            const mensaje = accion === 'validar' ? 
                '¿Está seguro de que desea validar esta solicitud?' : 
                '¿Está seguro de que desea denegar esta solicitud?';
            
            if (confirm(mensaje)) {
                // Enviar formulario
                validacionFormCustom.action = `/bandeja/${solicitudActualId}/validar`;
                validacionFormCustom.submit();
            }
        });
    }
    
    // ===== MODAL DE REASIGNACIÓN PERSONALIZADO =====
    const reasignarBtn = document.getElementById('reasignarBtn');
    const reasignacionFormCustom = document.getElementById('reasignacionFormCustom');
    const confirmarReasignacionBtnCustom = document.getElementById('confirmarReasignacionBtnCustom');
    
    // Manejar cambios en el tipo de reasignación
    document.querySelectorAll('input[name="tipo_reasignacion"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const internoDiv = document.getElementById('departamentoInternoDivCustom');
            const externoDiv = document.getElementById('destinoExternoDivCustom');
            const deptSelect = document.getElementById('departamento_destino_custom');
            const externoInput = document.getElementById('destino_externo_custom');
            
            if (this.value === 'interno') {
                internoDiv.style.display = 'block';
                externoDiv.style.display = 'none';
                deptSelect.required = true;
                externoInput.required = false;
                externoInput.value = '';
            } else {
                internoDiv.style.display = 'none';
                externoDiv.style.display = 'block';
                deptSelect.required = false;
                deptSelect.value = '';
                externoInput.required = true;
            }
        });
    });
    
    // Abrir modal de reasignación
    if (reasignarBtn) {
        reasignarBtn.addEventListener('click', function() {
            // Llenar datos del modal
            document.getElementById('reasignacionSolicitudIdDisplay').textContent = solicitudActualId;
            document.getElementById('reasignacionUsuarioDisplay').textContent = 
                document.getElementById('solicitudUsuario').textContent;
            document.getElementById('reasignacionSolicitudIdInput').value = solicitudActualId;
            
            // Resetear formulario
            document.getElementById('internoCustom').checked = true;
            document.getElementById('departamentoInternoDivCustom').style.display = 'block';
            document.getElementById('destinoExternoDivCustom').style.display = 'none';
            document.getElementById('departamento_destino_custom').value = '';
            document.getElementById('destino_externo_custom').value = '';
            document.getElementById('motivo_reasignacion_custom').value = '';
            
            showModalCustom(reasignacionModalCustom);
        });
    }
    
    // Cerrar modal de reasignación
    document.getElementById('closeReasignacionModal')?.addEventListener('click', () => {
        hideModalCustom(reasignacionModalCustom);
    });
    
    document.getElementById('cancelReasignacionBtn')?.addEventListener('click', () => {
        hideModalCustom(reasignacionModalCustom);
    });
    
    // Confirmar reasignación
    if (confirmarReasignacionBtnCustom) {
        confirmarReasignacionBtnCustom.addEventListener('click', function() {
            const tipo = document.querySelector('input[name="tipo_reasignacion"]:checked').value;
            const departamento = document.getElementById('departamento_destino_custom').value;
            const externo = document.getElementById('destino_externo_custom').value;
            
            // Validar campos requeridos
            if (tipo === 'interno' && !departamento) {
                alert('Debe seleccionar un departamento de destino.');
                return;
            }
            
            if (tipo === 'externo' && !externo.trim()) {
                alert('Debe especificar el destino externo.');
                return;
            }
            
            // Confirmar acción
            const destino = tipo === 'interno' ? 
                document.querySelector(`#departamento_destino_custom option[value="${departamento}"]`).textContent : 
                externo;
            
            if (confirm(`¿Está seguro de que desea reasignar esta solicitud a: ${destino}?`)) {
                // Enviar formulario
                reasignacionFormCustom.action = `/bandeja/${solicitudActualId}/reasignar`;
                reasignacionFormCustom.submit();
            }
        });
    }
    
    // Cerrar modales con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (validacionModalCustom && validacionModalCustom.classList.contains('show')) {
                hideModalCustom(validacionModalCustom);
            }
            if (reasignacionModalCustom && reasignacionModalCustom.classList.contains('show')) {
                hideModalCustom(reasignacionModalCustom);
            }
            if (derivacionUnidadModalCustom && derivacionUnidadModalCustom.classList.contains('show')) {
                hideModalCustom(derivacionUnidadModalCustom);
            }
        }
    });
    
    // Cerrar modales al hacer clic en el fondo
    if (validacionModalCustom) {
        validacionModalCustom.addEventListener('click', function(event) {
            if (event.target === validacionModalCustom) {
                hideModalCustom(validacionModalCustom);
            }
        });
    }
    
    if (reasignacionModalCustom) {
        reasignacionModalCustom.addEventListener('click', function(event) {
            if (event.target === reasignacionModalCustom) {
                hideModalCustom(reasignacionModalCustom);
            }
        });
    }

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
        if (!solicitudDetailsPanel.classList.contains('show') && 
            !(validacionModalCustom && validacionModalCustom.classList.contains('show')) &&
            !(reasignacionModalCustom && reasignacionModalCustom.classList.contains('show')) &&
            !(derivacionUnidadModalCustom && derivacionUnidadModalCustom.classList.contains('show'))) {
            
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

// ===== FUNCIONES AUXILIARES =====

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

// ===== ATAJOS DE TECLADO =====
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
    
    // Esc para cerrar cualquier modal
    if (e.key === 'Escape') {
        const solicitudDetailsPanel = document.getElementById('solicitudDetailsPanel');
        const validacionModalCustom = document.getElementById('validacionModalCustom');
        const reasignacionModalCustom = document.getElementById('reasignacionModalCustom');
        const derivacionUnidadModalCustom = document.getElementById('derivacionUnidadModalCustom');
        
        if (solicitudDetailsPanel && solicitudDetailsPanel.classList.contains('show')) {
            solicitudDetailsPanel.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        if (validacionModalCustom && validacionModalCustom.classList.contains('show')) {
            validacionModalCustom.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        if (reasignacionModalCustom && reasignacionModalCustom.classList.contains('show')) {
            reasignacionModalCustom.classList.remove('show');
            document.body.style.overflow = '';
        }
        
        if (derivacionUnidadModalCustom && derivacionUnidadModalCustom.classList.contains('show')) {
            derivacionUnidadModalCustom.classList.remove('show');
            document.body.style.overflow = '';
        }
    }
});

// ===== ORDENAR SOLICITUDES =====
function ordenarSolicitudes() {
    const tbody = document.querySelector('.data-table tbody');
    if (!tbody) return;
    
    // Convertir NodeList a Array para poder ordenar
    const filas = Array.from(tbody.querySelectorAll('tr.solicitud-row'));
    
    // Función de ordenamiento
    filas.sort((a, b) => {
        // Primero revisar si alguna tiene estado "Completado"
        const estadoA = a.querySelector('td:nth-child(7) .status-badge').textContent.trim();
        const estadoB = b.querySelector('td:nth-child(7) .status-badge').textContent.trim();
        
        // Si ambos están completados o ninguno está completado, ordenar por fecha
        if ((estadoA === 'Completado' && estadoB === 'Completado') || 
            (estadoA !== 'Completado' && estadoB !== 'Completado')) {
            
            // Obtener fechas (están en el formato DD/MM/YYYY)
            const fechaTextoA = a.querySelector('td:nth-child(6) small').textContent.trim().split(' ')[0];
            const fechaTextoB = b.querySelector('td:nth-child(6) small').textContent.trim().split(' ')[0];
            
            // Convertir a objetos Date para comparar (formato: DD/MM/YYYY)
            const partesFechaA = fechaTextoA.split('/');
            const partesFechaB = fechaTextoB.split('/');
            
            if (partesFechaA.length === 3 && partesFechaB.length === 3) {
                const fechaA = new Date(
                    parseInt(partesFechaA[2]), // Año
                    parseInt(partesFechaA[1]) - 1, // Mes (0-11)
                    parseInt(partesFechaA[0]) // Día
                );
                
                const fechaB = new Date(
                    parseInt(partesFechaB[2]), // Año
                    parseInt(partesFechaB[1]) - 1, // Mes (0-11)
                    parseInt(partesFechaB[0]) // Día
                );
                
                // Ordenar descendente por fecha (más reciente primero)
                return fechaB - fechaA;
            }
            
            return 0; // Si no se pueden comparar fechas, mantener orden
        }
        
        // Si una está completada y la otra no, la completada va al final
        return estadoA === 'Completado' ? 1 : -1;
    });
    
    // Reordenar el DOM
    filas.forEach(fila => tbody.appendChild(fila));
    
    console.log('Solicitudes reordenadas: completadas al final, resto por fecha de ingreso');
}

// Llamar a la función de ordenamiento cuando la página cargue
document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar la ordenación inicial
    ordenarSolicitudes();
    
    // Agregar un botón para ordenar manualmente
    const headerActions = document.querySelector('.filter-card-header .d-flex');
    if (headerActions) {
        const ordenarBtn = document.createElement('button');
        ordenarBtn.className = 'btn btn-sm btn-header ms-2';
        ordenarBtn.innerHTML = '<i class="fas fa-sort"></i> Ordenar';
        ordenarBtn.title = 'Ordenar: Completadas al final, resto por fecha';
        ordenarBtn.addEventListener('click', ordenarSolicitudes);
        headerActions.appendChild(ordenarBtn);
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
    if (!solicitudDetailsPanel.classList.contains('show') && 
        !(validacionModalCustom && validacionModalCustom.classList.contains('show')) &&
        !(reasignacionModalCustom && reasignacionModalCustom.classList.contains('show')) &&
        !(derivacionUnidadModalCustom && derivacionUnidadModalCustom.classList.contains('show'))) {
        
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
</script>
@endsection
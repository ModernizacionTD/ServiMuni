@extends('layouts.app')

@section('title', 'Búsqueda de Usuario - ServiMuni')

@section('page-title', 'Búsqueda de Usuario')

@section('content')
<link rel="stylesheet" href="{{ asset('css/busqueda.css') }}">

<div class="search-view-container">
    <div class="card">
        <div class="search-card-header">
            <h3 class="search-card-title">
                <i class="fas fa-search"></i>Buscar Usuario
            </h3>
        </div>
        <div class="search-card-body">
            <form id="buscarUsuarioForm" method="GET" action="{{ route('buscar.usuario') }}" class="row g-3 align-items-center">
                <div class="col-md-16">
                    <label for="rut" class="form-label fw-semibold">RUT del Usuario</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-id-card text-primary"></i>
                        </span>
                        <input type="text" id="rut" name="rut" class="form-control" 
                               placeholder="Ej: 12345678-9" 
                               value="{{ $rut ?? '' }}" 
                               required
                               maxlength="12"
                               pattern="[0-9]{7,8}-[0-9kK]{1}">
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Formato: 12345678-9 (sin puntos, con guión)
                    </small>
                    <div class="invalid-feedback" id="rutError"></div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-search" id="btnBuscar">
                            <i class="fas fa-search me-2"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar" title="Limpiar">
                            <i class="fas fa-broom"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($usuario))
    <!-- Información del Usuario Encontrado -->
    <div class="card" id="usuarioCard">
        <div class="search-card-header bg-success">
            <h3 class="search-card-title mb-0">
                <i class="fas fa-user-check"></i>Usuario Encontrado
            </h3>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-add" id="btnEditarContacto">
                    <i class="fas fa-edit me-1"></i> Editar Contacto
                </button>
                <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" 
                   class="btn btn-sm btn-add">
                    <i class="fas fa-plus me-1"></i> Nueva Solicitud
                </a>
            </div>
        </div>
        <div class="search-card-body">
            <!-- Vista de Información -->
            <div id="usuarioInfo">
                <!-- Header con avatar y nombre -->
                <div class="user-header mb-4">
                    <div class="d-flex align-items-center">
                        <div class="user-avatar me-3">
                            @php
                                $nombre = $usuario['nombre'] ?? 'U';
                                $apellidos = $usuario['apellidos'] ?? '';
                                $palabras = array_filter(explode(' ', trim($nombre . ' ' . $apellidos)));
                                if (count($palabras) >= 2) {
                                    $iniciales = strtoupper($palabras[0][0] . $palabras[1][0]);
                                } else {
                                    $iniciales = strtoupper(substr($nombre, 0, 2));
                                }
                            @endphp
                            {{ $iniciales }}
                        </div>
                        <div class="user-basic-info">
                            <h4 class="mb-1">{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</h4>
                            <div class="d-flex gap-3 text-muted">
                                <span><i class="fas fa-id-card me-1"></i>{{ $usuario['rut'] }}</span>
                                <span class="status-badge {{ $usuario['tipo_persona'] == 'Natural' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $usuario['tipo_persona'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del Usuario en Dos Columnas -->
                <div class="row g-4">
                    <!-- Columna Izquierda: Datos Personales -->
                    <div class="col-lg-6">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-user text-primary"></i>Datos Personales
                            </h6>
                            
                            @if($usuario['tipo_persona'] == 'Natural')
                                @if(isset($usuario['fecha_nacimiento']) && !empty($usuario['fecha_nacimiento']) && $usuario['fecha_nacimiento'] !== '1900-01-01')
                                <div class="info-item">
                                    <span class="info-label">Fecha de Nacimiento:</span>
                                    <span class="info-value">
                                        {{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->format('d/m/Y') }}
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->age }} años)</small>
                                    </span>
                                </div>
                                @endif
                                
                                @if(isset($usuario['genero']) && !empty($usuario['genero']) && $usuario['genero'] !== 'No decir')
                                <div class="info-item">
                                    <span class="info-label">Género:</span>
                                    <span class="info-value">{{ $usuario['genero'] }}</span>
                                </div>
                                @endif
                                
                                @if(isset($usuario['uso_ns']) && $usuario['uso_ns'] == 'Sí' && isset($usuario['nombre_social']) && !empty($usuario['nombre_social']))
                                <div class="info-item">
                                    <span class="info-label">Nombre Social:</span>
                                    <span class="info-value">{{ $usuario['nombre_social'] }}</span>
                                </div>
                                @endif
                            @else
                                <div class="info-item">
                                    <span class="info-label">Tipo de Persona:</span>
                                    <span class="info-value">{{ $usuario['tipo_persona'] }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Razón Social:</span>
                                    <span class="info-value">{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">RUT:</span>
                                    <span class="info-value">{{ $usuario['rut'] }}</span>
                                </div>
                            @endif
                            
                            <!-- Dirección -->
                            <div class="info-item">
                                <span class="info-label">Dirección:</span>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="info-value">{{ $usuario['direccion'] }}</span>
                                    <a href="https://www.google.com/maps/search/{{ urlencode($usuario['direccion']) }}" 
                                       target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Derecha: Datos de Contacto -->
                    <div class="col-lg-6">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-phone text-success"></i>Datos de Contacto
                            </h6>
                            
                            <div class="info-item">
                                <span class="info-label">Teléfono Principal:</span>
                                <span class="info-value">
                                    <a href="tel:{{ $usuario['telefono'] }}" class="contact-link">
                                        <i class="fas fa-phone"></i>{{ $usuario['telefono'] }}
                                    </a>
                                </span>
                            </div>
                            
                            @if(isset($usuario['telefono_2']) && !empty($usuario['telefono_2']))
                            <div class="info-item">
                                <span class="info-label">Teléfono Alternativo:</span>
                                <span class="info-value">
                                    <a href="tel:{{ $usuario['telefono_2'] }}" class="contact-link">
                                        <i class="fas fa-phone"></i>{{ $usuario['telefono_2'] }}
                                    </a>
                                </span>
                            </div>
                            @endif
                            
                            <div class="info-item">
                                <span class="info-label">Email Principal:</span>
                                <span class="info-value">
                                    <a href="mailto:{{ $usuario['email'] }}" class="contact-link">
                                        <i class="fas fa-envelope"></i>{{ $usuario['email'] }}
                                    </a>
                                </span>
                            </div>
                            
                            @if(isset($usuario['email_2']) && !empty($usuario['email_2']))
                            <div class="info-item">
                                <span class="info-label">Email Alternativo:</span>
                                <span class="info-value">
                                    <a href="mailto:{{ $usuario['email_2'] }}" class="contact-link">
                                        <i class="fas fa-envelope"></i>{{ $usuario['email_2'] }}
                                    </a>
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Edición -->
            <div id="editarUsuarioForm" style="display: none;">
                <form method="POST" action="{{ route('usuarios.update.contacto', $usuario['rut']) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Teléfono Principal *</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="{{ $usuario['telefono'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefono_2" class="form-label">Teléfono Alternativo</label>
                            <input type="tel" class="form-control" id="telefono_2" name="telefono_2" 
                                   value="{{ $usuario['telefono_2'] ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Principal *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ $usuario['email'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email_2" class="form-label">Email Alternativo</label>
                            <input type="email" class="form-control" id="email_2" name="email_2" 
                                   value="{{ $usuario['email_2'] ?? '' }}">
                        </div>
                        <div class="col-12">
                            <label for="direccion" class="form-label">Dirección *</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                   value="{{ $usuario['direccion'] }}" required>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i> Guardar Cambios
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnCancelarEdicion">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Historial de Solicitudes -->
    @if(isset($solicitudes) && count($solicitudes) > 0)
    <div class="card">
        <div class="search-card-header">
            <h3 class="search-card-title mb-0">
                <i class="fas fa-history"></i>Historial de Solicitudes
                <span class="badge bg-white text-primary ms-2">{{ count($solicitudes) }}</span>
            </h3>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary active" onclick="filtrarSolicitudes('')">
                    Todas ({{ count($solicitudes) }})
                </button>
                <button class="btn btn-outline-warning" onclick="filtrarSolicitudes('Pendiente')">
                    Pendientes ({{ collect($solicitudes)->where('estado', 'Pendiente')->count() }})
                </button>
                <button class="btn btn-outline-info" onclick="filtrarSolicitudes('En proceso')">
                    En Proceso ({{ collect($solicitudes)->where('estado', 'En proceso')->count() }})
                </button>
                <button class="btn btn-outline-success" onclick="filtrarSolicitudes('Completado')">
                    Completadas ({{ collect($solicitudes)->where('estado', 'Completado')->count() }})
                </button>
            </div>
        </div>
        <div class="search-card-body p-0">
            <div class="table-responsive">
                <table class="table data-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="12%">Fecha</th>
                            <th width="20%">Departamento</th>
                            <th width="30%">Requerimiento</th>
                            <th width="18%">Localidad</th>
                            <th width="12%">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="solicitudesTableBody">
                        @foreach($solicitudes as $solicitud)
                        <tr class="solicitud-row" data-estado="{{ $solicitud['estado'] ?? '' }}">
                            <td>
                                <span class="fw-bold text-primary">#{{ $solicitud['id_solicitud'] ?? 'S/N' }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    @if(isset($solicitud['fecha_ingreso']) && !empty($solicitud['fecha_ingreso']))
                                        {{ \Carbon\Carbon::parse($solicitud['fecha_ingreso'])->format('d/m/Y') }}
                                    @else
                                        Sin fecha
                                    @endif
                                </span>
                            </td>
                            
                            {{-- COLUMNA DEPARTAMENTO - CORREGIDA --}}
                            <td>
                                @if(isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]))
                                    @php
                                        $requerimiento = $requerimientos[$solicitud['requerimiento_id']];
                                        $departamentoId = $requerimiento['departamento_id'] ?? null;
                                    @endphp
                                    
                                    @if($departamentoId && isset($departamentos[$departamentoId]))
                                        <span class="badge bg-info text-white">
                                            <i class="fas fa-building me-1"></i>
                                            {{ $departamentos[$departamentoId]['nombre'] }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">
                                            <i class="fas fa-question-circle me-1"></i>
                                            Depto. No especificado
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary text-white">
                                        <i class="fas fa-question-circle me-1"></i>
                                        Sin departamento
                                    </span>
                                @endif
                            </td>
                            
                            {{-- COLUMNA REQUERIMIENTO --}}
                            <td>
                                @if(isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]))
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-primary">
                                            <i class="fas fa-clipboard-check me-1"></i>
                                            {{ $requerimientos[$solicitud['requerimiento_id']]['nombre'] }}
                                        </span>
                                        @if(isset($solicitud['descripcion']) && !empty($solicitud['descripcion']))
                                            <small class="text-muted mt-1">
                                                {{ Str::limit($solicitud['descripcion'], 60) }}
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i>
                                        Requerimiento no especificado
                                    </span>
                                @endif
                            </td>
                            
                            {{-- COLUMNA LOCALIDAD + TIPO --}}
                            <td>
                                <div class="d-flex flex-column">
                                    @if(isset($solicitud['localidad']) && !empty($solicitud['localidad']))
                                        <span class="text-secondary">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $solicitud['localidad'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            No especificada
                                        </span>
                                    @endif
                                    
                                    @if(isset($solicitud['tipo_ubicacion']) && !empty($solicitud['tipo_ubicacion']))
                                        <small class="text-muted">
                                            <i class="fas fa-home me-1"></i>
                                            {{ $solicitud['tipo_ubicacion'] }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            
                            {{-- COLUMNA ESTADO + ACCIONES --}}
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="status-badge mb-2
                                        @if(($solicitud['estado'] ?? '') == 'Completado') status-success
                                        @elseif(($solicitud['estado'] ?? '') == 'En proceso') bg-primary
                                        @elseif(($solicitud['estado'] ?? '') == 'Pendiente') bg-warning text-dark
                                        @else status-secondary @endif">
                                        {{ $solicitud['estado'] ?? 'Sin estado' }}
                                    </span>
                                    
                                    @if(isset($solicitud['providencia']) && !empty($solicitud['providencia']))
                                        <small class="text-info fw-bold mb-2">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Prov: {{ $solicitud['providencia'] }}
                                        </small>
                                    @endif
                                    
                                    {{-- BOTONES DE ACCIÓN --}}
                                    <div class="table-actions">
                                        <button type="button" class="action-btn btn-view view-solicitud-details" 
                                                title="Ver detalles"
                                                data-id="{{ $solicitud['id_solicitud'] ?? '' }}"
                                                data-fecha="{{ $solicitud['fecha_ingreso'] ?? '' }}"
                                                data-estado="{{ $solicitud['estado'] ?? '' }}"
                                                data-etapa="{{ $solicitud['etapa'] ?? '' }}"
                                                data-descripcion="{{ $solicitud['descripcion'] ?? '' }}"
                                                data-localidad="{{ $solicitud['localidad'] ?? '' }}"
                                                data-ubicacion="{{ $solicitud['ubicacion'] ?? '' }}"
                                                data-tipo-ubicacion="{{ $solicitud['tipo_ubicacion'] ?? '' }}"
                                                data-providencia="{{ $solicitud['providencia'] ?? '' }}"
                                                data-requerimiento="{{ isset($requerimientos[$solicitud['requerimiento_id'] ?? '']) ? $requerimientos[$solicitud['requerimiento_id']]['nombre'] : 'No especificado' }}"
                                                data-departamento="{{ isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]) && isset($departamentos[$requerimientos[$solicitud['requerimiento_id']]['departamento_id'] ?? '']) ? $departamentos[$requerimientos[$solicitud['requerimiento_id']]['departamento_id']]['nombre'] : 'No especificado' }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud'] ?? '') }}" 
                                           class="action-btn btn-edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(isset($usuario))
    <!-- Usuario sin solicitudes -->
    <div class="card">
        <div class="search-card-header">
            <h3 class="search-card-title mb-0">
                <i class="fas fa-clipboard-list"></i>Solicitudes
            </h3>
        </div>
        <div class="search-card-body text-center py-5">
            <i class="fas fa-clipboard text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
            <h5 class="text-muted">No hay solicitudes registradas</h5>
            <p class="text-muted mb-4">Este usuario no tiene solicitudes en el sistema</p>
            <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" 
               class="btn btn-success btn-lg">
                <i class="fas fa-plus me-2"></i>Crear Primera Solicitud
            </a>
        </div>
    </div>
    @endif

    @elseif(isset($rut) && !empty($rut))
    <!-- Usuario no encontrado -->
    <div class="card">
        <div class="search-card-header bg-warning">
            <h3 class="search-card-title mb-0 text-dark">
                <i class="fas fa-user-times"></i>Usuario No Encontrado
            </h3>
        </div>
        <div class="search-card-body text-center py-5">
            <i class="fas fa-user-slash text-warning mb-3" style="font-size: 4rem; opacity: 0.6;"></i>
            <h5 class="text-warning">No se encontró ningún usuario con RUT: {{ $rut }}</h5>
            <p class="text-muted mb-4">Verifique el RUT ingresado o registre al usuario en el sistema</p>
            <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarBusqueda()">
                    <i class="fas fa-search me-2"></i>Buscar Otro Usuario
                </button>
                <a href="{{ route('usuarios.create', ['rut' => $rut]) }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Registrar Usuario
                </a>
            </div>
        </div>
    </div>
    @endif
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

<style>
.status-badge {
    font-size: 0.75rem;
    padding: 0.3rem 0.6rem;
    border-radius: 0.375rem;
    font-weight: 500;
    white-space: nowrap;
}

.status-success {
    background-color: #10b981;
    color: white;
}

.table-actions {
    display: flex;
    gap: 4px;
    justify-content: center;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-view {
    background-color: #06b6d4;
    color: white;
}

.btn-view:hover {
    background-color: #0891b2;
    color: white;
    transform: translateY(-1px);
}

.btn-edit {
    background-color: #3b82f6;
    color: white;
}

.btn-edit:hover {
    background-color: #2563eb;
    color: white;
    transform: translateY(-1px);
}

/* Mejorar el responsive de la tabla */
@media (max-width: 768px) {
    .table th, .table td {
        padding: 0.5rem 0.3rem;
        font-size: 0.85rem;
    }
    
    .status-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .action-btn {
        width: 28px;
        height: 28px;
    }
}

/* Animación para las filas */
.solicitud-row {
    transition: all 0.2s ease;
}

.solicitud-row:hover {
    background-color: rgba(59, 130, 246, 0.05);
    transform: translateX(2px);
}

/* Estilos para el modal de detalles de solicitud */
.details-panel {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(3px);
}

.details-panel.show {
    opacity: 1;
    visibility: visible;
}

.user-details-modal {
    background-color: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    width: 85%;
    max-width: 550px;
    max-height: 85vh;
    overflow: hidden;
    transform: scale(0.9) translateY(20px);
    transition: all 0.3s ease;
}

.details-panel.show .user-details-modal {
    transform: scale(1) translateY(0);
}

.user-details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 24px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.user-details-header h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border: 1px solid rgba(255, 255, 255, 0.3);
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
    transition: all 0.2s ease;
    cursor: pointer;
}

.detail-close-btn:hover {
    background-color: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.user-profile-header {
    display: flex;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.03) 0%, rgba(255, 255, 255, 0.8) 100%);
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 16px;
    font-weight: bold;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.user-info h3 {
    margin: 0 0 4px 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.user-type {
    color: #64748b;
    font-size: 0.9rem;
    margin: 0;
    font-weight: 500;
}

.user-details-container {
    padding: 24px;
    max-height: 50vh;
    overflow-y: auto;
}

.details-section {
    margin-bottom: 24px;
}

.section-title {
    font-size: 1rem;
    margin-bottom: 12px;
    color: #1e293b;
    display: flex;
    align-items: center;
    font-weight: 600;
    padding-bottom: 8px;
    border-bottom: 1px solid #eaedf3;
}

.section-title i {
    color: #3b82f6;
    margin-right: 8px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-full-width {
    grid-column: 1 / -1;
}

.detail-label {
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 4px;
    font-weight: 500;
}

.detail-value {
    font-weight: 600;
    color: #1e293b;
}

.panel-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding: 18px 24px;
    border-top: 1px solid #e2e8f0;
    background-color: #f8f9fa;
}

@media (max-width: 768px) {
    .user-details-modal {
        width: 95%;
        max-height: 90vh;
    }
    
    .user-profile-header {
        flex-direction: column;
        text-align: center;
        padding: 16px;
    }
    
    .user-avatar {
        margin-right: 0;
        margin-bottom: 12px;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .panel-actions {
        flex-direction: column;
        gap: 8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const rutInput = document.getElementById('rut');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const btnEditarContacto = document.getElementById('btnEditarContacto');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    const usuarioInfo = document.getElementById('usuarioInfo');
    const editarUsuarioForm = document.getElementById('editarUsuarioForm');
    const buscarUsuarioForm = document.getElementById('buscarUsuarioForm');

    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES DE SOLICITUD =====
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
    
    // Mostrar panel de detalles al hacer clic en el ojo
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Extraer datos de la solicitud
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
                departamento: this.getAttribute('data-departamento')
            };
            
            // Llenar datos en el panel
            document.getElementById('solicitudTitle').textContent = `Solicitud #${solicitudData.id}`;
            document.getElementById('solicitudSubtitle').textContent = `${solicitudData.departamento} - ${solicitudData.requerimiento}`;
            document.getElementById('solicitudId').textContent = solicitudData.id || 'Sin ID';
            document.getElementById('solicitudFecha').textContent = solicitudData.fecha ? formatearFecha(solicitudData.fecha) : 'Sin fecha';
            document.getElementById('solicitudEstado').textContent = solicitudData.estado || 'Sin estado';
            document.getElementById('solicitudEtapa').textContent = solicitudData.etapa || 'Sin etapa';
            document.getElementById('solicitudDescripcion').textContent = solicitudData.descripcion || 'Sin descripción';
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
            
            // Mostrar el panel
            showSolicitudDetails();
        });
    });
    
    // Cerrar panel de detalles
    if (closeSolicitudPanelBtn) {
        closeSolicitudPanelBtn.addEventListener('click', hideSolicitudDetails);
    }
    
    if (closeSolicitudPanelBtn2) {
        closeSolicitudPanelBtn2.addEventListener('click', hideSolicitudDetails);
    }
    
    // Cerrar al hacer clic en el fondo del modal
    if (solicitudDetailsPanel) {
        solicitudDetailsPanel.addEventListener('click', function(event) {
            if (event.target === solicitudDetailsPanel) {
                hideSolicitudDetails();
            }
        });
    }
    
    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && solicitudDetailsPanel.classList.contains('show')) {
            hideSolicitudDetails();
        }
    });

    // Validación y formateo de RUT
    if (rutInput) {
        rutInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9kK\-]/g, '');
            let cleanValue = value.replace(/\-/g, '');
            
            if (cleanValue.length > 1) {
                let rut = cleanValue.slice(0, -1);
                let dv = cleanValue.slice(-1);
                value = rut + '-' + dv;
            } else {
                value = cleanValue;
            }
            
            e.target.value = value;
            
            if (value.length >= 9) {
                validateRUT(value);
            } else {
                clearRutValidation();
            }
        });

        rutInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                validateRUT(e.target.value);
            }
        });

        rutInput.addEventListener('keypress', function(e) {
            const allowedChars = /[0-9kK\-]/;
            if (!allowedChars.test(e.key) && !['Backspace', 'Delete', 'Tab', 'Enter'].includes(e.key)) {
                e.preventDefault();
            }
            
            if (e.key === 'Enter') {
                e.preventDefault();
                if (validateRUT(e.target.value)) {
                    buscarUsuarioForm.submit();
                }
            }
        });
    }

    // Función para validar RUT
    function validateRUT(rut) {
        const rutRegex = /^\d{7,8}-[\dkK]$/;
        
        if (!rutRegex.test(rut)) {
            showRutError('Formato de RUT inválido. Use formato: 12345678-9');
            return false;
        }

        const cleanRut = rut.replace(/\-/g, '');
        const rutNumbers = cleanRut.slice(0, -1);
        const dv = cleanRut.slice(-1).toLowerCase();
        
        if (calculateDV(rutNumbers) !== dv) {
            showRutError('Dígito verificador incorrecto');
            return false;
        }

        clearRutValidation();
        return true;
    }

    // Calcular dígito verificador
    function calculateDV(rut) {
        let sum = 0;
        let multiplier = 2;
        
        for (let i = rut.length - 1; i >= 0; i--) {
            sum += parseInt(rut[i]) * multiplier;
            multiplier = multiplier === 7 ? 2 : multiplier + 1;
        }
        
        const remainder = sum % 11;
        const dv = 11 - remainder;
        
        if (dv === 11) return '0';
        if (dv === 10) return 'k';
        return dv.toString();
    }

    // Mostrar error de RUT  
    function showRutError(message) {
        const rutError = document.getElementById('rutError');
        if (rutError) {
            rutError.textContent = message;
            rutError.style.display = 'block';
        }
        rutInput.classList.add('is-invalid');
        btnBuscar.disabled = true;
    }

    // Limpiar validación de RUT
    function clearRutValidation() {
        const rutError = document.getElementById('rutError');
        if (rutError) {
            rutError.style.display = 'none';
        }
        rutInput.classList.remove('is-invalid');
        btnBuscar.disabled = false;
    }

    // Botón limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', limpiarBusqueda);
    }

    // Toggle para editar usuario
    if (btnEditarContacto && editarUsuarioForm && usuarioInfo) {
        btnEditarContacto.addEventListener('click', function() {
            usuarioInfo.style.display = 'none';
            editarUsuarioForm.style.display = 'block';
            btnEditarContacto.style.display = 'none';
        });
    }

    // Cancelar edición
    if (btnCancelarEdicion && editarUsuarioForm && usuarioInfo) {
        btnCancelarEdicion.addEventListener('click', function() {
            editarUsuarioForm.style.display = 'none';
            usuarioInfo.style.display = 'block';
            btnEditarContacto.style.display = 'inline-block';
        });
    }

    // Enfocar input al cargar
    if (rutInput) {
        rutInput.focus();
    }
});

// Función para limpiar búsqueda
function limpiarBusqueda() {
    const rutInput = document.getElementById('rut');
    if (rutInput) {
        rutInput.value = '';
        rutInput.focus();
    }
    
    // Limpiar URL
    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.pushState({path: newUrl}, '', newUrl);
    
    // Recargar página para limpiar completamente
    window.location.href = newUrl;
}

// Función para filtrar solicitudes
function filtrarSolicitudes(estado) {
    const rows = document.querySelectorAll('.solicitud-row');
    const buttons = document.querySelectorAll('[onclick^="filtrarSolicitudes"]');
    
    // Actualizar botones activos
    buttons.forEach(btn => {
        btn.classList.remove('btn-outline-secondary', 'btn-outline-warning', 'btn-outline-info', 'btn-outline-success', 'active');
        
        if ((btn.textContent.includes('Todas') && estado === '') ||
            (btn.textContent.includes('Pendientes') && estado === 'Pendiente') ||
            (btn.textContent.includes('En Proceso') && estado === 'En proceso') ||
            (btn.textContent.includes('Completadas') && estado === 'Completado')) {
            btn.classList.add('active', 'btn-outline-secondary');
        } else {
            if (btn.textContent.includes('Pendientes')) {
                btn.classList.add('btn-outline-warning');
            } else if (btn.textContent.includes('En Proceso')) {
                btn.classList.add('btn-outline-info');
            } else if (btn.textContent.includes('Completadas')) {
                btn.classList.add('btn-outline-success');
            } else {
                btn.classList.add('btn-outline-secondary');
            }
        }
    });
    
    // Filtrar filas
    rows.forEach(row => {
        const rowEstado = row.getAttribute('data-estado');
        if (estado === '' || rowEstado === estado) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    const tbody = document.getElementById('solicitudesTableBody');
    
    // Remover mensaje previo
    const noResultsMsg = tbody.querySelector('.no-results-message');
    if (noResultsMsg) {
        noResultsMsg.remove();
    }
    
    if (visibleRows.length === 0 && estado !== '') {
        const noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-message';
        noResultsRow.innerHTML = `
            <td colspan="6" class="text-center py-4 text-muted">
                <i class="fas fa-search me-2"></i>
                No se encontraron solicitudes con estado "${estado}"
            </td>
        `;
        tbody.appendChild(noResultsRow);
    }
}

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

// Atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + F para enfocar búsqueda
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        const rutInput = document.getElementById('rut');
        if (rutInput) {
            rutInput.focus();
            rutInput.select();
        }
    }
    
    // Escape para limpiar búsqueda
    if (e.key === 'Escape') {
        limpiarBusqueda();
    }
    
    // Ctrl + N para nueva solicitud (si hay usuario)
    if (e.ctrlKey && e.key === 'n' && document.getElementById('usuarioCard')) {
        e.preventDefault();
        const nuevaSolicitudBtn = document.querySelector('a[href*="solicitudes.create"]');
        if (nuevaSolicitudBtn) {
            window.location.href = nuevaSolicitudBtn.href;
        }
    }
});
</script>
@endsection
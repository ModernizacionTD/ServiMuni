@extends('layouts.app')

@section('title', 'Búsqueda de Usuario - ServiMuni')

@section('page-title', 'Búsqueda de Usuario')

@section('content')
<div class="search-view-container table-view-container">
    <link rel="stylesheet" href="{{ asset('css/busqueda.css') }}">

    <div class="card">
        <div class="card-header search-card-header">
            <h3 class="card-title search-card-title">
                <i class="fas fa-search me-2"></i>Buscar Usuario
            </h3>
        </div>
        <div class="card-body search-card-body">
            <form id="buscarUsuarioForm" method="GET" action="{{ route('buscar.usuario') }}" class="row g-3 align-items-end">
                <div class="col-md-8">
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
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill" id="btnBuscar">
                            <i class="fas fa-search me-2"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar">
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
        <div class="card-header bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user-check me-2"></i>Usuario Encontrado
                </h3>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" id="btnEditarContacto">
                        <i class="fas fa-edit me-1"></i> Editar Contacto
                    </button>
                    <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" 
                       class="btn btn-sm btn-warning">
                        <i class="fas fa-plus me-1"></i> Nueva Solicitud
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
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

                <!-- Información Detallada -->
                <div class="row g-3">
                    @if($usuario['tipo_persona'] == 'Natural')
                    <!-- Datos Personales -->
                    <div class="col-md-6">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-user text-primary me-2"></i>Datos Personales
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Fecha de Nacimiento:</span>
                                    <span class="info-value">
                                        {{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->format('d/m/Y') }}
                                        <small class="text-muted">({{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->age }} años)</small>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Género:</span>
                                    <span class="info-value">{{ $usuario['genero'] ?? 'No especificado' }}</span>
                                </div>
                                @if($usuario['uso_ns'] == 'Sí')
                                <div class="info-item">
                                    <span class="info-label">Nombre Social:</span>
                                    <span class="info-value">{{ $usuario['nombre_social'] ?: 'No especificado' }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Información de Contacto -->
                    <div class="col-md-6">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-phone text-success me-2"></i>Contacto
                            </h6>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Teléfono Principal:</span>
                                    <span class="info-value">
                                        <a href="tel:{{ $usuario['telefono'] }}" class="contact-link">
                                            <i class="fas fa-phone me-1"></i>{{ $usuario['telefono'] }}
                                        </a>
                                    </span>
                                </div>
                                @if($usuario['telefono_2'])
                                <div class="info-item">
                                    <span class="info-label">Teléfono Alternativo:</span>
                                    <span class="info-value">
                                        <a href="tel:{{ $usuario['telefono_2'] }}" class="contact-link">
                                            <i class="fas fa-phone me-1"></i>{{ $usuario['telefono_2'] }}
                                        </a>
                                    </span>
                                </div>
                                @endif
                                <div class="info-item">
                                    <span class="info-label">Email Principal:</span>
                                    <span class="info-value">
                                        <a href="mailto:{{ $usuario['email'] }}" class="contact-link">
                                            <i class="fas fa-envelope me-1"></i>{{ $usuario['email'] }}
                                        </a>
                                    </span>
                                </div>
                                @if($usuario['email_2'])
                                <div class="info-item">
                                    <span class="info-label">Email Alternativo:</span>
                                    <span class="info-value">
                                        <a href="mailto:{{ $usuario['email_2'] }}" class="contact-link">
                                            <i class="fas fa-envelope me-1"></i>{{ $usuario['email_2'] }}
                                        </a>
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dirección (Ancho completo) -->
                    <div class="col-12">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-map-marker-alt text-danger me-2"></i>Dirección
                            </h6>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="info-value">{{ $usuario['direccion'] }}</span>
                                <a href="https://www.google.com/maps/search/{{ urlencode($usuario['direccion']) }}" 
                                   target="_blank" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-external-link-alt me-1"></i>Ver en mapa
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de Solicitudes -->
                    @if(isset($solicitudes))
                    <div class="col-12">
                        <div class="search-info-group">
                            <h6 class="search-info-group-title">
                                <i class="fas fa-chart-bar text-info me-2"></i>Resumen de Solicitudes
                            </h6>
                            <div class="stats-grid">
                                @php
                                    $totalSolicitudes = count($solicitudes);
                                    $pendientes = collect($solicitudes)->where('estado', 'Pendiente')->count();
                                    $enProceso = collect($solicitudes)->where('estado', 'En proceso')->count();
                                    $completadas = collect($solicitudes)->where('estado', 'Completado')->count();
                                @endphp
                                
                                <div class="stat-item">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number">{{ $totalSolicitudes }}</span>
                                        <span class="stat-label">Total</span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-icon bg-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number">{{ $pendientes }}</span>
                                        <span class="stat-label">Pendientes</span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-icon bg-info">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number">{{ $enProceso }}</span>
                                        <span class="stat-label">En Proceso</span>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-content">
                                        <span class="stat-number">{{ $completadas }}</span>
                                        <span class="stat-label">Completadas</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
                                   value="{{ $usuario['telefono_2'] }}">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Principal *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ $usuario['email'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email_2" class="form-label">Email Alternativo</label>
                            <input type="email" class="form-control" id="email_2" name="email_2" 
                                   value="{{ $usuario['email_2'] }}">
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
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-history me-2"></i>Historial de Solicitudes
                </h3>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-secondary active" onclick="filtrarSolicitudes('')">
                        Todas ({{ count($solicitudes) }})
                    </button>
                    <button class="btn btn-outline-warning" onclick="filtrarSolicitudes('Pendiente')">
                        Pendientes ({{ collect($solicitudes)->where('estado', 'Pendiente')->count() }})
                    </button>
                    <button class="btn btn-outline-success" onclick="filtrarSolicitudes('Completado')">
                        Completadas ({{ collect($solicitudes)->where('estado', 'Completado')->count() }})
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table data-table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="10%">ID</th>
                            <th width="12%">Fecha</th>
                            <th width="30%">Requerimiento</th>
                            <th width="12%">Estado</th>
                            <th width="18%">Etapa</th>
                            <th width="18%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="solicitudesTableBody">
                        @foreach($solicitudes as $solicitud)
                        <tr class="solicitud-row" data-estado="{{ $solicitud['estado'] }}">
                            <td>
                                <span class="fw-bold text-primary">#{{ $solicitud['id_solicitud'] }}</span>
                            </td>
                            <td>
                                <span class="text-muted">{{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}</span>
                            </td>
                            <td>
                                @if(isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]))
                                    <span class="status-badge bg-light text-dark border">{{ $requerimientos[$solicitud['requerimiento_id']]['nombre'] }}</span>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge 
                                    @if($solicitud['estado'] == 'Completado') status-success
                                    @elseif($solicitud['estado'] == 'En proceso') bg-primary
                                    @elseif($solicitud['estado'] == 'Pendiente') bg-warning text-dark
                                    @else status-secondary @endif">
                                    {{ $solicitud['estado'] }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $solicitud['etapa'] ?: 'Sin etapa' }}</small>
                                @if($solicitud['providencia'])
                                <br><small class="text-info">{{ $solicitud['providencia'] }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('solicitudes.show', $solicitud['id_solicitud']) }}" 
                                       class="btn btn-sm btn-info action-btn btn-view" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud']) }}" 
                                       class="btn btn-sm btn-primary action-btn btn-edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @elseif(isset($rut) && !isset($usuario))
    <!-- Usuario No Encontrado -->
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title">
                <i class="fas fa-user-slash me-2"></i>Usuario No Encontrado
            </h3>
        </div>
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-user-times display-1 text-warning"></i>
            </div>
            <h4 class="mb-3">No se encontró el usuario</h4>
            <p class="text-muted mb-4">
                No existe ningún usuario registrado con el RUT: <strong>{{ $rut }}</strong>
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('usuarios.create') }}?rut={{ $rut }}" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>Registrar Usuario
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="limpiarBusqueda()">
                    <i class="fas fa-search me-2"></i>Nueva Búsqueda
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Indicador de carga -->
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-content">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <h5 class="text-primary">Buscando usuario...</h5>
            <p class="text-muted">Por favor espere un momento</p>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para buscar usuario */
.user-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 8px;
    padding: 20px;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, #2c5aa0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(56, 103, 214, 0.3);
}

.user-basic-info h4 {
    font-weight: 600;
    color: var(--text-color);
}

.info-grid {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.contact-link {
    color: var(--primary-color);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
}

.contact-link:hover {
    color: #2c5aa0;
    text-decoration: none;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 12px;
    background: var(--bg-light);
    border-radius: 8px;
    gap: 10px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: bold;
    color: var(--text-color);
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.loading-content {
    background: white;
    padding: 2rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.status-badge {
    font-weight: 500;
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .user-header {
        text-align: center;
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
        margin: 0 auto 12px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
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
    const loadingOverlay = document.getElementById('loadingOverlay');
    const buscarUsuarioForm = document.getElementById('buscarUsuarioForm');

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
    
    // Ocultar tarjeta de usuario si existe
    const usuarioCard = document.getElementById('usuarioCard');
    if (usuarioCard) {
        usuarioCard.remove();
    }
    
    // Limpiar URL
    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
    window.history.pushState({path: newUrl}, '', newUrl);
}

// Función para filtrar solicitudes
function filtrarSolicitudes(estado) {
    const rows = document.querySelectorAll('.solicitud-row');
    const buttons = document.querySelectorAll('[onclick^="filtrarSolicitudes"]');
    
    // Actualizar botones activos
    buttons.forEach(btn => {
        btn.classList.remove('btn-outline-secondary', 'btn-outline-warning', 'btn-outline-success', 'active');
        
        if ((btn.textContent.includes('Todas') && estado === '') ||
            (btn.textContent.includes('Pendientes') && estado === 'Pendiente') ||
            (btn.textContent.includes('Completadas') && estado === 'Completado')) {
            btn.classList.add('active', 'btn-outline-secondary');
        } else {
            if (btn.textContent.includes('Pendientes')) {
                btn.classList.add('btn-outline-warning');
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

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Inicializar y mostrar toast
    if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    } else {
        // Fallback si Bootstrap no está disponible
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
}

// Crear contenedor de toasts si no existe
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
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
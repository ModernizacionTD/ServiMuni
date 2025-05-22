@extends('layouts.app')

@section('title', 'Búsqueda de Usuario - ServiMuni')

@section('page-title', 'Búsqueda de Usuario')

@section('content')
<div class="container">
    <!-- Sección de búsqueda por RUT mejorada -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h2 class="card-title mb-0"><i class="fas fa-search me-2"></i>Buscar Usuario por RUT</h2>
        </div>
        <div class="card-body">
            <form id="buscarUsuarioForm" method="GET" action="{{ route('buscar.usuario') }}" class="row align-items-end">
                <div class="col-md-6">
                    <div class="form-group mb-0">
                        <label for="rut" class="form-label">Ingrese RUT del Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
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
                            Ingrese RUT sin puntos y con guión. Formato: 12345678-9
                        </small>
                        <div class="invalid-feedback" id="rutError"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnBuscar">
                            <i class="fas fa-search me-1"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="btnLimpiar">
                            <i class="fas fa-eraser me-1"></i> Limpiar
                        </button>
                        <a href="{{ route('usuarios.create') }}" class="btn btn-success" id="btnNuevoUsuario">
                            <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Indicador de carga -->
    <div class="loading-indicator" id="loadingIndicator" style="display: none;">
        <div class="card">
            <div class="card-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mb-0">Buscando usuario...</p>
            </div>
        </div>
    </div>

    <!-- Resultados de la búsqueda -->
    @if(isset($usuario))
        <div class="result-container" id="resultContainer">
            <div class="alert alert-success shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">Usuario encontrado</h5>
                        <p class="mb-0">{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }} - RUT: {{ $usuario['rut'] }}</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Información del usuario compacta -->
                <div class="col-lg-8">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center bg-light">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Información del Usuario
                            </h3>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditarUsuario">
                                    <i class="fas fa-edit me-1"></i> Editar
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <!-- Vista normal de información COMPACTA -->
                            <div id="usuarioInfo">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">RUT</small>
                                        <span class="fw-bold">{{ $usuario['rut'] }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Tipo</small>
                                        <span class="badge {{ $usuario['tipo_persona'] == 'Natural' ? 'bg-primary' : 'bg-info' }}">
                                            {{ $usuario['tipo_persona'] }}
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Nombre Completo</small>
                                        <span class="fw-bold">{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</span>
                                    </div>
                                </div>

                                @if($usuario['tipo_persona'] == 'Natural')
                                <div class="row g-2 mt-2">
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Fecha Nacimiento</small>
                                        <span>{{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->format('d/m/Y') }}</span>
                                        <small class="text-muted ms-1">({{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->age }} años)</small>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Género</small>
                                        <span>{{ $usuario['genero'] }}</span>
                                    </div>
                                    @if($usuario['uso_ns'] == 'Sí')
                                    <div class="col-md-4">
                                        <small class="text-muted d-block">Nombre Social</small>
                                        <span>{{ $usuario['nombre_social'] ?: 'No especificado' }}</span>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <div class="row g-2 mt-2">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Teléfonos</small>
                                        <div>
                                            <a href="tel:{{ $usuario['telefono'] }}" class="text-decoration-none me-2">
                                                <i class="fas fa-phone text-primary me-1"></i>{{ $usuario['telefono'] }}
                                            </a>
                                            @if($usuario['telefono_2'])
                                                <a href="tel:{{ $usuario['telefono_2'] }}" class="text-decoration-none">
                                                    <i class="fas fa-phone text-secondary me-1"></i>{{ $usuario['telefono_2'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Emails</small>
                                        <div>
                                            <a href="mailto:{{ $usuario['email'] }}" class="text-decoration-none d-block">
                                                <i class="fas fa-envelope text-primary me-1"></i>{{ $usuario['email'] }}
                                            </a>
                                            @if($usuario['email_2'])
                                                <a href="mailto:{{ $usuario['email_2'] }}" class="text-decoration-none d-block">
                                                    <i class="fas fa-envelope text-secondary me-1"></i>{{ $usuario['email_2'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-2 mt-2">
                                    <div class="col-12">
                                        <small class="text-muted d-block">Dirección</small>
                                        <span>{{ $usuario['direccion'] }}</span>
                                        <a href="https://www.google.com/maps/search/{{ urlencode($usuario['direccion']) }}" 
                                           target="_blank" class="btn btn-sm btn-outline-info ms-2">
                                            <i class="fas fa-map"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario de edición compacto -->
                            <div id="editarUsuarioForm" style="display: none;">
                                <form method="POST" action="{{ route('usuarios.update.contacto', $usuario['rut']) }}">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label for="telefono" class="form-label">Teléfono Principal</label>
                                            <input type="tel" class="form-control form-control-sm" id="telefono" name="telefono" 
                                                   value="{{ $usuario['telefono'] }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="telefono_2" class="form-label">Teléfono Alternativo</label>
                                            <input type="tel" class="form-control form-control-sm" id="telefono_2" name="telefono_2" 
                                                   value="{{ $usuario['telefono_2'] }}">
                                        </div>
                                    </div>
                                    
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-6">
                                            <label for="email" class="form-label">Email Principal</label>
                                            <input type="email" class="form-control form-control-sm" id="email" name="email" 
                                                   value="{{ $usuario['email'] }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email_2" class="form-label">Email Alternativo</label>
                                            <input type="email" class="form-control form-control-sm" id="email_2" name="email_2" 
                                                   value="{{ $usuario['email_2'] }}">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label">Dirección</label>
                                        <input type="text" class="form-control form-control-sm" id="direccion" name="direccion" 
                                               value="{{ $usuario['direccion'] }}" required>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save me-1"></i> Guardar
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm" id="btnCancelarEdicion">
                                            <i class="fas fa-times me-1"></i> Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral compacto -->
                <div class="col-lg-4">
                    <!-- Botón para nueva solicitud -->
                    <div class="card mb-4 shadow-sm border-success">
                        <div class="card-header bg-success text-white py-2">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Nueva Solicitud
                            </h4>
                        </div>
                        <div class="card-body p-3 text-center">
                            <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" 
                               class="btn btn-success w-100">
                                <i class="fas fa-clipboard-list me-2"></i> Crear Solicitud
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de solicitudes mejorado -->
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-history me-2"></i>Historial de Solicitudes
                    </h3>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" onclick="filtrarSolicitudes('')">
                            Todas
                        </button>
                        <button class="btn btn-sm btn-outline-primary" onclick="filtrarSolicitudes('Pendiente')">
                            Pendientes
                        </button>
                        <button class="btn btn-sm btn-outline-success" onclick="filtrarSolicitudes('Completado')">
                            Completadas
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($solicitudes) && count($solicitudes) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">ID</th>
                                        <th width="12%">Fecha</th>
                                        <th width="20%">Requerimiento</th>
                                        <th width="12%">Estado</th>
                                        <th width="12%">Etapa</th>
                                        <th width="12%">Providencia</th>
                                        <th width="22%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="solicitudesTableBody">
                                    @foreach($solicitudes as $solicitud)
                                    <tr class="solicitud-row" data-estado="{{ $solicitud['estado'] }}">
                                        <td>
                                            <span class="fw-bold">#{{ $solicitud['id_solicitud'] }}</span>
                                        </td>
                                        <td>
                                            <span class="small">{{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            @if(isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]))
                                                <span class="badge bg-light text-dark">{{ $requerimientos[$solicitud['requerimiento_id']]['nombre'] }}</span>
                                            @else
                                                <span class="text-muted small">No especificado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($solicitud['estado'] == 'Completado') bg-success
                                                @elseif($solicitud['estado'] == 'En proceso') bg-primary
                                                @elseif($solicitud['estado'] == 'Pendiente') bg-warning text-dark
                                                @else bg-secondary @endif">
                                                {{ $solicitud['estado'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $solicitud['etapa'] }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $solicitud['providencia'] ?: 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('solicitudes.show', $solicitud['id_solicitud']) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud']) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="verDetalleRapido({{ $solicitud['id_solicitud'] }})" 
                                                        title="Vista rápida">
                                                    <i class="fas fa-search-plus"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No hay solicitudes registradas</h5>
                            <p class="text-muted">Este usuario no tiene solicitudes registradas en el sistema.</p>
                            <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Crear Primera Solicitud
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif(isset($rut) && !isset($usuario))
        <!-- Usuario no encontrado -->
        <div class="result-container" id="resultContainer">
            <div class="alert alert-warning shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">Usuario no encontrado</h5>
                        <p class="mb-0">No se encontró ningún usuario con el RUT: <strong>{{ $rut }}</strong></p>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-plus fs-1 text-primary mb-4"></i>
                    <h4 class="mb-3">¿Desea registrar este usuario?</h4>
                    <p class="text-muted mb-4">El usuario con RUT {{ $rut }} no está registrado en el sistema.</p>
                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('usuarios.create', ['rut' => $rut]) }}" 
                           class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i> Registrar Usuario
                        </a>
                        <button class="btn btn-outline-secondary btn-lg" id="btnBuscarOtro">
                            <i class="fas fa-search me-2"></i> Buscar Otro RUT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Toast para notificaciones eliminado -->

<!-- Modal de detalle rápido eliminado -->

<style>
/* Estilos adicionales */
.info-item {
    margin-bottom: 1rem;
}

.info-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-light);
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 0.95rem;
    color: var(--text-color);
    font-weight: 500;
}

.stat-item {
    padding: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.loading-indicator {
    animation: fadeIn 0.3s ease-in;
}

.result-container {
    animation: slideInUp 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mejoras visuales */
.card {
    border: none;
    border-radius: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.table {
    border-radius: 8px;
    overflow: hidden;
}

.table th {
    border-top: none;
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-group-text {
    border: none;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
}

/* Responsive improvements */
@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 8px !important;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        font-size: 0.9rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
}

/* Toast customization */
.toast {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Modal improvements */
.modal-content {
    border: none;
    border-radius: 12px;
}

.modal-header {
    border-bottom: 1px solid #eee;
    background-color: #f8f9fa;
}

/* Badge improvements */
.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
    border-radius: 6px;
}

/* Animation for form toggle */
#editarUsuarioForm {
    transition: all 0.3s ease;
}

#usuarioInfo {
    transition: all 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const rutInput = document.getElementById('rut');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const btnEditarUsuario = document.getElementById('btnEditarUsuario');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    const btnVerCompleto = document.getElementById('btnVerCompleto');
    const btnBuscarOtro = document.getElementById('btnBuscarOtro');
    const usuarioInfo = document.getElementById('usuarioInfo');
    const usuarioInfoCompleto = document.getElementById('usuarioInfoCompleto');
    const editarUsuarioForm = document.getElementById('editarUsuarioForm');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const buscarUsuarioForm = document.getElementById('buscarUsuarioForm');

    // Validación y formateo de RUT
    if (rutInput) {
        // Formatear RUT mientras se escribe (SIN PUNTOS, solo guión)
        rutInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^0-9kK\-]/g, ''); // Solo números, K y guión
            
            // Remover guiones existentes para reformatear
            let cleanValue = value.replace(/\-/g, '');
            
            if (cleanValue.length > 1) {
                // Separar dígito verificador
                let rut = cleanValue.slice(0, -1);
                let dv = cleanValue.slice(-1);
                
                // Formatear SIN puntos, solo con guión
                value = rut + '-' + dv;
            } else {
                value = cleanValue;
            }
            
            e.target.value = value;
            
            // Validar RUT si tiene el formato completo
            if (value.length >= 9) {
                validateRUT(value);
            } else {
                clearRutValidation();
            }
        });

        // Validar al perder foco
        rutInput.addEventListener('blur', function(e) {
            if (e.target.value) {
                validateRUT(e.target.value);
            }
        });

        // Permitir solo ciertos caracteres
        rutInput.addEventListener('keypress', function(e) {
            const allowedChars = /[0-9kK\-]/;
            if (!allowedChars.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab') {
                e.preventDefault();
            }
        });
    }

    // Función para validar RUT
    function validateRUT(rut) {
        // Formato esperado: 12345678-9 (sin puntos)
        const rutRegex = /^\d{7,8}-[\dkK]$/;
        
        if (!rutRegex.test(rut)) {
            showRutError('Formato de RUT inválido. Use formato: 12345678-9');
            return false;
        }

        // Validar dígito verificador
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
        btnLimpiar.addEventListener('click', function() {
            rutInput.value = '';
            clearRutValidation();
            rutInput.focus();
            
            // Ocultar resultados si existen
            const resultContainer = document.getElementById('resultContainer');
            if (resultContainer) {
                resultContainer.style.display = 'none';
            }
        });
    }

    // Manejar envío del formulario
    if (buscarUsuarioForm) {
        buscarUsuarioForm.addEventListener('submit', function(e) {
            if (rutInput.value && !validateRUT(rutInput.value)) {
                e.preventDefault();
                showNotification('Por favor, ingrese un RUT válido', 'error');
                return;
            }

            // Mostrar indicador de carga
            if (loadingIndicator) {
                loadingIndicator.style.display = 'block';
            }

            // Deshabilitar botón de búsqueda
            btnBuscar.disabled = true;
            btnBuscar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Buscando...';
        });
    }

    // Toggle para editar usuario
    if (btnEditarUsuario && editarUsuarioForm && usuarioInfo) {
        btnEditarUsuario.addEventListener('click', function() {
            usuarioInfo.style.display = 'none';
            editarUsuarioForm.style.display = 'block';
            btnEditarUsuario.style.display = 'none';
        });
    }

    // Cancelar edición
    if (btnCancelarEdicion && editarUsuarioForm && usuarioInfo) {
        btnCancelarEdicion.addEventListener('click', function() {
            editarUsuarioForm.style.display = 'none';
            usuarioInfo.style.display = 'block';
            btnEditarUsuario.style.display = 'inline-block';
        });
    }

    // Buscar otro RUTUsuarioForm.style.display = 'none';
            usuarioInfo.style.display = 'block';
            btnEditarUsuario.style.display = 'inline-block';
        });
    }

    // Toggle vista completa
    if (btnVerCompleto && usuarioInfoCompleto && usuarioInfo) {
        btnVerCompleto.addEventListener('click', function() {
            if (usuarioInfoCompleto.style.display === 'none') {
                usuarioInfoCompleto.style.display = 'block';
                usuarioInfo.style.display = 'none';
                btnVerCompleto.innerHTML = '<i class="fas fa-compress me-1"></i> Vista Normal';
            } else {
                usuarioInfoCompleto.style.display = 'none';
                usuarioInfo.style.display = 'block';
                btnVerCompleto.innerHTML = '<i class="fas fa-expand me-1"></i> Ver Completo';
            }
        });
    }

});

// Función para mostrar notificaciones (simplificada)
function showNotification(message, type = 'info') {
    // Mostrar alerta simple en lugar de toast
    if (type === 'error') {
        alert('Error: ' + message);
    } else {
        console.log(message);
    }
}

// Función para filtrar solicitudes
function filtrarSolicitudes(estado) {
    const rows = document.querySelectorAll('.solicitud-row');
    const buttons = document.querySelectorAll('[onclick^="filtrarSolicitudes"]');
    
    // Actualizar botones activos
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary', 'btn-outline-primary', 'btn-outline-secondary', 'btn-outline-success');
        if (btn.textContent.trim() === 'Todas' && estado === '') {
            btn.classList.add('btn-primary');
        } else if (btn.textContent.trim() === 'Pendientes' && estado === 'Pendiente') {
            btn.classList.add('btn-primary');
        } else if (btn.textContent.trim() === 'Completadas' && estado === 'Completado') {
            btn.classList.add('btn-success');
        } else {
            btn.classList.add('btn-outline-secondary');
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
}

// Función simplificada para ver detalle rápido
function verDetalleRapido(solicitudId) {
    // Redirigir directamente a la página de detalles
    window.location.href = `/solicitudes/${solicitudId}`;
}

// Atajos de teclado simplificados
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
    
    // Escape para cancelar edición
    if (e.key === 'Escape') {
        const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
        if (btnCancelarEdicion && btnCancelarEdicion.offsetParent !== null) {
            btnCancelarEdicion.click();
        }
    }
});
</script>
@endsection
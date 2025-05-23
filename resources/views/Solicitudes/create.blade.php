@extends('layouts.app')

@section('title', 'Crear Solicitud - ServiMuni')

@section('page-title', 'Crear Nueva Solicitud')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="form-view-container">
    <div class="card">
        <div class="form-card-header">
            <h2 class="form-card-title">
                <i class="fas fa-clipboard-list"></i>Crear Nueva Solicitud
            </h2>
            <div class="form-header-actions">
                <a href="{{ route('buscar.usuario') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="form-card-body">
            @if(session('error'))
                <div class="form-alert form-alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Error:</strong> {{ session('error') }}
                    </div>
                </div>
            @endif
            
            @if($usuario)
                <div class="form-alert form-alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Solicitud para:</strong> {{ $usuario['nombre'] }} {{ $usuario['apellidos'] }} (RUT: {{ $usuario['rut'] }})
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('solicitudes.store') }}" enctype="multipart/form-data" id="solicitudForm" novalidate>
                @csrf
                
                <!-- Sección: Usuario -->
                @if($usuario)
                    <input type="hidden" name="rut_usuario" value="{{ $usuario['rut'] }}">
                @else
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-user"></i>Usuario Solicitante
                    </h3>
                    
                    <div class="form-group">
                        <label for="rut_usuario" class="form-label required">RUT del Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" 
                                   id="rut_usuario" 
                                   name="rut_usuario" 
                                   class="form-control @error('rut_usuario') is-invalid @enderror" 
                                   placeholder="12.345.678-9" 
                                   value="{{ old('rut_usuario') }}" 
                                   required>
                            <a href="{{ route('buscar.usuario') }}" class="btn btn-outline-info">
                                <i class="fas fa-search"></i> Buscar
                            </a>
                        </div>
                        @error('rut_usuario')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Si no conoce el RUT, use el buscador de usuarios
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Sección: Tipo de Requerimiento -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-tasks"></i>Tipo de Requerimiento
                    </h3>
                    
                    <!-- Selector de método de búsqueda -->
                    <div class="form-group">
                        <label class="form-label">¿Cómo desea buscar el requerimiento?</label>
                        <div class="form-row">
                            <div class="form-col-2">
                                <div class="form-check form-radio">
                                    <input class="form-check-input" type="radio" name="metodoBusqueda" id="metodoDepartamento" value="departamento" checked>
                                    <label class="form-check-label" for="metodoDepartamento">
                                        <i class="fas fa-sitemap me-2"></i>Por Departamento
                                    </label>
                                </div>
                            </div>
                            <div class="form-col-2">
                                <div class="form-check form-radio">
                                    <input class="form-check-input" type="radio" name="metodoBusqueda" id="metodoRequerimiento" value="requerimiento">
                                    <label class="form-check-label" for="metodoRequerimiento">
                                        <i class="fas fa-list me-2"></i>Por Tipo de Requerimiento
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Búsqueda por departamento -->
                    <div id="busquedaPorDepartamento">
                        <div class="form-row">
                            <div class="form-col-2">
                                <div class="form-group">
                                    <label for="departamento_id" class="form-label">Departamento</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-building"></i>
                                        </span>
                                        <select id="departamento_id" class="form-select">
                                            <option value="">Todos los departamentos</option>
                                            @foreach($departamentos as $departamento)
                                                <option value="{{ $departamento['id'] }}" {{ old('departamento_id') == $departamento['id'] ? 'selected' : '' }}>
                                                    {{ $departamento['nombre'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i>
                                        Filtra los requerimientos por departamento
                                    </div>
                                </div>
                            </div>
                            <div class="form-col-2">
                                <div class="form-group">
                                    <label for="requerimiento_id_dept" class="form-label required">Tipo de Requerimiento</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-clipboard-check"></i>
                                        </span>
                                        <select id="requerimiento_id_dept" name="requerimiento_id" class="form-select @error('requerimiento_id') is-invalid @enderror" required>
                                            <option value="">Seleccionar requerimiento...</option>
                                            @foreach($requerimientos as $requerimiento)
                                                <option 
                                                    value="{{ $requerimiento['id_requerimiento'] }}" 
                                                    data-departamento="{{ $requerimiento['departamento_id'] }}"
                                                    data-descripcion="{{ $requerimiento['descripcion_req'] }}"
                                                    data-precio="{{ $requerimiento['descripcion_precio'] }}"
                                                    {{ old('requerimiento_id') == $requerimiento['id_requerimiento'] ? 'selected' : '' }}
                                                >
                                                    {{ $requerimiento['nombre'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('requerimiento_id')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Búsqueda directa por requerimiento -->
                    <div id="busquedaPorRequerimiento" style="display: none;">
                        <div class="form-group">
                            <label for="requerimiento_id_directo" class="form-label required">Seleccione Tipo de Requerimiento</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-clipboard-list"></i>
                                </span>
                                <select id="requerimiento_id_directo" class="form-select @error('requerimiento_id') is-invalid @enderror">
                                    <option value="">Seleccionar requerimiento...</option>
                                    @php
                                        $requerimientosOrdenados = collect($requerimientos)->sortBy('nombre')->all();
                                    @endphp
                                    
                                    @foreach($requerimientosOrdenados as $requerimiento)
                                        <option 
                                            value="{{ $requerimiento['id_requerimiento'] }}"
                                            data-descripcion="{{ $requerimiento['descripcion_req'] }}"
                                            data-precio="{{ $requerimiento['descripcion_precio'] }}"
                                            {{ old('requerimiento_id') == $requerimiento['id_requerimiento'] ? 'selected' : '' }}
                                        >
                                            {{ $requerimiento['nombre'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('requerimiento_id')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle"></i>
                                Lista completa de requerimientos ordenada alfabéticamente
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detalles del requerimiento -->
                    <div id="detallesRequerimiento" style="display: none;">
                        <div class="form-alert form-alert-info">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Detalles del Requerimiento Seleccionado</strong>
                                <div class="mt-2">
                                    <strong>Descripción:</strong> <span id="descripcion_requerimiento">-</span>
                                </div>
                                <div class="mt-1">
                                    <strong>Precio:</strong> <span id="descripcion_precio">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Datos de la Solicitud -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-file-alt"></i>Datos de la Solicitud
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-2">
                            <div class="form-group">
                                <label for="providencia" class="form-label">Número de Providencia</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <input type="number" 
                                           id="providencia" 
                                           name="providencia" 
                                           class="form-control @error('providencia') is-invalid @enderror" 
                                           value="{{ old('providencia') }}"
                                           placeholder="Número de providencia">
                                </div>
                                @error('providencia')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Campo opcional
                                </div>
                            </div>
                        </div>
                        <div class="form-col-2">
                            <!-- Espacio para mantener el layout -->
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion" class="form-label required">Descripción de la Solicitud</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-align-left"></i>
                            </span>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      class="form-textarea @error('descripcion') is-invalid @enderror" 
                                      rows="4" 
                                      placeholder="Describa detalladamente la solicitud..."
                                      required>{{ old('descripcion') }}</textarea>
                        </div>
                        @error('descripcion')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Proporcione todos los detalles relevantes de la solicitud
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Ubicación -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i>Ubicación
                    </h3>
                    
                    <div class="form-row">
                        <div class="form-col-3">
                            <div class="form-group">
                                <label for="localidad" class="form-label required">Localidad</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-city"></i>
                                    </span>
                                    <select id="localidad" name="localidad" class="form-select @error('localidad') is-invalid @enderror" required>
                                        <option value="">Seleccione localidad...</option>
                                        <option value="Valparaíso" {{ old('localidad') == 'Valparaíso' ? 'selected' : '' }}>Valparaíso</option>
                                        <option value="Placilla" {{ old('localidad') == 'Placilla' ? 'selected' : '' }}>Placilla</option>
                                        <option value="Laguna Verde" {{ old('localidad') == 'Laguna Verde' ? 'selected' : '' }}>Laguna Verde</option>
                                        <option value="Curauma" {{ old('localidad') == 'Curauma' ? 'selected' : '' }}>Curauma</option>
                                        <option value="Playa Ancha" {{ old('localidad') == 'Playa Ancha' ? 'selected' : '' }}>Playa Ancha</option>
                                        <option value="Otro" {{ old('localidad') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                @error('localidad')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-3">
                            <div class="form-group">
                                <label for="tipo_ubicacion" class="form-label required">Tipo de Ubicación</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-home"></i>
                                    </span>
                                    <select id="tipo_ubicacion" name="tipo_ubicacion" class="form-select @error('tipo_ubicacion') is-invalid @enderror" required>
                                        <option value="">Seleccionar...</option>
                                        <option value="Domicilio" {{ old('tipo_ubicacion') == 'Domicilio' ? 'selected' : '' }}>Domicilio</option>
                                        <option value="Espacio Público" {{ old('tipo_ubicacion') == 'Espacio Público' ? 'selected' : '' }}>Espacio Público</option>
                                        <option value="Establecimiento" {{ old('tipo_ubicacion') == 'Establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                                        <option value="Otro" {{ old('tipo_ubicacion') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                @error('tipo_ubicacion')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-col-3">
                            <div class="form-group">
                                <label for="ubicacion" class="form-label required">Dirección/Ubicación</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" 
                                           id="ubicacion" 
                                           name="ubicacion" 
                                           class="form-control @error('ubicacion') is-invalid @enderror" 
                                           value="{{ old('ubicacion') }}" 
                                           placeholder="Dirección específica"
                                           required>
                                    <button type="button" class="btn btn-outline-info" id="btnSeleccionarUbicacion">
                                        <i class="fas fa-map"></i>
                                    </button>
                                </div>
                                @error('ubicacion')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i>
                                    Use el botón del mapa para seleccionar ubicación exacta
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sección: Documentos -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-paperclip"></i>Documentos
                    </h3>
                    
                    <div class="form-group">
                        <label for="imagen" class="form-label">Imagen de Referencia</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-image"></i>
                            </span>
                            <input type="file" 
                                   id="imagen" 
                                   name="imagen" 
                                   class="form-control @error('imagen') is-invalid @enderror" 
                                   accept="image/*">
                        </div>
                        @error('imagen')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>{{ $message }}
                            </div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i>
                            Opcional. Formatos: JPG, PNG, GIF. Máximo 5MB
                        </div>
                    </div>
                </div>
                
                <!-- Campos ocultos -->
                <input type="hidden" name="rut_ingreso" value="{{ session('user_id') }}">
                <input type="hidden" name="estado" value="Pendiente">
                <input type="hidden" name="etapa" value="Ingreso">
                <input type="hidden" name="fecha_inicio" value="{{ date('Y-m-d') }}">
                <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud') }}">
                <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud') }}">
                
                <!-- Acciones del Formulario -->
                <div class="form-actions">
                    <a href="{{ route('buscar.usuario') }}" class="form-btn form-btn-outline">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    
                    <button type="submit" class="form-btn form-btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> 
                        <span>Guardar Solicitud</span>
                        <div class="form-spinner" style="display: none;"></div>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para seleccionar ubicación en mapa -->
<div class="modal fade" id="mapaModal" tabindex="-1" aria-labelledby="mapaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapaModalLabel">
                    <i class="fas fa-map-marker-alt me-2"></i>Seleccionar ubicación en Valparaíso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="map-search-input" class="form-control" placeholder="Buscar dirección en Valparaíso...">
                        <button type="button" class="btn btn-primary" id="map-search-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="position-relative">
                    <div id="map-container" style="height: 400px; width: 100%; border-radius: 8px; border: 2px solid #e2e8f0;"></div>
                    <div class="loading-indicator" id="map-loading" style="display: none;">
                        <i class="fas fa-spinner fa-spin me-2"></i> Cargando...
                    </div>
                </div>
                <p class="mt-2 text-muted">
                    <small><i class="fas fa-info-circle me-1"></i>Mueva el mapa para seleccionar una ubicación exacta</small>
                </p>
                <div class="mt-3">
                    <p><strong>Dirección seleccionada:</strong> <span id="selected-address" class="text-primary">Ninguna</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btn-confirm-location">
                    <i class="fas fa-check"></i> Confirmar ubicación
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let marker;
    let mapaModal;
    let isMapInitialized = false;
    
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a elementos del formulario
        const form = document.getElementById('solicitudForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // Botón para abrir el modal
        const btnSeleccionarUbicacion = document.getElementById('btnSeleccionarUbicacion');
        const btnConfirmLocation = document.getElementById('btn-confirm-location');
        const ubicacionInput = document.getElementById('ubicacion');
        const localidadSelect = document.getElementById('localidad');
        const mapLoading = document.getElementById('map-loading');
        
        // Inicializar el modal de Bootstrap
        try {
            mapaModal = new bootstrap.Modal(document.getElementById('mapaModal'), {
                backdrop: 'static'
            });
        } catch (error) {
            console.error('Error al inicializar el modal:', error);
        }
        
        btnSeleccionarUbicacion.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Mostrar el modal
            mapaModal.show();
            
            // Inicializar mapa si aún no está inicializado
            if (!isMapInitialized) {
                mapLoading.style.display = 'block';
                
                // Pequeño retraso para asegurar que el modal esté completamente visible
                setTimeout(() => {
                    initMap();
                    isMapInitialized = true;
                    mapLoading.style.display = 'none';
                }, 500);
            } else {
                // Si ya está inicializado, sólo actualiza el tamaño
                if (map) {
                    setTimeout(() => {
                        map.invalidateSize();
                    }, 300);
                }
            }
        });
        
        // Buscar ubicación
        const searchBtn = document.getElementById('map-search-btn');
        const searchInput = document.getElementById('map-search-input');
        
        searchBtn.addEventListener('click', function() {
            const direccion = searchInput.value.trim();
            if (direccion) {
                mapLoading.style.display = 'block';
                buscarUbicacion(direccion);
            }
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const direccion = searchInput.value.trim();
                if (direccion) {
                    mapLoading.style.display = 'block';
                    buscarUbicacion(direccion);
                }
            }
        });
        
        // Confirmar ubicación
        btnConfirmLocation.addEventListener('click', function() {
            const selectedAddress = document.getElementById('selected-address').textContent;
            
            if (selectedAddress && selectedAddress !== 'Ninguna') {
                const center = map.getCenter();
                
                // Establecer valores en los campos ocultos
                document.getElementById('latitud').value = center.lat;
                document.getElementById('longitud').value = center.lng;
                
                // Establecer la dirección en el campo de texto
                ubicacionInput.value = selectedAddress;
                
                // Cerrar el modal
                mapaModal.hide();
            } else {
                alert('Por favor, seleccione una ubicación en el mapa.');
            }
        });
        
        // Si cambia la localidad, actualizar el centro del mapa
        localidadSelect.addEventListener('change', function() {
            if (map && isMapInitialized) {
                const localidad = this.value;
                if (localidad) {
                    switch(localidad) {
                        case 'Valparaíso':
                            map.setView([-33.047238, -71.612688], 13);
                            break;
                        case 'Placilla':
                            map.setView([-33.121419, -71.572622], 14);
                            break;
                        case 'Laguna Verde':
                            map.setView([-33.103230, -71.690090], 14);
                            break;
                        case 'Curauma':
                            map.setView([-33.140673, -71.559825], 14);
                            break;
                        case 'Playa Ancha':
                            map.setView([-33.030277, -71.635674], 14);
                            break;
                    }
                    // Actualizar la dirección después de mover el mapa
                    setTimeout(() => {
                        obtenerDireccionCentro();
                    }, 300);
                }
            }
        });

        // Formateador de RUT
        const rutInput = document.getElementById('rut_usuario');
        if(rutInput) {
            rutInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9kK]/g, '');
                
                if (value.length > 1) {
                    let cuerpo = value.slice(0, -1);
                    let dv = value.slice(-1);
                    
                    if (cuerpo.length > 3) {
                        cuerpo = cuerpo.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                    }
                    
                    value = cuerpo + '-' + dv;
                }
                
                e.target.value = value;
            });
        }
        
        // Validación en tiempo real
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    validateField(this);
                }
            });
        });

        function validateField(field) {
            if (field.hasAttribute('required') && !field.value.trim()) {
                field.classList.add('is-invalid');
                return false;
            } else if (field.type === 'email' && field.value && !isValidEmail(field.value)) {
                field.classList.add('is-invalid');
                return false;
            } else {
                field.classList.remove('is-invalid');
                if (field.value.trim()) {
                    field.classList.add('is-valid');
                }
                return true;
            }
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        // Manejar envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Enviando formulario de solicitud...');
            
            let isValid = true;
            
            // Validar campos requeridos
            const camposRequeridos = form.querySelectorAll('[required]');
            
            camposRequeridos.forEach(campo => {
                if (!validateField(campo)) {
                    isValid = false;
                }
            });

            console.log('Formulario válido:', isValid);

            if (isValid) {
                // Mostrar loading
                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Guardando...';
                submitBtn.querySelector('.form-spinner').style.display = 'inline-block';
                
                console.log('Enviando datos al servidor...');
                
                // Enviar formulario
                this.submit();
            } else {
                console.log('Formulario tiene errores');
                
                // Scroll al primer error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
        
        // Manejo de métodos de búsqueda
        const metodoDepartamento = document.getElementById('metodoDepartamento');
        const metodoRequerimiento = document.getElementById('metodoRequerimiento');
        const busquedaPorDepartamento = document.getElementById('busquedaPorDepartamento');
        const busquedaPorRequerimiento = document.getElementById('busquedaPorRequerimiento');
        const detallesRequerimiento = document.getElementById('detallesRequerimiento');
        const descripcionRequerimiento = document.getElementById('descripcion_requerimiento');
        const descripcionPrecio = document.getElementById('descripcion_precio');
        
        // Función para actualizar los detalles del requerimiento
        function actualizarDetallesRequerimiento(requerimientoId, element) {
            if (!requerimientoId) {
                detallesRequerimiento.style.display = 'none';
                return;
            }
            
            // Buscar la opción seleccionada
            const opcionSeleccionada = element.querySelector(`option[value="${requerimientoId}"]`);
            
            if (opcionSeleccionada) {
                const descripcion = opcionSeleccionada.getAttribute('data-descripcion');
                const precio = opcionSeleccionada.getAttribute('data-precio');
                
                if (descripcion || precio) {
                    descripcionRequerimiento.textContent = descripcion || 'No disponible';
                    descripcionPrecio.textContent = precio || 'No disponible';
                    detallesRequerimiento.style.display = 'block';
                } else {
                    detallesRequerimiento.style.display = 'none';
                }
            } else {
                detallesRequerimiento.style.display = 'none';
            }
        }
        
        // Función para cambiar entre métodos de búsqueda
        function cambiarMetodoBusqueda() {
            if (metodoDepartamento.checked) {
                busquedaPorDepartamento.style.display = 'block';
                busquedaPorRequerimiento.style.display = 'none';
                // Desactivar el select de búsqueda directa
                document.getElementById('requerimiento_id_directo').name = '';
                document.getElementById('requerimiento_id_dept').name = 'requerimiento_id';
                
                // Actualizar detalles basados en el selector de departamento
                const requerimientoId = document.getElementById('requerimiento_id_dept').value;
                actualizarDetallesRequerimiento(requerimientoId, document.getElementById('requerimiento_id_dept'));
            } else {
                busquedaPorDepartamento.style.display = 'none';
                busquedaPorRequerimiento.style.display = 'block';
                // Desactivar el select de búsqueda por departamento
                document.getElementById('requerimiento_id_dept').name = '';
                document.getElementById('requerimiento_id_directo').name = 'requerimiento_id';
                
                // Actualizar detalles basado en el selector directo
                const requerimientoId = document.getElementById('requerimiento_id_directo').value;
                actualizarDetallesRequerimiento(requerimientoId, document.getElementById('requerimiento_id_directo'));
            }
        }
        
        // Agregar eventos a los radios
        metodoDepartamento.addEventListener('change', cambiarMetodoBusqueda);
        metodoRequerimiento.addEventListener('change', cambiarMetodoBusqueda);
        
        // Filtrar requerimientos por departamento
        const departamentoSelect = document.getElementById('departamento_id');
        const requerimientoDeptSelect = document.getElementById('requerimiento_id_dept');
        
        departamentoSelect.addEventListener('change', function() {
            const departamentoId = this.value;
            
            // Obtener todas las opciones
            const opciones = requerimientoDeptSelect.querySelectorAll('option');
            
            // Mostrar solo el primer "Seleccionar requerimiento..."
            for (let i = 0; i < opciones.length; i++) {
                if (i === 0) {
                    opciones[i].style.display = '';
                    continue;
                }
                
                // Si no hay departamento seleccionado, mostrar todos
                if (!departamentoId) {
                    opciones[i].style.display = '';
                } else {
                    // Filtrar por departamento
                    const opcionDepartamentoId = opciones[i].getAttribute('data-departamento');
                    if (opcionDepartamentoId === departamentoId) {
                        opciones[i].style.display = '';
                    } else {
                        opciones[i].style.display = 'none';
                    }
                }
            }
            
            // Resetear el valor seleccionado
            requerimientoDeptSelect.value = '';
            
            // Ocultar los detalles al cambiar el departamento
            detallesRequerimiento.style.display = 'none';
        });
        
        // Actualizar detalles cuando cambia la selección del requerimiento (por departamento)
        requerimientoDeptSelect.addEventListener('change', function() {
            const requerimientoId = this.value;
            const requerimientoDirectoSelect = document.getElementById('requerimiento_id_directo');
            requerimientoDirectoSelect.value = requerimientoId;
            actualizarDetallesRequerimiento(requerimientoId, this);
        });
        
        // Sincronizar selección de requerimiento entre ambos selectores
        const requerimientoDirectoSelect = document.getElementById('requerimiento_id_directo');
        
        // Actualizar detalles cuando cambia la selección del requerimiento (directo)
        requerimientoDirectoSelect.addEventListener('change', function() {
            const requerimientoId = this.value;
            
            // Intentar encontrar esta opción en el selector por departamento
            const opciones = requerimientoDeptSelect.querySelectorAll('option');
            for (let i = 0; i < opciones.length; i++) {
                if (opciones[i].value === requerimientoId) {
                    // Si encontramos la opción, seleccionamos su departamento
                    const departamentoId = opciones[i].getAttribute('data-departamento');
                    if (departamentoId) {
                        departamentoSelect.value = departamentoId;
                        // Disparar evento para actualizar la visualización
                        departamentoSelect.dispatchEvent(new Event('change'));
                    }
                    requerimientoDeptSelect.value = requerimientoId;
                    break;
                }
            }
            
            actualizarDetallesRequerimiento(requerimientoId, this);
        });
        
        // Inicializar con el método seleccionado por defecto
        cambiarMetodoBusqueda();
        
        // Si hay un valor de requerimiento_id seleccionado (old), sincronizar ambos selectores
        const oldValue = "{{ old('requerimiento_id') }}";
        if (oldValue) {
            requerimientoDeptSelect.value = oldValue;
            requerimientoDirectoSelect.value = oldValue;
            
            // Intentar encontrar el departamento para este requerimiento
            const opcionSeleccionada = Array.from(requerimientoDeptSelect.options).find(option => option.value === oldValue);
            if (opcionSeleccionada) {
                const departamentoId = opcionSeleccionada.getAttribute('data-departamento');
                if (departamentoId) {
                    departamentoSelect.value = departamentoId;
                    departamentoSelect.dispatchEvent(new Event('change'));
                }
                
                // Actualizar detalles del requerimiento seleccionado
                const descripcion = opcionSeleccionada.getAttribute('data-descripcion');
                const precio = opcionSeleccionada.getAttribute('data-precio');
                
                if (descripcion || precio) {
                    descripcionRequerimiento.textContent = descripcion || 'No disponible';
                    descripcionPrecio.textContent = precio || 'No disponible';
                    detallesRequerimiento.style.display = 'block';
                }
            }
        }
    });
    
    function initMap() {
        // Centro del mapa (Valparaíso, Chile)
        const valparaiso = [-33.047238, -71.612688];
        
        // Crear el mapa
        map = L.map('map-container', {
            center: valparaiso,
            zoom: 14,
            zoomControl: true,
            scrollWheelZoom: true,
            doubleClickZoom: true
        });
        
        // Añadir capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Obtener la dirección en el centro del mapa cada vez que éste se mueva
        map.on('moveend', function() {
            obtenerDireccionCentro();
        });
        
        // Buscar ubicación inicial si ya hay una dirección en el campo
        const ubicacionInput = document.getElementById('ubicacion');
        if (ubicacionInput.value && ubicacionInput.value.trim() !== '') {
            buscarUbicacion(ubicacionInput.value);
        } else {
            // Obtener dirección inicial
            obtenerDireccionCentro();
        }
    }
    
    function obtenerDireccionCentro() {
        if (!map) return;
        
        const center = map.getCenter();
        const loading = document.getElementById('map-loading');
        loading.style.display = 'block';
        
        // Hacer una solicitud a Nominatim (servicio de geocodificación de OpenStreetMap)
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${center.lat}&lon=${center.lng}&zoom=18&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                
                if (data && data.display_name) {
                    // Verificar si la dirección está en Valparaíso
                    if (data.address && 
                        (data.address.city === 'Valparaíso' || 
                         data.address.county === 'Valparaíso' || 
                         data.display_name.includes('Valparaíso'))) {
                        
                        const address = data.display_name;
                        document.getElementById('selected-address').textContent = address;
                        
                        // Actualizar campos ocultos
                        document.getElementById('latitud').value = center.lat;
                        document.getElementById('longitud').value = center.lng;
                    } else {
                        document.getElementById('selected-address').textContent = 
                            "⚠️ La ubicación seleccionada no parece estar en Valparaíso. Por favor, seleccione una ubicación dentro de la comuna.";
                    }
                } else {
                    document.getElementById('selected-address').textContent = "No se pudo determinar la dirección";
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                console.error("Error al obtener dirección:", error);
                document.getElementById('selected-address').textContent = "Error al obtener la dirección";
            });
    }
    
    function buscarUbicacion(direccion) {
        if (!direccion || direccion.trim() === '') {
            alert('Por favor, ingrese una dirección para buscar');
            document.getElementById('map-loading').style.display = 'none';
            return;
        }
        
        // Añadir "Valparaíso, Chile" a la búsqueda si no se incluye
        if (!direccion.toLowerCase().includes('valparaíso') && !direccion.toLowerCase().includes('chile')) {
            direccion += ', Valparaíso, Chile';
        }
        
        // Hacer una solicitud a Nominatim para buscar la dirección
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}&limit=1&countrycodes=cl`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('map-loading').style.display = 'none';
                
                if (data && data.length > 0) {
                    const result = data[0];
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);
                    
                    // Mover el mapa
                    map.setView([lat, lng], 16);
                    
                    // La dirección se actualizará automáticamente con el evento moveend
                } else {
                    alert('No se encontraron resultados para la dirección ingresada');
                }
            })
            .catch(error => {
                document.getElementById('map-loading').style.display = 'none';
                console.error("Error al buscar dirección:", error);
                alert('Error al buscar la dirección');
            });
    }
</script>

<style>
/* Estilos específicos para formulario de creación de solicitudes */
.form-card-header {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px 8px 0 0;
}

.form-card-title {
    margin: 0;
    font-size: 1.3rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-header-actions .btn {
    background-color: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    transition: all 0.2s;
}

.form-header-actions .btn:hover {
    background-color: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
}

.form-alert-success {
    background-color: #ecfdf5;
    border-left: 4px solid #10b981;
    color: #065f46;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-success i {
    color: #10b981;
    font-size: 1.2rem;
    margin-top: 2px;
}

.form-alert-danger {
    background-color: #fef2f2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-danger i {
    color: #ef4444;
    font-size: 1.2rem;
    margin-top: 2px;
}

.form-alert-info {
    background-color: #ecfeff;
    border-left: 4px solid #06b6d4;
    color: #155e75;
    padding: 16px;
    margin-bottom: 24px;
    border-radius: 8px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.form-alert-info i {
    color: #06b6d4;
    font-size: 1.2rem;
    margin-top: 2px;
}

/* Destacar que es un formulario de creación de solicitudes */
.form-view-container .card {
    border-top: 4px solid #059669;
}

/* Estilos para radio buttons mejorados */
.form-check-input[type="radio"] {
    width: 1.2em;
    height: 1.2em;
    margin-top: 0.1em;
    cursor: pointer;
}

.form-check-label {
    cursor: pointer;
    display: flex;
    align-items: center;
    font-weight: 500;
    margin-left: 8px;
}

.form-radio {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    transition: all 0.2s;
}

.form-radio:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
}

.form-radio:has(input:checked) {
    background-color: rgba(5, 150, 105, 0.1);
    border-color: #059669;
}

/* Estilos para el mapa */
.leaflet-container {
    z-index: 1;
}

.loading-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: white;
    padding: 8px 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 1000;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
}

/* Mejorar visualización en móviles */
@media (max-width: 768px) {
    .form-card-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .form-header-actions {
        width: 100%;
        justify-content: center;
    }
    
    .form-radio {
        padding: 10px 12px;
    }
    
    #map-container {
        height: 300px !important;
    }
}
</style>
@endsection
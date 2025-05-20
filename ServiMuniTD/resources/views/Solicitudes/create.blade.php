@extends('layouts.app')

@section('title', 'Crear Solicitud - ServiMuni')

@section('page-title', 'Crear Nueva Solicitud')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<style>
    /* Estilos personalizados para el mapa */
    .leaflet-container {
        z-index: 1;
    }
    .map-marker-centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #2563eb;
        font-size: 2rem;
        z-index: 2;
    }
    .loading-indicator {
        display: none;
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: white;
        padding: 5px 10px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        z-index: 1000;
    }
</style>

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-clipboard-list me-2"></i>Crear Nueva Solicitud</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        @if($usuario)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Creando solicitud para: <strong>{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</strong> (RUT: {{ $usuario['rut'] }})
        </div>
        @endif
        
        <form method="POST" action="{{ route('solicitudes.store') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Si tenemos el usuario, lo enviamos como campo oculto -->
            @if($usuario)
            <input type="hidden" name="rut_usuario" value="{{ $usuario['rut'] }}">
            @else
            <!-- Si no tenemos usuario, mostramos campo para buscarlo -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="rut_usuario" class="form-label">RUT del Usuario</label>
                        <div class="input-group">
                            <input type="text" id="rut_usuario" name="rut_usuario" class="form-control @error('rut_usuario') is-invalid @enderror" 
                                placeholder="Ingrese RUT del usuario" value="{{ old('rut_usuario') }}" required>
                            <a href="{{ route('buscar.usuario') }}" class="btn btn-secondary">
                                <i class="fas fa-search"></i> Buscar
                            </a>
                        </div>
                        <small class="text-muted">Si no conoce el RUT, use el buscador de usuarios</small>
                        @error('rut_usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Selector de método de búsqueda de requerimiento -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>¿Cómo desea buscar el requerimiento?</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metodoBusqueda" id="metodoDepartamento" value="departamento" checked>
                                <label class="form-check-label" for="metodoDepartamento">
                                    <i class="fas fa-sitemap me-2"></i>Por Departamento
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="metodoBusqueda" id="metodoRequerimiento" value="requerimiento">
                                <label class="form-check-label" for="metodoRequerimiento">
                                    <i class="fas fa-list me-2"></i>Por Tipo de Requerimiento
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Búsqueda por departamento (visible inicialmente) -->
            <div class="row mb-4" id="busquedaPorDepartamento">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="departamento_id" class="form-label">Seleccione Departamento</label>
                        <select id="departamento_id" class="form-control">
                            <option value="">Todos los departamentos</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento['id'] }}" {{ old('departamento_id') == $departamento['id'] ? 'selected' : '' }}>
                                    {{ $departamento['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Al seleccionar un departamento, se filtrarán los tipos de requerimiento disponibles</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="requerimiento_id_dept" class="form-label">Tipo de Requerimiento</label>
                        <select id="requerimiento_id_dept" name="requerimiento_id" class="form-control @error('requerimiento_id') is-invalid @enderror" required>
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
                        @error('requerimiento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Búsqueda directa por requerimiento (inicialmente oculta) -->
            <div class="row mb-4" id="busquedaPorRequerimiento" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group mb-3">
                        <label for="requerimiento_id_directo" class="form-label">Seleccione Tipo de Requerimiento</label>
                        <select id="requerimiento_id_directo" class="form-control @error('requerimiento_id') is-invalid @enderror">
                            <option value="">Seleccionar requerimiento...</option>
                            @php
                                // Ordenar requerimientos alfabéticamente
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
                        @error('requerimiento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Detalles del requerimiento seleccionado (inicialmente oculto) -->
            <div class="card mb-4" id="detallesRequerimiento" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Detalles del Requerimiento Seleccionado</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Descripción del Requerimiento:</label>
                                <p id="descripcion_requerimiento" class="p-2 bg-light rounded">Seleccione un requerimiento para ver su descripción</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="fw-bold">Información de Precio:</label>
                                <p id="descripcion_precio" class="p-2 bg-light rounded">Seleccione un requerimiento para ver la información de precio</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="providencia" class="form-label">Número de Providencia</label>
                        <input type="number" id="providencia" name="providencia" class="form-control @error('providencia') is-invalid @enderror" 
                            value="{{ old('providencia') }}">
                        <small class="text-muted">Opcional</small>
                        @error('providencia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="descripcion" class="form-label">Descripción de la Solicitud</label>
                <textarea id="descripcion" name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" 
                    rows="4" required>{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="localidad" class="form-label">Localidad</label>
                        <select id="localidad" name="localidad" class="form-control @error('localidad') is-invalid @enderror" required>
                            <option value="">Seleccione una localidad...</option>
                            <option value="Valparaíso" {{ old('localidad') == 'Valparaíso' ? 'selected' : '' }}>Valparaíso</option>
                            <option value="Placilla" {{ old('localidad') == 'Placilla' ? 'selected' : '' }}>Placilla</option>
                            <option value="Laguna Verde" {{ old('localidad') == 'Laguna Verde' ? 'selected' : '' }}>Laguna Verde</option>
                            <option value="Curauma" {{ old('localidad') == 'Curauma' ? 'selected' : '' }}>Curauma</option>
                            <option value="Playa Ancha" {{ old('localidad') == 'Playa Ancha' ? 'selected' : '' }}>Playa Ancha</option>
                            <option value="Otro" {{ old('localidad') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('localidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="tipo_ubicacion" class="form-label">Tipo de Ubicación</label>
                        <select id="tipo_ubicacion" name="tipo_ubicacion" class="form-control @error('tipo_ubicacion') is-invalid @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="Domicilio" {{ old('tipo_ubicacion') == 'Domicilio' ? 'selected' : '' }}>Domicilio</option>
                            <option value="Espacio Público" {{ old('tipo_ubicacion') == 'Espacio Público' ? 'selected' : '' }}>Espacio Público</option>
                            <option value="Establecimiento" {{ old('tipo_ubicacion') == 'Establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                            <option value="Otro" {{ old('tipo_ubicacion') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="ubicacion" class="form-label">Dirección/Ubicación</label>
                        <div class="input-group">
                            <input type="text" id="ubicacion" name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror" 
                                value="{{ old('ubicacion') }}" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnSeleccionarUbicacion">
                                <i class="fas fa-map-marker-alt"></i> Seleccionar en mapa
                            </button>
                        </div>
                        <small class="text-muted">Ingrese la dirección o selecciónela en el mapa</small>
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="imagen" class="form-label">Imagen de Referencia</label>
                <input type="file" id="imagen" name="imagen" class="form-control @error('imagen') is-invalid @enderror" 
                    accept="image/*">
                <small class="text-muted">Opcional. Formatos permitidos: JPG, PNG, GIF. Máx. 5MB</small>
                @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Campos ocultos -->
            <input type="hidden" name="rut_ingreso" value="{{ session('user_id') }}">
            <input type="hidden" name="estado" value="Pendiente">
            <input type="hidden" name="etapa" value="Ingreso">
            <input type="hidden" name="fecha_inicio" value="{{ date('Y-m-d') }}">
            <input type="hidden" id="latitud" name="latitud" value="{{ old('latitud') }}">
            <input type="hidden" id="longitud" name="longitud" value="{{ old('longitud') }}">
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('buscar.usuario') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Solicitud
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para seleccionar ubicación en mapa -->
<div class="modal fade" id="mapaModal" tabindex="-1" aria-labelledby="mapaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapaModalLabel">Seleccionar ubicación en Valparaíso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="input-group">
                        <input type="text" id="map-search-input" class="form-control" placeholder="Buscar dirección en Valparaíso...">
                        <button type="button" class="btn btn-primary" id="map-search-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="position-relative">
                    <div id="map-container" style="height: 400px; width: 100%; border-radius: 8px;"></div>
                    <div class="loading-indicator" id="map-loading">
                        <i class="fas fa-spinner fa-spin me-2"></i> Cargando...
                    </div>
                </div>
                <p class="mt-2 text-muted"><small>Mueva el mapa para seleccionar una ubicación exacta</small></p>
                <div class="mt-3">
                    <p><strong>Dirección seleccionada:</strong> <span id="selected-address">Ninguna</span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-location">Confirmar ubicación</button>
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
        
        // Agregar un marcador en el centro (opcional)
        const centerMarkerIcon = L.divIcon({
            html: '<i class="fas fa-map-marker-alt"></i>',
            className: 'map-marker-centered',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
        
        // Añadir el control de localización
        L.control.locate({
            position: 'topright',
            strings: {
                title: "Mostrar mi ubicación",
                popup: "Estás dentro de {distance} metros de este punto"
            },
            locateOptions: {
                enableHighAccuracy: true
            }
        }).addTo(map);
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formateador de RUT (si existe el campo)
    const rutInput = document.getElementById('rut_usuario');
    if(rutInput) {
        rutInput.addEventListener('blur', function() {
            let rut = this.value.replace(/\./g, '').replace('-', '');
            if(rut.length > 1) {
                rut = rut.substring(0, rut.length - 1) + '-' + rut.charAt(rut.length - 1);
                this.value = rut;
            }
        });
    }
    
    // Manejo de métodos de búsqueda
    const metodoDepartamento = document.getElementById('metodoDepartamento');
    const metodoRequerimiento = document.getElementById('metodoRequerimiento');
    const busquedaPorDepartamento = document.getElementById('busquedaPorDepartamento');
    const busquedaPorRequerimiento = document.getElementById('busquedaPorRequerimiento');
    const detallesRequerimiento = document.getElementById('detallesRequerimiento');
    const descripcionRequerimiento = document.getElementById('descripcion_requerimiento');
    const descripcionPrecio = document.getElementById('descripcion_precio');
    
    // Función para actualizar los detalles del requerimiento
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
            busquedaPorDepartamento.style.display = 'flex';
            busquedaPorRequerimiento.style.display = 'none';
            // Desactivar el select de búsqueda directa
            document.getElementById('requerimiento_id_directo').name = '';
            document.getElementById('requerimiento_id_dept').name = 'requerimiento_id';
            
            // Actualizar detalles basados en el selector de departamento
            const requerimientoId = document.getElementById('requerimiento_id_dept').value;
            actualizarDetallesRequerimiento(requerimientoId, document.getElementById('requerimiento_id_dept'));
        } else {
            busquedaPorDepartamento.style.display = 'none';
            busquedaPorRequerimiento.style.display = 'flex';
            // Desactivar el select de búsqueda por departamento
            document.getElementById('requerimiento_id_dept').name = '';
            document.getElementById('requerimiento_id_directo').name = 'requerimiento_id';
            
            // Actualizar detalles basados en el selector directo
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
</script>
@endsection
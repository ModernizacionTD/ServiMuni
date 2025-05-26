@extends('layouts.app')

@section('title', 'Mapa de Solicitudes - ServiMuni')

@section('page-title', 'Mapa de Solicitudes')

@section('content')
<link rel="stylesheet" href="{{ asset('css/filtros.css') }}">
<link rel="stylesheet" href="{{ asset('css/button.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<style>
/* Estilos específicos para el mapa */
.mapa-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 24px;
}

.mapa-header {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.mapa-title {
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.estadisticas-mapa {
    display: flex;
    gap: 20px;
    align-items: center;
}

.contador-solicitudes {
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
}

.filtros-mapa {
    background: #f8f9fa;
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
}

.filtros-grid {
    display: grid;
    grid-template-columns: 2fr repeat(4, 1fr) auto auto;
    gap: 16px;
    align-items: end;
    margin-bottom: 16px;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.filter-input, .filter-select {
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    background: white;
}

.filter-input:focus, .filter-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.search-input-container {
    position: relative;
}

.search-input-container i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
}

.search-input-container input {
    padding-left: 40px;
}

.filtros-activos {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 32px;
    align-items: center;
}

.chip-filtro {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
    border: 1px solid rgba(59, 130, 246, 0.2);
    color: #1e40af;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.chip-remove {
    background: none;
    border: none;
    color: #1e40af;
    cursor: pointer;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.chip-remove:hover {
    background-color: rgba(59, 130, 246, 0.2);
}

#mapa {
    height: 70vh;
    min-height: 500px;
    width: 100%;
}

.leyenda-mapa {
    position: absolute;
    top: 10px;
    right: 10px;
    background: white;
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 200px;
}

.leyenda-title {
    font-weight: 600;
    margin-bottom: 12px;
    color: #374151;
    font-size: 0.9rem;
}

.leyenda-item {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 0.8rem;
}

.leyenda-color {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
}

.info-panel {
    background: white;
    border-radius: 8px;
    padding: 16px;
    margin-top: 20px;
    border: 1px solid #e2e8f0;
}

.info-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-item {
    text-align: center;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #3b82f6;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin-top: 4px;
}

.btn-toggle {
    background: #6b7280;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-toggle:hover {
    background: #4b5563;
    transform: translateY(-1px);
}

.btn-toggle.active {
    background: #3b82f6;
}

.btn-reset {
    background: #ef4444;
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-reset:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

/* Responsive */
@media (max-width: 1200px) {
    .filtros-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .filter-group {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .mapa-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .estadisticas-mapa {
        flex-direction: column;
        gap: 8px;
    }
    
    .filtros-mapa {
        padding: 16px;
    }
    
    #mapa {
        height: 60vh;
        min-height: 400px;
    }
    
    .leyenda-mapa {
        position: relative;
        margin-top: 16px;
        margin-bottom: 16px;
    }
    
    .info-stats {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Estilos para el popup del mapa */
.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.popup-content {
    max-width: 300px;
}

.popup-header {
    background: #3b82f6;
    color: white;
    padding: 12px 16px;
    margin: -9px -9px 12px -9px;
    border-radius: 8px 8px 0 0;
    font-weight: 600;
}

.popup-field {
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.popup-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
}

.popup-value {
    color: #6b7280;
    font-size: 0.85rem;
    text-align: right;
    max-width: 60%;
    word-wrap: break-word;
}

.popup-actions {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
}

.popup-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    font-size: 0.8rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}

.popup-btn-primary {
    background: #3b82f6;
    color: white;
}

.popup-btn-primary:hover {
    background: #2563eb;
}

.popup-btn-secondary {
    background: #6b7280;
    color: white;
}

.popup-btn-secondary:hover {
    background: #4b5563;
}

.status-badge-popup {
    font-size: 0.7rem;
    padding: 3px 8px;
    border-radius: 12px;
    font-weight: 500;
    color: white;
}

.status-en-curso { background: #f59e0b; }
.status-completado { background: #10b981; }
.status-derivada { background: #6b7280; }
.status-denegada { background: #ef4444; }
</style>

<div class="filter-view-container">
    <div class="mapa-container">
        <div class="mapa-header">
            <h1 class="mapa-title">
                <i class="fas fa-map-marked-alt"></i>
                Mapa de Solicitudes
            </h1>
            <div class="estadisticas-mapa">
                <div class="contador-solicitudes">
                    <span id="solicitudesVisibles">0</span> solicitudes mostradas
                </div>
                <div class="contador-solicitudes">
                    <span id="solicitudesTotal">{{ count($solicitudes) }}</span> total
                </div>
            </div>
        </div>
        
        <div class="filtros-mapa">
            <div class="filtros-grid">
                <!-- Búsqueda -->
                <div class="filter-group">
                    <label class="filter-label">Buscar</label>
                    <div class="search-input-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="busquedaInput" class="filter-input" placeholder="Buscar por ID, usuario, descripción...">
                    </div>
                </div>
                
                <!-- Estado -->
                <div class="filter-group">
                    <label class="filter-label">Estado</label>
                    <select id="estadoFilter" class="filter-select">
                        <option value="">Todos</option>
                        <option value="En curso">En curso</option>
                        <option value="Completado">Completado</option>
                        <option value="Derivada">Derivada</option>
                        <option value="Denegada">Denegada</option>
                    </select>
                </div>
                
                <!-- Localidad -->
                <div class="filter-group">
                    <label class="filter-label">Localidad</label>
                    <select id="localidadFilter" class="filter-select">
                        <option value="">Todas</option>
                        @foreach($localidades as $localidad)
                            <option value="{{ $localidad }}">{{ $localidad }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Departamento -->
                <div class="filter-group">
                    <label class="filter-label">Departamento</label>
                    <select id="departamentoFilter" class="filter-select">
                        <option value="">Todos</option>
                        @foreach($departamentos as $dept)
                            <option value="{{ $dept['id'] }}">{{ $dept['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Fecha -->
                <div class="filter-group">
                    <label class="filter-label">Fecha</label>
                    <select id="fechaFilter" class="filter-select">
                        <option value="">Todas</option>
                        <option value="hoy">Hoy</option>
                        <option value="semana">Esta semana</option>
                        <option value="mes">Este mes</option>
                        <option value="trimestre">Último trimestre</option>
                    </select>
                </div>
                
                <!-- Botón aplicar -->
                <button type="button" class="btn-toggle" id="aplicarFiltros">
                    <i class="fas fa-filter"></i> Aplicar
                </button>
                
                <!-- Botón reset -->
                <button type="button" class="btn-reset" id="resetFiltros">
                    <i class="fas fa-sync-alt"></i> Reset
                </button>
            </div>
            
            <!-- Chips de filtros activos -->
            <div class="filtros-activos" id="filtrosActivos"></div>
        </div>
        
        <div style="position: relative;">
            <div id="mapa"></div>
            
            <!-- Leyenda -->
            <div class="leyenda-mapa">
                <div class="leyenda-title">
                    <i class="fas fa-list"></i> Leyenda
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-color" style="background: #f59e0b;"></div>
                    <span>En curso</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-color" style="background: #10b981;"></div>
                    <span>Completado</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-color" style="background: #6b7280;"></div>
                    <span>Derivada</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-color" style="background: #ef4444;"></div>
                    <span>Denegada</span>
                </div>
                <div class="leyenda-item">
                    <div class="leyenda-color" style="background: #8b5cf6;"></div>
                    <span>Vencida</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Panel de información -->
    <div class="info-panel">
        <div class="info-stats">
            <div class="stat-item">
                <div class="stat-number" id="totalSolicitudes">{{ count($solicitudes) }}</div>
                <div class="stat-label">Total Solicitudes</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="enCursoCount">0</div>
                <div class="stat-label">En Curso</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="completadasCount">0</div>
                <div class="stat-label">Completadas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="derivadasCount">0</div>
                <div class="stat-label">Derivadas</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="vencidasCount">0</div>
                <div class="stat-label">Vencidas</div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de solicitudes desde Laravel
    const solicitudesData = @json($solicitudes);
    const requerimientosData = @json($requerimientos);
    const departamentosData = @json($departamentos);
    const usuariosData = @json($usuarios);
    
    // Variables globales
    let mapa;
    let marcadores = [];
    let solicitudesFiltradas = [...solicitudesData];
    
    // Inicializar mapa
    function inicializarMapa() {
        // Crear mapa centrado en Chile
        mapa = L.map('mapa').setView([-33.4489, -70.6693], 6);
        
        // Añadir capa de mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapa);
        
        // Cargar todas las solicitudes inicialmente
        cargarMarcadores();
    }
    
    // Función para geocodificar direcciones (simulada)
    function geocodificarDireccion(direccion, localidad) {
        // Coordenadas por defecto para diferentes localidades de Chile
        const coordenadasLocalidades = {
            'Santiago': [-33.4489, -70.6693],
            'Valparaíso': [-33.0458, -71.6197],
            'Concepción': [-36.8201, -73.0444],
            'La Serena': [-29.9027, -71.2519],
            'Antofagasta': [-23.6509, -70.3975],
            'Temuco': [-38.7359, -72.5904],
            'Rancagua': [-34.1708, -70.7394],
            'Talca': [-35.4264, -71.6554],
            'Iquique': [-20.2141, -70.1522],
            'Puerto Montt': [-41.4693, -72.9424],
            'Punta Arenas': [-53.1638, -70.9171],
            'Calama': [-22.4558, -68.9203],
            'Osorno': [-40.5706, -73.1344],
            'Quillota': [-32.8836, -71.2394],
            'Valdivia': [-39.8142, -73.2459]
        };
        
        // Obtener coordenada base de la localidad
        let coordBase = coordenadasLocalidades[localidad] || coordenadasLocalidades['Santiago'];
        
        // Añadir variación aleatoria para simular direcciones específicas
        const variacion = 0.02; // ~2km de variación
        const lat = coordBase[0] + (Math.random() - 0.5) * variacion;
        const lng = coordBase[1] + (Math.random() - 0.5) * variacion;
        
        return [lat, lng];
    }
    
    // Función para obtener color según estado
    function obtenerColorEstado(solicitud) {
        const estado = solicitud.estado;
        
        // Verificar si está vencida
        if (solicitud.fecha_estimada_op && estado !== 'Completado') {
            const fechaEstimada = new Date(solicitud.fecha_estimada_op);
            const hoy = new Date();
            if (fechaEstimada < hoy) {
                return '#8b5cf6'; // Púrpura para vencidas
            }
        }
        
        switch (estado) {
            case 'En curso':
                return '#f59e0b'; // Amarillo
            case 'Completado':
                return '#10b981'; // Verde
            case 'Derivada':
                return '#6b7280'; // Gris
            case 'Denegada':
                return '#ef4444'; // Rojo
            default:
                return '#3b82f6'; // Azul por defecto
        }
    }
    
    // Función para crear popup de marcador
    function crearPopup(solicitud) {
        const usuario = usuariosData[solicitud.rut_usuario] || {};
        const requerimiento = requerimientosData[solicitud.requerimiento_id] || {};
        const departamento = departamentosData[requerimiento.departamento_id] || {};
        
        const fechaFormateada = solicitud.fecha_ingreso ? 
            new Date(solicitud.fecha_ingreso).toLocaleDateString('es-ES') : 'Sin fecha';
        
        return `
            <div class="popup-content">
                <div class="popup-header">
                    Solicitud #${solicitud.id_solicitud}
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Usuario:</span>
                    <span class="popup-value">${usuario.nombre || 'N/A'} ${usuario.apellidos || ''}</span>
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Departamento:</span>
                    <span class="popup-value">${departamento.nombre || 'N/A'}</span>
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Estado:</span>
                    <span class="popup-value">
                        <span class="status-badge-popup status-${solicitud.estado?.toLowerCase().replace(' ', '-')}">${solicitud.estado || 'N/A'}</span>
                    </span>
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Fecha:</span>
                    <span class="popup-value">${fechaFormateada}</span>
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Localidad:</span>
                    <span class="popup-value">${solicitud.localidad || 'N/A'}</span>
                </div>
                
                <div class="popup-field">
                    <span class="popup-label">Descripción:</span>
                    <span class="popup-value">${(solicitud.descripcion || 'Sin descripción').substring(0, 100)}${solicitud.descripcion?.length > 100 ? '...' : ''}</span>
                </div>
                
                <div class="popup-actions">
                    <a href="/solicitudes/${solicitud.id_solicitud}" class="popup-btn popup-btn-primary">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <a href="/solicitudes/${solicitud.id_solicitud}/editar" class="popup-btn popup-btn-secondary">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                </div>
            </div>
        `;
    }
    
    // Función para cargar marcadores
    function cargarMarcadores() {
        // Limpiar marcadores existentes
        marcadores.forEach(marcador => mapa.removeLayer(marcador));
        marcadores = [];
        
        // Crear marcadores para solicitudes filtradas
        solicitudesFiltradas.forEach(solicitud => {
            const coordenadas = geocodificarDireccion(solicitud.ubicacion, solicitud.localidad);
            const color = obtenerColorEstado(solicitud);
            
            // Crear icono personalizado
            const icono = L.divIcon({
                className: 'custom-marker',
                html: `<div style="
                    width: 20px;
                    height: 20px;
                    background-color: ${color};
                    border: 3px solid white;
                    border-radius: 50%;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 10px;
                    color: white;
                    font-weight: bold;
                ">${solicitud.id_solicitud}</div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });
            
            // Crear marcador
            const marcador = L.marker(coordenadas, { icon: icono })
                .bindPopup(crearPopup(solicitud))
                .addTo(mapa);
            
            marcadores.push(marcador);
        });
        
        // Ajustar vista del mapa si hay marcadores
        if (marcadores.length > 0) {
            const grupo = new L.featureGroup(marcadores);
            mapa.fitBounds(grupo.getBounds().pad(0.1));
        }
        
        // Actualizar estadísticas
        actualizarEstadisticas();
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const busqueda = document.getElementById('busquedaInput').value.toLowerCase();
        const estado = document.getElementById('estadoFilter').value;
        const localidad = document.getElementById('localidadFilter').value;
        const departamento = document.getElementById('departamentoFilter').value;
        const fecha = document.getElementById('fechaFilter').value;
        
        solicitudesFiltradas = solicitudesData.filter(solicitud => {
            // Filtro de búsqueda
            if (busqueda) {
                const texto = `${solicitud.id_solicitud} ${solicitud.descripcion} ${solicitud.rut_usuario}`.toLowerCase();
                if (!texto.includes(busqueda)) return false;
            }
            
            // Filtro de estado
            if (estado && solicitud.estado !== estado) return false;
            
            // Filtro de localidad
            if (localidad && solicitud.localidad !== localidad) return false;
            
            // Filtro de departamento
            if (departamento) {
                const requerimiento = requerimientosData[solicitud.requerimiento_id];
                if (!requerimiento || requerimiento.departamento_id != departamento) return false;
            }
            
            // Filtro de fecha
            if (fecha && solicitud.fecha_ingreso) {
                const fechaSolicitud = new Date(solicitud.fecha_ingreso);
                const hoy = new Date();
                
                switch (fecha) {
                    case 'hoy':
                        if (fechaSolicitud.toDateString() !== hoy.toDateString()) return false;
                        break;
                    case 'semana':
                        const semanaAtras = new Date(hoy.getTime() - 7 * 24 * 60 * 60 * 1000);
                        if (fechaSolicitud < semanaAtras) return false;
                        break;
                    case 'mes':
                        const mesAtras = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate());
                        if (fechaSolicitud < mesAtras) return false;
                        break;
                    case 'trimestre':
                        const trimestreAtras = new Date(hoy.getFullYear(), hoy.getMonth() - 3, hoy.getDate());
                        if (fechaSolicitud < trimestreAtras) return false;
                        break;
                }
            }
            
            return true;
        });
        
        cargarMarcadores();
        actualizarChipsFiltros();
    }
    
    // Función para actualizar estadísticas
    function actualizarEstadisticas() {
        document.getElementById('solicitudesVisibles').textContent = solicitudesFiltradas.length;
        
        const estadisticas = {
            enCurso: 0,
            completadas: 0,
            derivadas: 0,
            vencidas: 0
        };
        
        solicitudesFiltradas.forEach(solicitud => {
            // Verificar si está vencida
            if (solicitud.fecha_estimada_op && solicitud.estado !== 'Completado') {
                const fechaEstimada = new Date(solicitud.fecha_estimada_op);
                const hoy = new Date();
                if (fechaEstimada < hoy) {
                    estadisticas.vencidas++;
                    return;
                }
            }
            
            switch (solicitud.estado) {
                case 'En curso':
                    estadisticas.enCurso++;
                    break;
                case 'Completado':
                    estadisticas.completadas++;
                    break;
                case 'Derivada':
                    estadisticas.derivadas++;
                    break;
            }
        });
        
        document.getElementById('enCursoCount').textContent = estadisticas.enCurso;
        document.getElementById('completadasCount').textContent = estadisticas.completadas;
        document.getElementById('derivadasCount').textContent = estadisticas.derivadas;
        document.getElementById('vencidasCount').textContent = estadisticas.vencidas;
    }
    
    // Función para actualizar chips de filtros
    function actualizarChipsFiltros() {
        const container = document.getElementById('filtrosActivos');
        container.innerHTML = '';
        
        const busqueda = document.getElementById('busquedaInput').value;
        const estado = document.getElementById('estadoFilter').value;
        const localidad = document.getElementById('localidadFilter').value;
        const departamento = document.getElementById('departamentoFilter').value;
        const fecha = document.getElementById('fechaFilter').value;
        
        // Chip de búsqueda
        if (busqueda.trim()) {
            agregarChip(container, 'Búsqueda', `"${busqueda}"`, () => {
                document.getElementById('busquedaInput').value = '';
                aplicarFiltros();
            });
        }
        
        // Chip de estado
        if (estado) {
            const select = document.getElementById('estadoFilter');
            const texto = select.options[select.selectedIndex].text;
            agregarChip(container, 'Estado', texto, () => {
                select.value = '';
                aplicarFiltros();
            });
        }
        
        // Chip de localidad
        if (localidad) {
            agregarChip(container, 'Localidad', localidad, () => {
                document.getElementById('localidadFilter').value = '';
                aplicarFiltros();
            });
        }
        
        // Chip de departamento
        if (departamento) {
            const select = document.getElementById('departamentoFilter');
            const texto = select.options[select.selectedIndex].text;
            agregarChip(container, 'Departamento', texto, () => {
                select.value = '';
                aplicarFiltros();
            });
        }
        
        // Chip de fecha
        if (fecha) {
            const select = document.getElementById('fechaFilter');
            const texto = select.options[select.selectedIndex].text;
            agregarChip(container, 'Fecha', texto, () => {
                select.value = '';
                aplicarFiltros();
            });
        }
    }
    
    // Función para agregar chip
    function agregarChip(container, tipo, valor, onRemove) {
        const chip = document.createElement('div');
        chip.className = 'chip-filtro';
        chip.innerHTML = `
            <span>${tipo}: ${valor}</span>
            <button type="button" class="chip-remove">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        chip.querySelector('.chip-remove').addEventListener('click', onRemove);
        container.appendChild(chip);
    }
    
    // Función para resetear filtros
    function resetearFiltros() {
        document.getElementById('busquedaInput').value = '';
        document.getElementById('estadoFilter').value = '';
        document.getElementById('localidadFilter').value = '';
        document.getElementById('departamentoFilter').value = '';
        document.getElementById('fechaFilter').value = '';
        
        solicitudesFiltradas = [...solicitudesData];
        cargarMarcadores();
        actualizarChipsFiltros();
    }
    
    // Event listeners
    document.getElementById('aplicarFiltros').addEventListener('click', aplicarFiltros);
    document.getElementById('resetFiltros').addEventListener('click', resetearFiltros);
    
    // Aplicar filtros automáticamente al cambiar inputs
    document.getElementById('busquedaInput').addEventListener('input', debounce(aplicarFiltros, 500));
    document.getElementById('estadoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('localidadFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('departamentoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('fechaFilter').addEventListener('change', aplicarFiltros);
    
    // Función debounce para búsqueda
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Inicializar todo
    inicializarMapa();
    actualizarEstadisticas();
    
    // Auto-refresh cada 60 segundos
    setInterval(() => {
        // Aquí podrías hacer una llamada AJAX para obtener datos actualizados
        console.log('Auto-refresh del mapa (implementar llamada AJAX)');
    }, 60000);
});

// Función para mostrar/ocultar leyenda en móviles
function toggleLeyenda() {
    const leyenda = document.querySelector('.leyenda-mapa');
    leyenda.style.display = leyenda.style.display === 'none' ? 'block' : 'none';
}

// Event listener para responsive
window.addEventListener('resize', () => {
    if (mapa) {
        setTimeout(() => {
            mapa.invalidateSize();
        }, 100);
    }
});
</script>
@endsection
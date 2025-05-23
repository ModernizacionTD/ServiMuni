@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Sistema de Gestión')

@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="table-view-container filter-view-container">
    <link rel="stylesheet" href="{{ asset('css/tabla.css') }}">
    <link rel="stylesheet" href="{{ asset('css/filtros.css') }}">

    <div class="card">
        <div class="card-header filter-card-header">
            <h3 class="card-title filter-card-title">
                <i class="fas fa-users me-2"></i>Lista de Usuarios
            </h3>
            <a href="{{ route('usuarios.create') }}" class="btn btn-add filter-add-btn">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>
        
        <!-- Barra de filtros horizontal -->
        <div class="filters-bar">
            <div class="filters-container">
                <!-- Búsqueda -->
                <div class="filter-item search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="searchInput" class="form-control filter-search-input" placeholder="Buscar usuario...">
                    </div>
                </div>
                
                <!-- Filtro por tipo de persona -->
                <div class="filter-item">
                    <select id="tipoPersonaFilter" class="form-select filter-select">
                        <option value="">Todos los tipos</option>
                        <option value="Natural">Persona Natural</option>
                        <option value="Jurídica">Persona Jurídica</option>
                    </select>
                </div>
                
                <!-- Filtro por género -->
                <div class="filter-item">
                    <select id="generoFilter" class="form-select filter-select">
                        <option value="">Todos los géneros</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Transmasculino">Transmasculino</option>
                        <option value="Transfemenino">Transfemenino</option>
                        <option value="No decir">Prefiero no decir</option>
                    </select>
                </div>
                
                <!-- Filtro por nombre social -->
                <div class="filter-item">
                    <select id="nombreSocialFilter" class="form-select filter-select">
                        <option value="">Nombre social</option>
                        <option value="Sí">Usa nombre social</option>
                        <option value="No">No usa nombre social</option>
                    </select>
                </div>
                
                <!-- Filtro por rango de edad -->
                <div class="filter-item">
                    <select id="rangoEdadFilter" class="form-select filter-select">
                        <option value="">Filtrar por edad</option>
                        <option value="0-17">Menor de edad (0-17)</option>
                        <option value="18-30">Joven adulto (18-30)</option>
                        <option value="31-50">Adulto (31-50)</option>
                        <option value="51-65">Adulto mayor (51-65)</option>
                        <option value="66-150">Tercera edad (66+)</option>
                    </select>
                </div>
                
                <!-- Filtro por dominio de email -->
                <div class="filter-item">
                    <select id="dominioEmailFilter" class="form-select filter-select">
                        <option value="">Filtrar por dominio</option>
                        <!-- Se llenarán dinámicamente -->
                    </select>
                </div>
                
                <!-- Botón reset -->
                <div class="filter-item">
                    <button type="button" class="btn btn-outline-secondary filter-reset-btn" id="clearFiltersBtn">
                        <i class="fas fa-sync-alt"></i> Restablecer filtros
                    </button>
                </div>
            </div>
            
            <!-- Chips de filtros activos -->
            <div class="active-filters-chips" id="activeFiltersContainer"></div>
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
                <table id="usuariosTable" class="table data-table">
                    <thead>
                        <tr>
                            <th width="12%">RUT</th>
                            <th width="10%">Persona</th>
                            <th width="15%">Nombre</th>
                            <th width="15%">Apellidos</th>
                            <th width="18%">Email</th>
                            <th width="12%">Teléfono</th>
                            <th width="15%">Dirección</th>
                            <th width="13%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr class="user-row" 
                                data-tipo-persona="{{ $usuario['tipo_persona'] }}"
                                data-genero="{{ $usuario['genero'] ?? '' }}"
                                data-uso-ns="{{ $usuario['uso_ns'] ?? 'No' }}"
                                data-fecha-nacimiento="{{ $usuario['fecha_nacimiento'] ?? '' }}"
                                data-email-domain="{{ $usuario['email'] ? explode('@', $usuario['email'])[1] ?? '' : '' }}">
                                <td><strong>{{ $usuario['rut'] }}</strong></td>
                                <td>
                                    <span class="persona-badge {{ strtolower($usuario['tipo_persona']) == 'natural' ? 'persona-natural' : 'persona-juridica' }}">
                                        {{ $usuario['tipo_persona'] }}
                                    </span>
                                </td>
                                <td>{{ $usuario['nombre'] }}</td>
                                <td>{{ $usuario['apellidos'] }}</td>
                                <td>
                                    <a href="mailto:{{ $usuario['email'] }}" class="contact-link">
                                        <i class="fas fa-envelope"></i>
                                        {{ $usuario['email'] }}
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:{{ $usuario['telefono'] }}" class="contact-link">
                                        <i class="fas fa-phone"></i>
                                        {{ $usuario['telefono'] }}
                                    </a>
                                </td>
                                <td class="address-cell">{{ $usuario['direccion'] }}</td>
                                <td>
                                    <div class="table-actions">
                                        <button type="button" class="btn btn-sm btn-info action-btn btn-view view-details" title="Ver detalles" 
                                            data-rut="{{ $usuario['rut'] }}"
                                            data-nombre="{{ $usuario['nombre'] }}"
                                            data-apellidos="{{ $usuario['apellidos'] }}"
                                            data-tipo="{{ $usuario['tipo_persona'] }}"
                                            data-uso-ns="{{ $usuario['uso_ns'] ?? 'No' }}"
                                            data-nombre-social="{{ $usuario['nombre_social'] ?? 'N/A' }}"
                                            data-nacimiento="{{ $usuario['fecha_nacimiento'] ?? '' }}"
                                            data-genero="{{ $usuario['genero'] ?? 'N/A' }}"
                                            data-telefono="{{ $usuario['telefono'] }}"
                                            data-telefono2="{{ $usuario['telefono_2'] ?? 'N/A' }}"
                                            data-email="{{ $usuario['email'] }}"
                                            data-email2="{{ $usuario['email_2'] ?? 'N/A' }}"
                                            data-direccion="{{ $usuario['direccion'] }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('usuarios.edit', $usuario['rut']) }}" class="btn btn-sm btn-primary action-btn btn-edit" title="Editar usuario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-danger action-btn btn-delete" title="Eliminar" 
                                            onclick="confirmarEliminacion('{{ $usuario['rut'] }}', '{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $usuario['rut'] }}" action="{{ route('usuarios.destroy', $usuario['rut']) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="table-empty-state">
                                        <i class="fas fa-users"></i>
                                        <p class="table-empty-state-text">No hay usuarios registrados en el sistema</p>
                                        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Añadir Usuario
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="table-pagination">
                <div class="pagination-info">
                    Mostrando <span class="fw-bold" id="resultCount">{{ count($usuarios) }}</span> de <span class="fw-bold">{{ count($usuarios) }}</span> usuarios
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles del usuario -->
    <div class="user-details-panel details-panel" id="userDetailsPanel">
        <div class="user-details-modal">
            <div class="user-details-header">
                <h3><i class="fas fa-user"></i> Detalles del Usuario</h3>
                <button type="button" class="detail-close-btn" id="closePanelBtn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="user-profile-header">
                <div class="user-avatar">
                    <span id="userInitials"></span>
                </div>
                <div class="user-info">
                    <h3 id="userName"></h3>
                    <p id="userType" class="user-type"></p>
                </div>
            </div>
            
            <div class="user-details-container">
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-id-card"></i> Información Personal</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">RUT</span>
                            <span class="detail-value" id="userRut"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Fecha de Nacimiento</span>
                            <span class="detail-value" id="userBirthdate"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Género</span>
                            <span class="detail-value" id="userGender"></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Uso de Nombre Social</span>
                            <span class="detail-value" id="userUseNS"></span>
                        </div>
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Nombre Social</span>
                            <span class="detail-value" id="userSocialName"></span>
                        </div>
                    </div>
                </div>
                
                <div class="details-section">
                    <h4 class="section-title"><i class="fas fa-phone-alt"></i> Información de Contacto</h4>
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Email Principal</span>
                            <a class="detail-value contact-link" id="userEmail"></a>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email Alternativo</span>
                            <a class="detail-value contact-link" id="userEmail2"></a>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Teléfono Principal</span>
                            <a class="detail-value contact-link" id="userPhone"></a>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Teléfono Alternativo</span>
                            <a class="detail-value contact-link" id="userPhone2"></a>
                        </div>
                        <div class="detail-item detail-full-width">
                            <span class="detail-label">Dirección</span>
                            <span class="detail-value" id="userAddress"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel-actions">
                <button type="button" class="btn btn-secondary" id="closePanelBtn2">
                    <i class="fas fa-times"></i> Cerrar
                </button>
                <a href="#" id="editUserBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Usuario
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para calcular la edad
    function calcularEdad(fechaNacimiento) {
        if (!fechaNacimiento) return null;
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const diferenciaMeses = hoy.getMonth() - nacimiento.getMonth();
        
        if (diferenciaMeses < 0 || (diferenciaMeses === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        
        return edad;
    }
    
    // Función para verificar si la edad está en el rango
    function edadEnRango(edad, rango) {
        if (edad === null) return false;
        
        const [min, max] = rango.split('-').map(Number);
        return edad >= min && edad <= max;
    }
    
    // Llenar dinámicamente el filtro de dominios de email
    function llenarDominiosEmail() {
        const rows = document.querySelectorAll('.user-row');
        const dominios = new Set();
        
        rows.forEach(row => {
            const dominio = row.getAttribute('data-email-domain');
            if (dominio && dominio.trim()) {
                dominios.add(dominio);
            }
        });
        
        const dominioSelect = document.getElementById('dominioEmailFilter');
        dominios.forEach(dominio => {
            const option = document.createElement('option');
            option.value = dominio;
            option.textContent = `@${dominio}`;
            dominioSelect.appendChild(option);
        });
    }
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const tipoPersona = document.getElementById('tipoPersonaFilter').value;
        const genero = document.getElementById('generoFilter').value;
        const nombreSocial = document.getElementById('nombreSocialFilter').value;
        const rangoEdad = document.getElementById('rangoEdadFilter').value;
        const dominioEmail = document.getElementById('dominioEmailFilter').value;
        
        const rows = document.querySelectorAll('.user-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            let mostrar = true;
            
            // Filtro de búsqueda general
            if (searchValue) {
                const texto = row.textContent.toLowerCase();
                if (!texto.includes(searchValue)) {
                    mostrar = false;
                }
            }
            
            // Filtro por tipo de persona
            if (tipoPersona && row.getAttribute('data-tipo-persona') !== tipoPersona) {
                mostrar = false;
            }
            
            // Filtro por género
            if (genero && row.getAttribute('data-genero') !== genero) {
                mostrar = false;
            }
            
            // Filtro por uso de nombre social
            if (nombreSocial && row.getAttribute('data-uso-ns') !== nombreSocial) {
                mostrar = false;
            }
            
            // Filtro por rango de edad
            if (rangoEdad) {
                const fechaNacimiento = row.getAttribute('data-fecha-nacimiento');
                const edad = calcularEdad(fechaNacimiento);
                if (!edadEnRango(edad, rangoEdad)) {
                    mostrar = false;
                }
            }
            
            // Filtro por dominio de email
            if (dominioEmail && row.getAttribute('data-email-domain') !== dominioEmail) {
                mostrar = false;
            }
            
            // Mostrar u ocultar fila
            row.style.display = mostrar ? '' : 'none';
            if (mostrar) visibleCount++;
        });
        
        // Actualizar contador
        document.getElementById('resultCount').textContent = visibleCount;
        
        // Actualizar filtros activos
        actualizarFiltrosActivos();
    }
    
    // Función para actualizar los chips de filtros activos
    function actualizarFiltrosActivos() {
        const container = document.getElementById('activeFiltersContainer');
        container.innerHTML = '';
        
        // Búsqueda general
        const searchValue = document.getElementById('searchInput').value;
        if (searchValue.trim()) {
            agregarChipFiltro(container, 'Búsqueda', `"${searchValue}"`, () => {
                document.getElementById('searchInput').value = '';
                aplicarFiltros();
            });
        }
        
        // Tipo de persona
        const tipoPersona = document.getElementById('tipoPersonaFilter');
        if (tipoPersona.value) {
            agregarChipFiltro(container, 'Tipo', tipoPersona.options[tipoPersona.selectedIndex].text, () => {
                tipoPersona.value = '';
                aplicarFiltros();
            });
        }
        
        // Género
        const genero = document.getElementById('generoFilter');
        if (genero.value) {
            agregarChipFiltro(container, 'Género', genero.options[genero.selectedIndex].text, () => {
                genero.value = '';
                aplicarFiltros();
            });
        }
        
        // Nombre social
        const nombreSocial = document.getElementById('nombreSocialFilter');
        if (nombreSocial.value) {
            agregarChipFiltro(container, 'Nombre Social', nombreSocial.options[nombreSocial.selectedIndex].text, () => {
                nombreSocial.value = '';
                aplicarFiltros();
            });
        }
        
        // Rango de edad
        const rangoEdad = document.getElementById('rangoEdadFilter');
        if (rangoEdad.value) {
            agregarChipFiltro(container, 'Edad', rangoEdad.options[rangoEdad.selectedIndex].text, () => {
                rangoEdad.value = '';
                aplicarFiltros();
            });
        }
        
        // Dominio de email
        const dominioEmail = document.getElementById('dominioEmailFilter');
        if (dominioEmail.value) {
            agregarChipFiltro(container, 'Email', dominioEmail.options[dominioEmail.selectedIndex].text, () => {
                dominioEmail.value = '';
                aplicarFiltros();
            });
        }
    }
    
    // Función para agregar un chip de filtro activo
    function agregarChipFiltro(container, tipo, valor, onRemove) {
        const chip = document.createElement('div');
        chip.className = 'filter-chip';
        chip.innerHTML = `
            <span>${tipo}: ${valor}</span>
            <button type="button" class="filter-chip-remove">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        chip.querySelector('.filter-chip-remove').addEventListener('click', onRemove);
        container.appendChild(chip);
    }
    
    // Event listeners para los filtros
    document.getElementById('searchInput').addEventListener('input', aplicarFiltros);
    document.getElementById('tipoPersonaFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('generoFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('nombreSocialFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('rangoEdadFilter').addEventListener('change', aplicarFiltros);
    document.getElementById('dominioEmailFilter').addEventListener('change', aplicarFiltros);
    
    // Botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('tipoPersonaFilter').value = '';
        document.getElementById('generoFilter').value = '';
        document.getElementById('nombreSocialFilter').value = '';
        document.getElementById('rangoEdadFilter').value = '';
        document.getElementById('dominioEmailFilter').value = '';
        aplicarFiltros();
    });
    
    // Inicializar
    llenarDominiosEmail();
    
    // ===== FUNCIONALIDAD DEL MODAL DE DETALLES =====
    const userDetailsPanel = document.getElementById('userDetailsPanel');
    const viewButtons = document.querySelectorAll('.view-details');
    const closePanelBtn = document.getElementById('closePanelBtn');
    const closePanelBtn2 = document.getElementById('closePanelBtn2');
    
    function showUserDetails() {
        userDetailsPanel.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function hideUserDetails() {
        userDetailsPanel.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // Mostrar panel de detalles al hacer clic en el ojo
    viewButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Extraer datos del usuario
            const userData = {
                rut: this.getAttribute('data-rut'),
                nombre: this.getAttribute('data-nombre'),
                apellidos: this.getAttribute('data-apellidos'),
                tipo: this.getAttribute('data-tipo'),
                usoNS: this.getAttribute('data-uso-ns'),
                nombreSocial: this.getAttribute('data-nombre-social'),
                nacimiento: this.getAttribute('data-nacimiento'),
                genero: this.getAttribute('data-genero'),
                telefono: this.getAttribute('data-telefono'),
                telefono2: this.getAttribute('data-telefono2'),
                email: this.getAttribute('data-email'),
                email2: this.getAttribute('data-email2'),
                direccion: this.getAttribute('data-direccion')
            };
            
            // Llenar datos en el panel
            document.getElementById('userInitials').textContent = obtenerIniciales(userData.nombre, userData.apellidos);
            document.getElementById('userName').textContent = `${userData.nombre} ${userData.apellidos}`;
            document.getElementById('userType').textContent = userData.tipo;
            document.getElementById('userRut').textContent = userData.rut;
            document.getElementById('userBirthdate').textContent = formatearFecha(userData.nacimiento);
            document.getElementById('userGender').textContent = userData.genero;
            document.getElementById('userUseNS').textContent = userData.usoNS;
            document.getElementById('userSocialName').textContent = userData.nombreSocial;
            
            // Configurar enlaces de email
            const userEmail = document.getElementById('userEmail');
            userEmail.textContent = userData.email;
            userEmail.href = `mailto:${userData.email}`;
            
            const userEmail2 = document.getElementById('userEmail2');
            if (userData.email2 && userData.email2 !== 'N/A') {
                userEmail2.textContent = userData.email2;
                userEmail2.href = `mailto:${userData.email2}`;
                userEmail2.style.display = 'inline-flex';
            } else {
                userEmail2.textContent = 'No disponible';
                userEmail2.removeAttribute('href');
                userEmail2.style.display = 'inline';
            }
            
            // Configurar enlaces de teléfono
            const userPhone = document.getElementById('userPhone');
            userPhone.textContent = userData.telefono;
            userPhone.href = `tel:${userData.telefono}`;
            
            const userPhone2 = document.getElementById('userPhone2');
            if (userData.telefono2 && userData.telefono2 !== 'N/A') {
                userPhone2.textContent = userData.telefono2;
                userPhone2.href = `tel:${userData.telefono2}`;
                userPhone2.style.display = 'inline-flex';
            } else {
                userPhone2.textContent = 'No disponible';
                userPhone2.removeAttribute('href');
                userPhone2.style.display = 'inline';
            }
            
            document.getElementById('userAddress').textContent = userData.direccion;
            
            // Configurar botón de editar
            document.getElementById('editUserBtn').href = `/usuarios/${userData.rut}/edit`;
            
            // Mostrar el panel
            showUserDetails();
        });
    });
    
    // Cerrar panel de detalles
    if (closePanelBtn) {
        closePanelBtn.addEventListener('click', hideUserDetails);
    }
    
    if (closePanelBtn2) {
        closePanelBtn2.addEventListener('click', hideUserDetails);
    }
    
    // Cerrar al hacer clic en el fondo del modal
    if (userDetailsPanel) {
        userDetailsPanel.addEventListener('click', function(event) {
            if (event.target === userDetailsPanel) {
                hideUserDetails();
            }
        });
    }
    
    // Cerrar con la tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && userDetailsPanel.classList.contains('show')) {
            hideUserDetails();
        }
    });
});

// Función para confirmar eliminación
function confirmarEliminacion(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar al usuario ${nombre}?\n\nEsta acción no se puede deshacer.`)) {
        document.getElementById(`delete-form-${id}`).submit();
    }
}

// Función para obtener iniciales
function obtenerIniciales(nombre, apellidos) {
    if (!nombre) return '?';
    const inicial1 = nombre.charAt(0).toUpperCase();
    const inicial2 = apellidos ? apellidos.charAt(0).toUpperCase() : '';
    return inicial1 + inicial2;
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
</script>
@endsection
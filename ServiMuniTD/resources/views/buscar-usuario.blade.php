@extends('layouts.app')

@section('title', 'Búsqueda de Usuario - ServiMuni')

@section('page-title', 'Búsqueda de Usuario')

@section('content')
<div class="container">
    <!-- Sección de búsqueda por RUT -->
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-search me-2"></i>Buscar Usuario por RUT</h2>
        </div>
        <div class="card-body">
            <form id="buscarUsuarioForm" method="GET" action="{{ route('buscar.usuario') }}" class="row align-items-end">
                <div class="col-md-8">
                    <div class="form-group mb-0">
                        <label for="rut" class="form-label">Ingrese RUT del Usuario</label>
                        <input type="text" id="rut" name="rut" class="form-control" placeholder="Ej: 12345678-9" 
                               value="{{ $rut ?? '' }}" required>
                        <small class="text-muted">Ingrese RUT sin puntos y con guión</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('usuarios.create') }}" class="btn btn-success" id="btnNuevoUsuario">
                            <i class="fas fa-user-plus"></i> Nuevo Usuario
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados de la búsqueda (muestra si existe usuario) -->
    @if(isset($usuario))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Usuario encontrado: {{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}
                </div>
            </div>
        </div>

        <!-- Sección con la información del usuario -->
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0"><i class="fas fa-user me-2"></i>Información del Usuario</h3>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditarUsuario">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="usuarioInfo">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>RUT:</strong> {{ $usuario['rut'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Tipo Persona:</strong> {{ $usuario['tipo_persona'] }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Nombre:</strong> {{ $usuario['nombre'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Apellidos:</strong> {{ $usuario['apellidos'] }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($usuario['fecha_nacimiento'])->format('d/m/Y') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Género:</strong> {{ $usuario['genero'] }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Teléfono Principal:</strong> {{ $usuario['telefono'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Teléfono Alternativo:</strong> {{ $usuario['telefono_2'] ?: 'No registrado' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Email Principal:</strong> {{ $usuario['email'] }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Email Alternativo:</strong> {{ $usuario['email_2'] ?: 'No registrado' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <strong>Dirección:</strong> {{ $usuario['direccion'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de edición de datos de contacto (inicialmente oculto) -->
                        <div id="editarUsuarioForm" style="display: none;">
                            <form method="POST" action="{{ route('usuarios.update.contacto', $usuario['rut']) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono Principal</label>
                                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                                   value="{{ $usuario['telefono'] }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="telefono_2">Teléfono Alternativo</label>
                                            <input type="tel" class="form-control" id="telefono_2" name="telefono_2" 
                                                   value="{{ $usuario['telefono_2'] }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email Principal</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="{{ $usuario['email'] }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email_2">Email Alternativo</label>
                                            <input type="email" class="form-control" id="email_2" name="email_2" 
                                                   value="{{ $usuario['email_2'] }}">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" 
                                                   value="{{ $usuario['direccion'] }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Guardar Cambios
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="btnCancelarEdicion">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Botón para nueva solicitud -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0"><i class="fas fa-plus-circle me-2"></i>Nueva Solicitud</h3>
                    </div>
                    <div class="card-body text-center">
                        <p>Ingrese una nueva solicitud para este usuario</p>
                        <a href="{{ route('solicitudes.create', ['rut' => $usuario['rut']]) }}" class="btn btn-lg btn-success">
                            <i class="fas fa-clipboard-list"></i> Crear Nueva Solicitud
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de solicitudes del usuario -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history me-2"></i>Historial de Solicitudes</h3>
            </div>
            <div class="card-body">
                @if(isset($solicitudes) && count($solicitudes) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Requerimiento</th>
                                    <th>Estado</th>
                                    <th>Etapa</th>
                                    <th>Providencia</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solicitudes as $solicitud)
                                <tr>
                                    <td>{{ $solicitud['id_solicitud'] }}</td>
                                    <td>{{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}</td>
                                    <td>
                                        @if(isset($solicitud['requerimiento_id']) && isset($requerimientos[$solicitud['requerimiento_id']]))
                                            {{ $requerimientos[$solicitud['requerimiento_id']]['nombre'] }}
                                        @else
                                            No especificado
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($solicitud['estado'] == 'Completado') bg-success
                                            @elseif($solicitud['estado'] == 'En proceso') bg-primary
                                            @elseif($solicitud['estado'] == 'Pendiente') bg-warning
                                            @else bg-secondary @endif">
                                            {{ $solicitud['estado'] }}
                                        </span>
                                    </td>
                                    <td>{{ $solicitud['etapa'] }}</td>
                                    <td>{{ $solicitud['providencia'] ?: 'No asignada' }}</td>
                                    <td>{{ Str::limit($solicitud['descripcion'], 50) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('solicitudes.show', $solicitud['id_solicitud']) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud']) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Este usuario no tiene solicitudes registradas.
                    </div>
                @endif
            </div>
        </div>
    @elseif(isset($rut) && !isset($usuario))
        <!-- Mensaje si se buscó pero no se encontró el usuario -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No se encontró ningún usuario con el RUT: <strong>{{ $rut }}</strong>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body text-center">
                <p class="mb-4">El usuario no está registrado en el sistema. ¿Desea registrarlo ahora?</p>
                <a href="{{ route('usuarios.create', ['rut' => $rut]) }}" class="btn btn-lg btn-success">
                    <i class="fas fa-user-plus"></i> Registrar Nuevo Usuario
                </a>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Formateador de RUT (agrega guión si no lo tiene)
    const rutInput = document.getElementById('rut');
    if(rutInput) {
        rutInput.addEventListener('blur', function() {
            let rut = this.value.replace(/\./g, '').replace('-', '');
            if(rut.length > 1) {
                rut = rut.substring(0, rut.length - 1) + '-' + rut.charAt(rut.length - 1);
                this.value = rut;
            }
        });
    }
    
    // Toggle para edición de datos de contacto
    const btnEditarUsuario = document.getElementById('btnEditarUsuario');
    const btnCancelarEdicion = document.getElementById('btnCancelarEdicion');
    const usuarioInfo = document.getElementById('usuarioInfo');
    const editarUsuarioForm = document.getElementById('editarUsuarioForm');
    
    if(btnEditarUsuario) {
        btnEditarUsuario.addEventListener('click', function() {
            usuarioInfo.style.display = 'none';
            editarUsuarioForm.style.display = 'block';
        });
    }
    
    if(btnCancelarEdicion) {
        btnCancelarEdicion.addEventListener('click', function() {
            editarUsuarioForm.style.display = 'none';
            usuarioInfo.style.display = 'block';
        });
    }
});
</script>
@endsection
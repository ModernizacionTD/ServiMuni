@extends('layouts.app')

@section('title', 'Detalles de Solicitud - ServiMuni')

@section('page-title', 'Detalles de Solicitud #' . $solicitud['id_solicitud'])

@section('content')
<div class="container">
    <!-- Enlaces de navegación -->
    <div class="mb-3">
        <a href="{{ route('solicitudes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-list"></i> Volver a Lista
        </a>
        <a href="{{ route('buscar.usuario', ['rut' => $solicitud['rut_usuario']]) }}" class="btn btn-outline-primary">
            <i class="fas fa-user"></i> Ver Usuario
        </a>
        <a href="{{ route('solicitudes.edit', $solicitud['id_solicitud']) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar Solicitud
        </a>
    </div>

    <!-- Mensajes flash -->
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

    <div class="row">
        <!-- Información de la Solicitud -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-clipboard-list me-2"></i>Información de la Solicitud</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>ID de Solicitud:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['id_solicitud'] }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Fecha de Ingreso:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipo de Requerimiento:</strong>
                        </div>
                        <div class="col-md-8">
                            @if($requerimiento)
                                {{ $requerimiento['nombre'] }}
                            @else
                                <span class="text-muted">No especificado</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Estado:</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="badge 
                                @if($solicitud['estado'] == 'Completado') bg-success
                                @elseif($solicitud['estado'] == 'En proceso') bg-primary
                                @elseif($solicitud['estado'] == 'Pendiente') bg-warning
                                @else bg-secondary @endif">
                                {{ $solicitud['estado'] }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Etapa:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['etapa'] }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Fecha Estimada:</strong>
                        </div>
                        <div class="col-md-8">
                            @if(!empty($solicitud['fecha_estimada_op']))
                                {{ \Carbon\Carbon::parse($solicitud['fecha_estimada_op'])->format('d/m/Y') }}
                            @else
                                <span class="text-muted">No definida</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Providencia:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['providencia'] ?: 'No asignada' }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Descripción:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['descripcion'] }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Localidad:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['localidad'] }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Tipo de Ubicación:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['tipo_ubicacion'] }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Dirección/Ubicación:</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $solicitud['ubicacion'] }}
                        </div>
                    </div>

                    @if(!empty($solicitud['imagen']))
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Imagen:</strong>
                        </div>
                        <div class="col-md-8">
                            <img src="{{ asset('storage/solicitudes/' . $solicitud['imagen']) }}" 
                                 alt="Imagen de solicitud" class="img-fluid img-thumbnail" 
                                 style="max-height: 200px;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel lateral con información relacionada -->
        <div class="col-md-4">
            <!-- Información del Usuario -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-user me-2"></i>Información del Usuario</h3>
                </div>
                <div class="card-body">
                    @if($usuario)
                        <div class="row mb-2">
                            <div class="col-5">
                                <strong>RUT:</strong>
                            </div>
                            <div class="col-7">
                                {{ $usuario['rut'] }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <strong>Nombre:</strong>
                            </div>
                            <div class="col-7">
                                {{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <strong>Teléfono:</strong>
                            </div>
                            <div class="col-7">
                                {{ $usuario['telefono'] }}
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5">
                                <strong>Email:</strong>
                            </div>
                            <div class="col-7">
                                {{ $usuario['email'] }}
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('buscar.usuario', ['rut' => $usuario['rut']]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver Perfil Completo
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No se encuentra información del usuario.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de Funcionarios Asignados -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-users-cog me-2"></i>Funcionarios Asignados</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Ingresado por:</h6>
                        @if($funcionarioIngreso)
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    {{ substr($funcionarioIngreso['nombre'], 0, 1) }}
                                </div>
                                <div>
                                    {{ $funcionarioIngreso['nombre'] }}
                                </div>
                            </div>
                        @else
                            <span class="text-muted">No especificado</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6>Gestor Asignado:</h6>
                        @if($funcionarioGestor)
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    {{ substr($funcionarioGestor['nombre'], 0, 1) }}
                                </div>
                                <div>
                                    {{ $funcionarioGestor['nombre'] }}
                                </div>
                            </div>
                            @if(!empty($solicitud['fecha_derivacion']))
                                <small class="text-muted d-block mt-1">
                                    Asignado el: {{ \Carbon\Carbon::parse($solicitud['fecha_derivacion'])->format('d/m/Y') }}
                                </small>
                            @endif
                        @else
                            <span class="text-muted">No asignado</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6>Técnico Asignado:</h6>
                        @if($funcionarioTecnico)
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2">
                                    {{ substr($funcionarioTecnico['nombre'], 0, 1) }}
                                </div>
                                <div>
                                    {{ $funcionarioTecnico['nombre'] }}
                                </div>
                            </div>
                        @else
                            <span class="text-muted">No asignado</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Fechas Importantes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Fechas Importantes</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Ingreso:</strong>
                        </div>
                        <div class="col-7">
                            {{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Derivación:</strong>
                        </div>
                        <div class="col-7">
                            @if(!empty($solicitud['fecha_derivacion']))
                                {{ \Carbon\Carbon::parse($solicitud['fecha_derivacion'])->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Pendiente</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Estimada:</strong>
                        </div>
                        <div class="col-7">
                            @if(!empty($solicitud['fecha_estimada_op']))
                                {{ \Carbon\Carbon::parse($solicitud['fecha_estimada_op'])->format('d/m/Y') }}
                            @else
                                <span class="text-muted">No definida</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-5">
                            <strong>Término:</strong>
                        </div>
                        <div class="col-7">
                            @if(!empty($solicitud['fecha_termino']))
                                {{ \Carbon\Carbon::parse($solicitud['fecha_termino'])->format('d/m/Y') }}
                            @else
                                <span class="text-muted">Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}
</style>
@endsection
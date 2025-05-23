@extends('layouts.app')

@section('title', 'Editar Solicitud - ServiMuni')

@section('page-title', 'Editar Solicitud #' . $solicitud['id_solicitud'])

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-edit me-2"></i>Editar Solicitud</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        @if($usuario)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Editando solicitud para: <strong>{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}</strong> (RUT: {{ $usuario['rut'] }})
        </div>
        @endif
        
        <form method="POST" action="{{ route('solicitudes.update', $solicitud['id_solicitud']) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="requerimiento_id" class="form-label">Tipo de Requerimiento</label>
                        <select id="requerimiento_id" name="requerimiento_id" class="form-control @error('requerimiento_id') is-invalid @enderror" required>
                            <option value="">Seleccionar requerimiento...</option>
                            @foreach($requerimientos as $requerimiento)
                                <option value="{{ $requerimiento['id_requerimiento'] }}" 
                                    {{ old('requerimiento_id', $solicitud['requerimiento_id']) == $requerimiento['id_requerimiento'] ? 'selected' : '' }}>
                                    {{ $requerimiento['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('requerimiento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="providencia" class="form-label">Número de Providencia</label>
                        <input type="number" id="providencia" name="providencia" class="form-control @error('providencia') is-invalid @enderror" 
                            value="{{ old('providencia', $solicitud['providencia']) }}">
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
                    rows="4" required>{{ old('descripcion', $solicitud['descripcion']) }}</textarea>
                @error('descripcion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="localidad" class="form-label">Localidad</label>
                        <input type="text" id="localidad" name="localidad" class="form-control @error('localidad') is-invalid @enderror" 
                            value="{{ old('localidad', $solicitud['localidad']) }}" required>
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
                            <option value="Domicilio" {{ old('tipo_ubicacion', $solicitud['tipo_ubicacion']) == 'Domicilio' ? 'selected' : '' }}>Domicilio</option>
                            <option value="Espacio Público" {{ old('tipo_ubicacion', $solicitud['tipo_ubicacion']) == 'Espacio Público' ? 'selected' : '' }}>Espacio Público</option>
                            <option value="Establecimiento" {{ old('tipo_ubicacion', $solicitud['tipo_ubicacion']) == 'Establecimiento' ? 'selected' : '' }}>Establecimiento</option>
                            <option value="Otro" {{ old('tipo_ubicacion', $solicitud['tipo_ubicacion']) == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('tipo_ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group mb-3">
                        <label for="ubicacion" class="form-label">Dirección/Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror" 
                            value="{{ old('ubicacion', $solicitud['ubicacion']) }}" required>
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="imagen" class="form-label">Imagen de Referencia</label>
                @if(!empty($solicitud['imagen']))
                    <div class="mb-2">
                        <img src="{{ asset('storage/solicitudes/' . $solicitud['imagen']) }}" 
                             alt="Imagen actual" class="img-thumbnail" style="max-height: 150px;">
                        <p class="text-muted">Imagen actual: {{ $solicitud['imagen'] }}</p>
                    </div>
                @endif
                <input type="file" id="imagen" name="imagen" class="form-control @error('imagen') is-invalid @enderror" 
                    accept="image/*">
                <small class="text-muted">Opcional. Sube una nueva imagen para reemplazar la actual. Formatos permitidos: JPG, PNG, GIF. Máx. 5MB</small>
                @error('imagen')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Estado y Asignación -->
            <div class="card my-4">
                <div class="card-header bg-light">
                    <h3 class="card-title mb-0">Estado y Asignación</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="estado"<select id="etapa" name="etapa" class="form-control @error('etapa') is-invalid @enderror" required>
                                    <option value="Ingreso" {{ old('etapa', $solicitud['etapa']) == 'Ingreso' ? 'selected' : '' }}>Ingreso</option>
                                    <option value="Asignada" {{ old('etapa', $solicitud['etapa']) == 'Asignada' ? 'selected' : '' }}>Asignada</option>
                                    <option value="En proceso" {{ old('etapa', $solicitud['etapa']) == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                    <option value="Evaluación" {{ old('etapa', $solicitud['etapa']) == 'Evaluación' ? 'selected' : '' }}>Evaluación</option>
                                    <option value="Finalizada" {{ old('etapa', $solicitud['etapa']) == 'Finalizada' ? 'selected' : '' }}>Finalizada</option>
                                </select>
                                @error('etapa')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="fecha_estimada_op" class="form-label">Fecha Estimada</label>
                                <input type="date" id="fecha_estimada_op" name="fecha_estimada_op" class="form-control @error('fecha_estimada_op') is-invalid @enderror" 
                                    value="{{ old('fecha_estimada_op', $solicitud['fecha_estimada_op']) }}">
                                <small class="text-muted">Fecha estimada de operación</small>
                                @error('fecha_estimada_op')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rut_gestor" class="form-label">Gestor Asignado</label>
                                <select id="rut_gestor" name="rut_gestor" class="form-control @error('rut_gestor') is-invalid @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($funcionarios as $funcionario)
                                        <option value="{{ $funcionario['id'] }}" 
                                            {{ old('rut_gestor', $solicitud['rut_gestor']) == $funcionario['id'] ? 'selected' : '' }}>
                                            {{ $funcionario['nombre'] }} ({{ $funcionario['rol'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Seleccione el funcionario que gestionará esta solicitud</small>
                                @error('rut_gestor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="rut_tecnico" class="form-label">Técnico Asignado</label>
                                <select id="rut_tecnico" name="rut_tecnico" class="form-control @error('rut_tecnico') is-invalid @enderror">
                                    <option value="">Sin asignar</option>
                                    @foreach($funcionarios as $funcionario)
                                        <option value="{{ $funcionario['id'] }}" 
                                            {{ old('rut_tecnico', $solicitud['rut_tecnico']) == $funcionario['id'] ? 'selected' : '' }}>
                                            {{ $funcionario['nombre'] }} ({{ $funcionario['rol'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Seleccione el técnico que trabajará en esta solicitud</small>
                                @error('rut_tecnico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fechas (solo mostrar, no editar directamente) -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Fecha de Ingreso</label>
                                <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($solicitud['fecha_inicio'])->format('d/m/Y') }}" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Fecha de Derivación</label>
                                <input type="text" class="form-control" value="{{ !empty($solicitud['fecha_derivacion']) ? \Carbon\Carbon::parse($solicitud['fecha_derivacion'])->format('d/m/Y') : 'Pendiente' }}" readonly>
                                <small class="text-muted">Se actualiza al asignar gestor</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">Fecha de Término</label>
                                <input type="text" class="form-control" value="{{ !empty($solicitud['fecha_termino']) ? \Carbon\Carbon::parse($solicitud['fecha_termino'])->format('d/m/Y') : 'Pendiente' }}" readonly>
                                <small class="text-muted">Se actualiza al completar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mantener valores originales -->
            <input type="hidden" name="rut_usuario" value="{{ $solicitud['rut_usuario'] }}">
            <input type="hidden" name="rut_ingreso" value="{{ $solicitud['rut_ingreso'] }}">
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('solicitudes.show', $solicitud['id_solicitud']) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar automáticamente la fecha de término al cambiar el estado a Completado
    const estadoSelect = document.getElementById('estado');
    
    if (estadoSelect) {
        estadoSelect.addEventListener('change', function() {
            if (this.value === 'Completado') {
                // Automáticamente seleccionar "Finalizada" en el campo etapa
                const etapaSelect = document.getElementById('etapa');
                if (etapaSelect) {
                    etapaSelect.value = 'Finalizada';
                }
            }
        });
    }
    
    // Actualizar etapa automáticamente al asignar gestor o técnico
    const rutGestorSelect = document.getElementById('rut_gestor');
    const rutTecnicoSelect = document.getElementById('rut_tecnico');
    const etapaSelect = document.getElementById('etapa');
    
    if (rutGestorSelect && etapaSelect) {
        rutGestorSelect.addEventListener('change', function() {
            if (this.value && etapaSelect.value === 'Ingreso') {
                etapaSelect.value = 'Asignada';
            }
        });
    }
    
    if (rutTecnicoSelect && etapaSelect) {
        rutTecnicoSelect.addEventListener('change', function() {
            if (this.value && (etapaSelect.value === 'Ingreso' || etapaSelect.value === 'Asignada')) {
                etapaSelect.value = 'En proceso';
            }
        });
    }
});
</script>
@endsection
@extends('layouts.app')

@section('title', 'Editar Requerimiento - Sistema de Gestión')

@section('page-title', 'Editar Requerimiento')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="card">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-clipboard-list me-2"></i>Editar Requerimiento</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('requerimientos.update', $requerimiento['id_requerimiento']) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="departamento_id" class="form-label">Departamento</label>
                        <select id="departamento_id" name="departamento_id" class="form-control @error('departamento_id') is-invalid @enderror" required>
                            <option value="">Seleccionar departamento...</option>
                            @foreach($departamentos as $departamento)
                                <option value="{{ $departamento['id'] }}" {{ old('departamento_id', $requerimiento['departamento_id']) == $departamento['id'] ? 'selected' : '' }}>
                                    {{ $departamento['nombre'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('departamento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="nombre" class="form-label">Nombre del Requerimiento</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre', $requerimiento['nombre']) }}" required maxlength="30">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="descripcion_req" class="form-label">Descripción del Requerimiento</label>
                <textarea id="descripcion_req" name="descripcion_req" class="form-control @error('descripcion_req') is-invalid @enderror" 
                          rows="4" required maxlength="255">{{ old('descripcion_req', $requerimiento['descripcion_req']) }}</textarea>
                @error('descripcion_req')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group mb-3">
                <label for="descripcion_precio" class="form-label">Descripción de Precio</label>
                <textarea id="descripcion_precio" name="descripcion_precio" class="form-control @error('descripcion_precio') is-invalid @enderror" 
                          rows="2" required maxlength="255">{{ old('descripcion_precio', $requerimiento['descripcion_precio']) }}</textarea>
                @error('descripcion_precio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" id="privado" name="privado" class="form-check-input @error('privado') is-invalid @enderror" 
                               value="1" {{ old('privado', $requerimiento['privado']) ? 'checked' : '' }}>
                        <label for="privado" class="form-check-label">Privado</label>
                        @error('privado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" id="publico" name="publico" class="form-check-input @error('publico') is-invalid @enderror" 
                               value="1" {{ old('publico', $requerimiento['publico']) ? 'checked' : '' }}>
                        <label for="publico" class="form-check-label">Público</label>
                        @error('publico')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('requerimientos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Requerimiento
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-label {
    font-weight: 500;
    margin-bottom: 5px;
}

.invalid-feedback {
    display: block;
}

.form-check-input {
    width: 3em;
    height: 1.5em;
    margin-top: 0;
}

.form-check-label {
    margin-left: 8px;
    font-weight: 500;
}

.form-switch {
    padding-left: 3.5em;
}
</style>
@endsection
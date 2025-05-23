@extends('layouts.app')

@section('title', 'Editar Departamento - Sistema de Gestión')

@section('page-title', 'Editar Departamento')

@section('content')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Editar Departamento</h2>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('departamentos.update', $departamento['id']) }}">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="nombre">Nombre del Departamento</label>
                <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $departamento['nombre']) }}" required autofocus>
                @error('nombre')
                    <div class="error" style="color: #dc3545; font-size: 0.875rem; margin-top: 5px;">{{ $message }}</div>
                @enderror
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar Departamento
                </button>
                
                <a href="{{ route('departamentos.index') }}" class="btn btn-danger">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
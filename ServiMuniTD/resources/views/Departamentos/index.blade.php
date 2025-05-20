@extends('layouts.app')

@section('title', 'Gestión de Departamentos - Sistema de Gestión')

@section('page-title', 'Gestión de Departamentos')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Departamentos</h2>
        <a href="{{ route('departamentos.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Departamento
        </a>
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
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departamentos as $departamento)
                        <tr>
                            <td>{{ $departamento['id'] }}</td>
                            <td>{{ $departamento['nombre'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('departamentos.edit', $departamento['id']) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    
                                    <form method="POST" action="{{ route('departamentos.destroy', $departamento['id']) }}" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este departamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3">
                                <div class="empty-state">
                                    <i class="fas fa-folder-open"></i>
                                    <p class="empty-state-text">No hay departamentos registrados</p>
                                    <a href="{{ route('departamentos.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Añadir Departamento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
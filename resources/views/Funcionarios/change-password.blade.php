@extends('layouts.app')

@section('title', 'Cambiar Contraseña - ServiMuni')

@section('page-title', 'Cambiar Contraseña')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-key me-2"></i>Cambiar Contraseña</h4>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('funcionarios.changePassword') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label required">Contraseña Actual</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label required">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   id="new_password" name="new_password" required minlength="6">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label required">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                   id="new_password_confirmation" name="new_password_confirmation" required minlength="6">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Guardar Cambios
                            </button>
                            <a href="{{ route('funcionarios.profile') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Volver a Mi Cuenta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
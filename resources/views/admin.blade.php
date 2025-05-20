@extends('layouts.app')

@section('title', 'Panel de Administración - Sistema de Gestión')

@section('page-title', 'Panel de Administración')

@section('content')
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Panel de Administración</h2>
    </div>
    <div class="card-body">
        <p>Bienvenido al panel de administración. Desde aquí puedes gestionar diferentes aspectos del sistema.</p>
        
        <div style="margin-top: 30px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">

                <div class="card" style="border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
                    <div style="padding: 25px; text-align: center; height: 100%;">
                        <i class="fas fa-sitemap" style="font-size: 2.5rem; color: #4361ee; margin-bottom: 15px;"></i>
                        <h3 style="margin: 0; font-size: 1.2rem; margin-bottom: 15px;">Gestión de Usuarios</h3>
                        <p style="color: #6c757d; margin-bottom: 20px;">Administra los usuarios del sistema, añade nuevos o modifica los existentes.</p>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-primary">
                            Ir a Usuarios
                        </a>
                    </div>
                </div>

                <div class="card" style="border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
                    <div style="padding: 25px; text-align: center; height: 100%;">
                        <i class="fas fa-sitemap" style="font-size: 2.5rem; color: #4361ee; margin-bottom: 15px;"></i>
                        <h3 style="margin: 0; font-size: 1.2rem; margin-bottom: 15px;">Gestión de Departamentos</h3>
                        <p style="color: #6c757d; margin-bottom: 20px;">Administra los departamentos del sistema, añade nuevos o modifica los existentes.</p>
                        <a href="{{ route('departamentos.index') }}" class="btn btn-primary">
                            Ir a Departamentos
                        </a>
                    </div>
                </div>
                
                <!-- Puedes añadir más módulos aquí -->
                <div class="card" style="border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05);">
                    <div style="padding: 25px; text-align: center; height: 100%; border: 2px dashed #e0e0e0; border-radius: 8px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <i class="fas fa-plus-circle" style="font-size: 2.5rem; color: #6c757d; margin-bottom: 15px;"></i>
                        <h3 style="margin: 0; font-size: 1.2rem; margin-bottom: 15px; color: #6c757d;">Próximo Módulo</h3>
                        <p style="color: #6c757d; margin-bottom: 10px;">Se añadirán más módulos en futuras actualizaciones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Estadísticas del Sistema</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
            <div style="background-color: #e8f0fe; padding: 20px; border-radius: 8px; text-align: center;">
                <i class="fas fa-users" style="font-size: 2rem; color: #4361ee; margin-bottom: 10px;"></i>
                <h4 style="margin: 0; font-size: 0.9rem; color: #6c757d;">Total Usuarios</h4>
                <p style="font-size: 1.5rem; font-weight: bold; margin: 10px 0 0;">-</p>
            </div>
            
            <div style="background-color: #e8f0fe; padding: 20px; border-radius: 8px; text-align: center;">
                <i class="fas fa-sitemap" style="font-size: 2rem; color: #4361ee; margin-bottom: 10px;"></i>
                <h4 style="margin: 0; font-size: 0.9rem; color: #6c757d;">Departamentos</h4>
                <p style="font-size: 1.5rem; font-weight: bold; margin: 10px 0 0;">-</p>
            </div>
            
            <div style="background-color: #e8f0fe; padding: 20px; border-radius: 8px; text-align: center;">
                <i class="fas fa-clipboard-list" style="font-size: 2rem; color: #4361ee; margin-bottom: 10px;"></i>
                <h4 style="margin: 0; font-size: 0.9rem; color: #6c757d;">Solicitudes</h4>
                <p style="font-size: 1.5rem; font-weight: bold; margin: 10px 0 0;">-</p>
            </div>
            
            <div style="background-color: #e8f0fe; padding: 20px; border-radius: 8px; text-align: center;">
                <i class="fas fa-chart-line" style="font-size: 2rem; color: #4361ee; margin-bottom: 10px;"></i>
                <h4 style="margin: 0; font-size: 0.9rem; color: #6c757d;">Actividad</h4>
                <p style="font-size: 1.5rem; font-weight: bold; margin: 10px 0 0;">-</p>
            </div>
        </div>
    </div>
</div>
@endsection
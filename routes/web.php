<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapsController; // NUEVA LÍNEA
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BandejaController;

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Página de inicio
Route::get('/', function () {
    return view('welcome');
}); 

// Búsqueda de usuarios por RUT
Route::get('/buscar-usuario', [BusquedaController::class, 'buscarUsuario'])->name('buscar.usuario');
Route::put('/usuarios/{rut}/actualizar-contacto', [BusquedaController::class, 'actualizarContacto'])->name('usuarios.update.contacto');

// Ruta para crear solicitudes (con parámetro RUT opcional)
Route::get('/solicitudes/crear/{rut?}', [SolicitudController::class, 'create'])->name('solicitudes.create');

// Rutas para gestión de solicitudes
Route::prefix('solicitudes')->group(function () {
    Route::get('/', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/crear/{rut?}', [SolicitudController::class, 'create'])->name('solicitudes.create');
    Route::post('/', [SolicitudController::class, 'store'])->name('solicitudes.store');
    Route::get('/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('/{id}/editar', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
    Route::put('/{id}', [SolicitudController::class, 'update'])->name('solicitudes.update');
    Route::delete('/{id}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');
});

// Rutas para bandeja de solicitudes
Route::prefix('bandeja')->group(function () {
    Route::get('/', [BandejaController::class, 'index'])->name('bandeja.index');
    Route::post('/{id}/tomar', [BandejaController::class, 'tomarSolicitud'])->name('bandeja.tomar');
    Route::post('/{id}/cambiar-estado', [BandejaController::class, 'cambiarEstado'])->name('bandeja.cambiar-estado');
    
    // Rutas existentes
    Route::post('/{id}/validar', [BandejaController::class, 'validarIngreso'])->name('bandeja.validar');
    Route::post('/{id}/reasignar', [BandejaController::class, 'reasignarSolicitud'])->name('bandeja.reasignar');
    
    // NUEVA RUTA: Derivar a técnico
    Route::post('/{id}/derivar-tecnico', [BandejaController::class, 'derivarATecnico'])->name('bandeja.derivar-tecnico');
});

// NUEVAS RUTAS PARA EL MAPA
Route::prefix('mapa')->group(function () {
    Route::get('/', [MapsController::class, 'index'])->name('mapa.index');
    Route::get('/datos', [MapsController::class, 'obtenerDatosMapa'])->name('mapa.datos');
});

// Agregar esta ruta API para obtener técnicos
Route::get('/api/funcionarios/tecnicos', [BandejaController::class, 'getTecnicos'])->name('api.tecnicos');

// Dashboard usando DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// APIs opcionales para métricas en tiempo real
Route::get('/api/dashboard/metrics', [DashboardController::class, 'getMetrics'])->name('dashboard.metrics');
Route::get('/api/dashboard/chart-data', [DashboardController::class, 'getChartData'])->name('dashboard.chart');

// Página de administración - solo para administradores
Route::get('/admin', function () {
    // Verificar si el usuario está autenticado
    if (!session('user_id')) {
        return redirect()->route('login');
    }
    
    // Verificar si el usuario es administrador
    if (session('user_rol') !== 'admin') {
        abort(403, 'No tienes permiso para acceder a esta página.');
    }
    
    return view('admin', [
        'nombre' => session('user_nombre')
    ]);
})->name('admin');

// Rutas para gestión de departamentos
Route::prefix('departamentos')->group(function () {
    Route::get('/', [DepartamentoController::class, 'index'])->name('departamentos.index');
    Route::get('/create', [DepartamentoController::class, 'create'])->name('departamentos.create');
    Route::post('/', [DepartamentoController::class, 'store'])->name('departamentos.store');
    Route::get('/{id}/edit', [DepartamentoController::class, 'edit'])->name('departamentos.edit');
    Route::put('/{id}', [DepartamentoController::class, 'update'])->name('departamentos.update');
    Route::delete('/{id}', [DepartamentoController::class, 'destroy'])->name('departamentos.destroy');
});

// Rutas para gestión de usuarios
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/{id}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
});

// Rutas para gestión de requerimientos
Route::prefix('requerimientos')->group(function () {
    Route::get('/', [RequerimientoController::class, 'index'])->name('requerimientos.index');
    Route::get('/create', [RequerimientoController::class, 'create'])->name('requerimientos.create');
    Route::post('/', [RequerimientoController::class, 'store'])->name('requerimientos.store');
    Route::get('/{id}/edit', [RequerimientoController::class, 'edit'])->name('requerimientos.edit');
    Route::put('/{id}', [RequerimientoController::class, 'update'])->name('requerimientos.update');
    Route::delete('/{id}', [RequerimientoController::class, 'destroy'])->name('requerimientos.destroy');
});

// Rutas para gestión de funcionarios
Route::prefix('funcionarios')->group(function () {
    Route::get('/', [FuncionarioController::class, 'index'])->name('funcionarios.index');
    Route::get('/create', [FuncionarioController::class, 'create'])->name('funcionarios.create');
    Route::post('/', [FuncionarioController::class, 'store'])->name('funcionarios.store');
    Route::get('/{id}/edit', [FuncionarioController::class, 'edit'])->name('funcionarios.edit');
    Route::put('/{id}', [FuncionarioController::class, 'update'])->name('funcionarios.update');
    Route::delete('/{id}', [FuncionarioController::class, 'destroy'])->name('funcionarios.destroy');
    
    // Rutas adicionales
    Route::get('/profile', [FuncionarioController::class, 'showProfile'])->name('profile');
    Route::get('/funcionarios/profile', [FuncionarioController::class, 'showProfile'])->name('funcionarios.profile');
    Route::get('/change-password', [FuncionarioController::class, 'showChangePassword'])->name('funcionarios.showChangePassword');
    Route::post('/change-password', [FuncionarioController::class, 'changePassword'])->name('funcionarios.changePassword');
});

Route::get('/test-view', function () {
    // Datos de prueba básicos
    $funcionario = [
        'id' => '1',
        'email' => 'test@example.com',
        'nombre' => 'Usuario de Prueba',
        'rol' => 'admin'
    ];
    
    // Intenta cargar la vista directamente con datos de prueba
    return view('funcionarios.profile', [
        'funcionario' => $funcionario,
        'nombre' => 'Usuario de Prueba'
    ]);
})->name('test-view');
<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RequerimientoController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\BusquedaController;
use Illuminate\Support\Facades\Route;

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

// Dashboard - protegido para usuarios autenticados
Route::get('/dashboard', function () {
    // Verificar si el usuario está autenticado
    if (!session('user_id')) {
        return redirect()->route('login');
    }
    
    $funcionarioService = app(App\Services\FuncionarioService::class);
    $departamentoService = app(App\Services\DepartamentoService::class);
    $requerimientoService = app(App\Services\RequerimientoService::class);
    $usuarioService = app(App\Services\UsuarioService::class);
    $solicitudService = app(App\Services\SolicitudService::class);

    // Obtener los datos necesarios
    $departamentos = $departamentoService->getAllDepartamentos() ?? [];
    $requerimientos = $requerimientoService->getAllRequerimientos() ?? [];
    $usuarios = $usuarioService->getAllUsuarios() ?? [];
    
    
    // Para funcionarios, como es un ejemplo y no hay una función específica,
    // podemos usar la misma lista de usuarios
    $funcionarios = $funcionarioService->getAllFuncionarios() ?? [];
    
    // Ejemplos de actividades recientes (en una app real, esto vendría de una tabla de actividades)
    $actividades = []; // En caso de que quieras implementar actividades reales más adelante
    
    return view('dashboard', [
        'nombre' => session('user_nombre'),
        'rol' => session('user_rol'),
        'departamentos' => $departamentos,
        'requerimientos' => $requerimientos, 
        'usuarios' => $usuarios,
        'funcionarios' => $funcionarios,
        'actividades' => $actividades
    ]);
})->name('dashboard');

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
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FuncionarioService;
use App\Services\DepartamentoService;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    protected $funcionarioService;

    public function __construct(FuncionarioService $funcionarioService)
    {
        $this->funcionarioService = $funcionarioService;
    }

    /**
     * Muestra la lista de funcionarios
     */
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        try {
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            
            return view('funcionarios.index', [
                'funcionarios' => $funcionarios,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar funcionarios: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo funcionario
     */
    public function create()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // The correct way to instantiate DepartamentoService
        $departamentoService = app(\App\Services\DepartamentoService::class);
        $departamentos = $departamentoService->getAllDepartamentos();
        
        return view('funcionarios.create', [
            'nombre' => session('user_nombre'),
            'departamentos' => $departamentos
        ]);
    }

    /**
     * Almacena un nuevo funcionario
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Verificar si el usuario es administrador
        if (session('user_rol') !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        $request->validate([
            'email' => 'required|email|max:255',
            'nombre' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
            'rol' => 'required|in:admin,desarrollador,orientador,gestor,tecnico',
        ]);

        try {
            // Preparar datos para el funcionario
            $funcionarioData = [
                'email' => $request->email,
                'nombre' => $request->nombre,
                'password' => $request->password,
                'rol' => $request->rol,
            ];
            
            // Guardar funcionario
            $result = $this->funcionarioService->createFuncionario($funcionarioData);
            
            return redirect()->route('funcionarios.index')
                ->with('success', 'Funcionario creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear funcionario: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el funcionario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un funcionario
     */
    public function edit($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Para edición, permitimos que el usuario pueda editar su propio perfil
        // pero solo administradores pueden editar a otros
        if (session('user_rol') !== 'admin' && session('user_id') != $id) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para editar este funcionario.');
        }
        
        try {
            $funcionario = $this->funcionarioService->getFuncionarioById($id);
            
            if (!$funcionario) {
                return redirect()->route('funcionarios.index')
                    ->with('error', 'Funcionario no encontrado.');
            }
            
            return view('funcionarios.edit', [
                'funcionario' => $funcionario,
                'nombre' => session('user_nombre'),
                'esAdmin' => session('user_rol') === 'admin',
                'esPropietario' => session('user_id') == $id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar funcionario: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el funcionario: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un funcionario
     */
    public function update(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Para actualización, permitimos que el usuario pueda actualizar su propio perfil
        // pero solo administradores pueden actualizar a otros o cambiar roles
        $esAdmin = session('user_rol') === 'admin';
        $esPropietario = session('user_id') == $id;
        
        if (!$esAdmin && !$esPropietario) {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para actualizar este funcionario.');
        }
        
        // Validar campos siempre requeridos
        $reglas = [
            'email' => 'required|email|max:255',
            'nombre' => 'required|string|max:255',
        ];
        
        // Solo los administradores pueden cambiar roles
        if ($esAdmin) {
            $reglas['rol'] = 'required|in:admin,desarrollador,orientador,gestor,tecnico';
        }
        
        // La contraseña es opcional en actualización, pero si se proporciona debe cumplir requisitos
        $reglas['password'] = 'nullable|string|min:6|max:255';
        
        // Si no es admin y no es propietario, no permitir cambiar contraseña
        if (!$esAdmin && !$esPropietario) {
            unset($reglas['password']);
        }
        
        $request->validate($reglas);

        try {
            // Obtener funcionario actual
            $funcionarioActual = $this->funcionarioService->getFuncionarioById($id);
            
            if (!$funcionarioActual) {
                return redirect()->route('funcionarios.index')
                    ->with('error', 'Funcionario no encontrado.');
            }
            
            // Preparar datos para actualizar
            $funcionarioData = [
                'id' => $id,
                'email' => $request->email,
                'nombre' => $request->nombre,
            ];
            
            // Solo los administradores pueden cambiar roles
            if ($esAdmin && $request->has('rol')) {
                $funcionarioData['rol'] = $request->rol;
            } else {
                // Mantener el rol actual
                $funcionarioData['rol'] = $funcionarioActual['rol'];
            }
            
            // Solo actualizar la contraseña si se proporciona
            if ($request->filled('password') && ($esAdmin || $esPropietario)) {
                $funcionarioData['password'] = $request->password;
            } else {
                // Mantener la contraseña actual
                $funcionarioData['password'] = $funcionarioActual['password'];
            }
            
            // Actualizar funcionario
            $result = $this->funcionarioService->updateFuncionario($id, $funcionarioData);
            
            // Actualizar la sesión si el usuario ha actualizado su propio perfil
            if ($esPropietario) {
                session([
                    'user_email' => $funcionarioData['email'],
                    'user_nombre' => $funcionarioData['nombre'],
                    'user_rol' => $funcionarioData['rol'],
                ]);
            }
            
            return redirect()->route('funcionarios.index')
                ->with('success', 'Funcionario actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar funcionario: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar el funcionario: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un funcionario
     */
    public function destroy($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        // Solo los administradores pueden eliminar funcionarios
        if (session('user_rol') !== 'admin') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para eliminar funcionarios.');
        }
        
        // No permitir eliminar el propio usuario
        if (session('user_id') == $id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        
        try {
            $result = $this->funcionarioService->deleteFuncionario($id);
            
            return redirect()->route('funcionarios.index')
                ->with('success', 'Funcionario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el funcionario: ' . $e->getMessage());
        }
    }
    
    /**
     * Permite al usuario cambiar su propia contraseña
     */
    public function showChangePassword()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        return view('funcionarios.change-password', [
            'nombre' => session('user_nombre')
        ]);
    }
    
    /**
     * Procesa el cambio de contraseña
     */
    public function changePassword(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
        
        try {
            // Obtener el funcionario actual
            $funcionario = $this->funcionarioService->getFuncionarioById(session('user_id'));
            
            if (!$funcionario) {
                return back()->with('error', 'Usuario no encontrado.');
            }
            
            // Verificar contraseña actual
            if ($request->current_password != $funcionario['password']) {
                return back()->with('error', 'La contraseña actual es incorrecta.');
            }
            
            // Actualizar contraseña
            $funcionarioData = [
                'id' => session('user_id'),
                'email' => $funcionario['email'],
                'nombre' => $funcionario['nombre'],
                'password' => $request->new_password,
                'rol' => $funcionario['rol'],
            ];
            
            $result = $this->funcionarioService->updateFuncionario(session('user_id'), $funcionarioData);
            
            return redirect()->route('dashboard')
                ->with('success', 'Contraseña actualizada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar la contraseña: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra perfil del funcionario
     */
    public function showProfile()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $funcionario = $this->funcionarioService->getFuncionarioById(session('user_id'));
            
            if (!$funcionario) {
                return redirect()->route('dashboard')
                    ->with('error', 'Perfil no encontrado.');
            }
            
            // Depuración - Añade esto para ver qué estás obteniendo
            \Log::info('Datos del funcionario: ' . json_encode($funcionario));
            
            return view('funcionarios.profile', [
                'funcionario' => $funcionario,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar perfil: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return back()->with('error', 'Error al cargar el perfil: ' . $e->getMessage());
        }
    }
}
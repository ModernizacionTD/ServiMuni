<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UsuarioService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
   protected $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;

    }

    /**
     * Muestra la lista de usuarios
     */
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $usuarios = $this->usuarioService->getAllUsuarios();
            
            return view('usuarios.index', [
                'usuarios' => $usuarios,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo usuario
     */
    public function create()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        return view('usuarios.create', [
            'nombre' => session('user_nombre')
        ]);
    }

    /**
     * Almacena un nuevo usuario
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'rut' => 'required|string|max:255',
            'tipo_persona' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'uso_ns' => 'required|in:Sí,No',
            'nombre_social' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:Masculino,Femenino,Transmasculino,Transfemenino,No decir',
            'telefono' => 'required|string|max:12',
            'telefono_2' => 'nullable|string|max:12',
            'email' => 'required|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:255',
        ]);

        try {
            $result = $this->usuarioService->createUsuario($request->all());
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un usuario
     */
    public function edit($rut)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $usuario = $this->usuarioService->getUsuarioByRut($rut);
            
            if (!$usuario) {
                return redirect()->route('usuarios.index')
                    ->with('error', 'Usuario no encontrado.');
            }
            
            return view('usuarios.edit', [
                'usuario' => $usuario,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar usuario: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un usuario
     */
    public function update(Request $request, $rut)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'rut' => 'required|string|max:255',
            'tipo_persona' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'uso_ns' => 'required|in:Sí,No',
            'nombre_social' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:Masculino,Femenino,Transmasculino,Transfemenino,No decir',
            'telefono' => 'required|string|max:12',
            'telefono_2' => 'nullable|string|max:12',
            'email' => 'required|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:255',
        ]);

        try {
            $result = $this->usuarioService->updateUsuario($rut, $request->all()); // Corregido: usar updateUsuario con 'U' mayúscula
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario
     */
    public function destroy($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $result = $this->usuarioService->deleteUsuario($id); // Corregido: usar deleteUsuario con 'U' mayúscula
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
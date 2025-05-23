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
        
        // Log para debugging
        \Log::info('Datos recibidos para crear usuario:', $request->all());
        
        // Validaciones base que siempre se aplican
        $rules = [
            'rut' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Natural,Jurídica',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'telefono_2' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:500',
        ];

        // Validaciones específicas para Persona Natural
        if ($request->tipo_persona === 'Natural') {
            $rules = array_merge($rules, [
                'apellidos' => 'required|string|max:255',
                'uso_ns' => 'required|in:Sí,No',
                'nombre_social' => 'nullable|string|max:255',
                'fecha_nacimiento' => 'required|date|before:today',
                'genero' => 'required|in:Masculino,Femenino,Transmasculino,Transfemenino,No decir',
            ]);
            
            // Si usa nombre social, hacer requerido
            if ($request->uso_ns === 'Sí') {
                $rules['nombre_social'] = 'required|string|max:255';
            }
        }

        // Aplicar validaciones
        $validatedData = $request->validate($rules);
        
        // Log después de validación
        \Log::info('Datos validados:', $validatedData);

        try {
            // Preparar datos para persona jurídica (valores por defecto)
            if ($request->tipo_persona === 'Jurídica') {
                $validatedData['apellidos'] = '';
                $validatedData['uso_ns'] = 'No';
                $validatedData['nombre_social'] = '';
                $validatedData['fecha_nacimiento'] = '1900-01-01'; // Fecha por defecto
                $validatedData['genero'] = 'No decir';
            }
            
            // Log antes de enviar al service
            \Log::info('Datos finales para crear usuario:', $validatedData);
            
            $result = $this->usuarioService->createUsuario($validatedData);
            
            \Log::info('Usuario creado exitosamente');
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado correctamente.');
                
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            \Log::error('Trace: ' . $e->getTraceAsString());
            
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
        
        \Log::info('Actualizando usuario RUT: ' . $rut, $request->all());
        
        // Validaciones base
        $rules = [
            'rut' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Natural,Jurídica',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'telefono_2' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'email_2' => 'nullable|email|max:255',
            'direccion' => 'required|string|max:500',
        ];

        // Validaciones específicas para Persona Natural
        if ($request->tipo_persona === 'Natural') {
            $rules = array_merge($rules, [
                'apellidos' => 'required|string|max:255',
                'uso_ns' => 'required|in:Sí,No',
                'nombre_social' => 'nullable|string|max:255',
                'fecha_nacimiento' => 'required|date|before:today',
                'genero' => 'required|in:Masculino,Femenino,Transmasculino,Transfemenino,No decir',
            ]);
            
            if ($request->uso_ns === 'Sí') {
                $rules['nombre_social'] = 'required|string|max:255';
            }
        }

        $validatedData = $request->validate($rules);

        try {
            // Preparar datos para persona jurídica
            if ($request->tipo_persona === 'Jurídica') {
                $validatedData['apellidos'] = '';
                $validatedData['uso_ns'] = 'No';
                $validatedData['nombre_social'] = '';
                $validatedData['fecha_nacimiento'] = '1900-01-01';
                $validatedData['genero'] = 'No decir';
            }
            
            $result = $this->usuarioService->updateUsuario($rut, $validatedData);
            
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
    public function destroy($rut)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $result = $this->usuarioService->deleteUsuario($rut);
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario eliminado correctamente.');
                
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            return back()
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
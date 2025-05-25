<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UsuarioService;
use App\Services\SolicitudService;
use App\Services\RequerimientoService;
use App\Services\DepartamentoService;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    protected $usuarioService;
    protected $solicitudService;
    protected $requerimientoService;
    protected $departamentoService;

    public function __construct(
        UsuarioService $usuarioService,
        SolicitudService $solicitudService,
        RequerimientoService $requerimientoService,
        DepartamentoService $departamentoService
    ) {
        $this->usuarioService = $usuarioService;
        $this->solicitudService = $solicitudService;
        $this->requerimientoService = $requerimientoService;
        $this->departamentoService = $departamentoService;
    }

    /**
     * Busca un usuario por RUT y muestra su información
     */
    public function buscarUsuario(Request $request)
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $rut = $request->input('rut');
        
        if ($rut) {
            try {
                // Limpiamos y formateamos el RUT
                $rut = str_replace('.', '', $rut); // Quitar puntos
                
                \Log::info('Buscando usuario con RUT: ' . $rut);
                
                // Buscar usuario
                $usuario = $this->usuarioService->getUsuarioByRut($rut);
                
                if ($usuario) {
                    \Log::info('Usuario encontrado: ' . $usuario['nombre'] . ' ' . $usuario['apellidos']);
                    
                    // Si existe el usuario, obtener sus solicitudes
                    $solicitudes = $this->solicitudService->getByRutUsuario($rut);
                    \Log::info('Solicitudes encontradas: ' . count($solicitudes));
                    
                    // Obtener información de requerimientos para mostrar nombres
                    $requerimientos = [];
                    $todosRequerimientos = $this->requerimientoService->getAllRequerimientos();
                    
                    foreach ($todosRequerimientos as $requerimiento) {
                        $requerimientos[$requerimiento['id_requerimiento']] = $requerimiento;
                    }
                    
                    // Obtener información de departamentos
                    $departamentos = [];
                    $todosDepartamentos = $this->departamentoService->getAllDepartamentos();
                    
                    foreach ($todosDepartamentos as $departamento) {
                        $departamentos[$departamento['id']] = $departamento;
                    }
                    
                    \Log::info('Requerimientos cargados: ' . count($requerimientos));
                    \Log::info('Departamentos cargados: ' . count($departamentos));
                    
                    return view('buscar-usuario', [
                        'rut' => $rut,
                        'usuario' => $usuario,
                        'solicitudes' => $solicitudes,
                        'requerimientos' => $requerimientos,
                        'departamentos' => $departamentos,
                        'nombre' => session('user_nombre')
                    ]);
                } else {
                    \Log::info('Usuario no encontrado con RUT: ' . $rut);
                }
                
                // Si no existe, devolver la vista con mensaje
                return view('buscar-usuario', [
                    'rut' => $rut,
                    'nombre' => session('user_nombre')
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Error al buscar usuario: ' . $e->getMessage());
                \Log::error('Traza: ' . $e->getTraceAsString());
                
                return back()->with('error', 'Error al buscar usuario: ' . $e->getMessage());
            }
        }
        
        // Si no se proporcionó RUT, mostrar solo el formulario de búsqueda
        return view('buscar-usuario', [
            'nombre' => session('user_nombre')
        ]);
    }
    
    /**
     * Actualiza los datos de contacto de un usuario
     */
    public function actualizarContacto(Request $request, $rut)
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            \Log::info('Actualizando contacto para usuario RUT: ' . $rut);
            \Log::info('Datos recibidos:', $request->all());
            
            $request->validate([
                'telefono' => 'required|string|max:12',
                'telefono_2' => 'nullable|string|max:12',
                'email' => 'required|email|max:255',
                'email_2' => 'nullable|email|max:255',
                'direccion' => 'required|string|max:255',
            ]);
            
            // Obtener usuario actual
            $usuario = $this->usuarioService->getUsuarioByRut($rut);
            
            if (!$usuario) {
                \Log::error('Usuario no encontrado para actualizar contacto: ' . $rut);
                return back()->with('error', 'Usuario no encontrado.');
            }
            
            // Preparar datos actualizados (solo los campos que se pueden editar)
            $datosActualizados = [
                'telefono' => $request->telefono,
                'telefono_2' => $request->telefono_2 ?: '', // Convertir null a cadena vacía
                'email' => $request->email,
                'email_2' => $request->email_2 ?: '', // Convertir null a cadena vacía
                'direccion' => $request->direccion,
            ];
            
            \Log::info('Datos a actualizar:', $datosActualizados);
            
            // Actualizar usuario
            $this->usuarioService->updateUsuario($rut, $datosActualizados);
            
            \Log::info('Contacto actualizado exitosamente para RUT: ' . $rut);
            
            return redirect()->route('buscar.usuario', ['rut' => $rut])
                             ->with('success', 'Información de contacto actualizada correctamente.');
                             
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación al actualizar contacto: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error al actualizar contacto: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return back()->with('error', 'Error al actualizar la información: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario de búsqueda (método alternativo si se necesita)
     */
    public function index()
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        return view('buscar-usuario', [
            'nombre' => session('user_nombre')
        ]);
    }
}
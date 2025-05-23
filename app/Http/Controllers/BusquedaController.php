<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UsuarioService;
use App\Services\SolicitudService;
use App\Services\RequerimientoService;

class BusquedaController extends Controller
{
    protected $usuarioService;
    protected $solicitudService;
    protected $requerimientoService;

    public function __construct(
        UsuarioService $usuarioService, 
        SolicitudService $solicitudService,
        RequerimientoService $requerimientoService
    ) {
        $this->usuarioService = $usuarioService;
        $this->solicitudService = $solicitudService;
        $this->requerimientoService = $requerimientoService;
    }

    /**
     * Busca un usuario por RUT y muestra su información
     */
    public function buscarUsuario(Request $request)
    {
        $rut = $request->input('rut');
        
        if ($rut) {
            // Limpiamos y formateamos el RUT
            $rut = str_replace('.', '', $rut); // Quitar puntos
            
            // Buscar usuario
            $usuario = $this->usuarioService->getUsuarioByRut($rut);
            
            if ($usuario) {
                // Si existe el usuario, obtener sus solicitudes
                $solicitudes = $this->solicitudService->getByRutUsuario($rut);
                
                // Obtener información de requerimientos para mostrar nombres
                $requerimientos = [];
                $todosRequerimientos = $this->requerimientoService->getAllRequerimientos();
                
                foreach ($todosRequerimientos as $requerimiento) {
                    $requerimientos[$requerimiento['id_requerimiento']] = $requerimiento;
                }
                
                return view('buscar-usuario', compact('rut', 'usuario', 'solicitudes', 'requerimientos'));
            }
            
            // Si no existe, devolver la vista con mensaje
            return view('buscar-usuario', compact('rut'));
        }
        
        // Si no se proporcionó RUT, mostrar solo el formulario de búsqueda
        return view('buscar-usuario');
    }
    
    /**
     * Actualiza los datos de contacto de un usuario
     */
    public function actualizarContacto(Request $request, $rut)
    {
        try {
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
                return back()->with('error', 'Usuario no encontrado.');
            }
            
            // Preparar datos actualizados (solo los campos que se pueden editar)
            $datosActualizados = [
                'telefono' => $request->telefono,
                'telefono_2' => $request->telefono_2,
                'email' => $request->email,
                'email_2' => $request->email_2,
                'direccion' => $request->direccion,
            ];
            
            // Actualizar usuario
            $this->usuarioService->updateUsuario($rut, $datosActualizados);
            
            return redirect()->route('buscar.usuario', ['rut' => $rut])
                             ->with('success', 'Información de contacto actualizada correctamente.');
                             
        } catch (\Exception $e) {
            \Log::error('Error al actualizar contacto: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar la información: ' . $e->getMessage());
        }
    }
}
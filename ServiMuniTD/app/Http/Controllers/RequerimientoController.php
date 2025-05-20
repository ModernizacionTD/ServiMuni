<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DataService;
use Illuminate\Http\Request;

class RequerimientoController extends Controller
{
   protected $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Muestra la lista de requerimientos
     */
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $requerimientos = $this->dataService->getAllRequerimientos();
            $departamentos = $this->dataService->getAllDepartamentos();
            
            return view('requerimientos.index', [
                'requerimientos' => $requerimientos,
                'departamentos' => $departamentos,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar requerimientos: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo requerimiento
     */
    public function create()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            // Obtener todos los departamentos para el select
            $departamentos = $this->dataService->getAllDepartamentos();
            
            return view('requerimientos.create', [
                'departamentos' => $departamentos,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacena un nuevo requerimiento
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'departamento_id' => 'required|integer',
            'nombre' => 'required|string|max:30',
            'descripcion_req' => 'required|string|max:255',
            'descripcion_precio' => 'required|string|max:255',
            'privado' => 'boolean',
            'publico' => 'boolean',
        ]);

        try {
            // Preparar los datos
            $data = [
                'departamento_id' => $request->departamento_id,
                'nombre' => $request->nombre,
                'descripcion_req' => $request->descripcion_req,
                'descripcion_precio' => $request->descripcion_precio,
                'privado' => $request->has('privado') ? true : false,
                'publico' => $request->has('publico') ? true : false,
            ];
            
            $result = $this->dataService->createRequerimiento($data);
            
            return redirect()->route('requerimientos.index')
                ->with('success', 'Requerimiento creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear requerimiento: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un requerimiento
     */
    public function edit($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $requerimiento = $this->dataService->getRequerimientoById($id);
            
            if (!$requerimiento) {
                return redirect()->route('requerimientos.index')
                    ->with('error', 'Requerimiento no encontrado.');
            }
            
            // Obtener todos los departamentos para el select
            $departamentos = $this->dataService->getAllDepartamentos();
            
            return view('requerimientos.edit', [
                'requerimiento' => $requerimiento,
                'departamentos' => $departamentos,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar requerimiento: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un requerimiento
     */
    public function update(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'departamento_id' => 'required|integer',
            'nombre' => 'required|string|max:30',
            'descripcion_req' => 'required|string|max:255',
            'descripcion_precio' => 'required|string|max:255',
            'privado' => 'boolean',
            'publico' => 'boolean',
        ]);

        try {
            // Preparar los datos
            $data = [
                'id_requerimiento' => $id,
                'departamento_id' => $request->departamento_id,
                'nombre' => $request->nombre,
                'descripcion_req' => $request->descripcion_req,
                'descripcion_precio' => $request->descripcion_precio,
                'privado' => $request->has('privado') ? true : false,
                'publico' => $request->has('publico') ? true : false,
            ];
            
            $result = $this->dataService->updateRequerimiento($id, $data);
            
            return redirect()->route('requerimientos.index')
                ->with('success', 'Requerimiento actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar requerimiento: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un requerimiento
     */
    public function destroy($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $result = $this->dataService->deleteRequerimiento($id);
            
            return redirect()->route('requerimientos.index')
                ->with('success', 'Requerimiento eliminado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el requerimiento: ' . $e->getMessage());
        }
    }
}
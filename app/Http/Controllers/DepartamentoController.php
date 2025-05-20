<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\DepartamentoService;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    protected $departamentoService;

    public function __construct(DepartamentoService $departamentoService)
    {
        $this->departamentoService = $departamentoService;
    }

    /**
     * Muestra la lista de departamentos
     */
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $departamentos = $this->departamentoService->getAllDepartamentos();
            
            return view('departamentos.index', [
                'departamentos' => $departamentos,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar departamentos: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear un nuevo departamento
     */
    public function create()
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        return view('departamentos.create', [
            'nombre' => session('user_nombre')
        ]);
    }

    /**
     * Almacena un nuevo departamento
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            $result = $this->departamentoService->createDepartamento($request->nombre);
            
            return redirect()->route('departamentos.index')
                ->with('success', 'Departamento creado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Controller error al crear departamento: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear el departamento: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar un departamento
     */
    public function edit($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $departamento = $this->departamentoService->getDepartamentoById($id);
            
            if (!$departamento) {
                return redirect()->route('departamentos.index')
                    ->with('error', 'Departamento no encontrado.');
            }
            
            return view('departamentos.edit', [
                'departamento' => $departamento,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar el departamento: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un departamento
     */
    public function update(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        try {
            $result = $this->departamentoService->updateDepartamento($id, $request->nombre);
            
            return redirect()->route('departamentos.index')
                ->with('success', 'Departamento actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al actualizar el departamento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un departamento
     */
    public function destroy($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $result = $this->departamentoService->deleteDepartamento($id);
            
            return redirect()->route('departamentos.index')
                ->with('success', 'Departamento eliminado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar el departamento: ' . $e->getMessage());
        }
    }
}
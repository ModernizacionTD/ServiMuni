<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\UnidadService;
use App\Services\DepartamentoService;
use App\Services\FuncionarioService;
use Illuminate\Http\Request;

class UnidadController extends Controller
{
    protected $unidadService;
    protected $departamentoService;
    protected $funcionarioService;

    public function __construct(
        UnidadService $unidadService,
        DepartamentoService $departamentoService,
        FuncionarioService $funcionarioService
    ) {
        $this->unidadService = $unidadService;
        $this->departamentoService = $departamentoService;
        $this->funcionarioService = $funcionarioService;
    }

    /**
     * Muestra la lista de unidades
     */
    public function index()
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $unidades = $this->unidadService->getAllUnidades();
            $departamentos = $this->departamentoService->getAllDepartamentos();
            
            return view('unidades.index', [
                'unidades' => $unidades,
                'departamentos' => $departamentos,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar unidades: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear una nueva unidad
     */
    public function create()
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        if (session('user_rol') !== 'admin' && session('user_rol') !== 'desarrollador') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        try {
            $departamentos = $this->departamentoService->getAllDepartamentos();
            // No cargar funcionarios aquí, se cargarán dinámicamente por departamento
            
            return view('unidades.create', [
                'departamentos' => $departamentos,
                'funcionarios' => [], // Array vacío inicialmente
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * NUEVA RUTA API: Obtener funcionarios por departamento
     */
    public function getFuncionariosByDepartamento($departamentoId)
    {
        try {
            // Obtener todos los funcionarios
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            
            // Filtrar funcionarios del departamento específico y que no tengan unidad asignada
            $funcionariosFiltrados = array_filter($funcionarios, function($funcionario) use ($departamentoId) {
                return isset($funcionario['departamento_id']) && 
                       $funcionario['departamento_id'] == $departamentoId &&
                       empty($funcionario['unidad_id']); // Solo funcionarios sin unidad
            });
            
            // Reindexar el array
            $funcionariosFiltrados = array_values($funcionariosFiltrados);
            
            return response()->json($funcionariosFiltrados);
        } catch (\Exception $e) {
            \Log::error('Error al obtener funcionarios por departamento: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar funcionarios'], 500);
        }
    }

    /**
     * Almacena una nueva unidad
     */
    public function store(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        if (session('user_rol') !== 'admin' && session('user_rol') !== 'desarrollador') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required',
            'funcionarios' => 'array',
        ]);

        // Validación manual para departamento_id
        $departamentos = $this->departamentoService->getAllDepartamentos();
        $departamentoIds = array_column($departamentos, 'id');
        if (!in_array($request->departamento_id, $departamentoIds)) {
            return back()->withInput()->with('error', 'El departamento seleccionado no es válido.');
        }

        // Validación manual para funcionarios - MEJORADA para verificar departamento
        if ($request->has('funcionarios') && is_array($request->funcionarios)) {
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            
            foreach ($request->funcionarios as $funcionarioId) {
                // Buscar el funcionario
                $funcionario = null;
                foreach ($funcionarios as $f) {
                    if ($f['id'] == $funcionarioId) {
                        $funcionario = $f;
                        break;
                    }
                }
                
                if (!$funcionario) {
                    return back()->withInput()->with('error', 'Uno o más funcionarios seleccionados no son válidos.');
                }
                
                // Verificar que el funcionario pertenezca al mismo departamento
                if ($funcionario['departamento_id'] != $request->departamento_id) {
                    return back()->withInput()->with('error', 'Solo puedes asignar funcionarios que pertenezcan al mismo departamento que la unidad.');
                }
                
                // Verificar que el funcionario no tenga unidad asignada
                if (!empty($funcionario['unidad_id'])) {
                    return back()->withInput()->with('error', "El funcionario {$funcionario['nombre']} ya está asignado a otra unidad.");
                }
            }
        }

        try {
            \Log::info('Creando unidad con datos:', $request->all());
            
            // Crear la unidad
            $data = [
                'nombre' => $request->nombre,
                'departamento_id' => $request->departamento_id,
            ];
            
            $unidad = $this->unidadService->createUnidad($data);
            \Log::info('Unidad creada:', $unidad);
            
            // Asignar funcionarios a la unidad
            if ($request->has('funcionarios') && is_array($request->funcionarios)) {
                foreach ($request->funcionarios as $funcionarioId) {
                    $this->funcionarioService->asignarUnidad($funcionarioId, $unidad['id_unidad']);
                    \Log::info("Funcionario $funcionarioId asignado a unidad {$unidad['id_unidad']}");
                }
            }
            
            return redirect()->route('unidades.index')
                ->with('success', 'Unidad creada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al crear unidad: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar una unidad
     */
    public function edit($id)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        if (session('user_rol') !== 'admin' && session('user_rol') !== 'desarrollador') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }
        
        try {
            $unidad = $this->unidadService->getUnidadById($id);
            
            if (!$unidad) {
                return redirect()->route('unidades.index')
                    ->with('error', 'Unidad no encontrada.');
            }
            
            $departamentos = $this->departamentoService->getAllDepartamentos();
            
            // Para edición, obtener funcionarios del mismo departamento
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            $funcionariosDepartamento = array_filter($funcionarios, function($funcionario) use ($unidad) {
                return isset($funcionario['departamento_id']) && 
                       $funcionario['departamento_id'] == $unidad['departamento_id'] &&
                       (empty($funcionario['unidad_id']) || $funcionario['unidad_id'] == $unidad['id_unidad']);
            });
            
            return view('unidades.edit', [
                'unidad' => $unidad,
                'departamentos' => $departamentos,
                'funcionarios' => array_values($funcionariosDepartamento),
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cargar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza una unidad
     */
    public function update(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        if (session('user_rol') !== 'admin' && session('user_rol') !== 'desarrollador') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'departamento_id' => 'required',
            'funcionarios' => 'array',
        ]);

        // Validación manual para departamento_id
        $departamentos = $this->departamentoService->getAllDepartamentos();
        $departamentoIds = array_column($departamentos, 'id');
        if (!in_array($request->departamento_id, $departamentoIds)) {
            return back()->withInput()->with('error', 'El departamento seleccionado no es válido.');
        }

        // Validación manual para funcionarios - MEJORADA
        if ($request->has('funcionarios') && is_array($request->funcionarios)) {
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            
            foreach ($request->funcionarios as $funcionarioId) {
                $funcionario = null;
                foreach ($funcionarios as $f) {
                    if ($f['id'] == $funcionarioId) {
                        $funcionario = $f;
                        break;
                    }
                }
                
                if (!$funcionario) {
                    return back()->withInput()->with('error', 'Uno o más funcionarios seleccionados no son válidos.');
                }
                
                // Verificar que el funcionario pertenezca al mismo departamento
                if ($funcionario['departamento_id'] != $request->departamento_id) {
                    return back()->withInput()->with('error', 'Solo puedes asignar funcionarios que pertenezcan al mismo departamento que la unidad.');
                }
                
                // Verificar que el funcionario no tenga otra unidad asignada (excepto la actual)
                if (!empty($funcionario['unidad_id']) && $funcionario['unidad_id'] != $id) {
                    return back()->withInput()->with('error', "El funcionario {$funcionario['nombre']} ya está asignado a otra unidad.");
                }
            }
        }

        try {
            \Log::info('Actualizando unidad con datos:', $request->all());
            
            // Actualizar la unidad
            $data = [
                'nombre' => $request->nombre,
                'departamento_id' => $request->departamento_id,
            ];
            
            $unidad = $this->unidadService->updateUnidad($id, $data);
            \Log::info('Unidad actualizada:', $unidad);
            
            // Eliminar asignaciones actuales
            $this->funcionarioService->desasignarTodosDeUnidad($id);
            
            // Asignar funcionarios a la unidad
            if ($request->has('funcionarios') && is_array($request->funcionarios)) {
                foreach ($request->funcionarios as $funcionarioId) {
                    $this->funcionarioService->asignarUnidad($funcionarioId, $id);
                    \Log::info("Funcionario $funcionarioId asignado a unidad $id");
                }
            }
            
            return redirect()->route('unidades.index')
                ->with('success', 'Unidad actualizada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar unidad: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una unidad
     */
    public function destroy($id)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        if (session('user_rol') !== 'admin' && session('user_rol') !== 'desarrollador') {
            return redirect()->route('dashboard')
                ->with('error', 'No tienes permisos para realizar esta acción.');
        }
        
        try {
            // Primero desasignar todos los funcionarios de esta unidad
            $this->funcionarioService->desasignarTodosDeUnidad($id);
            
            // Luego eliminar la unidad
            $result = $this->unidadService->deleteUnidad($id);
            
            return redirect()->route('unidades.index')
                ->with('success', 'Unidad eliminada correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al eliminar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * API para obtener técnicos por unidad
     */
    public function getTecnicosByUnidad($id)
    {
        try {
            $tecnicos = $this->unidadService->getTecnicosByUnidad($id);
            return response()->json($tecnicos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener técnicos'], 500);
        }
    }

    /**
 * API para obtener TODOS los funcionarios de una unidad (no solo técnicos)
 */
public function getFuncionariosByUnidad($id)
{
    try {
        \Log::info("Obteniendo funcionarios de la unidad ID: $id");
        
        // Usar el servicio de funcionarios para obtener funcionarios por unidad
        $funcionarios = $this->funcionarioService->getFuncionariosByUnidad($id);
        
        \Log::info("Funcionarios encontrados: " . count($funcionarios));
        
        return response()->json($funcionarios);
    } catch (\Exception $e) {
        \Log::error('Error al obtener funcionarios por unidad: ' . $e->getMessage());
        return response()->json(['error' => 'Error al obtener funcionarios'], 500);
    }
}
}
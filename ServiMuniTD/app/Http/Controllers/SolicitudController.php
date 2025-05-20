<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SolicitudService;
use App\Services\UsuarioService;
use App\Services\RequerimientoService;
use App\Services\FuncionarioService;
use App\Services\DepartamentoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SolicitudController extends Controller
{
    protected $solicitudService;
    protected $usuarioService;
    protected $requerimientoService;
    protected $funcionarioService;
    protected $departamentoService;

    public function __construct(
        SolicitudService $solicitudService,
        UsuarioService $usuarioService,
        RequerimientoService $requerimientoService,
        FuncionarioService $funcionarioService,
        DepartamentoService $departamentoService
    ) {
        $this->solicitudService = $solicitudService;
        $this->usuarioService = $usuarioService;
        $this->requerimientoService = $requerimientoService;
        $this->funcionarioService = $funcionarioService;
        $this->departamentoService = $departamentoService;
    }

    /**
     * Muestra una lista de todas las solicitudes
     */
    public function index(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $filtro = $request->input('filtro', 'todas');
            $busqueda = $request->input('busqueda');
            
            // Obtener todas las solicitudes
            $todasSolicitudes = $this->solicitudService->getAll();
            
            // Aplicar filtro por estado
            if ($filtro !== 'todas') {
                $todasSolicitudes = array_filter($todasSolicitudes, function($solicitud) use ($filtro) {
                    return $solicitud['estado'] === $filtro;
                });
            }
            
            // Aplicar búsqueda por texto
            if ($busqueda) {
                $todasSolicitudes = array_filter($todasSolicitudes, function($solicitud) use ($busqueda) {
                    // Buscar en varios campos
                    return (
                        (isset($solicitud['id_solicitud']) && stripos($solicitud['id_solicitud'], $busqueda) !== false) ||
                        (isset($solicitud['descripcion']) && stripos($solicitud['descripcion'], $busqueda) !== false) ||
                        (isset($solicitud['localidad']) && stripos($solicitud['localidad'], $busqueda) !== false) ||
                        (isset($solicitud['rut_usuario']) && stripos($solicitud['rut_usuario'], $busqueda) !== false) ||
                        (isset($solicitud['ubicacion']) && stripos($solicitud['ubicacion'], $busqueda) !== false)
                    );
                });
            }
            
            // Reindexar array
            $solicitudes = array_values($todasSolicitudes);
            
            // Obtener información complementaria
            $requerimientos = [];
            $usuarios = [];
            
            $todosRequerimientos = $this->requerimientoService->getAll();
            foreach ($todosRequerimientos as $requerimiento) {
                $requerimientos[$requerimiento['id_requerimiento']] = $requerimiento;
            }
            
            // Obtener info de usuarios solo para las solicitudes filtradas
            $rutsUnicos = array_unique(array_column($solicitudes, 'rut_usuario'));
            foreach ($rutsUnicos as $rut) {
                if (!empty($rut)) {
                    $usuario = $this->usuarioService->getByRut($rut);
                    if ($usuario) {
                        $usuarios[$rut] = $usuario;
                    }
                }
            }
            
            return view('solicitudes.index', [
                'solicitudes' => $solicitudes,
                'requerimientos' => $requerimientos,
                'usuarios' => $usuarios,
                'filtro' => $filtro,
                'busqueda' => $busqueda,
                'totalSolicitudes' => count($solicitudes),
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar solicitudes: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar solicitudes: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para crear una nueva solicitud
     */
    public function create(Request $request, $rut = null)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $usuario = null;
        
        // Si se proporciona un RUT, buscar el usuario
        if ($rut) {
            $usuario = $this->usuarioService->getByRut($rut);
            
            if (!$usuario) {
                return redirect()->route('buscar.usuario')
                    ->with('error', 'Usuario no encontrado. Por favor, búsquelo nuevamente.');
            }
        }
        
        try {
            // Obtener todos los departamentos para el select
            $departamentos = $this->departamentoService->getAll();
            // Obtener todos los requerimientos para el select
            $requerimientos = $this->requerimientoService->getAll();
            
            return view('solicitudes.create', [
                'departamentos' => $departamentos,  // Añadimos esta línea
                'requerimientos' => $requerimientos,
                'usuario' => $usuario,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al cargar formulario: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar formulario: ' . $e->getMessage());
        }
    }

    /**
     * Almacena una nueva solicitud
     */
    public function store(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'rut_usuario' => 'required|string|max:14',
            'requerimiento_id' => 'required|integer',
            'descripcion' => 'required|string|max:255',
            'localidad' => 'required|string|max:30',
            'tipo_ubicacion' => 'required|string|max:20',
            'ubicacion' => 'required|string|max:50',
            'providencia' => 'nullable|integer',
            'imagen' => 'nullable|image|max:5120', // Máx 5MB
        ]);

        try {
            // Verificar si el usuario existe
            $usuario = $this->usuarioService->getByRut($request->rut_usuario);
            
            if (!$usuario) {
                return back()->withInput()
                    ->with('error', 'El usuario con RUT ' . $request->rut_usuario . ' no existe en el sistema.');
            }
            
            // Preparar los datos básicos
            $data = [
                'rut_usuario' => $request->rut_usuario,
                'rut_ingreso' => session('user_id'),
                'requerimiento_id' => $request->requerimiento_id,
                'descripcion' => $request->descripcion,
                'localidad' => $request->localidad,
                'tipo_ubicacion' => $request->tipo_ubicacion,
                'ubicacion' => $request->ubicacion,
                'providencia' => $request->providencia,
                'estado' => 'Pendiente',
                'etapa' => 'Ingreso',
                'fecha_inicio' => date('Y-m-d'),
            ];
            
            // Manejar la carga de imagen si está presente
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                
                // Guardar imagen en storage/app/public/solicitudes
                $rutaImagen = $imagen->storeAs('public/solicitudes', $nombreImagen);
                
                // Guardar nombre de archivo en la BD
                $data['imagen'] = $nombreImagen;
            }
            
            // Crear la solicitud
            $solicitud = $this->solicitudService->create($data);
            
            return redirect()->route('buscar.usuario', ['rut' => $request->rut_usuario])
                ->with('success', 'Solicitud creada correctamente con ID: ' . $solicitud['id_solicitud']);
        } catch (\Exception $e) {
            \Log::error('Error al crear solicitud: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Muestra detalles de una solicitud específica
     */
    public function show($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            // Obtener la solicitud
            $solicitud = $this->solicitudService->getById($id);
            
            if (!$solicitud) {
                return redirect()->route('solicitudes.index')
                    ->with('error', 'Solicitud no encontrada.');
            }
            
            // Obtener información relacionada
            $usuario = $this->usuarioService->getByRut($solicitud['rut_usuario']);
            
            // Obtener el requerimiento asociado si existe
            $requerimiento = null;
            if (!empty($solicitud['requerimiento_id'])) {
                $requerimiento = $this->requerimientoService->getById($solicitud['requerimiento_id']);
            }
            
            // Obtener información de los funcionarios asignados
            $funcionarioIngreso = null;
            $funcionarioGestor = null;
            $funcionarioTecnico = null;
            
            if (!empty($solicitud['rut_ingreso'])) {
                $funcionarioIngreso = $this->funcionarioService->getById($solicitud['rut_ingreso']);
            }
            
            if (!empty($solicitud['rut_gestor'])) {
                $funcionarioGestor = $this->funcionarioService->getById($solicitud['rut_gestor']);
            }
            
            if (!empty($solicitud['rut_tecnico'])) {
                $funcionarioTecnico = $this->funcionarioService->getById($solicitud['rut_tecnico']);
            }
            
            return view('solicitudes.show', [
                'solicitud' => $solicitud,
                'usuario' => $usuario,
                'requerimiento' => $requerimiento,
                'funcionarioIngreso' => $funcionarioIngreso,
                'funcionarioGestor' => $funcionarioGestor,
                'funcionarioTecnico' => $funcionarioTecnico,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al mostrar solicitud: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el formulario para editar una solicitud
     */
    public function edit($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            // Obtener la solicitud
            $solicitud = $this->solicitudService->getById($id);
            
            if (!$solicitud) {
                return redirect()->route('solicitudes.index')
                    ->with('error', 'Solicitud no encontrada.');
            }
            
            // Obtener información relacionada
            $usuario = $this->usuarioService->getByRut($solicitud['rut_usuario']);
            
            // Obtener todos los requerimientos para el select
            $requerimientos = $this->requerimientoService->getAll();
            
            // Obtener todos los funcionarios para los selects de asignación
            $funcionarios = $this->funcionarioService->getAll();
            
            return view('solicitudes.edit', [
                'solicitud' => $solicitud,
                'usuario' => $usuario,
                'requerimientos' => $requerimientos,
                'funcionarios' => $funcionarios,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al editar solicitud: ' . $e->getMessage());
            return back()->with('error', 'Error al cargar el formulario: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza una solicitud existente
     */
    public function update(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'requerimiento_id' => 'required|integer',
            'descripcion' => 'required|string|max:255',
            'localidad' => 'required|string|max:30',
            'tipo_ubicacion' => 'required|string|max:20',
            'ubicacion' => 'required|string|max:50',
            'providencia' => 'nullable|integer',
            'estado' => 'required|string|max:30',
            'etapa' => 'required|string|max:30',
            'fecha_estimada_op' => 'nullable|date',
            'rut_gestor' => 'nullable|string|max:14',
            'rut_tecnico' => 'nullable|string|max:14',
            'imagen' => 'nullable|image|max:5120', // Máx 5MB
        ]);
        
        try {
            // Obtener la solicitud actual
            $solicitudActual = $this->solicitudService->getById($id);
            
            if (!$solicitudActual) {
                return redirect()->route('solicitudes.index')
                    ->with('error', 'Solicitud no encontrada.');
            }
            
            // Preparar datos para actualizar
            $data = [
                'requerimiento_id' => $request->requerimiento_id,
                'descripcion' => $request->descripcion,
                'localidad' => $request->localidad,
                'tipo_ubicacion' => $request->tipo_ubicacion,
                'ubicacion' => $request->ubicacion,
                'providencia' => $request->providencia,
                'estado' => $request->estado,
                'etapa' => $request->etapa,
                'fecha_estimada_op' => $request->fecha_estimada_op,
                'rut_gestor' => $request->rut_gestor,
                'rut_tecnico' => $request->rut_tecnico,
            ];
            
            // Actualizar fecha de derivación si se asigna un gestor y no tenía antes
            if (!empty($request->rut_gestor) && empty($solicitudActual['rut_gestor'])) {
                $data['fecha_derivacion'] = date('Y-m-d');
            }
            
            // Actualizar fecha de término si se completa y no tenía antes
            if ($request->estado === 'Completado' && $solicitudActual['estado'] !== 'Completado') {
                $data['fecha_termino'] = date('Y-m-d');
            }
            
            // Manejar la carga de imagen si está presente
            if ($request->hasFile('imagen')) {
                $imagen = $request->file('imagen');
                $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
                
                // Guardar imagen en storage/app/public/solicitudes
                $rutaImagen = $imagen->storeAs('public/solicitudes', $nombreImagen);
                
                // Guardar nombre de archivo en la BD
                $data['imagen'] = $nombreImagen;
                
                // Eliminar imagen anterior si existe
                if (!empty($solicitudActual['imagen'])) {
                    $rutaImagenAnterior = 'public/solicitudes/' . $solicitudActual['imagen'];
                    if (Storage::exists($rutaImagenAnterior)) {
                        Storage::delete($rutaImagenAnterior);
                    }
                }
            }
            
            // Actualizar la solicitud
            $solicitud = $this->solicitudService->update($id, $data);
            
            return redirect()->route('solicitudes.show', $id)
                ->with('success', 'Solicitud actualizada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar solicitud: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una solicitud
     */
    public function destroy($id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            // Obtener la solicitud para poder eliminar la imagen asociada
            $solicitud = $this->solicitudService->getById($id);
            
            if (!$solicitud) {
                return redirect()->route('solicitudes.index')
                    ->with('error', 'Solicitud no encontrada.');
            }
            
            // Eliminar imagen si existe
            if (!empty($solicitud['imagen'])) {
                $rutaImagen = 'public/solicitudes/' . $solicitud['imagen'];
                if (Storage::exists($rutaImagen)) {
                    Storage::delete($rutaImagen);
                }
            }
            
            // Eliminar la solicitud
            $this->solicitudService->delete($id);
            
            return redirect()->route('solicitudes.index')
                ->with('success', 'Solicitud eliminada correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al eliminar solicitud: ' . $e->getMessage());
            return back()->with('error', 'Error al eliminar la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Asignar gestor a una solicitud
     */
    public function asignarGestor(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'rut_gestor' => 'required|string|max:14',
        ]);
        
        try {
            // Verificar si el funcionario existe
            $funcionario = $this->funcionarioService->getById($request->rut_gestor);
            
            if (!$funcionario) {
                return back()->with('error', 'El funcionario seleccionado no existe.');
            }
            
            // Asignar gestor
            $solicitud = $this->solicitudService->asignarGestor($id, $request->rut_gestor);
            
            return redirect()->route('solicitudes.show', $id)
                ->with('success', 'Gestor asignado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al asignar gestor: ' . $e->getMessage());
            return back()->with('error', 'Error al asignar gestor: ' . $e->getMessage());
        }
    }

    /**
     * Asignar técnico a una solicitud
     */
    public function asignarTecnico(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'rut_tecnico' => 'required|string|max:14',
        ]);
        
        try {
            // Verificar si el funcionario existe
            $funcionario = $this->funcionarioService->getById($request->rut_tecnico);
            
            if (!$funcionario) {
                return back()->with('error', 'El funcionario seleccionado no existe.');
            }
            
            // Asignar técnico
            $solicitud = $this->solicitudService->asignarTecnico($id, $request->rut_tecnico);
            
            return redirect()->route('solicitudes.show', $id)
                ->with('success', 'Técnico asignado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al asignar técnico: ' . $e->getMessage());
            return back()->with('error', 'Error al asignar técnico: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar estado de una solicitud
     */
    public function actualizarEstado(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'estado' => 'required|string|max:30',
            'etapa' => 'required|string|max:30',
        ]);
        
        try {
            // Actualizar estado
            $solicitud = $this->solicitudService->updateEstado($id, $request->estado, $request->etapa);
            
            return redirect()->route('solicitudes.show', $id)
                ->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado: ' . $e->getMessage());
            return back()->with('error', 'Error al actualizar estado: ' . $e->getMessage());
        }
    }

    /**
     * Establecer fecha estimada de operación
     */
    public function establecerFechaEstimada(Request $request, $id)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'fecha_estimada_op' => 'required|date',
        ]);
        
        try {
            // Actualizar fecha estimada
            $data = [
                'fecha_estimada_op' => $request->fecha_estimada_op,
            ];
            
            $solicitud = $this->solicitudService->update($id, $data);
            
            return redirect()->route('solicitudes.show', $id)
                ->with('success', 'Fecha estimada establecida correctamente.');
        } catch (\Exception $e) {
            \Log::error('Error al establecer fecha estimada: ' . $e->getMessage());
            return back()->with('error', 'Error al establecer fecha estimada: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar reporte de solicitudes
     */
    public function reporte(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!session('user_id')) {
            return redirect()->route('login');
        }
        
        try {
            $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
            $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
            $estado = $request->input('estado');
            $localidad = $request->input('localidad');
            
            // Obtener todas las solicitudes
            $todasSolicitudes = $this->solicitudService->getAll();
            
            // Filtrar por fecha
            $solicitudesFiltradas = array_filter($todasSolicitudes, function($solicitud) use ($fechaInicio, $fechaFin) {
                if (empty($solicitud['fecha_inicio'])) {
                    return false;
                }
                
                $fechaSolicitud = Carbon::parse($solicitud['fecha_inicio']);
                return $fechaSolicitud->between($fechaInicio, $fechaFin);
            });
            
            // Filtrar por estado si se especifica
            if ($estado) {
                $solicitudesFiltradas = array_filter($solicitudesFiltradas, function($solicitud) use ($estado) {
                    return $solicitud['estado'] === $estado;
                });
            }
            
            // Filtrar por localidad si se especifica
            if ($localidad) {
                $solicitudesFiltradas = array_filter($solicitudesFiltradas, function($solicitud) use ($localidad) {
                    return $solicitud['localidad'] === $localidad;
                });
            }
            
            // Reindexar array
            $solicitudes = array_values($solicitudesFiltradas);
            
            // Obtener estadísticas
            $estadisticas = $this->calcularEstadisticas($solicitudes);
            
            // Obtener lista única de localidades para filtro
            $localidades = array_unique(array_column($todasSolicitudes, 'localidad'));
            $localidades = array_filter($localidades);
            sort($localidades);
            
            return view('solicitudes.reporte', [
                'solicitudes' => $solicitudes,
                'estadisticas' => $estadisticas,
                'localidades' => $localidades,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'estadoSeleccionado' => $estado,
                'localidadSeleccionada' => $localidad,
                'nombre' => session('user_nombre')
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al generar reporte: ' . $e->getMessage());
            return back()->with('error', 'Error al generar reporte: ' . $e->getMessage());
        }
    }

    /**
     * Calcular estadísticas para reporte
     */
    private function calcularEstadisticas($solicitudes)
    {
        $total = count($solicitudes);
        $porEstado = [];
        $porEtapa = [];
        $porLocalidad = [];
        $porRequerimiento = [];
        $tiemposRespuesta = [];
        
        // Contar por estado, etapa, localidad y calcular tiempos de respuesta
        foreach ($solicitudes as $solicitud) {
            $estado = $solicitud['estado'] ?? 'Sin estado';
            $etapa = $solicitud['etapa'] ?? 'Sin etapa';
            $localidad = $solicitud['localidad'] ?? 'Sin localidad';
            $requerimientoId = $solicitud['requerimiento_id'] ?? 'Sin requerimiento';
            
            // Contar por estado
            if (!isset($porEstado[$estado])) {
                $porEstado[$estado] = 0;
            }
            $porEstado[$estado]++;
            
            // Contar por etapa
            if (!isset($porEtapa[$etapa])) {
                $porEtapa[$etapa] = 0;
            }
            $porEtapa[$etapa]++;
            
            // Contar por localidad
            if (!isset($porLocalidad[$localidad])) {
                $porLocalidad[$localidad] = 0;
            }
            $porLocalidad[$localidad]++;
            
            // Contar por requerimiento
            if (!isset($porRequerimiento[$requerimientoId])) {
                $porRequerimiento[$requerimientoId] = 0;
            }
            $porRequerimiento[$requerimientoId]++;
            
            // Calcular tiempos de respuesta para solicitudes terminadas
            if (!empty($solicitud['fecha_termino']) && !empty($solicitud['fecha_inicio'])) {
                $fechaInicio = Carbon::parse($solicitud['fecha_inicio']);
                $fechaTermino = Carbon::parse($solicitud['fecha_termino']);
                
                if ($fechaTermino->gte($fechaInicio)) {
                    $dias = $fechaInicio->diffInDays($fechaTermino);
                    $tiemposRespuesta[] = $dias;
                }
            }
        }
        
        // Calcular promedios y porcentajes
        $promedioTiempoRespuesta = !empty($tiemposRespuesta) ? array_sum($tiemposRespuesta) / count($tiemposRespuesta) : 0;
        
        // Ordenar arrays por valor (de mayor a menor)
        arsort($porEstado);
        arsort($porEtapa);
        arsort($porLocalidad);
        arsort($porRequerimiento);
        
        return [
            'total' => $total,
            'por_estado' => $porEstado,
            'por_etapa' => $porEtapa,
            'por_localidad' => $porLocalidad,
            'por_requerimiento' => $porRequerimiento,
            'promedio_tiempo_respuesta' => $promedioTiempoRespuesta,
            'min_tiempo_respuesta' => !empty($tiemposRespuesta) ? min($tiemposRespuesta) : 0,
            'max_tiempo_respuesta' => !empty($tiemposRespuesta) ? max($tiemposRespuesta) : 0,
        ];
    }
}
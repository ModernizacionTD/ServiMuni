<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SolicitudService;
use App\Services\UsuarioService;
use App\Services\RequerimientoService;
use App\Services\DepartamentoService;
use App\Services\FuncionarioService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BandejaController extends Controller
{
    protected $solicitudService;
    protected $usuarioService;
    protected $requerimientoService;
    protected $departamentoService;
    protected $funcionarioService;

    public function __construct(
        SolicitudService $solicitudService,
        UsuarioService $usuarioService,
        RequerimientoService $requerimientoService,
        DepartamentoService $departamentoService,
        FuncionarioService $funcionarioService
    ) {
        $this->solicitudService = $solicitudService;
        $this->usuarioService = $usuarioService;
        $this->requerimientoService = $requerimientoService;
        $this->departamentoService = $departamentoService;
        $this->funcionarioService = $funcionarioService;
    }

    /**
     * Muestra la bandeja de solicitudes filtradas por rol y departamento
     */
    public function index(Request $request)
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        try {
            // Obtener información del usuario actual
            $usuarioActual = $this->funcionarioService->getFuncionarioById(session('user_id'));
            
            if (!$usuarioActual) {
                return redirect()->route('login')->with('error', 'Usuario no encontrado.');
            }

            $rolUsuario = $usuarioActual['rol'];
            $departamentoUsuario = $usuarioActual['departamento_id'] ?? null;

            \Log::info("Usuario: {$usuarioActual['nombre']}, Rol: {$rolUsuario}, Departamento: {$departamentoUsuario}");

            // Obtener filtros de la request
            $filtroEstado = $request->input('estado', '');
            $filtroEtapa = $request->input('etapa', '');
            $filtroPrioridad = $request->input('prioridad', '');
            $filtroFecha = $request->input('fecha', '');
            $busqueda = $request->input('busqueda', '');

            // Obtener todas las solicitudes
            $todasSolicitudes = $this->solicitudService->getAllSolicitudes();
            
            // Filtrar solicitudes según el rol y departamento
            $solicitudesFiltradas = $this->filtrarSolicitudesPorRolYDepartamento(
                $todasSolicitudes, 
                $rolUsuario, 
                $departamentoUsuario
            );

            // Aplicar filtros adicionales
            $solicitudesFiltradas = $this->aplicarFiltrosAdicionales(
                $solicitudesFiltradas,
                $filtroEstado,
                $filtroEtapa,
                $filtroPrioridad,
                $filtroFecha,
                $busqueda
            );

            // Ordenar por fecha de ingreso (más recientes primero)
            $solicitudesFiltradas = $this->ordenarSolicitudes($solicitudesFiltradas);

            // Obtener información complementaria
            $requerimientos = $this->obtenerRequerimientos();
            $departamentos = $this->obtenerDepartamentos();
            $usuarios = $this->obtenerUsuarios($solicitudesFiltradas);
            $funcionarios = $this->obtenerFuncionarios();

            // Calcular estadísticas para la bandeja
            $estadisticas = $this->calcularEstadisticasBandeja($solicitudesFiltradas, $todasSolicitudes);

            // Obtener nombre del departamento del usuario
            $nombreDepartamento = 'Todos los departamentos';
            if ($departamentoUsuario && isset($departamentos[$departamentoUsuario])) {
                $nombreDepartamento = $departamentos[$departamentoUsuario]['nombre'];
            }

            return view('bandeja.index', [
                'solicitudes' => $solicitudesFiltradas,
                'requerimientos' => $requerimientos,
                'departamentos' => $departamentos,
                'usuarios' => $usuarios,
                'funcionarios' => $funcionarios,
                'estadisticas' => $estadisticas,
                'usuarioActual' => $usuarioActual,
                'nombreDepartamento' => $nombreDepartamento,
                'filtros' => [
                    'estado' => $filtroEstado,
                    'etapa' => $filtroEtapa,
                    'prioridad' => $filtroPrioridad,
                    'fecha' => $filtroFecha,
                    'busqueda' => $busqueda
                ],
                'nombre' => session('user_nombre'),
                'rol' => $rolUsuario
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en BandejaController: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al cargar la bandeja: ' . $e->getMessage());
        }
    }

    /**
     * Filtra solicitudes según el rol y departamento del usuario
     */
    private function filtrarSolicitudesPorRolYDepartamento($solicitudes, $rol, $departamentoId)
    {
        // Los administradores ven todas las solicitudes
        if ($rol === 'admin') {
            return $solicitudes;
        }

        // Si no tiene departamento asignado, no ve solicitudes
        if (!$departamentoId) {
            \Log::warning("Usuario sin departamento asignado, no verá solicitudes");
            return [];
        }

        // Obtener requerimientos del departamento del usuario
        $requerimientos = $this->requerimientoService->getAllRequerimientos();
        $requerimientosDepartamento = array_filter($requerimientos, function($req) use ($departamentoId) {
            return isset($req['departamento_id']) && $req['departamento_id'] == $departamentoId;
        });

        $idsRequerimientos = array_column($requerimientosDepartamento, 'id_requerimiento');

        \Log::info("Departamento {$departamentoId} tiene requerimientos: " . implode(', ', $idsRequerimientos));

        // Filtrar solicitudes que pertenecen a los requerimientos del departamento
        $solicitudesFiltradas = array_filter($solicitudes, function($solicitud) use ($idsRequerimientos, $rol) {
            $requerimientoId = $solicitud['requerimiento_id'] ?? null;
            
            // Verificar si la solicitud pertenece al departamento
            $perteneceAlDepartamento = in_array($requerimientoId, $idsRequerimientos);
            
            // Filtros adicionales según el rol
            switch ($rol) {
                case 'orientador':
                    // Los orientadores ven todas las solicitudes de su departamento
                    return $perteneceAlDepartamento;
                    
                case 'gestor':
                    // Los gestores ven solicitudes de su departamento que están asignadas a ellos o sin asignar
                    return $perteneceAlDepartamento && (
                        empty($solicitud['rut_gestor']) || 
                        $solicitud['rut_gestor'] == session('user_id')
                    );
                    
                case 'tecnico':
                    // Los técnicos ven solicitudes asignadas a ellos
                    return $perteneceAlDepartamento && (
                        $solicitud['rut_tecnico'] == session('user_id')
                    );
                    
                default:
                    // Otros roles ven solicitudes de su departamento
                    return $perteneceAlDepartamento;
            }
        });

        \Log::info("Solicitudes filtradas para rol {$rol}: " . count($solicitudesFiltradas));

        return array_values($solicitudesFiltradas);
    }

    /**
     * Aplica filtros adicionales a las solicitudes
     */
    private function aplicarFiltrosAdicionales($solicitudes, $estado, $etapa, $prioridad, $fecha, $busqueda)
    {
        // Filtro por estado
        if (!empty($estado)) {
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($estado) {
                return ($solicitud['estado'] ?? '') === $estado;
            });
        }

        // Filtro por etapa
        if (!empty($etapa)) {
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($etapa) {
                return ($solicitud['etapa'] ?? '') === $etapa;
            });
        }

        // Filtro por prioridad (basado en fecha estimada)
        if (!empty($prioridad)) {
            $hoy = Carbon::now();
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($prioridad, $hoy) {
                $fechaEstimada = $solicitud['fecha_estimada_op'] ?? null;
                
                if (!$fechaEstimada) return $prioridad === 'sin_fecha';
                
                $fechaEst = Carbon::parse($fechaEstimada);
                $diasRestantes = $hoy->diffInDays($fechaEst, false);
                
                switch ($prioridad) {
                    case 'urgente':
                        return $diasRestantes < 0; // Vencidas
                    case 'alta':
                        return $diasRestantes >= 0 && $diasRestantes <= 3;
                    case 'media':
                        return $diasRestantes > 3 && $diasRestantes <= 7;
                    case 'baja':
                        return $diasRestantes > 7;
                    default:
                        return true;
                }
            });
        }

        // Filtro por fecha
        if (!empty($fecha)) {
            $hoy = Carbon::now();
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($fecha, $hoy) {
                $fechaIngreso = $solicitud['fecha_ingreso'] ?? null;
                if (!$fechaIngreso) return false;
                
                $fechaIng = Carbon::parse($fechaIngreso);
                
                switch ($fecha) {
                    case 'hoy':
                        return $fechaIng->isToday();
                    case 'ayer':
                        return $fechaIng->isYesterday();
                    case 'semana':
                        return $fechaIng->isCurrentWeek();
                    case 'mes':
                        return $fechaIng->isCurrentMonth();
                    default:
                        return true;
                }
            });
        }

        // Filtro por búsqueda de texto
        if (!empty($busqueda)) {
            $busqueda = strtolower($busqueda);
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($busqueda) {
                $textoSolicitud = strtolower(implode(' ', [
                    $solicitud['id_solicitud'] ?? '',
                    $solicitud['descripcion'] ?? '',
                    $solicitud['localidad'] ?? '',
                    $solicitud['rut_usuario'] ?? '',
                    $solicitud['ubicacion'] ?? ''
                ]));
                
                return strpos($textoSolicitud, $busqueda) !== false;
            });
        }

        return array_values($solicitudes);
    }

    /**
     * Ordena las solicitudes por prioridad y fecha
     */
    private function ordenarSolicitudes($solicitudes)
    {
        usort($solicitudes, function($a, $b) {
            // Prioridad por estado (Pendiente y En proceso primero)
            $prioridadEstado = ['Pendiente' => 1, 'En proceso' => 2, 'Completado' => 3];
            $prioA = $prioridadEstado[$a['estado'] ?? ''] ?? 4;
            $prioB = $prioridadEstado[$b['estado'] ?? ''] ?? 4;
            
            if ($prioA !== $prioB) {
                return $prioA - $prioB;
            }
            
            // Si tienen el mismo estado, ordenar por fecha de ingreso (más recientes primero)
            $fechaA = $a['fecha_ingreso'] ?? '1900-01-01';
            $fechaB = $b['fecha_ingreso'] ?? '1900-01-01';
            
            return strcmp($fechaB, $fechaA);
        });

        return $solicitudes;
    }

    /**
     * Calcula estadísticas para la bandeja
     */
    private function calcularEstadisticasBandeja($solicitudesFiltradas, $todasSolicitudes)
    {
        $total = count($solicitudesFiltradas);
        $pendientes = 0;
        $enProceso = 0;
        $completadas = 0;
        $vencidas = 0;
        $hoy = Carbon::now();

        foreach ($solicitudesFiltradas as $solicitud) {
            $estado = $solicitud['estado'] ?? '';
            
            switch ($estado) {
                case 'Pendiente':
                    $pendientes++;
                    break;
                case 'En proceso':
                    $enProceso++;
                    break;
                case 'Completado':
                    $completadas++;
                    break;
            }

            // Verificar si está vencida
            $fechaEstimada = $solicitud['fecha_estimada_op'] ?? null;
            if ($fechaEstimada && $estado !== 'Completado') {
                $fechaEst = Carbon::parse($fechaEstimada);
                if ($fechaEst->isPast()) {
                    $vencidas++;
                }
            }
        }

        return [
            'total' => $total,
            'pendientes' => $pendientes,
            'en_proceso' => $enProceso,
            'completadas' => $completadas,
            'vencidas' => $vencidas,
            'porcentaje_completadas' => $total > 0 ? round(($completadas / $total) * 100) : 0
        ];
    }

    /**
     * Obtiene todos los requerimientos indexados por ID
     */
    private function obtenerRequerimientos()
    {
        $requerimientos = [];
        $todosRequerimientos = $this->requerimientoService->getAllRequerimientos();
        
        foreach ($todosRequerimientos as $requerimiento) {
            $requerimientos[$requerimiento['id_requerimiento']] = $requerimiento;
        }
        
        return $requerimientos;
    }

    /**
     * Obtiene todos los departamentos indexados por ID
     */
    private function obtenerDepartamentos()
    {
        $departamentos = [];
        $todosDepartamentos = $this->departamentoService->getAllDepartamentos();
        
        foreach ($todosDepartamentos as $departamento) {
            $departamentos[$departamento['id']] = $departamento;
        }
        
        return $departamentos;
    }

    /**
     * Obtiene usuarios necesarios para las solicitudes
     */
    private function obtenerUsuarios($solicitudes)
    {
        $usuarios = [];
        $rutsUnicos = array_unique(array_column($solicitudes, 'rut_usuario'));
        
        foreach ($rutsUnicos as $rut) {
            if (!empty($rut)) {
                try {
                    $usuario = $this->usuarioService->getUsuarioByRut($rut);
                    if ($usuario) {
                        $usuarios[$rut] = $usuario;
                    }
                } catch (\Exception $e) {
                    \Log::warning("No se pudo obtener usuario con RUT: $rut");
                }
            }
        }
        
        return $usuarios;
    }

    /**
     * Obtiene todos los funcionarios indexados por ID
     */
    private function obtenerFuncionarios()
    {
        $funcionarios = [];
        $todosFuncionarios = $this->funcionarioService->getAllFuncionarios();
        
        foreach ($todosFuncionarios as $funcionario) {
            $funcionarios[$funcionario['id']] = $funcionario;
        }
        
        return $funcionarios;
    }

    /**
     * Toma una solicitud (asignar al usuario actual)
     */
    public function tomarSolicitud(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        try {
            $usuarioActual = $this->funcionarioService->getFuncionarioById(session('user_id'));
            $rol = $usuarioActual['rol'];

            $data = [];
            
            // Asignar según el rol
            if ($rol === 'gestor') {
                $data['rut_gestor'] = session('user_id');
                $data['fecha_derivacion'] = date('Y-m-d');
                $data['etapa'] = 'Asignada';
            } elseif ($rol === 'tecnico') {
                $data['rut_tecnico'] = session('user_id');
                $data['etapa'] = 'En proceso';
            }

            if (!empty($data)) {
                $this->solicitudService->updateSolicitud($id, $data);
                return redirect()->route('bandeja.index')
                    ->with('success', 'Solicitud tomada correctamente.');
            }

            return back()->with('error', 'No tienes permisos para tomar esta solicitud.');

        } catch (\Exception $e) {
            \Log::error('Error al tomar solicitud: ' . $e->getMessage());
            return back()->with('error', 'Error al tomar la solicitud.');
        }
    }

    /**
     * Cambiar estado de una solicitud
     */
    public function cambiarEstado(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'estado' => 'required|string',
            'etapa' => 'nullable|string',
        ]);

        try {
            $data = ['estado' => $request->estado];
            
            if ($request->etapa) {
                $data['etapa'] = $request->etapa;
            }

            if ($request->estado === 'Completado') {
                $data['fecha_termino'] = date('Y-m-d');
            }

            $this->solicitudService->updateSolicitud($id, $data);
            
            return redirect()->route('bandeja.index')
                ->with('success', 'Estado actualizado correctamente.');

        } catch (\Exception $e) {
            \Log::error('Error al cambiar estado: ' . $e->getMessage());
            return back()->with('error', 'Error al cambiar el estado.');
        }
    }
}
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

class MapsController extends Controller
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
     * Muestra el mapa de solicitudes con filtros
     */
    public function index(Request $request)
    {
        // Verificar si el usuario está autenticado
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

            // Obtener todas las solicitudes
            $todasSolicitudes = $this->solicitudService->getAllSolicitudes();
            
            // Filtrar solicitudes según el rol y departamento (si no es admin)
            if ($rolUsuario !== 'admin' && $departamentoUsuario) {
                $solicitudesFiltradas = $this->filtrarSolicitudesPorDepartamento(
                    $todasSolicitudes, 
                    $departamentoUsuario
                );
            } else {
                $solicitudesFiltradas = $todasSolicitudes;
            }

            // Obtener información complementaria
            $requerimientos = $this->obtenerRequerimientos();
            $departamentos = $this->obtenerDepartamentos();
            $usuarios = $this->obtenerUsuarios($solicitudesFiltradas);
            
            // Obtener localidades únicas
            $localidades = $this->obtenerLocalidadesUnicas($solicitudesFiltradas);

            // Calcular estadísticas
            $estadisticas = $this->calcularEstadisticas($solicitudesFiltradas);

            return view('bandeja.maps', [
                'solicitudes' => $solicitudesFiltradas,
                'requerimientos' => $requerimientos,
                'departamentos' => $departamentos,
                'usuarios' => $usuarios,
                'localidades' => $localidades,
                'estadisticas' => $estadisticas,
                'usuarioActual' => $usuarioActual,
                'nombre' => session('user_nombre'),
                'rol' => $rolUsuario
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en MapaSolicitudesController: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            
            return back()->with('error', 'Error al cargar el mapa: ' . $e->getMessage());
        }
    }

    /**
     * API para obtener datos actualizados del mapa
     */
    public function obtenerDatosMapa(Request $request)
    {
        try {
            // Verificar autenticación
            if (!session('user_id')) {
                return response()->json(['error' => 'No autorizado'], 401);
            }

            $usuarioActual = $this->funcionarioService->getFuncionarioById(session('user_id'));
            $rolUsuario = $usuarioActual['rol'];
            $departamentoUsuario = $usuarioActual['departamento_id'] ?? null;

            // Obtener solicitudes
            $todasSolicitudes = $this->solicitudService->getAllSolicitudes();
            
            // Filtrar según rol
            if ($rolUsuario !== 'admin' && $departamentoUsuario) {
                $solicitudes = $this->filtrarSolicitudesPorDepartamento(
                    $todasSolicitudes, 
                    $departamentoUsuario
                );
            } else {
                $solicitudes = $todasSolicitudes;
            }

            // Aplicar filtros adicionales si se proporcionan
            $filtros = $request->only(['estado', 'localidad', 'departamento', 'fecha', 'busqueda']);
            $solicitudes = $this->aplicarFiltrosAdicionales($solicitudes, $filtros);

            // Obtener datos complementarios
            $requerimientos = $this->obtenerRequerimientos();
            $departamentos = $this->obtenerDepartamentos();
            $usuarios = $this->obtenerUsuarios($solicitudes);

            return response()->json([
                'solicitudes' => $solicitudes,
                'requerimientos' => $requerimientos,
                'departamentos' => $departamentos,
                'usuarios' => $usuarios,
                'estadisticas' => $this->calcularEstadisticas($solicitudes)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error al obtener datos del mapa: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Filtra solicitudes por departamento del usuario
     */
    private function filtrarSolicitudesPorDepartamento($solicitudes, $departamentoId)
    {
        // Obtener requerimientos del departamento del usuario
        $requerimientos = $this->requerimientoService->getAllRequerimientos();
        $requerimientosDepartamento = array_filter($requerimientos, function($req) use ($departamentoId) {
            return isset($req['departamento_id']) && $req['departamento_id'] == $departamentoId;
        });

        $idsRequerimientos = array_column($requerimientosDepartamento, 'id_requerimiento');

        // Filtrar solicitudes que pertenecen a los requerimientos del departamento
        $solicitudesFiltradas = array_filter($solicitudes, function($solicitud) use ($idsRequerimientos) {
            $requerimientoId = $solicitud['requerimiento_id'] ?? null;
            return in_array($requerimientoId, $idsRequerimientos);
        });

        return array_values($solicitudesFiltradas);
    }

    /**
     * Aplica filtros adicionales a las solicitudes
     */
    private function aplicarFiltrosAdicionales($solicitudes, $filtros)
    {
        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($filtros) {
                return ($solicitud['estado'] ?? '') === $filtros['estado'];
            });
        }

        // Filtro por localidad
        if (!empty($filtros['localidad'])) {
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($filtros) {
                return ($solicitud['localidad'] ?? '') === $filtros['localidad'];
            });
        }

        // Filtro por departamento
        if (!empty($filtros['departamento'])) {
            $requerimientos = $this->requerimientoService->getAllRequerimientos();
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($filtros, $requerimientos) {
                $requerimiento = null;
                foreach ($requerimientos as $req) {
                    if ($req['id_requerimiento'] == ($solicitud['requerimiento_id'] ?? '')) {
                        $requerimiento = $req;
                        break;
                    }
                }
                return $requerimiento && ($requerimiento['departamento_id'] ?? '') == $filtros['departamento'];
            });
        }

        // Filtro por fecha
        if (!empty($filtros['fecha'])) {
            $hoy = Carbon::now();
            $solicitudes = array_filter($solicitudes, function($solicitud) use ($filtros, $hoy) {
                $fechaIngreso = $solicitud['fecha_ingreso'] ?? null;
                if (!$fechaIngreso) return false;
                
                $fechaIng = Carbon::parse($fechaIngreso);
                
                switch ($filtros['fecha']) {
                    case 'hoy':
                        return $fechaIng->isToday();
                    case 'semana':
                        return $fechaIng->isCurrentWeek();
                    case 'mes':
                        return $fechaIng->isCurrentMonth();
                    case 'trimestre':
                        return $fechaIng->between($hoy->copy()->subMonths(3), $hoy);
                    default:
                        return true;
                }
            });
        }

        // Filtro por búsqueda de texto
        if (!empty($filtros['busqueda'])) {
            $busqueda = strtolower($filtros['busqueda']);
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
     * Obtiene localidades únicas de las solicitudes
     */
    private function obtenerLocalidadesUnicas($solicitudes)
    {
        $localidades = array_unique(array_column($solicitudes, 'localidad'));
        $localidades = array_filter($localidades, function($localidad) {
            return !empty($localidad);
        });
        sort($localidades);
        
        return $localidades;
    }

    /**
     * Calcula estadísticas de las solicitudes
     */
    private function calcularEstadisticas($solicitudes)
    {
        $total = count($solicitudes);
        $porEstado = [];
        $vencidas = 0;
        $hoy = Carbon::now();

        foreach ($solicitudes as $solicitud) {
            $estado = $solicitud['estado'] ?? 'Sin estado';
            
            // Contar por estado
            if (!isset($porEstado[$estado])) {
                $porEstado[$estado] = 0;
            }
            $porEstado[$estado]++;

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
            'por_estado' => $porEstado,
            'vencidas' => $vencidas,
            'en_curso' => $porEstado['En curso'] ?? 0,
            'completadas' => $porEstado['Completado'] ?? 0,
            'derivadas' => $porEstado['Derivada'] ?? 0,
            'denegadas' => $porEstado['Denegada'] ?? 0,
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
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FuncionarioService;
use App\Services\DepartamentoService;
use App\Services\RequerimientoService;
use App\Services\UsuarioService;
use App\Services\SolicitudService;
use DateTime;

class DashboardController extends Controller
{
    protected $funcionarioService;
    protected $departamentoService;
    protected $requerimientoService;
    protected $usuarioService;
    protected $solicitudService;

    public function __construct(
        FuncionarioService $funcionarioService,
        DepartamentoService $departamentoService,
        RequerimientoService $requerimientoService,
        UsuarioService $usuarioService,
        SolicitudService $solicitudService
    ) {
        $this->funcionarioService = $funcionarioService;
        $this->departamentoService = $departamentoService;
        $this->requerimientoService = $requerimientoService;
        $this->usuarioService = $usuarioService;
        $this->solicitudService = $solicitudService;
    }

    public function index()
    {
        // Verificar autenticación
        if (!session('user_id')) {
            return redirect()->route('login');
        }

        try {
            // Obtener datos básicos
            $funcionarios = $this->funcionarioService->getAllFuncionarios() ?? [];
            $departamentos = $this->departamentoService->getAllDepartamentos() ?? [];
            $requerimientos = $this->requerimientoService->getAllRequerimientos() ?? [];
            $usuarios = $this->usuarioService->getAllUsuarios() ?? [];
            $solicitudes = $this->solicitudService->getAllSolicitudes() ?? [];

            // Calcular métricas de rendimiento
            $rendimiento = $this->calcularMetricasRendimiento($solicitudes);
            
            // Datos para gráficos
            $departamentosData = $this->procesarDatosDepartamentos($departamentos, $requerimientos);
            $datosGraficoRendimiento = $this->generarDatosGraficoRendimiento($solicitudes);

            return view('dashboard', [
                'nombre' => session('user_nombre'),
                'rol' => session('user_rol'),
                'departamentos' => $departamentos,
                'requerimientos' => $requerimientos,
                'usuarios' => $usuarios,
                'funcionarios' => $funcionarios,
                'departamentosData' => $departamentosData['data'],
                'totalRequerimientos' => $departamentosData['total'],
                // Datos de rendimiento
                'solicitudesCompletadas' => $rendimiento['completadas'],
                'solicitudesEnProceso' => $rendimiento['enProceso'],
                'solicitudesNuevas' => $rendimiento['nuevas'],
                'tiempoPromedio' => $rendimiento['tiempoPromedio'],
                'eficiencia' => $rendimiento['eficiencia'],
                'datosGraficoRendimiento' => $datosGraficoRendimiento,
                'mesActual' => date('M Y'),
                'totalSolicitudesMes' => $rendimiento['totalMes'],
                // Datos adicionales
                'solicitudesVencidas' => $rendimiento['vencidas'],
                'solicitudesHoy' => $rendimiento['hoy'],
                'promedioSemanal' => $rendimiento['promedioSemanal']
            ]);

        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error en DashboardController: ' . $e->getMessage());
            
            // Datos por defecto en caso de error
            return view('dashboard', [
                'nombre' => session('user_nombre', 'Usuario'),
                'rol' => session('user_rol', 'usuario'),
                'departamentos' => [],
                'requerimientos' => [],
                'usuarios' => [],
                'funcionarios' => [],
                'departamentosData' => [],
                'totalRequerimientos' => 0,
                'solicitudesCompletadas' => 0,
                'solicitudesEnProceso' => 0,
                'solicitudesNuevas' => 0,
                'tiempoPromedio' => 0,
                'eficiencia' => 0,
                'datosGraficoRendimiento' => [],
                'mesActual' => date('M Y'),
                'totalSolicitudesMes' => 0,
                'solicitudesVencidas' => 0,
                'solicitudesHoy' => 0,
                'promedioSemanal' => 0
            ]);
        }
    }

    /**
     * Calcular métricas de rendimiento mensual y general
     */
    private function calcularMetricasRendimiento($solicitudes)
    {
        $mesActual = date('Y-m');
        $hoy = date('Y-m-d');
        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        
        // Filtrar solicitudes del mes actual
        $solicitudesMesActual = array_filter($solicitudes, function($solicitud) use ($mesActual) {
            return isset($solicitud['fecha_inicio']) && 
                   date('Y-m', strtotime($solicitud['fecha_inicio'])) === $mesActual;
        });

        // Calcular estados
        $completadas = $this->contarPorEstado($solicitudesMesActual, ['completado', 'finalizada', 'cerrada']);
        $enProceso = $this->contarPorEstado($solicitudesMesActual, ['en proceso', 'asignada', 'evaluacion']);
        $nuevas = $this->contarPorEstado($solicitudesMesActual, ['pendiente', 'ingreso']);

        // Solicitudes de hoy
        $solicitudesHoy = count(array_filter($solicitudes, function($solicitud) use ($hoy) {
            return isset($solicitud['fecha_inicio']) && 
                   date('Y-m-d', strtotime($solicitud['fecha_inicio'])) === $hoy;
        }));

        // Solicitudes vencidas (estimada < hoy y no completadas)
        $vencidas = count(array_filter($solicitudes, function($solicitud) use ($hoy) {
            return isset($solicitud['fecha_estimada_op']) && 
                   isset($solicitud['estado']) &&
                   date('Y-m-d', strtotime($solicitud['fecha_estimada_op'])) < $hoy &&
                   !in_array(strtolower($solicitud['estado']), ['completado', 'finalizada', 'cerrada']);
        }));

        // Tiempo promedio de resolución
        $tiempoPromedio = $this->calcularTiempoPromedio($solicitudes);

        // Eficiencia (completadas vs total del mes)
        $totalMes = count($solicitudesMesActual);
        $eficiencia = $totalMes > 0 ? round(($completadas / $totalMes) * 100) : 0;

        // Promedio semanal
        $promedioSemanal = $this->calcularPromedioSemanal($solicitudes);

        return [
            'completadas' => $completadas,
            'enProceso' => $enProceso,
            'nuevas' => $nuevas,
            'tiempoPromedio' => $tiempoPromedio,
            'eficiencia' => $eficiencia,
            'totalMes' => $totalMes,
            'hoy' => $solicitudesHoy,
            'vencidas' => $vencidas,
            'promedioSemanal' => $promedioSemanal
        ];
    }

    /**
     * Contar solicitudes por estado
     */
    private function contarPorEstado($solicitudes, $estados)
    {
        return count(array_filter($solicitudes, function($solicitud) use ($estados) {
            return isset($solicitud['estado']) && 
                   in_array(strtolower($solicitud['estado']), array_map('strtolower', $estados));
        }));
    }

    /**
     * Calcular tiempo promedio de resolución en días
     */
    private function calcularTiempoPromedio($solicitudes)
    {
        $solicitudesFinalizadas = array_filter($solicitudes, function($solicitud) {
            return isset($solicitud['fecha_inicio']) && 
                   isset($solicitud['fecha_termino']) && 
                   !empty($solicitud['fecha_termino']) &&
                   $solicitud['fecha_termino'] !== '0000-00-00' &&
                   $solicitud['fecha_termino'] !== null;
        });

        if (count($solicitudesFinalizadas) === 0) {
            return 0;
        }

        $tiempoTotal = 0;
        foreach ($solicitudesFinalizadas as $solicitud) {
            try {
                $inicio = new DateTime($solicitud['fecha_inicio']);
                $fin = new DateTime($solicitud['fecha_termino']);
                $diferencia = $fin->diff($inicio)->days;
                $tiempoTotal += $diferencia;
            } catch (\Exception $e) {
                // Ignorar fechas inválidas
                continue;
            }
        }

        return round($tiempoTotal / count($solicitudesFinalizadas), 1);
    }

    /**
     * Calcular promedio de solicitudes por semana
     */
    private function calcularPromedioSemanal($solicitudes)
    {
        $solicitudesUltimas4Semanas = array_filter($solicitudes, function($solicitud) {
            $hace4Semanas = date('Y-m-d', strtotime('-4 weeks'));
            return isset($solicitud['fecha_inicio']) && 
                   date('Y-m-d', strtotime($solicitud['fecha_inicio'])) >= $hace4Semanas;
        });

        return round(count($solicitudesUltimas4Semanas) / 4, 1);
    }

    /**
     * Procesar datos de departamentos para el gráfico
     */
    private function procesarDatosDepartamentos($departamentos, $requerimientos)
    {
        $departamentosData = [];
        $totalRequerimientos = 0;

        foreach($departamentos as $departamento) {
            $requerimientosCount = 0;
            foreach($requerimientos as $requerimiento) {
                if(isset($requerimiento['departamento_id']) && $requerimiento['departamento_id'] == $departamento['id']) {
                    $requerimientosCount++;
                }
            }
            
            if($requerimientosCount > 0) {
                $departamentosData[] = [
                    'nombre' => $departamento['nombre'],
                    'count' => $requerimientosCount,
                    'id' => $departamento['id']
                ];
                $totalRequerimientos += $requerimientosCount;
            }
        }

        // Si no hay datos, crear uno por defecto
        if(empty($departamentosData)) {
            $departamentosData[] = [
                'nombre' => 'Sin requerimientos',
                'count' => 1,
                'id' => 0
            ];
            $totalRequerimientos = 1;
        }

        // Ordenar por cantidad
        usort($departamentosData, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        // Limitar a top 6
        $departamentosData = array_slice($departamentosData, 0, 6);

        // Calcular porcentajes
        foreach($departamentosData as &$dept) {
            $dept['porcentaje'] = round(($dept['count'] / $totalRequerimientos) * 100, 1);
        }

        return [
            'data' => $departamentosData,
            'total' => $totalRequerimientos
        ];
    }

    /**
     * Generar datos para el gráfico de rendimiento de los últimos 7 días
     */
    private function generarDatosGraficoRendimiento($solicitudes)
    {
        $datosGrafico = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = date('Y-m-d', strtotime("-$i days"));
            
            // Solicitudes nuevas del día
            $solicitudesDia = array_filter($solicitudes, function($solicitud) use ($fecha) {
                return isset($solicitud['fecha_inicio']) && 
                       date('Y-m-d', strtotime($solicitud['fecha_inicio'])) === $fecha;
            });
            
            // Solicitudes completadas del día
            $completadasDia = array_filter($solicitudes, function($solicitud) use ($fecha) {
                return isset($solicitud['fecha_termino']) && 
                       !empty($solicitud['fecha_termino']) &&
                       $solicitud['fecha_termino'] !== '0000-00-00' &&
                       date('Y-m-d', strtotime($solicitud['fecha_termino'])) === $fecha;
            });
            
            $datosGrafico[] = [
                'fecha' => date('d/m', strtotime($fecha)),
                'nuevas' => count($solicitudesDia),
                'completadas' => count($completadasDia),
                'enProceso' => count($solicitudesDia) - count($completadasDia)
            ];
        }
        
        return $datosGrafico;
    }

    /**
     * API para obtener métricas en tiempo real (opcional)
     */
    public function getMetrics()
    {
        try {
            $solicitudes = $this->solicitudService->getAllSolicitudes() ?? [];
            $rendimiento = $this->calcularMetricasRendimiento($solicitudes);
            
            return response()->json([
                'success' => true,
                'data' => $rendimiento,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener métricas'
            ], 500);
        }
    }

    /**
     * API para obtener datos del gráfico (opcional)
     */
    public function getChartData()
    {
        try {
            $solicitudes = $this->solicitudService->getAllSolicitudes() ?? [];
            $datosGrafico = $this->generarDatosGraficoRendimiento($solicitudes);
            
            return response()->json([
                'success' => true,
                'data' => $datosGrafico,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener datos del gráfico'
            ], 500);
        }
    }
}
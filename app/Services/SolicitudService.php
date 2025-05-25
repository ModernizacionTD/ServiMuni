<?php

namespace App\Services;

use Google\Service\Sheets;

class SolicitudService extends BaseService
{
    /**
     * Limpia los datos reemplazando null por cadenas vacías
     *
     * @param mixed $value
     * @return string
     */
    private function cleanValue($value)
    {
        if ($value === null || $value === 'null') {
            return '';
        }
        return (string) $value;
    }
    /**
     * Obtiene todas las solicitudes del sistema
     *
     * @return array
     */
    public function getAllSolicitudes()
    {
        try {
            $range = 'Solicitudes!A:R'; // Cambiado a R para incluir latitud y longitud
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            // Definir los encabezados de acuerdo a los campos de la tabla
            $headers = [
                'id_solicitud', 
                'fecha_ingreso', 
                'fecha_termino', 
                'fecha_derivacion', 
                'fecha_estimada_op', 
                'estado', 
                'etapa', 
                'rut_usuario', 
                'rut_ingreso', 
                'rut_gestor', 
                'rut_tecnico', 
                'providencia', 
                'requerimiento_id', 
                'descripcion', 
                'imagen', 
                'localidad', 
                'tipo_ubicacion', 
                'ubicacion',
                'latitud',
                'longitud'
            ];
            
            $result = [];
            foreach ($values as $index => $row) {
                // Saltar la fila de encabezados si existe
                if ($index === 0) {
                    continue;
                }
                
                // Asegurarse de que tengamos suficientes elementos en la fila
                while (count($row) < count($headers)) {
                    $row[] = '';  // Usar cadena vacía en lugar de null
                }
                
                $item = [];
                foreach ($headers as $headerIndex => $header) {
                    $item[$header] = $row[$headerIndex] ?? '';  // Usar cadena vacía en lugar de null
                }
                $result[] = $item;
            }
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (solicitudes): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }
    
    /**
     * Obtiene una solicitud por su ID
     *
     * @param string $id
     * @return array|null
     */
    public function getSolicitudById($id)
    {
        try {
            \Log::info('Buscando solicitud con ID: ' . $id);
            
            // Obtener todas las solicitudes
            $solicitudes = $this->getAllSolicitudes();
            
            // Buscar la solicitud con el ID especificado
            foreach ($solicitudes as $solicitud) {
                if (isset($solicitud['id_solicitud']) && $solicitud['id_solicitud'] == $id) {
                    \Log::info('Solicitud encontrada');
                    return $solicitud;
                }
            }
            
            \Log::info('Solicitud no encontrada');
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al buscar solicitud por ID: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al buscar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene solicitudes por RUT de usuario
     *
     * @param string $rutUsuario
     * @return array
     */
    public function getByRutUsuario($rutUsuario)
    {
        try {
            \Log::info('Buscando solicitudes para el usuario con RUT: ' . $rutUsuario);
            
            // Obtener todas las solicitudes
            $solicitudes = $this->getAllSolicitudes();
            
            // Filtrar por rut_usuario
            $filteredSolicitudes = array_filter($solicitudes, function($solicitud) use ($rutUsuario) {
                return isset($solicitud['rut_usuario']) && $solicitud['rut_usuario'] == $rutUsuario;
            });
            
            \Log::info('Se encontraron ' . count($filteredSolicitudes) . ' solicitudes para el usuario');
            return array_values($filteredSolicitudes); // Reindexar el array
        } catch (\Exception $e) {
            \Log::error('Error al buscar solicitudes por RUT de usuario: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al buscar solicitudes por RUT de usuario: ' . $e->getMessage());
        }
    }
    
    /**
     * Crea una nueva solicitud - MÉTODO CORREGIDO
     *
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        try {
            \Log::info('Iniciando creación de solicitud');
            \Log::info('Datos recibidos:', $data);
            
            // Obtener todas las solicitudes para determinar el próximo ID
            $solicitudes = $this->getAllSolicitudes();
            $nextId = 1;
            
            if (!empty($solicitudes)) {
                // Encontrar el ID más alto y sumar 1
                $maxId = max(array_column($solicitudes, 'id_solicitud'));
                $nextId = intval($maxId) + 1;
            }
            
            \Log::info('Nuevo ID para la solicitud: ' . $nextId);
            
            // Preparar datos para insertar - USAR FECHA_INGRESO CONSISTENTE
            $values = [
                [
                    $nextId,
                    $this->cleanValue($data['fecha_ingreso'] ?? $data['fecha_inicio'] ?? date('Y-m-d')), // fecha_ingreso
                    $this->cleanValue($data['fecha_termino'] ?? ''),
                    $this->cleanValue($data['fecha_derivacion'] ?? ''),
                    $this->cleanValue($data['fecha_estimada_op'] ?? ''),
                    $this->cleanValue($data['estado'] ?? 'Pendiente'),
                    $this->cleanValue($data['etapa'] ?? 'Ingreso'),
                    $this->cleanValue($data['rut_usuario'] ?? ''),
                    $this->cleanValue($data['rut_ingreso'] ?? ''),
                    $this->cleanValue($data['rut_gestor'] ?? ''),
                    $this->cleanValue($data['rut_tecnico'] ?? ''),
                    $this->cleanValue($data['providencia'] ?? ''),
                    $this->cleanValue($data['requerimiento_id'] ?? ''),
                    $this->cleanValue($data['descripcion'] ?? ''),
                    $this->cleanValue($data['imagen'] ?? ''),
                    $this->cleanValue($data['localidad'] ?? ''),
                    $this->cleanValue($data['tipo_ubicacion'] ?? ''),
                    $this->cleanValue($data['ubicacion'] ?? ''),
                    $this->cleanValue($data['latitud'] ?? ''),
                    $this->cleanValue($data['longitud'] ?? '')
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($solicitudes) + 2; // +1 para los encabezados, +1 para la siguiente fila
            
            // Si la hoja está vacía o tiene menos de 2 filas, crear los encabezados
            if (empty($solicitudes) || count($solicitudes) === 0) {
                \Log::info('Creando encabezados porque la hoja está vacía');
                $headers = [
                    'id_solicitud', 
                    'fecha_ingreso', 
                    'fecha_termino', 
                    'fecha_derivacion', 
                    'fecha_estimada_op', 
                    'estado', 
                    'etapa', 
                    'rut_usuario', 
                    'rut_ingreso', 
                    'rut_gestor', 
                    'rut_tecnico', 
                    'providencia', 
                    'requerimiento_id', 
                    'descripcion', 
                    'imagen', 
                    'localidad', 
                    'tipo_ubicacion', 
                    'ubicacion',
                    'latitud',
                    'longitud'
                ];
                
                // Crear encabezados primero
                $headerBody = new \Google\Service\Sheets\ValueRange([
                    'values' => [$headers]
                ]);
                
                $this->sheets->spreadsheets_values->update(
                    $this->spreadsheetId, 
                    'Solicitudes!A1:T1', 
                    $headerBody, 
                    ['valueInputOption' => 'RAW']
                );
                
                \Log::info('Encabezados creados exitosamente');
            }
            
            $range = "Solicitudes!A$nextRow:T$nextRow";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            \Log::info('Insertando en rango: ' . $range);
            \Log::info('Valores a insertar (limpios):', $values[0]);
            \Log::info('Tipo de cada valor:', array_map('gettype', $values[0]));
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Solicitud creada correctamente - Respuesta de Google Sheets recibida');
            
            // Devolver los datos con el ID asignado
            $data['id_solicitud'] = $nextId;
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear solicitud): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al crear la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza una solicitud existente
     *
     * @param string $id
     * @param array $data
     * @return array
     */
    public function updateSolicitud($id, $data)
    {
        try {
            \Log::info('Iniciando actualización de solicitud ID: ' . $id);
            
            // Obtener todas las solicitudes para encontrar la fila correcta
            $solicitudes = $this->getAllSolicitudes();
            $rowIndex = null;
            $solicitudActual = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($solicitudes as $index => $solicitud) {
                if (isset($solicitud['id_solicitud']) && $solicitud['id_solicitud'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    $solicitudActual = $solicitud;
                    break;
                }
            }
            
            if ($rowIndex === null || $solicitudActual === null) {
                \Log::error("Solicitud con ID $id no encontrada");
                throw new \Exception("Solicitud con ID $id no encontrada");
            }
            
            \Log::info('Solicitud encontrada en fila: ' . $rowIndex);
            
            // Fusionar datos actuales con nuevos datos
            $solicitudActualizada = array_merge($solicitudActual, $data);
            $solicitudActualizada['id_solicitud'] = $id; // Mantener el mismo ID
            
            // Preparar datos para actualizar - SIN NULLS
            $values = [
                [
                    $this->cleanValue($solicitudActualizada['id_solicitud']),
                    $this->cleanValue($solicitudActualizada['fecha_ingreso'] ?? ''),
                    $this->cleanValue($solicitudActualizada['fecha_termino'] ?? ''),
                    $this->cleanValue($solicitudActualizada['fecha_derivacion'] ?? ''),
                    $this->cleanValue($solicitudActualizada['fecha_estimada_op'] ?? ''),
                    $this->cleanValue($solicitudActualizada['estado'] ?? ''),
                    $this->cleanValue($solicitudActualizada['etapa'] ?? ''),
                    $this->cleanValue($solicitudActualizada['rut_usuario'] ?? ''),
                    $this->cleanValue($solicitudActualizada['rut_ingreso'] ?? ''),
                    $this->cleanValue($solicitudActualizada['rut_gestor'] ?? ''),
                    $this->cleanValue($solicitudActualizada['rut_tecnico'] ?? ''),
                    $this->cleanValue($solicitudActualizada['providencia'] ?? ''),
                    $this->cleanValue($solicitudActualizada['requerimiento_id'] ?? ''),
                    $this->cleanValue($solicitudActualizada['descripcion'] ?? ''),
                    $this->cleanValue($solicitudActualizada['imagen'] ?? ''),
                    $this->cleanValue($solicitudActualizada['localidad'] ?? ''),
                    $this->cleanValue($solicitudActualizada['tipo_ubicacion'] ?? ''),
                    $this->cleanValue($solicitudActualizada['ubicacion'] ?? ''),
                    $this->cleanValue($solicitudActualizada['latitud'] ?? ''),
                    $this->cleanValue($solicitudActualizada['longitud'] ?? '')
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Solicitudes!A$rowIndex:T$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Solicitud actualizada correctamente');
            
            return $solicitudActualizada;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (actualizar solicitud): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al actualizar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Elimina una solicitud
     *
     * @param string $id
     * @return bool
     */
    public function deleteSolicitud($id)
    {
        try {
            \Log::info('Iniciando eliminación de solicitud ID: ' . $id);
            
            // Obtener todas las solicitudes
            $solicitudes = $this->getAllSolicitudes();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($solicitudes as $index => $solicitud) {
                if (isset($solicitud['id_solicitud']) && $solicitud['id_solicitud'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Solicitud con ID $id no encontrada");
                throw new \Exception("Solicitud con ID $id no encontrada");
            }
            
            \Log::info('Solicitud encontrada en fila: ' . $rowIndex);
            
            // Preparar solicitud para eliminar la fila (reemplazando con valores vacíos)
            $values = [
                array_fill(0, 20, '') // 20 celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Solicitudes!A$rowIndex:T$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Solicitud eliminada correctamente');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (eliminar solicitud): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al eliminar la solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza el estado de una solicitud
     *
     * @param string $id
     * @param string $estado
     * @param string|null $etapa
     * @return array
     */
    public function updateEstado($id, $estado, $etapa = null)
    {
        try {
            \Log::info("Actualizando estado de solicitud ID: $id a '$estado'");
            
            // Obtener la solicitud actual
            $solicitud = $this->getSolicitudById($id);
            
            if (!$solicitud) {
                throw new \Exception("Solicitud con ID $id no encontrada");
            }
            
            // Preparar datos para actualizar
            $data = [
                'estado' => $estado
            ];
            
            // Si se proporciona etapa, actualizarla también
            if ($etapa !== null) {
                $data['etapa'] = $etapa;
            }
            
            // Si el estado es "Completado", establecer la fecha de término actual
            if ($estado === 'Completado') {
                $data['fecha_termino'] = date('Y-m-d');
            }
            
            // Actualizar la solicitud
            return $this->updateSolicitud($id, $data);
        } catch (\Exception $e) {
            \Log::error('Error al actualizar estado de solicitud: ' . $e->getMessage());
            throw new \Exception('Error al actualizar estado de solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Asigna un gestor a una solicitud
     *
     * @param string $id
     * @param string $rutGestor
     * @return array
     */
    public function asignarGestor($id, $rutGestor)
    {
        try {
            \Log::info("Asignando gestor RUT: $rutGestor a solicitud ID: $id");
            
            // Actualizar la solicitud
            $data = [
                'rut_gestor' => $rutGestor,
                'fecha_derivacion' => date('Y-m-d'),
                'etapa' => 'Asignada'
            ];
            
            return $this->updateSolicitud($id, $data);
        } catch (\Exception $e) {
            \Log::error('Error al asignar gestor a solicitud: ' . $e->getMessage());
            throw new \Exception('Error al asignar gestor a solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Asigna un técnico a una solicitud
     *
     * @param string $id
     * @param string $rutTecnico
     * @return array
     */
    public function asignarTecnico($id, $rutTecnico)
    {
        try {
            \Log::info("Asignando técnico RUT: $rutTecnico a solicitud ID: $id");
            
            // Actualizar la solicitud
            $data = [
                'rut_tecnico' => $rutTecnico,
                'etapa' => 'En proceso'
            ];
            
            return $this->updateSolicitud($id, $data);
        } catch (\Exception $e) {
            \Log::error('Error al asignar técnico a solicitud: ' . $e->getMessage());
            throw new \Exception('Error al asignar técnico a solicitud: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene solicitudes por fecha de ingreso en un rango específico
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function getByRangoFechas($fechaInicio, $fechaFin)
    {
        try {
            \Log::info("Buscando solicitudes entre fechas: $fechaInicio y $fechaFin");
            
            // Obtener todas las solicitudes
            $solicitudes = $this->getAllSolicitudes();
            
            // Filtrar por rango de fechas
            $filteredSolicitudes = array_filter($solicitudes, function($solicitud) use ($fechaInicio, $fechaFin) {
                if (!isset($solicitud['fecha_ingreso']) || empty($solicitud['fecha_ingreso'])) {
                    return false;
                }
                
                $fechaSolicitud = strtotime($solicitud['fecha_ingreso']);
                $inicio = strtotime($fechaInicio);
                $fin = strtotime($fechaFin);
                
                return $fechaSolicitud >= $inicio && $fechaSolicitud <= $fin;
            });
            
            \Log::info('Se encontraron ' . count($filteredSolicitudes) . ' solicitudes en el rango de fechas');
            return array_values($filteredSolicitudes); // Reindexar el array
        } catch (\Exception $e) {
            \Log::error('Error al buscar solicitudes por rango de fechas: ' . $e->getMessage());
            throw new \Exception('Error al buscar solicitudes por rango de fechas: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene estadísticas sobre las solicitudes
     *
     * @return array
     */
    public function getEstadisticas()
    {
        try {
            // Obtener todas las solicitudes
            $solicitudes = $this->getAllSolicitudes();
            
            // Inicializar estadísticas
            $estadisticas = [
                'total' => count($solicitudes),
                'por_estado' => [],
                'por_etapa' => [],
                'por_localidad' => [],
                'promedio_tiempo_respuesta' => 0
            ];
            
            // Contar por estado
            foreach ($solicitudes as $solicitud) {
                $estado = $solicitud['estado'] ?? 'Sin estado';
                $etapa = $solicitud['etapa'] ?? 'Sin etapa';
                $localidad = $solicitud['localidad'] ?? 'Sin localidad';
                
                // Contar por estado
                if (!isset($estadisticas['por_estado'][$estado])) {
                    $estadisticas['por_estado'][$estado] = 0;
                }
                $estadisticas['por_estado'][$estado]++;
                
                // Contar por etapa
                if (!isset($estadisticas['por_etapa'][$etapa])) {
                    $estadisticas['por_etapa'][$etapa] = 0;
                }
                $estadisticas['por_etapa'][$etapa]++;
                
                // Contar por localidad
                if (!isset($estadisticas['por_localidad'][$localidad])) {
                    $estadisticas['por_localidad'][$localidad] = 0;
                }
                $estadisticas['por_localidad'][$localidad]++;
                
                // Calcular tiempos de respuesta para solicitudes terminadas
                if (isset($solicitud['fecha_termino']) && !empty($solicitud['fecha_termino']) &&
                    isset($solicitud['fecha_ingreso']) && !empty($solicitud['fecha_ingreso'])) {
                    
                    $fechaIngreso = strtotime($solicitud['fecha_ingreso']);
                    $fechaTermino = strtotime($solicitud['fecha_termino']);
                    
                    if ($fechaTermino >= $fechaIngreso) {
                        $dias = ceil(($fechaTermino - $fechaIngreso) / (60 * 60 * 24));
                        
                        if (!isset($estadisticas['tiempos_respuesta'])) {
                            $estadisticas['tiempos_respuesta'] = [];
                        }
                        
                        $estadisticas['tiempos_respuesta'][] = $dias;
                    }
                }
            }
            
            // Calcular promedio de tiempo de respuesta
            if (isset($estadisticas['tiempos_respuesta']) && !empty($estadisticas['tiempos_respuesta'])) {
                $estadisticas['promedio_tiempo_respuesta'] = array_sum($estadisticas['tiempos_respuesta']) / count($estadisticas['tiempos_respuesta']);
                unset($estadisticas['tiempos_respuesta']); // Eliminar el array de tiempos individuales
            }
            
            return $estadisticas;
        } catch (\Exception $e) {
            \Log::error('Error al obtener estadísticas de solicitudes: ' . $e->getMessage());
            throw new \Exception('Error al obtener estadísticas de solicitudes: ' . $e->getMessage());
        }
    }
}
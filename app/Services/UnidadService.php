<?php

namespace App\Services;

use Google\Service\Sheets;

class UnidadService extends BaseService
{
    /**
     * Obtiene todas las unidades del sistema
     *
     * @return array
     */
    public function getAllUnidades()
    {
        try {
            \Log::info('Intentando leer Unidades de la hoja con ID: ' . $this->spreadsheetId);
            
            $range = 'Unidades!A:C'; // Rango de datos para unidades (id_unidad, nombre, departamento_id)
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                \Log::info('La hoja Unidades está vacía o no se encontraron datos');
                return [];
            }
            
            $headers = ['id_unidad', 'nombre', 'departamento_id'];
            
            $result = [];
            foreach ($values as $index => $row) {
                // Saltar fila de encabezados
                if ($index === 0) {
                    continue;
                }
                
                // Asegurar que tengamos suficientes elementos
                while (count($row) < count($headers)) {
                    $row[] = null;
                }
                
                $item = [];
                foreach ($headers as $headerIndex => $header) {
                    $item[$header] = $row[$headerIndex] ?? null;
                }
                $result[] = $item;
            }
            
            \Log::info('Se encontraron ' . count($result) . ' unidades');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (unidades): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Obtiene una unidad por su ID
     *
     * @param int $id
     * @return array|null
     */
    public function getUnidadById($id)
    {
        try {
            \Log::info('Buscando unidad con ID: ' . $id);
            
            $unidades = $this->getAllUnidades();
            
            foreach ($unidades as $unidad) {
                if (isset($unidad['id_unidad']) && $unidad['id_unidad'] == $id) {
                    \Log::info('Unidad encontrada');
                    return $unidad;
                }
            }
            
            \Log::info('Unidad no encontrada');
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al buscar unidad por ID: ' . $e->getMessage());
            throw new \Exception('Error al buscar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene unidades por departamento
     *
     * @param int $departamentoId
     * @return array
     */
    public function getUnidadesByDepartamento($departamentoId)
    {
        try {
            \Log::info('Buscando unidades para departamento ID: ' . $departamentoId);
            
            $unidades = $this->getAllUnidades();
            
            $filteredUnidades = array_filter($unidades, function($unidad) use ($departamentoId) {
                return isset($unidad['departamento_id']) && $unidad['departamento_id'] == $departamentoId;
            });
            
            \Log::info('Se encontraron ' . count($filteredUnidades) . ' unidades para el departamento');
            return array_values($filteredUnidades);
        } catch (\Exception $e) {
            \Log::error('Error al buscar unidades por departamento: ' . $e->getMessage());
            throw new \Exception('Error al buscar unidades por departamento: ' . $e->getMessage());
        }
    }

    /**
     * Crea una nueva unidad
     *
     * @param array $data
     * @return array
     */
    public function createUnidad($data)
    {
        try {
            \Log::info('Iniciando creación de unidad: ' . $data['nombre']);
            
            // Obtener todas las unidades para determinar el próximo ID
            $unidades = $this->getAllUnidades();
            $nextId = 1;
            
            if (!empty($unidades)) {
                $maxId = max(array_column($unidades, 'id_unidad'));
                $nextId = intval($maxId) + 1;
            }
            
            \Log::info('Nuevo ID para la unidad: ' . $nextId);
            
            // Preparar datos para insertar
            $values = [
                [$nextId, $data['nombre'], $data['departamento_id']]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($unidades) + 2;
            
            // Si la hoja está vacía, crear encabezados
            if (empty($unidades)) {
                $this->createHeadersIfNeeded('Unidades!A1:C1', ['id_unidad', 'nombre', 'departamento_id']);
            }
            
            $range = "Unidades!A$nextRow:C$nextRow";
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Unidad creada correctamente');
            
            return [
                'id_unidad' => $nextId,
                'nombre' => $data['nombre'],
                'departamento_id' => $data['departamento_id']
            ];
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear unidad): ' . $e->getMessage());
            throw new \Exception('Error al crear la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza una unidad existente
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateUnidad($id, $data)
    {
        try {
            \Log::info('Iniciando actualización de unidad ID: ' . $id);
            
            $unidades = $this->getAllUnidades();
            $rowIndex = null;
            
            foreach ($unidades as $index => $unidad) {
                if (isset($unidad['id_unidad']) && $unidad['id_unidad'] == $id) {
                    $rowIndex = $index + 2;
                    break;
                }
            }
            
            if ($rowIndex === null) {
                throw new \Exception("Unidad con ID $id no encontrada");
            }
            
            $values = [
                [$id, $data['nombre'], $data['departamento_id']]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Unidades!A$rowIndex:C$rowIndex";
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Unidad actualizada correctamente');
            
            return [
                'id_unidad' => $id,
                'nombre' => $data['nombre'],
                'departamento_id' => $data['departamento_id']
            ];
        } catch (\Exception $e) {
            \Log::error('Error al actualizar unidad: ' . $e->getMessage());
            throw new \Exception('Error al actualizar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Elimina una unidad
     *
     * @param int $id
     * @return bool
     */
    public function deleteUnidad($id)
    {
        try {
            \Log::info('Iniciando eliminación de unidad ID: ' . $id);
            
            $unidades = $this->getAllUnidades();
            $rowIndex = null;
            
            // Primero, encontrar la fila exacta en la hoja de cálculo
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, 'Unidades!A:A');
            $rows = $response->getValues();
            
            // Buscar la fila que contiene el ID
            $sheetRowIndex = null;
            foreach ($rows as $index => $row) {
                if (!empty($row[0]) && $row[0] == $id) {
                    $sheetRowIndex = $index + 1; // +1 porque las filas en Sheets empiezan en 1
                    break;
                }
            }
            
            if ($sheetRowIndex === null) {
                throw new \Exception("Unidad con ID $id no encontrada en la hoja de cálculo");
            }
            
            // Crear la solicitud para eliminar la fila
            $deleteRequest = new \Google_Service_Sheets_Request([
                'deleteDimension' => [
                    'range' => [
                        'sheetId' => 0,
                        'dimension' => 'ROWS',
                        'startIndex' => $sheetRowIndex - 1, // Índice base 0
                        'endIndex' => $sheetRowIndex // El índice final es exclusivo
                    ]
                ]
            ]);
            
            // Crear la solicitud por lotes
            $batchUpdateRequest = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [$deleteRequest]
            ]);
            
            // Ejecutar la solicitud
            $this->sheets->spreadsheets->batchUpdate($this->spreadsheetId, $batchUpdateRequest);
            
            \Log::info('Unidad eliminada correctamente');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error al eliminar unidad: ' . $e->getMessage());
            throw new \Exception('Error al eliminar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene funcionarios con rol de técnico por unidad
     *
     * @param int $unidadId
     * @return array
     */
    public function getTecnicosByUnidad($unidadId)
    {
        try {
            // Necesitaremos el FuncionarioService para esto
            $funcionarioService = app(FuncionarioService::class);
            $funcionarios = $funcionarioService->getAllFuncionarios();
            
            // Filtrar técnicos de la unidad específica
            $tecnicos = array_filter($funcionarios, function($funcionario) use ($unidadId) {
                return isset($funcionario['rol']) && $funcionario['rol'] === 'tecnico' &&
                       isset($funcionario['unidad_id']) && $funcionario['unidad_id'] == $unidadId;
            });
            
            return array_values($tecnicos);
        } catch (\Exception $e) {
            \Log::error('Error al obtener técnicos por unidad: ' . $e->getMessage());
            throw new \Exception('Error al obtener técnicos por unidad: ' . $e->getMessage());
        }
    }
}
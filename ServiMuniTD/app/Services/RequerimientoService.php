<?php

namespace App\Services;

use Google\Service\Sheets;

class RequerimientoService extends BaseService
{
    // Métodos para gestionar Requerimientos
    public function getAll()
    {
        try {
            // Debug: Comprobar el ID de la hoja
            \Log::info('Intentando leer Requerimientos de la hoja con ID: ' . $this->spreadsheetId);
            
            $range = 'Requerimientos!A:G'; // Rango de datos para requerimientos
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                \Log::info('La hoja Requerimientos está vacía o no se encontraron datos');
                return [];
            }
            
            $headers = ['id_requerimiento', 'departamento_id', 'nombre', 'descripcion_req', 'descripcion_precio', 'privado', 'publico'];
            
            $result = [];
            foreach ($values as $index => $row) {
                // Skip the header row if it exists
                if ($index === 0) {
                    continue;
                }
                
                // Make sure we have enough elements in the row
                while (count($row) < count($headers)) {
                    $row[] = null;
                }
                
                $item = [];
                foreach ($headers as $headerIndex => $header) {
                    $value = $row[$headerIndex] ?? null;
                    
                    // Convertir strings "true"/"false" a booleanos para los campos privado y publico
                    if ($header === 'privado' || $header === 'publico') {
                        $item[$header] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $item[$header] = $value;
                    }
                }
                $result[] = $item;
            }
            
            \Log::info('Se encontraron ' . count($result) . ' requerimientos');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (requerimientos): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Obtiene un requerimiento por su ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById($id)
    {
        try {
            \Log::info('Buscando requerimiento con ID: ' . $id);
            
            // Obtener todos los requerimientos
            $requerimientos = $this->getAll();
            
            // Buscar el requerimiento con el ID especificado
            foreach ($requerimientos as $requerimiento) {
                if (isset($requerimiento['id_requerimiento']) && $requerimiento['id_requerimiento'] == $id) {
                    \Log::info('Requerimiento encontrado');
                    return $requerimiento;
                }
            }
            
            \Log::info('Requerimiento no encontrado');
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al buscar requerimiento por ID: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al buscar el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Crea un nuevo requerimiento
     *
     * @param array $data
     * @return array
     */
    public function create($data)
    {
        try {
            \Log::info('Iniciando creación de requerimiento: ' . $data['nombre']);
            
            // Obtener todos los requerimientos para determinar el próximo ID
            $requerimientos = $this->getAll();
            $nextId = 1;
            
            if (!empty($requerimientos)) {
                // Encontrar el ID más alto y sumar 1
                $maxId = max(array_column($requerimientos, 'id_requerimiento'));
                $nextId = intval($maxId) + 1;
            }
            
            \Log::info('Nuevo ID para el requerimiento: ' . $nextId);
            
            // Convertir valores booleanos a string para almacenar en la hoja
            $privado = isset($data['privado']) && $data['privado'] ? 'TRUE' : 'FALSE';
            $publico = isset($data['publico']) && $data['publico'] ? 'TRUE' : 'FALSE';
            
            // Preparar datos para insertar
            $values = [
                [
                    $nextId,
                    $data['departamento_id'],
                    $data['nombre'],
                    $data['descripcion_req'],
                    $data['descripcion_precio'],
                    $privado,
                    $publico
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($requerimientos) + 2; // +1 para los encabezados, +1 para la siguiente fila
            \Log::info('Insertando en fila: ' . $nextRow);
            
            // Si la hoja está vacía, asegurar que existe y crear los encabezados
            if (empty($requerimientos)) {
                \Log::info('La hoja está vacía, intentando crear encabezados');
                
                // Crear encabezados
                try {
                    $headerValues = [
                        ['id_requerimiento', 'departamento_id', 'nombre', 'descripcion_req', 'descripcion_precio', 'privado', 'publico']
                    ];
                    
                    $headerBody = new \Google\Service\Sheets\ValueRange([
                        'values' => $headerValues
                    ]);
                    
                    $headerRange = "Requerimientos!A1:G1";
                    
                    $this->sheets->spreadsheets_values->update(
                        $this->spreadsheetId, 
                        $headerRange, 
                        $headerBody, 
                        ['valueInputOption' => 'RAW']
                    );
                    
                    \Log::info('Encabezados creados correctamente');
                } catch (\Exception $e) {
                    \Log::error('Error al crear encabezados: ' . $e->getMessage());
                }
            }
            
            $range = "Requerimientos!A$nextRow:G$nextRow";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            \Log::info('Ejecutando actualización en Google Sheets');
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Requerimiento creado correctamente');
            
            // Devolver los datos con el ID asignado
            $data['id_requerimiento'] = $nextId;
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear requerimiento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al crear el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un requerimiento existente
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        try {
            \Log::info('Iniciando actualización de requerimiento ID: ' . $id);
            
            // Obtener todos los requerimientos para encontrar la fila correcta
            $requerimientos = $this->getAll();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($requerimientos as $index => $requerimiento) {
                if (isset($requerimiento['id_requerimiento']) && $requerimiento['id_requerimiento'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Requerimiento con ID $id no encontrado");
                throw new \Exception("Requerimiento con ID $id no encontrado");
            }
            
            \Log::info('Requerimiento encontrado en fila: ' . $rowIndex);
            
            // Convertir valores booleanos a string para almacenar en la hoja
            $privado = isset($data['privado']) && $data['privado'] ? 'TRUE' : 'FALSE';
            $publico = isset($data['publico']) && $data['publico'] ? 'TRUE' : 'FALSE';
            
            // Preparar datos para actualizar
            $values = [
                [
                    $id, // Mantener el mismo ID
                    $data['departamento_id'],
                    $data['nombre'],
                    $data['descripcion_req'],
                    $data['descripcion_precio'],
                    $privado,
                    $publico
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Requerimientos!A$rowIndex:G$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            \Log::info('Ejecutando actualización en Google Sheets');
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Requerimiento actualizado correctamente');
            
            // Asegurar que el ID esté en los datos devueltos
            $data['id_requerimiento'] = $id;
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (actualizar requerimiento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al actualizar el requerimiento: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un requerimiento
     *
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            \Log::info('Iniciando eliminación de requerimiento ID: ' . $id);
            
            // Obtener todos los requerimientos
            $requerimientos = $this->getAll();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($requerimientos as $index => $requerimiento) {
                if (isset($requerimiento['id_requerimiento']) && $requerimiento['id_requerimiento'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Requerimiento con ID $id no encontrado");
                throw new \Exception("Requerimiento con ID $id no encontrado");
            }
            
            \Log::info('Requerimiento encontrado en fila: ' . $rowIndex);
            
            // Preparar solicitud para eliminar la fila (reemplazando con valores vacíos)
            $values = [
                array_fill(0, 7, '') // 7 celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Requerimientos!A$rowIndex:G$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            \Log::info('Ejecutando actualización en Google Sheets');
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Requerimiento eliminado correctamente');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (eliminar requerimiento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al eliminar el requerimiento: ' . $e->getMessage());
        }
    }
}
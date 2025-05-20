<?php

namespace App\Services;

use Google\Service\Sheets;

class DepartamentoService extends BaseService
{
    // Métodos para gestionar departamentos
    public function getAllDepartamentos()
    {
        try {
            // Debug: Comprobar el ID de la hoja
            \Log::info('Intentando leer Departamentos de la hoja con ID: ' . $this->spreadsheetId);
            
            $range = 'Departamentos!A:B'; // Rango de datos para departamentos (id, nombre)
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                \Log::info('La hoja Departamentos está vacía o no se encontraron datos');
                return [];
            }
            
            $headers = ['id', 'nombre']; // Columnas para departamentos
            
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
                    $item[$header] = $row[$headerIndex] ?? null;
                }
                $result[] = $item;
            }
            
            \Log::info('Se encontraron ' . count($result) . ' departamentos');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (departamentos): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }

    public function getDepartamentosById($id)
    {
        $departamentos = $this->getAllDepartamentos();
        
        foreach ($departamentos as $departamento) {
            if (isset($departamento['id']) && $departamento['id'] == $id) {
                return $departamento;
            }
        }
        
        return null;
    }

    public function createDepartamentos($nombre)
    {
        try {
            \Log::info('Iniciando creación de departamento: ' . $nombre);
            
            // Obtener todos los departamentos para determinar el próximo ID
            $departamentos = $this->getAllDepartamentos();
            $nextId = 1;
            
            if (!empty($departamentos)) {
                // Encontrar el ID más alto y sumar 1
                $maxId = max(array_column($departamentos, 'id'));
                $nextId = intval($maxId) + 1;
            }
            
            \Log::info('Nuevo ID para el departamento: ' . $nextId);
            
            // Preparar datos para insertar
            $values = [
                [$nextId, $nombre]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($departamentos) + 2; // +1 para los encabezados, +1 para la siguiente fila
            \Log::info('Insertando en fila: ' . $nextRow);
            
            // Si la hoja está vacía, asegurar que existe y crear los encabezados
            if (empty($departamentos)) {
                \Log::info('La hoja está vacía, intentando crear encabezados');
                
                // Intentar crear los encabezados
                try {
                    $headerValues = [
                        ['id', 'nombre']
                    ];
                    
                    $headerBody = new \Google\Service\Sheets\ValueRange([
                        'values' => $headerValues
                    ]);
                    
                    $headerRange = "Departamentos!A1:B1";
                    
                    $this->sheets->spreadsheets_values->update(
                        $this->spreadsheetId, 
                        $headerRange, 
                        $headerBody, 
                        ['valueInputOption' => 'RAW']
                    );
                    
                    \Log::info('Encabezados creados correctamente');
                } catch (\Exception $e) {
                    \Log::error('Error al crear encabezados: ' . $e->getMessage());
                    // Continuar con la inserción de datos
                }
            }
            
            $range = "Departamentos!A$nextRow:B$nextRow";
            
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
            
            \Log::info('Departamento creado correctamente. Resultado: ' . json_encode($result));
            
            return [
                'id' => $nextId,
                'nombre' => $nombre
            ];
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear departamento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al crear el departamento: ' . $e->getMessage());
        }
    }

    public function updateDepartamentos($id, $nombre)
    {
        try {
            \Log::info('Iniciando actualización de departamento ID: ' . $id . ', Nombre: ' . $nombre);
            
            // Obtener todos los departamentos para encontrar la fila correcta
            $departamentos = $this->getAllDepartamentos();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($departamentos as $index => $departamento) {
                if (isset($departamento['id']) && $departamento['id'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Departamento con ID $id no encontrado");
                throw new \Exception("Departamento con ID $id no encontrado");
            }
            
            \Log::info('Departamento encontrado en fila: ' . $rowIndex);
            
            // Preparar datos para actualizar
            $values = [
                [$id, $nombre]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Departamentos!A$rowIndex:B$rowIndex";
            
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
            
            \Log::info('Departamento actualizado correctamente. Resultado: ' . json_encode($result));
            
            return [
                'id' => $id,
                'nombre' => $nombre
            ];
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (actualizar departamento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al actualizar el departamento: ' . $e->getMessage());
        }
    }

    public function deleteDepartamentos($id)
    {
        try {
            \Log::info('Iniciando eliminación de departamento ID: ' . $id);
            
            // Obtener todos los departamentos
            $departamentos = $this->getAllDepartamentos();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($departamentos as $index => $departamento) {
                if (isset($departamento['id']) && $departamento['id'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Departamento con ID $id no encontrado");
                throw new \Exception("Departamento con ID $id no encontrado");
            }
            
            \Log::info('Departamento encontrado en fila: ' . $rowIndex);
            
            // Preparar solicitud para eliminar la fila (reemplazando con valores vacíos)
            $values = [
                ['', ''] // Celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Departamentos!A$rowIndex:B$rowIndex";
            
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
            
            \Log::info('Departamento eliminado correctamente. Resultado: ' . json_encode($result));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (eliminar departamento): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al eliminar el departamento: ' . $e->getMessage());
        }
    }
  /**
 * Obtiene todos los requerimientos para un departamento específico
 *
 * @param int $departamentoId
 * @return array
 */
public function getRequerimientosByDepartamento($departamentoId)
{
    try {
        \Log::info('Buscando requerimientos para el departamento ID: ' . $departamentoId);
        
        // Obtener todos los requerimientos
        $requerimientos = $this->getAllRequerimientos();
        
        // Filtrar por departamento_id
        $filteredRequerimientos = array_filter($requerimientos, function($requerimiento) use ($departamentoId) {
            return isset($requerimiento['departamento_id']) && $requerimiento['departamento_id'] == $departamentoId;
        });
        
        \Log::info('Se encontraron ' . count($filteredRequerimientos) . ' requerimientos para el departamento');
        return array_values($filteredRequerimientos); // Reindexar el array
    } catch (\Exception $e) {
        \Log::error('Error al buscar requerimientos por departamento: ' . $e->getMessage());
        \Log::error('Traza: ' . $e->getTraceAsString());
        throw new \Exception('Error al buscar requerimientos por departamento: ' . $e->getMessage());
    }
}
}
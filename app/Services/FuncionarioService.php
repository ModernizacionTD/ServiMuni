<?php

namespace App\Services;

use Google\Service\Sheets;

class FuncionarioService extends BaseService
{
    /**
     * Obtiene todos los funcionarios del sistema
     *
     * @return array
     */
    public function getAllFuncionarios()
    {
        try {
            $range = 'Funcionarios!A:F'; // Rango de datos para funcionarios (id, email, nombre, password, rol)
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            // Asumiendo que la primera fila es encabezados
            $headers = ['id', 'email', 'nombre', 'password', 'rol', 'departamento_id']; 
            
            $result = [];
            foreach ($values as $index => $row) {
                // Saltar la fila de encabezados si existe
                if ($index === 0) {
                    continue;
                }
                
                // Asegurarse de que tengamos suficientes elementos en la fila
                while (count($row) < count($headers)) {
                    $row[] = null;
                }
                
                $item = [];
                foreach ($headers as $headerIndex => $header) {
                    $item[$header] = $row[$headerIndex] ?? null;
                }
                $result[] = $item;
            }
            
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (funcionarios): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }


    /**
     * Obtiene un funcionario por su ID
     *
     * @param string $id
     * @return array|null
     */
    public function getFuncionarioById($id)
    {
        try {
            \Log::info('Buscando funcionario con ID: ' . $id);
            
            // Obtener todos los funcionarios
            $funcionarios = $this->getAllFuncionarios();
            
            // Buscar el funcionario con el ID especificado
            foreach ($funcionarios as $funcionario) {
                if (isset($funcionario['id']) && $funcionario['id'] == $id) {
                    \Log::info('Funcionario encontrado');
                    return $funcionario;
                }
            }
            
            \Log::info('Funcionario no encontrado');
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al buscar funcionario por ID: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al buscar el funcionario: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtiene un funcionario por su email
     *
     * @param string $email
     * @return array|null
     */
    public function getFuncionarioByEmail($email)
    {
        \Log::info('Buscando funcionario con email: ' . $email);
        
        $funcionarios = $this->getAllFuncionarios();
        \Log::info('Funcionarios encontrados: ' . count($funcionarios));
        
        foreach ($funcionarios as $item) {
            \Log::info('Comparando con: ' . ($item['email'] ?? 'null'));
            if (isset($item['email']) && trim(strtolower($item['email'])) === trim(strtolower($email))) {
                \Log::info('Funcionario encontrado!');
                return $item;
            }
        }
        
        \Log::info('Funcionario NO encontrado');
        return null;
    }
    
    /**
     * Crea un nuevo funcionario
     *
     * @param array $data
     * @return array
     */
    public function createFuncionario($data)
    {
        try {
            \Log::info('Iniciando creación de funcionario: ' . $data['nombre']);
            
            // Obtener todos los funcionarios para determinar el próximo ID
            $funcionarios = $this->getAllFuncionarios();
            $nextId = 1;
            
            if (!empty($funcionarios)) {
                // Encontrar el ID más alto y sumar 1
                $maxId = max(array_column($funcionarios, 'id'));
                $nextId = intval($maxId) + 1;
            }
            
            \Log::info('Nuevo ID para el funcionario: ' . $nextId);
            
            // Preparar datos para insertar
            $values = [
                [
                    $nextId,
                    $data['email'],
                    $data['nombre'],
                    $data['password'],
                    $data['rol'],
                    $data['departamento_id'] ?? null // Asegurarse de que el departamento_id esté presente
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($funcionarios) + 2; // +1 para los encabezados, +1 para la siguiente fila
            
            // Si la hoja está vacía, asegurar que existe y crear los encabezados
            if (empty($funcionarios)) {
                $this->createHeadersIfNeeded('Funcionarios!A1:E1', ['id', 'email', 'nombre', 'password', 'rol']);
            }
            
            $range = "Funcionarios!A$nextRow:F$nextRow";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Funcionario creado correctamente');
            
            // Devolver los datos con el ID asignado
            $data['id'] = $nextId;
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear funcionario): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al crear el funcionario: ' . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un funcionario existente
     *
     * @param string $id
     * @param array $data
     * @return array
     */
    public function updateFuncionario($id, $data)
    {
        try {
            \Log::info('Iniciando actualización de funcionario ID: ' . $id);
            
            // Obtener todos los funcionarios para encontrar la fila correcta
            $funcionarios = $this->getAllFuncionarios();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($funcionarios as $index => $funcionario) {
                if (isset($funcionario['id']) && $funcionario['id'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                throw new \Exception("Funcionario con ID $id no encontrado");
            }
            
            // Preparar datos para actualizar
            $values = [
                [
                    $id, // Mantener el mismo ID
                    $data['email'],
                    $data['nombre'],
                    $data['password'],
                    $data['rol'],
                    $data['departamento_id'] ?? null // Asegurarse de que el departamento_id esté presente
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Funcionarios!A$rowIndex:F$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Funcionario actualizado correctamente');
            
            // Asegurar que el ID esté en los datos devueltos
            $data['id'] = $id;
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (actualizar funcionario): ' . $e->getMessage());
            throw new \Exception('Error al actualizar el funcionario: ' . $e->getMessage());
        }
    }
    
    /**
     * Elimina un funcionario
     *
     * @param string $id
     * @return bool
     */
    public function deleteFuncionario($id)
    {
        try {
            \Log::info('Iniciando eliminación de funcionario ID: ' . $id);
            
            // Obtener todos los funcionarios
            $funcionarios = $this->getAllFuncionarios();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al ID
            foreach ($funcionarios as $index => $funcionario) {
                if (isset($funcionario['id']) && $funcionario['id'] == $id) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                throw new \Exception("Funcionario con ID $id no encontrado");
            }
            
            // Preparar solicitud para eliminar la fila (reemplazando con valores vacíos)
            $values = [
                ['', '', '', '', '', ''] // 5 celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Funcionarios!A$rowIndex:F$rowIndex";
            
            $params = [
                'valueInputOption' => 'RAW'
            ];
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                $params
            );
            
            \Log::info('Funcionario eliminado correctamente');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (eliminar funcionario): ' . $e->getMessage());
            throw new \Exception('Error al eliminar el funcionario: ' . $e->getMessage());
        }
    }
}
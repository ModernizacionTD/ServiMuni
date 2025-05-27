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
            // CAMBIAR EL RANGO PARA INCLUIR LA COLUMNA UNIDAD_ID (G)
            $range = 'Funcionarios!A:G'; // Ahora incluye: id, email, nombre, password, rol, departamento_id, unidad_id
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                return [];
            }
            
            // ACTUALIZAR LOS HEADERS PARA INCLUIR UNIDAD_ID
            $headers = ['id', 'email', 'nombre', 'password', 'rol', 'departamento_id', 'unidad_id']; 
            
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
     * API para obtener funcionarios con rol de técnico
     * Agregar este método al BandejaController o crear un nuevo ApiController
     */
    public function getTecnicos()
    {
        try {
            // Obtener todos los funcionarios
            $funcionarios = $this->funcionarioService->getAllFuncionarios();
            
            // Filtrar solo los técnicos
            $tecnicos = array_filter($funcionarios, function($funcionario) {
                return isset($funcionario['rol']) && $funcionario['rol'] === 'tecnico';
            });
            
            // Reindexar el array
            $tecnicos = array_values($tecnicos);
            
            return response()->json($tecnicos);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener técnicos: ' . $e->getMessage());
            return response()->json([], 500);
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
            
            // ACTUALIZAR PARA INCLUIR UNIDAD_ID
            $values = [
                [
                    $nextId,
                    $data['email'],
                    $data['nombre'],
                    $data['password'],
                    $data['rol'],
                    $data['departamento_id'] ?? null,
                    $data['unidad_id'] ?? null // Agregar unidad_id
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($funcionarios) + 2; // +1 para los encabezados, +1 para la siguiente fila
            
            // Si la hoja está vacía, asegurar que existe y crear los encabezados
            if (empty($funcionarios)) {
                $this->createHeadersIfNeeded('Funcionarios!A1:G1', ['id', 'email', 'nombre', 'password', 'rol', 'departamento_id', 'unidad_id']);
            }
            
            // ACTUALIZAR EL RANGO PARA INCLUIR LA COLUMNA G
            $range = "Funcionarios!A$nextRow:G$nextRow";
            
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
            
            // ACTUALIZAR PARA INCLUIR UNIDAD_ID
            $values = [
                [
                    $id, // Mantener el mismo ID
                    $data['email'],
                    $data['nombre'],
                    $data['password'],
                    $data['rol'],
                    $data['departamento_id'] ?? null,
                    $data['unidad_id'] ?? null // Agregar unidad_id
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // ACTUALIZAR EL RANGO PARA INCLUIR LA COLUMNA G
            $range = "Funcionarios!A$rowIndex:G$rowIndex";
            
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
            
            // ACTUALIZAR PARA INCLUIR LA COLUMNA G
            $values = [
                ['', '', '', '', '', '', ''] // 7 celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Funcionarios!A$rowIndex:G$rowIndex";
            
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

    /**
 * Asigna un funcionario a una unidad
 *
 * @param string $funcionarioId
 * @param string $unidadId
 * @return bool
 */
public function asignarUnidad($funcionarioId, $unidadId)
{
    try {
        \Log::info("Asignando funcionario ID: $funcionarioId a unidad ID: $unidadId");
        
        // Obtener el funcionario
        $funcionario = $this->getFuncionarioById($funcionarioId);
        
        if (!$funcionario) {
            throw new \Exception("Funcionario con ID $funcionarioId no encontrado");
        }
        
        // Actualizar el funcionario con la nueva unidad
        $funcionarioData = $funcionario;
        $funcionarioData['unidad_id'] = $unidadId;
        
        $result = $this->updateFuncionario($funcionarioId, $funcionarioData);
        
        \Log::info('Funcionario asignado correctamente a la unidad');
        
        return true;
    } catch (\Exception $e) {
        \Log::error('Error al asignar funcionario a unidad: ' . $e->getMessage());
        throw new \Exception('Error al asignar funcionario a unidad: ' . $e->getMessage());
    }
}

/**
 * Desasigna un funcionario de cualquier unidad
 *
 * @param string $funcionarioId
 * @return bool
 */
public function desasignarUnidad($funcionarioId)
{
    try {
        \Log::info("Desasignando funcionario ID: $funcionarioId de su unidad");
        
        // Obtener el funcionario
        $funcionario = $this->getFuncionarioById($funcionarioId);
        
        if (!$funcionario) {
            throw new \Exception("Funcionario con ID $funcionarioId no encontrado");
        }
        
        // Actualizar el funcionario quitando la unidad
        $funcionarioData = $funcionario;
        $funcionarioData['unidad_id'] = null;
        
        $result = $this->updateFuncionario($funcionarioId, $funcionarioData);
        
        \Log::info('Funcionario desasignado correctamente de la unidad');
        
        return true;
    } catch (\Exception $e) {
        \Log::error('Error al desasignar funcionario de unidad: ' . $e->getMessage());
        throw new \Exception('Error al desasignar funcionario de unidad: ' . $e->getMessage());
    }
}

/**
 * Desasigna todos los funcionarios de una unidad específica
 *
 * @param string $unidadId
 * @return bool
 */
public function desasignarTodosDeUnidad($unidadId)
{
    try {
        \Log::info("Desasignando todos los funcionarios de la unidad ID: $unidadId");
        
        // Obtener todos los funcionarios
        $funcionarios = $this->getAllFuncionarios();
        
        // Filtrar los que pertenecen a esta unidad
        $funcionariosUnidad = array_filter($funcionarios, function($f) use ($unidadId) {
            return isset($f['unidad_id']) && $f['unidad_id'] == $unidadId;
        });
        
        // Desasignar cada uno
        foreach ($funcionariosUnidad as $funcionario) {
            $this->desasignarUnidad($funcionario['id']);
        }
        
        \Log::info('Todos los funcionarios desasignados correctamente de la unidad');
        
        return true;
    } catch (\Exception $e) {
        \Log::error('Error al desasignar todos los funcionarios de la unidad: ' . $e->getMessage());
        throw new \Exception('Error al desasignar todos los funcionarios de la unidad: ' . $e->getMessage());
    }
}

/**
 * Obtiene todos los funcionarios asignados a una unidad
 *
 * @param string $unidadId
 * @return array
 */
public function getFuncionariosByUnidad($unidadId)
{
    try {
        \Log::info("Obteniendo funcionarios de la unidad ID: $unidadId");
        
        // Obtener todos los funcionarios
        $funcionarios = $this->getAllFuncionarios();
        
        // Filtrar los que pertenecen a esta unidad
        $funcionariosUnidad = array_filter($funcionarios, function($f) use ($unidadId) {
            return isset($f['unidad_id']) && $f['unidad_id'] == $unidadId;
        });
        
        \Log::info('Funcionarios encontrados: ' . count($funcionariosUnidad));
        
        return array_values($funcionariosUnidad); // Reindexar el array
    } catch (\Exception $e) {
        \Log::error('Error al obtener funcionarios por unidad: ' . $e->getMessage());
        throw new \Exception('Error al obtener funcionarios por unidad: ' . $e->getMessage());
    }
}

/**
 * Obtiene todos los funcionarios con rol de técnico asignados a una unidad
 *
 * @param string $unidadId
 * @return array
 */
public function getTecnicosByUnidad($unidadId)
{
    try {
        \Log::info("Obteniendo técnicos de la unidad ID: $unidadId");
        
        // Obtener todos los funcionarios de la unidad
        $funcionariosUnidad = $this->getFuncionariosByUnidad($unidadId);
        
        // Filtrar solo los técnicos
        $tecnicos = array_filter($funcionariosUnidad, function($f) {
            return isset($f['rol']) && $f['rol'] === 'tecnico';
        });
        
        \Log::info('Técnicos encontrados: ' . count($tecnicos));
        
        return array_values($tecnicos); // Reindexar el array
    } catch (\Exception $e) {
        \Log::error('Error al obtener técnicos por unidad: ' . $e->getMessage());
        throw new \Exception('Error al obtener técnicos por unidad: ' . $e->getMessage());
    }
}

}
<?php

namespace App\Services;

use Google\Service\Sheets;

class UsuarioService extends BaseService
{
    /**
     * Obtiene todos los funcionarios del sistema
     *
     * @return array
     */
    // Métodos para gestionar Usuarios
    public function getAllUsuarios()
    {
        try {
            // Debug: Comprobar el ID de la hoja
            \Log::info('Intentando leer Usuarios de la hoja con ID: ' . $this->spreadsheetId);
            
            $range = 'Usuarios!A:M'; // Rango de datos para usuarios (rut, tipo_persona, nombre, etc.)
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                \Log::info('La hoja Usuarios está vacía o no se encontraron datos');
                return [];
            }
            
            $headers = ['rut', 'tipo_persona', 'nombre', 'apellidos', 'uso_ns', 'nombre_social', 'fecha_nacimiento', 'genero', 'telefono', 'telefono_2', 'email', 'email_2', 'direccion']; // Columnas para usuarios

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
            
            \Log::info('Se encontraron ' . count($result) . ' usuarios');
            return $result;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (usuarios): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Obtiene un usuario por su RUT
     *
     * @param string $rut
     * @return array|null
     */
    public function getUsuarioByRut($rut)
    {
        try {
            \Log::info('Buscando usuario con RUT: ' . $rut);
            
            // Obtener todos los usuarios
            $usuarios = $this->getAllUsuarios();
            
            // Buscar el usuario con el RUT especificado
            foreach ($usuarios as $usuario) {
                if (isset($usuario['rut']) && $usuario['rut'] == $rut) {
                    \Log::info('Usuario encontrado');
                    return $usuario;
                }
            }
            
            \Log::info('Usuario no encontrado');
            return null;
        } catch (\Exception $e) {
            \Log::error('Error al buscar usuario por RUT: ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al buscar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Crea un nuevo usuario
     *
     * @param array $data
     * @return array
     */
    public function createUsuario($data)
    {
        try {
            \Log::info('Iniciando creación de usuario: ' . $data['nombre'] . ' ' . $data['apellidos']);
            
            // Obtener todos los usuarios
            $usuarios = $this->getAllUsuarios();
            
            // Verificar si el RUT ya existe
            foreach ($usuarios as $usuario) {
                if (isset($usuario['rut']) && $usuario['rut'] == $data['rut']) {
                    throw new \Exception('Ya existe un usuario con este RUT');
                }
            }
            
            // Preparar datos para insertar
            $values = [
                [
                    $data['rut'],
                    $data['tipo_persona'],
                    $data['nombre'],
                    $data['apellidos'],
                    $data['uso_ns'],
                    $data['nombre_social'] ?? '',
                    $data['fecha_nacimiento'],
                    $data['genero'],
                    $data['telefono'],
                    $data['telefono_2'] ?? '',
                    $data['email'],
                    $data['email_2'] ?? '',
                    $data['direccion']
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            // Determinar la siguiente fila disponible
            $nextRow = count($usuarios) + 2; // +1 para los encabezados, +1 para la siguiente fila
            \Log::info('Insertando en fila: ' . $nextRow);
            
            // Si la hoja está vacía, asegurar que existe y crear los encabezados
            if (empty($usuarios)) {
                \Log::info('La hoja está vacía, intentando crear encabezados');
                
                // Crear encabezados
                try {
                    $headerValues = [
                        ['rut', 'tipo_persona', 'nombre', 'apellidos', 'uso_ns', 'nombre_social', 'fecha_nacimiento', 'genero', 'telefono', 'telefono_2', 'email', 'email_2', 'direccion']
                    ];
                    
                    $headerBody = new \Google\Service\Sheets\ValueRange([
                        'values' => $headerValues
                    ]);
                    
                    $headerRange = "Usuarios!A1:M1";
                    
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
            
            $range = "Usuarios!A$nextRow:M$nextRow";
            
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
            
            \Log::info('Usuario creado correctamente');
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (crear usuario): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un usuario existente
     *
     * @param string $rut
     * @param array $data
     * @return array
     */
    public function updateUsuario($rut, $data)
    {
        try {
            \Log::info('Iniciando actualización de usuario RUT: ' . $rut);
            
            // Obtener todos los usuarios para encontrar la fila correcta
            $usuarios = $this->getAllUsuarios();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al RUT
            foreach ($usuarios as $index => $usuario) {
                if (isset($usuario['rut']) && $usuario['rut'] == $rut) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Usuario con RUT $rut no encontrado");
                throw new \Exception("Usuario con RUT $rut no encontrado");
            }
            
            \Log::info('Usuario encontrado en fila: ' . $rowIndex);
            
            // Preparar datos para actualizar
            $values = [
                [
                    $data['rut'],
                    $data['tipo_persona'],
                    $data['nombre'],
                    $data['apellidos'],
                    $data['uso_ns'],
                    $data['nombre_social'] ?? '',
                    $data['fecha_nacimiento'],
                    $data['genero'],
                    $data['telefono'],
                    $data['telefono_2'] ?? '',
                    $data['email'],
                    $data['email_2'] ?? '',
                    $data['direccion']
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Usuarios!A$rowIndex:M$rowIndex";
            
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
            
            \Log::info('Usuario actualizado correctamente');
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (actualizar usuario): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario
     *
     * @param string $rut
     * @return bool
     */
    public function deleteUsuario($rut)
    {
        try {
            \Log::info('Iniciando eliminación de usuario RUT: ' . $rut);
            
            // Obtener todos los usuarios para encontrar la fila correcta
            $usuarios = $this->getAllUsuarios();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al RUT
            foreach ($usuarios as $index => $usuario) {
                if (isset($usuario['rut']) && $usuario['rut'] == $rut) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para el índice basado en 0
                    break;
                }
            }
            
            if ($rowIndex === null) {
                \Log::error("Usuario con RUT $rut no encontrado");
                throw new \Exception("Usuario con RUT $rut no encontrado");
            }
            
            \Log::info('Usuario encontrado en fila: ' . $rowIndex);
            
            // Preparar solicitud para eliminar la fila (reemplazando con valores vacíos)
            $values = [
                array_fill(0, 13, '') // 13 celdas vacías para "eliminar" la fila
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Usuarios!A$rowIndex:M$rowIndex";
            
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
            
            \Log::info('Usuario eliminado correctamente');
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Sheets API error (eliminar usuario): ' . $e->getMessage());
            \Log::error('Traza: ' . $e->getTraceAsString());
            throw new \Exception('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
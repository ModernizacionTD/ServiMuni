<?php

namespace App\Services;

use Google\Service\Sheets;

class UsuarioService extends BaseService
{
    /**
     * Obtiene todos los usuarios del sistema
     *
     * @return array
     */
    public function getAllUsuarios()
    {
        try {
            \Log::info('Intentando leer Usuarios de la hoja con ID: ' . $this->spreadsheetId);
            
            $range = 'Usuarios!A:M'; // Rango de datos para usuarios
            $response = $this->sheets->spreadsheets_values->get($this->spreadsheetId, $range);
            $values = $response->getValues();
            
            if (empty($values)) {
                \Log::info('La hoja Usuarios está vacía o no se encontraron datos');
                return [];
            }
            
            $headers = ['rut', 'tipo_persona', 'nombre', 'apellidos', 'uso_ns', 'nombre_social', 'fecha_nacimiento', 'genero', 'telefono', 'telefono_2', 'email', 'email_2', 'direccion'];

            $result = [];
            foreach ($values as $index => $row) {
                // Skip the header row if it exists
                if ($index === 0) {
                    continue;
                }
                
                // Skip empty rows
                if (empty($row) || (count($row) === 1 && empty(trim($row[0])))) {
                    continue;
                }
                
                // Make sure we have enough elements in the row
                while (count($row) < count($headers)) {
                    $row[] = '';
                }
                
                $item = [];
                foreach ($headers as $headerIndex => $header) {
                    $item[$header] = $row[$headerIndex] ?? '';
                }
                
                // Solo agregar si tiene RUT
                if (!empty(trim($item['rut']))) {
                    $result[] = $item;
                }
            }
            
            \Log::info('Se encontraron ' . count($result) . ' usuarios válidos');
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
            
            // Limpiar el RUT para comparación
            $rutLimpio = $this->limpiarRut($rut);
            
            // Obtener todos los usuarios
            $usuarios = $this->getAllUsuarios();
            
            // Buscar el usuario con el RUT especificado
            foreach ($usuarios as $usuario) {
                if (isset($usuario['rut']) && $this->limpiarRut($usuario['rut']) === $rutLimpio) {
                    \Log::info('Usuario encontrado');
                    return $usuario;
                }
            }
            
            \Log::info('Usuario no encontrado');
            return null;
            
        } catch (\Exception $e) {
            \Log::error('Error al buscar usuario por RUT: ' . $e->getMessage());
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
            \Log::info('Iniciando creación de usuario', $data);
            
            // Verificar campos requeridos
            $requiredFields = ['rut', 'tipo_persona', 'nombre', 'telefono', 'email', 'direccion'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new \Exception("El campo '$field' es requerido");
                }
            }
            
            // Obtener todos los usuarios para verificar duplicados
            $usuarios = $this->getAllUsuarios();
            $rutLimpio = $this->limpiarRut($data['rut']);
            
            // Verificar si el RUT ya existe
            foreach ($usuarios as $usuario) {
                if (isset($usuario['rut']) && $this->limpiarRut($usuario['rut']) === $rutLimpio) {
                    throw new \Exception('Ya existe un usuario con este RUT: ' . $data['rut']);
                }
            }
            
            // Limpiar y preparar datos
            $userData = $this->prepararDatosUsuario($data);
            
            \Log::info('Datos preparados para insertar:', $userData);
            
            // Preparar valores para insertar en la hoja
            $values = [
                [
                    $userData['rut'],
                    $userData['tipo_persona'],
                    $userData['nombre'],
                    $userData['apellidos'],
                    $userData['uso_ns'],
                    $userData['nombre_social'],
                    $userData['fecha_nacimiento'],
                    $userData['genero'],
                    $userData['telefono'],
                    $userData['telefono_2'],
                    $userData['email'],
                    $userData['email_2'],
                    $userData['direccion']
                ]
            ];
            
            // Crear o verificar encabezados si la hoja está vacía
            if (empty($usuarios)) {
                $this->crearEncabezados();
            }
            
            // Determinar la siguiente fila disponible
            $nextRow = count($usuarios) + 2; // +1 para encabezados, +1 para siguiente fila
            \Log::info('Insertando en fila: ' . $nextRow);
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Usuarios!A$nextRow:M$nextRow";
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Usuario creado correctamente en Google Sheets');
            \Log::info('Respuesta de Google Sheets:', [
                'updatedCells' => $result->getUpdatedCells(),
                'updatedColumns' => $result->getUpdatedColumns(),
                'updatedRows' => $result->getUpdatedRows()
            ]);
            
            return $userData;
            
        } catch (\Exception $e) {
            \Log::error('Error al crear usuario: ' . $e->getMessage());
            \Log::error('Datos recibidos:', $data);
            \Log::error('Traza completa: ' . $e->getTraceAsString());
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
            \Log::info('Iniciando actualización de usuario RUT: ' . $rut, $data);
            
            $rutLimpio = $this->limpiarRut($rut);
            $usuarios = $this->getAllUsuarios();
            $rowIndex = null;
            $usuarioActual = null;
            
            // Buscar la fila que corresponde al RUT y obtener datos actuales
            foreach ($usuarios as $index => $usuario) {
                if (isset($usuario['rut']) && $this->limpiarRut($usuario['rut']) === $rutLimpio) {
                    $rowIndex = $index + 2; // +1 para encabezados, +1 para índice base-0
                    $usuarioActual = $usuario;
                    break;
                }
            }
            
            if ($rowIndex === null || $usuarioActual === null) {
                throw new \Exception("Usuario con RUT $rut no encontrado");
            }
            
            \Log::info('Usuario encontrado en fila: ' . $rowIndex);
            
            // Combinar datos actuales con los nuevos datos
            $datosCompletos = array_merge($usuarioActual, $data);
            // Asegurarse de que el RUT esté presente
            $datosCompletos['rut'] = $rut;
            
            // Preparar datos
            $userData = $this->prepararDatosUsuario($datosCompletos);
            
            $values = [
                [
                    $userData['rut'],
                    $userData['tipo_persona'],
                    $userData['nombre'],
                    $userData['apellidos'],
                    $userData['uso_ns'],
                    $userData['nombre_social'],
                    $userData['fecha_nacimiento'],
                    $userData['genero'],
                    $userData['telefono'],
                    $userData['telefono_2'],
                    $userData['email'],
                    $userData['email_2'],
                    $userData['direccion']
                ]
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Usuarios!A$rowIndex:M$rowIndex";
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Usuario actualizado correctamente');
            return $userData;
            
        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());
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
            
            $rutLimpio = $this->limpiarRut($rut);
            $usuarios = $this->getAllUsuarios();
            $rowIndex = null;
            
            // Buscar la fila que corresponde al RUT
            foreach ($usuarios as $index => $usuario) {
                if (isset($usuario['rut']) && $this->limpiarRut($usuario['rut']) === $rutLimpio) {
                    $rowIndex = $index + 2;
                    break;
                }
            }
            
            if ($rowIndex === null) {
                throw new \Exception("Usuario con RUT $rut no encontrado");
            }
            
            // "Eliminar" llenando con valores vacíos
            $values = [
                array_fill(0, 13, '') // 13 columnas vacías
            ];
            
            $body = new \Google\Service\Sheets\ValueRange([
                'values' => $values
            ]);
            
            $range = "Usuarios!A$rowIndex:M$rowIndex";
            
            $result = $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $body, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Usuario eliminado correctamente');
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Error al eliminar usuario: ' . $e->getMessage());
            throw new \Exception('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

        /**
     * Prepara y limpia los datos del usuario para inserción
     *
     * @param array $data
     * @return array
     */
    private function prepararDatosUsuario($data)
    {
        // Verificar que el RUT esté presente
        if (!isset($data['rut']) || empty($data['rut'])) {
            throw new \Exception('El RUT es requerido para preparar los datos del usuario');
        }

        return [
            'rut' => trim($data['rut']),
            'tipo_persona' => $data['tipo_persona'] ?? 'Natural',
            'nombre' => trim($data['nombre'] ?? ''),
            'apellidos' => trim($data['apellidos'] ?? ''),
            'uso_ns' => $data['uso_ns'] ?? 'No',
            'nombre_social' => trim($data['nombre_social'] ?? ''),
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? '1900-01-01',
            'genero' => $data['genero'] ?? 'No decir',
            'telefono' => trim($data['telefono'] ?? ''),
            'telefono_2' => trim($data['telefono_2'] ?? ''),
            'email' => trim($data['email'] ?? ''),
            'email_2' => trim($data['email_2'] ?? ''),
            'direccion' => trim($data['direccion'] ?? '')
        ];
    }

    /**
     * Limpia el RUT para comparaciones
     *
     * @param string $rut
     * @return string
     */
    private function limpiarRut($rut)
    {
        return strtolower(preg_replace('/[^0-9kK]/', '', $rut));
    }

    /**
     * Crea los encabezados en la hoja si no existen
     */
    private function crearEncabezados()
    {
        try {
            \Log::info('Creando encabezados en hoja Usuarios');
            
            $headerValues = [
                ['RUT', 'Tipo Persona', 'Nombre', 'Apellidos', 'Uso NS', 'Nombre Social', 'Fecha Nacimiento', 'Género', 'Teléfono', 'Teléfono 2', 'Email', 'Email 2', 'Dirección']
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
            throw new \Exception('Error al crear encabezados: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Services;

use Google\Service\Sheets;

abstract class BaseService
{
    protected $sheets;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->sheets = app('sheets');
        $this->spreadsheetId = config('services.google.sheet_id');
    }
    
    /**
     * Crea los encabezados en una hoja si es necesario
     *
     * @param string $range
     * @param array $headers
     * @return void
     */
    protected function createHeadersIfNeeded($range, $headers)
    {
        try {
            $headerValues = [$headers];
            
            $headerBody = new \Google\Service\Sheets\ValueRange([
                'values' => $headerValues
            ]);
            
            $this->sheets->spreadsheets_values->update(
                $this->spreadsheetId, 
                $range, 
                $headerBody, 
                ['valueInputOption' => 'RAW']
            );
            
            \Log::info('Encabezados creados correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al crear encabezados: ' . $e->getMessage());
        }
    }
}
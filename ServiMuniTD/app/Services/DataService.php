<?php

namespace App\Services;

use Google\Service\Sheets;

class DataService
{
    protected $sheets;
    protected $spreadsheetId;

    public function __construct()
    {
        $this->sheets = app('sheets');
        $this->spreadsheetId = config('services.google.sheet_id');
    }


    public function getByEmail($email)
    {
        $data = $this->getAll();
        
        foreach ($data as $item) {
            if (isset($item['email']) && $item['email'] === $email) {
                return $item;
            }
        }
        
        return null;
    }









    
}
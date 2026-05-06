<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUdaraModel extends Model
{
    protected $table = 'data_udara';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    // PERBAIKAN: Tentukan allowedFields dengan jelas
    protected $allowedFields = [
        'temperature', 
        'humidity', 
        'pm1_0', 
        'pm2_5', 
        'pm10', 
        'pollutant'
    ];
    
    // PERBAIKAN: Gunakan timestamps
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'timestamp';
    protected $updatedField = null; // Tidak perlu updated field
    
    // PERBAIKAN: Validation rules yang tepat
    protected $validationRules = [
        'temperature' => 'permit_empty|numeric',
        'humidity' => 'permit_empty|numeric',
        'pm1_0' => 'permit_empty|integer',
        'pm2_5' => 'permit_empty|integer',
        'pm10' => 'permit_empty|integer',
        'pollutant' => 'permit_empty|numeric'
    ];
    
    protected $skipValidation = false;
    
    /**
     * Insert data dengan aman
     */
    public function insertData($data)
    {
        // Hanya ambil field yang diizinkan
        $filteredData = [];
        foreach ($this->allowedFields as $field) {
            if (isset($data[$field])) {
                $filteredData[$field] = $data[$field];
            }
        }
        
        // Set default values if needed
        if (!isset($filteredData['pm1_0'])) $filteredData['pm1_0'] = 0;
        if (!isset($filteredData['pm2_5'])) $filteredData['pm2_5'] = 0;
        if (!isset($filteredData['pm10'])) $filteredData['pm10'] = 0;
        if (!isset($filteredData['pollutant'])) $filteredData['pollutant'] = 0;
        
        // Insert ke database
        return $this->insert($filteredData);
    }
}
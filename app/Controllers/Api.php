<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Api extends BaseController
{
    use ResponseTrait;

    public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }

    public function options($any = null)
    {
        return $this->response->setStatusCode(200)->setBody('');
    }

    public function insertData()
    {
        try {
            // Get data from request
            $data = [];
            
            if (strtolower($this->request->getMethod()) === 'post') {
                $json = $this->request->getBody();
                $data = json_decode($json, true) ?: $this->request->getPost();
            } else {
                $data = $this->request->getGet();
            }
            
            // Extract values with defaults
            $temperature = isset($data['temperature']) ? (float)$data['temperature'] : null;
            $humidity = isset($data['humidity']) ? (float)$data['humidity'] : null;
            $pm1_0 = isset($data['pm1_0']) ? (int)$data['pm1_0'] : 0;
            $pm2_5 = isset($data['pm2_5']) ? (int)$data['pm2_5'] : 0;
            $pm10 = isset($data['pm10']) ? (int)$data['pm10'] : 0;
            $pollutant = isset($data['pollutant']) ? (float)$data['pollutant'] : 0;
            $ozone = isset($data['ozone']) ? (float)$data['ozone'] : 0;
            $no2 = isset($data['no2']) ? (float)$data['no2'] : 0;
            
            // Validate
            if ($temperature === null || $humidity === null) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Temperature and humidity required',
                        'received' => $data
                    ]);
            }
            
            // Direct database insert using Query Builder
            $db = \Config\Database::connect();
            
            $insertData = [
                'temperature' => $temperature,
                'humidity' => $humidity,
                'pm1_0' => $pm1_0,
                'pm2_5' => $pm2_5,
                'pm10' => $pm10,
                'pollutant' => $pollutant,
                'ozone' => $ozone,
                'no2' => $no2,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $result = $db->table('data_udara')->insert($insertData);
            
            if ($result) {
                $insertId = $db->insertID();
                
                return $this->response
                    ->setStatusCode(200)
                    ->setJSON([
                        'success' => true,
                        'message' => 'Data inserted successfully',
                        'id' => $insertId,
                        'data' => $insertData
                    ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON([
                        'success' => false,
                        'message' => 'Failed to insert data'
                    ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'API Error: ' . $e->getMessage());
            
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => 'Server error: ' . $e->getMessage()
                ]);
        }
    }
}
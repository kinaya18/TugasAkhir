<?php

namespace App\Models;

use CodeIgniter\Model;

class PredictionModel extends Model
{
    protected $table = 'data_udara_prediksi_future';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'timestamp', 
        'temperature', 
        'humidity', 
        'pm1_0', 
        'pm2_5', 
        'pm10',
        'pollutant', 
        'ozone', 
        'no2', 
        'prediction_time', 
        'model_type',
        'days_ahead',
        'interval_minutes',
        'prediction_start',
    ];
    
    protected $useTimestamps = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    // Validation rules
    protected $validationRules = [
        'temperature' => 'permit_empty|numeric',
        'humidity' => 'permit_empty|numeric|greater_than[0]|less_than_equal_to[100]',
        'pm1_0' => 'permit_empty|numeric|greater_than[0]',
        'pm2_5' => 'permit_empty|numeric|greater_than[0]',
        'pm10' => 'permit_empty|numeric|greater_than[0]',
        'pollutant' => 'permit_empty|numeric|greater_than[0]',
        'ozone' => 'permit_empty|numeric|greater_than[0]',
        'no2' => 'permit_empty|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'humidity' => [
            'greater_than' => 'Kelembaban harus lebih dari 0%',
            'less_than_equal_to' => 'Kelembaban tidak boleh lebih dari 100%'
        ],
    ];

    /**
     * Simpan prediksi baru
     */
    public function savePrediction($data)
    {
        try {
            // Validasi data
            if (!$this->validate($data)) {
                log_message('error', '[PREDICTION MODEL] Validation failed: ' . json_encode($this->errors()));
                return false;
            }
            
            // Set default values jika kosong
            if (!isset($data['timestamp'])) {
                $data['timestamp'] = date('Y-m-d H:i:s');
            }
            
            if (!isset($data['prediction_time'])) {
                $data['prediction_time'] = date('Y-m-d H:i:s');
            }
            
            if (!isset($data['model_type'])) {
                $data['model_type'] = 'svr';
            }

            if (!isset($data['days_ahead'])) {
                $data['days_ahead'] = 7;
            }

            if (!isset($data['interval_minutes'])) {
                $data['interval_minutes'] = 5;
            }
            
            // Insert data
            $inserted = $this->insert($data);
            
            if ($inserted) {
                log_message('info', '[PREDICTION MODEL] Inserted ID: ' . $this->insertID);
                return $this->insertID;
            }
            
            log_message('error', '[PREDICTION MODEL] Insert failed');
            return false;
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Simpan batch prediksi (untuk multiple records)
     */
    public function saveBatchPredictions($dataArray)
    {
        try {
            if (empty($dataArray)) {
                return false;
            }
            
            $inserted = 0;
            foreach ($dataArray as $data) {
                if ($this->savePrediction($data)) {
                    $inserted++;
                }
            }
            
            log_message('info', "[PREDICTION MODEL] Batch insert: {$inserted} records saved");
            return $inserted;
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Batch insert error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil prediksi terbaru
     */
    public function getLatestPrediction()
    {
        try {
            return $this->orderBy('id', 'DESC')->first();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get latest error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil prediksi terbaru dengan timestamp tertentu
     */
    public function getLatestPredictionByTime($hours = 24)
    {
        try {
            $dateTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
            
            return $this->where('prediction_time >=', $dateTime)
                        ->orderBy('id', 'DESC')
                        ->first();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get latest by time error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil history prediksi dengan pagination
     */
    public function getPredictionHistory($limit = 30, $page = 1)
    {
        try {
            $offset = ($page - 1) * $limit;
            
            return $this->orderBy('id', 'DESC')
                        ->limit($limit, $offset)
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get history error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil prediksi berdasarkan rentang tanggal
     */
    public function getPredictionByDateRange($startDate, $endDate)
    {
        try {
            return $this->where('timestamp >=', $startDate)
                        ->where('timestamp <=', $endDate)
                        ->orderBy('timestamp', 'ASC')
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get by date range error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil prediksi berdasarkan parameter tertentu
     */
    public function getPredictionsByParameter($parameter, $limit = 100)
    {
        try {
            if (!in_array($parameter, $this->allowedFields)) {
                return [];
            }
            
            return $this->select("id, timestamp, {$parameter}, prediction_time")
                        ->orderBy('id', 'DESC')
                        ->limit($limit)
                        ->findAll();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get by parameter error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of predictions
     */
    public function getTotalCount()
    {
        try {
            return $this->countAll();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get total count error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get statistics per parameter
     */
    public function getStatistics($hours = 24)
    {
        try {
            $dateTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
            
            $query = $this->select('
                    COUNT(*) as total_records,
                    AVG(temperature) as avg_temperature,
                    MIN(temperature) as min_temperature,
                    MAX(temperature) as max_temperature,
                    AVG(humidity) as avg_humidity,
                    MIN(humidity) as min_humidity,
                    MAX(humidity) as max_humidity,
                    AVG(pm1_0) as avg_pm1_0,
                    MIN(pm1_0) as min_pm1_0,
                    MAX(pm1_0) as max_pm1_0,
                    AVG(pm2_5) as avg_pm2_5,
                    MIN(pm2_5) as min_pm2_5,
                    MAX(pm2_5) as max_pm2_5,
                    AVG(pm10) as avg_pm10,
                    MIN(pm10) as min_pm10,
                    MAX(pm10) as max_pm10,
                    AVG(pollutant) as avg_pollutant,
                    MIN(pollutant) as min_pollutant,
                    MAX(pollutant) as max_pollutant,
                    AVG(ozone) as avg_ozone,
                    MIN(ozone) as min_ozone,
                    MAX(ozone) as max_ozone,
                    AVG(no2) as avg_no2,
                    MIN(no2) as min_no2,
                    MAX(no2) as max_no2
                ')
                ->where('prediction_time >=', $dateTime);
            
            $result = $query->first();
            
            // Format results
            if ($result) {
                foreach ($result as $key => $value) {
                    if (is_numeric($value)) {
                        $result[$key] = round((float)$value, 3);
                    }
                }
            }
            
            return $result;
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get statistics error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analisis trend per parameter
     */
    public function getTrendAnalysis($hours = 72)
    {
        try {
            $dateTime = date('Y-m-d H:i:s', strtotime("-{$hours} hours"));
            
            $data = $this->where('prediction_time >=', $dateTime)
                        ->orderBy('timestamp', 'ASC')
                        ->findAll();
            
            if (empty($data)) {
                return [];
            }
            
            $trends = [];
            $parameters = ['temperature', 'humidity', 'pm2_5', 'pm10', 'ozone', 'no2'];
            
            foreach ($parameters as $param) {
                $values = array_column($data, $param);
                $values = array_filter($values, function($v) { return $v !== null; });
                
                if (count($values) > 1) {
                    // Hitung trend menggunakan linear regression sederhana
                    $n = count($values);
                    $x = range(0, $n - 1);
                    
                    $sumX = array_sum($x);
                    $sumY = array_sum($values);
                    $sumXY = array_sum(array_map(function($x, $y) { return $x * $y; }, $x, $values));
                    $sumX2 = array_sum(array_map(function($x) { return $x * $x; }, $x));
                    
                    $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
                    
                    $trends[$param] = [
                        'slope' => round($slope, 4),
                        'direction' => $slope > 0 ? 'increasing' : ($slope < 0 ? 'decreasing' : 'stable'),
                        'first_value' => $values[0],
                        'last_value' => end($values),
                        'change_percent' => $values[0] > 0 ? round((end($values) - $values[0]) / $values[0] * 100, 2) : 0
                    ];
                } else {
                    $trends[$param] = [
                        'slope' => 0,
                        'direction' => 'insufficient_data',
                        'first_value' => $values[0] ?? null,
                        'last_value' => $values[0] ?? null,
                        'change_percent' => 0
                    ];
                }
            }
            
            return $trends;
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Trend analysis error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Hapus data prediksi lama
     */
    public function deleteOldData($days = 30)
    {
        try {
            $dateTime = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            
            return $this->where('prediction_time <', $dateTime)->delete();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Delete old data error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Hapus semua data prediksi
     */
    public function truncateTable()
    {
        try {
            return $this->truncate();
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Truncate error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek koneksi database
     */
    public function checkConnection()
    {
        try {
            $this->db->query('SELECT 1');
            return true;
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Connection check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get prediction by ID
     */
    public function getPredictionById($id)
    {
        try {
            return $this->find($id);
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Get by ID error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Update prediction
     */
    public function updatePrediction($id, $data)
    {
        try {
            return $this->update($id, $data);
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Update error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete prediction by ID
     */
    public function deletePrediction($id)
    {
        try {
            return $this->delete($id);
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION MODEL] Delete error: ' . $e->getMessage());
            return false;
        }
    }
}
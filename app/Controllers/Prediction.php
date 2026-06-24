<?php

namespace App\Controllers;

use App\Models\PredictionModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Prediction extends BaseController
{
    private $predictionModel;
    private $apiUrl;
    private $apiPort;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->predictionModel = new PredictionModel();
        $this->apiUrl = env('PYTHON_API_URL', 'http://localhost');
        $this->apiPort = env('PYTHON_API_PORT', 5000);
    }

    /**
     * Generate prediksi baru dengan memanggil API Python
     * URL: /prediction/generate
     * Method: GET
     */
    public function generate()
    {
        try {
            log_message('info', '[PREDICTION] Starting prediction generation');
            
            // Cek koneksi ke API Python
            if (!$this->checkApiHealth()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'API Python tidak tersedia. Pastikan server prediksi berjalan di port ' . $this->apiPort
                ])->setStatusCode(503);
            }

            // Panggil API Python untuk generate prediksi
            $client = \Config\Services::curlrequest();
            $response = $client->get($this->apiUrl . ':' . $this->apiPort . '/predict', [
                'timeout' => 30,
                'http_errors' => false
            ]);
            
            $result = json_decode($response->getBody(), true);
            
            // Log response
            log_message('debug', '[PREDICTION] API Response: ' . json_encode($result));
            
            // Cek hasil dari API
            if ($result && isset($result['status'])) {
                if ($result['status'] == 'success') {
                    // Prediksi berhasil, simpan ke database via model
                    $saveResult = $this->savePredictionToDatabase($result['data']);
                    
                    if ($saveResult['status'] == 'success') {
                        return $this->response->setJSON([
                            'status' => 'success',
                            'message' => 'Prediksi berhasil dibuat dan disimpan',
                            'data' => $result['data'],
                            'saved_id' => $saveResult['id']
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'status' => 'warning',
                            'message' => 'Prediksi berhasil tetapi gagal disimpan: ' . $saveResult['message'],
                            'data' => $result['data']
                        ]);
                    }
                } else {
                    // API mengembalikan error
                    $errorMsg = $result['message'] ?? 'Unknown error from API';
                    log_message('error', '[PREDICTION] API Error: ' . $errorMsg);
                    
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal membuat prediksi: ' . $errorMsg
                    ]);
                }
            } else {
                // Response tidak valid
                log_message('error', '[PREDICTION] Invalid API response: ' . $response->getBody());
                
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Response tidak valid dari server prediksi'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Exception: ' . $e->getMessage());
            log_message('error', '[PREDICTION] Trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Simpan prediksi ke database
     */
    private function savePredictionToDatabase($predictionData)
    {
        try {
            // Siapkan data untuk disimpan
            // Catatan: tidak ada confidence_score karena kolom tersebut
            // tidak ada di tabel data_udara_prediksi_future
            $data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'temperature' => $predictionData['temperature']['value'] ?? null,
                'humidity' => $predictionData['humidity']['value'] ?? null,
                'pm1_0' => $predictionData['pm1_0']['value'] ?? null,
                'pm2_5' => $predictionData['pm2_5']['value'] ?? null,
                'pm10' => $predictionData['pm10']['value'] ?? null,
                'pollutant' => $predictionData['pollutant']['value'] ?? null,
                'ozone' => $predictionData['ozone']['value'] ?? null,
                'no2' => $predictionData['no2']['value'] ?? null,
                'prediction_time' => date('Y-m-d H:i:s'),
                'model_type' => 'svr',
                'days_ahead' => $predictionData['days_ahead'] ?? 7,
                'interval_minutes' => $predictionData['interval_minutes'] ?? 5,
                'prediction_start' => $predictionData['prediction_start'] ?? date('Y-m-d H:i:s'),
            ];
            
            // Simpan via model
            $id = $this->predictionModel->savePrediction($data);
            
            if ($id) {
                log_message('info', '[PREDICTION] Data saved to database with ID: ' . $id);
                return [
                    'status' => 'success',
                    'id' => $id,
                    'message' => 'Data berhasil disimpan'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data'
                ];
            }
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Save error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Cek kesehatan API Python
     */
    private function checkApiHealth()
    {
        try {
            $client = \Config\Services::curlrequest();
            $response = $client->get($this->apiUrl . ':' . $this->apiPort . '/health', [
                'timeout' => 5,
                'http_errors' => false
            ]);
            
            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody(), true);
                return isset($result['status']) && $result['status'] == 'healthy';
            }
            
            return false;
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil prediksi terbaru
     * URL: /prediction/getLatest
     * Method: GET
     */
    public function getLatest()
    {
        try {
            $data = $this->predictionModel->getLatestPrediction();
            
            if ($data) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $data
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'not_found',
                    'message' => 'Belum ada data prediksi'
                ])->setStatusCode(404);
            }
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Get latest error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Ambil history prediksi
     * URL: /prediction/getHistory
     * Method: GET
     * Parameter: limit (optional, default 30)
     */
    public function getHistory()
    {
        try {
            $limit = $this->request->getGet('limit') ?? 30;
            $page = $this->request->getGet('page') ?? 1;
            
            $data = $this->predictionModel->getPredictionHistory($limit, $page);
            $total = $this->predictionModel->getTotalCount();
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$limit,
                    'total' => (int)$total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Get history error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Ambil prediksi berdasarkan rentang tanggal
     * URL: /prediction/getByDateRange
     * Method: GET
     * Parameter: start_date, end_date
     */
    public function getByDateRange()
    {
        try {
            $startDate = $this->request->getGet('start_date');
            $endDate = $this->request->getGet('end_date');
            
            if (!$startDate || !$endDate) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Parameter start_date dan end_date diperlukan'
                ])->setStatusCode(400);
            }
            
            $data = $this->predictionModel->getPredictionByDateRange($startDate, $endDate);
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'count' => count($data)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Get by date range error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Ambil statistik prediksi
     * URL: /prediction/getStatistics
     * Method: GET
     */
    public function getStatistics()
    {
        try {
            $hours = $this->request->getGet('hours') ?? 24;
            
            $stats = $this->predictionModel->getStatistics($hours);
            $trends = $this->predictionModel->getTrendAnalysis();
            
            return $this->response->setJSON([
                'status' => 'success',
                'statistics' => $stats,
                'trends' => $trends
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Get statistics error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Export data prediksi ke CSV
     * URL: /prediction/exportCsv
     * Method: GET
     */
    public function exportCsv()
    {
        try {
            $limit = $this->request->getGet('limit') ?? 1000;
            $data = $this->predictionModel->getPredictionHistory($limit);
            
            if (empty($data)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Tidak ada data untuk diexport'
                ])->setStatusCode(404);
            }
            
            $filename = 'prediksi_kualitas_udara_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Set header untuk download
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            
            $output = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($output, [
                'ID', 'Timestamp', 'Suhu (°C)', 'Kelembaban (%)', 
                'PM1.0 (μg/m³)', 'PM2.5 (μg/m³)', 'PM10 (μg/m³)',
                'Polutan (ppm)', 'Ozon (ppm)', 'NO2 (ppm)',
                'Waktu Prediksi', 'Model', 'Days Ahead', 'Interval (menit)'
            ]);
            
            // Data
            foreach ($data as $row) {
                fputcsv($output, [
                    $row['id'],
                    $row['timestamp'],
                    $row['temperature'] ?? '',
                    $row['humidity'] ?? '',
                    $row['pm1_0'] ?? '',
                    $row['pm2_5'] ?? '',
                    $row['pm10'] ?? '',
                    $row['pollutant'] ?? '',
                    $row['ozone'] ?? '',
                    $row['no2'] ?? '',
                    $row['prediction_time'] ?? '',
                    $row['model_type'] ?? 'svr',
                    $row['days_ahead'] ?? '',
                    $row['interval_minutes'] ?? ''
                ]);
            }
            
            fclose($output);
            exit();
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Export CSV error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Auto-generate prediksi (untuk cron job)
     * URL: /prediction/autoGenerate
     * Method: GET
     * Parameter: api_key (untuk keamanan)
     */
    public function autoGenerate()
    {
        try {
            // Validasi API key (jika diperlukan)
            $apiKey = $this->request->getGet('api_key');
            $expectedKey = getenv('PREDICTION_API_KEY') ?? 'default_key_123';
            
            if ($apiKey !== $expectedKey) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Invalid API key'
                ])->setStatusCode(401);
            }
            
            // Panggil generate
            return $this->generate();
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Auto generate error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Cek status API dan database
     * URL: /prediction/status
     * Method: GET
     */
    public function status()
    {
        try {
            $apiStatus = $this->checkApiHealth();
            $dbStatus = $this->predictionModel->checkConnection();
            $totalRecords = $this->predictionModel->getTotalCount();
            $latest = $this->predictionModel->getLatestPrediction();
            
            return $this->response->setJSON([
                'status' => 'success',
                'api_python' => [
                    'status' => $apiStatus ? 'running' : 'stopped',
                    'url' => $this->apiUrl . ':' . $this->apiPort
                ],
                'database' => [
                    'status' => $dbStatus ? 'connected' : 'disconnected',
                    'total_records' => $totalRecords
                ],
                'latest_prediction' => $latest,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Status check error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get parameter untuk chart
     * URL: /prediction/chart
     * Method: GET
     */
    public function chart()
    {
        try {
            $limit = $this->request->getGet('limit') ?? 100;
            $data = $this->predictionModel->getPredictionHistory($limit);
            
            $chartData = [
                'labels' => [],
                'temperature' => [],
                'humidity' => [],
                'pm2_5' => [],
                'pm10' => [],
                'ozone' => [],
                'no2' => []
            ];
            
            // Urutkan dari yang terlama ke terbaru untuk chart
            foreach (array_reverse($data) as $row) {
                $chartData['labels'][] = date('d/m H:i', strtotime($row['timestamp']));
                $chartData['temperature'][] = (float) ($row['temperature'] ?? 0);
                $chartData['humidity'][] = (float) ($row['humidity'] ?? 0);
                $chartData['pm2_5'][] = (float) ($row['pm2_5'] ?? 0);
                $chartData['pm10'][] = (float) ($row['pm10'] ?? 0);
                $chartData['ozone'][] = (float) ($row['ozone'] ?? 0);
                $chartData['no2'][] = (float) ($row['no2'] ?? 0);
            }
            
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $chartData
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[PREDICTION] Chart error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
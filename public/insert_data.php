<?php
/**
 * ESP32 Data Receiver API
 * Endpoint untuk menerima data dari sensor ESP32
 * URL: http://192.168.100.111/insert_data.php
 */

// ============================================
// 1. KONFIGURASI HEADER (CORS & CONTENT TYPE)
// ============================================

// Mengizinkan akses dari domain manapun (termasuk ESP32)
header("Access-Control-Allow-Origin: *");

// Mengizinkan method HTTP yang digunakan
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Mengizinkan header tambahan
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Format response sebagai JSON
header("Content-Type: application/json; charset=utf-8");

// ============================================
// 2. HANDLE PREFLIGHT REQUEST (OPTIONS)
// ============================================

// Browser kadang mengirim request OPTIONS sebelum request sebenarnya
// Ini untuk CORS preflight check
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);  // Kirim response OK
    exit();  // Hentikan eksekusi
}

// ============================================
// 3. KONFIGURASI DATABASE
// ============================================

// Sesuaikan dengan konfigurasi database Anda
$db_config = [
    'host' => 'localhost',           // Server MySQL (bisa juga 127.0.0.1)
    'port' => '3306',                 // Port default MySQL
    'database' => 'asma_db',          // Nama database Anda
    'username' => 'meh',              // Username MySQL
    'password' => '123',              // Password MySQL
    'charset' => 'utf8mb4'            // Character set
];

// ============================================
// 4. FUNGSI UNTUK LOG/DEBUGGING
// ============================================

/**
 * Catat pesan ke file log
 * @param string $message Pesan yang akan dicatat
 * @param string $type Tipe log (info, error, debug)
 */
function writeLog($message, $type = 'info') {
    // Folder untuk menyimpan log
    $logDir = __DIR__ . '/../writable/logs/';
    
    // Buat folder jika belum ada
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }
    
    // Nama file log (per hari)
    $logFile = $logDir . 'esp32_' . date('Y-m-d') . '.log';
    
    // Format log: [tanggal waktu] [type] pesan
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
    
    // Tulis ke file
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// ============================================
// 5. FUNGSI VALIDASI DATA
// ============================================

/**
 * Validasi nilai temperature
 * @param float $temp Nilai temperature
 * @return bool Valid atau tidak
 */
function isValidTemperature($temp) {
    return is_numeric($temp) && $temp >= -40 && $temp <= 80;
}

/**
 * Validasi nilai humidity
 * @param float $hum Nilai kelembaban
 * @return bool Valid atau tidak
 */
function isValidHumidity($hum) {
    return is_numeric($hum) && $hum >= 0 && $hum <= 100;
}

/**
 * Validasi nilai PM (particulate matter)
 * @param int $pm Nilai PM
 * @return bool Valid atau tidak
 */
function isValidPM($pm) {
    return is_numeric($pm) && $pm >= 0 && $pm <= 1000;
}

// ============================================
// 6. AMBIL DATA DARI REQUEST
// ============================================

// Array untuk menyimpan data yang diterima
$receivedData = [];

// Cek method request (GET atau POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika POST, cek apakah data dalam format JSON
    $inputJSON = file_get_contents('php://input');
    $jsonData = json_decode($inputJSON, true);
    
    if (json_last_error() == JSON_ERROR_NONE && !empty($jsonData)) {
        // Data dalam format JSON
        $receivedData = $jsonData;
        writeLog("Data received via POST (JSON): " . json_encode($receivedData));
    } else {
        // Data dalam format form-data atau x-www-form-urlencoded
        $receivedData = $_POST;
        writeLog("Data received via POST (form): " . json_encode($receivedData));
    }
} else {
    // Method GET (biasanya digunakan ESP32 untuk testing)
    $receivedData = $_GET;
    writeLog("Data received via GET: " . json_encode($receivedData));
}

// ============================================
// 7. EKSTRAK NILAI DARI DATA YANG DITERIMA
// ============================================

// Ambil nilai dengan default jika tidak ada
$temperature = isset($receivedData['temperature']) ? floatval($receivedData['temperature']) : null;
$humidity = isset($receivedData['humidity']) ? floatval($receivedData['humidity']) : null;
$pm1_0 = isset($receivedData['pm1_0']) ? intval($receivedData['pm1_0']) : 0;
$pm2_5 = isset($receivedData['pm2_5']) ? intval($receivedData['pm2_5']) : 0;
$pm10 = isset($receivedData['pm10']) ? intval($receivedData['pm10']) : 0;
$pollutant = isset($receivedData['pollutant']) ? floatval($receivedData['pollutant']) : 0;

// Tambahkan IP address pengirim untuk logging
$senderIP = $_SERVER['REMOTE_ADDR'];
writeLog("Data from IP: {$senderIP}");

// ============================================
// 8. VALIDASI DATA WAJIB
// ============================================

// Cek apakah temperature dan humidity ada
if ($temperature === null || $humidity === null) {
    $errorMsg = "Missing required parameters: temperature and humidity are required";
    writeLog($errorMsg, 'error');
    
    // Kirim response error
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'received_data' => $receivedData,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;  // Hentikan eksekusi
}

// Validasi range nilai
if (!isValidTemperature($temperature)) {
    $errorMsg = "Invalid temperature value: {$temperature}. Must be between -40 and 80";
    writeLog($errorMsg, 'error');
    
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'received_data' => $receivedData
    ]);
    exit;
}

if (!isValidHumidity($humidity)) {
    $errorMsg = "Invalid humidity value: {$humidity}. Must be between 0 and 100";
    writeLog($errorMsg, 'error');
    
    echo json_encode([
        'success' => false,
        'message' => $errorMsg,
        'received_data' => $receivedData
    ]);
    exit;
}

// ============================================
// 9. KONEKSI KE DATABASE
// ============================================

try {
    // Buat koneksi PDO ke MySQL
    // Format: mysql:host=localhost;port=3306;dbname=asma_db;charset=utf8mb4
    $dsn = "mysql:host={$db_config['host']};port={$db_config['port']};dbname={$db_config['database']};charset={$db_config['charset']}";
    
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);
    
    // Set mode error: exception (akan throw error jika ada masalah)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set fetch mode default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    writeLog("Database connected successfully");
    
} catch (PDOException $e) {
    // Gagal koneksi ke database
    $errorMsg = "Database connection failed: " . $e->getMessage();
    writeLog($errorMsg, 'error');
    
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error',
        'error_detail' => $e->getMessage()
    ]);
    exit;
}

// ============================================
// 10. INSERT DATA KE DATABASE
// ============================================

try {
    // SQL query untuk insert data
    // Gunakan prepared statement untuk keamanan (mencegah SQL injection)
    $sql = "INSERT INTO data_udara 
            (temperature, humidity, pm1_0, pm2_5, pm10, pollutant, timestamp) 
            VALUES 
            (:temperature, :humidity, :pm1_0, :pm2_5, :pm10, :pollutant, NOW())";
    
    // Persiapkan statement
    $stmt = $pdo->prepare($sql);
    
    // Eksekusi dengan parameter
    $result = $stmt->execute([
        ':temperature' => $temperature,
        ':humidity' => $humidity,
        ':pm1_0' => $pm1_0,
        ':pm2_5' => $pm2_5,
        ':pm10' => $pm10,
        ':pollutant' => $pollutant
    ]);
    
    // Ambil ID yang baru diinsert
    $insertId = $pdo->lastInsertId();
    
    // Catat keberhasilan ke log
    writeLog("Data inserted successfully - ID: {$insertId}, Temp: {$temperature}, Humidity: {$humidity}");
    
    // ============================================
    // 11. KIRIM RESPONSE SUKSES
    // ============================================
    
    // Siapkan data response
    $responseData = [
        'temperature' => $temperature,
        'humidity' => $humidity,
        'pm1_0' => $pm1_0,
        'pm2_5' => $pm2_5,
        'pm10' => $pm10,
        'pollutant' => $pollutant
    ];
    
    // Kirim response JSON
    echo json_encode([
        'success' => true,
        'message' => 'Data inserted successfully',
        'id' => $insertId,
        'data' => $responseData,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (PDOException $e) {
    // Error saat eksekusi query
    $errorMsg = "Insert failed: " . $e->getMessage();
    writeLog($errorMsg, 'error');
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to insert data into database',
        'error_detail' => $e->getMessage()
    ]);
}
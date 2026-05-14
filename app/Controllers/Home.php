<?php

namespace App\Controllers;

use App\Models\DataUdaraModel;

class Home extends BaseController
{
    protected $dataUdaraModel;

    public function __construct()
    {
        $this->dataUdaraModel = new DataUdaraModel();
    }

    public function index()
    {
        helper('air');

        date_default_timezone_set('Asia/Jakarta');

        $db = \Config\Database::connect();

        // ==================================================
        // HERO REALTIME (DATA TERBARU)
        // ==================================================
        $latestRaw = $this->dataUdaraModel
            ->orderBy('id', 'DESC')
            ->first();

        $latestUdara = [];

        if ($latestRaw) {

            $pm25 = (float) ($latestRaw['pm2_5'] ?? 0);

            $aqi = $this->hitungAqi($pm25);

            $latestUdara = [
                'aqi'         => $aqi,
                'aqhi' => calcAqhi(
                    (float) ($latestRaw['no2'] ?? 0),
                    (float) ($latestRaw['ozone'] ?? 0),
                    (float) ($latestRaw['pm2_5'] ?? 0)
                ),
                'pm25'        => (float) ($latestRaw['pm2_5'] ?? 0),
                'pm10'        => (float) ($latestRaw['pm10'] ?? 0),
                'pm1'         => (float) ($latestRaw['pm1_0'] ?? 0),
                'polutan'     => (float) ($latestRaw['pollutant'] ?? 0),
                'o3'          => (float) ($latestRaw['ozone'] ?? 0),
                'no2'         => (float) ($latestRaw['no2'] ?? 0),
                'suhu'        => (float) ($latestRaw['temperature'] ?? 0),
                'kelembaban'  => (float) ($latestRaw['humidity'] ?? 0),
                'location'    => 'Bojongsoang',
                'timestamp'   => $latestRaw['timestamp'] ?? null,
            ];
        }

        // ==================================================
        // HOURLY HISTORY (RATA-RATA PER JAM)
        // ==================================================
        $hourlyQuery = $db->query("
            SELECT 
                HOUR(timestamp) as jam,
                AVG(pm2_5) as pm25,
                AVG(pm10) as pm10,
                AVG(pm1_0) as pm1,
                AVG(pollutant) as polutan,
                AVG(ozone) as o3,
                AVG(no2) as no2,
                AVG(temperature) as temp,
                AVG(humidity) as humidity
            FROM data_udara
            WHERE DATE(timestamp) = CURDATE()
            GROUP BY HOUR(timestamp)
            ORDER BY jam ASC
        ");

        $historyHourly = [];

        foreach ($hourlyQuery->getResultArray() as $row) {

            $aqi = $this->hitungAqi((float)$row['pm25']);

            $historyHourly[] = [
                'date'       => date('d M Y'),
                'time'       => sprintf('%02d:00', $row['jam']),
                'aqhi' => calcAqhi(
                    (float) $row['no2'],
                    (float) $row['o3'],
                    (float) $row['pm25']
                ),
                'aqi'        => $aqi,
                'pm25'       => round($row['pm25'], 1),
                'pm10'       => round($row['pm10'], 1),
                'pm1'        => round($row['pm1'], 1),
                'polutan'    => round($row['polutan'], 1),
                'o3'         => round($row['o3'], 1),
                'no2'        => round($row['no2'], 1),
                'temp'       => round($row['temp'], 1),
                'humidity'   => round($row['humidity'], 1),
                'location'   => 'Bojongsoang',
            ];
        }

        // ==================================================
        // DAILY HISTORY (RATA-RATA PER HARI)
        // ==================================================
        $dailyQuery = $db->query("
            SELECT 
                DATE(timestamp) as tanggal,
                AVG(pm2_5) as pm25,
                AVG(pm10) as pm10,
                AVG(pm1_0) as pm1,
                AVG(pollutant) as polutan,
                AVG(ozone) as o3,
                AVG(no2) as no2,
                AVG(temperature) as temp,
                AVG(humidity) as humidity
            FROM data_udara
            GROUP BY DATE(timestamp)
            ORDER BY tanggal DESC
            LIMIT 7
        ");

        $historyDaily = [];

        $counter = 0;

        foreach ($dailyQuery->getResultArray() as $row) {

            $aqi = $this->hitungAqi((float)$row['pm25']);

            $historyDaily[] = [
                'date'       => date('d M Y', strtotime($row['tanggal'])),
                'is_today'   => $counter === 0,
                'aqhi' => calcAqhi(
                    (float) $row['no2'],
                    (float) $row['o3'],
                    (float) $row['pm25']
                ),
                'aqi'        => $aqi,
                'pm25'       => round($row['pm25'], 1),
                'pm10'       => round($row['pm10'], 1),
                'pm1'        => round($row['pm1'], 1),
                'polutan'    => round($row['polutan'], 1),
                'o3'         => round($row['o3'], 1),
                'no2'        => round($row['no2'], 1),
                'temp'       => round($row['temp'], 1),
                'humidity'   => round($row['humidity'], 1),
                'location'   => 'Bojongsoang',
            ];

            $counter++;
        }

        // ==================================================
        // MONTHLY HISTORY (RATA-RATA PER BULAN)
        // ==================================================
        $monthlyQuery = $db->query("
            SELECT 
                DATE_FORMAT(timestamp, '%Y-%m') as bulan,
                AVG(pm2_5) as pm25,
                AVG(pm10) as pm10,
                AVG(pm1_0) as pm1,
                AVG(pollutant) as polutan,
                AVG(ozone) as o3,
                AVG(no2) as no2,
                AVG(temperature) as temp,
                AVG(humidity) as humidity
            FROM data_udara
            GROUP BY DATE_FORMAT(timestamp, '%Y-%m')
            ORDER BY bulan ASC
        ");

        $historyMonthly = [];

        foreach ($monthlyQuery->getResultArray() as $row) {

            $aqi = $this->hitungAqi((float)$row['pm25']);

            $historyMonthly[] = [
                'month'      => date('M Y', strtotime($row['bulan'] . '-01')),
                'aqhi' => calcAqhi(
                    (float) $row['no2'],
                    (float) $row['o3'],
                    (float) $row['pm25']
                ),
                'aqi'        => $aqi,
                'pm25'       => round($row['pm25'], 1),
                'pm10'       => round($row['pm10'], 1),
                'pm1'        => round($row['pm1'], 1),
                'polutan'    => round($row['polutan'], 1),
                'o3'         => round($row['o3'], 1),
                'no2'        => round($row['no2'], 1),
                'temp'       => round($row['temp'], 1),
                'humidity'   => round($row['humidity'], 1),
                'location'   => 'Bojongsoang',
            ];
        }

        // ==================================================
        // SEND TO VIEW
        // ==================================================
        $data = [
            'title'           => 'Dashboard',
            'latestUdara'     => $latestUdara,
            'historyHourly'   => $historyHourly,
            'historyDaily'    => $historyDaily,
            'historyMonthly'  => $historyMonthly,
            'forecastHourly'  => [],
        ];

        return view('dashboard', $data);
    }

    // ==================================================
    // INSERT DATA DARI ESP32
    // ==================================================
    public function insert()
    {
        $data = [
            'temperature' => $this->request->getPost('temperature'),
            'humidity'    => $this->request->getPost('humidity'),
            'pm1_0'       => $this->request->getPost('pm1_0'),
            'pm2_5'       => $this->request->getPost('pm2_5'),
            'pm10'        => $this->request->getPost('pm10'),
            'pollutant'   => $this->request->getPost('pollutant'),
            'ozone'       => $this->request->getPost('ozone'),
            'no2'         => $this->request->getPost('no2'),
        ];

        $this->dataUdaraModel->insert($data);

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    // ==================================================
    // HITUNG AQI DARI PM2.5
    // ==================================================
    private function hitungAqi(float $pm25): int
    {
        $breakpoints = [
            ['cLow'=>0.0,'cHigh'=>12.0,'iLow'=>0,'iHigh'=>50],
            ['cLow'=>12.1,'cHigh'=>35.4,'iLow'=>51,'iHigh'=>100],
            ['cLow'=>35.5,'cHigh'=>55.4,'iLow'=>101,'iHigh'=>150],
            ['cLow'=>55.5,'cHigh'=>150.4,'iLow'=>151,'iHigh'=>200],
            ['cLow'=>150.5,'cHigh'=>250.4,'iLow'=>201,'iHigh'=>300],
            ['cLow'=>250.5,'cHigh'=>500.4,'iLow'=>301,'iHigh'=>500],
        ];

        foreach ($breakpoints as $bp) {

            if ($pm25 >= $bp['cLow'] && $pm25 <= $bp['cHigh']) {

                $aqi = (
                    ($bp['iHigh'] - $bp['iLow']) /
                    ($bp['cHigh'] - $bp['cLow'])
                ) * ($pm25 - $bp['cLow']) + $bp['iLow'];

                return round($aqi);
            }
        }

        return 0;
    }
}
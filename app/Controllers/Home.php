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
        helper('aqi');

        // Fix timezone WIB
        date_default_timezone_set('Asia/Jakarta');

        // =====================
        // DATA TERBARU (untuk dashboard utama)
        // =====================
        $allData     = $this->dataUdaraModel->orderBy('id', 'DESC')->findAll();
        $latestRaw   = $this->dataUdaraModel->orderBy('id', 'DESC')->first();

        // Hitung AQI dari pm25 jika kolom aqi belum ada
        $latestUdara = null;
        if ($latestRaw) {
            $pm25 = (float) ($latestRaw['pm25'] ?? 0);
            $pm10 = (float) ($latestRaw['pm10'] ?? 0);
            $polutan  = 120; // sesuaikan jika ada kolom gas/polutan di database

            // Hitung AQI sederhana berbasis PM2.5
            $aqi = $this->hitungAqi($pm25);

            $latestUdara = [
                'pm25'       => $pm25,
                'pm10'       => $pm10,
                'polutan'        => $polutan,
                'suhu'       => (float) ($latestRaw['suhu']       ?? 0),
                'kelembaban' => (float) ($latestRaw['kelembaban'] ?? 0),
                'aqi'        => $aqi,
            ];
        }

        // =====================
        // HOURLY (24 jam simulasi / bisa diganti query real)
        // =====================
        $historyHourly = [];

        for ($i = 0; $i < 24; $i++) {
            $timestamp = strtotime("-$i hours");
            $historyHourly[] = [
                'time'     => date('H:00', $timestamp),
                'aqi'      => rand(20, 250),
                'pm25'     => rand(10, 50),
                'pm10'     => rand(20, 80),
                'gas'      => rand(50, 300),
                'temp'     => rand(24, 34),
                'humidity' => rand(55, 85),
            ];
        }

        // Balik agar urutan dari jam terlama ke terbaru
        $historyHourly = array_reverse($historyHourly);

        // =====================
        // DAILY (7 hari)
        // =====================
        $historyDaily = [];

        $hariIndo = [
            'Sun' => 'Minggu', 'Mon' => 'Senin',  'Tue' => 'Selasa',
            'Wed' => 'Rabu',   'Thu' => 'Kamis',   'Fri' => 'Jumat',
            'Sat' => 'Sabtu',
        ];

        for ($d = 0; $d < 7; $d++) {
            $timestamp  = strtotime("-$d days");
            $hari       = date('D', $timestamp);
            $labelHari  = $d === 0 ? 'Hari ini' : $hariIndo[$hari];

            // Rata-rata AQI harian
            $aqiPerJam = [];
            for ($h = 0; $h < 24; $h++) {
                $aqiPerJam[] = rand(20, 250);
            }
            $dailyAqi = round(array_sum($aqiPerJam) / count($aqiPerJam));

            $historyDaily[] = [
                'date'     => $labelHari,
                'aqi'      => $dailyAqi,
                'pm25'     => rand(10, 50),
                'pm10'     => rand(20, 80),
                'polutan'      => rand(50, 300),
                'temp'     => rand(26, 32),
                'humidity' => rand(65, 90),
                'is_today' => $d === 0,
            ];
        }

        $data = [
            'title'         => 'Dashboard',
            'latestUdara'   => $latestUdara,
            'historyHourly' => $historyHourly,
            'historyDaily'  => $historyDaily,
        ];

        return view('dashboard', $data);
    }

    public function insert()
    {
        $data = [
            'pm25'       => $this->request->getPost('pm25'),
            'pm10'       => $this->request->getPost('pm10'),
            'suhu'       => $this->request->getPost('suhu'),
            'kelembaban' => $this->request->getPost('kelembaban'),
        ];

        $this->dataUdaraModel->insert($data);

        return $this->response->setJSON(['status' => 'success']);
    }

    // =====================
    // HELPER: Hitung AQI dari PM2.5
    // =====================
    private function hitungAqi(float $pm25): int
    {
        // Breakpoint standar US EPA untuk PM2.5
        $breakpoints = [
            ['cLow' =>   0.0, 'cHigh' =>  12.0, 'iLow' =>  0, 'iHigh' =>  50],
            ['cLow' =>  12.1, 'cHigh' =>  35.4, 'iLow' => 51, 'iHigh' => 100],
            ['cLow' =>  35.5, 'cHigh' =>  55.4, 'iLow' => 101,'iHigh' => 150],
            ['cLow' =>  55.5, 'cHigh' => 150.4, 'iLow' => 151,'iHigh' => 200],
            ['cLow' => 150.5, 'cHigh' => 250.4, 'iLow' => 201,'iHigh' => 300],
            ['cLow' => 250.5, 'cHigh' => 500.4, 'iLow' => 301,'iHigh' => 500],
        ];

        foreach ($breakpoints as $bp) {
            if ($pm25 >= $bp['cLow'] && $pm25 <= $bp['cHigh']) {
                $aqi = (($bp['iHigh'] - $bp['iLow']) / ($bp['cHigh'] - $bp['cLow']))
                     * ($pm25 - $bp['cLow'])
                     + $bp['iLow'];
                return (int) round($aqi);
            }
        }

        return $pm25 > 500 ? 500 : 0;
    }
}
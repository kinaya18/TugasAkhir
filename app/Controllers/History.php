<?php

namespace App\Controllers;

class History extends BaseController
{
    public function index()
    {
        helper('aqi');

        // 🔥 FIX TIMEZONE WIB
        date_default_timezone_set('Asia/Jakarta');

        // =====================
        // HOURLY (24 JAM DARI SEKARANG)
        // =====================
            $historyHourly = [];

            for ($i = 0; $i < 24; $i++) {

                $timestamp = strtotime("+$i hours");

                $historyHourly[] = [
                    'time' => date('H:00', $timestamp),
                    'aqi' => rand(20, 250),
                    'pm25' => rand(10, 50),
                    'pm10' => rand(20, 80),
                    'gas' => rand(50, 300),
                    'temp' => rand(10, 38),
                    'humidity' => rand(25, 80),
                ];
            }

        // =====================
        // DAILY (7 HARI)
        // =====================
        $historyDaily = [];

        $hariIndo = [
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu',
        ];

        for ($d = 0; $d < 7; $d++) {

            $timestamp = strtotime("-$d days");
            $hari = date('D', $timestamp);

            // label hari
            $labelHari = $d == 0 ? 'Hari ini' : $hariIndo[$hari];

            // =====================
            // HITUNG AQI HARIAN (AVERAGE 24 JAM)
            // =====================
            $aqiPerJam = [];

            for ($h = 0; $h < 24; $h++) {
                $aqiPerJam[] = rand(20, 250); // nanti dari database
            }

            $dailyAqi = round(array_sum($aqiPerJam) / count($aqiPerJam));

            // =====================
            // DATA FINAL
            // =====================
            $historyDaily[] = [
                'date' => $labelHari,
                'aqi' => $dailyAqi,
                'pm25' => rand(10, 50),
                'temp' => rand(26, 30),
                'pm10' => rand(20, 80),
                'nox' => rand(50, 300),
                'humidity' => rand(70, 90),
                'is_today' => $d == 0
            ];
        }

        return view('RiwayatData/riwayat-data', [
            'historyHourly' => $historyHourly,
            'historyDaily' => $historyDaily
        ]);
    }
}
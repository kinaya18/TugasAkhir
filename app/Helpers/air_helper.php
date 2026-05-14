<?php

// ======================================================
// AQI CLASS
// ======================================================
function getAqiClass($aqi)
{
    if ($aqi <= 50)  return 'aqi-good';
    if ($aqi <= 100) return 'aqi-moderate';
    if ($aqi <= 150) return 'aqi-unhealthy';
    if ($aqi <= 200) return 'aqi-very';
    return 'aqi-hazard';
}

// ======================================================
// AQI LABEL
// ======================================================
function getAqiLabel($aqi)
{
    if ($aqi <= 50)  return 'Baik';
    if ($aqi <= 100) return 'Sedang';
    if ($aqi <= 150) return 'Tidak sehat (sensitif)';
    if ($aqi <= 200) return 'Tidak sehat';
    if ($aqi <= 300) return 'Sangat tidak sehat';
    return 'Berbahaya';
}

// ======================================================
// AQI COLOR
// ======================================================
function getAqiColor($aqi)
{
    if ($aqi <= 50)  return '#22c55e';
    if ($aqi <= 100) return '#f59e0b';
    if ($aqi <= 150) return '#f97316';
    if ($aqi <= 200) return '#ef4444';
    if ($aqi <= 300) return '#a855f7';
    return '#7f1d1d';
}

// ======================================================
// DAILY PILL CLASS
// ======================================================
function getDailyPillClass($aqi)
{
    if ($aqi <= 50)  return 'pill-good';
    if ($aqi <= 100) return 'pill-moderate';
    if ($aqi <= 150) return 'pill-sensitive';
    if ($aqi <= 200) return 'pill-unhealthy';
    if ($aqi <= 300) return 'pill-very';
    return 'pill-hazard';
}

// ======================================================
// HITUNG AQI DARI PM2.5
// ======================================================
function calcAqi($pm25)
{
    $pm25 = (float)$pm25;

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

// ======================================================
// HITUNG AQHI
// ======================================================
function calcAqhi($no2_ppb, $o3_ppb, $pm25)
{
    $no2  = (float)$no2_ppb;
    $o3   = (float)$o3_ppb;
    $pm25 = (float)$pm25;

    $risk =
        (exp(0.000537 * $no2)  - 1) +
        (exp(0.000871 * $o3)   - 1) +
        (exp(0.000487 * $pm25) - 1);

    $aqhi = round(($risk / 10.4) * 10 + 1);

    return min(max((int)$aqhi, 1), 11);
}

// ======================================================
// AQHI LABEL
// ======================================================
function getAqhiLabel($aqhi)
{
    $v = (int)$aqhi;

    if ($v <= 3)  return 'Low';
    if ($v <= 6)  return 'Moderate';
    if ($v <= 10) return 'High';

    return 'Very High';
}

// ======================================================
// AQHI COLOR
// ======================================================
function getAqhiColor($aqhi)
{
    $v = (int)$aqhi;

    if ($v <= 3)  return '#22c55e';
    if ($v <= 6)  return '#f59e0b';
    if ($v <= 10) return '#ef4444';

    return '#7f1d1d';
}

// ======================================================
// AQHI PILL CLASS
// ======================================================
function getAqhiPillClass($aqhi)
{
    $v = (int)$aqhi;

    if ($v <= 3)  return 'pill-good';
    if ($v <= 6)  return 'pill-moderate';
    if ($v <= 10) return 'pill-unhealthy';

    return 'pill-hazard';
}
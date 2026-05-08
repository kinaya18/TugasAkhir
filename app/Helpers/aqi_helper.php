<?php

function getAqiClass($aqi) {
    if ($aqi <= 50) return 'aqi-good';
    if ($aqi <= 100) return 'aqi-moderate';
    if ($aqi <= 150) return 'aqi-unhealthy';
    if ($aqi <= 200) return 'aqi-very';
    return 'aqi-hazard';
}

function getAqiLabel($aqi) {
    if ($aqi <= 50) return 'Baik';
    if ($aqi <= 100) return 'Sedang';
    if ($aqi <= 150) return 'Tidak sehat (sensitif)';
    if ($aqi <= 200) return 'Tidak sehat';
    return 'Berbahaya';
}

function getDailyPillClass($aqi) {
    if ($aqi <= 50)  return 'pill-good';
    if ($aqi <= 100) return 'pill-moderate';
    if ($aqi <= 150) return 'pill-sensitive';
    if ($aqi <= 200) return 'pill-unhealthy';
    if ($aqi <= 300) return 'pill-very';
    return 'pill-hazard';
}

function calcAqhi($no2_ppb, $o3_ppb, $pm25) {
    $no2  = (float)$no2_ppb * 1.88;
    $o3   = (float)$o3_ppb  * 1.96;
    $pm25 = (float)$pm25;
    $risk = (exp(0.000537 * $no2)  - 1)
          + (exp(0.000871 * $o3)   - 1)
          + (exp(0.000487 * $pm25) - 1);
    $aqhi = round(($risk / 10.4) * 10 + 1);
    return min(max((int)$aqhi, 1), 11);
}

function getAqhiLabel($aqhi) {
    $v = (int)$aqhi;
    if ($v <= 3)  return 'Low';
    if ($v <= 6)  return 'Moderate';
    if ($v <= 10) return 'High';
    return 'Very High';
}

function getAqhiPillClass($aqhi) {
    $v = (int)$aqhi;
    if ($v <= 3)  return 'pill-good';
    if ($v <= 6)  return 'pill-moderate';
    if ($v <= 10) return 'pill-unhealthy';
    return 'pill-hazard';
}


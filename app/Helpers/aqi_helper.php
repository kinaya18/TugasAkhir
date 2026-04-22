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
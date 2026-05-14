<?php

$aqhi = $latestUdara['aqhi'] ?? 1;

$aqhiClass = 'low';

if ($aqhi > 3 && $aqhi <= 6) {
    $aqhiClass = 'moderate';
}
elseif ($aqhi > 6 && $aqhi <= 10) {
    $aqhiClass = 'high';
}
elseif ($aqhi > 10) {
    $aqhiClass = 'very-high';
}
?>

<?= $this->extend('layout/main_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

<!-- Data PHP di-inject sekali di sini, dipakai semua section -->
<script>
    window.DASH = {
        hourlyRaw     : <?= json_encode($historyHourly) ?>,
        dailyRaw      : <?= json_encode($historyDaily) ?>,
        latestData    : <?= json_encode(array_merge($latestUdara ?? [], ['location' => 'Bojongsoang'])) ?>,
        monthlyRaw    : <?= isset($historyMonthly)  ? json_encode($historyMonthly)  : '[]' ?>,
        forecastHourly: <?= isset($forecastHourly)  ? json_encode($forecastHourly)  : '[]' ?>,
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<?= $this->include('dashboard/sections/section_hero') ?>
<?= $this->include('dashboard/sections/section_history') ?>
<?= $this->include('dashboard/sections/section_aqi_guide') ?>
<?= $this->include('dashboard/sections/partial_gauge_popup') ?>

<?= $this->endSection() ?>
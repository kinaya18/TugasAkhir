<?= $this->extend('layout/main_layout') ?>

<?= $this->section('content') ?>

<!-- ================= CSS DASHBOARD ================= -->
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/base.css?v=99') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/section_hero.css?v=99') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/section_history.css?v=99') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/section_aqi_guide.css?v=99') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/responsive.css?v=99') ?>">

<!-- ================= DATA GLOBAL ================= -->
<script>
window.DASH = {
    hourlyRaw: <?= json_encode($historyHourly ?? []) ?>,
    dailyRaw: <?= json_encode($historyDaily ?? []) ?>,
    latestData: <?= json_encode(
        array_merge(
            $latestUdara ?? [],
            ['location' => 'Bojongsoang']
        )
    ) ?>,
    monthlyRaw: <?= json_encode($historyMonthly ?? []) ?>,
    forecastHourly: <?= json_encode($forecastHourly ?? []) ?>
};
</script>

<!-- ================= LIBRARY ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- ================= SECTION ================= -->
<?= $this->include('dashboard/sections/section_hero') ?>

<?= $this->include('dashboard/sections/section_history') ?>

<?= $this->include('dashboard/sections/section_aqi_guide') ?>

<?= $this->include('dashboard/sections/partial_gauge_popup') ?>

<?= $this->endSection() ?>
// riwayat
<?= $this->extend('layout/main_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/riwayat.css') ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

<div class="history-wrapper">

    <!-- DAILY -->
    <div class="history-card-box daily-card-box">
        <h3>Riwayat Harian</h3>

        <?php foreach ($historyDaily as $item): ?>
            <!-- DESKTOP ROW -->
            <div class="daily-row <?= $item['is_today'] ? 'today-row' : '' ?>">
                <div class="col-day"><?= $item['date'] ?></div>
                <div class="col-aqi">
                    <div class="aqi-box <?= getAqiClass($item['aqi']) ?>"><?= $item['aqi'] ?></div>
                </div>
                <div class="col-pm"><small>PM2.5</small><span><?= $item['pm25'] ?> µg/m³</span></div>
                <div class="col-pm"><small>PM1.0</small><span><?= $item['pm10'] ?> µg/m³</span></div>
                <div class="col-pm"><small>NOX/VOC</small><span><?= $item['nox'] ?> ppm</span></div>
                <div class="col-climate"><i class="fa-solid fa-temperature-half"></i> <?= $item['temp'] ?>°</div>
                <div class="col-climate"><i class="fa-solid fa-droplet"></i> <?= $item['humidity'] ?>%</div>
                <div class="col-status"><?= getAqiLabel($item['aqi']) ?></div>
            </div>

            <!-- MOBILE CARD -->
            <div class="daily-mobile-card <?= $item['is_today'] ? 'today-row' : '' ?>">
                <div class="mobile-card-top">
                    <div class="col-day"><?= $item['date'] ?></div>
                    <div class="mobile-right-top">
                        <div class="aqi-box <?= getAqiClass($item['aqi']) ?>"><?= $item['aqi'] ?></div>
                        <div class="col-status"><?= getAqiLabel($item['aqi']) ?></div>
                    </div>
                </div>
                <div class="mobile-card-stats">
                    <div class="col-pm"><small>PM2.5</small><span><?= $item['pm25'] ?> µg/m³</span></div>
                    <div class="col-pm"><small>PM10</small><span><?= $item['pm10'] ?> µg/m³</span></div>
                    <div class="col-pm"><small>NOX/VOC</small><span><?= $item['nox'] ?> ppm</span></div>
                    <div class="col-climate"><i class="fa-solid fa-temperature-half"></i> <?= $item['temp'] ?>°</div>
                    <div class="col-climate"><i class="fa-solid fa-droplet"></i> <?= $item['humidity'] ?>%</div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- CHART -->
    <div class="history-card-box">
        <h3>History</h3>

        <div class="chart-grid">

            <!-- HOURLY CHART -->
            <div class="chart-box">
                <div class="chart-filter-bar">
                    <h4>Hourly</h4>
                    <div class="chart-controls">
                        <select id="hourlyChartMetric" class="chart-metric-select" onchange="renderCharts()">
                            <option value="aqi">AQI</option>
                            <option value="pm25">PM2.5</option>
                            <option value="pm10">PM1.0</option>
                            <option value="gas">NOx / VOC</option>
                            <option value="temp">Suhu</option>
                            <option value="humidity">Kelembapan</option>
                        </select>
                        <div class="chart-toggle">
                            <button onclick="setChartType('bar','hourly')" id="hourlyBar">
                                <i class="fa-solid fa-chart-column"></i>
                            </button>
                            <button onclick="setChartType('line','hourly')" id="hourlyLine">
                                <i class="fa-solid fa-chart-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="chart-highlight">
                    <div>
                        <div id="hourlyTime">-</div>
                        <small id="hourlyMetricLabel">AQI</small>
                    </div>
                    <div id="hourlyDesc">-</div>
                </div>

                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>

            <!-- DAILY CHART -->
            <div class="chart-box">
                <div class="chart-filter-bar">
                    <h4>Daily</h4>
                    <div class="chart-controls">
                        <select id="dailyChartMetric" class="chart-metric-select" onchange="renderCharts()">
                            <option value="aqi">AQI</option>
                            <option value="pm25">PM2.5</option>
                            <option value="pm10">PM1.0</option>
                            <option value="gas">NOx / VOC</option>
                            <option value="temp">Suhu</option>
                            <option value="humidity">Kelembapan</option>
                        </select>
                        <div class="chart-toggle">
                            <button onclick="setChartType('bar','daily')" id="dailyBar">
                                <i class="fa-solid fa-chart-column"></i>
                            </button>
                            <button onclick="setChartType('line','daily')" id="dailyLine">
                                <i class="fa-solid fa-chart-line"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="chart-highlight">
                    <div>
                        <div id="dailyTime">-</div>
                        <small id="dailyMetricLabel">AQI</small>
                    </div>
                    <div id="dailyDesc">-</div>
                </div>

                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ── Raw data dari PHP ──────────────────────────────────────────────────────────
const hourlyRaw = <?= json_encode($historyHourly) ?>;
const dailyRaw  = <?= json_encode($historyDaily) ?>;

// ── Color helpers ─────────────────────────────────────────────────────────────
function getAqiColor(v) {
    if (v <= 50)  return "#76a13c";
    if (v <= 100) return "#d9b11c";
    if (v <= 150) return "#d9732e";
    if (v <= 200) return "#c93d3d";
    if (v <= 300) return "#7e4f94";
    return "#4d1d2b";
}

function getPm25Color(v) {
    if (v <= 12)  return "#76a13c";
    if (v <= 35)  return "#d9b11c";
    if (v <= 55)  return "#d9732e";
    if (v <= 150) return "#c93d3d";
    if (v <= 250) return "#7e4f94";
    return "#4d1d2b";
}

function getPm10Color(v) {
    if (v <= 54)  return "#76a13c";
    if (v <= 154) return "#d9b11c";
    if (v <= 254) return "#d9732e";
    if (v <= 354) return "#c93d3d";
    if (v <= 424) return "#7e4f94";
    return "#4d1d2b";
}

function getGasColor(v) {
    if (v <= 50) return "#76a13c";
    if (v <= 100) return "#d9b11c";
    if (v <= 199) return "#d9732e";
    if (v <= 299) return "#c93d3d";
    return "#7e4f94";
}

function getTempColor(v) {
    if (v <= 15) return "#2980b9";
    if (v <= 22) return "#76a13c";
    if (v <= 28) return "#d9b11c";
    if (v <= 35) return "#d9732e";
    return "#c93d3d";
}

function getHumidityColor(v) {
    if (v <= 25) return "#c93d3d";
    if (v <= 39) return "#d9732e";
    if (v <= 60) return "#76a13c";
    if (v <= 75) return "#d9b11c";
    return "#7e4f94";
}

function getAqiDesc(aqi) {
    if (aqi <= 50)  return "Baik";
    if (aqi <= 100) return "Sedang";
    if (aqi <= 150) return "Tidak sehat bagi kelompok sensitif";
    if (aqi <= 200) return "Tidak sehat";
    return "Berbahaya";
}

// ── Konfigurasi metrik ────────────────────────────────────────────────────────
const METRICS = {
    aqi:      { key: 'aqi',      label: 'AQI',        unit: '',        colorFn: getAqiColor      },
    pm25:     { key: 'pm25',     label: 'PM2.5',       unit: ' µg/m³',  colorFn: getPm25Color     },
    pm10:     { key: 'pm10',     label: 'PM1.0',       unit: ' µg/m³',  colorFn: getPm10Color     },
    gas:      { key: 'gas',      label: 'NOx / VOC',   unit: ' ppm',    colorFn: getGasColor      },
    temp:     { key: 'temp',     label: 'Suhu',        unit: ' °C',     colorFn: getTempColor     },
    humidity: { key: 'humidity', label: 'Kelembapan',  unit: ' %',      colorFn: getHumidityColor },
};

// ── Chart state ───────────────────────────────────────────────────────────────
let hourlyType  = 'bar';
let dailyType   = 'bar';
let hourlyChart = null;
let dailyChart  = null;

// ── Extract data ──────────────────────────────────────────────────────────────
function extractData(rawArr, metricKey) {
    return rawArr.map(item => {
        if (metricKey === 'gas') {
            return parseFloat(item['gas'] !== undefined ? item['gas'] : item['nox']) || 0;
        }
        return parseFloat(item[METRICS[metricKey].key]) || 0;
    });
}

function extractLabels(rawArr, isHourly) {
    return rawArr.map(item => isHourly ? item['time'] : item['date']);
}

// ── Update highlight info ─────────────────────────────────────────────────────
function updateInfo(target, label, value, metricKey) {
    const m = METRICS[metricKey];
    document.getElementById(target + 'Time').innerText       = label;
    document.getElementById(target + 'MetricLabel').innerText = m.label;
    document.getElementById(target + 'Desc').innerText =
        metricKey === 'aqi'
            ? getAqiDesc(value) + ' (' + value + ')'
            : value + m.unit;
}

// ── Render charts ─────────────────────────────────────────────────────────────
function renderCharts() {
    const hourlyMetric = document.getElementById('hourlyChartMetric').value;
    const dailyMetric  = document.getElementById('dailyChartMetric').value;

    const hLabels = extractLabels(hourlyRaw, true);
    const hData   = extractData(hourlyRaw, hourlyMetric);
    const dLabels = extractLabels(dailyRaw, false);
    const dData   = extractData(dailyRaw, dailyMetric);

    const hMeta = METRICS[hourlyMetric];
    const dMeta = METRICS[dailyMetric];

    if (hourlyChart) hourlyChart.destroy();
    if (dailyChart)  dailyChart.destroy();

    hourlyChart = new Chart(document.getElementById('hourlyChart'), {
        type: hourlyType,
        data: {
            labels: hLabels,
            datasets: [{
                data: hData,
                backgroundColor: hData.map(hMeta.colorFn),
                borderColor: hData.map(hMeta.colorFn),
                fill: false,
                tension: 0.4,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + hMeta.unit
                    }
                }
            },
            scales: {
                x: { ticks: { callback: (v, i) => i % 4 === 0 ? hLabels[i] : '' } },
                y: { beginAtZero: true }
            },
            onClick: (evt, el) => {
                if (el.length > 0) updateInfo('hourly', hLabels[el[0].index], hData[el[0].index], hourlyMetric);
            }
        }
    });

    dailyChart = new Chart(document.getElementById('dailyChart'), {
        type: dailyType,
        data: {
            labels: dLabels,
            datasets: [{
                data: dData,
                backgroundColor: dData.map(dMeta.colorFn),
                borderColor: dData.map(dMeta.colorFn),
                fill: false,
                tension: 0.4,
                pointRadius: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + dMeta.unit
                    }
                }
            },
            onClick: (evt, el) => {
                if (el.length > 0) updateInfo('daily', dLabels[el[0].index], dData[el[0].index], dailyMetric);
            }
        }
    });

    if (hData.length > 0) updateInfo('hourly', hLabels[0], hData[0], hourlyMetric);
    if (dData.length > 0) updateInfo('daily',  dLabels[0], dData[0], dailyMetric);
}

// ── Toggle chart type ─────────────────────────────────────────────────────────
function setChartType(type, target) {
    if (target === 'hourly') {
        hourlyType = type;
        document.getElementById('hourlyBar').classList.toggle('active', type === 'bar');
        document.getElementById('hourlyLine').classList.toggle('active', type === 'line');
    }
    if (target === 'daily') {
        dailyType = type;
        document.getElementById('dailyBar').classList.toggle('active', type === 'bar');
        document.getElementById('dailyLine').classList.toggle('active', type === 'line');
    }
    renderCharts();
}

// ── Hourly scroll list filter ─────────────────────────────────────────────────
function changeHourlyData() {
    const type = document.getElementById('hourlyFilter').value;
    document.querySelectorAll('.hourly-value').forEach(el => {
        let value = '', unit = '';
        if (type === 'aqi')     value = el.dataset.aqi;
        if (type === 'pm25')  { value = el.dataset.pm25; unit = ' µg'; }
        if (type === 'pm10')  { value = el.dataset.pm10; unit = ' µg'; }
        if (type === 'gas')   { value = el.dataset.gas;  unit = ' ppm'; }
        if (type === 'climate') {
            value = el.dataset.temp + '° / ' + el.dataset.humidity + '%';
            el.closest('.aqi-box')?.classList.add('neutral-box');
        } else {
            el.closest('.aqi-box')?.classList.remove('neutral-box');
        }
        el.innerText = value + unit;
    });
}

// ── Init ──────────────────────────────────────────────────────────────────────
setChartType('bar', 'hourly');
setChartType('bar', 'daily');
</script>

<?= $this->endSection() ?>
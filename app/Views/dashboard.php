<?= $this->extend('layout/main_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

<!-- ===================== SECTION 1: DASHBOARD ===================== -->
<div class="dashboard-wrapper">

    <!-- ASTHMA RISK CARD -->
    <div class="asthma-risk-card" id="asthma-risk-card">
        <div class="asthma-risk-indicator" id="asthma-risk-indicator"></div>
        <div class="asthma-risk-icon-wrap" id="asthma-risk-icon-wrap">
            <i class="fa-solid fa-lungs" id="asthma-risk-icon"></i>
        </div>
        <div class="asthma-risk-content">
            <p class="asthma-risk-title" id="asthma-risk-title">Risiko Asma: Waspada</p>
            <p class="asthma-risk-desc" id="asthma-risk-desc">Kualitas udara sedang dan dapat memicu gejala ringan pada penderita asma yang sensitif.</p>
        </div>
    </div>

    <div class="main-card">

        <!-- HERO -->
        <div class="hero-card">
            <div class="hero-left">
                <div class="hero-top">
                    <span id="realtime-clock">Loading...</span>
                </div>
                <div class="hero-main">
                    <h1 class="hero-temp" id="hero-aqi-value">--</h1>
                    <p class="hero-desc" id="hero-aqi-desc">Air Quality Index</p>
                </div>
                <div class="hero-bottom">
                    <div><i class="fa-solid fa-temperature-half"></i> <span id="hero-temp">--</span>°C</div>
                    <div><i class="fa-solid fa-droplet"></i> <span id="hero-humidity">--</span>%</div>
                </div>
            </div>
            <div class="hero-right">
                <p class="hero-label">PARTIKEL PM2.5</p>
                <h2 class="hero-value" id="hero-pm25-value">-- µg/m³</h2>
                <div class="hero-status" id="hero-status-badge">--</div>
            </div>
        </div>

        <!-- GAUGE ROW -->
        <div class="gauge-row">

            <div class="gauge-card" onclick="openPopup('NOx')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-NOx"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-NOx">--</span>
                        <span class="gauge-unit">ppm</span>
                    </div>
                </div>
                <p class="gauge-label">Gas Iritan (NOx / VOC)</p>
                <span class="badge badge-good" id="badge-NOx">--</span>
            </div>

            <div class="gauge-card" onclick="openPopup('pm25')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-pm25"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-pm25">--</span>
                        <span class="gauge-unit">µg/m³</span>
                    </div>
                </div>
                <p class="gauge-label">PM 2.5</p>
                <span class="badge badge-good" id="badge-pm25">--</span>
            </div>

            <div class="gauge-card" onclick="openPopup('pm10')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-pm10"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-pm10">--</span>
                        <span class="gauge-unit">µg/m³</span>
                    </div>
                </div>
                <p class="gauge-label">PM 10</p>
                <span class="badge badge-moderate" id="badge-pm10">--</span>
            </div>

            <div class="gauge-card" onclick="openPopup('aqi')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-aqi"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-aqi">--</span>
                        <span class="gauge-unit">AQI</span>
                    </div>
                </div>
                <p class="gauge-label">AQI</p>
                <span class="badge badge-moderate" id="badge-aqi">--</span>
            </div>

        </div>

    </div>

    <!-- HEALTH RECOMMENDATION CARD -->
    <div class="health-card">
        <div class="health-header">
            <h3>Health Recommendations</h3>
        </div>
        <div id="health-list" class="health-list"></div>
    </div>

</div>

<!-- ===================== SECTION 2: RIWAYAT ===================== -->
<div class="section-divider">
    <span>Riwayat Data</span>
</div>

<div class="history-wrapper">

    <!-- DAILY TABLE -->
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
                <div class="col-pm"><small>PM10</small><span><?= $item['pm10'] ?> µg/m³</span></div>
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

    <!-- CHARTS -->
    <div class="history-card-box">
        <h3>Grafik Historis</h3>

        <div class="chart-grid">

            <!-- HOURLY CHART -->
            <div class="chart-box">
                <div class="chart-filter-bar">
                    <h4>Hourly</h4>
                    <div class="chart-controls">
                        <select id="hourlyChartMetric" class="chart-metric-select" onchange="renderCharts()">
                            <option value="aqi">AQI</option>
                            <option value="pm25">PM2.5</option>
                            <option value="pm10">PM10</option>
                            <option value="gas">NOx / VOC</option>
                            <option value="temp">Suhu</option>
                            <option value="humidity">Kelembapan</option>
                        </select>
                        <div class="chart-toggle">
                            <button onclick="setChartType('bar','hourly')" id="hourlyBar" class="active">
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
                            <option value="pm10">PM10</option>
                            <option value="gas">NOx / VOC</option>
                            <option value="temp">Suhu</option>
                            <option value="humidity">Kelembapan</option>
                        </select>
                        <div class="chart-toggle">
                            <button onclick="setChartType('bar','daily')" id="dailyBar" class="active">
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

<!-- ===================== SECTION 3: INFORMASI AQI ===================== -->
<div class="section-divider">
    <span>Panduan AQI</span>
</div>

<div class="aqi-info-wrapper">

    <div class="aqi-header">
        <h2>Panduan Indikator AQI Indoor</h2>
        <p>Standar penilaian kualitas udara dalam ruangan. Klik untuk melihat detail & panduan.</p>
    </div>

    <div class="aqi-grid">

        <div class="aqi-card" onclick="openAqiPopup('good')">
            <div class="aqi-bar good"></div>
            <div class="aqi-content">
                <h4>GOOD</h4>
                <p>Udara bersih dan sehat untuk semua kelompok termasuk penderita asma.</p>
                <span class="aqi-badge badge-good">0 – 50</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openAqiPopup('moderate')">
            <div class="aqi-bar moderate"></div>
            <div class="aqi-content">
                <h4>MODERATE</h4>
                <p>Kualitas udara dapat diterima, namun dapat mempengaruhi penderita asma sensitif.</p>
                <span class="aqi-badge badge-moderate">51 – 100</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openAqiPopup('sensitive')">
            <div class="aqi-bar sensitive"></div>
            <div class="aqi-content">
                <h4>SENSITIVE GROUPS</h4>
                <p>Berisiko bagi penderita asma, lansia, dan anak-anak di dalam ruangan.</p>
                <span class="aqi-badge badge-sensitive">101 – 150</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openAqiPopup('unhealthy')">
            <div class="aqi-bar unhealthy"></div>
            <div class="aqi-content">
                <h4>UNHEALTHY</h4>
                <p>Masyarakat umum mulai merasakan dampak kesehatan di dalam ruangan.</p>
                <span class="aqi-badge badge-unhealthy">151 – 200</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openAqiPopup('very-unhealthy')">
            <div class="aqi-bar very-unhealthy"></div>
            <div class="aqi-content">
                <h4>VERY UNHEALTHY</h4>
                <p>Peringatan kesehatan darurat, semua orang berisiko di dalam ruangan.</p>
                <span class="aqi-badge badge-very-unhealthy">201 – 300</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openAqiPopup('hazardous')">
            <div class="aqi-bar hazardous"></div>
            <div class="aqi-content">
                <h4>HAZARDOUS</h4>
                <p>Kondisi udara sangat berbahaya, segera tinggalkan atau bersihkan ruangan.</p>
                <span class="aqi-badge badge-hazardous">301 – 500</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

    </div>

    <!-- AQI INFO POPUP OVERLAY -->
    <div class="aqi-overlay" id="aqiOverlay" onclick="closeOnAqiOverlay(event)">
        <div class="aqi-popup" id="aqiPopup">
            <div class="popup-head">
                <div class="popup-head-left">
                    <div class="popup-dot" id="popupDot"></div>
                    <div>
                        <div class="popup-title" id="popupTitle"></div>
                        <div class="popup-range" id="popupRange"></div>
                    </div>
                </div>
                <button class="aqi-popup-close" onclick="closeAqiPopup()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="popup-body">
                <p class="popup-desc" id="popupDesc"></p>
                <span class="popup-pm" id="popupPm"></span>
                <div class="dd-grid">
                    <div class="dd-box dd-do">
                        <div class="dd-title"><i class="fa-solid fa-circle-check"></i> DO'S</div>
                        <div id="doList"></div>
                    </div>
                    <div class="dd-box dd-dont">
                        <div class="dd-title"><i class="fa-solid fa-circle-xmark"></i> DON'TS</div>
                        <div id="dontList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ===================== GAUGE POPUP ===================== -->
<div id="popup-overlay" class="popup-overlay" onclick="closePopupOutside(event)">
    <div class="popup-box">
        <button class="popup-close" onclick="closePopup()">&#x2715;</button>

        <div class="popup-header">
            <div class="popup-icon-wrap" id="popup-icon-wrap">
                <i id="popup-icon" class="fa-solid fa-wind"></i>
            </div>
            <div>
                <h2 class="popup-title" id="popup-title">NOx / VOC</h2>
                <p class="popup-subtitle" id="popup-subtitle">Gas Iritan</p>
            </div>
        </div>

        <div class="popup-value-row">
            <span class="popup-big-value" id="popup-big-value">--</span>
            <span class="popup-big-unit" id="popup-big-unit">ppm</span>
        </div>

        <div class="popup-status-badge" id="popup-status-badge">--</div>

        <p class="popup-update">Last Update: just now</p>

        <div class="popup-scale">
            <div class="popup-scale-bar">
                <div class="scale-seg seg-good"></div>
                <div class="scale-seg seg-moderate"></div>
                <div class="scale-seg seg-poor"></div>
                <div class="scale-seg seg-unhealthy"></div>
                <div class="scale-seg seg-severe"></div>
                <div class="scale-seg seg-hazardous"></div>
                <div class="scale-needle" id="scale-needle"></div>
            </div>
            <div class="popup-scale-labels">
                <span>Good</span><span>Moderate</span><span>Poor</span>
                <span>Unhealthy</span><span>Severe</span><span>Hazardous</span>
            </div>
            <div class="popup-scale-numbers">
                <span>0</span><span>50</span><span>100</span>
                <span>150</span><span>200</span><span>300</span><span>510+</span>
            </div>
        </div>

        <p class="popup-avg-title" id="popup-avg-title">Average <span style="color:#94a3b8;font-size:12px;"></span></p>
        <div class="popup-avg-row">
            <div class="avg-box avg-green">
                <span class="avg-label">1 hr</span>
                <span class="avg-val" id="avg-1hr">--</span>
            </div>
            <div class="avg-box avg-yellow">
                <span class="avg-label">8 hr</span>
                <span class="avg-val" id="avg-8hr">--</span>
            </div>
            <div class="avg-box avg-pink">
                <span class="avg-label">12 hr</span>
                <span class="avg-val" id="avg-12hr">--</span>
            </div>
        </div>

    </div>
</div>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ============================================================
// DATA DARI PHP
// ============================================================
const hourlyRaw = <?= json_encode($historyHourly) ?>;
const dailyRaw  = <?= json_encode($historyDaily) ?>;
const latestData = <?= json_encode($latestUdara) ?>;

// ============================================================
// REALTIME CLOCK
// ============================================================
function updateClock() {
    const now = new Date();
    const datePart = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', weekday: 'long',
        day: 'numeric', month: 'long', year: 'numeric'
    }).format(now);
    const timePart = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit', hour12: false
    }).format(now);
    document.getElementById('realtime-clock').innerText = `${datePart} • ${timePart} WIB`;
}
setInterval(updateClock, 1000);
updateClock();

// ============================================================
// AQI HELPERS
// ============================================================
function getAqiColor(aqi) {
    if (aqi <= 50)  return '#22c55e';
    if (aqi <= 100) return '#f59e0b';
    if (aqi <= 150) return '#f97316';
    if (aqi <= 200) return '#ef4444';
    if (aqi <= 300) return '#a855f7';
    return '#7f1d1d';
}

function getAqiLabel(aqi) {
    if (aqi <= 50)  return 'Good';
    if (aqi <= 100) return 'Moderate';
    if (aqi <= 150) return 'Sensitive';
    if (aqi <= 200) return 'Unhealthy';
    if (aqi <= 300) return 'Very Unhealthy';
    return 'Hazardous';
}

function getAqiStatusClass(aqi) {
    if (aqi <= 50)  return 'status-good';
    if (aqi <= 100) return 'status-moderate';
    if (aqi <= 150) return 'status-poor';
    if (aqi <= 200) return 'status-unhealthy';
    if (aqi <= 300) return 'status-severe';
    return 'status-hazardous';
}

function getBadgeClass(aqi) {
    if (aqi <= 50)  return 'badge-good';
    if (aqi <= 100) return 'badge-moderate';
    return 'badge-unhealthy';
}

// ============================================================
// GAUGE HELPER
// ============================================================
const gaugeInstances = {};

function createGauge(id, value, max, color) {
    const canvas = document.getElementById(id);
    if (!canvas) return;
    if (gaugeInstances[id]) gaugeInstances[id].destroy();
    gaugeInstances[id] = new Chart(canvas, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [Math.min(value, max), Math.max(max - value, 0)],
                backgroundColor: [color, '#e2e8f0'],
                borderWidth: 0,
                borderRadius: 10
            }]
        },
        options: {
            cutout: '78%',
            rotation: -90,
            circumference: 360,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            animation: { duration: 800 },
            responsive: true,
            maintainAspectRatio: true
        }
    });
}

// ============================================================
// RENDER DASHBOARD DATA
// ============================================================
function renderDashboard() {
    const d = latestData;
    if (!d) return;

    const aqi      = parseFloat(d.aqi)      || 0;
    const pm25     = parseFloat(d.pm25)     || 0;
    const pm10     = parseFloat(d.pm10)     || 0;
    const nox      = parseFloat(d.nox)      || 0;
    const suhu     = parseFloat(d.suhu)     || 0;
    const kelembaban = parseFloat(d.kelembaban) || 0;

    const aqiColor = getAqiColor(aqi);
    const aqiLabel = getAqiLabel(aqi);

    // Hero
    document.getElementById('hero-aqi-value').textContent = aqi;
    document.getElementById('hero-aqi-desc').textContent  = 'Air Quality ' + aqiLabel;
    document.getElementById('hero-temp').textContent      = suhu;
    document.getElementById('hero-humidity').textContent  = kelembaban;
    document.getElementById('hero-pm25-value').textContent = pm25 + ' µg/m³';

    const heroBadge = document.getElementById('hero-status-badge');
    heroBadge.textContent  = aqiLabel.toUpperCase();
    heroBadge.style.background = aqiColor;

    // Gauge values
    document.getElementById('val-NOx').textContent  = nox;
    document.getElementById('val-pm25').textContent = pm25;
    document.getElementById('val-pm10').textContent = pm10;
    document.getElementById('val-aqi').textContent  = aqi;

    // Gauge badges
    ['NOx','pm25','pm10','aqi'].forEach(k => {
        const el = document.getElementById('badge-' + k);
        const val = k === 'NOx' ? nox : k === 'pm25' ? pm25 : k === 'pm10' ? pm10 : aqi;
        el.textContent = getAqiLabel(Math.round(val));
        el.className   = 'badge ' + getBadgeClass(Math.round(val));
    });

    // Gauges
    createGauge('gauge-NOx',  nox,  300, getAqiColor(nox));
    createGauge('gauge-pm25', pm25, 300, getAqiColor(pm25));
    createGauge('gauge-pm10', pm10, 300, getAqiColor(pm10));
    createGauge('gauge-aqi',  aqi,  300, aqiColor);

    // Asthma risk
    const risk = getAsthmaRisk(aqi);
    const card = document.getElementById('asthma-risk-card');
    card.style.background  = risk.cardBg;
    card.style.borderColor = risk.borderColor;
    document.getElementById('asthma-risk-indicator').style.background = risk.color;
    document.getElementById('asthma-risk-icon-wrap').style.background = risk.iconBg;
    document.getElementById('asthma-risk-icon').style.color           = risk.iconColor;
    document.getElementById('asthma-risk-title').style.color          = risk.titleColor;
    document.getElementById('asthma-risk-title').textContent          = risk.title;
    document.getElementById('asthma-risk-desc').textContent           = risk.desc;

    // Health recommendations
    const healthData = getHealthRecommendations(aqi);
    const listContainer = document.getElementById('health-list');
    listContainer.innerHTML = '';
    healthData.items.forEach(item => {
        listContainer.innerHTML += `
            <div class="health-item">
                <i class="fa-solid fa-circle-check"></i>
                <span>${item}</span>
            </div>`;
    });

    // Update popup data with real values
    popupData.NOx.value  = nox;
    popupData.pm25.value = pm25;
    popupData.pm10.value = pm10;
    popupData.aqi.value  = aqi;
    popupData.NOx.status  = getAqiLabel(nox).toUpperCase();
    popupData.pm25.status = getAqiLabel(pm25).toUpperCase();
    popupData.pm10.status = getAqiLabel(pm10).toUpperCase();
    popupData.aqi.status  = aqiLabel.toUpperCase();
    popupData.NOx.statusClass  = getAqiStatusClass(nox);
    popupData.pm25.statusClass = getAqiStatusClass(pm25);
    popupData.pm10.statusClass = getAqiStatusClass(pm10);
    popupData.aqi.statusClass  = getAqiStatusClass(aqi);
    popupData.NOx.needlePct  = Math.min((nox / 510) * 100, 100);
    popupData.pm25.needlePct = Math.min((pm25 / 510) * 100, 100);
    popupData.pm10.needlePct = Math.min((pm10 / 510) * 100, 100);
    popupData.aqi.needlePct  = Math.min((aqi / 510) * 100, 100);
}

// ============================================================
// HEALTH RECOMMENDATIONS
// ============================================================
function getHealthRecommendations(aqi) {
    if (aqi <= 50) return { status: "Good", items: [
        "Lakukan aktivitas normal di dalam ruangan",
        "Biarkan ventilasi alami terbuka",
        "Tidak perlu air purifier",
        "Bersihkan debu ringan secara rutin"
    ]};
    if (aqi <= 100) return { status: "Moderate", items: [
        "Nyalakan air purifier di mode rendah",
        "Buka jendela bila udara luar lebih baik",
        "Hindari asap rokok & polusi indoor",
        "Periksa filter AC dan bersihkan jika kotor"
    ]};
    if (aqi <= 150) return { status: "Sensitive", items: [
        "Tingkatkan ventilasi atau buka semua jendela",
        "Gunakan air purifier di mode tinggi",
        "Hindari asap & pengharum berbakar",
        "Penderita asma siapkan inhaler"
    ]};
    if (aqi <= 200) return { status: "Unhealthy", items: [
        "Evakuasi penderita asma ke ruangan lain",
        "Air purifier mode maksimal",
        "Kurangi aktivitas dalam ruangan",
        "Gunakan masker N95 jika harus berada di ruangan"
    ]};
    if (aqi <= 300) return { status: "Very Unhealthy", items: [
        "Buat ruangan bersih (clean room)",
        "Air purifier nyala terus",
        "Gunakan masker di dalam ruangan",
        "Segera evakuasi semua penghuni ruangan"
    ]};
    return { status: "Hazardous", items: [
        "Evakuasi segera seluruh penghuni ruangan",
        "Gunakan air purifier maksimal",
        "Tutup semua celah udara",
        "Siapkan tindakan darurat asma"
    ]};
}

// ============================================================
// ASTHMA RISK
// ============================================================
function getAsthmaRisk(aqi) {
    if (aqi <= 50)  return { title: "Risiko Asma: Rendah",         desc: "Kualitas udara baik. Penderita asma dapat beraktivitas normal tanpa kekhawatiran.",                                          color: "#22c55e", cardBg: "#f0fdf4", borderColor: "#bbf7d0", iconBg: "#dcfce7", iconColor: "#16a34a", titleColor: "#15803d" };
    if (aqi <= 100) return { title: "Risiko Asma: Waspada",        desc: "Kualitas udara sedang dan dapat memicu gejala ringan pada penderita asma yang sensitif.",                                    color: "#f59e0b", cardBg: "#fffbeb", borderColor: "#fde68a", iconBg: "#fef3c7", iconColor: "#d97706", titleColor: "#b45309" };
    if (aqi <= 150) return { title: "Risiko Asma: Tinggi",         desc: "Udara tidak sehat bagi kelompok sensitif. Penderita asma disarankan membawa inhaler dan mengurangi aktivitas.",             color: "#f97316", cardBg: "#fff7ed", borderColor: "#fed7aa", iconBg: "#ffedd5", iconColor: "#ea580c", titleColor: "#c2410c" };
    if (aqi <= 200) return { title: "Risiko Asma: Sangat Tinggi",  desc: "Kualitas udara tidak sehat. Penderita asma berisiko mengalami serangan. Hindari paparan udara luar.",                       color: "#ef4444", cardBg: "#fef2f2", borderColor: "#fecaca", iconBg: "#fee2e2", iconColor: "#dc2626", titleColor: "#b91c1c" };
    if (aqi <= 300) return { title: "Risiko Asma: Kritis",         desc: "Kualitas udara sangat tidak sehat. Penderita asma harus tetap di dalam ruangan dan siapkan obat darurat.",                  color: "#a855f7", cardBg: "#faf5ff", borderColor: "#e9d5ff", iconBg: "#f3e8ff", iconColor: "#9333ea", titleColor: "#7e22ce" };
    return             { title: "Risiko Asma: Darurat",           desc: "Kondisi udara berbahaya. Penderita asma memerlukan penanganan segera. Hubungi layanan kesehatan jika timbul gejala.",       color: "#991b1b", cardBg: "#fff1f2", borderColor: "#fecdd3", iconBg: "#ffe4e6", iconColor: "#be123c", titleColor: "#9f1239" };
}

// ============================================================
// GAUGE POPUP
// ============================================================
const popupData = {
    NOx:  { title: 'NOx / VOC', subtitle: 'Gas Iritan',         icon: 'fa-wind',       iconColor: '#22c55e', value: '--', unit: 'ppm',    status: '--', statusClass: 'status-good',     needlePct: 0, avgTitle: 'NOx Average',   avgUnit: 'ppm',    avg1: '--', avg8: '--', avg12: '--' },
    pm25: { title: 'PM 2.5',    subtitle: 'Partikel Halus',     icon: 'fa-smog',       iconColor: '#22c55e', value: '--', unit: 'µg/m³', status: '--', statusClass: 'status-good',     needlePct: 0, avgTitle: 'PM2.5 Average',  avgUnit: 'µg/m³', avg1: '--', avg8: '--', avg12: '--' },
    pm10: { title: 'PM 10',     subtitle: 'Partikel Kasar',     icon: 'fa-circle-dot', iconColor: '#f59e0b', value: '--', unit: 'µg/m³', status: '--', statusClass: 'status-moderate', needlePct: 0, avgTitle: 'PM10 Average',   avgUnit: 'µg/m³', avg1: '--', avg8: '--', avg12: '--' },
    aqi:  { title: 'AQI',       subtitle: 'Air Quality Index',  icon: 'fa-gauge-high', iconColor: '#f59e0b', value: '--', unit: 'AQI',   status: '--', statusClass: 'status-moderate', needlePct: 0, avgTitle: 'AQI Average',    avgUnit: 'index',  avg1: '--', avg8: '--', avg12: '--' }
};

function openPopup(key) {
    const d = popupData[key];
    document.getElementById('popup-title').textContent     = d.title;
    document.getElementById('popup-subtitle').textContent  = d.subtitle;
    document.getElementById('popup-big-value').textContent = d.value;
    document.getElementById('popup-big-unit').textContent  = d.unit;
    const badge = document.getElementById('popup-status-badge');
    badge.textContent = d.status;
    badge.className   = 'popup-status-badge ' + d.statusClass;
    const iconWrap = document.getElementById('popup-icon-wrap');
    iconWrap.style.background = d.iconColor + '22';
    const icon = document.getElementById('popup-icon');
    icon.className  = 'fa-solid ' + d.icon;
    icon.style.color = d.iconColor;
    document.getElementById('scale-needle').style.left = d.needlePct + '%';
    document.getElementById('popup-avg-title').innerHTML =
        d.avgTitle + ' <span style="color:#94a3b8;font-size:12px;">(in ' + d.avgUnit + ')</span>';
    document.getElementById('avg-1hr').textContent  = d.avg1;
    document.getElementById('avg-8hr').textContent  = d.avg8;
    document.getElementById('avg-12hr').textContent = d.avg12;
    document.getElementById('popup-overlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('popup-overlay').classList.remove('active');
    document.body.style.overflow = '';
}

function closePopupOutside(e) {
    if (e.target === document.getElementById('popup-overlay')) closePopup();
}

// ============================================================
// CHART HELPERS
// ============================================================
function getAqiColorChart(v) {
    if (v <= 50)  return "#76a13c";
    if (v <= 100) return "#d9b11c";
    if (v <= 150) return "#d9732e";
    if (v <= 200) return "#c93d3d";
    if (v <= 300) return "#7e4f94";
    return "#4d1d2b";
}
function getPm25Color(v) { if(v<=12)return"#76a13c"; if(v<=35)return"#d9b11c"; if(v<=55)return"#d9732e"; if(v<=150)return"#c93d3d"; if(v<=250)return"#7e4f94"; return"#4d1d2b"; }
function getPm10Color(v) { if(v<=54)return"#76a13c"; if(v<=154)return"#d9b11c"; if(v<=254)return"#d9732e"; if(v<=354)return"#c93d3d"; if(v<=424)return"#7e4f94"; return"#4d1d2b"; }
function getGasColor(v)  { if(v<=50)return"#76a13c"; if(v<=100)return"#d9b11c"; if(v<=199)return"#d9732e"; if(v<=299)return"#c93d3d"; return"#7e4f94"; }
function getTempColor(v) { if(v<=15)return"#2980b9"; if(v<=22)return"#76a13c"; if(v<=28)return"#d9b11c"; if(v<=35)return"#d9732e"; return"#c93d3d"; }
function getHumidityColor(v) { if(v<=25)return"#c93d3d"; if(v<=39)return"#d9732e"; if(v<=60)return"#76a13c"; if(v<=75)return"#d9b11c"; return"#7e4f94"; }
function getAqiDesc(aqi) { if(aqi<=50)return"Baik"; if(aqi<=100)return"Sedang"; if(aqi<=150)return"Tidak sehat bagi kelompok sensitif"; if(aqi<=200)return"Tidak sehat"; return"Berbahaya"; }

const METRICS = {
    aqi:      { key: 'aqi',      label: 'AQI',       unit: '',        colorFn: getAqiColorChart },
    pm25:     { key: 'pm25',     label: 'PM2.5',      unit: ' µg/m³', colorFn: getPm25Color     },
    pm10:     { key: 'pm10',     label: 'PM10',       unit: ' µg/m³', colorFn: getPm10Color     },
    gas:      { key: 'gas',      label: 'NOx / VOC',  unit: ' ppm',   colorFn: getGasColor      },
    temp:     { key: 'temp',     label: 'Suhu',       unit: ' °C',    colorFn: getTempColor     },
    humidity: { key: 'humidity', label: 'Kelembapan', unit: ' %',     colorFn: getHumidityColor },
};

let hourlyType  = 'bar';
let dailyType   = 'bar';
let hourlyChart = null;
let dailyChart  = null;

function extractData(rawArr, metricKey) {
    return rawArr.map(item => {
        if (metricKey === 'gas') return parseFloat(item.gas !== undefined ? item.gas : item.nox) || 0;
        return parseFloat(item[METRICS[metricKey].key]) || 0;
    });
}
function extractLabels(rawArr, isHourly) {
    return rawArr.map(item => isHourly ? item.time : item.date);
}
function updateInfo(target, label, value, metricKey) {
    const m = METRICS[metricKey];
    document.getElementById(target + 'Time').innerText        = label;
    document.getElementById(target + 'MetricLabel').innerText = m.label;
    document.getElementById(target + 'Desc').innerText =
        metricKey === 'aqi' ? getAqiDesc(value) + ' (' + value + ')' : value + m.unit;
}

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
            datasets: [{ data: hData, backgroundColor: hData.map(hMeta.colorFn), borderColor: hData.map(hMeta.colorFn), fill: false, tension: 0.4, pointRadius: 3 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + hMeta.unit } } },
            scales: { x: { ticks: { callback: (v, i) => i % 4 === 0 ? hLabels[i] : '' } }, y: { beginAtZero: true } },
            onClick: (evt, el) => { if (el.length > 0) updateInfo('hourly', hLabels[el[0].index], hData[el[0].index], hourlyMetric); }
        }
    });

    dailyChart = new Chart(document.getElementById('dailyChart'), {
        type: dailyType,
        data: {
            labels: dLabels,
            datasets: [{ data: dData, backgroundColor: dData.map(dMeta.colorFn), borderColor: dData.map(dMeta.colorFn), fill: false, tension: 0.4, pointRadius: 3 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.parsed.y + dMeta.unit } } },
            onClick: (evt, el) => { if (el.length > 0) updateInfo('daily', dLabels[el[0].index], dData[el[0].index], dailyMetric); }
        }
    });

    if (hData.length > 0) updateInfo('hourly', hLabels[0], hData[0], hourlyMetric);
    if (dData.length > 0) updateInfo('daily',  dLabels[0], dData[0], dailyMetric);
}

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

// ============================================================
// AQI INFO POPUP
// ============================================================
const aqiData = {
    good: {
        title: 'Good — Udara Baik', range: 'AQI 0–50 · PM2.5 < 12 µg/m³',
        color: '#22c55e', bg: '#dcfce7',
        desc: 'Kualitas udara dalam ruangan sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak.',
        pm: 'PM2.5 < 12 µg/m³', pmBg: '#dcfce7', pmColor: '#15803d',
        dos: ['Lakukan aktivitas normal di dalam ruangan','Biarkan ventilasi alami terbuka','Matikan air purifier untuk hemat energi','Catat kondisi ini sebagai baseline sensor'],
        donts: ['Jangan abaikan pemeliharaan ventilasi rutin','Jangan biarkan sumber polutan baru masuk','Jangan memasak tanpa exhaust fan aktif']
    },
    moderate: {
        title: 'Moderate — Udara Sedang', range: 'AQI 51–100 · PM2.5 12–35 µg/m³',
        color: '#eab308', bg: '#fef9c3',
        desc: 'Kualitas udara cukup dapat diterima namun mulai menurun. Penderita asma yang sangat sensitif mungkin mengalami gejala ringan.',
        pm: 'PM2.5 12–35 µg/m³', pmBg: '#fef9c3', pmColor: '#a16207',
        dos: ['Nyalakan air purifier di mode rendah','Buka jendela bila udara luar lebih baik','Periksa filter AC dan bersihkan jika kotor','Batasi aktivitas memasak yang menghasilkan asap'],
        donts: ['Jangan bakar dupa atau lilin aromaterapi','Jangan semprotkan pengharum ruangan aerosol','Jangan biarkan penderita asma beraktivitas berat']
    },
    sensitive: {
        title: 'Sensitive Groups — Tidak Sehat (Sensitif)', range: 'AQI 101–150 · PM2.5 35–55 µg/m³',
        color: '#f97316', bg: '#ffedd5',
        desc: 'Udara berbahaya bagi kelompok sensitif. Penderita asma berisiko mengalami gangguan pernapasan.',
        pm: 'PM2.5 35–55 µg/m³', pmBg: '#ffedd5', pmColor: '#c2410c',
        dos: ['Nyalakan air purifier di mode tinggi','Segera cari sumber polutan','Anjurkan penderita asma siapkan inhaler','Tingkatkan ventilasi'],
        donts: ['Jangan izinkan penderita asma beraktivitas fisik','Jangan memasak dengan bahan yang berasap','Jangan biarkan anak-anak dan lansia di ruangan lama']
    },
    unhealthy: {
        title: 'Unhealthy — Tidak Sehat', range: 'AQI 151–200 · PM2.5 55–150 µg/m³',
        color: '#ef4444', bg: '#fee2e2',
        desc: 'Seluruh penghuni ruangan mulai berisiko terkena dampak kesehatan. Tindakan perbaikan harus segera dilakukan.',
        pm: 'PM2.5 55–150 µg/m³', pmBg: '#fee2e2', pmColor: '#b91c1c',
        dos: ['Evakuasi penderita asma ke ruangan lain','Nyalakan semua air purifier ke mode maksimal','Hubungi teknisi untuk cek sistem ventilasi','Gunakan masker N95'],
        donts: ['Jangan tinggalkan penderita asma tanpa pengawasan','Jangan matikan air purifier','Jangan tunda perbaikan sumber masalah']
    },
    'very-unhealthy': {
        title: 'Very Unhealthy — Sangat Tidak Sehat', range: 'AQI 201–300 · PM2.5 150–250 µg/m³',
        color: '#a855f7', bg: '#f3e8ff',
        desc: 'Kondisi darurat kesehatan di dalam ruangan. Semua orang berisiko mengalami efek serius.',
        pm: 'PM2.5 150–250 µg/m³', pmBg: '#f3e8ff', pmColor: '#7e22ce',
        dos: ['Segera evakuasi semua penghuni','Hubungi pihak terkait jika dicurigai kebocoran','Gunakan masker N95/respirator','Matikan semua sumber api'],
        donts: ['Jangan tunda evakuasi','Jangan abaikan tanda-tanda sumber bahaya','Jangan masuk kembali sebelum udara diperbaiki']
    },
    hazardous: {
        title: 'Hazardous — Berbahaya', range: 'AQI 301–500 · PM2.5 > 250 µg/m³',
        color: '#7f1d1d', bg: '#fecaca',
        desc: 'Kondisi udara sangat kritis dan mengancam jiwa. Semua orang harus segera keluar.',
        pm: 'PM2.5 > 250 µg/m³', pmBg: '#fecaca', pmColor: '#7f1d1d',
        dos: ['Evakuasi segera seluruh penghuni','Hubungi 119 jika ada indikasi kebakaran','Tutup pintu dan jendela untuk isolasi','Gunakan jalur evakuasi yang aman'],
        donts: ['Jangan masuk kembali dalam kondisi apapun','Jangan coba atasi sendiri tanpa alat proteksi','Jangan gunakan lift — gunakan tangga darurat']
    }
};

function openAqiPopup(key) {
    const d = aqiData[key];
    document.getElementById('popupTitle').textContent = d.title;
    document.getElementById('popupRange').textContent = d.range;
    const dot = document.getElementById('popupDot');
    dot.style.background = d.bg;
    dot.innerHTML = `<div style="width:16px;height:16px;border-radius:5px;background:${d.color}"></div>`;
    document.getElementById('popupDesc').textContent = d.desc;
    const pm = document.getElementById('popupPm');
    pm.textContent     = d.pm;
    pm.style.background = d.pmBg;
    pm.style.color      = d.pmColor;
    document.getElementById('doList').innerHTML   = d.dos.map(t   => `<div class="dd-item"><span class="dd-bullet bullet-do"></span><span>${t}</span></div>`).join('');
    document.getElementById('dontList').innerHTML = d.donts.map(t => `<div class="dd-item"><span class="dd-bullet bullet-dont"></span><span>${t}</span></div>`).join('');
    document.getElementById('aqiOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeAqiPopup() {
    document.getElementById('aqiOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function closeOnAqiOverlay(e) {
    if (e.target === document.getElementById('aqiOverlay')) closeAqiPopup();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closePopup(); closeAqiPopup(); }
});

// ============================================================
// INIT
// ============================================================
renderDashboard();
setChartType('bar', 'hourly');
setChartType('bar', 'daily');
</script>

<?= $this->endSection() ?>
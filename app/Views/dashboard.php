<?= $this->extend('layout/main_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css') ?>">

<!-- ===================== SECTION 1: DASHBOARD ===================== -->
<div class="dashboard-wrapper">

    <!-- LEFT COLUMN -->
    <div class="dash-left">

        <!-- HERO CARD -->
        <div class="hero-card">
            <div class="hero-badge-row">
                <span class="hero-status" id="hero-status-badge">--</span>
                <span class="hero-time-label">just now</span>
            </div>

            <div class="hero-aqi-block">
                <h1 class="hero-temp" id="hero-aqi-value">--</h1>
                <div class="hero-aqi-info">
                    <p class="hero-desc" id="hero-aqi-desc">Air Quality Index</p>
                    <p class="hero-pm25" id="hero-pm25-value">-- µg/m³ PM2.5</p>
                </div>
            </div>

            <div class="hero-climate-row">
                <div class="hero-climate-item">
                    <i class="fa-solid fa-temperature-half"></i>
                    <span id="hero-temp">--</span>°C
                </div>
                <div class="hero-climate-item">
                    <i class="fa-solid fa-droplet"></i>
                    <span id="hero-humidity">--</span>%
                </div>
            </div>

            <!-- SCALE BAR -->
            <div class="hero-scale-wrap">
                <div class="hero-scale-bar">
                    <div class="hs-seg hs-good"></div>
                    <div class="hs-seg hs-moderate"></div>
                    <div class="hs-seg hs-sensitive"></div>
                    <div class="hs-seg hs-unhealthy"></div>
                    <div class="hs-seg hs-very"></div>
                    <div class="hs-seg hs-hazard"></div>
                    <div class="hero-scale-needle" id="hero-scale-needle"></div>
                </div>
                <div class="hero-scale-nums">
                    <span>0</span><span>50</span><span>100</span>
                    <span>150</span><span>200</span><span>300</span><span>500+</span>
                </div>
            </div>
        </div>

        <!-- ASTHMA RISK CARD -->
        <div class="asthma-risk-card" id="asthma-risk-card">
            <div class="asthma-risk-indicator" id="asthma-risk-indicator"></div>
            <div class="asthma-risk-icon-wrap" id="asthma-risk-icon-wrap">
                <i class="fa-solid fa-lungs" id="asthma-risk-icon"></i>
            </div>
            <div class="asthma-risk-content">
                <p class="asthma-risk-title" id="asthma-risk-title">Risiko Asma</p>
            </div>
            <span class="asthma-badge" id="asthma-badge">--</span>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="dash-right">

        <!-- HEALTH RECOMMENDATION CARD -->
        <div class="health-card">
            <div class="health-header">
                <i class="fa-solid fa-shield-heart" style="color:#3b82f6;font-size:16px;"></i>
                <h3>Health Recommendations</h3>
            </div>
            <div id="health-list" class="health-list"></div>
        </div>

        <!-- GAUGE ROW -->
        <div class="gauge-row">

            <div class="gauge-card" onclick="openPopup('aqi')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-aqi"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-aqi">--</span>
                        <span class="gauge-unit">AQI</span>
                    </div>
                </div>
                <p class="gauge-label">Air Quality</p>
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
            </div>

            <div class="gauge-card" onclick="openPopup('NOx')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-NOx"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-NOx">--</span>
                        <span class="gauge-unit">ppm</span>
                    </div>
                </div>
                <p class="gauge-label">NOx / VOC</p>
            </div>

        </div>

    </div>

</div>

<!-- ===================== SECTION 2: RIWAYAT ===================== -->

<div class="history-wrapper">

<!-- TIMELINE SCROLLABLE PER JAM -->
<div class="timeline-card">
    <div class="timeline-card-header">
        <div>
            <h3 class="timeline-card-title">Prakiraan AQI per Jam</h3>
            <p class="timeline-card-sub">Perkiraan kualitas udara 24 jam ke depan</p>
        </div>
        <span class="timeline-now-badge" id="timeline-now-badge">-- AQI sekarang</span>
    </div>
    <div class="fc-scroll">
        <div class="fc-timeline" id="fc-timeline"></div>
    </div>
</div>

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

    <!-- UNIFIED CHART CARD -->
    <div class="history-card-box">
        <h3>Riwayat</h3>
        <p class="rwh-sub">Grafik riwayat kualitas udara</p>
    
        <div class="rwh-header">
        </div>

        <div class="rwh-controls">
            <div class="rwh-highlight">
                <span class="rwh-dot" id="rwh-dot"></span>
                <div>
                    <div class="rwh-val-row">
                        <span class="rwh-val" id="rwh-val">--</span>
                        <span class="rwh-desc" id="rwh-desc"></span>
                    </div>
                    <div class="rwh-meta" id="rwh-meta">--</div>
                </div>
            </div>
            <div class="rwh-right">
                <div class="rwh-tabs">
                    <button class="rwh-tab active" onclick="rwhSetTab('jam',this)">per jam</button>
                    <button class="rwh-tab"        onclick="rwhSetTab('hari',this)">per hari</button>
                    <button class="rwh-tab"        onclick="rwhSetTab('bulan',this)">bulanan</button>
                </div>
                <select class="rwh-select" id="rwh-metric" onchange="rwhRender()">
                    <option value="aqi">AQI</option>
                    <option value="pm25">PM2.5</option>
                    <option value="pm10">PM10</option>
                    <option value="nox">NOx / VOC</option>
                    <option value="temp">Suhu</option>
                    <option value="humidity">Kelembapan</option>
                </select>
            </div>
        </div>

        <div class="mini-chart-card">

        <div class="rwh-canvas-wrap">
            <canvas id="rwhChart" role="img" aria-label="Bar chart riwayat kualitas udara">Data riwayat kualitas udara.</canvas>
        </div>

    </div>

</div>

<!-- ===================== SECTION 3: INFORMASI AQI ===================== -->

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

<!-- ===================== INLINE CSS TAMBAHAN ===================== -->
<style>
/* --- Unified History Chart --- */
.rwh-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px; }
.rwh-sub { margin:0; font-size:12px; color:#94a3b8; }
.rwh-controls { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:16px; }
.rwh-highlight { display:flex; align-items:center; gap:10px; }
.rwh-dot { width:10px; height:10px; border-radius:50%; background:#f97316; flex-shrink:0; transition:background .3s; }
.rwh-val-row { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
.rwh-val { font-size:15px; font-weight:600; color:#1e293b; }
.rwh-desc { font-size:13px; color:#64748b; }
.rwh-meta { font-size:11px; color:#94a3b8; margin-top:2px; }
.rwh-right { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.rwh-tabs { display:flex; background:#f1f5f9; border-radius:8px; padding:2px; gap:1px; }
.rwh-tab { border:none; background:transparent; font-size:12px; color:#64748b; padding:4px 10px; border-radius:6px; cursor:pointer; transition:all .15s; }
.rwh-tab.active { background:#fff; color:#1e293b; font-weight:600; border:0.5px solid #e2e8f0; box-shadow:0 1px 2px rgba(0,0,0,.06); }
.rwh-select { font-size:12px; color:#1e293b; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:5px 10px; cursor:pointer; outline:none; }
.rwh-canvas-wrap { position:relative; width:100%; height:240px; }
@media (max-width:600px) {
    .rwh-canvas-wrap { height:180px; }
    .rwh-controls { flex-direction:column; align-items:flex-start; }
}
</style>

<!-- ===================== SCRIPTS ===================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ============================================================
// DATA DARI PHP
// ============================================================
const hourlyRaw  = <?= json_encode($historyHourly) ?>;
const dailyRaw   = <?= json_encode($historyDaily) ?>;
const latestData = <?= json_encode($latestUdara) ?>;
const monthlyRaw = <?= isset($historyMonthly) ? json_encode($historyMonthly) : '[]' ?>;

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

    const aqi        = parseFloat(d.aqi)        || 0;
    const pm25       = parseFloat(d.pm25)       || 0;
    const pm10       = parseFloat(d.pm10)       || 0;
    const nox        = parseFloat(d.nox)        || 0;
    const suhu       = parseFloat(d.suhu)       || 0;
    const kelembaban = parseFloat(d.kelembaban) || 0;

    const aqiColor = getAqiColor(aqi);
    const aqiLabel = getAqiLabel(aqi);

    // Hero
    document.getElementById('hero-aqi-value').textContent   = aqi;
    document.getElementById('hero-aqi-desc').textContent    = 'Air Quality · ' + aqiLabel;
    document.getElementById('hero-temp').textContent        = suhu;
    document.getElementById('hero-humidity').textContent    = kelembaban;
    document.getElementById('hero-pm25-value').textContent  = pm25 + ' µg/m³ PM2.5';

    const heroBadge = document.getElementById('hero-status-badge');
    heroBadge.textContent      = aqiLabel.toUpperCase();
    heroBadge.style.background = aqiColor;

    // Scale needle (0–500 range → 0–100%)
    const needlePct = Math.min((aqi / 500) * 100, 100);
    document.getElementById('hero-scale-needle').style.left = needlePct + '%';

    // Gauge values
    document.getElementById('val-NOx').textContent  = nox;
    document.getElementById('val-pm25').textContent = pm25;
    document.getElementById('val-pm10').textContent = pm10;
    document.getElementById('val-aqi').textContent  = aqi;

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

    const asthmaBadge = document.getElementById('asthma-badge');
    asthmaBadge.textContent         = risk.badgeLabel;
    asthmaBadge.style.background    = risk.color;
    asthmaBadge.style.color         = '#fff';
    asthmaBadge.style.display       = 'inline-block';
    asthmaBadge.style.padding       = '5px 12px';
    asthmaBadge.style.borderRadius  = '20px';
    asthmaBadge.style.fontSize      = '11px';
    asthmaBadge.style.fontWeight    = '700';
    asthmaBadge.style.letterSpacing = '0.04em';
    asthmaBadge.style.whiteSpace    = 'nowrap';

    // Health recommendations
    const healthData    = getHealthRecommendations(aqi);
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
    if (aqi <= 50)  return { status: "Good",          items: ["Lakukan aktivitas normal di dalam ruangan","Biarkan ventilasi alami terbuka","Tidak perlu air purifier","Bersihkan debu ringan secara rutin"] };
    if (aqi <= 100) return { status: "Moderate",      items: ["Nyalakan air purifier di mode rendah","Buka jendela bila udara luar lebih baik","Hindari asap rokok & polusi indoor","Periksa filter AC dan bersihkan jika kotor"] };
    if (aqi <= 150) return { status: "Sensitive",     items: ["Tingkatkan ventilasi atau buka semua jendela","Gunakan air purifier di mode tinggi","Hindari asap & pengharum berbakar","Penderita asma siapkan inhaler"] };
    if (aqi <= 200) return { status: "Unhealthy",     items: ["Evakuasi penderita asma ke ruangan lain","Air purifier mode maksimal","Kurangi aktivitas dalam ruangan","Gunakan masker N95 jika harus berada di ruangan"] };
    if (aqi <= 300) return { status: "Very Unhealthy",items: ["Buat ruangan bersih (clean room)","Air purifier nyala terus","Gunakan masker di dalam ruangan","Segera evakuasi semua penghuni ruangan"] };
    return             { status: "Hazardous",         items: ["Evakuasi segera seluruh penghuni ruangan","Gunakan air purifier maksimal","Tutup semua celah udara","Siapkan tindakan darurat asma"] };
}

// ============================================================
// ASTHMA RISK
// ============================================================
function getAsthmaRisk(aqi) {
    if (aqi <= 50)  return { title:"Risiko Asma", badgeLabel:"RENDAH",        color:"#22c55e", cardBg:"#f0fdf4", borderColor:"#bbf7d0", iconBg:"#dcfce7", iconColor:"#16a34a", titleColor:"#15803d" };
    if (aqi <= 100) return { title:"Risiko Asma", badgeLabel:"WASPADA",       color:"#f59e0b", cardBg:"#fffbeb", borderColor:"#fde68a", iconBg:"#fef3c7", iconColor:"#d97706", titleColor:"#b45309" };
    if (aqi <= 150) return { title:"Risiko Asma", badgeLabel:"TINGGI",        color:"#f97316", cardBg:"#fff7ed", borderColor:"#fed7aa", iconBg:"#ffedd5", iconColor:"#ea580c", titleColor:"#c2410c" };
    if (aqi <= 200) return { title:"Risiko Asma", badgeLabel:"SANGAT TINGGI", color:"#ef4444", cardBg:"#fef2f2", borderColor:"#fecaca", iconBg:"#fee2e2", iconColor:"#dc2626", titleColor:"#b91c1c" };
    if (aqi <= 300) return { title:"Risiko Asma", badgeLabel:"KRITIS",        color:"#a855f7", cardBg:"#faf5ff", borderColor:"#e9d5ff", iconBg:"#f3e8ff", iconColor:"#9333ea", titleColor:"#7e22ce" };
    return             { title:"Risiko Asma", badgeLabel:"DARURAT",       color:"#991b1b", cardBg:"#fff1f2", borderColor:"#fecdd3", iconBg:"#ffe4e6", iconColor:"#be123c", titleColor:"#9f1239" };
}

// ============================================================
// GAUGE POPUP
// ============================================================
const popupData = {
    NOx:  { title:'NOx / VOC', subtitle:'Gas Iritan',        icon:'fa-wind',       iconColor:'#22c55e', value:'--', unit:'ppm',    status:'--', statusClass:'status-good',     needlePct:0, avgTitle:'NOx Average',  avgUnit:'ppm',    avg1:'--', avg8:'--', avg12:'--' },
    pm25: { title:'PM 2.5',    subtitle:'Partikel Halus',    icon:'fa-smog',       iconColor:'#22c55e', value:'--', unit:'µg/m³',  status:'--', statusClass:'status-good',     needlePct:0, avgTitle:'PM2.5 Average',avgUnit:'µg/m³',  avg1:'--', avg8:'--', avg12:'--' },
    pm10: { title:'PM 10',     subtitle:'Partikel Kasar',    icon:'fa-circle-dot', iconColor:'#f59e0b', value:'--', unit:'µg/m³',  status:'--', statusClass:'status-moderate', needlePct:0, avgTitle:'PM10 Average', avgUnit:'µg/m³',  avg1:'--', avg8:'--', avg12:'--' },
    aqi:  { title:'AQI',       subtitle:'Air Quality Index', icon:'fa-gauge-high', iconColor:'#f59e0b', value:'--', unit:'AQI',    status:'--', statusClass:'status-moderate', needlePct:0, avgTitle:'AQI Average',  avgUnit:'index',  avg1:'--', avg8:'--', avg12:'--' }
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
    icon.className   = 'fa-solid ' + d.icon;
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
// UNIFIED CHART — COLOR HELPERS
// ============================================================
const RWH_COLOR = {
    aqi:      v => v<=50?'#22c55e':v<=100?'#eab308':v<=150?'#f97316':v<=200?'#ef4444':v<=300?'#a855f7':'#7f1d1d',
    pm25:     v => v<=12?'#22c55e':v<=35?'#eab308':v<=55?'#f97316':v<=150?'#ef4444':'#7f1d1d',
    pm10:     v => v<=54?'#22c55e':v<=154?'#eab308':v<=254?'#f97316':v<=354?'#ef4444':'#7f1d1d',
    nox:      v => v<=50?'#22c55e':v<=100?'#eab308':v<=199?'#f97316':v<=299?'#ef4444':'#7f1d1d',
    temp:     v => v<=15?'#60a5fa':v<=22?'#22c55e':v<=28?'#eab308':v<=35?'#f97316':'#ef4444',
    humidity: v => v<=25?'#ef4444':v<=39?'#f97316':v<=60?'#22c55e':v<=75?'#eab308':'#a855f7',
};

const RWH_UNIT = { aqi:'AQI', pm25:'µg/m³', pm10:'µg/m³', nox:'ppm', temp:'°C', humidity:'%' };

const RWH_DESC = {
    aqi:      v => v<=50?'Baik':v<=100?'Sedang':v<=150?'Tidak sehat bagi kelompok sensitif':v<=200?'Tidak sehat':v<=300?'Sangat tidak sehat':'Berbahaya',
    pm25:     v => v<=12?'Baik':v<=35?'Sedang':v<=55?'Tidak sehat sensitif':v<=150?'Tidak sehat':'Berbahaya',
    pm10:     v => v<=54?'Baik':v<=154?'Sedang':v<=254?'Tidak sehat sensitif':'Tidak sehat',
    nox:      v => v<=50?'Baik':v<=100?'Sedang':v<=199?'Tidak sehat sensitif':'Tidak sehat',
    temp:     v => v<=15?'Sangat dingin':v<=22?'Sejuk':v<=28?'Normal':v<=35?'Panas':'Sangat panas',
    humidity: v => v<=25?'Sangat kering':v<=39?'Kering':v<=60?'Optimal':v<=75?'Lembap':'Sangat lembap',
};

const RWH_TAB_LABEL = { jam:'per jam', hari:'per hari', bulan:'bulanan' };

let rwhTab   = 'jam';
let rwhChart = null;

// ============================================================
// UNIFIED CHART — AMBIL DATASET
// ============================================================
function rwhGetDataset(tabKey, metricKey) {
    const raw = tabKey === 'jam'  ? hourlyRaw
              : tabKey === 'hari' ? dailyRaw
              : monthlyRaw;

    const labels = raw.map(item => {
        if (tabKey === 'jam')  return item.time   || item.jam      || '';
        if (tabKey === 'hari') return item.date   || item.tanggal  || '';
        return item.month || item.bulan || item.date || '';
    });

    const values = raw.map(item => {
        if (metricKey === 'nox') return parseFloat(item.nox ?? item.gas ?? 0) || 0;
        return parseFloat(item[metricKey]) || 0;
    });

    return { labels, values };
}

// ============================================================
// UNIFIED CHART — UPDATE HIGHLIGHT
// ============================================================
function rwhUpdateInfo(label, value, metricKey) {
    const color = RWH_COLOR[metricKey](value);
    document.getElementById('rwh-dot').style.background = color;
    document.getElementById('rwh-val').textContent      = value + ' ' + RWH_UNIT[metricKey];
    document.getElementById('rwh-desc').textContent     = RWH_DESC[metricKey](value);
    document.getElementById('rwh-meta').textContent     = label + ' · ' + RWH_TAB_LABEL[rwhTab];
}

// ============================================================
// UNIFIED CHART — RENDER
// ============================================================
function rwhRender() {
    const metric = document.getElementById('rwh-metric').value;
    const { labels, values } = rwhGetDataset(rwhTab, metric);

    if (!labels.length) {
        if (rwhChart) { rwhChart.destroy(); rwhChart = null; }
        document.getElementById('rwh-val').textContent  = '--';
        document.getElementById('rwh-desc').textContent = '';
        document.getElementById('rwh-meta').textContent = 'Tidak ada data';
        return;
    }

    const bgColors = values.map(v => RWH_COLOR[metric](v));

    // Highlight data terbaru
    rwhUpdateInfo(labels[labels.length - 1], values[values.length - 1], metric);

    if (rwhChart) rwhChart.destroy();

    rwhChart = new Chart(document.getElementById('rwhChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: bgColors,
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.parsed.y + ' ' + RWH_UNIT[metric]
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks: {
                        font:          { size: 11 },
                        color:         '#94a3b8',
                        maxRotation:   0,
                        autoSkip:      true,
                        maxTicksLimit: rwhTab === 'jam' ? 8 : 12
                    }
                },
                y: {
                    grid:        { color: 'rgba(148,163,184,0.12)' },
                    border:      { display: false },
                    beginAtZero: true,
                    ticks: { font: { size: 11 }, color: '#94a3b8' }
                }
            },
            onHover: (evt, els) => {
                if (els.length > 0) {
                    const i = els[0].index;
                    rwhUpdateInfo(labels[i], values[i], metric);
                }
            },
            onClick: (evt, els) => {
                if (els.length > 0) {
                    const i = els[0].index;
                    rwhUpdateInfo(labels[i], values[i], metric);
                }
            }
        }
    });
}

// ============================================================
// UNIFIED CHART — SET TAB
// ============================================================
function rwhSetTab(tabKey, btnEl) {
    rwhTab = tabKey;
    document.querySelectorAll('.rwh-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
    rwhRender();
}

// ============================================================
// AQI INFO POPUP
// ============================================================
const aqiData = {
    good: {
        title:'Good — Udara Baik', range:'AQI 0–50 · PM2.5 < 12 µg/m³',
        color:'#22c55e', bg:'#dcfce7',
        desc:'Kualitas udara dalam ruangan sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak.',
        pm:'PM2.5 < 12 µg/m³', pmBg:'#dcfce7', pmColor:'#15803d',
        dos:['Lakukan aktivitas normal di dalam ruangan','Biarkan ventilasi alami terbuka','Matikan air purifier untuk hemat energi','Catat kondisi ini sebagai baseline sensor'],
        donts:['Jangan abaikan pemeliharaan ventilasi rutin','Jangan biarkan sumber polutan baru masuk','Jangan memasak tanpa exhaust fan aktif']
    },
    moderate: {
        title:'Moderate — Udara Sedang', range:'AQI 51–100 · PM2.5 12–35 µg/m³',
        color:'#eab308', bg:'#fef9c3',
        desc:'Kualitas udara cukup dapat diterima namun mulai menurun. Penderita asma yang sangat sensitif mungkin mengalami gejala ringan.',
        pm:'PM2.5 12–35 µg/m³', pmBg:'#fef9c3', pmColor:'#a16207',
        dos:['Nyalakan air purifier di mode rendah','Buka jendela bila udara luar lebih baik','Periksa filter AC dan bersihkan jika kotor','Batasi aktivitas memasak yang menghasilkan asap'],
        donts:['Jangan bakar dupa atau lilin aromaterapi','Jangan semprotkan pengharum ruangan aerosol','Jangan biarkan penderita asma beraktivitas berat']
    },
    sensitive: {
        title:'Sensitive Groups — Tidak Sehat (Sensitif)', range:'AQI 101–150 · PM2.5 35–55 µg/m³',
        color:'#f97316', bg:'#ffedd5',
        desc:'Udara berbahaya bagi kelompok sensitif. Penderita asma berisiko mengalami gangguan pernapasan.',
        pm:'PM2.5 35–55 µg/m³', pmBg:'#ffedd5', pmColor:'#c2410c',
        dos:['Nyalakan air purifier di mode tinggi','Segera cari sumber polutan','Anjurkan penderita asma siapkan inhaler','Tingkatkan ventilasi'],
        donts:['Jangan izinkan penderita asma beraktivitas fisik','Jangan memasak dengan bahan yang berasap','Jangan biarkan anak-anak dan lansia di ruangan lama']
    },
    unhealthy: {
        title:'Unhealthy — Tidak Sehat', range:'AQI 151–200 · PM2.5 55–150 µg/m³',
        color:'#ef4444', bg:'#fee2e2',
        desc:'Seluruh penghuni ruangan mulai berisiko terkena dampak kesehatan. Tindakan perbaikan harus segera dilakukan.',
        pm:'PM2.5 55–150 µg/m³', pmBg:'#fee2e2', pmColor:'#b91c1c',
        dos:['Evakuasi penderita asma ke ruangan lain','Nyalakan semua air purifier ke mode maksimal','Hubungi teknisi untuk cek sistem ventilasi','Gunakan masker N95'],
        donts:['Jangan tinggalkan penderita asma tanpa pengawasan','Jangan matikan air purifier','Jangan tunda perbaikan sumber masalah']
    },
    'very-unhealthy': {
        title:'Very Unhealthy — Sangat Tidak Sehat', range:'AQI 201–300 · PM2.5 150–250 µg/m³',
        color:'#a855f7', bg:'#f3e8ff',
        desc:'Kondisi darurat kesehatan di dalam ruangan. Semua orang berisiko mengalami efek serius.',
        pm:'PM2.5 150–250 µg/m³', pmBg:'#f3e8ff', pmColor:'#7e22ce',
        dos:['Segera evakuasi semua penghuni','Hubungi pihak terkait jika dicurigai kebocoran','Gunakan masker N95/respirator','Matikan semua sumber api'],
        donts:['Jangan tunda evakuasi','Jangan abaikan tanda-tanda sumber bahaya','Jangan masuk kembali sebelum udara diperbaiki']
    },
    hazardous: {
        title:'Hazardous — Berbahaya', range:'AQI 301–500 · PM2.5 > 250 µg/m³',
        color:'#7f1d1d', bg:'#fecaca',
        desc:'Kondisi udara sangat kritis dan mengancam jiwa. Semua orang harus segera keluar.',
        pm:'PM2.5 > 250 µg/m³', pmBg:'#fecaca', pmColor:'#7f1d1d',
        dos:['Evakuasi segera seluruh penghuni','Hubungi 119 jika ada indikasi kebakaran','Tutup pintu dan jendela untuk isolasi','Gunakan jalur evakuasi yang aman'],
        donts:['Jangan masuk kembali dalam kondisi apapun','Jangan coba atasi sendiri tanpa alat proteksi','Jangan gunakan lift — gunakan tangga darurat']
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
    pm.textContent      = d.pm;
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
rwhRender();

// ============================================================
// TIMELINE SCROLLABLE PER JAM
// ============================================================

// Data forecast dari PHP (ganti sesuai controller-mu)
const forecastHourly = <?= isset($forecastHourly) ? json_encode($forecastHourly) : '[]' ?>;

function getAqiColorTimeline(v) {
    if (v <= 50)  return '#22c55e';
    if (v <= 100) return '#eab308';
    if (v <= 150) return '#f97316';
    if (v <= 200) return '#ef4444';
    if (v <= 300) return '#a855f7';
    return '#7f1d1d';
}

function getAqiLabelShort(v) {
    if (v <= 50)  return 'Good';
    if (v <= 100) return 'Moderate';
    if (v <= 150) return 'Sensitive';
    if (v <= 200) return 'Unhealthy';
    if (v <= 300) return 'Very Un.';
    return 'Hazard';
}

function renderTimeline() {
    const container = document.getElementById('fc-timeline');
    if (!container) return;

    const data   = forecastHourly.length > 0 ? forecastHourly : generateDummyForecast();
    const maxVal = Math.max(...data.map(d => d.aqi));
    const BAR_MAX_HEIGHT = 52;
    const nowHour = new Date().getHours();

    container.innerHTML = '';

    // Update badge jam sekarang
    const nowItem  = data[0];
    const nowBadge = document.getElementById('timeline-now-badge');
    if (nowBadge && nowItem) {
        const nowColor = getAqiColorTimeline(parseFloat(nowItem.aqi));
        nowBadge.textContent      = nowItem.aqi + ' AQI sekarang';
        nowBadge.style.color      = nowColor;
        nowBadge.style.borderColor = nowColor + '55';
    }

    data.forEach((item, i) => {
        const aqi      = parseFloat(item.aqi) || 0;
        const jam      = item.time || item.jam || formatHour(i);
        const color    = getAqiColorTimeline(aqi);
        const barH     = Math.max(4, Math.round((aqi / Math.max(maxVal, 200)) * BAR_MAX_HEIGHT));
        const itemHour = parseInt(jam.split(':')[0]);
        const isNow    = itemHour === nowHour && i < 24;

        const el = document.createElement('div');
        el.className = 'fc-hour' + (isNow ? ' active is-now' : '');
        if (isNow) {
            el.style.borderColor = color;
            el.style.color       = color;
        }

        el.innerHTML = `
            <span class="fc-h-time" style="${isNow ? 'color:' + color + ';font-weight:700;' : ''}">${jam}</span>
            <div class="fc-h-bar-wrap">
                <div class="fc-h-bar" style="height:${barH}px;background:${color};"></div>
            </div>
            <span class="fc-h-val" style="color:${color};">${aqi}</span>
            <span class="fc-h-label">${getAqiLabelShort(aqi)}</span>
        `;

        el.addEventListener('click', () => onTimelineClick(el, item, color, data));
        container.appendChild(el);
    });

    const activeEl = container.querySelector('.fc-hour.active');
    if (activeEl) {
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
}

function onTimelineClick(clickedEl, item, color, data) {
    // Reset semua kartu
    document.querySelectorAll('.fc-hour').forEach(x => {
        x.classList.remove('active');
        if (!x.classList.contains('is-now')) {
            x.style.borderColor = '';
            x.style.color       = '';
        }
    });

    // Aktifkan yang diklik
    clickedEl.classList.add('active');
    clickedEl.style.borderColor = color;
    clickedEl.style.color       = color;

    const aqi  = parseFloat(item.aqi) || 0;
    const jam  = item.time || item.jam || '--';

    const rwhVal  = document.getElementById('rwh-val');
    const rwhMeta = document.getElementById('rwh-meta');

    if (rwhVal)  rwhVal.textContent  = aqi + ' AQI';
    if (rwhMeta) rwhMeta.textContent = jam + ' · forecast';
}

function formatHour(index) {
    const h = index % 24;
    return (h < 10 ? '0' : '') + h + ':00';
}

// Dummy data jika belum ada endpoint forecast
function generateDummyForecast() {
    const baseAqi = [42,38,35,33,36,44,58,72,88,103,118,128,135,142,138,126,110,95,82,68,57,48,44,40];
    const now     = new Date().getHours();
    return baseAqi.map((aqi, i) => ({
        aqi,
        time: formatHour((now + i) % 24)
    }));
}

// Init
renderTimeline();
</script>

<?= $this->endSection() ?>
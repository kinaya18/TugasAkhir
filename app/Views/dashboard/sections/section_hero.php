<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/section_hero.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/base.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/dashboard.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/dashboard/responsive.css') ?>">

<!-- ===================== SECTION 1: DASHBOARD ===================== -->
<div class="dashboard-wrapper">

    <!-- LEFT COLUMN -->
    <div class="dash-left">

        <!-- HERO CARD -->
        <div class="hero-card" id="hero-card">
            <div class="hero-badge-row">
                <span class="hero-status" id="hero-status-badge">--</span>
                <div class="hero-badge-right" style="display:none;">
                </div>
            </div>

            <div class="hero-aqi-block">
                <h1 class="hero-temp" id="hero-aqhi-value">--</h1>
                <div class="hero-aqi-info">
                    <p class="hero-desc" id="hero-aqhi-desc">Air Quality Health Index</p>
                    <div class="hero-pollutant-row">
                        <span id="hero-pm25-value">-- µg/m³ <b>PM2.5</b></span>
                        <span id="hero-no2-value">-- ppb <b>NO₂</b></span>
                        <span id="hero-o3-value">-- ppb <b>O₃</b></span>
                    </div>
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

            <!-- SCALE BAR (skala AQHI 1–10+) -->
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
                    <span>1</span><span>3</span><span>5</span>
                    <span>7</span><span>10</span><span>10+</span>
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
                <p class="gauge-label">AQI</p>
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

            <div class="gauge-card" onclick="openPopup('pm1')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-pm1"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-pm1">--</span>
                        <span class="gauge-unit">µg/m³</span>
                    </div>
                </div>
                <p class="gauge-label">PM 1</p>
            </div>

            <div class="gauge-card" onclick="openPopup('polutan')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-polutan"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-polutan">--</span>
                        <span class="gauge-unit">ppm</span>
                    </div>
                </div>
                <p class="gauge-label">Polutan</p>
            </div>

        </div>

    </div>

</div>

<script>
// ============================================================
// AQI HELPERS (global, dipakai gauge & popup)
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

function getPm25Status(pm25) {
    if (pm25 <= 12) return 'GOOD';
    if (pm25 <= 35.4) return 'MODERATE';
    if (pm25 <= 55.4) return 'SENSITIVE';
    if (pm25 <= 150.4) return 'UNHEALTHY';
    if (pm25 <= 250.4) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

function getPm10Status(pm10) {
    if (pm10 <= 54) return 'GOOD';
    if (pm10 <= 154) return 'MODERATE';
    if (pm10 <= 254) return 'SENSITIVE';
    if (pm10 <= 354) return 'UNHEALTHY';
    if (pm10 <= 424) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

function getPm1Status(pm1) {
    if (pm1 <= 10) return 'GOOD';
    if (pm1 <= 25) return 'MODERATE';
    if (pm1 <= 50) return 'SENSITIVE';
    if (pm1 <= 100) return 'UNHEALTHY';
    if (pm1 <= 200) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

function getNeedlePosition(status) {

    switch(status) {

        case 'GOOD':
            return 8;

        case 'MODERATE':
            return 25;

        case 'SENSITIVE':
            return 42;

        case 'UNHEALTHY':
            return 58;

        case 'VERY UNHEALTHY':
            return 75;

        case 'HAZARDOUS':
            return 92;

        default:
            return 0;
    }
}

function getStatusClass(status) {

    switch(status) {
        case 'GOOD':
            return 'status-good';

        case 'MODERATE':
            return 'status-moderate';

        case 'SENSITIVE':
            return 'status-poor';

        case 'UNHEALTHY':
            return 'status-unhealthy';

        case 'VERY UNHEALTHY':
            return 'status-severe';

        case 'HAZARDOUS':
            return 'status-hazardous';

        default:
            return 'status-good';
    }
}


function getAqhiLabel(aqhi) {
    if (aqhi <= 3)  return 'Low';
    if (aqhi <= 6)  return 'Moderate';
    if (aqhi <= 10) return 'High';
    return 'Very High';
}

function getAqhiColor(aqhi) {
    if (aqhi <= 3)  return '#3b82f6';
    if (aqhi <= 6)  return '#eab308';
    if (aqhi <= 10) return '#ef4444';
    return '#7f1d1d';
}

function getPm25Color(pm25) {
    if (pm25 <= 12) return '#22c55e';
    if (pm25 <= 35.4) return '#eab308';
    if (pm25 <= 55.4) return '#f97316';
    if (pm25 <= 150.4) return '#ef4444';
    if (pm25 <= 250.4) return '#a855f7';
    return '#7f1d1d';
}

function getPm10Color(pm10) {
    if (pm10 <= 54) return '#22c55e';
    if (pm10 <= 154) return '#eab308';
    if (pm10 <= 254) return '#f97316';
    if (pm10 <= 354) return '#ef4444';
    if (pm10 <= 424) return '#a855f7';
    return '#7f1d1d';
}

function getPm1Color(pm1) {
    if (pm1 <= 10) return '#22c55e';
    if (pm1 <= 25) return '#eab308';
    if (pm1 <= 50) return '#f97316';
    if (pm1 <= 100) return '#ef4444';
    if (pm1 <= 200) return '#a855f7';
    return '#7f1d1d';
}

// ============================================================
// GAUGE
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
// HEALTH RECOMMENDATIONS (berdasarkan AQHI)
// ============================================================
function getHealthRecommendations(aqhi) {

    if (aqhi <= 3)
        return {
            items: [
                'Aktivitas normal dapat dilakukan',
                'Kualitas udara relatif aman bagi penderita asma',
                'Tidak diperlukan tindakan khusus',
                'Lanjutkan pemantauan kualitas udara secara berkala'
            ]
        };

    if (aqhi <= 6)
        return {
            items: [
                'Aktivitas normal masih dapat dilakukan',
                'Penderita asma perlu memperhatikan gejala pernapasan',
                'Kurangi aktivitas berat jika muncul gejala',
                'Lanjutkan pemantauan kualitas udara'
            ]
        };

    if (aqhi <= 10)
        return {
            items: [
                'Kurangi atau jadwalkan ulang aktivitas berat',
                'Penderita asma disarankan menyiapkan inhaler',
                'Kurangi paparan terhadap sumber polusi',
                'Tingkatkan ventilasi atau filtrasi udara'
            ]
        };

    return {
        items: [
            'Hindari aktivitas berat yang dapat memicu gejala asma',
            'Tetap berada di area dengan kualitas udara lebih baik',
            'Gunakan sistem filtrasi atau pemurni udara jika tersedia',
            'Ikuti rencana penanganan asma dan cari bantuan medis jika gejala memburuk'
        ]
    };
}

// ============================================================
// ASTHMA RISK (berdasarkan AQHI 1–11)
// ============================================================
function getAsthmaRisk(aqhi) {

    if (aqhi <= 3)
        return {
            title:'Risiko Asma',
            badgeLabel:'RENDAH',
            color:'#3b82f6',
            cardBg:'#eff6ff',
            borderColor:'#93c5fd',
            iconBg:'#dbeafe',
            iconColor:'#2563eb',
            titleColor:'#1d4ed8'
        };

    if (aqhi <= 6)
        return {
            title:'Risiko Asma',
            badgeLabel:'WASPADA',
            color:'#eab308',
            cardBg:'#fefce8',
            borderColor:'#fde047',
            iconBg:'#fef9c3',
            iconColor:'#ca8a04',
            titleColor:'#a16207'
        };

    if (aqhi <= 10)
        return {
            title:'Risiko Asma',
            badgeLabel:'TINGGI',
            color:'#ef4444',
            cardBg:'#fef2f2',
            borderColor:'#f87171',
            iconBg:'#fee2e2',
            iconColor:'#dc2626',
            titleColor:'#b91c1c'
        };

    return {
        title:'Risiko Asma',
        badgeLabel:'SANGAT TINGGI',
        color:'#7f1d1d',
        cardBg:'#450a0a',
        borderColor:'#991b1b',
        iconBg:'#7f1d1d',
        iconColor:'#fecaca',
        titleColor:'#ffffff'
    };
}

// ============================================================
// RENDER DASHBOARD
// ============================================================
function renderDashboard() {
    const d = window.DASH.latestData;
    if (!d) return;

    const aqi        = parseFloat(d.aqi)        || 0;
    const pm25       = parseFloat(d.pm25)       || 0;
    const pm10       = parseFloat(d.pm10)       || 0;
    const pm1        = parseFloat(d.pm1)        || 0;
    const polutan    = parseFloat(d.polutan)    || 0;
    const suhu       = parseFloat(d.suhu)       || 0;
    const kelembaban = parseFloat(d.kelembaban) || 0;
    const no2        = parseFloat(d.no2)        || 0;  // ppb
    const o3         = parseFloat(d.o3)         || 0;  // ppb

    // Hitung AQHI dari NO2, O3, PM2.5
    const aqhi = parseFloat(d.aqhi) || 1;

    console.log('AQHI dari PHP:', d.aqhi);

    const aqhiColor = getAqhiColor(aqhi);
    const aqhiLabel = getAqhiLabel(aqhi);

    // =====================================
// HERO CARD COLOR
// =====================================

const heroCard = document.getElementById('hero-card');

heroCard.classList.remove(
    'hero-low',
    'hero-moderate',
    'hero-high',
    'hero-very-high'
);

if (aqhi <= 3) {
    heroCard.classList.add('hero-low');
}
else if (aqhi <= 6) {
    heroCard.classList.add('hero-moderate');
}
else if (aqhi <= 10) {
    heroCard.classList.add('hero-high');
}
else {
    heroCard.classList.add('hero-very-high');
}

    // AQI tetap dipakai untuk gauge
    const aqiColor = getAqiColor(aqi);
    const aqiLabel = getAqiLabel(aqi);

    // ---- Hero: tampilkan AQHI ----
    document.getElementById('hero-aqhi-value').textContent = aqhi;
    document.getElementById('hero-aqhi-desc').textContent  = 'Air Quality Health Index · ' + aqhiLabel;
    document.getElementById('hero-temp').textContent       = suhu;
    document.getElementById('hero-humidity').textContent   = kelembaban;
    document.getElementById('hero-pm25-value').innerHTML = pm25 + ' µg/m³ <b>PM2.5</b>';
    document.getElementById('hero-no2-value').innerHTML =
        no2 + ' ppm <b>NO₂</b>';

    document.getElementById('hero-o3-value').innerHTML =
        o3 + ' ppm <b>O₃</b>';

    // Badge status berdasarkan AQHI
    const heroBadge = document.getElementById('hero-status-badge');
    heroBadge.textContent      = aqhiLabel.toUpperCase();
    heroBadge.style.background = aqhiColor;

    // Needle scale bar AQHI 1–11
    const needlePct = Math.min(((aqhi - 1) / 10) * 100, 100);
    document.getElementById('hero-scale-needle').style.left = needlePct + '%';

    // ---- Gauge values (tetap AQI) ----
    document.getElementById('val-polutan').textContent  = polutan;
    document.getElementById('val-pm25').textContent = pm25;
    document.getElementById('val-pm10').textContent = pm10;
    document.getElementById('val-pm1').textContent = pm1;
    document.getElementById('val-aqi').textContent  = aqi;

    createGauge('gauge-polutan',  polutan,  300, getAqiColor(polutan));
    createGauge('gauge-pm25', pm25, 300, getPm25Color(pm25));
    createGauge('gauge-pm10', pm10, 300, getPm10Color(pm10));
    createGauge('gauge-pm1', pm1, 300, getPm1Color(pm1));
    createGauge('gauge-aqi',  aqi,  300, aqiColor);

    // ---- Asthma risk berdasarkan AQHI ----
    const risk = getAsthmaRisk(aqhi);
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

    // ---- Health recommendations berdasarkan AQHI ----
    const listContainer = document.getElementById('health-list');
    listContainer.innerHTML = getHealthRecommendations(aqhi).items
        .map(item => `<div class="health-item"><i class="fa-solid fa-circle-check"></i><span>${item}</span></div>`)
        .join('');

    popupData.pm25.value = pm25;
    popupData.pm25.status = getPm25Status(pm25);

    popupData.pm10.value = pm10;
    popupData.pm10.status = getPm10Status(pm10);

    popupData.pm1.value = pm1;
    popupData.pm1.status = getPm1Status(pm1);

    popupData.aqi.value = aqi;
    popupData.aqi.status = getAqiLabel(aqi).toUpperCase();

    popupData.polutan.value = polutan;
    popupData.polutan.status = getAqiLabel(polutan).toUpperCase();
}

renderDashboard();

async function refreshRealtimeData() {

    try {

        const response = await fetch('/latest-data');

        const data = await response.json();

        if (data && Object.keys(data).length > 0) {

            window.DASH.latestData = data;

            renderDashboard();
        }

    } catch (error) {

        console.error('Realtime Error:', error);
    }
}

// cek data baru tiap 5 detik
setInterval(refreshRealtimeData, 5000);

</script>
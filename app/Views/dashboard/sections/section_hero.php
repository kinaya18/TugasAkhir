<!-- ===================== SECTION 1: DASHBOARD ===================== -->
<div class="dashboard-wrapper">

    <!-- LEFT COLUMN -->
    <div class="dash-left">

        <!-- HERO CARD -->
        <div class="hero-card">
            <div class="hero-badge-row">
                <span class="hero-status" id="hero-status-badge">--</span>
                <div class="hero-badge-right">
                    <i class="fa-solid fa-location-dot hero-loc-icon"></i>
                    <span class="hero-loc-text" id="hero-location">Ruang Utama</span>
                    <span class="hero-time-label">· just now</span>
                </div>
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

<script>
// ============================================================
// AQI HELPERS (global, dipakai semua section)
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
// HEALTH RECOMMENDATIONS
// ============================================================
function getHealthRecommendations(aqi) {
    if (aqi <= 50)  return { items: ['Lakukan aktivitas normal di dalam ruangan','Biarkan ventilasi alami terbuka','Tidak perlu air purifier','Bersihkan debu ringan secara rutin'] };
    if (aqi <= 100) return { items: ['Nyalakan air purifier di mode rendah','Buka jendela bila udara luar lebih baik','Hindari asap rokok & polusi indoor','Periksa filter AC dan bersihkan jika kotor'] };
    if (aqi <= 150) return { items: ['Tingkatkan ventilasi atau buka semua jendela','Gunakan air purifier di mode tinggi','Hindari asap & pengharum berbakar','Penderita asma siapkan inhaler'] };
    if (aqi <= 200) return { items: ['Evakuasi penderita asma ke ruangan lain','Air purifier mode maksimal','Kurangi aktivitas dalam ruangan','Gunakan masker N95 jika harus berada di ruangan'] };
    if (aqi <= 300) return { items: ['Buat ruangan bersih (clean room)','Air purifier nyala terus','Gunakan masker di dalam ruangan','Segera evakuasi semua penghuni ruangan'] };
    return             { items: ['Evakuasi segera seluruh penghuni ruangan','Gunakan air purifier maksimal','Tutup semua celah udara','Siapkan tindakan darurat asma'] };
}

// ============================================================
// ASTHMA RISK
// ============================================================
function getAsthmaRisk(aqi) {
    if (aqi <= 50)  return { title:'Risiko Asma', badgeLabel:'RENDAH',        color:'#22c55e', cardBg:'#f0fdf4', borderColor:'#bbf7d0', iconBg:'#dcfce7', iconColor:'#16a34a', titleColor:'#15803d' };
    if (aqi <= 100) return { title:'Risiko Asma', badgeLabel:'WASPADA',       color:'#f59e0b', cardBg:'#fffbeb', borderColor:'#fde68a', iconBg:'#fef3c7', iconColor:'#d97706', titleColor:'#b45309' };
    if (aqi <= 150) return { title:'Risiko Asma', badgeLabel:'TINGGI',        color:'#f97316', cardBg:'#fff7ed', borderColor:'#fed7aa', iconBg:'#ffedd5', iconColor:'#ea580c', titleColor:'#c2410c' };
    if (aqi <= 200) return { title:'Risiko Asma', badgeLabel:'SANGAT TINGGI', color:'#ef4444', cardBg:'#fef2f2', borderColor:'#fecaca', iconBg:'#fee2e2', iconColor:'#dc2626', titleColor:'#b91c1c' };
    if (aqi <= 300) return { title:'Risiko Asma', badgeLabel:'KRITIS',        color:'#a855f7', cardBg:'#faf5ff', borderColor:'#e9d5ff', iconBg:'#f3e8ff', iconColor:'#9333ea', titleColor:'#7e22ce' };
    return             { title:'Risiko Asma', badgeLabel:'DARURAT',       color:'#991b1b', cardBg:'#fff1f2', borderColor:'#fecdd3', iconBg:'#ffe4e6', iconColor:'#be123c', titleColor:'#9f1239' };
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
    const nox        = parseFloat(d.nox)        || 0;
    const suhu       = parseFloat(d.suhu)       || 0;
    const kelembaban = parseFloat(d.kelembaban) || 0;

    const aqiColor = getAqiColor(aqi);
    const aqiLabel = getAqiLabel(aqi);

    // Hero
    document.getElementById('hero-aqi-value').textContent  = aqi;
    document.getElementById('hero-aqi-desc').textContent   = 'Air Quality · ' + aqiLabel;
    document.getElementById('hero-temp').textContent       = suhu;
    document.getElementById('hero-humidity').textContent   = kelembaban;
    document.getElementById('hero-pm25-value').textContent = pm25 + ' µg/m³ PM2.5';
    document.getElementById('hero-location').textContent   = d.location ?? 'Ruang Utama';

    const heroBadge = document.getElementById('hero-status-badge');
    heroBadge.textContent      = aqiLabel.toUpperCase();
    heroBadge.style.background = aqiColor;

    const needlePct = Math.min((aqi / 500) * 100, 100);
    document.getElementById('hero-scale-needle').style.left = needlePct + '%';

    // Gauge values
    document.getElementById('val-NOx').textContent  = nox;
    document.getElementById('val-pm25').textContent = pm25;
    document.getElementById('val-pm10').textContent = pm10;
    document.getElementById('val-aqi').textContent  = aqi;

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
    const listContainer = document.getElementById('health-list');
    listContainer.innerHTML = getHealthRecommendations(aqi).items
        .map(item => `<div class="health-item"><i class="fa-solid fa-circle-check"></i><span>${item}</span></div>`)
        .join('');

    // Sync popup data (didefinisikan di _partial_gauge_popup.php)
    const vals = { NOx: nox, pm25, pm10, aqi };
    Object.keys(vals).forEach(k => {
        popupData[k].value       = vals[k];
        popupData[k].status      = getAqiLabel(vals[k]).toUpperCase();
        popupData[k].statusClass = getAqiStatusClass(vals[k]);
        popupData[k].needlePct   = Math.min((vals[k] / 510) * 100, 100);
    });
}

renderDashboard();
</script>
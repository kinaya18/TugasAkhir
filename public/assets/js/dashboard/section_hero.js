// =============================================================
//  DASHBOARD JAVASCRIPT — Air Quality Health Index (AQHI)
//  Berisi:
//    1. Helper warna & label (AQI, AQHI, PM, Polutan)
//    2. Manajemen Gauge Chart (Chart.js)
//    3. Logika risiko asma & rekomendasi kesehatan
//    4. Render dashboard (renderDashboard)
//    5. Polling data real-time (refreshRealtimeData)
// =============================================================


// =============================================================
// 1. HELPER FUNGSI — AQI
//    AQI (Air Quality Index): indeks kualitas udara standar EPA
// =============================================================

/**
 * Mengembalikan warna berdasarkan nilai AQI.
 * Dipakai untuk mewarnai gauge dan elemen status.
 */
function getAqiColor(aqi) {
    if (aqi <= 50)  return '#22c55e'; // Hijau   — Good
    if (aqi <= 100) return '#f59e0b'; // Kuning  — Moderate
    if (aqi <= 150) return '#f97316'; // Oranye  — Sensitive
    if (aqi <= 200) return '#ef4444'; // Merah   — Unhealthy
    if (aqi <= 300) return '#a855f7'; // Ungu    — Very Unhealthy
    return '#7f1d1d';                 // Maroon  — Hazardous
}

/**
 * Mengembalikan label teks kategori AQI.
 */
function getAqiLabel(aqi) {
    if (aqi <= 50)  return 'Good';
    if (aqi <= 100) return 'Moderate';
    if (aqi <= 150) return 'Sensitive';
    if (aqi <= 200) return 'Unhealthy';
    if (aqi <= 300) return 'Very Unhealthy';
    return 'Hazardous';
}

/**
 * Mengembalikan label status kategori PM2.5.
 * Dipakai oleh popup gauge saat menampilkan detail sensor.
 * Ambang batas mengacu standar EPA.
 */
function getPm25Status(pm25) {
    if (pm25 <= 12)    return 'GOOD';
    if (pm25 <= 35.4)  return 'MODERATE';
    if (pm25 <= 55.4)  return 'SENSITIVE';
    if (pm25 <= 150.4) return 'UNHEALTHY';
    if (pm25 <= 250.4) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

/**
 * Mengembalikan label status kategori PM10.
 * Dipakai oleh popup gauge saat menampilkan detail sensor.
 */
function getPm10Status(pm10) {
    if (pm10 <= 54)  return 'GOOD';
    if (pm10 <= 154) return 'MODERATE';
    if (pm10 <= 254) return 'SENSITIVE';
    if (pm10 <= 354) return 'UNHEALTHY';
    if (pm10 <= 424) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

/**
 * Mengembalikan label status kategori PM1.
 * Dipakai oleh popup gauge saat menampilkan detail sensor.
 */
function getPm1Status(pm1) {
    if (pm1 <= 10)  return 'GOOD';
    if (pm1 <= 25)  return 'MODERATE';
    if (pm1 <= 50)  return 'SENSITIVE';
    if (pm1 <= 100) return 'UNHEALTHY';
    if (pm1 <= 200) return 'VERY UNHEALTHY';
    return 'HAZARDOUS';
}

/**
 * Mengembalikan posisi jarum (%) pada skala popup berdasarkan status.
 * Setiap kategori ditempatkan di tengah segmen yang sesuai.
 */
function getNeedlePosition(status) {
    switch (status) {
        case 'GOOD':          return 8;
        case 'MODERATE':      return 25;
        case 'SENSITIVE':     return 42;
        case 'UNHEALTHY':     return 58;
        case 'VERY UNHEALTHY':return 75;
        case 'HAZARDOUS':     return 92;
        default:              return 0;
    }
}

/**
 * Mengembalikan CSS class warna badge status.
 * Dipakai pada elemen popup-status-badge untuk pewarnaan dinamis.
 */
function getStatusClass(status) {
    switch (status) {
        case 'GOOD':          return 'status-good';
        case 'MODERATE':      return 'status-moderate';
        case 'SENSITIVE':     return 'status-poor';
        case 'UNHEALTHY':     return 'status-unhealthy';
        case 'VERY UNHEALTHY':return 'status-severe';
        case 'HAZARDOUS':     return 'status-hazardous';
        default:              return 'status-good';
    }
}


// =============================================================
// 2. HELPER FUNGSI — AQHI
//    AQHI (Air Quality Health Index): indeks berbasis risiko
//    kesehatan, dihitung dari PM2.5, NO₂, dan O₃.
//    Skala: 1–3 (Low), 4–6 (Moderate), 7–10 (High), 10+ (Very High)
// =============================================================

/**
 * Mengembalikan label kategori AQHI dalam Bahasa Indonesia.
 */
function getAqhiStatusLabel(aqhi) {
    if (aqhi <= 3)  return 'Sehat';
    if (aqhi <= 6)  return 'Sedang';
    if (aqhi <= 10) return 'Tidak Sehat';
    return 'Sangat Tidak Sehat';
}

/**
 * Mengembalikan label kategori AQHI dalam Bahasa Inggris.
 */
function getAqhiLabel(aqhi) {
    if (aqhi <= 3)  return 'Low';
    if (aqhi <= 6)  return 'Moderate';
    if (aqhi <= 10) return 'High';
    return 'Very High';
}

/**
 * Mengembalikan warna berdasarkan kategori AQHI.
 */
function getAqhiColor(aqhi) {
    if (aqhi <= 3)  return '#3b82f6'; // Biru   — Low
    if (aqhi <= 6)  return '#eab308'; // Kuning — Moderate
    if (aqhi <= 10) return '#ef4444'; // Merah  — High
    return '#7f1d1d';                 // Maroon — Very High
}


// =============================================================
// 3. HELPER FUNGSI — WARNA PARTIKEL (PM2.5, PM10, PM1)
//    Ambang batas mengacu standar EPA / BMKG.
// =============================================================

function getPm25Color(pm25) {
    if (pm25 <= 12)    return '#22c55e';
    if (pm25 <= 35.4)  return '#eab308';
    if (pm25 <= 55.4)  return '#f97316';
    if (pm25 <= 150.4) return '#ef4444';
    if (pm25 <= 250.4) return '#a855f7';
    return '#7f1d1d';
}

function getPm10Color(pm10) {
    if (pm10 <= 54)  return '#22c55e';
    if (pm10 <= 154) return '#eab308';
    if (pm10 <= 254) return '#f97316';
    if (pm10 <= 354) return '#ef4444';
    if (pm10 <= 424) return '#a855f7';
    return '#7f1d1d';
}

function getPm1Color(pm1) {
    if (pm1 <= 10)  return '#22c55e';
    if (pm1 <= 25)  return '#eab308';
    if (pm1 <= 50)  return '#f97316';
    if (pm1 <= 100) return '#ef4444';
    if (pm1 <= 200) return '#a855f7';
    return '#7f1d1d';
}


// =============================================================
// 4. HELPER FUNGSI — POLUTAN (CO₂ / Gas Campuran, satuan ppm)
// =============================================================

function getPolutanColor(ppm) {
    if (ppm < 800)   return '#22c55e'; // Normal
    if (ppm <= 1200) return '#eab308'; // Elevated
    if (ppm <= 1500) return '#f97316'; // High
    if (ppm <= 1999) return '#ef4444'; // Very High
    return '#7f1d1d';                  // Dangerous
}


// =============================================================
// 5. GAUGE — Chart.js Doughnut
//    Setiap sensor ditampilkan sebagai gauge lingkaran.
//    gaugeInstances menyimpan referensi Chart agar bisa di-update
//    tanpa membuat ulang canvas (lebih efisien).
// =============================================================

const gaugeInstances = {};

/**
 * Membuat gauge baru pada canvas dengan ID tertentu.
 * @param {string} id    - ID elemen canvas
 * @param {number} value - Nilai awal
 * @param {number} max   - Nilai maksimum skala
 * @param {string} color - Warna arc (hex)
 */
function createGauge(id, value, max, color) {
    const canvas = document.getElementById(id);
    if (!canvas) return;

    // Hancurkan instance lama jika ada (cegah memory leak)
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

/**
 * Memperbarui data gauge yang sudah ada (tanpa animasi ulang penuh).
 * @param {string} id    - ID elemen canvas
 * @param {number} value - Nilai baru
 * @param {number} max   - Nilai maksimum skala
 * @param {string} color - Warna arc baru
 */
function updateGauge(id, value, max, color) {
    const chart = gaugeInstances[id];
    if (!chart) return;

    chart.data.datasets[0].data = [
        Math.min(value, max),
        Math.max(max - value, 0)
    ];
    chart.data.datasets[0].backgroundColor = [color, '#e2e8f0'];
    chart.update('none'); // 'none' = skip animasi agar update cepat
}


// =============================================================
// 6. RISIKO ASMA — Berdasarkan Nilai AQHI
//    Mengembalikan konfigurasi warna & label kartu risiko asma.
// =============================================================

/**
 * Mengembalikan objek konfigurasi tampilan risiko asma.
 * @param {number} aqhi - Nilai AQHI (1–10+)
 * @returns {object} Konfigurasi warna, label, dan tema kartu
 */
function getAsthmaRisk(aqhi) {
    if (aqhi <= 3) return {
        title: 'Risiko Asma', badgeLabel: 'RENDAH',
        color: '#3b82f6', cardBg: '#eff6ff', borderColor: '#93c5fd',
        iconBg: '#dbeafe', iconColor: '#2563eb', titleColor: '#1d4ed8'
    };
    if (aqhi <= 6) return {
        title: 'Risiko Asma', badgeLabel: 'WASPADA',
        color: '#eab308', cardBg: '#fefce8', borderColor: '#fde047',
        iconBg: '#fef9c3', iconColor: '#ca8a04', titleColor: '#a16207'
    };
    if (aqhi <= 10) return {
        title: 'Risiko Asma', badgeLabel: 'TINGGI',
        color: '#ef4444', cardBg: '#fef2f2', borderColor: '#f87171',
        iconBg: '#fee2e2', iconColor: '#dc2626', titleColor: '#b91c1c'
    };
    return {
        title: 'Risiko Asma', badgeLabel: 'SANGAT TINGGI',
        color: '#7f1d1d', cardBg: '#450a0a', borderColor: '#991b1b',
        iconBg: '#7f1d1d', iconColor: '#fecaca', titleColor: '#ffffff'
    };
}


// =============================================================
// 7. REKOMENDASI KESEHATAN — Berdasarkan Nilai AQHI
// =============================================================

/**
 * Mengembalikan daftar rekomendasi kesehatan sesuai kategori AQHI.
 * @param {number} aqhi - Nilai AQHI
 * @returns {object} { items: string[] }
 */
function getHealthRecommendations(aqhi) {
    if (aqhi <= 3) return { items: [
        'Aktivitas normal dapat dilakukan',
        'Kualitas udara relatif aman bagi penderita asma',
        'Tidak diperlukan tindakan khusus',
        'Lanjutkan pemantauan kualitas udara secara berkala'
    ]};
    if (aqhi <= 6) return { items: [
        'Aktivitas normal masih dapat dilakukan',
        'Penderita asma perlu memperhatikan gejala pernapasan',
        'Kurangi aktivitas berat jika muncul gejala',
        'Lanjutkan pemantauan kualitas udara'
    ]};
    if (aqhi <= 10) return { items: [
        'Kurangi atau jadwalkan ulang aktivitas berat',
        'Penderita asma disarankan menyiapkan inhaler',
        'Kurangi paparan terhadap sumber polusi',
        'Tingkatkan ventilasi atau filtrasi udara'
    ]};
    return { items: [
        'Hindari aktivitas berat yang dapat memicu gejala asma',
        'Tetap berada di area dengan kualitas udara lebih baik',
        'Gunakan sistem filtrasi atau pemurni udara jika tersedia',
        'Ikuti rencana penanganan asma dan cari bantuan medis jika gejala memburuk'
    ]};
}


// =============================================================
// 8. RENDER DASHBOARD
//    Fungsi utama yang memperbarui seluruh tampilan dashboard
//    menggunakan data terbaru dari window.DASH.latestData.
// =============================================================

function renderDashboard() {
    const d = window.DASH.latestData;
    if (!d) return;

    // --- Parsing data sensor ---
    const aqi        = parseFloat(d.aqi)        || 0;
    const pm25       = parseFloat(d.pm25)        || 0;
    const pm10       = parseFloat(d.pm10)        || 0;
    const pm1        = parseFloat(d.pm1)         || 0;
    const polutan    = parseFloat(d.polutan)     || 0;
    const suhu       = parseFloat(d.suhu)        || 0;
    const kelembaban = parseFloat(d.kelembaban)  || 0;
    const no2        = parseFloat(d.no2)         || 0; // ppb
    const o3         = parseFloat(d.o3)          || 0; // ppb
    const aqhi       = parseFloat(d.aqhi)        || 1; // dari server PHP

    console.log('AQHI dari PHP:', aqhi);

    // --- Warna & label berdasarkan AQHI ---
    const aqhiColor = getAqhiColor(aqhi);
    const aqhiLabel = getAqhiLabel(aqhi);

    // --- Warna & label berdasarkan AQI (untuk gauge) ---
    const aqiColor  = getAqiColor(aqi);

    // ---- Update tema warna Hero Card ----
    const heroCard = document.getElementById('hero-card');
    heroCard.classList.remove('hero-low', 'hero-moderate', 'hero-high', 'hero-very-high');
    if      (aqhi <= 3)  heroCard.classList.add('hero-low');
    else if (aqhi <= 6)  heroCard.classList.add('hero-moderate');
    else if (aqhi <= 10) heroCard.classList.add('hero-high');
    else                 heroCard.classList.add('hero-very-high');

    // ---- Update nilai Hero Card ----
    document.getElementById('hero-aqhi-value').textContent = aqhi;
    document.getElementById('hero-aqhi-desc').textContent  = getAqhiStatusLabel(aqhi);
    document.getElementById('hero-temp').textContent       = suhu;
    document.getElementById('hero-humidity').textContent   = kelembaban;
    document.getElementById('hero-pm25-value').textContent = pm25;
    document.getElementById('hero-no2-value').textContent  = no2;
    document.getElementById('hero-o3-value').textContent   = o3;

    // ---- Progress bar polutan (% terhadap batas atas) ----
    const pm25Pct = Math.min((pm25 / 500) * 100, 100);
    const no2Pct  = Math.min((no2  / 2)   * 100, 100);
    const o3Pct   = Math.min((o3   / 2)   * 100, 100);

    document.getElementById('hero-pm25-bar').style.width      = pm25Pct + '%';
    document.getElementById('hero-no2-bar').style.width       = no2Pct  + '%';
    document.getElementById('hero-o3-bar').style.width        = o3Pct   + '%';
    document.getElementById('hero-pm25-bar').style.background = getPm25Color(pm25);
    document.getElementById('hero-no2-bar').style.background  = '#f59e0b';
    document.getElementById('hero-o3-bar').style.background   = '#3b82f6';

    // ---- Badge & skala AQHI ----
    document.getElementById('hero-scale-badge').textContent = 'SKALA ' + aqhi + ' (' + aqhiLabel.toUpperCase() + ')';

    const heroBadge = document.getElementById('hero-status-badge');
    heroBadge.textContent      = aqhiLabel.toUpperCase();
    heroBadge.style.background = aqhiColor;

    // Posisi jarum skala AQHI: rumus linear dari rentang 1–11
    const needlePct = Math.min(((aqhi - 1) / 10) * 100, 100);
    document.getElementById('hero-scale-needle').style.left = needlePct + '%';

    // ---- Update nilai Gauge ----
    document.getElementById('val-aqi').textContent     = aqi;
    document.getElementById('val-pm25').textContent    = pm25;
    document.getElementById('val-pm10').textContent    = pm10;
    document.getElementById('val-pm1').textContent     = pm1;
    document.getElementById('val-polutan').textContent = polutan;

    updateGauge('gauge-aqi',     aqi,     300,  aqiColor);
    updateGauge('gauge-pm25',    pm25,    300,  getPm25Color(pm25));
    updateGauge('gauge-pm10',    pm10,    300,  getPm10Color(pm10));
    updateGauge('gauge-pm1',     pm1,     300,  getPm1Color(pm1));
    updateGauge('gauge-polutan', polutan, 2500, getPolutanColor(polutan));

    // ---- Update Kartu Risiko Asma ----
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
    asthmaBadge.textContent      = risk.badgeLabel;
    asthmaBadge.style.background = risk.color;

    // ---- Update Rekomendasi Kesehatan ----
    const listContainer = document.getElementById('health-list');
    listContainer.innerHTML = getHealthRecommendations(aqhi).items
        .map(item => `
            <div class="health-item">
                <i class="fa-solid fa-circle-check"></i>
                <span>${item}</span>
            </div>`)
        .join('');
}


// =============================================================
// 9. REAL-TIME DATA POLLING
//    Mengambil data terbaru dari endpoint /latest-data setiap
//    5 detik. Dashboard hanya di-render ulang jika data berubah.
// =============================================================

async function refreshRealtimeData() {
    try {
        console.log('Mengambil data terbaru...');

        const response = await fetch('/latest-data');
        const data     = await response.json();

        console.log('Data real-time:', data);

        // Hanya render ulang jika ada perubahan data
        if (data && Object.keys(data).length > 0) {
            const dataLama = JSON.stringify(window.DASH.latestData);
            const dataBaru = JSON.stringify(data);

            if (dataLama !== dataBaru) {
                console.log('Data berubah — dashboard diperbarui');
                window.DASH.latestData = data;
                renderDashboard();
            }
        }

    } catch (error) {
        console.error('Gagal mengambil data real-time:', error);
    }
}


// =============================================================
// 10. INISIALISASI
//     Dijalankan sekali saat halaman pertama kali dimuat.
// =============================================================

// Buat gauge awal dengan nilai 0 dan warna hijau (default)
createGauge('gauge-aqi',     0, 300,  '#22c55e');
createGauge('gauge-pm25',    0, 300,  '#22c55e');
createGauge('gauge-pm10',    0, 300,  '#22c55e');
createGauge('gauge-pm1',     0, 300,  '#22c55e');
createGauge('gauge-polutan', 0, 2500, '#22c55e');

// Render awal menggunakan data yang sudah tersedia di window.DASH
renderDashboard();

// Ambil data real-time pertama kali, lalu ulangi setiap 5 detik
refreshRealtimeData();
setInterval(refreshRealtimeData, 5000);
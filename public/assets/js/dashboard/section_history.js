/* ================================================================
   section_history.js
   JavaScript untuk Section 2 – Riwayat & Grafik Kualitas Udara

   Dibagi menjadi 6 blok:
     A. Konstanta warna, satuan, deskripsi per metrik
     B. Fungsi render grafik (rwhRender, rwhSetTab, setChartType)
     C. Fungsi render timeline forecast 24 jam
     D. Fungsi render prediksi SVR 7 hari ke depan
     E. Fungsi export (Excel, CSV, PDF, Print, Copy)
     F. Fungsi kontrol tabel (toggle view, filter, pagination)

   Dependensi (harus dimuat sebelum file ini di <head>):
     - Chart.js
     - SheetJS (XLSX)
     - Font Awesome
   Dependensi dimuat dinamis (tidak perlu di <head>):
     - jsPDF + jsPDF-AutoTable (dimuat otomatis saat Export PDF)
================================================================ */

/* ================================================================
   A. KONSTANTA METRIK
   Digunakan bersama oleh grafik, timeline, dan prediksi.
================================================================ */

/**
 * Mengembalikan warna HEX berdasarkan nilai dan jenis metrik.
 * Warna mengikuti standar AQI / AQHI internasional.
 */
const RWH_COLOR = {
    aqi:      v => v <= 50  ? '#22c55e' : v <= 100 ? '#eab308' : v <= 150 ? '#f97316' : v <= 200 ? '#ef4444' : v <= 300 ? '#a855f7' : '#7f1d1d',
    aqhi:     v => v <= 3   ? '#3b82f6' : v <= 6   ? '#eab308' : v <= 10  ? '#ef4444' : '#7f1d1d',
    pm25:     v => v <= 12  ? '#22c55e' : v <= 35  ? '#eab308' : v <= 55  ? '#f97316' : v <= 150 ? '#ef4444' : '#7f1d1d',
    pm10:     v => v <= 54  ? '#22c55e' : v <= 154 ? '#eab308' : v <= 254 ? '#f97316' : v <= 354 ? '#ef4444' : '#7f1d1d',
    no2:      v => v <= 40  ? '#22c55e' : v <= 100 ? '#eab308' : v <= 200 ? '#f97316' : v <= 400 ? '#ef4444' : '#7f1d1d',
    o3:       v => v <= 50  ? '#22c55e' : v <= 100 ? '#eab308' : v <= 168 ? '#f97316' : v <= 208 ? '#ef4444' : '#7f1d1d',
    polutan:  v => v <= 50  ? '#22c55e' : v <= 100 ? '#eab308' : v <= 199 ? '#f97316' : v <= 299 ? '#ef4444' : '#7f1d1d',
    temp:     v => v <= 15  ? '#60a5fa' : v <= 22  ? '#22c55e' : v <= 28  ? '#eab308' : v <= 35  ? '#f97316' : '#ef4444',
    humidity: v => v <= 25  ? '#ef4444' : v <= 39  ? '#f97316' : v <= 60  ? '#22c55e' : v <= 75  ? '#eab308' : '#a855f7',
};

/** Satuan tampilan per metrik */
const RWH_UNIT = {
    aqi: 'AQI', aqhi: 'AQHI', pm25: 'µg/m³', pm10: 'µg/m³',
    no2: 'ppm', o3: 'ppm', polutan: 'ppm', temp: '°C', humidity: '%',
};

/** Label deskriptif berdasarkan nilai per metrik */
const RWH_DESC = {
    aqi:      v => v <= 50  ? 'Baik' : v <= 100 ? 'Sedang' : v <= 150 ? 'Tidak sehat bagi kelompok sensitif' : v <= 200 ? 'Tidak sehat' : v <= 300 ? 'Sangat tidak sehat' : 'Berbahaya',
    aqhi:     v => v <= 3   ? 'Risiko rendah'      : v <= 6  ? 'Risiko sedang'   : v <= 10 ? 'Risiko tinggi' : 'Risiko sangat tinggi',
    pm25:     v => v <= 12  ? 'Baik'               : v <= 35 ? 'Sedang'          : v <= 55 ? 'Tidak sehat sensitif' : v <= 150 ? 'Tidak sehat' : 'Berbahaya',
    pm10:     v => v <= 54  ? 'Baik'               : v <= 154? 'Sedang'          : v <= 254? 'Tidak sehat sensitif' : 'Tidak sehat',
    no2:      v => v <= 40  ? 'Baik'               : v <= 100? 'Sedang'          : v <= 200? 'Tidak sehat sensitif' : v <= 400 ? 'Tidak sehat' : 'Berbahaya',
    o3:       v => v <= 50  ? 'Baik'               : v <= 100? 'Sedang'          : v <= 168? 'Tidak sehat sensitif' : v <= 208 ? 'Tidak sehat' : 'Berbahaya',
    polutan:  v => v <= 50  ? 'Baik'               : v <= 100? 'Sedang'          : v <= 199? 'Tidak sehat sensitif' : 'Tidak sehat',
    temp:     v => v <= 15  ? 'Sangat dingin'      : v <= 22 ? 'Sejuk'           : v <= 28 ? 'Normal'         : v <= 35 ? 'Panas' : 'Sangat panas',
    humidity: v => v <= 25  ? 'Sangat kering'      : v <= 39 ? 'Kering'          : v <= 60 ? 'Optimal'        : v <= 75 ? 'Lembap' : 'Sangat lembap',
};

/** Label tab grafik untuk ditampilkan di info hover */
const RWH_TAB_LABEL = { jam: 'per jam', hari: 'per hari', bulan: 'bulanan' };


/* ================================================================
   B. GRAFIK TREN (Chart.js)
================================================================ */

let rwhTab           = 'jam';   // periode aktif: jam | hari | bulan
let rwhChart         = null;    // instance Chart.js aktif
let currentChartType = 'bar';   // tipe grafik: bar | line

/**
 * Mengisi gap jam yang hilang (alat mati) dengan nilai 0.
 *
 * Cara kerja:
 *  1. Buat peta (Map) dari jam → nilai berdasarkan data asli.
 *  2. Tentukan rentang jam: dari jam pertama hingga jam terakhir data.
 *  3. Iterasi setiap jam dalam rentang tersebut.
 *     - Jika jam ada di data → pakai nilai asli.
 *     - Jika tidak ada (gap / alat mati) → isi dengan 0.
 *
 * Contoh: data ada di 08:00 dan 14:00, maka 09:00–13:00
 * akan diisi 0 secara otomatis.
 *
 * @param {Array}  raw       - Array data mentah per jam
 * @param {string} metricKey - Nama metrik (aqhi, aqi, pm25, dll.)
 * @returns {{ labels: string[], values: number[] }}
 */
function fillHourlyGaps(raw, metricKey) {
    if (!raw || raw.length === 0) return { labels: [], values: [] };

    // Ambil label waktu tiap item (format "HH:MM" atau "HH:00")
    const getLabel = item => item.time || item.jam || '';

    // Ambil nilai numerik metrik dari satu item
    const getValue = item => {
        if (metricKey === 'polutan') return parseFloat(item.polutan ?? item.gas ?? 0) || 0;
        return parseFloat(item[metricKey]) || 0;
    };

    // Konversi label "HH:MM" → angka menit sejak 00:00 (untuk perbandingan)
    const toMinutes = label => {
        const [h, m] = label.split(':').map(Number);
        return (isNaN(h) ? 0 : h) * 60 + (isNaN(m) ? 0 : m);
    };

    // Buat peta: "HH:MM" → nilai
    const dataMap = new Map();
    raw.forEach(item => {
        const lbl = getLabel(item);
        if (lbl) dataMap.set(lbl, getValue(item));
    });

    // Tentukan jam awal dan akhir dari data
    const allLabels   = raw.map(getLabel).filter(Boolean);
    const firstMinute = Math.min(...allLabels.map(toMinutes));
    const lastMinute  = Math.max(...allLabels.map(toMinutes));

    // Buat array jam lengkap tiap 60 menit dari awal hingga akhir
    const labels = [];
    const values = [];

    for (let min = firstMinute; min <= lastMinute; min += 60) {
        const h   = Math.floor(min / 60) % 24;
        const m   = min % 60;
        const lbl = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');

        labels.push(lbl);
        // Jika jam ini ada di data → nilai asli; jika tidak → 0 (alat mati)
        values.push(dataMap.has(lbl) ? dataMap.get(lbl) : 0);
    }

    return { labels, values };
}

function rwhGetDataset(tabKey, metricKey) {
    // Pilih sumber data sesuai tab
    const raw = tabKey === 'jam'  ? window.DASH.hourlyRaw
              : tabKey === 'hari' ? window.DASH.dailyRaw
              : window.DASH.monthlyRaw;

    // Khusus per-jam: isi gap dengan 0 agar sumbu X mulus setiap jam
    if (tabKey === 'jam') {
        return fillHourlyGaps(raw, metricKey);
    }

    // Per hari / bulanan: data sumber biasanya terurut terbaru→terlama
    // (DESC, untuk kebutuhan tabel histori), tapi grafik butuh urutan
    // kronologis terlama→terbaru (kiri ke kanan), jadi dibalik di sini.
    const ordered = [...raw].reverse();

    const labels = ordered.map(item => {
        if (tabKey === 'hari') return item.date  || item.tanggal || '';
        return item.month || item.bulan || item.date || '';
    });

    const values = ordered.map(item => {
        if (metricKey === 'polutan') return parseFloat(item.polutan ?? item.gas ?? 0) || 0;
        return parseFloat(item[metricKey]) || 0;
    });

    return { labels, values };
}

/** Jumlah desimal tampilan per metrik. Metrik dengan rentang nilai kecil
 *  (O3, NO2) butuh desimal lebih banyak agar tidak terlihat sebagai 0. */
const RWH_DECIMALS = {
    aqi: 0, aqhi: 0, pm25: 1, pm10: 1, pm1: 1,
    no2: 3, o3: 3, polutan: 0, temp: 1, humidity: 0,
};

/** Memformat nilai metrik sesuai jumlah desimal yang sesuai */
function rwhFormatValue(value, metricKey) {
    const decimals = RWH_DECIMALS[metricKey] ?? 0;
    return Number(value).toFixed(decimals);
}

/**
 * Memperbarui info panel (dot warna, nilai, deskripsi, label waktu)
 * saat pengguna hover atau klik batang/titik grafik.
 */
function rwhUpdateInfo(label, value, metricKey) {
    document.getElementById('rwh-dot').style.background = RWH_COLOR[metricKey](value);
    document.getElementById('rwh-val').textContent      = rwhFormatValue(value, metricKey) + ' ' + RWH_UNIT[metricKey];
    document.getElementById('rwh-desc').textContent     = RWH_DESC[metricKey](value);
    document.getElementById('rwh-meta').textContent     = label + ' · ' + RWH_TAB_LABEL[rwhTab];
}

/**
 * Menentukan nilai minimum sumbu Y secara dinamis.
 * Untuk metrik dengan rentang desimal kecil (O3, NO2),
 * sumbu dimulai sedikit di bawah nilai terkecil data
 * (bukan dari 0) agar variasi nilai tetap terlihat jelas.
 * Metrik lain tetap mulai dari 0 seperti biasa.
 */
function rwhGetYMin(metricKey, values) {
    if (!['o3', 'no2'].includes(metricKey)) return undefined; // pakai beginAtZero biasa

    const minVal = Math.min(...values);
    // Beri sedikit ruang di bawah nilai terkecil (10% dari range, minimal 0.005)
    const maxVal = Math.max(...values);
    const range  = Math.max(maxVal - minVal, 0.01);
    const pad    = range * 0.15;

    return Math.max(0, +(minVal - pad).toFixed(3));
}

/**
 * Menentukan nilai maksimum sumbu Y secara dinamis.
 * Memberi padding di atas nilai terbesar data agar puncak
 * grafik tidak menempel di tepi atas chart.
 */
function rwhGetYMax(metricKey, values) {
    if (!['o3', 'no2'].includes(metricKey)) return undefined; // auto seperti biasa

    const minVal = Math.min(...values);
    const maxVal = Math.max(...values);
    const range  = Math.max(maxVal - minVal, 0.01);
    const pad    = range * 0.15;

    return +(maxVal + pad).toFixed(3);
}

/**
 * Merender ulang grafik Chart.js berdasarkan tab dan metrik aktif.
 * Dipanggil setiap kali tab, metrik, atau tipe grafik berubah.
 */
function rwhRender() {
    const metric           = document.getElementById('rwh-metric').value;
    const { labels, values } = rwhGetDataset(rwhTab, metric);

    // Tampilkan pesan kosong jika tidak ada data
    if (!labels.length) {
        if (rwhChart) { rwhChart.destroy(); rwhChart = null; }
        document.getElementById('rwh-val').textContent  = '--';
        document.getElementById('rwh-desc').textContent = '';
        document.getElementById('rwh-meta').textContent = 'Tidak ada data';
        return;
    }

    // Update info dengan data terbaru (titik terakhir)
    rwhUpdateInfo(labels[labels.length - 1], values[values.length - 1], metric);

    // Hancurkan grafik lama sebelum membuat yang baru
    if (rwhChart) { rwhChart.destroy(); rwhChart = null; }

    const ctx = document.getElementById('rwhChart').getContext('2d');

    rwhChart = new Chart(ctx, {
        type: currentChartType,
        data: {
            labels,
            datasets: [{
                data: values,

                // Bar: warna per nilai; Line: gradient hijau
                backgroundColor: currentChartType === 'bar'
                    ? values.map(v => RWH_COLOR[metric](v))
                    : (() => {
                        const g = ctx.createLinearGradient(0, 0, 0, 260);
                        g.addColorStop(0, 'rgba(16,185,129,0.25)');
                        g.addColorStop(1, 'rgba(16,185,129,0.02)');
                        return g;
                    })(),

                borderColor:  metric === 'aqhi' ? RWH_COLOR.aqhi(values[values.length - 1]) : '#10b981',
                borderWidth:  currentChartType === 'line' ? 3 : 0,
                tension:      0.4,
                fill:         currentChartType === 'line',
                pointRadius:  currentChartType === 'line' ? 4 : 0,
                pointHoverRadius:        currentChartType === 'line' ? 6 : 0,
                pointBackgroundColor:    metric === 'aqhi' ? RWH_COLOR.aqhi(values[values.length - 1]) : '#10b981',
                borderRadius: currentChartType === 'bar' ? 4 : 0,
                borderSkipped: false,
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        /**
                         * Label tooltip:
                         * - Nilai 0 pada tab per-jam → tampilkan "Alat mati"
                         *   sebagai penanda gap data (bukan nilai nol sebenarnya).
                         * - Nilai lain → tampilkan angka + satuan seperti biasa.
                         */
                        label: ctx => {
                            if (rwhTab === 'jam' && ctx.parsed.y === 0) {
                                return ' Alat mati / tidak ada data';
                            }
                            return ' ' + rwhFormatValue(ctx.parsed.y, metric) + ' ' + RWH_UNIT[metric];
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { font: { size: 11 }, color: '#94a3b8', maxRotation: 0, autoSkip: true, maxTicksLimit: rwhTab === 'jam' ? 8 : 12 },
                },
                y: {
                    grid:        { color: 'rgba(148,163,184,0.12)' },
                    border:      { display: false },
                    beginAtZero: ['o3', 'no2'].includes(metric) ? false : true,
                    min:         rwhGetYMin(metric, values),
                    max:         rwhGetYMax(metric, values),
                    ticks: {
                        font: { size: 11 },
                        color: '#94a3b8',
                        precision: ['o3', 'no2'].includes(metric) ? 3 : 0,
                    },
                },
            },
            // Update info panel saat hover / klik
            onHover: (evt, els) => {
                if (!els.length) return;
                const val = values[els[0].index];
                const lbl = labels[els[0].index];
                // Jika titik adalah gap (nilai 0 pada per-jam), tampilkan keterangan khusus
                if (rwhTab === 'jam' && val === 0) {
                    document.getElementById('rwh-dot').style.background = '#cbd5e1';
                    document.getElementById('rwh-val').textContent      = '– –';
                    document.getElementById('rwh-desc').textContent     = 'Alat mati / tidak ada data';
                    document.getElementById('rwh-meta').textContent     = lbl + ' · per jam';
                } else {
                    rwhUpdateInfo(lbl, val, metric);
                }
            },
            onClick: (evt, els) => {
                if (!els.length) return;
                const val = values[els[0].index];
                const lbl = labels[els[0].index];
                if (rwhTab === 'jam' && val === 0) {
                    document.getElementById('rwh-dot').style.background = '#cbd5e1';
                    document.getElementById('rwh-val').textContent      = '– –';
                    document.getElementById('rwh-desc').textContent     = 'Alat mati / tidak ada data';
                    document.getElementById('rwh-meta').textContent     = lbl + ' · per jam';
                } else {
                    rwhUpdateInfo(lbl, val, metric);
                }
            },
        }
    });
}

/** Mengganti tab periode grafik (jam / hari / bulan) */
function rwhSetTab(tabKey, btnEl) {
    rwhTab = tabKey;
    document.querySelectorAll('.rwh-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
    rwhRender();
}

/** Mengganti tipe grafik antara bar dan line */
function setChartType(type, btnEl) {
    currentChartType = type;
    document.querySelectorAll('.graph-btn').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
    rwhRender();
}

// Render grafik pertama kali saat halaman dimuat
rwhRender();


/* ================================================================
   C. TIMELINE FORECAST 24 JAM
================================================================ */

/** Label singkat risiko AQHI untuk kartu timeline */
function getAqhiLabelShort(v) {
    if (v <= 3)  return 'Low';
    if (v <= 6)  return 'Moderate';
    if (v <= 10) return 'High';
    return 'Very High';
}

/** Format angka jam menjadi string "HH:00" */
function formatHour(index) {
    const h = index % 24;
    return (h < 10 ? '0' : '') + h + ':00';
}

/**
 * Menghasilkan data forecast dummy 24 jam apabila
 * window.DASH.forecastHourly kosong (fallback development).
 */
function generateDummyForecast() {
    const baseAqhi = [2,2,1,1,2,3,4,5,6,7,7,8,8,9,8,7,6,5,5,4,3,3,2,2];
    const now      = new Date().getHours();
    return baseAqhi.map((aqhi, i) => ({ aqhi, time: formatHour((now + i) % 24) }));
}

/**
 * Handler klik kartu jam pada timeline.
 * Menandai kartu aktif dan memperbarui info panel grafik.
 */
function onTimelineClick(clickedEl, item, color) {
    // Reset semua kartu
    document.querySelectorAll('.fc-hour').forEach(x => {
        x.classList.remove('active');
        if (!x.classList.contains('is-now')) {
            x.style.borderColor = '';
            x.style.color       = '';
        }
    });

    // Tandai kartu yang diklik
    clickedEl.classList.add('active');
    clickedEl.style.borderColor = color;
    clickedEl.style.color       = color;

    // Sinkronisasi info panel grafik
    const jam = item.time || item.jam || '--';
    const rwhVal  = document.getElementById('rwh-val');
    const rwhMeta = document.getElementById('rwh-meta');
    if (rwhVal)  rwhVal.textContent  = item.aqhi + ' AQHI';
    if (rwhMeta) rwhMeta.textContent = jam + ' · forecast';
}

/**
 * Merender semua kartu jam pada timeline forecast.
 * Kartu pertama selalu menampilkan data AQHI SEKARANG
 * (sinkron dengan hero card), diberi label waktu "Now".
 * Kartu berikutnya adalah forecast jam demi jam.
 * Tidak ada highlight/auto-scroll karena kartu "now" selalu
 * berada di posisi pertama (paling kiri).
 */
function renderTimeline() {
    const container = document.getElementById('fc-timeline');
    if (!container) return;

    const forecastData = window.DASH.forecastHourly.length > 0
        ? window.DASH.forecastHourly
        : generateDummyForecast();

    // Ambil data AQHI sekarang dari sumber yang sama dengan hero card
    const latest   = window.DASH.latestData || {};
    const nowAqhi   = parseFloat(latest.aqhi) || 0;
    const nowItem   = { aqhi: nowAqhi, time: 'Now' };

    // Gabungkan: kartu "Now" di depan, lalu forecast jam-jam berikutnya
    const data = [nowItem, ...forecastData];

    const maxVal  = Math.max(...data.map(d => d.aqhi ?? d.aqi));
    const BAR_MAX = 52; // tinggi maksimum batang dalam px

    container.innerHTML = '';

    // Perbarui badge AQHI sekarang di header
    const nowBadge = document.getElementById('timeline-now-badge');
    if (nowBadge) {
        const nowColor = getAqhiColor(nowAqhi);
        nowBadge.textContent       = nowAqhi + ' AQHI sekarang';
        nowBadge.style.color       = nowColor;
        nowBadge.style.borderColor = nowColor + '55';
    }

    // Buat kartu untuk setiap titik (Now + forecast)
    data.forEach((item, i) => {
        const aqhi   = parseFloat(item.aqhi ?? item.aqi) || 0;
        const jam    = item.time || item.jam || formatHour(i - 1);
        const color  = getAqhiColor(aqhi);
        const barH   = Math.max(4, Math.round((aqhi / Math.max(maxVal, 11)) * BAR_MAX));
        const isNow  = i === 0;

        const el = document.createElement('div');
        el.className = 'fc-hour' + (isNow ? ' is-now' : '');

        el.innerHTML = `
            <span class="fc-h-time" style="${isNow ? 'color:' + color + ';font-weight:700;' : ''}">${jam}</span>
            <div class="fc-h-bar-wrap">
                <div class="fc-h-bar" style="height:${barH}px;background:${color};"></div>
            </div>
            <span class="fc-h-val"   style="color:${color};">${aqhi}</span>
            <span class="fc-h-label">${getAqhiLabelShort(aqhi)}</span>
        `;

        el.addEventListener('click', () => onTimelineClick(el, item, color));
        container.appendChild(el);
    });
}

renderTimeline();


/* ================================================================
   D. PREDIKSI SVR 7 HARI KE DEPAN
   Sumber: window.DASH.predictionFuture (dari tabel
   data_udara_prediksi_future, BUKAN data sensor aktual).
================================================================ */

/**
 * Handler klik kartu prediksi.
 * Menandai kartu aktif (tidak menyentuh grafik history,
 * karena ini data prediksi, bukan history).
 */
function onPredictionClick(clickedEl, item, color) {
    // Reset semua kartu prediksi
    document.querySelectorAll('#prediction-timeline .fc-hour').forEach(x => {
        x.classList.remove('active');
        x.style.borderColor = '';
        x.style.color       = '';
    });

    // Tandai kartu yang diklik
    clickedEl.classList.add('active');
    clickedEl.style.borderColor = color;
    clickedEl.style.color       = color;
}

/**
 * Merender semua kartu prediksi SVR 7 hari ke depan.
 * Label kartu memakai tanggal + jam (karena rentangnya beberapa hari,
 * berbeda dari timeline 24 jam yang cukup pakai jam saja).
 */
function renderPredictionFuture() {
    const container = document.getElementById('prediction-timeline');
    const emptyEl    = document.getElementById('prediction-empty');
    if (!container) return;

    const data = window.DASH.predictionFuture || [];

    // Tidak ada dummy fallback untuk prediksi — kalau kosong,
    // tampilkan pesan empty state agar tidak menyesatkan pengguna
    // dengan data yang terlihat seperti hasil model padahal dummy.
    if (data.length === 0) {
        container.innerHTML = '';
        if (emptyEl) emptyEl.style.display = 'flex';
        const badge = document.getElementById('prediction-now-badge');
        if (badge) badge.textContent = '-- AQHI prediksi';
        return;
    }

    if (emptyEl) emptyEl.style.display = 'none';

    const maxVal  = Math.max(...data.map(d => d.aqhi ?? d.aqi ?? 0));
    const BAR_MAX = 52; // tinggi maksimum batang dalam px

    container.innerHTML = '';

    // Perbarui badge AQHI prediksi pertama di header
    const firstItem = data[0];
    const badge      = document.getElementById('prediction-now-badge');
    if (badge && firstItem) {
        const firstVal   = parseFloat(firstItem.aqhi ?? firstItem.aqi) || 0;
        const firstColor = getAqhiColor(firstVal);
        badge.textContent       = firstVal + ' AQHI prediksi';
        badge.style.color       = firstColor;
        badge.style.borderColor = firstColor + '55';
    }

    // Buat kartu untuk setiap titik prediksi
    data.forEach((item, i) => {
        const aqhi   = parseFloat(item.aqhi ?? item.aqi) || 0;
        const tgl    = item.date || '';
        const jam    = item.time || '';
        const label  = tgl ? (tgl.split(' ')[0] + ' ' + jam) : (jam || formatHour(i));
        const color  = getAqhiColor(aqhi);
        const barH   = Math.max(4, Math.round((aqhi / Math.max(maxVal, 11)) * BAR_MAX));

        const el = document.createElement('div');
        el.className = 'fc-hour' + (i === 0 ? ' active' : '');
        if (i === 0) { el.style.borderColor = color; el.style.color = color; }

        el.innerHTML = `
            <span class="fc-h-time" style="${i === 0 ? 'color:' + color + ';font-weight:700;' : ''}">${label}</span>
            <div class="fc-h-bar-wrap">
                <div class="fc-h-bar" style="height:${barH}px;background:${color};"></div>
            </div>
            <span class="fc-h-val"   style="color:${color};">${aqhi}</span>
            <span class="fc-h-label">${getAqhiLabelShort(aqhi)}</span>
        `;

        el.addEventListener('click', () => onPredictionClick(el, item, color));
        container.appendChild(el);
    });
}

renderPredictionFuture();


/* ================================================================
   E. FUNGSI EXPORT
================================================================ */

/**
 * Mengekspor data tabel ke format Excel (.xlsx) menggunakan SheetJS.
 * Mengambil data dari window.DASH.hourlyRaw (atau dailyRaw sebagai fallback).
 */
function exportToExcel() {
    const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const source   = window.DASH.hourlyRaw.length > 0 ? window.DASH.hourlyRaw : window.DASH.dailyRaw;

    const rows = source.map(item => {
        const rawDate = item.created_at || item.date || item.tanggal || '';
        let hariTanggal = '';
        let jam = item.time || item.jam || '';

        if (rawDate) {
            const d = new Date(rawDate);
            if (!isNaN(d)) {
                hariTanggal = dayNames[d.getDay()] + ', '
                    + String(d.getDate()).padStart(2, '0') + '/'
                    + String(d.getMonth() + 1).padStart(2, '0') + '/'
                    + d.getFullYear();
                if (!jam) jam = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
            } else {
                hariTanggal = rawDate;
            }
        }

        const aqi  = parseFloat(item.aqi)  || 0;
        const aqhi = parseFloat(item.aqhi) || 0;

        return {
            'Bulan'              : item.bulan || (rawDate ? new Date(rawDate).toLocaleString('id-ID', { month:'long', year:'numeric' }) : ''),
            'Hari & Tanggal'     : hariTanggal,
            'Jam'                : jam,
            'Status'             : aqhi > 0 ? getAqhiLabelExport(aqhi) : getAqiLabel(aqi),
            'AQHI'               : aqhi,
            'AQI'                : aqi,
            'PM2.5 (µg/m³)'     : parseFloat(item.pm25)                          || 0,
            'NO2 (ppm)'          : parseFloat(item.no2)                           || 0,
            'O3 (ppm)'           : parseFloat(item.o3)                            || 0,
            'PM10 (µg/m³)'      : parseFloat(item.pm10)                          || 0,
            'PM1 (µg/m³)'       : parseFloat(item.pm1)                           || 0,
            'Polutan/VOC (ppm)'  : parseFloat(item.polutan ?? item.gas)           || 0,
            'Temp. (°C)'         : parseFloat(item.suhu    ?? item.temp)          || 0,
            'Humi. (%)'          : parseFloat(item.kelembaban ?? item.humidity)   || 0,
            'Lokasi'             : item.location || window.DASH.latestData?.location || 'Ruang Utama',
        };
    });

    if (rows.length === 0) { alert('Tidak ada data untuk diekspor.'); return; }

    const ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [
        { wch: 16 }, // Bulan
        { wch: 22 }, // Hari & Tanggal
        { wch:  8 }, // Jam
        { wch: 14 }, // Status
        { wch:  8 }, // AQHI
        { wch:  8 }, // AQI
        { wch: 14 }, // PM2.5
        { wch: 10 }, // NO2
        { wch: 10 }, // O3
        { wch: 14 }, // PM10
        { wch: 14 }, // PM1
        { wch: 16 }, // Polutan/VOC
        { wch: 12 }, // Temp
        { wch: 10 }, // Humi
        { wch: 16 }, // Lokasi
    ];

    const wb       = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Harian');
    XLSX.writeFile(wb, 'riwayat-udara-' + _hdDateStr() + '.xlsx');
}

/**
 * Mengembalikan label AQHI singkat untuk kolom Status di export.
 * Dipisah dari getAqhiLabelShort agar tidak bergantung pada fungsi timeline.
 */
function getAqhiLabelExport(v) {
    if (v <= 3)  return 'Low';
    if (v <= 6)  return 'Moderate';
    if (v <= 10) return 'High';
    return 'Very High';
}

/** Helper: format tanggal hari ini sebagai "DD-MM-YYYY" untuk nama file */
function _hdDateStr() {
    const d = new Date();
    return String(d.getDate()).padStart(2, '0') + '-'
         + String(d.getMonth() + 1).padStart(2, '0') + '-'
         + d.getFullYear();
}

/**
 * Entry point untuk semua tombol export pada toolbar tabel.
 * @param {'copy'|'csv'|'excel'|'pdf'|'print'} type
 */
function hdExport(type) {
    const headers = Array.from(document.querySelectorAll('.hd-tbl thead th')).map(th => th.innerText.trim());
    const rows    = hdGetVisibleRows().map(tr => Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim()));

    if (type === 'copy') {
        const text = [headers, ...rows].map(r => r.join('\t')).join('\n');
        navigator.clipboard.writeText(text).then(() => {
            // Feedback visual singkat pada tombol copy
            const btn  = document.querySelector('.hd-btn-copy');
            const orig = btn.innerHTML;
            btn.innerHTML   = '<i class="fa-solid fa-check"></i>';
            btn.style.color = '#10b981';
            setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 1500);
        });
        return;
    }

    if (type === 'csv') {
        const escape = v => `"${String(v).replace(/"/g, '""')}"`;
        const csv    = [headers, ...rows].map(r => r.map(escape).join(',')).join('\n');
        const blob   = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const url    = URL.createObjectURL(blob);
        const a      = Object.assign(document.createElement('a'), { href: url, download: `riwayat-udara-${_hdDateStr()}.csv` });
        a.click();
        URL.revokeObjectURL(url);
        return;
    }

    if (type === 'excel') {
        exportToExcel();
        return;
    }

    if (type === 'pdf') {
        // Muat jsPDF secara dinamis agar tidak membebani halaman jika tidak digunakan
        const load = cb => {
            if (window.jspdf) { cb(); return; }
            const s1 = document.createElement('script');
            s1.src    = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            s1.onload = () => {
                const s2 = document.createElement('script');
                s2.src    = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js';
                s2.onload = cb;
                document.head.appendChild(s2);
            };
            document.head.appendChild(s1);
        };

        load(() => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
            doc.setFontSize(12);
            doc.text('Air Quality Historical Data', 40, 36);
            doc.autoTable({
                head:               [headers],
                body:               rows,
                startY:             50,
                styles:             { fontSize: 7, cellPadding: 3 },
                headStyles:         { fillColor: [16, 185, 129], textColor: 255, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [245, 250, 247] },
            });
            doc.save(`riwayat-udara-${_hdDateStr()}.pdf`);
        });
        return;
    }

    if (type === 'print') {
        const html = `
            <html><head><title>Air Quality Data</title>
            <style>
                body  { font-family: sans-serif; font-size: 11px; }
                table { border-collapse: collapse; width: 100%; }
                th    { background: #10b981; color: #fff; padding: 6px 8px; text-align: left; }
                td    { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
                tr:nth-child(even) td { background: #f8fafc; }
            </style></head><body>
            <h3 style="margin-bottom:12px">Air Quality Historical Data</h3>
            <table>
                <thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
                <tbody>${rows.map(r => `<tr>${r.map(c => `<td>${c}</td>`).join('')}</tr>`).join('')}</tbody>
            </table>
            </body></html>`;
        const w = window.open('', '_blank');
        w.document.write(html);
        w.document.close();
        w.print();
    }
}


/* ================================================================
   F. KONTROL TABEL HISTORIS
   Mencakup: toggle tampilan, filter keyword, pagination
================================================================ */

let hdView    = 'jam'; // view aktif: 'jam' | 'hari'
let hdEntries = 10;    // jumlah baris yang ditampilkan per halaman

/**
 * Mengembalikan semua baris tabel yang saat ini tampil
 * (sesuai view aktif dan tidak disembunyikan filter/pagination).
 */
function hdGetVisibleRows() {
    return Array.from(document.querySelectorAll(`#hd-tbody tr[data-view="${hdView}"]`))
                .filter(r => r.style.display !== 'none');
}

/**
 * Mengganti tampilan tabel antara "per jam" dan "per hari".
 * Reset filter dan pagination setiap kali toggle.
 */
function hdSetView(view, btnEl) {
    hdView = view;
    document.querySelectorAll('.hd-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');

    // Ganti header kolom pertama
    document.getElementById('th-primary').textContent = view === 'jam' ? 'TIME ▼' : 'DATE ▼';

    // Tampilkan hanya baris yang sesuai view
    document.querySelectorAll('#hd-tbody tr').forEach(row => {
        row.style.display = row.dataset.view === view ? '' : 'none';
    });

    // Reset filter
    document.getElementById('hd-search').value        = '';
    document.getElementById('hd-clear').style.display = 'none';
    document.getElementById('hd-empty').style.display = 'none';

    hdApplyEntries();
}

/**
 * Menyaring baris tabel berdasarkan keyword yang diketik.
 * Baris yang tidak cocok disembunyikan via dataset.filtered.
 */
function hdFilter() {
    const kw = document.getElementById('hd-search').value.toLowerCase().trim();
    document.getElementById('hd-clear').style.display = kw ? 'block' : 'none';

    let visible = 0;
    document.querySelectorAll('#hd-tbody tr').forEach(row => {
        if (row.dataset.view !== hdView) return;
        const match = row.textContent.toLowerCase().includes(kw);
        row.dataset.filtered = match ? 'false' : 'true';
        if (match) visible++;
    });

    hdApplyEntries();

    // Tampilkan pesan kosong jika tidak ada hasil
    const emptyEl = document.getElementById('hd-empty');
    emptyEl.style.display = visible === 0 ? 'flex' : 'none';
    document.getElementById('hd-empty-kw').textContent = kw;
}

/** Menghapus filter dan menampilkan kembali semua baris */
function hdClear() {
    document.getElementById('hd-search').value        = '';
    document.getElementById('hd-clear').style.display = 'none';
    document.getElementById('hd-empty').style.display = 'none';
    document.querySelectorAll(`#hd-tbody tr[data-view="${hdView}"]`).forEach(r => r.style.display = '');
    hdApplyEntries();
}

/** Handler perubahan select "Show N entries" */
function hdChangeEntries() {
    hdEntries = parseInt(document.getElementById('hd-entries-select').value);
    hdApplyEntries();
}

/**
 * Menerapkan batas tampilan baris (pagination sederhana).
 * Baris yang lolos filter ditampilkan sebanyak hdEntries,
 * sisanya disembunyikan. Footer info diperbarui.
 */
function hdApplyEntries() {
    const rows = Array.from(document.querySelectorAll(`#hd-tbody tr[data-view="${hdView}"]`));
    let visibleIndex = 0;

    rows.forEach(row => {
        if (row.dataset.filtered === 'true') { row.style.display = 'none'; return; }
        row.style.display = visibleIndex < hdEntries ? '' : 'none';
        visibleIndex++;
    });

    const totalRows = rows.filter(r => r.dataset.filtered !== 'true').length;
    const showingTo = Math.min(totalRows, hdEntries);
    document.getElementById('hd-footer-info').textContent = `Showing 1 to ${showingTo} of ${totalRows} entries`;
}

// Inisialisasi tabel saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    hdSetView('jam', document.getElementById('btn-jam'));
});
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
        <div class="daily-card-header">
            <h4>Riwayat Harian</h4>
        </div>

        <div class="daily-tbl-wrap">
            <table class="daily-tbl">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>AQI</th>
                        <th>PM<sub>2.5</sub> (µg/m³)</th>
                        <th>PM<sub>10</sub> (µg/m³)</th>
                        <th>NOx/VOC (ppm)</th>
                        <th>Temp. (°C)</th>
                        <th>Humi. (%)</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historyDaily as $item): ?>
                    <tr class="<?= $item['is_today'] ? 'today-row' : '' ?>">
                        <td class="col-day"><?= $item['date'] ?></td>
                        <td>
                            <span class="daily-status-pill <?= getDailyPillClass($item['aqi']) ?>">
                                <?= getAqiLabel($item['aqi']) ?>
                            </span>
                        </td>
                        <td class="num-cell"><?= $item['aqi'] ?></td>
                        <td class="num-cell"><?= $item['pm25'] ?></td>
                        <td class="num-cell"><?= $item['pm10'] ?></td>
                        <td class="num-cell"><?= $item['nox'] ?></td>
                        <td class="num-cell"><?= $item['temp'] ?>°</td>
                        <td class="num-cell"><?= $item['humidity'] ?>%</td>
                        <td class="col-loc"><?= $item['location'] ?? 'Bojongsoang' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CHART CARD -->
    <div class="history-card-box daily-card-box">
        <div class="daily-card-header">
            <h4>Grafik Riwayat</h4>
            <button class="export-btn" onclick="exportToExcel()">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </button>
        </div>

        <div class="rwh-header"></div>

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

</div>


<script>
// ============================================================
// CHART — COLOR / UNIT / DESC HELPERS
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

function rwhGetDataset(tabKey, metricKey) {
    const raw = tabKey === 'jam'  ? window.DASH.hourlyRaw
              : tabKey === 'hari' ? window.DASH.dailyRaw
              : window.DASH.monthlyRaw;

    const labels = raw.map(item => {
        if (tabKey === 'jam')  return item.time  || item.jam     || '';
        if (tabKey === 'hari') return item.date  || item.tanggal || '';
        return item.month || item.bulan || item.date || '';
    });

    const values = raw.map(item => {
        if (metricKey === 'nox') return parseFloat(item.nox ?? item.gas ?? 0) || 0;
        return parseFloat(item[metricKey]) || 0;
    });

    return { labels, values };
}

function rwhUpdateInfo(label, value, metricKey) {
    document.getElementById('rwh-dot').style.background = RWH_COLOR[metricKey](value);
    document.getElementById('rwh-val').textContent      = value + ' ' + RWH_UNIT[metricKey];
    document.getElementById('rwh-desc').textContent     = RWH_DESC[metricKey](value);
    document.getElementById('rwh-meta').textContent     = label + ' · ' + RWH_TAB_LABEL[rwhTab];
}

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

    rwhUpdateInfo(labels[labels.length - 1], values[values.length - 1], metric);

    if (rwhChart) rwhChart.destroy();

    rwhChart = new Chart(document.getElementById('rwhChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: values.map(v => RWH_COLOR[metric](v)),
                borderRadius: 4,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ctx.parsed.y + ' ' + RWH_UNIT[metric] } }
            },
            scales: {
                x: {
                    grid: { display: false }, border: { display: false },
                    ticks: { font:{size:11}, color:'#94a3b8', maxRotation:0, autoSkip:true, maxTicksLimit: rwhTab==='jam'?8:12 }
                },
                y: {
                    grid: { color:'rgba(148,163,184,0.12)' }, border: { display:false },
                    beginAtZero: true,
                    ticks: { font:{size:11}, color:'#94a3b8' }
                }
            },
            onHover: (evt, els) => { if (els.length) rwhUpdateInfo(labels[els[0].index], values[els[0].index], metric); },
            onClick: (evt, els) => { if (els.length) rwhUpdateInfo(labels[els[0].index], values[els[0].index], metric); }
        }
    });
}

function rwhSetTab(tabKey, btnEl) {
    rwhTab = tabKey;
    document.querySelectorAll('.rwh-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');
    rwhRender();
}

rwhRender();

// ============================================================
// TIMELINE PER JAM
// ============================================================
function getAqiLabelShort(v) {
    if (v <= 50)  return 'Good';
    if (v <= 100) return 'Moderate';
    if (v <= 150) return 'Sensitive';
    if (v <= 200) return 'Unhealthy';
    if (v <= 300) return 'Very Un.';
    return 'Hazard';
}

function formatHour(index) {
    const h = index % 24;
    return (h < 10 ? '0' : '') + h + ':00';
}

function generateDummyForecast() {
    const baseAqi = [42,38,35,33,36,44,58,72,88,103,118,128,135,142,138,126,110,95,82,68,57,48,44,40];
    const now     = new Date().getHours();
    return baseAqi.map((aqi, i) => ({ aqi, time: formatHour((now + i) % 24) }));
}

function onTimelineClick(clickedEl, item, color) {
    document.querySelectorAll('.fc-hour').forEach(x => {
        x.classList.remove('active');
        if (!x.classList.contains('is-now')) {
            x.style.borderColor = '';
            x.style.color       = '';
        }
    });
    clickedEl.classList.add('active');
    clickedEl.style.borderColor = color;
    clickedEl.style.color       = color;

    const aqi = parseFloat(item.aqi) || 0;
    const jam = item.time || item.jam || '--';
    const rwhVal  = document.getElementById('rwh-val');
    const rwhMeta = document.getElementById('rwh-meta');
    if (rwhVal)  rwhVal.textContent  = aqi + ' AQI';
    if (rwhMeta) rwhMeta.textContent = jam + ' · forecast';
}

function renderTimeline() {
    const container = document.getElementById('fc-timeline');
    if (!container) return;

    const data     = window.DASH.forecastHourly.length > 0 ? window.DASH.forecastHourly : generateDummyForecast();
    const maxVal   = Math.max(...data.map(d => d.aqi));
    const BAR_MAX  = 52;
    const nowHour  = new Date().getHours();

    container.innerHTML = '';

    const nowItem  = data[0];
    const nowBadge = document.getElementById('timeline-now-badge');
    if (nowBadge && nowItem) {
        const nowColor = getAqiColor(parseFloat(nowItem.aqi));
        nowBadge.textContent       = nowItem.aqi + ' AQI sekarang';
        nowBadge.style.color       = nowColor;
        nowBadge.style.borderColor = nowColor + '55';
    }

    data.forEach((item, i) => {
        const aqi      = parseFloat(item.aqi) || 0;
        const jam      = item.time || item.jam || formatHour(i);
        const color    = getAqiColor(aqi);
        const barH     = Math.max(4, Math.round((aqi / Math.max(maxVal, 200)) * BAR_MAX));
        const itemHour = parseInt(jam.split(':')[0]);
        const isNow    = itemHour === nowHour && i < 24;

        const el = document.createElement('div');
        el.className = 'fc-hour' + (isNow ? ' active is-now' : '');
        if (isNow) { el.style.borderColor = color; el.style.color = color; }

        el.innerHTML = `
            <span class="fc-h-time" style="${isNow ? 'color:'+color+';font-weight:700;' : ''}">${jam}</span>
            <div class="fc-h-bar-wrap">
                <div class="fc-h-bar" style="height:${barH}px;background:${color};"></div>
            </div>
            <span class="fc-h-val" style="color:${color};">${aqi}</span>
            <span class="fc-h-label">${getAqiLabelShort(aqi)}</span>
        `;

        el.addEventListener('click', () => onTimelineClick(el, item, color));
        container.appendChild(el);
    });

    const activeEl = container.querySelector('.fc-hour.active');
    if (activeEl) activeEl.scrollIntoView({ behavior:'smooth', block:'nearest', inline:'center' });
}

renderTimeline();

// ============================================================
// EXPORT TO EXCEL
// ============================================================
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
                hariTanggal = dayNames[d.getDay()] + ', ' +
                    String(d.getDate()).padStart(2,'0') + '/' +
                    String(d.getMonth()+1).padStart(2,'0') + '/' +
                    d.getFullYear();
                if (!jam) jam = String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
            } else {
                hariTanggal = rawDate;
            }
        }

        const aqi = parseFloat(item.aqi) || 0;

        return {
            'Bulan'          : item.bulan || (rawDate ? new Date(rawDate).toLocaleString('id-ID', { month:'long', year:'numeric' }) : ''),
            'Hari & Tanggal' : hariTanggal,
            'Jam'            : jam,
            'Status'         : getAqiLabel(aqi),
            'AQI'            : aqi,
            'PM2.5 (µg/m³)'  : parseFloat(item.pm25) || 0,
            'PM10 (µg/m³)'   : parseFloat(item.pm10) || 0,
            'NOx/VOC (ppm)'  : parseFloat(item.nox ?? item.gas) || 0,
            'Temp. (°C)'     : parseFloat(item.suhu ?? item.temp) || 0,
            'Humi. (%)'      : parseFloat(item.kelembaban ?? item.humidity) || 0,
            'Lokasi'         : item.location || window.DASH.latestData?.location || 'Ruang Utama',
        };
    });

    if (rows.length === 0) { alert('Tidak ada data untuk diekspor.'); return; }

    const ws = XLSX.utils.json_to_sheet(rows);
    ws['!cols'] = [
        {wch:16},{wch:22},{wch:8},{wch:18},{wch:10},
        {wch:14},{wch:14},{wch:14},{wch:12},{wch:10},{wch:16}
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Riwayat Harian');

    const today    = new Date();
    const fileName = 'riwayat-udara-' +
        String(today.getDate()).padStart(2,'0') + '-' +
        String(today.getMonth()+1).padStart(2,'0') + '-' +
        today.getFullYear() + '.xlsx';

    XLSX.writeFile(wb, fileName);
}
</script>
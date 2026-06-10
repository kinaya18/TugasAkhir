<!-- ===================== SECTION 2: RIWAYAT ===================== -->
<div class="history-wrapper">

<!-- DAILY TABLE -->
<div class="history-card-box hd-card">
    <div class="hd-header">
        <div class="hd-title-group">
            <span class="hd-eyebrow">Historical Data</span>
            <h4 class="hd-title">Air Quality Table</h4>
        </div>
    </div>

<div class="hd-controls">

    <!-- LEFT -->
    <div style="display:flex; align-items:center; gap:14px;">

        <div class="hd-toggle">

            <button class="hd-tab active"
                    id="btn-jam"
                    onclick="hdSetView('jam',this)">

                <i class="fa-solid fa-clock"></i>
                Per Jam

            </button>

            <button class="hd-tab"
                    id="btn-hari"
                    onclick="hdSetView('hari',this)">

                <i class="fa-solid fa-calendar-day"></i>
                Per Hari

            </button>

        </div>

        <!-- EXPORT TOOLBAR -->
        <div class="hd-export-toolbar">

            <button class="hd-export-btn hd-btn-copy"
                    onclick="hdExport('copy')"
                    title="Copy to Clipboard">

                <i class="fa-regular fa-copy"></i>

            </button>

            <button class="hd-export-btn hd-btn-csv"
                    onclick="hdExport('csv')"
                    title="Export CSV">

                <i class="fa-solid fa-file-csv"></i>

            </button>

            <button class="hd-export-btn hd-btn-excel"
                    onclick="hdExport('excel')"
                    title="Export Excel">

                <i class="fa-solid fa-file-excel"></i>

            </button>

            <button class="hd-export-btn hd-btn-pdf"
                    onclick="hdExport('pdf')"
                    title="Export PDF">

                <i class="fa-solid fa-file-pdf"></i>

            </button>

            <button class="hd-export-btn hd-btn-print"
                    onclick="hdExport('print')"
                    title="Print">

                <i class="fa-solid fa-print"></i>

            </button>

        </div>

    </div>

    <!-- RIGHT -->
    <div class="hd-search-wrap">

        <i class="fa-solid fa-magnifying-glass hd-search-icon"></i>

        <input type="text"
               id="hd-search"
               class="hd-search"
               placeholder="Filter by keyword..."
               oninput="hdFilter()" />

        <button class="hd-clear"
                id="hd-clear"
                onclick="hdClear()"
                title="Clear">

            <i class="fa-solid fa-xmark"></i>

        </button>

    </div>

</div>
<div class="hd-inner-card">
    <div class="hd-tbl-wrap">
        <table class="hd-tbl">
            <thead>
                <tr>
                    <th id="th-primary">TIME ▼</th>
                    <th>AQHI</th>
                    <th>AQI</th>
                    <th>PM2.5</th>
                    <th>PM10</th>
                    <th>PM1</th>
                    <th>NO<sub>2</sub></th>
                    <th>O<sub>3</sub></th>
                    <th>TEMP</th>
                    <th>HUMIDITY</th>
                    <th>LOCATION</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody id="hd-tbody">
                <?php foreach ($historyHourly as $item): ?>
                <tr class="<?= !empty($item['is_today']) ? 'today-row' : '' ?>" data-view="jam">
                    <td class="td-time"><?= $item['time'] ?? '--' ?></td>
                    <td class="td-aqhi"><?= $item['aqhi'] ?? '-' ?></td>
                    <td><?= $item['aqi'] ?? '-' ?></td>
                    <td><?= $item['pm25'] ?? '-' ?></td>
                    <td><?= $item['pm10'] ?? '-' ?></td>
                    <td><?= $item['pm1'] ?? '-' ?></td>
                    <td><?= $item['no2'] ?? '-' ?></td>
                    <td><?= $item['o3'] ?? '-' ?></td>
                    <td><?= $item['temp'] ?? '-' ?>°C</td>
                    <td><?= $item['humidity'] ?? '-' ?>%</td>
                    <td class="td-loc"><?= $item['location'] ?? 'Bojongsoang' ?></td>
                    <td><span class="pill <?= getAqhiPillClass($item['aqhi'] ?? 0) ?>"><?= getAqhiLabel($item['aqhi'] ?? 0) ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php foreach ($historyDaily as $item): ?>
                <tr class="<?= !empty($item['is_today']) ? 'today-row' : '' ?>" data-view="hari" style="display:none;">
                    <td class="td-date"><?= $item['date'] ?? '--' ?></td>
                    <td class="td-aqhi"><?= $item['aqhi'] ?? '-' ?></td>
                    <td><?= $item['aqi'] ?? '-' ?></td>
                    <td><?= $item['pm25'] ?? '-' ?></td>
                    <td><?= $item['pm10'] ?? '-' ?></td>
                    <td><?= $item['pm1'] ?? '-' ?></td>
                    <td><?= $item['no2'] ?? '-' ?></td>
                    <td><?= $item['o3'] ?? '-' ?></td>
                    <td><?= $item['temp'] ?? '-' ?>°C</td>
                    <td><?= $item['humidity'] ?? '-' ?>%</td>
                    <td class="td-loc"><?= $item['location'] ?? 'Bojongsoang' ?></td>
                    <td><span class="pill <?= getAqhiPillClass($item['aqhi'] ?? 0) ?>"><?= getAqhiLabel($item['aqhi'] ?? 0) ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="hd-empty" id="hd-empty">
            <i class="fa-solid fa-inbox"></i>
            <span>No data matches "<span id="hd-empty-kw"></span>"</span>
        </div>
    </div>
    <div class="hd-footer">

        <div class="hd-entries">

            <span>Show</span>

            <select id="hd-entries-select"
                    onchange="hdChangeEntries()">

                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="9999">All</option>

            </select>

            <span>entries</span>

        </div>

        <div class="hd-footer-info"
            id="hd-footer-info">

            Showing 1 to 10 entries

        </div>

    </div>
</div>
</div>

<!-- CHART CARD -->
<div class="history-card-box daily-card-box">

    <div class="daily-card-header">

        <div>
            <span class="hd-eyebrow">HISTORY GRAPH</span>
            <h4>Air Quality Trend</h4>
        </div>

        <div style="display:flex; align-items:center; gap:12px;">

            <!-- TOGGLE BAR / LINE -->
            <div class="graph-toggle">

                <button class="graph-btn active"
                        id="btn-bar"
                        onclick="setChartType('bar', this)"
                        title="Bar Chart">

                    <i class="fa-solid fa-chart-column"></i>

                </button>

                <button class="graph-btn"
                        id="btn-line"
                        onclick="setChartType('line', this)"
                        title="Line Chart">

                    <i class="fa-solid fa-chart-line"></i>

                </button>

            </div>

        </div>

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

                <button class="rwh-tab active"
                        onclick="rwhSetTab('jam',this)">
                    per jam
                </button>

                <button class="rwh-tab"
                        onclick="rwhSetTab('hari',this)">
                    per hari
                </button>

                <button class="rwh-tab"
                        onclick="rwhSetTab('bulan',this)">
                    bulanan
                </button>

            </div>

            <select class="rwh-select"
                    id="rwh-metric"
                    onchange="rwhRender()">

                <option value="aqhi">AQHI</option>
                <option value="aqi">AQI</option>
                <option value="pm25">PM2.5</option>
                <option value="no2">NO₂</option>
                <option value="o3">O₃</option>
                <option value="pm10">PM10</option>
                <option value="polutan">polutan / VOC</option>
                <option value="temp">Suhu</option>
                <option value="humidity">Kelembapan</option>

            </select>

        </div>

    </div>

    <div class="mini-chart-card">

        <div class="rwh-canvas-wrap">

            <canvas id="rwhChart"></canvas>

        </div>

    </div>

</div>


<script>
// ============================================================
// CHART — COLOR / UNIT / DESC HELPERS
// ============================================================
const RWH_COLOR = {
    aqi:      v => v<=50?'#22c55e':v<=100?'#eab308':v<=150?'#f97316':v<=200?'#ef4444':v<=300?'#a855f7':'#7f1d1d',
    aqhi:     v => v<=3?'#3b82f6':v<=6?'#eab308':v<=10?'#ef4444':'#7f1d1d',
    pm25:     v => v<=12?'#22c55e':v<=35?'#eab308':v<=55?'#f97316':v<=150?'#ef4444':'#7f1d1d',
    pm10:     v => v<=54?'#22c55e':v<=154?'#eab308':v<=254?'#f97316':v<=354?'#ef4444':'#7f1d1d',
    no2:      v => v<=40?'#22c55e':v<=100?'#eab308':v<=200?'#f97316':v<=400?'#ef4444':'#7f1d1d',
    o3:       v => v<=50?'#22c55e':v<=100?'#eab308':v<=168?'#f97316':v<=208?'#ef4444':'#7f1d1d',
    polutan:  v => v<=50?'#22c55e':v<=100?'#eab308':v<=199?'#f97316':v<=299?'#ef4444':'#7f1d1d',
    temp:     v => v<=15?'#60a5fa':v<=22?'#22c55e':v<=28?'#eab308':v<=35?'#f97316':'#ef4444',
    humidity: v => v<=25?'#ef4444':v<=39?'#f97316':v<=60?'#22c55e':v<=75?'#eab308':'#a855f7',
};

const RWH_UNIT = {
    aqi:'AQI', aqhi:'AQHI', pm25:'µg/m³', pm10:'µg/m³',
    no2:'ppb', o3:'ppb', polutan:'ppm', temp:'°C', humidity:'%'
};

const RWH_DESC = {
    aqi:      v => v<=50?'Baik':v<=100?'Sedang':v<=150?'Tidak sehat bagi kelompok sensitif':v<=200?'Tidak sehat':v<=300?'Sangat tidak sehat':'Berbahaya',
    aqhi:     v => v<=3?'Risiko rendah':v<=6?'Risiko sedang':v<=10?'Risiko tinggi':'Risiko sangat tinggi',
    pm25:     v => v<=12?'Baik':v<=35?'Sedang':v<=55?'Tidak sehat sensitif':v<=150?'Tidak sehat':'Berbahaya',
    pm10:     v => v<=54?'Baik':v<=154?'Sedang':v<=254?'Tidak sehat sensitif':'Tidak sehat',
    no2:      v => v<=40?'Baik':v<=100?'Sedang':v<=200?'Tidak sehat sensitif':v<=400?'Tidak sehat':'Berbahaya',
    o3:       v => v<=50?'Baik':v<=100?'Sedang':v<=168?'Tidak sehat sensitif':v<=208?'Tidak sehat':'Berbahaya',
    polutan:  v => v<=50?'Baik':v<=100?'Sedang':v<=199?'Tidak sehat sensitif':'Tidak sehat',
    temp:     v => v<=15?'Sangat dingin':v<=22?'Sejuk':v<=28?'Normal':v<=35?'Panas':'Sangat panas',
    humidity: v => v<=25?'Sangat kering':v<=39?'Kering':v<=60?'Optimal':v<=75?'Lembap':'Sangat lembap',
};

const RWH_TAB_LABEL = { jam:'per jam', hari:'per hari', bulan:'bulanan' };

let rwhTab        = 'jam';
let rwhChart      = null;
let currentChartType = 'bar';

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
        if (metricKey === 'polutan') return parseFloat(item.polutan ?? item.gas ?? 0) || 0;
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

    if (rwhChart) {
    rwhChart.destroy();
    rwhChart = null;
}

    const ctx = document
    .getElementById('rwhChart')
    .getContext('2d');

rwhChart = new Chart(ctx, {
        type: currentChartType,
        data: {
            labels,
            datasets: [{

                data: values,

                backgroundColor:
                    currentChartType === 'bar'
                    ? values.map(v => RWH_COLOR[metric](v))
                    : (() => {

                        const gradient =
                            ctx.createLinearGradient(0, 0, 0, 260);

                        gradient.addColorStop(
                            0,
                            'rgba(16,185,129,0.25)'
                        );

                        gradient.addColorStop(
                            1,
                            'rgba(16,185,129,0.02)'
                        );

                        return gradient;
                    })(),

                borderColor:
                    metric === 'aqhi'
                    ? RWH_COLOR.aqhi(values[values.length - 1])
                    : '#10b981',

                borderWidth:
                    currentChartType === 'line'
                    ? 3
                    : 0,

                tension: 0.4,

                fill:
                    currentChartType === 'line',

                pointRadius:
                    currentChartType === 'line'
                    ? 4
                    : 0,

                pointHoverRadius:
                    currentChartType === 'line'
                    ? 6
                    : 0,

                pointBackgroundColor:
                    metric === 'aqhi'
                    ? RWH_COLOR.aqhi(values[values.length - 1])
                    : '#10b981',

                borderRadius:
                    currentChartType === 'bar'
                    ? 4
                    : 0,

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

// ============================================================
// TOGGLE BAR / LINE
// ============================================================

function setChartType(type, btnEl)
{
    currentChartType = type;

    document.querySelectorAll('.graph-btn')
        .forEach(btn => btn.classList.remove('active'));

    btnEl.classList.add('active');

    rwhRender();
}

rwhRender();

// ============================================================
// TIMELINE PER JAM
// ============================================================
function getAqhiLabelShort(v) {
    if (v <= 3)  return 'Low';
    if (v <= 6)  return 'Moderate';
    if (v <= 10) return 'High';
    return 'Very High';
}

function getAqhiLabelExport(v) {
    if (v <= 3)  return 'Low';
    if (v <= 6)  return 'Moderate';
    if (v <= 10) return 'High';
    return 'Very High';
}

function formatHour(index) {
    const h = index % 24;
    return (h < 10 ? '0' : '') + h + ':00';
}

function generateDummyForecast() {
    const baseAqhi = [2,2,1,1,2,3,4,5,6,7,7,8,8,9,8,7,6,5,5,4,3,3,2,2];
    const now      = new Date().getHours();
    return baseAqhi.map((aqhi, i) => ({ aqhi, time: formatHour((now + i) % 24) }));
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
    if (rwhVal)  rwhVal.textContent  = item.aqhi + ' AQHI';
    if (rwhMeta) rwhMeta.textContent = jam + ' · forecast';
}

function renderTimeline() {
    const container = document.getElementById('fc-timeline');
    if (!container) return;

    const data   = window.DASH.forecastHourly.length > 0 ? window.DASH.forecastHourly : generateDummyForecast();
    const maxVal = Math.max(...data.map(d => d.aqhi ?? d.aqi));
    const BAR_MAX  = 52;
    const nowHour  = new Date().getHours();

    container.innerHTML = '';

    const nowItem  = data[0];
    const nowBadge = document.getElementById('timeline-now-badge');
    if (nowBadge && nowItem) {
        const nowVal   = parseFloat(nowItem.aqhi ?? nowItem.aqi) || 0;
        const nowColor = getAqhiColor(nowVal);
        nowBadge.textContent       = nowVal + ' AQHI sekarang';
        nowBadge.style.color       = nowColor;
        nowBadge.style.borderColor = nowColor + '55';
    }

    data.forEach((item, i) => {
        const aqhi  = parseFloat(item.aqhi ?? item.aqi) || 0;
        const jam      = item.time || item.jam || formatHour(i);
        const color = getAqhiColor(aqhi);
        const barH  = Math.max(4, Math.round((aqhi / Math.max(maxVal, 11)) * BAR_MAX));
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
            <span class="fc-h-val" style="color:${color};">${aqhi}</span>
            <span class="fc-h-label">${getAqhiLabelShort(aqhi)}</span>
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

        const aqi  = parseFloat(item.aqi)  || 0;
        const aqhi = parseFloat(item.aqhi) || 0;

        return {
            'Bulan'              : item.bulan || (rawDate ? new Date(rawDate).toLocaleString('id-ID', { month:'long', year:'numeric' }) : ''),
            'Hari & Tanggal'     : hariTanggal,
            'Jam'                : jam,
            'Status'             : aqhi > 0 ? getAqhiLabelExport(aqhi) : getAqiLabel(aqi),
            'AQHI'               : aqhi,
            'AQI'                : aqi,
            'PM2.5 (µg/m³)'     : parseFloat(item.pm25)  || 0,
            'NO2 (ppb)'          : parseFloat(item.no2)   || 0,
            'O3 (ppb)'           : parseFloat(item.o3)    || 0,
            'PM10 (µg/m³)'      : parseFloat(item.pm10)  || 0,
            'PM1 (µg/m³)'       : parseFloat(item.pm1)   || 0,
            'polutan/VOC (ppm)'  : parseFloat(item.polutan ?? item.gas) || 0,
            'Temp. (°C)'         : parseFloat(item.suhu ?? item.temp)   || 0,
            'Humi. (%)'          : parseFloat(item.kelembaban ?? item.humidity) || 0,
            'Lokasi'             : item.location || window.DASH.latestData?.location || 'Ruang Utama',
        };
    });

    if (rows.length === 0) { alert('Tidak ada data untuk diekspor.'); return; }

    const ws = XLSX.utils.json_to_sheet(rows);
        ws['!cols'] = [
            {wch:16}, // Bulan
            {wch:22}, // Hari & Tanggal
            {wch:8},  // Jam
            {wch:14}, // Status
            {wch:8},  // AQHI
            {wch:8},  // AQI
            {wch:14}, // PM2.5
            {wch:10}, // NO2
            {wch:10}, // O3
            {wch:14}, // PM10
            {wch:14}, // PM1
            {wch:16}, // polutan/VOC
            {wch:12}, // Temp
            {wch:10}, // Humi
            {wch:16}, // Lokasi
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

// ============================================================
// HISTORICAL DATA TOGGLE
// ============================================================

let hdView = 'jam';
let hdEntries = 10;

function hdSetView(view, btnEl) {
    hdView = view;
    document.querySelectorAll('.hd-tab').forEach(b => b.classList.remove('active'));
    btnEl.classList.add('active');

    const thPrimary = document.getElementById('th-primary');
    if (thPrimary) thPrimary.textContent = view === 'jam' ? 'TIME ▼' : 'DATE ▼';

    document.querySelectorAll('#hd-tbody tr').forEach(row => {
        row.style.display = row.dataset.view === view ? '' : 'none';
    });

    document.getElementById('hd-search').value = '';
    document.getElementById('hd-clear').style.display = 'none';
    document.getElementById('hd-empty').style.display = 'none';
    hdApplyEntries();
}

function hdFilter() {
    const kw = document.getElementById('hd-search').value.toLowerCase().trim();
    document.getElementById('hd-clear').style.display = kw ? 'block' : 'none';
    let visible = 0;
    document.querySelectorAll('#hd-tbody tr').forEach(row => {
        if (row.dataset.view !== hdView) return;
        const match =
            row.textContent
                .toLowerCase()
                .includes(kw);

        row.dataset.filtered =
            match ? 'false' : 'true';

        if (match) visible++;
    });

    hdApplyEntries();

    const emptyEl = document.getElementById('hd-empty');
    emptyEl.style.display = visible === 0 ? 'flex' : 'none';
    document.getElementById('hd-empty-kw').textContent = kw;
    hdUpdateCount(visible);
}

function hdClear() {
    document.getElementById('hd-search').value = '';
    document.getElementById('hd-clear').style.display = 'none';
    document.getElementById('hd-empty').style.display = 'none';
    document.querySelectorAll('#hd-tbody tr[data-view="' + hdView + '"]').forEach(r => r.style.display = '');
    hdApplyEntries();
}

function hdUpdateCount(count) {
    const n = count !== undefined ? count :
        Array.from(document.querySelectorAll('#hd-tbody tr[data-view="' + hdView + '"]'))
            .filter(r => r.style.display !== 'none').length;
    document.getElementById('hd-count').textContent = n + ' data';
}

document.addEventListener('DOMContentLoaded', () => {
    hdSetView('jam', document.getElementById('btn-jam'));
});

// ============================================================
// SHOW ENTRIES
// ============================================================

function hdChangeEntries()
{
    hdEntries = parseInt(
        document.getElementById('hd-entries-select').value
    );

    hdApplyEntries();
}

function hdApplyEntries()
{
    const rows = Array.from(
        document.querySelectorAll(
            '#hd-tbody tr[data-view="' + hdView + '"]'
        )
    );

    let visibleIndex = 0;

    rows.forEach(row => {

        // skip hidden hasil filter
        if (
            row.dataset.filtered === 'true'
        ) {
            row.style.display = 'none';
            return;
        }

        if (visibleIndex < hdEntries) {

            row.style.display = '';

        } else {

            row.style.display = 'none';
        }

        visibleIndex++;
    });

    const totalRows = rows.filter(
        r => r.dataset.filtered !== 'true'
    ).length;

    const showingTo =
        Math.min(totalRows, hdEntries);

    document.getElementById(
        'hd-footer-info'
    ).textContent =
        `Showing 1 to ${showingTo} of ${totalRows} entries`;

    hdUpdateCount(
        Math.min(visibleIndex, hdEntries)
    );
}

// ============================================================
// HD EXPORT: copy / csv / excel / pdf / print
// ============================================================
function hdGetVisibleRows() {
    return Array.from(
        document.querySelectorAll(`#hd-tbody tr[data-view="${hdView}"]`)
    ).filter(r => r.style.display !== 'none');
}

function hdGetHeaders() {
    return Array.from(document.querySelectorAll('.hd-tbl thead th'))
        .map(th => th.innerText.trim());
}

function hdRowToArray(tr) {
    return Array.from(tr.querySelectorAll('td')).map(td => td.innerText.trim());
}

function hdExport(type) {
    const headers = hdGetHeaders();
    const rows    = hdGetVisibleRows().map(hdRowToArray);

    if (type === 'copy') {
        const text = [headers, ...rows].map(r => r.join('\t')).join('\n');
        navigator.clipboard.writeText(text).then(() => {
            // toast feedback
            const btn = document.querySelector('.hd-btn-copy');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-check"></i>';
            btn.style.color = '#10b981';
            setTimeout(() => { btn.innerHTML = orig; btn.style.color = ''; }, 1500);
        });
        return;
    }

    if (type === 'csv') {
        const escape = v => `"${String(v).replace(/"/g,'""')}"`;
        const csv    = [headers, ...rows].map(r => r.map(escape).join(',')).join('\n');
        const blob   = new Blob(['\ufeff' + csv], { type: 'text/csv;charset=utf-8;' });
        const url    = URL.createObjectURL(blob);
        const a      = Object.assign(document.createElement('a'), { href: url, download: `riwayat-udara-${_hdDateStr()}.csv` });
        a.click(); URL.revokeObjectURL(url);
        return;
    }

    if (type === 'excel') {
        // pakai fungsi exportToExcel() yang sudah ada
        exportToExcel();
        return;
    }

    if (type === 'pdf') {
        // Requires jsPDF — load dynamically jika belum ada
        const load = cb => {
            if (window.jspdf) { cb(); return; }
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
            s.onload = () => {
                const s2 = document.createElement('script');
                s2.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js';
                s2.onload = cb;
                document.head.appendChild(s2);
            };
            document.head.appendChild(s);
        };

        load(() => {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
            doc.setFontSize(12);
            doc.text('Air Quality Historical Data', 40, 36);
            doc.autoTable({
                head: [headers],
                body: rows,
                startY: 50,
                styles: { fontSize: 7, cellPadding: 3 },
                headStyles: { fillColor: [16, 185, 129], textColor: 255, fontStyle: 'bold' },
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
                body { font-family: sans-serif; font-size: 11px; }
                table { border-collapse: collapse; width: 100%; }
                th { background: #10b981; color: #fff; padding: 6px 8px; text-align: left; }
                td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
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
        return;
    }
}

function _hdDateStr() {
    const d = new Date();
    return String(d.getDate()).padStart(2,'0') + '-' +
           String(d.getMonth()+1).padStart(2,'0') + '-' +
           d.getFullYear();
}
</script>


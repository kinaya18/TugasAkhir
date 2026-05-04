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
            <span class="popup-big-unit"  id="popup-big-unit">ppm</span>
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

        <p class="popup-avg-title" id="popup-avg-title">
            Average <span style="color:#94a3b8;font-size:12px;"></span>
        </p>
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

<script>
// popupData diisi ulang oleh renderDashboard() di _section_hero.php
const popupData = {
    NOx:  { title:'NOx / VOC', subtitle:'Gas Iritan',        icon:'fa-wind',       iconColor:'#22c55e', value:'--', unit:'ppm',   status:'--', statusClass:'status-good',     needlePct:0, avgTitle:'NOx Average',   avgUnit:'ppm',   avg1:'--', avg8:'--', avg12:'--' },
    pm25: { title:'PM 2.5',    subtitle:'Partikel Halus',    icon:'fa-smog',       iconColor:'#22c55e', value:'--', unit:'µg/m³', status:'--', statusClass:'status-good',     needlePct:0, avgTitle:'PM2.5 Average', avgUnit:'µg/m³', avg1:'--', avg8:'--', avg12:'--' },
    pm10: { title:'PM 10',     subtitle:'Partikel Kasar',    icon:'fa-circle-dot', iconColor:'#f59e0b', value:'--', unit:'µg/m³', status:'--', statusClass:'status-moderate', needlePct:0, avgTitle:'PM10 Average',  avgUnit:'µg/m³', avg1:'--', avg8:'--', avg12:'--' },
    aqi:  { title:'AQI',       subtitle:'Air Quality Index', icon:'fa-gauge-high', iconColor:'#f59e0b', value:'--', unit:'AQI',   status:'--', statusClass:'status-moderate', needlePct:0, avgTitle:'AQI Average',   avgUnit:'index', avg1:'--', avg8:'--', avg12:'--' }
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

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closePopup(); closeAqiPopup(); }
});
</script>
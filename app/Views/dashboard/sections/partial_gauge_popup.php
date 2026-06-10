<!-- ===================== GAUGE POPUP ===================== -->
<div id="popup-overlay" class="popup-overlay" onclick="closePopupOutside(event)">
    <div class="popup-box">
        <button class="popup-close" onclick="closePopup()">&#x2715;</button>

        <div class="popup-header">
            <div class="popup-icon-wrap" id="popup-icon-wrap">
                <i id="popup-icon" class="fa-solid fa-wind"></i>
            </div>
            <div>
                <h2 class="popup-title" id="popup-title">polutan</h2>
                <p class="popup-subtitle" id="popup-subtitle">Gas Iritan</p>
            </div>
        </div>

        <div class="popup-value-row">
            <span class="popup-big-value" id="popup-big-value">--</span>
            <span class="popup-big-unit"  id="popup-big-unit">ppm</span>
        </div>

        <div class="popup-status-badge" id="popup-status-badge">--</div>

        <div class="popup-scale">
            <div class="popup-scale-bar" id="popup-scale-bar">
                <div class="scale-seg seg-good"></div>
                <div class="scale-seg seg-moderate"></div>
                <div class="scale-seg seg-poor"></div>
                <div class="scale-seg seg-unhealthy"></div>
                <div class="scale-seg seg-severe"></div>
                <div class="scale-seg seg-hazardous"></div>
                <div class="scale-needle" id="scale-needle"></div>
            </div>
            <div class="popup-scale-labels" id="popup-scale-labels">
                <span>Good</span><span>Moderate</span><span>Sensitive</span>
                <span>Unhealthy</span><span>V.Unhealthy</span><span>Hazardous</span>
            </div>
            <div class="popup-scale-numbers" id="popup-scale-numbers">
                <span>0</span><span>50</span><span>100</span>
                <span>150</span><span>200</span><span>300</span><span>500+</span>
            </div>
        </div>

        <style>
            /* ── AQI 6-level scale colors (matches Image 1) ── */
            .scale-seg.seg-good        { background: #22c55e !important; }
            .scale-seg.seg-moderate    { background: #eab308 !important; }
            .scale-seg.seg-poor        { background: #f97316 !important; }
            .scale-seg.seg-unhealthy   { background: #ef4444 !important; }
            .scale-seg.seg-severe      { background: #a855f7 !important; }
            .scale-seg.seg-hazardous   { background: #7f1d1d !important; }
        </style>

    </div>
</div>

<script>
const popupData = {
    polutan: { title:'Polutan',  subtitle:'Gas Iritan',         icon:'fa-wind',       iconColor:'#22c55e', unit:'ppm'   },
    pm25:    { title:'PM 2.5',   subtitle:'Partikel Halus',     icon:'fa-smog',       iconColor:'#22c55e', unit:'µg/m³' },
    pm10:    { title:'PM 10',    subtitle:'Partikel Kasar',     icon:'fa-circle-dot', iconColor:'#f59e0b', unit:'µg/m³' },
    pm1:     { title:'PM 1',     subtitle:'Partikel Ultrafine', icon:'fa-circle-dot', iconColor:'#a855f7', unit:'µg/m³' },
    aqi:     { title:'AQI',      subtitle:'Air Quality Index',  icon:'fa-gauge-high', iconColor:'#f59e0b', unit:'AQI'   },
};

function openPopup(key) {
    const d = popupData[key];

    const scaleNumbers = document.getElementById('popup-scale-numbers');

    // ── Re-sync nilai dari latestData ─────────────────────────────
    const ld = window.DASH && window.DASH.latestData;
    if (ld) {
        const rawVal = parseFloat(
            key === 'polutan' ? (ld.polutan ?? ld.gas)
          : key === 'pm25'   ? ld.pm25
          : key === 'pm10'   ? ld.pm10
          : key === 'pm1'    ? ld.pm1
          : key === 'aqi'    ? ld.aqi
          : 0
        ) || 0;

        d.value = rawVal;

        switch(key) {

            case 'pm25':
                d.status = getPm25Status(rawVal);
                break;

            case 'pm10':
                d.status = getPm10Status(rawVal);
                break;

            case 'pm1':
                d.status = getPm1Status(rawVal);
                break;

            default:
                d.status = getAqiLabel(rawVal).toUpperCase();
        }

        d.statusClass = getStatusClass(d.status);
        d.needlePct = getNeedlePosition(d.status);
    }

    switch(key) {

      case 'pm25':
          scaleNumbers.innerHTML = `
              <span>0</span>
              <span>12</span>
              <span>35</span>
              <span>55</span>
              <span>150</span>
              <span>250</span>
              <span>250+</span>
          `;
          break;

      case 'pm10':
          scaleNumbers.innerHTML = `
              <span>0</span>
              <span>54</span>
              <span>154</span>
              <span>254</span>
              <span>354</span>
              <span>424</span>
              <span>424+</span>
          `;
          break;

      case 'pm1':
          scaleNumbers.innerHTML = `
              <span>0</span>
              <span>10</span>
              <span>25</span>
              <span>50</span>
              <span>100</span>
              <span>200</span>
              <span>200+</span>
          `;
          break;

      default:
          scaleNumbers.innerHTML = `
              <span>0</span>
              <span>50</span>
              <span>100</span>
              <span>150</span>
              <span>200</span>
              <span>300</span>
              <span>500+</span>
          `;
  }

    // ── Render ke DOM ─────────────────────────────────────────────
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

<!-- pop up guide -->
<!-- ===================== GUIDE POPUP (AQI & AQHI) ===================== -->

<div class="guide-overlay" id="guideOverlay" onclick="closeGuideOnOverlay(event)">
  <div class="guide-popup" id="guidePopup">

    <div class="guide-popup-topbar" id="guidePopupTopbar"></div>

    <div class="guide-popup-inner">

      <!-- HEAD -->
      <div class="guide-popup-head">
        <div class="guide-popup-head-left">
          <div class="guide-popup-dot" id="guidePopupDot"></div>
          <div>
            <div class="guide-popup-title" id="guidePopupTitle"></div>
            <div class="guide-popup-range" id="guidePopupRange"></div>
          </div>
        </div>
        <button class="guide-popup-close" onclick="closeGuidePopup()">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>

      <!-- DESC -->
      <p class="guide-popup-desc" id="guidePopupDesc"></p>

      <!-- PM BADGE -->
      <span class="guide-popup-pm" id="guidePopupPm"></span>

      <!-- SCALE BAR -->
      <div class="guide-scale-wrap">
        <div class="guide-scale-label-row" id="guideScaleLabels"></div>
        <div class="guide-scale-bar" id="guideScaleBar">
          <!-- segments injected by JS, needle kept last -->
          <div class="guide-scale-needle" id="guideScaleNeedle"></div>
        </div>
      </div>

      <!-- DO / DONT -->
      <div class="guide-dd-grid">
        <div class="guide-dd-box guide-dd-do">
          <div class="guide-dd-title">
            <i class="fa-solid fa-circle-check"></i> DO'S
          </div>
          <div id="guideDoList"></div>
        </div>
        <div class="guide-dd-box guide-dd-dont">
          <div class="guide-dd-title">
            <i class="fa-solid fa-circle-xmark"></i> DON'TS
          </div>
          <div id="guideDontList"></div>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
/* ── GUIDE POPUP STYLES ── */
.guide-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 22, 36, .45);
  backdrop-filter: blur(6px);
  -webkit-backdrop-filter: blur(6px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  opacity: 0;
  pointer-events: none;
  transition: opacity .22s ease;
}
.guide-overlay.active {
  opacity: 1;
  pointer-events: all;
}

.guide-popup {
  background: #ffffff;
  border-radius: 20px;
  width: 100%;
  max-width: 540px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 16px 48px rgba(15,22,36,.14), 0 4px 16px rgba(15,22,36,.08);
  border: 1.5px solid #e2e6ef;
  transform: scale(.94) translateY(12px);
  transition: transform .25s cubic-bezier(.34, 1.36, .64, 1);
}
.guide-overlay.active .guide-popup {
  transform: scale(1) translateY(0);
}

/* scrollbar */
.guide-popup::-webkit-scrollbar { width: 4px; }
.guide-popup::-webkit-scrollbar-track { background: transparent; }
.guide-popup::-webkit-scrollbar-thumb { background: #e2e6ef; border-radius: 99px; }

.guide-popup-topbar {
  height: 5px;
  width: 100%;
  border-radius: 20px 20px 0 0;
  flex-shrink: 0;
}

.guide-popup-inner {
  padding: 20px 22px 24px;
}

/* head */
.guide-popup-head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 14px;
}
.guide-popup-head-left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.guide-popup-dot {
  width: 42px; height: 42px;
  border-radius: 11px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}
.guide-popup-title {
  font-size: 16px;
  font-weight: 700;
  color: #0f1624;
  line-height: 1.25;
}
.guide-popup-range {
  font-family: 'DM Mono', 'Courier New', monospace;
  font-size: 11px;
  color: #9aa3bb;
  margin-top: 2px;
}
.guide-popup-close {
  width: 32px; height: 32px;
  border-radius: 8px;
  border: 1.5px solid #e2e6ef;
  background: #f7f8fc;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  color: #5a6480;
  font-size: 13px;
  flex-shrink: 0;
  transition: background .15s, color .15s;
}
.guide-popup-close:hover {
  background: #e2e6ef;
  color: #0f1624;
}

/* desc */
.guide-popup-desc {
  font-size: 13px;
  line-height: 1.68;
  color: #5a6480;
  margin-bottom: 12px;
}

/* pm badge */
.guide-popup-pm {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 7px;
  font-size: 11.5px;
  font-weight: 600;
  font-family: 'DM Mono', 'Courier New', monospace;
  margin-bottom: 16px;
}

/* scale */
.guide-scale-wrap {
  margin-bottom: 18px;
}
.guide-scale-label-row {
  display: flex;
  justify-content: space-between;
  font-size: 9.5px;
  color: #9aa3bb;
  font-weight: 500;
  margin-bottom: 5px;
}
.guide-scale-bar {
  position: relative;
  height: 8px;
  border-radius: 99px;
  display: flex;
  gap: 2px;
  overflow: visible;
}
.guide-scale-seg {
  flex: 1;
  height: 100%;
  border-radius: 2px;
  opacity: .3;
  transition: opacity .2s;
}
.guide-scale-seg.active {
  opacity: 1;
}
.guide-scale-needle {
  position: absolute;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 14px; height: 14px;
  background: white;
  border-radius: 50%;
  border: 3px solid #ccc;
  box-shadow: 0 2px 8px rgba(0,0,0,.18);
  transition: left .4s cubic-bezier(.34, 1.36, .64, 1);
  z-index: 2;
}

/* do / dont grid */
.guide-dd-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.guide-dd-box {
  border-radius: 11px;
  padding: 13px;
  border: 1.5px solid;
}
.guide-dd-do   { background: #f0fdf4; border-color: #bbf7d0; }
.guide-dd-dont { background: #fef2f2; border-color: #fecaca; }

.guide-dd-title {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11.5px;
  font-weight: 700;
  letter-spacing: .07em;
  margin-bottom: 10px;
}
.guide-dd-do   .guide-dd-title { color: #15803d; }
.guide-dd-dont .guide-dd-title { color: #b91c1c; }
.guide-dd-do   .guide-dd-title i { color: #22c55e; }
.guide-dd-dont .guide-dd-title i { color: #ef4444; }

.guide-dd-item {
  display: flex;
  align-items: flex-start;
  gap: 7px;
  margin-bottom: 7px;
  font-size: 11.5px;
  line-height: 1.5;
}
.guide-dd-item:last-child { margin-bottom: 0; }

.guide-dd-bullet {
  width: 6px; height: 6px;
  border-radius: 50%;
  flex-shrink: 0;
  margin-top: 5px;
}
.guide-dd-do   .guide-dd-bullet { background: #22c55e; }
.guide-dd-dont .guide-dd-bullet { background: #ef4444; }
.guide-dd-do   .guide-dd-item   { color: #166534; }
.guide-dd-dont .guide-dd-item   { color: #991b1b; }

/* responsive */
@media (max-width: 480px) {
  .guide-dd-grid { grid-template-columns: 1fr; }
  .guide-popup-inner { padding: 16px 16px 20px; }
}
</style>

<script>
/* ── GUIDE POPUP DATA ── */
const guidePopupData = {

  /* ── AQHI ── */
  'aqhi-low': {
    title: 'Low — Risiko Rendah',
    range: 'AQHI 1–3',
    color: '#2563eb', dotBg: '#eff6ff', pmBg: '#eff6ff', pmColor: '#2563eb',
    icon: '😊',
    desc: 'Kualitas udara sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak. Semua orang dapat menikmati aktivitas luar ruangan dengan bebas.',
    pm: 'Risiko Kesehatan: Sangat Rendah',
    scaleColors: ['#2563eb','#ca8a04','#ea580c','#dc2626'],
    scaleLabels: ['Low','Moderate','High','Very High'],
    activeIdx: 0,
    dos: [
      'Lakukan semua aktivitas luar ruangan dengan bebas',
      'Biarkan ventilasi alami terbuka lebar',
      'Catat kondisi sebagai baseline referensi',
      'Ajak anak-anak beraktivitas di luar ruangan',
      'Matikan air purifier untuk hemat energi'
    ],
    donts: [
      'Jangan abaikan pemeliharaan ventilasi rutin',
      'Jangan biarkan sumber polutan baru masuk',
      'Jangan memasak tanpa exhaust fan aktif',
      'Jangan buang kesempatan udara segar ini'
    ]
  },

  'aqhi-moderate': {
    title: 'Moderate — Risiko Sedang',
    range: 'AQHI 4–6',
    color: '#ca8a04', dotBg: '#fef9c3', pmBg: '#fef9c3', pmColor: '#a16207',
    icon: '😐',
    desc: 'Kualitas udara dapat diterima namun mulai mempengaruhi kelompok sensitif. Penderita asma atau penyakit jantung mungkin mengalami gejala ringan saat aktivitas intens di luar ruangan.',
    pm: 'Risiko Kesehatan: Sedang',
    scaleColors: ['#16a34a','#ca8a04','#ea580c','#dc2626'],
    scaleLabels: ['Low','Moderate','High','Very High'],
    activeIdx: 1,
    dos: [
      'Kurangi durasi aktivitas berat di luar ruangan',
      'Nyalakan air purifier mode rendah',
      'Periksa kondisi filter AC secara berkala',
      'Sediakan air minum yang cukup',
      'Monitor gejala pada penderita asma'
    ],
    donts: [
      'Jangan bakar dupa atau lilin aromaterapi',
      'Jangan lakukan olahraga intensitas tinggi di luar',
      'Jangan semprotkan aerosol dalam ruangan',
      'Jangan biarkan penderita asma tanpa inhaler'
    ]
  },

  'aqhi-high': {
    title: 'High — Risiko Tinggi',
    range: 'AQHI 7–10',
    color: '#ea580c', dotBg: '#ffedd5', pmBg: '#ffedd5', pmColor: '#c2410c',
    icon: '😷',
    desc: 'Udara berbahaya bagi kelompok sensitif. Semua penderita penyakit pernapasan dan jantung harus membatasi waktu di luar ruangan. Orang sehat pun sebaiknya mengurangi aktivitas berat.',
    pm: 'Risiko Kesehatan: Tinggi',
    scaleColors: ['#16a34a','#ca8a04','#ea580c','#dc2626'],
    scaleLabels: ['Low','Moderate','High','Very High'],
    activeIdx: 2,
    dos: [
      'Batasi aktivitas di luar ruangan',
      'Nyalakan air purifier pada mode tinggi',
      'Siapkan inhaler bagi penderita asma',
      'Gunakan masker jika harus keluar',
      'Pastikan ventilasi ruangan optimal'
    ],
    donts: [
      'Jangan izinkan penderita asma berolahraga di luar',
      'Jangan biarkan anak-anak bermain lama di luar',
      'Jangan abaikan gejala iritasi tenggorokan',
      'Jangan tunda konsultasi dokter jika gejala muncul'
    ]
  },

  'aqhi-very-high': {
    title: 'Very High — Risiko Sangat Tinggi',
    range: 'AQHI 10+',
    color: '#dc2626', dotBg: '#fee2e2', pmBg: '#fee2e2', pmColor: '#b91c1c',
    icon: '🚨',
    desc: 'Kondisi darurat kesehatan. Semua orang berisiko mengalami dampak serius. Hindari semua aktivitas luar ruangan dan pastikan ruangan tersegel dengan baik dari udara luar.',
    pm: 'Risiko Kesehatan: Sangat Tinggi',
    scaleColors: ['#16a34a','#ca8a04','#ea580c','#dc2626'],
    scaleLabels: ['Low','Moderate','High','Very High'],
    activeIdx: 3,
    dos: [
      'Tinggal di dalam ruangan dengan jendela tertutup',
      'Nyalakan semua air purifier ke mode maksimal',
      'Hubungi layanan kesehatan jika gejala parah',
      'Gunakan masker N95 jika harus keluar',
      'Evakuasi penderita asma ke tempat aman'
    ],
    donts: [
      'Jangan keluar ruangan tanpa masker N95',
      'Jangan lakukan aktivitas fisik apapun di luar',
      'Jangan tunda penanganan gejala pernapasan',
      'Jangan abaikan peringatan pihak berwenang'
    ]
  },

  /* ── AQI ── */
  'aqi-good': {
    title: 'Good — Udara Baik',
    range: 'AQI 0–50 · PM2.5 < 12 µg/m³',
    color: '#22c55e', dotBg: '#dcfce7', pmBg: '#dcfce7', pmColor: '#15803d',
    icon: '✅',
    desc: 'Kualitas udara dalam ruangan sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak. Kondisi ideal untuk semua aktivitas.',
    pm: 'PM2.5 < 12 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 0,
    dos: [
      'Lakukan aktivitas normal di dalam ruangan',
      'Biarkan ventilasi alami terbuka',
      'Matikan air purifier untuk hemat energi',
      'Catat kondisi ini sebagai baseline sensor',
      'Manfaatkan kondisi baik untuk bersih-bersih'
    ],
    donts: [
      'Jangan abaikan pemeliharaan ventilasi rutin',
      'Jangan biarkan sumber polutan baru masuk',
      'Jangan memasak tanpa exhaust fan aktif',
      'Jangan buang kondisi baik tanpa dokumentasi'
    ]
  },

  'aqi-moderate': {
    title: 'Moderate — Udara Sedang',
    range: 'AQI 51–100 · PM2.5 12–35 µg/m³',
    color: '#eab308', dotBg: '#fef9c3', pmBg: '#fef9c3', pmColor: '#a16207',
    icon: '⚠️',
    desc: 'Kualitas udara cukup dapat diterima namun mulai menurun. Penderita asma yang sangat sensitif mungkin mengalami gejala ringan saat berada di dalam ruangan.',
    pm: 'PM2.5 12–35 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 1,
    dos: [
      'Nyalakan air purifier di mode rendah',
      'Buka jendela bila udara luar lebih baik',
      'Periksa filter AC dan bersihkan jika kotor',
      'Batasi aktivitas memasak yang menghasilkan asap',
      'Monitor level PM2.5 setiap jam'
    ],
    donts: [
      'Jangan bakar dupa atau lilin aromaterapi',
      'Jangan semprotkan pengharum ruangan aerosol',
      'Jangan biarkan penderita asma beraktivitas berat',
      'Jangan abaikan kenaikan tren nilai AQI'
    ]
  },

  'aqi-sensitive': {
    title: 'Sensitive Groups — Tidak Sehat',
    range: 'AQI 101–150 · PM2.5 35–55 µg/m³',
    color: '#f97316', dotBg: '#ffedd5', pmBg: '#ffedd5', pmColor: '#c2410c',
    icon: '⚡',
    desc: 'Udara berbahaya bagi kelompok sensitif. Penderita asma, lansia, dan anak-anak berisiko mengalami gangguan pernapasan. Segera cari dan atasi sumber polutan.',
    pm: 'PM2.5 35–55 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 2,
    dos: [
      'Nyalakan air purifier di mode tinggi',
      'Segera cari dan identifikasi sumber polutan',
      'Anjurkan penderita asma siapkan inhaler',
      'Tingkatkan ventilasi dengan kipas angin',
      'Pindahkan anak-anak ke ruangan lebih bersih'
    ],
    donts: [
      'Jangan izinkan penderita asma beraktivitas fisik',
      'Jangan memasak dengan bahan yang berasap',
      'Jangan biarkan anak-anak dan lansia di ruangan lama',
      'Jangan tunda pencarian sumber masalah'
    ]
  },

  'aqi-unhealthy': {
    title: 'Unhealthy — Tidak Sehat',
    range: 'AQI 151–200 · PM2.5 55–150 µg/m³',
    color: '#ef4444', dotBg: '#fee2e2', pmBg: '#fee2e2', pmColor: '#b91c1c',
    icon: '🚫',
    desc: 'Seluruh penghuni ruangan mulai berisiko terkena dampak kesehatan. Tindakan perbaikan harus segera dilakukan. Jangan tunda evaluasi sistem ventilasi.',
    pm: 'PM2.5 55–150 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 3,
    dos: [
      'Evakuasi penderita asma ke ruangan lain',
      'Nyalakan semua air purifier ke mode maksimal',
      'Hubungi teknisi untuk cek sistem ventilasi',
      'Gunakan masker N95 di dalam ruangan',
      'Dokumentasikan waktu dan nilai AQI saat ini'
    ],
    donts: [
      'Jangan tinggalkan penderita asma tanpa pengawasan',
      'Jangan matikan air purifier dalam kondisi apapun',
      'Jangan tunda perbaikan sumber masalah',
      'Jangan biarkan lebih dari 30 menit tanpa tindakan'
    ]
  },

  'aqi-very-unhealthy': {
    title: 'Very Unhealthy — Sangat Tidak Sehat',
    range: 'AQI 201–300 · PM2.5 150–250 µg/m³',
    color: '#a855f7', dotBg: '#f3e8ff', pmBg: '#f3e8ff', pmColor: '#7e22ce',
    icon: '☣️',
    desc: 'Kondisi darurat kesehatan di dalam ruangan. Semua orang berisiko mengalami efek serius. Evakuasi segera dan hubungi pihak terkait untuk penanganan lebih lanjut.',
    pm: 'PM2.5 150–250 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 4,
    dos: [
      'Segera evakuasi semua penghuni ruangan',
      'Hubungi pihak terkait jika dicurigai kebocoran',
      'Gunakan masker N95/respirator',
      'Matikan semua sumber api di ruangan',
      'Catat semua langkah yang telah dilakukan'
    ],
    donts: [
      'Jangan tunda evakuasi meski hanya beberapa menit',
      'Jangan abaikan tanda-tanda sumber bahaya',
      'Jangan masuk kembali sebelum udara diperbaiki',
      'Jangan coba atasi sendiri tanpa alat proteksi'
    ]
  },

  'aqi-hazardous': {
    title: 'Hazardous — Berbahaya',
    range: 'AQI 301–500 · PM2.5 > 250 µg/m³',
    color: '#7f1d1d', dotBg: '#fecaca', pmBg: '#fecaca', pmColor: '#7f1d1d',
    icon: '☠️',
    desc: 'Kondisi udara sangat kritis dan mengancam jiwa. Semua orang harus segera keluar dari ruangan. Hubungi layanan darurat jika ada indikasi kebakaran atau kebocoran gas.',
    pm: 'PM2.5 > 250 µg/m³',
    scaleColors: ['#22c55e','#eab308','#f97316','#ef4444','#a855f7','#7f1d1d'],
    scaleLabels: ['Good','Moderate','Sensitive','Unhealthy','V.Unhealthy','Hazardous'],
    activeIdx: 5,
    dos: [
      'Evakuasi segera seluruh penghuni tanpa kecuali',
      'Hubungi 119 jika ada indikasi kebakaran',
      'Tutup pintu dan jendela untuk isolasi sementara',
      'Gunakan jalur evakuasi yang aman',
      'Tunggu instruksi dari pihak berwenang'
    ],
    donts: [
      'Jangan masuk kembali dalam kondisi apapun',
      'Jangan coba atasi sendiri tanpa alat proteksi',
      'Jangan gunakan lift — gunakan tangga darurat',
      'Jangan abaikan tanda fisik seperti asap atau bau'
    ]
  }
};

/* ── OPEN POPUP ── */
function openGuidePopup(key) {
  const d = guidePopupData[key];
  if (!d) return;

  // top bar
  document.getElementById('guidePopupTopbar').style.background = d.color;

  // dot / icon
  const dot = document.getElementById('guidePopupDot');
  dot.style.background = d.dotBg;
  dot.textContent = d.icon;

  // title & range
  document.getElementById('guidePopupTitle').textContent = d.title;
  document.getElementById('guidePopupRange').textContent = d.range;

  // desc
  document.getElementById('guidePopupDesc').textContent = d.desc;

  // pm badge
  const pm = document.getElementById('guidePopupPm');
  pm.textContent = d.pm;
  pm.style.background   = d.pmBg;
  pm.style.color        = d.pmColor;
  pm.style.border       = '1.5px solid ' + d.pmColor + '44';

  // scale bar — rebuild segments
  const bar    = document.getElementById('guideScaleBar');
  const needle = document.getElementById('guideScaleNeedle');
  const labels = document.getElementById('guideScaleLabels');

  // remove old segs (keep needle)
  Array.from(bar.children).forEach(c => { if (c !== needle) bar.removeChild(c); });
  labels.innerHTML = '';

  d.scaleColors.forEach((c, i) => {
    const seg = document.createElement('div');
    seg.className = 'guide-scale-seg' + (i === d.activeIdx ? ' active' : '');
    seg.style.background = c;
    bar.insertBefore(seg, needle);

    const lbl = document.createElement('span');
    lbl.textContent = d.scaleLabels[i];
    labels.appendChild(lbl);
  });

  // needle
  const pct = ((d.activeIdx + 0.5) / d.scaleColors.length) * 100;
  needle.style.left        = pct + '%';
  needle.style.borderColor = d.color;

  // do / dont
  document.getElementById('guideDoList').innerHTML =
    d.dos.map(t => `<div class="guide-dd-item"><span class="guide-dd-bullet"></span><span>${t}</span></div>`).join('');
  document.getElementById('guideDontList').innerHTML =
    d.donts.map(t => `<div class="guide-dd-item"><span class="guide-dd-bullet"></span><span>${t}</span></div>`).join('');

  document.getElementById('guideOverlay').classList.add('active');
  document.body.style.overflow = 'hidden';
}

/* ── CLOSE POPUP ── */
function closeGuidePopup() {
  document.getElementById('guideOverlay').classList.remove('active');
  document.body.style.overflow = '';
}

function closeGuideOnOverlay(e) {
  if (e.target === document.getElementById('guideOverlay')) closeGuidePopup();
}

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeGuidePopup();
});
</script>
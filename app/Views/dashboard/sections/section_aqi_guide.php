<!-- ===================== SECTION: AQI & AQHI GUIDE ===================== -->

<div class="mb-3"></div>

<!-- PAGE HEADER -->
<div class="guide-page-header">
  <div class="guide-header-left">
    <div class="guide-page-label">Air Quality Guide</div>
    <h2 class="guide-page-title">AQI &amp; AQHI Reference</h2>
    <p class="guide-page-sub">Klik kartu untuk melihat detail, do's &amp; don'ts.</p>
  </div>
</div>

<!-- ══ AQHI SECTION ══ -->
<div class="guide-section-wrap">
  <div class="guide-section-header">
    <div class="guide-section-header-left">
      <div class="guide-section-icon aqhi-icon"><i class="fa-solid fa-heart-pulse"></i></div>
      <div>
        <div class="guide-section-title">AQHI Guide</div>
        <div class="guide-section-desc">Air Quality Health Index — 4 tingkat risiko kesehatan</div>
      </div>
    </div>
    <span class="guide-section-tag aqhi-tag">4 Indeks</span>
  </div>

  <div class="guide-cards-grid guide-cols-4">

    <div class="guide-card guide-aqhi-low" onclick="openGuidePopup('aqhi-low')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">1 – 3</div>
      <div class="guide-card-title">Low</div>
      <div class="guide-card-desc">Aktivitas luar ruangan aman untuk semua orang.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqhi-mod" onclick="openGuidePopup('aqhi-moderate')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">4 – 6</div>
      <div class="guide-card-title">Moderate</div>
      <div class="guide-card-desc">Kelompok sensitif sebaiknya membatasi aktivitas berat.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqhi-high" onclick="openGuidePopup('aqhi-high')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">7 – 10</div>
      <div class="guide-card-title">High</div>
      <div class="guide-card-desc">Batasi aktivitas luar, terutama bagi penderita asma dan lansia.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqhi-vhigh" onclick="openGuidePopup('aqhi-very-high')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">10+</div>
      <div class="guide-card-title">Very High</div>
      <div class="guide-card-desc">Hindari semua aktivitas luar ruangan, kondisi berbahaya.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

  </div>
</div>

<!-- ══ AQI SECTION ══ -->
<div class="guide-section-wrap">
  <div class="guide-section-header">
    <div class="guide-section-header-left">
      <div class="guide-section-icon aqi-icon"><i class="fa-solid fa-wind"></i></div>
      <div>
        <div class="guide-section-title">AQI Guide</div>
        <div class="guide-section-desc">Air Quality Index — 6 tingkat kualitas udara indoor</div>
      </div>
    </div>
    <span class="guide-section-tag aqi-tag">6 Indeks</span>
  </div>

  <div class="guide-cards-grid guide-cols-6">

    <div class="guide-card guide-aqi-good" onclick="openGuidePopup('aqi-good')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">0 – 50</div>
      <div class="guide-card-title">Good</div>
      <div class="guide-card-desc">Udara bersih, aman untuk semua aktivitas.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqi-mod" onclick="openGuidePopup('aqi-moderate')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">51 – 100</div>
      <div class="guide-card-title">Moderate</div>
      <div class="guide-card-desc">Masih dapat diterima, sensitif perlu waspada.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqi-sens" onclick="openGuidePopup('aqi-sensitive')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">101 – 150</div>
      <div class="guide-card-title">Sensitive Groups</div>
      <div class="guide-card-desc">Kelompok rentan sebaiknya membatasi aktivitas.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqi-unhlth" onclick="openGuidePopup('aqi-unhealthy')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">151 – 200</div>
      <div class="guide-card-title">Unhealthy</div>
      <div class="guide-card-desc">Risiko meningkat untuk semua orang di ruangan.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqi-vunhlth" onclick="openGuidePopup('aqi-very-unhealthy')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">201 – 300</div>
      <div class="guide-card-title">Very Unhealthy</div>
      <div class="guide-card-desc">Peringatan kesehatan darurat, semua orang berisiko.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

    <div class="guide-card guide-aqi-haz" onclick="openGuidePopup('aqi-hazardous')">
      <div class="guide-card-stripe"></div>
      <div class="guide-card-range">301 – 500</div>
      <div class="guide-card-title">Hazardous</div>
      <div class="guide-card-desc">Kondisi kritis, segera tinggalkan ruangan.</div>
      <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
    </div>

  </div>
</div>

<style>
/* ── GUIDE SECTION STYLES ── */
.guide-page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 20px;
}
.guide-page-label {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: #9aa3bb;
  margin-bottom: 4px;
}
.guide-page-title {
  font-size: 22px;
  font-weight: 700;
  color: #0f1624;
  line-height: 1.15;
  margin: 0;
}
.guide-page-sub {
  font-size: 12.5px;
  color: #9aa3bb;
  margin-top: 3px;
}
.guide-src-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 14px;
  border: 1.5px solid #e2e6ef;
  border-radius: 99px;
  font-size: 12px;
  font-weight: 600;
  color: #5a6480;
  background: #ffffff;
  white-space: nowrap;
  flex-shrink: 0;
}
.guide-src-badge i { font-size: 10px; color: #9aa3bb; }

/* section wrapper */
.guide-section-wrap {
  background: #ffffff;
  border-radius: 16px;
  border: 1.5px solid #e2e6ef;
  box-shadow: 0 1px 3px rgba(15,22,36,.06);
  overflow: hidden;
  margin-bottom: 20px;
}
.guide-section-header {
  padding: 18px 20px 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.guide-section-header-left { display: flex; align-items: center; gap: 10px; }
.guide-section-icon {
  width: 34px; height: 34px;
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}
.guide-section-icon.aqhi-icon { background: #fef2f2; color: #dc2626; }
.guide-section-icon.aqi-icon  { background: #f0fdf4; color: #16a34a; }
.guide-section-title {
  font-size: 15px;
  font-weight: 700;
  color: #0f1624;
}
.guide-section-desc {
  font-size: 12px;
  color: #9aa3bb;
  margin-top: 1px;
}
.guide-section-tag {
  font-size: 11px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 6px;
  letter-spacing: .04em;
  white-space: nowrap;
}
.guide-section-tag.aqhi-tag { background: #fef2f2; color: #b91c1c; }
.guide-section-tag.aqi-tag  { background: #f0fdf4; color: #15803d; }

/* grid */
.guide-cards-grid {
  display: grid;
  padding: 14px 16px 18px;
  gap: 10px;
}
.guide-cols-4 { grid-template-columns: repeat(4, 1fr); }
.guide-cols-6 { grid-template-columns: repeat(6, 1fr); }

/* card base */
.guide-card {
  position: relative;
  border-radius: 12px;
  padding: 14px 13px 12px;
  cursor: pointer;
  border: 1.5px solid transparent;
  transition: transform .18s ease, box-shadow .18s ease;
  overflow: hidden;
}
.guide-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(15,22,36,.1);
}
.guide-card:active { transform: translateY(-1px); }

.guide-card-stripe {
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 4px;
  border-radius: 12px 12px 0 0;
}
.guide-card-range {
  font-family: 'DM Mono', 'Courier New', monospace;
  font-size: 10.5px;
  font-weight: 500;
  letter-spacing: .08em;
  margin-bottom: 9px;
  opacity: .7;
}
.guide-card-title {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 7px;
}
.guide-card-desc {
  font-size: 11px;
  line-height: 1.5;
  opacity: .8;
}
.guide-card-hint {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-top: 10px;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: .04em;
  opacity: .45;
}
.guide-card-hint i { font-size: 9px; }

/* ── AQHI colors ── */
.guide-aqhi-low   { background:#f0fdf4; color:#14532d; border-color:#bbf7d0; }
.guide-aqhi-low   .guide-card-stripe { background:#16a34a; }
.guide-aqhi-mod   { background:#fefce8; color:#713f12; border-color:#fde68a; }
.guide-aqhi-mod   .guide-card-stripe { background:#ca8a04; }
.guide-aqhi-high  { background:#fff7ed; color:#7c2d12; border-color:#fed7aa; }
.guide-aqhi-high  .guide-card-stripe { background:#ea580c; }
.guide-aqhi-vhigh { background:#fef2f2; color:#7f1d1d; border-color:#fecaca; }
.guide-aqhi-vhigh .guide-card-stripe { background:#dc2626; }

/* ── AQI colors ── */
.guide-aqi-good    { background:#f0fdf4; color:#14532d; border-color:#bbf7d0; }
.guide-aqi-good    .guide-card-stripe { background:#22c55e; }
.guide-aqi-mod     { background:#fefce8; color:#713f12; border-color:#fde68a; }
.guide-aqi-mod     .guide-card-stripe { background:#eab308; }
.guide-aqi-sens    { background:#fff7ed; color:#7c2d12; border-color:#fed7aa; }
.guide-aqi-sens    .guide-card-stripe { background:#f97316; }
.guide-aqi-unhlth  { background:#fef2f2; color:#7f1d1d; border-color:#fecaca; }
.guide-aqi-unhlth  .guide-card-stripe { background:#ef4444; }
.guide-aqi-vunhlth { background:#faf5ff; color:#4a1d96; border-color:#e9d5ff; }
.guide-aqi-vunhlth .guide-card-stripe { background:#a855f7; }
.guide-aqi-haz     { background:#fef2f2; color:#450a0a; border-color:#fca5a5; }
.guide-aqi-haz     .guide-card-stripe { background:#7f1d1d; }

/* responsive */
@media (max-width: 768px) {
  .guide-cols-4,
  .guide-cols-6 { grid-template-columns: repeat(2, 1fr); }
  .guide-page-header { flex-direction: column; align-items: flex-start; }
}
@media (max-width: 480px) {
  .guide-cols-4,
  .guide-cols-6 { grid-template-columns: 1fr 1fr; }
}
</style>
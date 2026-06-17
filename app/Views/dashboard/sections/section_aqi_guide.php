<?php
/**
 * SECTION 3 – AQI & AQHI GUIDE
 * -----------------------------------------------
 * Menampilkan kartu referensi kualitas udara:
 *  1. AQHI Guide — 4 tingkat risiko kesehatan
 *  2. AQI  Guide — 6 tingkat kualitas udara
 *
 * Setiap kartu dapat diklik untuk membuka popup detail
 * (do's & don'ts) melalui fungsi openGuidePopup() di JS.
 *
 * Dependensi:
 *  - section_guide.css  (stylesheet section ini)
 *  - section_guide.js   (fungsi openGuidePopup)
 *  - Font Awesome
 */
?>

<!-- ===================== SECTION 3: AQI & AQHI GUIDE ===================== -->

<div class="mb-3"></div>

<!-- PAGE HEADER -->
<div class="guide-page-header">
    <div class="guide-header-left">
        <div class="guide-page-label">Air Quality Guide</div>
        <h2 class="guide-page-title">AQI &amp; AQHI Reference</h2>
        <p class="guide-page-sub">Klik kartu untuk melihat detail, do's &amp; don'ts.</p>
    </div>
</div>


<!-- ─────────────────────────────────────────────────────────
     1. AQHI GUIDE
     4 tingkat risiko: Low / Moderate / High / Very High
───────────────────────────────────────────────────────────── -->
<div class="guide-section-wrap">

    <div class="guide-section-header">
        <div class="guide-section-header-left">
            <div class="guide-section-icon aqhi-icon">
                <i class="fa-solid fa-heart-pulse"></i>
            </div>
            <div>
                <div class="guide-section-title">AQHI Guide</div>
                <div class="guide-section-desc">Air Quality Health Index — 4 tingkat risiko kesehatan</div>
            </div>
        </div>
        <span class="guide-section-tag aqhi-tag">4 Indeks</span>
    </div>

    <div class="guide-cards-grid guide-cols-4">

        <!-- Low (1–3) -->
        <div class="guide-card guide-aqhi-low" onclick="openGuidePopup('aqhi-low')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">1 – 3</div>
            <div class="guide-card-title">Low</div>
            <div class="guide-card-desc">Aktivitas luar ruangan aman untuk semua orang.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Moderate (4–6) -->
        <div class="guide-card guide-aqhi-mod" onclick="openGuidePopup('aqhi-moderate')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">4 – 6</div>
            <div class="guide-card-title">Moderate</div>
            <div class="guide-card-desc">Kelompok sensitif sebaiknya membatasi aktivitas berat.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- High (7–10) -->
        <div class="guide-card guide-aqhi-high" onclick="openGuidePopup('aqhi-high')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">7 – 10</div>
            <div class="guide-card-title">High</div>
            <div class="guide-card-desc">Batasi aktivitas luar, terutama bagi penderita asma dan lansia.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Very High (10+) -->
        <div class="guide-card guide-aqhi-vhigh" onclick="openGuidePopup('aqhi-very-high')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">10+</div>
            <div class="guide-card-title">Very High</div>
            <div class="guide-card-desc">Hindari semua aktivitas luar ruangan, kondisi berbahaya.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

    </div>
</div>


<!-- ─────────────────────────────────────────────────────────
     2. AQI GUIDE
     6 tingkat kualitas udara: Good → Hazardous
───────────────────────────────────────────────────────────── -->
<div class="guide-section-wrap">

    <div class="guide-section-header">
        <div class="guide-section-header-left">
            <div class="guide-section-icon aqi-icon">
                <i class="fa-solid fa-wind"></i>
            </div>
            <div>
                <div class="guide-section-title">AQI Guide</div>
                <div class="guide-section-desc">Air Quality Index — 6 tingkat kualitas udara indoor</div>
            </div>
        </div>
        <span class="guide-section-tag aqi-tag">6 Indeks</span>
    </div>

    <div class="guide-cards-grid guide-cols-6">

        <!-- Good (0–50) -->
        <div class="guide-card guide-aqi-good" onclick="openGuidePopup('aqi-good')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">0 – 50</div>
            <div class="guide-card-title">Good</div>
            <div class="guide-card-desc">Udara bersih, aman untuk semua aktivitas.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Moderate (51–100) -->
        <div class="guide-card guide-aqi-mod" onclick="openGuidePopup('aqi-moderate')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">51 – 100</div>
            <div class="guide-card-title">Moderate</div>
            <div class="guide-card-desc">Masih dapat diterima, sensitif perlu waspada.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Sensitive Groups (101–150) -->
        <div class="guide-card guide-aqi-sens" onclick="openGuidePopup('aqi-sensitive')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">101 – 150</div>
            <div class="guide-card-title">Sensitive Groups</div>
            <div class="guide-card-desc">Kelompok rentan sebaiknya membatasi aktivitas.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Unhealthy (151–200) -->
        <div class="guide-card guide-aqi-unhlth" onclick="openGuidePopup('aqi-unhealthy')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">151 – 200</div>
            <div class="guide-card-title">Unhealthy</div>
            <div class="guide-card-desc">Risiko meningkat untuk semua orang di ruangan.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Very Unhealthy (201–300) -->
        <div class="guide-card guide-aqi-vunhlth" onclick="openGuidePopup('aqi-very-unhealthy')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">201 – 300</div>
            <div class="guide-card-title">Very Unhealthy</div>
            <div class="guide-card-desc">Peringatan kesehatan darurat, semua orang berisiko.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

        <!-- Hazardous (301–500) -->
        <div class="guide-card guide-aqi-haz" onclick="openGuidePopup('aqi-hazardous')">
            <div class="guide-card-stripe"></div>
            <div class="guide-card-range">301 – 500</div>
            <div class="guide-card-title">Hazardous</div>
            <div class="guide-card-desc">Kondisi kritis, segera tinggalkan ruangan.</div>
            <div class="guide-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat detail</div>
        </div>

    </div>
</div>
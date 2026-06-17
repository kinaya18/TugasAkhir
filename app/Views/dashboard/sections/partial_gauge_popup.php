<!-- =============================================================
     SECTION 2: GAUGE POPUP
     Popup detail sensor yang muncul saat gauge diklik.
     Menampilkan: nilai, status badge, skala warna, jarum penunjuk.
     Dipanggil dari: openPopup('aqi' | 'pm25' | 'pm10' | 'pm1' | 'polutan')
     ============================================================= -->

<div id="popup-overlay" class="popup-overlay" onclick="closePopupOutside(event)">
    <div class="popup-box">

        <button class="popup-close" onclick="closePopup()">&#x2715;</button>

        <!-- Header: ikon & judul sensor -->
        <div class="popup-header">
            <div class="popup-icon-wrap" id="popup-icon-wrap">
                <i id="popup-icon" class="fa-solid fa-wind"></i>
            </div>
            <div>
                <h2 class="popup-title"    id="popup-title">--</h2>
                <p  class="popup-subtitle" id="popup-subtitle">--</p>
            </div>
        </div>

        <!-- Nilai besar & satuan -->
        <div class="popup-value-row">
            <span class="popup-big-value" id="popup-big-value">--</span>
            <span class="popup-big-unit"  id="popup-big-unit">--</span>
        </div>

        <!-- Badge status (Good / Moderate / dst) -->
        <div class="popup-status-badge" id="popup-status-badge">--</div>

        <!-- Skala warna 6 kategori + jarum penunjuk -->
        <div class="popup-scale">
            <div class="popup-scale-bar" id="popup-scale-bar">
                <div class="scale-seg seg-good"></div>
                <div class="scale-seg seg-moderate"></div>
                <div class="scale-seg seg-poor"></div>
                <div class="scale-seg seg-unhealthy"></div>
                <div class="scale-seg seg-severe"></div>
                <div class="scale-seg seg-hazardous"></div>
                <!-- Jarum posisi diatur via JS (style.left) -->
                <div class="scale-needle" id="scale-needle"></div>
            </div>

            <!-- Label kategori skala -->
            <div class="popup-scale-labels">
                <span>Good</span><span>Moderate</span><span>Sensitive</span>
                <span>Unhealthy</span><span>V.Unhealthy</span><span>Hazardous</span>
            </div>

            <!-- Angka ambang batas — diperbarui tiap popup dibuka -->
            <div class="popup-scale-numbers" id="popup-scale-numbers">
                <span>0</span><span>50</span><span>100</span>
                <span>150</span><span>200</span><span>300</span><span>500+</span>
            </div>
        </div>

        <!-- Warna segmen skala (didefinisikan di sini agar spesifik untuk popup) -->
        <style>
            .scale-seg.seg-good      { background: #22c55e !important; }
            .scale-seg.seg-moderate  { background: #eab308 !important; }
            .scale-seg.seg-poor      { background: #f97316 !important; }
            .scale-seg.seg-unhealthy { background: #ef4444 !important; }
            .scale-seg.seg-severe    { background: #a855f7 !important; }
            .scale-seg.seg-hazardous { background: #7f1d1d !important; }
        </style>

    </div>
</div>


<!-- =============================================================
     SECTION 3: GUIDE POPUP (AQI & AQHI)
     Popup panduan kategori yang muncul dari kartu skala.
     Menampilkan: deskripsi, skala, dan Do's & Don'ts per kategori.
     Dipanggil dari: openGuidePopup('aqhi-low' | 'aqi-good' | dst)
     ============================================================= -->

<div class="guide-overlay" id="guideOverlay" onclick="closeGuideOnOverlay(event)">
    <div class="guide-popup" id="guidePopup">

        <!-- Garis warna di atas popup -->
        <div class="guide-popup-topbar" id="guidePopupTopbar"></div>

        <div class="guide-popup-inner">

            <!-- Header: ikon emoji, judul, rentang nilai -->
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

            <!-- Deskripsi kondisi -->
            <p class="guide-popup-desc" id="guidePopupDesc"></p>

            <!-- Badge rentang PM -->
            <span class="guide-popup-pm" id="guidePopupPm"></span>

            <!-- Skala warna dengan jarum penunjuk -->
            <div class="guide-scale-wrap">
                <div class="guide-scale-label-row" id="guideScaleLabels"></div>
                <div class="guide-scale-bar" id="guideScaleBar">
                    <!-- Segmen warna diinjeksi via JS -->
                    <div class="guide-scale-needle" id="guideScaleNeedle"></div>
                </div>
            </div>

            <!-- Grid Do's & Don'ts -->
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

<script src="<?= base_url('assets/js/dashboard/section_popup.js') ?>"></script>
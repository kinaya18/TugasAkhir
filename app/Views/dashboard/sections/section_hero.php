<!-- =========================================================
     SECTION 1: DASHBOARD — Air Quality Health Index (AQHI)
     Menampilkan nilai AQHI, kondisi cuaca, risiko asma,
     rekomendasi kesehatan, dan gauge polutan secara real-time.
     ========================================================= -->

<div class="dashboard-wrapper">

    <!-- ===== KOLOM KIRI: Hero Card (AQHI Utama) ===== -->
    <div class="dash-left">

        <div class="hero-card" id="hero-card">

            <!-- Baris Atas: Label & Badge Status -->
            <div class="hero-top-row">
                <div class="hero-top-left">
                    <i class="fa-solid fa-clock-rotate-left" style="font-size:11px;"></i>
                    <span class="hero-top-label">AIR QUALITY HEALTH INDEX</span>
                </div>
                <span class="hero-status" id="hero-status-badge">--</span>
            </div>

            <!-- Nilai AQHI & Deskripsi Status -->
            <div class="hero-main-row">
                <div class="hero-aqi-left">
                    <p class="hero-aqi-label">NILAI AQHI</p>
                    <h1 class="hero-temp" id="hero-aqhi-value">--</h1>
                </div>
                <div class="hero-status-box">
                    <p class="hero-status-title">Status Kesehatan</p>
                    <p class="hero-status-text" id="hero-aqhi-desc">--</p>
                </div>
            </div>

            <!-- Data Iklim: Suhu & Kelembaban -->
            <div class="hero-climate-row">
                <div class="hero-climate-item">
                    <i class="fa-solid fa-temperature-half"></i>
                    <div>
                        <p class="hero-climate-label">SUHU</p>
                        <p class="hero-climate-val"><span id="hero-temp">--</span> °C</p>
                    </div>
                </div>
                <div class="hero-climate-item">
                    <i class="fa-solid fa-droplet"></i>
                    <div>
                        <p class="hero-climate-label">KELEMBABAN</p>
                        <p class="hero-climate-val"><span id="hero-humidity">--</span> %</p>
                    </div>
                </div>
            </div>

            <!-- Kartu Polutan: PM2.5, NO₂, O₃ -->
            <div class="hero-pollutant-row">
                <div class="hero-poll-card">
                    <p class="hero-poll-label">PM2.5</p>
                    <p class="hero-poll-unit">µg/m³</p>
                    <p class="hero-poll-val" id="hero-pm25-value">--</p>
                    <div class="hero-poll-bar" id="hero-pm25-bar"></div>
                </div>
                <div class="hero-poll-card">
                    <p class="hero-poll-label">NO₂</p>
                    <p class="hero-poll-unit">ppm</p>
                    <p class="hero-poll-val" id="hero-no2-value">--</p>
                    <div class="hero-poll-bar" id="hero-no2-bar"></div>
                </div>
                <div class="hero-poll-card">
                    <p class="hero-poll-label">O₃</p>
                    <p class="hero-poll-unit">ppm</p>
                    <p class="hero-poll-val" id="hero-o3-value">--</p>
                    <div class="hero-poll-bar" id="hero-o3-bar"></div>
                </div>
            </div>

            <!-- Skala Risiko AQHI (1–10+) -->
            <div class="hero-scale-section">
                <div class="hero-scale-header">
                    <span class="hero-scale-title">INDEX RISIKO AQHI</span>
                    <span class="hero-scale-badge" id="hero-scale-badge">--</span>
                </div>
                <div class="hero-scale-wrap">
                    <div class="hero-scale-bar">
                        <div class="hs-seg hs-good"></div>
                        <div class="hs-seg hs-moderate"></div>
                        <div class="hs-seg hs-sensitive"></div>
                        <div class="hs-seg hs-unhealthy"></div>
                        <div class="hs-seg hs-very"></div>
                        <div class="hs-seg hs-hazard"></div>
                        <!-- Jarum penunjuk posisi AQHI -->
                        <div class="hero-scale-needle" id="hero-scale-needle"></div>
                    </div>
                    <div class="hero-scale-nums">
                        <span>1</span><span>3</span><span>5</span>
                        <span>7</span><span>10</span><span>10+</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- Akhir Kolom Kiri -->


    <!-- ===== KOLOM KANAN: Risiko Asma, Rekomendasi, Gauge ===== -->
    <div class="dash-right">

        <!-- Kartu Risiko Asma -->
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

        <!-- Kartu Rekomendasi Kesehatan -->
        <div class="health-card">
            <div class="health-header">
                <i class="fa-solid fa-shield-heart" style="color:#3b82f6;font-size:16px;"></i>
                <h3>Health Recommendations</h3>
            </div>
            <div id="health-list" class="health-list"></div>
        </div>

        <!-- Baris Gauge: AQI, PM2.5, PM10, PM1, Polutan -->
        <div class="gauge-row">

            <div class="gauge-card" onclick="openPopup('aqi')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-aqi"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-aqi">--</span>
                        <span class="gauge-unit">AQI</span>
                    </div>
                </div>
                <p class="gauge-label">AQI</p>
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

            <div class="gauge-card" onclick="openPopup('pm1')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-pm1"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-pm1">--</span>
                        <span class="gauge-unit">µg/m³</span>
                    </div>
                </div>
                <p class="gauge-label">PM 1</p>
            </div>

            <div class="gauge-card" onclick="openPopup('polutan')">
                <div class="gauge-wrapper">
                    <canvas id="gauge-polutan"></canvas>
                    <div class="gauge-center">
                        <span class="gauge-value" id="val-polutan">--</span>
                        <span class="gauge-unit">ppm</span>
                    </div>
                </div>
                <p class="gauge-label">Polutan</p>
            </div>

        </div>
        <!-- Akhir Gauge Row -->

    </div>
    <!-- Akhir Kolom Kanan -->

</div>
<!-- Akhir Dashboard Wrapper -->

<script src="<?= base_url('assets/js/dashboard/section_hero.js') ?>"></script>
<?php
/**
 * SECTION 2 – RIWAYAT & GRAFIK KUALITAS UDARA
 * -----------------------------------------------
 * Berisi:
 *  1. Timeline forecast 24 jam (scrollable)
 *  2. Tabel historis per jam / per hari
 *  3. Grafik tren kualitas udara (bar / line)
 *
 * Dependensi eksternal (muat di <head>):
 *  - Chart.js
 *  - SheetJS  (XLSX)
 *  - jsPDF + jsPDF-AutoTable  (dimuat dinamis saat dibutuhkan)
 *  - Font Awesome
 */
?>

<!-- ===================== SECTION 2: RIWAYAT ===================== -->
<div class="history-wrapper">

    <!-- ─────────────────────────────────────────────────────────
         1. TIMELINE FORECAST 24 JAM
         Menampilkan prediksi AQHI tiap jam dalam bentuk scrollable.
    ───────────────────────────────────────────────────────────── -->
    <div class="timeline-card">
        <div class="timeline-card-header">
            <div>
                <span class="hd-eyebrow">AQHI Forecast</span>
                <h4 class="hd-title">24-Hour Prediction</h4>
            </div>
            <!-- Badge AQHI saat ini, diisi oleh renderTimeline() -->
            <span class="timeline-now-badge" id="timeline-now-badge">-- AQHI sekarang</span>
        </div>

        <!-- Kontainer scroll horizontal untuk kartu per jam -->
        <div class="fc-scroll">
            <div class="fc-timeline" id="fc-timeline"></div>
        </div>
    </div>


    <!-- ─────────────────────────────────────────────────────────
         2. TABEL HISTORIS KUALITAS UDARA
         Toggle: Per Jam / Per Hari | Filter | Export | Pagination
    ───────────────────────────────────────────────────────────── -->
    <div class="history-card-box hd-card">

        <!-- Header judul -->
        <div class="hd-header">
            <div class="hd-title-group">
                <span class="hd-eyebrow">Historical Data</span>
                <h4 class="hd-title">Air Quality Table</h4>
            </div>
        </div>

        <!-- Toolbar: toggle tampilan + export + pencarian -->
        <div class="hd-controls">

            <div style="display:flex; align-items:center; gap:14px;">

                <!-- Toggle Per Jam / Per Hari -->
                <div class="hd-toggle">
                    <button class="hd-tab active" id="btn-jam" onclick="hdSetView('jam', this)">
                        <i class="fa-solid fa-clock"></i> Per Jam
                    </button>
                    <button class="hd-tab" id="btn-hari" onclick="hdSetView('hari', this)">
                        <i class="fa-solid fa-calendar-day"></i> Per Hari
                    </button>
                </div>

                <!-- Tombol export: copy, CSV, Excel, PDF, Print -->
                <div class="hd-export-toolbar">
                    <button class="hd-export-btn hd-btn-copy"  onclick="hdExport('copy')"  title="Copy ke Clipboard"><i class="fa-regular fa-copy"></i></button>
                    <button class="hd-export-btn hd-btn-csv"   onclick="hdExport('csv')"   title="Export CSV"><i class="fa-solid fa-file-csv"></i></button>
                    <button class="hd-export-btn hd-btn-excel" onclick="hdExport('excel')" title="Export Excel"><i class="fa-solid fa-file-excel"></i></button>
                    <button class="hd-export-btn hd-btn-pdf"   onclick="hdExport('pdf')"   title="Export PDF"><i class="fa-solid fa-file-pdf"></i></button>
                    <button class="hd-export-btn hd-btn-print" onclick="hdExport('print')" title="Print"><i class="fa-solid fa-print"></i></button>
                </div>

            </div>

            <!-- Kotak pencarian dengan tombol clear -->
            <div class="hd-search-wrap">
                <i class="fa-solid fa-magnifying-glass hd-search-icon"></i>
                <input type="text" id="hd-search" class="hd-search"
                       placeholder="Filter by keyword..."
                       oninput="hdFilter()" />
                <button class="hd-clear" id="hd-clear" onclick="hdClear()" title="Clear">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

        </div>

        <!-- Inner card: tabel + footer pagination -->
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

                        <!-- Baris data per jam (dirender dari PHP) -->
                        <?php foreach ($historyHourly as $item): ?>
                        <tr class="<?= !empty($item['is_today']) ? 'today-row' : '' ?>" data-view="jam">
                            <td class="td-time"><?= $item['time']     ?? '--' ?></td>
                            <td class="td-aqhi"><?= $item['aqhi']     ?? '-'  ?></td>
                            <td><?= $item['aqi']      ?? '-' ?></td>
                            <td><?= $item['pm25']     ?? '-' ?></td>
                            <td><?= $item['pm10']     ?? '-' ?></td>
                            <td><?= $item['pm1']      ?? '-' ?></td>
                            <td><?= $item['no2']      ?? '-' ?></td>
                            <td><?= $item['o3']       ?? '-' ?></td>
                            <td><?= $item['temp']     ?? '-' ?>°C</td>
                            <td><?= $item['humidity'] ?? '-' ?>%</td>
                            <td class="td-loc"><?= $item['location'] ?? 'Bojongsoang' ?></td>
                            <td>
                                <span class="pill <?= getAqhiPillClass($item['aqhi'] ?? 0) ?>">
                                    <?= getAqhiLabel($item['aqhi'] ?? 0) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- Baris data per hari (disembunyikan secara default) -->
                        <?php foreach ($historyDaily as $item): ?>
                        <tr class="<?= !empty($item['is_today']) ? 'today-row' : '' ?>" data-view="hari" style="display:none;">
                            <td class="td-date"><?= $item['date']     ?? '--' ?></td>
                            <td class="td-aqhi"><?= $item['aqhi']     ?? '-'  ?></td>
                            <td><?= $item['aqi']      ?? '-' ?></td>
                            <td><?= $item['pm25']     ?? '-' ?></td>
                            <td><?= $item['pm10']     ?? '-' ?></td>
                            <td><?= $item['pm1']      ?? '-' ?></td>
                            <td><?= $item['no2']      ?? '-' ?></td>
                            <td><?= $item['o3']       ?? '-' ?></td>
                            <td><?= $item['temp']     ?? '-' ?>°C</td>
                            <td><?= $item['humidity'] ?? '-' ?>%</td>
                            <td class="td-loc"><?= $item['location'] ?? 'Bojongsoang' ?></td>
                            <td>
                                <span class="pill <?= getAqhiPillClass($item['aqhi'] ?? 0) ?>">
                                    <?= getAqhiLabel($item['aqhi'] ?? 0) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>

                <!-- Pesan kosong saat filter tidak menemukan data -->
                <div class="hd-empty" id="hd-empty">
                    <i class="fa-solid fa-inbox"></i>
                    <span>No data matches "<span id="hd-empty-kw"></span>"</span>
                </div>
            </div>

            <!-- Footer: jumlah entri per halaman + info pagination -->
            <div class="hd-footer">
                <div class="hd-entries">
                    <span>Show</span>
                    <select id="hd-entries-select" onchange="hdChangeEntries()">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="9999">All</option>
                    </select>
                    <span>entries</span>
                </div>
                <div class="hd-footer-info" id="hd-footer-info">Showing 1 to 10 entries</div>
            </div>
        </div>

    </div>


    <!-- ─────────────────────────────────────────────────────────
         3. GRAFIK TREN KUALITAS UDARA
         Toggle: Per Jam / Per Hari / Bulanan | Bar / Line | Metrik
    ───────────────────────────────────────────────────────────── -->
    <div class="history-card-box daily-card-box">

        <div class="daily-card-header">
            <div>
                <span class="hd-eyebrow">HISTORY GRAPH</span>
                <h4>Air Quality Trend</h4>
            </div>

            <!-- Toggle tipe grafik: bar / line -->
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="graph-toggle">
                    <button class="graph-btn active" id="btn-bar"
                            onclick="setChartType('bar', this)" title="Bar Chart">
                        <i class="fa-solid fa-chart-column"></i>
                    </button>
                    <button class="graph-btn" id="btn-line"
                            onclick="setChartType('line', this)" title="Line Chart">
                        <i class="fa-solid fa-chart-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Info nilai aktif yang sedang di-hover / dipilih -->
        <div class="rwh-controls">
            <div class="rwh-highlight">
                <span class="rwh-dot" id="rwh-dot"></span>
                <div>
                    <div class="rwh-val-row">
                        <span class="rwh-val"  id="rwh-val">--</span>
                        <span class="rwh-desc" id="rwh-desc"></span>
                    </div>
                    <div class="rwh-meta" id="rwh-meta">--</div>
                </div>
            </div>

            <!-- Tab periode + pilihan metrik -->
            <div class="rwh-right">
                <div class="rwh-tabs">
                    <button class="rwh-tab active" onclick="rwhSetTab('jam',   this)">per jam</button>
                    <button class="rwh-tab"        onclick="rwhSetTab('hari',  this)">per hari</button>
                    <button class="rwh-tab"        onclick="rwhSetTab('bulan', this)">bulanan</button>
                </div>

                <select class="rwh-select" id="rwh-metric" onchange="rwhRender()">
                    <option value="aqhi">AQHI</option>
                    <option value="aqi">AQI</option>
                    <option value="pm25">PM2.5</option>
                    <option value="no2">NO₂</option>
                    <option value="o3">O₃</option>
                    <option value="pm10">PM10</option>
                    <option value="polutan">Polutan / VOC</option>
                    <option value="temp">Suhu</option>
                    <option value="humidity">Kelembapan</option>
                </select>
            </div>
        </div>

        <!-- Kanvas grafik Chart.js -->
        <div class="mini-chart-card">
            <div class="rwh-canvas-wrap">
                <canvas id="rwhChart"></canvas>
            </div>
        </div>

    </div>

</div><!-- /.history-wrapper -->


<script src="<?= base_url('assets/js/dashboard/section_history.js') ?>"></script>
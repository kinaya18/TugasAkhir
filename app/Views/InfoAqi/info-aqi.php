// informasi
<?= $this->extend('layout/main_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/infoaqi.css') ?>">

<div class="aqi-wrapper" id="aqiWrapper">

    <!-- HEADER -->
    <div class="aqi-header">
        <h2>Panduan Indikator AQI Indoor</h2>
        <p>Standar penilaian kualitas udara dalam ruangan. Klik untuk melihat detail & panduan.</p>
    </div>

    <!-- GRID -->
    <div class="aqi-grid">

        <div class="aqi-card" onclick="openPopup('good')">
            <div class="aqi-bar good"></div>
            <div class="aqi-content">
                <h4>GOOD</h4>
                <p>Udara bersih dan sehat untuk semua kelompok termasuk penderita asma.</p>
                <span class="aqi-badge badge-good">0 – 50</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openPopup('moderate')">
            <div class="aqi-bar moderate"></div>
            <div class="aqi-content">
                <h4>MODERATE</h4>
                <p>Kualitas udara dapat diterima, namun dapat mempengaruhi penderita asma sensitif.</p>
                <span class="aqi-badge badge-moderate">51 – 100</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openPopup('sensitive')">
            <div class="aqi-bar sensitive"></div>
            <div class="aqi-content">
                <h4>SENSITIVE GROUPS</h4>
                <p>Berisiko bagi penderita asma, lansia, dan anak-anak di dalam ruangan.</p>
                <span class="aqi-badge badge-sensitive">101 – 150</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openPopup('unhealthy')">
            <div class="aqi-bar unhealthy"></div>
            <div class="aqi-content">
                <h4>UNHEALTHY</h4>
                <p>Masyarakat umum mulai merasakan dampak kesehatan di dalam ruangan.</p>
                <span class="aqi-badge badge-unhealthy">151 – 200</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openPopup('very-unhealthy')">
            <div class="aqi-bar very-unhealthy"></div>
            <div class="aqi-content">
                <h4>VERY UNHEALTHY</h4>
                <p>Peringatan kesehatan darurat, semua orang berisiko di dalam ruangan.</p>
                <span class="aqi-badge badge-very-unhealthy">201 – 300</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

        <div class="aqi-card" onclick="openPopup('hazardous')">
            <div class="aqi-bar hazardous"></div>
            <div class="aqi-content">
                <h4>HAZARDOUS</h4>
                <p>Kondisi udara sangat berbahaya, segera tinggalkan atau bersihkan ruangan.</p>
                <span class="aqi-badge badge-hazardous">301 – 500</span>
                <div class="aqi-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk detail</div>
            </div>
        </div>

    </div>

    <!-- POPUP OVERLAY -->
    <div class="aqi-overlay" id="aqiOverlay" onclick="closeOnOverlay(event)">
        <div class="aqi-popup" id="aqiPopup">

            <!-- POPUP HEADER -->
            <div class="popup-head">
                <div class="popup-head-left">
                    <div class="popup-dot" id="popupDot"></div>
                    <div>
                        <div class="popup-title" id="popupTitle"></div>
                        <div class="popup-range" id="popupRange"></div>
                    </div>
                </div>
                <button class="popup-close" onclick="closePopup()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- POPUP BODY -->
            <div class="popup-body">
                <p class="popup-desc" id="popupDesc"></p>
                <span class="popup-pm" id="popupPm"></span>

                <div class="dd-grid">
                    <div class="dd-box dd-do">
                        <div class="dd-title">
                            <i class="fa-solid fa-circle-check"></i> DO'S
                        </div>
                        <div id="doList"></div>
                    </div>
                    <div class="dd-box dd-dont">
                        <div class="dd-title">
                            <i class="fa-solid fa-circle-xmark"></i> DON'TS
                        </div>
                        <div id="dontList"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
const aqiData = {
    good: {
        title: 'Good — Udara Baik',
        range: 'AQI 0–50 · PM2.5 < 12 µg/m³',
        color: '#22c55e', bg: '#dcfce7',
        desc: 'Kualitas udara dalam ruangan sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak. Kondisi ini ideal untuk semua aktivitas di dalam ruangan.',
        pm: 'PM2.5 < 12 µg/m³', pmBg: '#dcfce7', pmColor: '#15803d',
        dos: ['Lakukan aktivitas normal di dalam ruangan', 'Biarkan ventilasi alami terbuka', 'Matikan air purifier untuk hemat energi', 'Catat kondisi ini sebagai baseline sensor'],
        donts: ['Jangan abaikan pemeliharaan ventilasi rutin', 'Jangan biarkan sumber polutan baru masuk', 'Jangan memasak tanpa exhaust fan aktif']
    },
    moderate: {
        title: 'Moderate — Udara Sedang',
        range: 'AQI 51–100 · PM2.5 12–35 µg/m³',
        color: '#eab308', bg: '#fef9c3',
        desc: 'Kualitas udara cukup dapat diterima namun mulai menurun. Penderita asma yang sangat sensitif mungkin mengalami gejala ringan. Periksa sumber polutan di dalam ruangan seperti debu atau asap masak.',
        pm: 'PM2.5 12–35 µg/m³', pmBg: '#fef9c3', pmColor: '#a16207',
        dos: ['Nyalakan air purifier di mode rendah', 'Buka jendela bila udara luar lebih baik', 'Periksa filter AC dan bersihkan jika kotor', 'Batasi aktivitas memasak yang menghasilkan asap'],
        donts: ['Jangan bakar dupa atau lilin aromaterapi', 'Jangan semprotkan pengharum ruangan aerosol', 'Jangan biarkan penderita asma beraktivitas berat']
    },
    sensitive: {
        title: 'Sensitive Groups — Tidak Sehat (Sensitif)',
        range: 'AQI 101–150 · PM2.5 35–55 µg/m³',
        color: '#f97316', bg: '#ffedd5',
        desc: 'Udara berbahaya bagi kelompok sensitif di dalam ruangan. Penderita asma berisiko mengalami gangguan pernapasan. Segera identifikasi dan hentikan sumber polutan dalam ruangan.',
        pm: 'PM2.5 35–55 µg/m³', pmBg: '#ffedd5', pmColor: '#c2410c',
        dos: ['Nyalakan air purifier di mode tinggi', 'Segera cari sumber polutan (debu, cat, VOC)', 'Anjurkan penderita asma siapkan inhaler', 'Tingkatkan ventilasi atau buka semua jendela'],
        donts: ['Jangan izinkan penderita asma beraktivitas fisik', 'Jangan memasak dengan bahan yang berasap', 'Jangan gunakan bahan kimia pembersih berbau', 'Jangan biarkan anak-anak dan lansia di ruangan lama']
    },
    unhealthy: {
        title: 'Unhealthy — Tidak Sehat',
        range: 'AQI 151–200 · PM2.5 55–150 µg/m³',
        color: '#ef4444', bg: '#fee2e2',
        desc: 'Seluruh penghuni ruangan mulai berisiko terkena dampak kesehatan. Penderita asma sangat berisiko mengalami serangan. Tindakan perbaikan kualitas udara harus segera dilakukan.',
        pm: 'PM2.5 55–150 µg/m³', pmBg: '#fee2e2', pmColor: '#b91c1c',
        dos: ['Evakuasi penderita asma ke ruangan lain', 'Nyalakan semua air purifier ke mode maksimal', 'Hubungi teknisi untuk cek sistem ventilasi', 'Gunakan masker N95 jika harus berada di ruangan'],
        donts: ['Jangan tinggalkan penderita asma tanpa pengawasan', 'Jangan melakukan aktivitas yang meningkatkan polutan', 'Jangan matikan air purifier dalam kondisi apapun', 'Jangan tunda perbaikan sumber masalah']
    },
    'very-unhealthy': {
        title: 'Very Unhealthy — Sangat Tidak Sehat',
        range: 'AQI 201–300 · PM2.5 150–250 µg/m³',
        color: '#a855f7', bg: '#f3e8ff',
        desc: 'Kondisi darurat kesehatan di dalam ruangan. Semua orang berisiko mengalami efek serius. Sumber polutan ekstrem kemungkinan aktif seperti kebakaran kecil, kebocoran gas, atau bahan kimia.',
        pm: 'PM2.5 150–250 µg/m³', pmBg: '#f3e8ff', pmColor: '#7e22ce',
        dos: ['Segera evakuasi semua penghuni ruangan', 'Hubungi pihak terkait jika dicurigai kebocoran', 'Gunakan masker N95/respirator saat di dalam', 'Matikan semua sumber api dan bahan mudah terbakar'],
        donts: ['Jangan tunda evakuasi penderita asma', 'Jangan abaikan tanda-tanda sumber bahaya', 'Jangan masuk kembali sebelum udara diperbaiki', 'Jangan gunakan masker biasa — tidak cukup efektif']
    },
    hazardous: {
        title: 'Hazardous — Berbahaya',
        range: 'AQI 301–500 · PM2.5 > 250 µg/m³',
        color: '#7f1d1d', bg: '#fecaca',
        desc: 'Kondisi udara sangat kritis dan mengancam jiwa. Semua orang harus segera keluar. Sumber polutan berbahaya aktif — kemungkinan kebakaran, kebocoran bahan kimia, atau kontaminasi berat.',
        pm: 'PM2.5 > 250 µg/m³', pmBg: '#fecaca', pmColor: '#7f1d1d',
        dos: ['Evakuasi segera seluruh penghuni ruangan', 'Hubungi 119 atau pemadam jika ada indikasi kebakaran', 'Tutup pintu dan jendela untuk isolasi ruangan', 'Gunakan jalur evakuasi yang aman'],
        donts: ['Jangan masuk kembali ke ruangan dalam kondisi apapun', 'Jangan coba atasi sendiri tanpa alat proteksi', 'Jangan biarkan siapapun tinggal di dalam', 'Jangan gunakan lift — gunakan tangga darurat']
    }
};

function openPopup(key) {
    const d = aqiData[key];
    document.getElementById('popupTitle').textContent = d.title;
    document.getElementById('popupRange').textContent = d.range;

    const dot = document.getElementById('popupDot');
    dot.style.background = d.bg;
    dot.innerHTML = `<div style="width:16px;height:16px;border-radius:5px;background:${d.color}"></div>`;

    document.getElementById('popupDesc').textContent = d.desc;

    const pm = document.getElementById('popupPm');
    pm.textContent = d.pm;
    pm.style.background = d.pmBg;
    pm.style.color = d.pmColor;

    document.getElementById('doList').innerHTML = d.dos.map(t =>
        `<div class="dd-item"><span class="dd-bullet bullet-do"></span><span>${t}</span></div>`
    ).join('');

    document.getElementById('dontList').innerHTML = d.donts.map(t =>
        `<div class="dd-item"><span class="dd-bullet bullet-dont"></span><span>${t}</span></div>`
    ).join('');

    document.getElementById('aqiOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePopup() {
    document.getElementById('aqiOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

function closeOnOverlay(e) {
    if (e.target === document.getElementById('aqiOverlay')) closePopup();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePopup();
});
</script>

<?= $this->endSection() ?>
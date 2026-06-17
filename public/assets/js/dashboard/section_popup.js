// =============================================================
//  POPUP JAVASCRIPT
//  Berisi:
//    1. Data konfigurasi Gauge Popup (popupData)
//    2. Fungsi buka/tutup Gauge Popup (openPopup, closePopup)
//    3. Data konfigurasi Guide Popup (guidePopupData)
//    4. Fungsi buka/tutup Guide Popup (openGuidePopup, closeGuidePopup)
//    5. Event listener keyboard (Escape)
//
//  Catatan: fungsi getPm25Status, getPm10Status, getPm1Status,
//  getNeedlePosition, getStatusClass, dan getAqiLabel
//  didefinisikan di section_dashboard.js dan dipakai di sini.
// =============================================================


// =============================================================
// 1. DATA KONFIGURASI — GAUGE POPUP
//    Setiap key sesuai dengan ID gauge yang diklik di dashboard.
//    Nilai (value & status) diisi ulang saat popup dibuka.
// =============================================================

const popupData = {
    polutan: { title: 'Polutan',  subtitle: 'Gas Iritan',         icon: 'fa-wind',       iconColor: '#22c55e', unit: 'ppm'   },
    pm25:    { title: 'PM 2.5',   subtitle: 'Partikel Halus',     icon: 'fa-smog',       iconColor: '#22c55e', unit: 'µg/m³' },
    pm10:    { title: 'PM 10',    subtitle: 'Partikel Kasar',     icon: 'fa-circle-dot', iconColor: '#f59e0b', unit: 'µg/m³' },
    pm1:     { title: 'PM 1',     subtitle: 'Partikel Ultrafine', icon: 'fa-circle-dot', iconColor: '#a855f7', unit: 'µg/m³' },
    aqi:     { title: 'AQI',      subtitle: 'Air Quality Index',  icon: 'fa-gauge-high', iconColor: '#f59e0b', unit: 'AQI'   },
};


// =============================================================
// 2. FUNGSI GAUGE POPUP
// =============================================================

/**
 * Membuka popup detail sensor.
 * Mengambil nilai terkini dari window.DASH.latestData,
 * menentukan status & posisi jarum, lalu merender ke DOM.
 * @param {string} key - Kunci sensor: 'aqi' | 'pm25' | 'pm10' | 'pm1' | 'polutan'
 */
function openPopup(key) {
    const d  = popupData[key];
    const ld = window.DASH && window.DASH.latestData;

    // --- Ambil nilai terkini dari data real-time ---
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

        // Tentukan label status sesuai jenis sensor
        switch (key) {
            case 'pm25':    d.status = getPm25Status(rawVal); break;
            case 'pm10':    d.status = getPm10Status(rawVal); break;
            case 'pm1':     d.status = getPm1Status(rawVal);  break;
            default:        d.status = getAqiLabel(rawVal).toUpperCase();
        }

        // CSS class & posisi jarum dari status
        d.statusClass = getStatusClass(d.status);
        d.needlePct   = getNeedlePosition(d.status);
    }

    // --- Perbarui angka skala sesuai ambang batas masing-masing sensor ---
    const scaleNumbers = document.getElementById('popup-scale-numbers');
    switch (key) {
        case 'pm25':
            scaleNumbers.innerHTML =
                '<span>0</span><span>12</span><span>35</span><span>55</span><span>150</span><span>250</span><span>250+</span>';
            break;
        case 'pm10':
            scaleNumbers.innerHTML =
                '<span>0</span><span>54</span><span>154</span><span>254</span><span>354</span><span>424</span><span>424+</span>';
            break;
        case 'pm1':
            scaleNumbers.innerHTML =
                '<span>0</span><span>10</span><span>25</span><span>50</span><span>100</span><span>200</span><span>200+</span>';
            break;
        default: // AQI & Polutan menggunakan skala AQI standar
            scaleNumbers.innerHTML =
                '<span>0</span><span>50</span><span>100</span><span>150</span><span>200</span><span>300</span><span>500+</span>';
    }

    // --- Render data ke elemen DOM ---
    document.getElementById('popup-title').textContent     = d.title;
    document.getElementById('popup-subtitle').textContent  = d.subtitle;
    document.getElementById('popup-big-value').textContent = d.value;
    document.getElementById('popup-big-unit').textContent  = d.unit;

    const badge = document.getElementById('popup-status-badge');
    badge.textContent = d.status;
    badge.className   = 'popup-status-badge ' + d.statusClass;

    const iconWrap = document.getElementById('popup-icon-wrap');
    iconWrap.style.background = d.iconColor + '22'; // warna transparan 13%

    const icon = document.getElementById('popup-icon');
    icon.className   = 'fa-solid ' + d.icon;
    icon.style.color = d.iconColor;

    document.getElementById('scale-needle').style.left = d.needlePct + '%';

    // Tampilkan overlay
    document.getElementById('popup-overlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

/** Menutup Gauge Popup. */
function closePopup() {
    document.getElementById('popup-overlay').classList.remove('active');
    document.body.style.overflow = '';
}

/**
 * Menutup popup jika klik di luar kotak (area overlay).
 * @param {MouseEvent} e
 */
function closePopupOutside(e) {
    if (e.target === document.getElementById('popup-overlay')) closePopup();
}


// =============================================================
// 3. DATA KONFIGURASI — GUIDE POPUP
//    Berisi panduan lengkap per kategori AQHI dan AQI:
//    deskripsi, skala warna, Do's, dan Don'ts.
// =============================================================

const guidePopupData = {

    // ── AQHI ──────────────────────────────────────────────────

    'aqhi-low': {
        title: 'Low — Risiko Rendah', range: 'AQHI 1–3',
        color: '#2563eb', dotBg: '#eff6ff', pmBg: '#eff6ff', pmColor: '#2563eb',
        icon: '😊',
        desc: 'Kualitas udara sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak. Semua orang dapat menikmati aktivitas luar ruangan dengan bebas.',
        pm: 'Risiko Kesehatan: Sangat Rendah',
        scaleColors: ['#2563eb', '#ca8a04', '#ea580c', '#dc2626'],
        scaleLabels: ['Low', 'Moderate', 'High', 'Very High'],
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
        title: 'Moderate — Risiko Sedang', range: 'AQHI 4–6',
        color: '#ca8a04', dotBg: '#fef9c3', pmBg: '#fef9c3', pmColor: '#a16207',
        icon: '😐',
        desc: 'Kualitas udara dapat diterima namun mulai mempengaruhi kelompok sensitif. Penderita asma atau penyakit jantung mungkin mengalami gejala ringan saat aktivitas intens di luar ruangan.',
        pm: 'Risiko Kesehatan: Sedang',
        scaleColors: ['#2563eb', '#ca8a04', '#ea580c', '#dc2626'],
        scaleLabels: ['Low', 'Moderate', 'High', 'Very High'],
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
        title: 'High — Risiko Tinggi', range: 'AQHI 7–10',
        color: '#ea580c', dotBg: '#ffedd5', pmBg: '#ffedd5', pmColor: '#c2410c',
        icon: '😷',
        desc: 'Udara berbahaya bagi kelompok sensitif. Semua penderita penyakit pernapasan dan jantung harus membatasi waktu di luar ruangan. Orang sehat pun sebaiknya mengurangi aktivitas berat.',
        pm: 'Risiko Kesehatan: Tinggi',
        scaleColors: ['#2563eb', '#ca8a04', '#ea580c', '#dc2626'],
        scaleLabels: ['Low', 'Moderate', 'High', 'Very High'],
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
        title: 'Very High — Risiko Sangat Tinggi', range: 'AQHI 10+',
        color: '#dc2626', dotBg: '#fee2e2', pmBg: '#fee2e2', pmColor: '#b91c1c',
        icon: '🚨',
        desc: 'Kondisi darurat kesehatan. Semua orang berisiko mengalami dampak serius. Hindari semua aktivitas luar ruangan dan pastikan ruangan tersegel dengan baik dari udara luar.',
        pm: 'Risiko Kesehatan: Sangat Tinggi',
        scaleColors: ['#2563eb', '#ca8a04', '#ea580c', '#dc2626'],
        scaleLabels: ['Low', 'Moderate', 'High', 'Very High'],
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

    // ── AQI ───────────────────────────────────────────────────

    'aqi-good': {
        title: 'Good — Udara Baik', range: 'AQI 0–50 · PM2.5 < 12 µg/m³',
        color: '#22c55e', dotBg: '#dcfce7', pmBg: '#dcfce7', pmColor: '#15803d',
        icon: '✅',
        desc: 'Kualitas udara dalam ruangan sangat baik. Tidak ada risiko kesehatan bagi siapapun termasuk penderita asma, lansia, dan anak-anak. Kondisi ideal untuk semua aktivitas.',
        pm: 'PM2.5 < 12 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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
        title: 'Moderate — Udara Sedang', range: 'AQI 51–100 · PM2.5 12–35 µg/m³',
        color: '#eab308', dotBg: '#fef9c3', pmBg: '#fef9c3', pmColor: '#a16207',
        icon: '⚠️',
        desc: 'Kualitas udara cukup dapat diterima namun mulai menurun. Penderita asma yang sangat sensitif mungkin mengalami gejala ringan saat berada di dalam ruangan.',
        pm: 'PM2.5 12–35 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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
        title: 'Sensitive Groups — Tidak Sehat', range: 'AQI 101–150 · PM2.5 35–55 µg/m³',
        color: '#f97316', dotBg: '#ffedd5', pmBg: '#ffedd5', pmColor: '#c2410c',
        icon: '⚡',
        desc: 'Udara berbahaya bagi kelompok sensitif. Penderita asma, lansia, dan anak-anak berisiko mengalami gangguan pernapasan. Segera cari dan atasi sumber polutan.',
        pm: 'PM2.5 35–55 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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
        title: 'Unhealthy — Tidak Sehat', range: 'AQI 151–200 · PM2.5 55–150 µg/m³',
        color: '#ef4444', dotBg: '#fee2e2', pmBg: '#fee2e2', pmColor: '#b91c1c',
        icon: '🚫',
        desc: 'Seluruh penghuni ruangan mulai berisiko terkena dampak kesehatan. Tindakan perbaikan harus segera dilakukan. Jangan tunda evaluasi sistem ventilasi.',
        pm: 'PM2.5 55–150 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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
        title: 'Very Unhealthy — Sangat Tidak Sehat', range: 'AQI 201–300 · PM2.5 150–250 µg/m³',
        color: '#a855f7', dotBg: '#f3e8ff', pmBg: '#f3e8ff', pmColor: '#7e22ce',
        icon: '☣️',
        desc: 'Kondisi darurat kesehatan di dalam ruangan. Semua orang berisiko mengalami efek serius. Evakuasi segera dan hubungi pihak terkait untuk penanganan lebih lanjut.',
        pm: 'PM2.5 150–250 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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
        title: 'Hazardous — Berbahaya', range: 'AQI 301–500 · PM2.5 > 250 µg/m³',
        color: '#7f1d1d', dotBg: '#fecaca', pmBg: '#fecaca', pmColor: '#7f1d1d',
        icon: '☠️',
        desc: 'Kondisi udara sangat kritis dan mengancam jiwa. Semua orang harus segera keluar dari ruangan. Hubungi layanan darurat jika ada indikasi kebakaran atau kebocoran gas.',
        pm: 'PM2.5 > 250 µg/m³',
        scaleColors: ['#22c55e', '#eab308', '#f97316', '#ef4444', '#a855f7', '#7f1d1d'],
        scaleLabels: ['Good', 'Moderate', 'Sensitive', 'Unhealthy', 'V.Unhealthy', 'Hazardous'],
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


// =============================================================
// 4. FUNGSI GUIDE POPUP
// =============================================================

/**
 * Membuka Guide Popup untuk kategori tertentu.
 * Membangun ulang segmen skala secara dinamis dari data konfigurasi.
 * @param {string} key - Kunci kategori, contoh: 'aqhi-low', 'aqi-good'
 */
function openGuidePopup(key) {
    const d = guidePopupData[key];
    if (!d) return;

    // Top bar warna
    document.getElementById('guidePopupTopbar').style.background = d.color;

    // Dot ikon & header
    const dot = document.getElementById('guidePopupDot');
    dot.style.background = d.dotBg;
    dot.textContent      = d.icon;

    document.getElementById('guidePopupTitle').textContent = d.title;
    document.getElementById('guidePopupRange').textContent = d.range;
    document.getElementById('guidePopupDesc').textContent  = d.desc;

    // Badge PM
    const pm = document.getElementById('guidePopupPm');
    pm.textContent        = d.pm;
    pm.style.background   = d.pmBg;
    pm.style.color        = d.pmColor;
    pm.style.border       = '1.5px solid ' + d.pmColor + '44';

    // Bangun segmen skala secara dinamis
    const bar    = document.getElementById('guideScaleBar');
    const needle = document.getElementById('guideScaleNeedle');
    const labels = document.getElementById('guideScaleLabels');

    // Hapus segmen lama (jarum tetap dipertahankan)
    Array.from(bar.children).forEach(c => { if (c !== needle) bar.removeChild(c); });
    labels.innerHTML = '';

    d.scaleColors.forEach((color, i) => {
        // Buat segmen warna
        const seg = document.createElement('div');
        seg.className    = 'guide-scale-seg' + (i === d.activeIdx ? ' active' : '');
        seg.style.background = color;
        bar.insertBefore(seg, needle);

        // Buat label di atas segmen
        const lbl = document.createElement('span');
        lbl.textContent = d.scaleLabels[i];
        labels.appendChild(lbl);
    });

    // Posisi jarum: tengah segmen aktif
    const pct = ((d.activeIdx + 0.5) / d.scaleColors.length) * 100;
    needle.style.left        = pct + '%';
    needle.style.borderColor = d.color;

    // Render Do's & Don'ts
    document.getElementById('guideDoList').innerHTML = d.dos
        .map(t => `<div class="guide-dd-item"><span class="guide-dd-bullet"></span><span>${t}</span></div>`)
        .join('');
    document.getElementById('guideDontList').innerHTML = d.donts
        .map(t => `<div class="guide-dd-item"><span class="guide-dd-bullet"></span><span>${t}</span></div>`)
        .join('');

    document.getElementById('guideOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

/** Menutup Guide Popup. */
function closeGuidePopup() {
    document.getElementById('guideOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

/**
 * Menutup Guide Popup jika klik di luar kotak.
 * @param {MouseEvent} e
 */
function closeGuideOnOverlay(e) {
    if (e.target === document.getElementById('guideOverlay')) closeGuidePopup();
}


// =============================================================
// 5. EVENT LISTENER KEYBOARD
//    Tombol Escape menutup semua popup yang sedang aktif.
// =============================================================

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closePopup();
        closeGuidePopup();
    }
});
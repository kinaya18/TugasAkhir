<!DOCTYPE html>
<html lang="id">

<head>
    <?= $this->include('layout/header') ?>
</head>

<body>

    <!-- ================= TOPBAR ================= -->
    <div class="topbar d-flex justify-content-between align-items-center">

        <!-- LEFT -->
        <div class="topbar-left d-flex align-items-center">
            <div class="logo d-flex align-items-center">

                <a href="<?= base_url('dashboard') ?>"
                    class="d-flex align-items-center text-decoration-none">

                    <img src="<?= base_url('assets/images/logo/kualitas-udara.png') ?>"
                        class="logo-img"
                        alt="Logo">

                    <div class="logo-title-wrap ms-3">

                        <span class="logo-eyebrow">
                            SMART AIRCARE
                        </span>

                        <span class="logo-title">
                            Pemantau Kualitas Udara
                        </span>

                    </div>

                </a>

            </div>
        </div>

        <!-- RIGHT -->
        <div class="topbar-right d-flex align-items-center">

            <a href="#"
                id="topbar-location-link"
                target="_blank"
                class="topbar-location">

                <i class="fa-solid fa-location-dot"></i>

                <span id="topbar-location">
                    Mendeteksi lokasi...
                </span>

            </a>

        </div>

    </div>

    <!-- CONTENT -->
    <div class="main-content">
        <?= $this->renderSection('content') ?>
    </div>

    <?= $this->include('layout/footer') ?>

    <?= $this->renderSection('script') ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
async function detectLocation() {

    const locationEl = document.getElementById('topbar-location');
    const linkEl = document.getElementById('topbar-location-link');

    if (!locationEl) return;

    if (!navigator.geolocation) {
        locationEl.textContent = 'Lokasi tidak didukung';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        async (position) => {

            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            window.currentLat = lat;
            window.currentLon = lon;

            if (linkEl) {
                linkEl.href = `https://www.google.com/maps?q=${lat},${lon}`;
            }

            // 1) Coba cari nama POI/gedung terdekat lewat Overpass API
            const poiName = await getNearestPOIName(lat, lon);

            if (poiName) {
                locationEl.textContent = poiName;
                return;
            }

            // 2) Kalau tidak ada POI relevan, fallback ke Nominatim (jalan + kelurahan)
            const fallbackName = await getAddressFallback(lat, lon);
            locationEl.textContent = fallbackName;
        },

        (error) => {
            console.error(error);
            locationEl.textContent = 'Izin lokasi ditolak';
        },

        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

/**
 * Cari nama POI/bangunan terdekat menggunakan Overpass API (data OpenStreetMap).
 * Hanya mengambil elemen dengan tag yang relevan sebagai "tempat" (building, amenity,
 * office, shop, dll) supaya tidak kejaring nama elemen tidak relevan (jalan kecil,
 * pohon, tiang listrik, dll yang kebetulan punya tag "name").
 */
async function getNearestPOIName(lat, lon, radius = 150) {

    // Tag kunci yang menandakan "tempat/bangunan" yang relevan untuk ditampilkan.
    // Daftar ini bisa ditambah sesuai kebutuhan (mis. "leisure", "tourism", dll).
    const relevantKeys = [
        'building',
        'amenity',
        'office',
        'shop',
        'tourism',
        'leisure',
        'craft',
        'healthcare'
    ];

    // Query Overpass: ambil semua node/way ber-tag "name" dalam radius,
    // filter relevansinya (building/amenity/dll) dilakukan di JS lewat isRelevantPlace().
    const safeQuery = `
        [out:json][timeout:15];
        (
          node(around:${radius},${lat},${lon})[name];
          way(around:${radius},${lat},${lon})[name];
        );
        out center tags;
    `;

    try {
        const response = await fetch('https://overpass-api.de/api/interpreter', {
            method: 'POST',
            body: 'data=' + encodeURIComponent(safeQuery)
        });

        if (!response.ok) throw new Error('Overpass response not OK');

        const data = await response.json();

        if (!data.elements || data.elements.length === 0) {
            return null;
        }

        const withDistance = data.elements
            .filter(el => el.tags && el.tags.name)
            .filter(el => isRelevantPlace(el.tags, relevantKeys))
            .map(el => {
                const elLat = el.lat ?? el.center?.lat;
                const elLon = el.lon ?? el.center?.lon;

                return {
                    name: el.tags.name,
                    distance: haversineDistance(lat, lon, elLat, elLon)
                };
            })
            .sort((a, b) => a.distance - b.distance);

        return withDistance.length > 0 ? withDistance[0].name : null;

    } catch (err) {
        console.error('Overpass error:', err);
        return null;
    }
}

/**
 * Cek apakah sebuah elemen OSM relevan sebagai "tempat" yang layak ditampilkan,
 * berdasarkan ada/tidaknya salah satu key dari relevantKeys di tags-nya.
 */
function isRelevantPlace(tags, relevantKeys) {
    return relevantKeys.some(key => tags[key] !== undefined);
}

/**
 * Fallback: ambil nama jalan + kelurahan lewat Nominatim reverse geocoding.
 * Diprioritaskan: jalan, lalu kelurahan/desa (lebih presisi dari kecamatan).
 */
async function getAddressFallback(lat, lon) {

    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=18&addressdetails=1`
        );

        const data = await response.json();
        const addr = data.address || {};

        // Urutan prioritas: jalan -> kelurahan/desa -> kecamatan -> kota
        const location = [
            addr.road,
            addr.village || addr.suburb,
            addr.city_district || addr.suburb,
            addr.city || addr.town || addr.county
        ]
        .filter(Boolean)
        // Hilangkan duplikat berurutan (mis. suburb terpilih dua kali)
        .filter((value, index, arr) => arr.indexOf(value) === index)
        .slice(0, 2) // ambil 2 level teratas saja biar tidak terlalu panjang
        .join(', ');

        return location || 'Lokasi tidak diketahui';

    } catch (err) {
        console.error('Nominatim error:', err);
        return 'Lokasi tidak diketahui';
    }
}

/**
 * Hitung jarak antar dua koordinat (meter) menggunakan formula Haversine.
 */
function haversineDistance(lat1, lon1, lat2, lon2) {

    const R = 6371000; // radius bumi dalam meter
    const toRad = deg => deg * Math.PI / 180;

    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}

document.addEventListener('DOMContentLoaded', detectLocation);
</script>

</body>

</html>
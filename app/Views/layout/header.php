<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Pemantau Kualitas Udara' ?></title>

    <!-- Flaticon Icons -->
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-regular-rounded/css/uicons-regular-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-straight/css/uicons-solid-straight.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/header.css') ?>">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        const BASE_URL = "<?= base_url() ?>";
    </script>
</head>

<body>

<!-- ================= TOPBAR ================= -->
<div class="topbar d-flex justify-content-between align-items-center">

    <!-- LEFT -->
    <div class="topbar-left d-flex align-items-center">
        <div class="logo d-flex align-items-center">
            <a href="<?= base_url('dashboard') ?>" class="d-flex align-items-center text-decoration-none">
                <img src="<?= base_url('assets/images/logo/kualitas-udara.png') ?>" class="logo-img" />
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

<script>
    function detectLocation() {

    const locationEl = document.getElementById('topbar-location');

    if (!navigator.geolocation) {
        locationEl.textContent = 'Lokasi tidak didukung';
        return;
    }

    navigator.geolocation.getCurrentPosition(

        async (position) => {

            try {

                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                window.currentLat = lat;
                window.currentLon = lon;

                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`
                );

                const data = await response.json();

                const addr = data.address || {};

                const location = [
                    addr.road,
                    addr.suburb,
                    addr.village,
                    addr.city || addr.town,
                ]
                .filter(Boolean)
                .join(', ');

                console.log('Lokasi hasil OSM:', location);

                locationEl.textContent = location;
                document.getElementById('topbar-location-link').href =
                    `https://www.google.com/maps?q=${lat},${lon}`;
                locationEl.dataset.loaded = 'true';

            } catch (err) {

                console.error(err);

                locationEl.textContent =
                    'Lokasi tidak diketahui';
            }
        },

        (error) => {

            console.error(error);

            locationEl.textContent =
                'Izin lokasi ditolak';
        }
    );
}
detectLocation();
</script>

</body>
</html>
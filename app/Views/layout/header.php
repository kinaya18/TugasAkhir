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
        <button class="sidebar-toggle me-3">
            <i class="fas fa-bars text-white"></i>
        </button>
        <div class="logo d-flex align-items-center">
            <a href="<?= base_url('dashboard') ?>" class="d-flex align-items-center text-decoration-none">
                <img src="<?= base_url('assets/images/logo/kualitas-udara.png') ?>" class="logo-img" />
                <span class="logo-text logo-white ms-2">Pemantau Kualitas Udara</span>
            </a>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="topbar-right d-flex align-items-center">
        <div class="notif-wrap position-relative">

            <!-- TOMBOL NOTIF -->
            <button class="notif-btn" id="notifBtn">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-badge" id="notifBadge">3</span>
            </button>

            <!-- PANEL NOTIFIKASI -->
            <div class="notif-panel" id="notifPanel">

                <!-- HEADER -->
                <div class="notif-panel-head">
                    <div>
                        <div class="notif-panel-title">Notifikasi</div>
                        <div class="notif-panel-sub">Peringatan kualitas udara aktif</div>
                    </div>
                    <span class="notif-count-badge" id="notifCountBadge">3 baru</span>
                </div>

                <!-- LIST -->
                <div class="notif-list">

                    <!-- ITEM 1 — WASPADA -->
                    <div class="notif-item notif-yellow" data-id="1">
                        <div class="notif-icon-wrap notif-icon-yellow">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-title notif-title-yellow">Udara Sedang — Waspada</div>
                            <div class="notif-desc notif-desc-yellow">Kualitas udara menurun, buka ventilasi atau nyalakan purifier</div>
                            <div class="notif-meta notif-meta-yellow">
                                <span>AQI 51–100</span>
                                <span class="meta-dot"></span>
                                <span>Kurangi aktivitas outdoor</span>
                                <span class="meta-dot"></span>
                                <span>Baru saja</span>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 2 — SENSITIF -->
                    <div class="notif-item notif-orange" data-id="2">
                        <div class="notif-icon-wrap notif-icon-orange">
                            <i class="fa-solid fa-circle-info"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-title notif-title-orange">Tidak Sehat bagi Kelompok Sensitif</div>
                            <div class="notif-desc notif-desc-orange">Berbahaya bagi penderita asma, segera tingkatkan sirkulasi udara</div>
                            <div class="notif-meta notif-meta-orange">
                                <span>AQI 101–150</span>
                                <span class="meta-dot"></span>
                                <span>Gunakan masker</span>
                                <span class="meta-dot"></span>
                                <span>30 mnt lalu</span>
                            </div>
                        </div>
                    </div>

                    <!-- ITEM 3 — TIDAK SEHAT -->
                    <div class="notif-item notif-red" data-id="3">
                        <div class="notif-icon-wrap notif-icon-red">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="notif-body">
                            <div class="notif-title notif-title-red">Tidak Sehat — Berbahaya</div>
                            <div class="notif-desc notif-desc-red">Udara sangat buruk, tinggalkan ruangan atau gunakan purifier segera</div>
                            <div class="notif-meta notif-meta-red">
                                <span>AQI 151–200</span>
                                <span class="meta-dot"></span>
                                <span>Tetap di dalam ruangan</span>
                                <span class="meta-dot"></span>
                                <span>1 jam lalu</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER -->
                <div class="notif-panel-footer">
                    <span class="notif-footer-hint">Klik item untuk tandai dibaca</span>
                    <button class="notif-mark-all" id="markAllBtn">Tandai semua dibaca</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
/* ===== NOTIF LOGIC ===== */
(function () {
    var count = 3;

    document.getElementById('notifBtn').addEventListener('click', function (e) {
        e.stopPropagation();
        document.getElementById('notifPanel').classList.toggle('show');
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.notif-wrap')) {
            document.getElementById('notifPanel').classList.remove('show');
        }
    });

    document.querySelectorAll('.notif-item').forEach(function (el) {
        el.addEventListener('click', function () {
            if (el.classList.contains('read')) return;
            el.classList.add('read');
            count = Math.max(0, count - 1);
            updateBadge();
        });
    });

    document.getElementById('markAllBtn').addEventListener('click', function () {
        document.querySelectorAll('.notif-item').forEach(function (el) {
            el.classList.add('read');
        });
        count = 0;
        updateBadge();
    });

    function updateBadge() {
        var badge = document.getElementById('notifBadge');
        var countBadge = document.getElementById('notifCountBadge');
        if (count === 0) {
            badge.style.display = 'none';
            countBadge.textContent = '0 baru';
            countBadge.style.background = '#f1f5f9';
            countBadge.style.color = '#64748b';
            countBadge.style.borderColor = '#e2e8f0';
        } else {
            badge.style.display = 'flex';
            badge.textContent = count;
            countBadge.textContent = count + ' baru';
        }
    }
})();
</script>

</body>
</html>
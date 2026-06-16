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

</body>

</html>
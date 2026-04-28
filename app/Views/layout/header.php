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

    <!-- RIGHT (KOSONG / SIAP DIISI) -->
    <div class="topbar-right d-flex align-items-center">
        <!-- nanti bisa isi profile / avatar di sini -->
    </div>

</div>

</body>
</html>
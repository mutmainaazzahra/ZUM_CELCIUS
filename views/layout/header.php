<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zum Celcius - Weather & Activity</title>
    <link href="../node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="../node_modules/chart.js/dist/chart.umd.js"></script>

    <style>
        :root {
            --bg-color: #fffffe;
            --headline: #272343;
            --paragraph: #2d334a;
            --button: #ffd803;
            --button-text: #272343;
            --secondary: #e3f6f5;
            --tertiary: #bae8e8;
            --stroke: #272343;
            --highlight: #ffd803;

            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: 1px solid rgba(39, 35, 67, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(39, 35, 67, 0.05);
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Poppins', sans-serif;
            color: var(--paragraph);
            min-height: 100vh;
            overflow-x: hidden;
        }

        body::before {
            content: none;
        }

        .glass-card,
        .card,
        .modal-content,
        .dropdown-menu {
            background: #ffffff !important;
            border: 2px solid var(--tertiary) !important;
            border-radius: 16px !important;
            box-shadow: var(--glass-shadow);
            color: var(--paragraph) !important;
        }

        .dropdown-item {
            color: var(--headline);
        }

        .dropdown-item:hover,
        .dropdown-item:focus,
        .dropdown-item:active,
        .dropdown-item.active {
            background-color: var(--secondary) !important;
            color: var(--headline) !important;
            outline: 0;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .fw-bold,
        .navbar-brand {
            color: var(--headline) !important;
        }

        .text-white {
            color: var(--headline) !important;
        }

        .text-white-50 {
            color: var(--paragraph) !important;
            opacity: 0.7;
        }

        .text-muted {
            color: var(--paragraph) !important;
            opacity: 0.6;
        }

        .text-warning {
            color: #d6b500 !important;
        }

        .bg-warning {
            background-color: var(--button) !important;
            color: var(--button-text) !important;
        }

        .btn-warning,
        .btn-primary {
            background-color: var(--button) !important;
            color: var(--button-text) !important;
            border: 2px solid var(--button) !important;
            box-shadow: 4px 4px 0px var(--headline);
            font-weight: 700;
            transition: all 0.2s;
            border-radius: 12px !important;
        }

        .btn-warning:hover,
        .btn-primary:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px var(--headline);
            background-color: #ffe033 !important;
        }

        .btn-outline-light,
        .btn-light {
            background: #fff !important;
            border: 2px solid var(--headline) !important;
            color: var(--headline) !important;
            border-radius: 12px !important;
            box-shadow: 4px 4px 0px var(--tertiary);
        }

        .btn-outline-light:hover,
        .btn-light:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px var(--tertiary);
        }

        .navbar-glass {
            background: rgba(255, 255, 254, 0.9) !important;
            backdrop-filter: blur(12px);
            border-bottom: 2px solid var(--tertiary);
            box-shadow: 0 2px 15px rgba(39, 35, 67, 0.03);
        }

        .form-control,
        .form-select {
            background: var(--secondary) !important;
            border: 2px solid transparent !important;
            color: var(--headline) !important;
            border-radius: 12px;
            padding: 0.6rem 1rem;
        }

        .form-control:focus {
            background: #fff !important;
            border-color: var(--headline) !important;
            box-shadow: none !important;
        }

        .form-control::placeholder {
            color: var(--paragraph);
            opacity: 0.5;
        }

        .btn-search-trigger {
            background: var(--secondary) !important;
            border: 2px solid transparent !important;
            color: var(--paragraph) !important;
            text-align: left;
            border-radius: 50px !important;
            transition: all 0.3s;
        }

        .btn-search-trigger:hover {
            border-color: var(--button) !important;
            background: #fff !important;
            box-shadow: 0 0 0 4px rgba(255, 216, 3, 0.15);
        }

        .table {
            color: var(--paragraph) !important;
        }

        .table-hover tbody tr:hover {
            background-color: var(--secondary) !important;
        }

        .table thead th {
            background-color: var(--tertiary) !important;
            color: var(--headline) !important;
            border: none;
            font-weight: 600;
        }

        .table td {
            border-bottom: 1px solid rgba(39, 35, 67, 0.05);
        }

        .notif-wrapper {
            position: relative;
            display: inline-block;
        }

        .notif-badge {
            position: absolute;
            top: 0px;
            right: 0px;
            width: 10px;
            height: 10px;
            background-color: #ff5f5f;
            border: 2px solid #fff;
            border-radius: 50%;
            display: block;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-color);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--tertiary);
            border-radius: 5px;
            opacity: 0.3;
        }

        .notif-message {
            white-space: normal !important;
            word-break: break-word;
            max-width: 100%;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-glass sticky-top py-3">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <div class="bg-warning rounded-circle p-2 me-2 d-flex align-items-center justify-content-center border border-dark" style="width: 40px; height: 40px;">
                    <i class="bi bi-cloud-sun-fill fs-5 text-dark"></i>
                </div>
                <span style="letter-spacing: 1px; color: var(--headline);">ZUM CELCIUS</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <div class="d-flex flex-grow-1 mx-lg-5 my-2 my-lg-0 position-relative">
                    <button type="button"
                        class="btn w-100 btn-search-trigger d-flex align-items-center p-1 ps-3 shadow-sm"
                        id="navSearchTrigger">
                        <i class="bi bi-search me-3" style="color: var(--headline);"></i>
                        <span class="small flex-grow-1 fw-medium" style="opacity: 0.7;">Cari lokasi atau ganti kota...</span>
                        <div class="bg-warning rounded-circle p-2 d-flex align-items-center justify-content-center text-dark" style="width: 36px; height: 36px;">
                            <i class="bi bi-geo-alt-fill" style="font-size: 0.9rem;"></i>
                        </div>
                    </button>
                </div>

                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column align-items-center text-dark" href="index.php" title="Beranda">
                            <i class="bi bi-house-door-fill fs-5" style="color: var(--headline);"></i>
                        </a>
                    </li>

                    <?php if (isset($_SESSION['user'])): ?>
                        <?php if ($_SESSION['user']['role'] == 'administrator'): ?>
                            <li class="nav-item">
                                <a class="nav-link d-flex flex-column align-items-center" href="index.php?page=admin_dashboard" title="Analitik">
                                    <i class="bi bi-graph-up-arrow fs-5" style="color: var(--headline);"></i>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link d-flex flex-column align-items-center" href="index.php?page=dashboard" title="Aktivitas">
                                    <i class="bi bi-list-check fs-5" style="color: var(--headline);"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                        <!-- Lonceng Notifikasi -->
                        <li class="nav-item dropdown">
                            <a class="nav-link p-0" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
                                <div class="notif-wrapper p-2">
                                    <i class="bi bi-bell-fill fs-5" style="color: var(--headline);"></i>
                                    <?php if (isset($unreadCount) && $unreadCount > 0): ?>
                                        <span id="red-dot" class="notif-badge"></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0" style="width: 360px; max-height: 400px; overflow-y: auto;">
                                <div class="p-3 border-bottom rounded-top" style="background: var(--tertiary);">
                                    <h6 class="mb-0 fw-bold" style="color: var(--headline);">Notifikasi</h6>
                                </div>
                                <?php if (empty($notifications)): ?>
                                    <div class="p-4 text-center text-muted small">Belum ada notifikasi baru</div>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notif): ?>
                                        <a class="dropdown-item py-3 border-bottom d-flex align-items-start" href="#">
                                            <div class="me-3 mt-1"><i class="bi bi-info-circle-fill text-warning"></i></div>
                                            <div style="flex: 1;">
                                                <p class="mb-1 small fw-medium notif-message" style="color: var(--headline);">
                                                    <?php echo htmlspecialchars($notif['message']); ?>
                                                </p>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo date('d M H:i', strtotime($notif['created_at'])); ?>
                                                </small>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </li>

                        <!-- Profil Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <?php
                                $navPhoto = !empty($_SESSION['user']['profile_photo']) ? "uploads/" . $_SESSION['user']['profile_photo'] : "https://placehold.co/40x40/bae8e8/272343?text=U";
                                ?>
                                <img src="<?php echo $navPhoto; ?>" class="rounded-circle border border-2 border-dark shadow-sm" width="40" height="40" style="object-fit:cover;">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end p-2 shadow-lg border-0">
                                <li class="px-3 py-2">
                                    <strong class="d-block" style="color: var(--headline);"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></strong>
                                    <small class="text-muted">
                                        <?php echo ($_SESSION['user']['role'] == 'administrator') ? 'Administrator' : 'Guest User'; ?>
                                    </small>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item rounded" href="index.php?page=profile"><i class="bi bi-person-gear me-2"></i> Edit Profil</a></li>
                                <li><a class="dropdown-item rounded text-danger" href="index.php?page=logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-2">
                            <a class="btn btn-outline-light rounded-pill px-4 fw-bold" href="index.php?page=login" style="color: var(--headline); border-color: var(--headline);">Masuk</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="btn btn-warning rounded-pill px-4 fw-bold" href="index.php?page=register">Daftar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script>
        const VAPID_PUBLIC_KEY = '<?php echo $_ENV['VAPID_PUBLIC_KEY'] ?? ""; ?>';

        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        }
        async function subscribeUser() {
            if (!('serviceWorker' in navigator) || !('PushManager' in window) || VAPID_PUBLIC_KEY.length < 10) return;
            try {
                const registration = await navigator.serviceWorker.register('sw.js', {
                    scope: './'
                });
                let permission = Notification.permission;
                if (permission === 'default') permission = await Notification.requestPermission();
                if (permission !== 'granted') return;
                const applicationServerKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
                await fetch('index.php?page=push_subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(subscription.toJSON())
                });
            } catch (error) {
                console.error('Sub Error:', error);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            <?php if (isset($_SESSION['user'])): ?>subscribeUser();
        <?php endif; ?>
        const notifBtn = document.getElementById('notifDropdown');
        const redDot = document.getElementById('red-dot');
        if (notifBtn && redDot) {
            notifBtn.addEventListener('click', function() {
                redDot.style.display = 'none';
                fetch('index.php?page=mark_read').then(r => r.json());
            });
        }

        const navSearch = document.getElementById('navSearchTrigger');
        if (navSearch) {
            navSearch.addEventListener('click', function() {
                if (document.getElementById('mapboxMap')) {
                    const mapModal = new bootstrap.Modal(document.getElementById('mapModal'));
                    mapModal.show();
                } else {
                    window.location.href = 'index.php?page=home';
                }
            });
        }
        });
    </script>

    <div class="container-fluid p-0">
<?php

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/koneksi.php';

$page = $_GET['page'] ?? null;

if ($page === null) {
    header('Location: ?page=home#hero');
    exit;
}

if ($page === 'login') {
    require_once __DIR__ . '/app/controllers/AuthController.php';

    $authController = new AuthController($pdo);

    $loginResult = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $loginResult = $authController->login(
            $_POST['email'] ?? '',
            $_POST['password'] ?? ''
        );

        if ($loginResult['success']) {
            if ($_SESSION['user']['role'] === 'pengguna') {
                header('Location: ./');
                exit;
            } else if ($_SESSION['user']['role'] === 'admin') {
                header('Location: ?page=admin');
                exit;
            }
        }
    }

    $cardData= $authController->cardStatistic();
    require __DIR__ . '/app/views/login.php';
    exit;
}

if ($page === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $authController = new AuthController($pdo);
        $registerResult = $authController->registerPengguna([
            'nama' => $_POST['nama'],
            'email' => $_POST['email'],
            'no_hp' => $_POST['no_hp'] ?? '',
            'nim' => $_POST['nim'],
            'password' => $_POST['password'],
            'konfirmasi_password' => $_POST['konfirmasi_password']
        ]);

        if ($registerResult['success']) {
            $_SESSION['registerMessage'] = $registerResult['message'];
            header('Location:?page=login');
            exit;
        }
    }

    require __DIR__ . '/app/views/register.php';
    exit;
}

if ($page === 'logout') {
    require_once __DIR__ . '/app/controllers/AuthController.php';

    $authController = new AuthController($pdo);
    $authController->logout();

    header('Location: ./');
    exit;
}

if ($page === 'admin') {
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    $adminPages = [
        'admin' => 'index.php',
        'admin/event' => 'event.php',
        'admin/pembayaran' => 'pembayaran.php'
    ];

    if (isset($adminPages[$page])) {
        require __DIR__ . '/app/views/admin/' . $adminPages[$page];
        exit;
    }
}

if ($page === 'admin/event') {
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        header('Location: ./');
        exit;
    }


    require __DIR__ . '/app/views/admin/event.php';
    exit;
}

if ($page === 'admin/pembayaran') {
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        header('Location: ./');
        exit;
    }


    require __DIR__ . '/app/views/admin/pembayaran.php';
    exit;
}

if ($page === 'admin/qr') {
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        header('Location: ./');
        exit;
    }
    require __DIR__ . '/app/views/admin/qr.php';
    exit;
}

if ($page === 'admin/laporan') {
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        header('Location: ./');
        exit;
    }
    require __DIR__ . '/app/views/admin/laporan.php';
    exit;
}

if ($page === 'home') {
    if (isset($_SESSION['user']['role'])) {
        if ($_SESSION['user']['role'] == 'admin') {
            header('Location: ?page=admin');
            exit;
        }
    }
    require_once __DIR__ . '/app/controllers/EventController.php';
    require_once __DIR__ . '/app/controllers/DashboardController.php';

    $eventController = new EventController($pdo);
    $dashboardController = new DashboardController($pdo);

    $eventsDb = $eventController->index();
    $orgs = $dashboardController->getUkm();
    $stats = $dashboardController->index();
    $featured = $dashboardController->recentEvent();
    $categories = $eventController->getCategory();
    array_unshift(
        $categories,
        ['nama_kategori' => 'Semua']
    );

    require __DIR__ . '/app/views/home.php';
    exit;
}

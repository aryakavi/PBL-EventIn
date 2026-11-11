<?php require_once __DIR__ . '/data.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$login   = isset($_SESSION['user']);
$nama    = $login ? $_SESSION['user']['nama'] : '';
$role    = $login ? $_SESSION['user']['role'] : '';
$inisial = $login ? mb_strtoupper(mb_substr($nama, 0, 1)) : '';

$activeNav = $activeNav ?? '';
$aktif = function ($key) use ($activeNav) {
    return $activeNav === $key ? ' is-active' : '';
};

$base = $base ?? '';
$user = $_SESSION['user'] ?? null;
?>
<header class="navbar">
    <a class="brand" href="<?= e($routes['home']) ?>">
        <span class="brand-mark">EI</span>
        <span class="brand-name">EventIn</span>
    </a>

    <nav class="user-nav" aria-label="Menu utama">
        <a href="<?= e($base . $routes['home']) ?>" class="user-nav-link<?= $aktif('home') ?>">Beranda</a>
        <a href="<?= e($routes['explore']) ?>" class="user-nav-link<?= $aktif('explore') ?>">Jelajah Event</a>
        <a href="<?= e($routes['partners']) ?>" class="user-nav-link<?= $aktif('partners') ?>">Mitra</a>
        <?php if ($login): ?>
            <a href="<?= e($base . $routes['tiket']) ?>" class="user-nav-link<?= $aktif('tiket') ?>">Tiket Saya</a>
        <?php endif; ?>
    </nav>

    <div class="navbar-right">
        <?php if ($user): ?>
            <div class="user-info">
                <strong><?= e($user['nama']) ?></strong><br>
                <span><?= e(ucfirst($user['role'])) ?></span>
            </div>
            <span class="avatar"><?= e($inisial) ?></span>
            <div class="user-menu-wrap">
                <button class="user-menu-btn" type="button" aria-label="Menu akun" aria-expanded="false"><?= icon('menu') ?></button>
                <div class="user-menu-dropdown" hidden>
                    <a href="#" class="user-menu-item">Edit Profil</a>
                    <a href="?page=logout" class="user-menu-item user-menu-item--danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a class="btn btn-outline" href="<?= e($routes['login']) ?>">Login</a>
            <a class="btn btn-primary" href="<?= e($routes['register']) ?>">Register</a>
        <?php endif; ?>
    </div>
</header>
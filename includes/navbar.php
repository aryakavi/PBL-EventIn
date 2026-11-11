<?php
require_once __DIR__ . '/data.php';
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
?>
<header class="navbar user-navbar">
    <div class="navbar-left">
        <span class="logo-box">EI</span>
        <strong class="brand-name">EventIn</strong>
    </div>

    <nav class="user-nav" aria-label="Menu utama">
        <a href="<?= e($base . $routes['home']) ?>" class="user-nav-link<?= $aktif('home') ?>">Beranda</a>
        <a href="<?= e($routes['explore']) ?>" class="user-nav-link<?= $aktif('explore') ?>">Jelajah Event</a>
        <a href="<?= e($routes['partners']) ?>" class="user-nav-link<?= $aktif('partners') ?>">Mitra</a>
        <?php if ($login): ?>
            <a href="<?= e($base . $routes['tiket']) ?>" class="user-nav-link<?= $aktif('tiket') ?>">Tiket Saya</a>
        <?php endif; ?>
    </nav>

    <div class="navbar-right">
        <?php if ($login): ?>
            <div class="profile">
                <span class="profile-name"><?= e($nama) ?></span>
                <span class="profile-role"><?= e($role) ?></span>
            </div>
            <span class="avatar"><?= e($inisial) ?></span>
            <div class="user-menu-wrap">
                <button class="user-menu-btn" type="button" aria-label="Menu akun" aria-expanded="false"><?= icon('menu') ?></button>
                <div class="user-menu-dropdown" hidden>
                    <a href="<?= e($base . 'app/views/profil.php') ?>" class="user-menu-item">Edit Profil</a>
                    <a href="#" class="user-menu-item user-menu-item--danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a class="btn btn-outline" href="<?= e($base . $routes['login']) ?>">Login</a>
            <a class="btn btn-primary" href="<?= e($base . $routes['register']) ?>">Register</a>
        <?php endif; ?>
    </div>
</header>

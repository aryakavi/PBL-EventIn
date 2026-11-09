<?php
require_once __DIR__ . '/data.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$login   = isset($_SESSION['user']);
$nama    = $login ? $_SESSION['user']['nama'] : '';
$role    = $login ? ($_SESSION['user']['role'] ?? '') : '';
$inisial = $login ? user_initials($nama) : '';
$isAdmin = $login && is_admin_role($role);

$activeNav = $activeNav ?? '';
$aktif = function ($key) use ($activeNav) {
    return $activeNav === $key ? ' is-active' : '';
};

$base = $base ?? '';

// Tujuan menu akun mengikuti role pengguna.
$profilHref    = $base . ($isAdmin ? $routes['admin-edit-profil'] : $routes['edit-profil']);
$dashboardHref = $base . $routes['admin-dashboard'];
$logoutHref    = $base . $routes['logout'];
?>
<header class="navbar user-navbar">
    <a class="navbar-left brand-link" href="<?= e($base . $routes['home']) ?>" aria-label="EventIn - Beranda">
        <span class="logo-box">EI</span>
        <strong class="brand-name">EventIn</strong>
    </a>

    <nav class="user-nav" aria-label="Menu utama">
        <a href="<?= e($base . $routes['home']) ?>" class="user-nav-link<?= $aktif('home') ?>">Beranda</a>
        <a href="<?= e($base . $routes['explore']) ?>" class="user-nav-link<?= $aktif('explore') ?>">Jelajah Event</a>
        <a href="<?= e($base . $routes['partners']) ?>" class="user-nav-link<?= $aktif('partners') ?>">Mitra</a>
        <?php if ($login && !$isAdmin): ?>
            <a href="<?= e($base . $routes['tiket']) ?>" class="user-nav-link<?= $aktif('tiket') ?>">Tiket Saya</a>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
            <a href="<?= e($dashboardHref) ?>" class="user-nav-link<?= $aktif('admin') ?>">Dashboard</a>
        <?php endif; ?>
    </nav>

    <div class="navbar-right">
        <?php if ($login): ?>
            <div class="profile">
                <span class="profile-name"><?= e($nama) ?></span>
                <span class="profile-role"><?= e(role_label($role)) ?></span>
            </div>
            <span class="avatar"><?= e($inisial) ?></span>
            <div class="user-menu-wrap">
                <button class="user-menu-btn" type="button" aria-label="Menu akun <?= e($nama) ?>"
                        aria-expanded="false" aria-haspopup="true">&#9776;</button>
                <div class="user-menu-dropdown" hidden>
                    <p class="user-menu-head"><?= e($nama) ?></p>
                    <?php if ($isAdmin): ?>
                        <a href="<?= e($dashboardHref) ?>" class="user-menu-item">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= e($profilHref) ?>" class="user-menu-item">Edit Profil</a>
                    <a href="<?= e($logoutHref) ?>" class="user-menu-item user-menu-item--danger">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a class="btn btn-outline" href="<?= e($base . $routes['login']) ?>">Login</a>
            <a class="btn btn-primary" href="<?= e($base . $routes['register']) ?>">Register</a>
        <?php endif; ?>
    </div>
</header>

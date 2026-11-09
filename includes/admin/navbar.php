<?php
if (!function_exists('e')) {
    require_once __DIR__ . '/data.php';
}

$organizer = $organizer ?? [
    'nama'    => 'HMTI',
    'role'    => 'ADMIN ORGANISASI',
    'inisial' => 'H',
];
?>
<header class="navbar">
    <div class="navbar-left">
        <button class="navbar-toggle" type="button" aria-label="Buka menu"
                aria-controls="sidebar" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <a href="../../../index.php" class="navbar-back" aria-label="Kembali ke beranda">&larr;</a>
        <a href="../../../index.php" class="navbar-brand" aria-label="EventIn - Beranda">
            <span class="logo-box">EI</span>
            <span class="brand">
                <strong class="brand-name">EventIn</strong>
                <span class="brand-sub">Dashboard Penyelenggara</span>
            </span>
        </a>
    </div>
    <div class="navbar-right">
        <div class="profile">
            <span class="profile-name"><?= e($organizer['nama']) ?></span>
            <span class="profile-role"><?= e($organizer['role']) ?></span>
        </div>
        <span class="avatar"><?= e($organizer['inisial']) ?></span>
        <div class="user-menu-wrap">
            <button class="user-menu-btn" type="button" aria-label="Menu akun"
                    aria-expanded="false" aria-haspopup="true">&#9776;</button>
            <div class="user-menu-dropdown" hidden>
                <p class="user-menu-head"><?= e($organizer['nama']) ?></p>
                <a href="index.php" class="user-menu-item">Dashboard</a>
                <a href="profil.php" class="user-menu-item">Edit Profil</a>
                <a href="../../../logout.php" class="user-menu-item user-menu-item--danger">Logout</a>
            </div>
        </div>
    </div>
</header>

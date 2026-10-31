<?php
if (!function_exists('e')) {
    require_once __DIR__ . '/data.php';
}

$organizer = $organizer ?? ['nama' => 'HMTI', 'role' => 'ORGANIZER', 'inisial' => 'H'];
?>
<header class="navbar">
    <div class="navbar-left">
        <button class="navbar-toggle" type="button" aria-label="Buka menu"
                aria-controls="sidebar" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>
        <a href="index.php" class="navbar-back" aria-label="Kembali">&larr;</a>
        <span class="logo-box">EI</span>
        <div class="brand">
            <strong class="brand-name">EventIn</strong>
            <span class="brand-sub">Dashboard Penyelenggara</span>
        </div>
    </div>
    <div class="navbar-right">
        <div class="profile">
            <span class="profile-name"><?= e($organizer['nama']) ?></span>
            <span class="profile-role"><?= e($organizer['role']) ?></span>
        </div>
        <span class="avatar"><?= e($organizer['inisial']) ?></span>
    </div>
</header>

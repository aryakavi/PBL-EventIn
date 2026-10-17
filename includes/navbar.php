<?php require_once __DIR__ . '/data.php'; ?>
<header class="navbar">
    <a class="brand" href="<?= e($routes['home']) ?>">
        <span class="brand-mark">EI</span>
        <span class="brand-name">EventIn</span>
    </a>

    <nav class="menu" aria-label="Menu utama">
        <a class="is-active" href="<?= e($routes['home']) ?>">Beranda</a>
        <a href="<?= e($routes['explore']) ?>">Jelajah Event</a>
        <a href="<?= e($routes['partners']) ?>">Mitra</a>
    </nav>

    <div class="actions">
        <a class="btn btn-outline" href="<?= e($routes['login']) ?>">Login</a>
        <a class="btn btn-primary" href="<?= e($routes['register']) ?>">Register</a>
    </div>
</header>

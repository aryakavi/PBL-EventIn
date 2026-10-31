<?php
if (!function_exists('e')) {
    require_once __DIR__ . '/data.php';
}

$menu = $menu ?? [];
$halamanAktif = $halamanAktif ?? '';
?>
<aside class="sidebar" id="sidebar">
    <p class="sidebar-title">MENU NAVIGASI</p>
    <nav>
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
                <li>
                    <a href="<?= e($item['href']) ?>"
                       class="menu-item<?= $item['key'] === $halamanAktif ? ' is-active' : '' ?>">
                        <?= e($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

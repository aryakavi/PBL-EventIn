<?php
if (!function_exists('e')) {
    require_once __DIR__ . '/data.php';
}

$menu = $menu ?? [];
$menuLogout = $menuLogout ?? null;
$halamanAktif = $halamanAktif ?? '';
?>
<aside class="sidebar" id="sidebar">
    <p class="sidebar-title">MENU NAVIGASI</p>
    <nav>
        <ul class="menu">
            <?php foreach ($menu as $item): ?>
                <li>
                    <a href="<?= e($item['href']) ?>"
                       class="menu-item<?= $item['key'] === $halamanAktif ? ' is-active' : '' ?><?= !empty($item['mobile']) ? ' menu-item--mobile-only' : '' ?>">
                        <?= e($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <?php if ($menuLogout): ?>
        <div class="sidebar-mobile-menu">
            <a href="<?= e($menuLogout['href']) ?>"
               class="menu-item menu-item--logout<?= $menuLogout['key'] === $halamanAktif ? ' is-active' : '' ?>">
                <?= e($menuLogout['label']) ?>
            </a>
        </div>
    <?php endif; ?>
</aside>

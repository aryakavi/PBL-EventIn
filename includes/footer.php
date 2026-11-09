<?php
require_once __DIR__ . '/data.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$login = isset($_SESSION['user']);
$isAdmin = $login && is_admin_role($_SESSION['user']['role'] ?? '');
$base = $base ?? '';

$footerSupportHref = [
    'Pusat Bantuan'      => $routes['faq'] ?? ($routes['home'] . '#faq'),
    'Syarat & Ketentuan' => $routes['home'] . '#faq',
    'Kebijakan Privasi'  => $routes['home'] . '#faq',
];
$socialHref = [
    'Fb' => 'https://www.facebook.com/',
    'Ig' => 'https://www.instagram.com/',
    'X'  => 'https://x.com/',
    'In' => 'https://www.linkedin.com/',
];
$footerNavHref = [
    'Beranda'       => $routes['home'],
    'Jelajah Event' => $routes['explore'],
    'Mitra Kampus'  => $routes['partners'],
];
?>
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col footer-brand">
            <a class="footer-logo" href="<?= e($base . $routes['home']) ?>" aria-label="EventIn - Beranda">
                <span class="footer-logo-mark">EI</span>
                <span class="footer-logo-name">EventIn</span>
            </a>
            <p>Platform manajemen dan penemuan event terbaik untuk seluruh sivitas akademika kampus.</p>
        </div>

        <div class="footer-col footer-links">
            <h4>Navigasi</h4>
            <?php foreach ($footerNav as $label): ?>
                <a href="<?= e($base . ($footerNavHref[$label] ?? $routes['home'])) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <?php if ($login && !$isAdmin): ?>
                <a href="<?= e($base . $routes['tiket']) ?>">Tiket Saya</a>
            <?php endif; ?>
        </div>

        <div class="footer-col footer-links">
            <h4>Dukungan</h4>
            <?php foreach ($footerSupport as $label): ?>
                <a href="<?= e($base . ($footerSupportHref[$label] ?? $routes['home'])) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="footer-col footer-social">
            <h4>Sosial Media</h4>
            <div class="social-row">
                <?php foreach ($socials as $s): ?>
                    <a class="social" href="<?= e($socialHref[$s] ?? '#') ?>" target="_blank" rel="noopener" aria-label="<?= e($s) ?>"><?= e($s) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 EventIn. Seluruh hak cipta dilindungi.</p>
        <p>Politeknik Negeri Malang</p>
    </div>
</footer>

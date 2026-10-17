<?php require_once __DIR__ . '/data.php'; ?>
<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col footer-brand">
            <div class="footer-logo">
                <span class="footer-logo-mark">EI</span>
                <span class="footer-logo-name">EventIn</span>
            </div>
            <p>Platform manajemen dan penemuan event terbaik untuk seluruh sivitas akademika kampus.</p>
        </div>

        <div class="footer-col footer-links">
            <h4>Navigasi</h4>
            <?php foreach ($footerNav as $label): ?>
                <a href="#"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="footer-col footer-links">
            <h4>Dukungan</h4>
            <?php foreach ($footerSupport as $label): ?>
                <a href="#"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="footer-col footer-social">
            <h4>Sosial Media</h4>
            <div class="social-row">
                <?php foreach ($socials as $s): ?>
                    <a class="social" href="#" aria-label="<?= e($s) ?>"><?= e($s) ?></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2026 EventIn. Seluruh hak cipta dilindungi.</p>
        <p>Politeknik Negeri Malang</p>
    </div>
</footer>

<?php
require __DIR__ . '/../../../includes/admin/data.php';
require __DIR__ . '/../../../includes/icons.php';
$halamanAktif = 'qr';


$eventDipilih = $_GET['event'] ?? '';
if (!array_key_exists($eventDipilih, $daftarQrEvent)) {
    $eventDipilih = null;
}

$qrAktif = $eventDipilih !== null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'QR Check-in Loket';
$assetBase  = '../../../assets';
$pageStyles = ['admin.css'];
require __DIR__ . '/../../../includes/head.php';
?>
</head>
<body>

<?php include __DIR__ . '/../../../includes/admin/navbar.php'; ?>

<div class="layout">
    <?php include __DIR__ . '/../../../includes/admin/sidebar.php'; ?>

    <main class="content">
        <div class="welcome">
            <div>
                <h1>QR Check-in Loket</h1>
                <p>Lakukan pemindaian QR code elektronik peserta secara langsung menggunakan modul kamera.</p>
            </div>
            <div>
                <form method="get" action="">
                    <label for="event_qr" class="sr-only">Pilih event</label>
                    <select name="event" id="event_qr" class="filter-select" onchange="this.form.submit()">
                        <option value=""<?= $qrAktif ? '' : ' selected' ?> disabled>Pilih Event..</option>
                        <?php foreach ($daftarQrEvent as $slug => $nama): ?>
                            <option value="<?= e($slug) ?>"<?= $eventDipilih === $slug ? ' selected' : '' ?>><?= e($nama) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if ($qrAktif): ?>
        <!-- Area Pemindai & Live Feed -->
        <section class="qr-grid">
            <!-- Kolom Kiri: Pemindai Kamera -->
            <div class="qr-col">
                <h2 class="qr-section-title">Pemindai Kamera Aktif</h2>
                <div class="scanner-box" role="img" aria-label="Area pemindai kamera">
                    <div class="scanner-icon">[ ]</div>
                    <strong class="scanner-title">Arahkan QR Code ke Kamera</strong>
                    <p class="scanner-sub">Sistem otomatis mengidentifikasi dan mencocokkan kredensial peserta secara real-time</p>
                </div>
            </div>

            <!-- Kolom Kanan: Presensi Terkini -->
            <div class="qr-col">
                <h2 class="qr-section-title">Presensi Terkini (Live Feed)</h2>
                <div class="feed-card">
                    <ul class="feed-list" aria-label="Presensi terkini">
                        <?php foreach ($presensiTerkini as $p): ?>
                            <li class="feed-item">
                                <span class="feed-avatar"><?= e($p['inisial']) ?></span>
                                <div class="feed-info">
                                    <strong class="feed-name"><?= e($p['nama']) ?></strong>
                                    <span class="feed-time"><?= e($p['waktu']) ?></span>
                                </div>
                                <span class="badge badge-success badge-small"><?= e($p['status']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Ringkasan Statistik Kehadiran (3 Kolom) -->
        <section class="kpi kpi-cols-3">
            <?php foreach ($kpiQr as $k): ?>
                <article class="kpi-card">
                    <span class="kpi-label muted"><?= e($k['label']) ?></span>
                    <strong class="kpi-value"><?= e($k['nilai']) ?></strong>
                    <span class="kpi-trend">
                        <span class="kpi-arrow"><?= $k['arah'] === 'up' ? '&uarr;' : '&darr;' ?></span>
                        <?= e($k['tren']) ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </section>
        <?php else: ?>
            <p class="qr-empty">Silakan pilih event terlebih dahulu untuk memulai pemindaian.</p>
        <?php endif; ?>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js"></script>
</body>
</html>
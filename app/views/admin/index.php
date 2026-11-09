<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Dashboard Penyelenggara';
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
                <h1>Selamat Datang</h1>
                <p>Berikut aktivitas pendaftaran, pendapatan, dan partisipasi event kemahasiswaan Anda.</p>
            </div>
            <a href="event.php#form-event" class="btn btn-primary">Buat Event Baru</a>
        </div>

        <section class="kpi">
            <?php foreach ($kpi as $k): ?>
                <article class="kpi-card">
                    <span class="kpi-label"><?= e($k['label']) ?></span>
                    <strong class="kpi-value"><?= e($k['nilai']) ?></strong>
                    <span class="kpi-trend">
                        <span class="kpi-arrow"><?= $k['arah'] === 'up' ? '&uarr;' : '&darr;' ?></span>
                        <?= e($k['tren']) ?>
                    </span>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="activity">
            <h2>Aktivitas Pendaftaran Terbaru</h2>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aktivitas as $row): ?>
                            <tr>
                                <td><?= e($row['event']) ?></td>
                                <td class="muted"><?= e($row['tanggal']) ?></td>
                                <td><?= e($row['peserta']) ?></td>
                                <td><span class="badge badge-success"><?= e($row['status']) ?></span></td>
                                <td class="actions">
                                    <a href="pembayaran.php?transaksi=<?= e($row['id'] ?? '') ?>" class="actions-detail">Detail</a>
                                    <span class="actions-sep">|</span>
                                    <a href="pembayaran.php?status=menunggu" class="actions-verify">Verifikasi</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>



<script src="../../../assets/js/admin.js"></script>
</body>
</html>

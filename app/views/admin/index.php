<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Penyelenggara | EventIn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../assets/css/admin.css">
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
            <a href="#" class="btn btn-primary">Buat Event Baru</a>
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
                                    <a href="#" class="actions-detail">Detail</a>
                                    <span class="actions-sep">|</span>
                                    <a href="#" class="actions-verify">Verifikasi</a>
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

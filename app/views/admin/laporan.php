<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'laporan';

$tanggal  = $_GET['tanggal'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$rincianFiltered = array_values(array_filter($rincianLaporan, function ($row) use ($tanggal, $kategori) {
    if ($tanggal !== '' && $row['tanggal'] !== $tanggal) {
        return false;
    }
    if ($kategori !== '' && $row['kategori'] !== $kategori) {
        return false;
    }
    return true;
}));
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Laporan & Analitik';
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
                <h1>Laporan &amp; Analitik</h1>
                <p>Analisis performa pendaftaran event, tingkat kehadiran peserta, dan kelayakan e-sertifikat SKPI.</p>
            </div>
            <form method="get" action="" class="report-filters">
                <label for="tanggal" class="sr-only">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="filter-select"
                    value="<?= e($tanggal) ?>" onchange="this.form.submit()">
                <label for="kategori" class="sr-only">Kategori</label>
                <select name="kategori" id="kategori" class="filter-select" onchange="this.form.submit()">
                    <option value="">Kategori</option>
                    <option value="Seminar"<?= $kategori === 'Seminar' ? ' selected' : '' ?>>Seminar</option>
                    <option value="Workshop"<?= $kategori === 'Workshop' ? ' selected' : '' ?>>Workshop</option>
                    <option value="Kompetisi"<?= $kategori === 'Kompetisi' ? ' selected' : '' ?>>Kompetisi</option>
                </select>
            </form>
        </div>

        <!-- Ringkasan Statistik (4 Kolom KPI) -->
        <section class="kpi">
            <?php foreach ($kpiLaporan as $k): ?>
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

        <!-- Tabel Rincian Data Pendaftar -->
        <section class="activity">
            <div class="section-header">
                <h2>Rincian Data Pendaftar Terverifikasi</h2>
                <div class="export-actions">
                    <a href="export-csv.php?<?= e(http_build_query(array_filter(['tanggal' => $tanggal, 'kategori' => $kategori]))) ?>" class="btn btn-outline">Export CSV</a>
                    <a href="laporan.php" class="btn btn-primary" onclick="window.print();return false;">Export PDF</a>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Email Kampus</th>
                            <th scope="col">Status Pembayaran</th>
                            <th scope="col">Kehadiran</th>
                            <th scope="col">Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rincianFiltered)): ?>
                            <tr>
                                <td colspan="5" class="muted" style="text-align:center">Tidak ada data yang cocok dengan filter.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($rincianFiltered as $row): ?>
                            <?php
                            if ($row['status'] === 'Lunas') {
                                $badgeClass = 'badge-success';
                            } elseif ($row['status'] === 'Tertunda') {
                                $badgeClass = 'badge-warning';
                            } else {
                                $badgeClass = 'badge-danger';
                            }
                            ?>
                            <tr>
                                <td><strong><?= e($row['nama']) ?></strong></td>
                                <td class="mono-email"><?= e($row['email']) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= e($row['status']) ?></span></td>
                                <td><?= e($row['kehadiran']) ?></td>
                                <td class="muted"><?= e($row['sertifikat']) ?></td>
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
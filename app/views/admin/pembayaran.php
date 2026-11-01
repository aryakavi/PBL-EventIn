<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'pembayaran';

$statusMap = [
    'semua'        => null,
    'menunggu'     => 'Menunggu',
    'diverifikasi' => 'Diverifikasi',
    'ditolak'      => 'Ditolak',
];

$statusTab = $_GET['status'] ?? 'semua';
if (!array_key_exists($statusTab, $statusMap)) {
    $statusTab = 'semua';
}

$statusFilter = $statusMap[$statusTab];

$pembayaranFiltered = $statusFilter === null
    ? $daftarPembayaran
    : array_values(array_filter(
        $daftarPembayaran,
        static fn($p) => $p['status'] === $statusFilter
    ));

$idTransaksi = $_GET['transaksi'] ?? null;
$transaksiAktif = null;

if ($idTransaksi !== null) {
    foreach ($daftarPembayaran as $p) {
        if ($p['id'] === $idTransaksi) {
            $transaksiAktif = $p;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran | EventIn</title>
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
                <h1>Verifikasi Pembayaran</h1>
                <p>Pantau log transaksi, periksa bukti transfer dari peserta, dan sahkan tiket masuk secara cepat.</p>
            </div>
        </div>

        <!-- Tab Filter Status -->
        <div class="status-tabs" role="tablist" aria-label="Filter status pembayaran">
            <a href="?status=semua" class="tab-item<?= $statusTab === 'semua' ? ' is-active' : '' ?>"
               role="tab"<?= $statusTab === 'semua' ? ' aria-current="page"' : '' ?>>Semua</a>
            <a href="?status=menunggu" class="tab-item<?= $statusTab === 'menunggu' ? ' is-active' : '' ?>"
               role="tab"<?= $statusTab === 'menunggu' ? ' aria-current="page"' : '' ?>>Menunggu</a>
            <a href="?status=diverifikasi" class="tab-item<?= $statusTab === 'diverifikasi' ? ' is-active' : '' ?>"
               role="tab"<?= $statusTab === 'diverifikasi' ? ' aria-current="page"' : '' ?>>Diverifikasi</a>
            <a href="?status=ditolak" class="tab-item<?= $statusTab === 'ditolak' ? ' is-active' : '' ?>"
               role="tab"<?= $statusTab === 'ditolak' ? ' aria-current="page"' : '' ?>>Ditolak</a>
        </div>

        <!-- Tabel Log Transaksi -->
        <section class="payment-section">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID Transaksi</th>
                            <th scope="col">Peserta</th>
                            <th scope="col">Event</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Bukti</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pembayaranFiltered)): ?>
                            <tr>
                                <td colspan="7" class="muted" style="text-align:center">Tidak ada transaksi dengan status ini.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($pembayaranFiltered as $p): ?>
                            <?php
                            if ($p['status'] === 'Diverifikasi') {
                                $badgeClass = 'badge-success';
                            } elseif ($p['status'] === 'Menunggu') {
                                $badgeClass = 'badge-warning';
                            } else {
                                $badgeClass = 'badge-danger';
                            }
                            ?>
                            <tr>
                                <td class="mono-id"><?= e($p['id']) ?></td>
                                <td><?= e($p['peserta']) ?></td>
                                <td class="muted"><?= e($p['event']) ?></td>
                                <td><?= e($p['jumlah']) ?></td>
                                <td><span class="proof-tag">IMG</span></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= e($p['status']) ?></span></td>
                                <td class="actions">
                                    <a href="?status=<?= e($statusTab) ?>&transaksi=<?= e($p['id']) ?>#detail-transaksi" class="actions-detail">Periksa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Card Detail Transaksi -->
        <?php if ($transaksiAktif): ?>
            <section class="form-card" id="detail-transaksi">
                <div class="form-card-header">
                    <h2>Detail Transaksi: <?= e($transaksiAktif['id']) ?></h2>
                </div>

                <div class="detail-body">
                    <!-- Kolom Bukti Transfer -->
                    <div class="proof-col">
                        <span class="proof-title">Lampiran Bukti Transfer</span>
                        <div class="proof-preview">
                            <span class="proof-icon">&#128196;</span>
                            <span>Lampiran Bukti Transfer</span>
                        </div>
                        <span class="proof-filename"><?= e($transaksiAktif['berkas']) ?></span>
                    </div>

                    <!-- Kolom Informasi Transaksi -->
                    <div class="info-col">
                        <div class="info-item">
                            <span class="info-label">NAMA PESERTA</span>
                            <strong class="info-value"><?= e($transaksiAktif['peserta']) ?></strong>
                        </div>

                        <div class="info-item">
                            <span class="info-label">EVENT DIIKUTI</span>
                            <strong class="info-value"><?= e($transaksiAktif['event']) ?></strong>
                        </div>

                        <div class="info-item">
                            <span class="info-label">METODE PEMBAYARAN</span>
                            <span class="info-text"><?= e($transaksiAktif['metode']) ?></span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">WAKTU TRANSFER</span>
                            <span class="info-text"><?= e($transaksiAktif['waktu']) ?></span>
                        </div>

                        <div class="detail-actions">
                            <button type="button" class="btn btn-outline">Tolak Transaksi</button>
                            <button type="button" class="btn btn-primary">Verifikasi Pembayaran</button>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js"></script>
</body>
</html>
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
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Verifikasi Pembayaran';
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
                                <td colspan="7" class="empty-state"><?= e($emptyText) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($pembayaranFiltered as $p): ?>
                            <?php
                            $badgeClass = match ($p['status']) {
                                'Diverifikasi' => 'badge-success',
                                'Menunggu' => 'badge-warning',
                                'Ditolak' => 'badge-danger',
                                default => 'badge-neutral',
                            };
                            ?>
                            <tr>
                                <td class="mono-id"><?= e($p['id'] !== '' ? $p['id'] : $emptyText) ?></td>
                                <td><?= e($p['peserta'] !== '' ? $p['peserta'] : $emptyText) ?></td>
                                <td class="muted"><?= e($p['event'] !== '' ? $p['event'] : $emptyText) ?></td>
                                <td><?= e($p['jumlah']) ?></td>
                                <td>
                                    <?php if ($p['berkas'] !== ''): ?>
                                        <span class="proof-tag" title="<?= e($p['berkas']) ?>">IMG</span>
                                    <?php else: ?>
                                        <span class="muted"><?= e($emptyText) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= e($p['status'] !== '' ? $p['status'] : $emptyText) ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <a href="pembayaran-verifikasi.php?id=<?= (int) $p['id'] ?>" class="actions-detail">Periksa</a>
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

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'pembayaran';

$statusMap = [
    'semua'        => null,
    'menunggu'     => 'Menunggu',
    'diverifikasi' => 'Diverifikasi',
    'ditolak'      => 'Ditolak',
    'belum'        => null,
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

$hitung = [
    'semua'        => count($daftarPembayaran),
    'menunggu'     => 0,
    'diverifikasi' => 0,
    'ditolak'      => 0,
    'belum'        => count($daftarBelumBayar),
];

foreach ($daftarPembayaran as $p) {
    $kunci = match ($p['status']) {
        'Menunggu' => 'menunggu',
        'Diverifikasi' => 'diverifikasi',
        'Ditolak' => 'ditolak',
        default => null,
    };

    if ($kunci !== null) {
        $hitung[$kunci]++;
    }
}

$tampilkanPembayaran = $statusTab !== 'belum';
$tampilkanBelum = $statusTab === 'belum' || ($statusTab === 'semua' && count($daftarBelumBayar) > 0);
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

        <?php if ($hitung['menunggu'] > 0): ?>
            <p class="form-notice">
                Ada <?= (int) $hitung['menunggu'] ?> transaksi menunggu verifikasi.
                <a href="?status=menunggu">Lihat yang belum diverifikasi</a>.
            </p>
        <?php endif; ?>

        <!-- Tab Filter Status -->
        <div class="status-tabs" role="tablist" aria-label="Filter status pembayaran">
            <?php
            $tabLabel = [
                'semua'        => 'Semua',
                'menunggu'     => 'Menunggu',
                'diverifikasi' => 'Diverifikasi',
                'ditolak'      => 'Ditolak',
                'belum'        => 'Tanpa Bukti',
            ];
            ?>
            <?php foreach ($tabLabel as $key => $label): ?>
                <a href="?status=<?= e($key) ?>" class="tab-item<?= $statusTab === $key ? ' is-active' : '' ?>"
                   role="tab"<?= $statusTab === $key ? ' aria-current="page"' : '' ?>>
                    <?= e($label) ?>
                    <span class="tab-count"><?= (int) $hitung[$key] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($tampilkanPembayaran): ?>
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
                                    <td colspan="7" class="empty-state">Belum ada transaksi pada filter ini.</td>
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
                                        <div class="row-actions">
                                            <a href="pembayaran-verifikasi.php?id=<?= (int) $p['id'] ?>" class="actions-detail">Periksa</a>
                                            <?php if ($p['id'] !== ''): ?>
                                                <form action="pembayaran-verifikasi.php" method="post" class="inline-form">
                                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                    <input type="hidden" name="id_pembayaran" value="<?= (int) $p['id'] ?>">
                                                    <button type="submit" name="aksi" value="verifikasi" class="link-btn link-ok">Lunas</button>
                                                    <button type="submit" name="aksi" value="tolak" class="link-btn link-danger">Gagal</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($tampilkanBelum): ?>
            <section class="payment-section">
                <div class="section-title-row">
                    <div>
                        <h2>Tiket tanpa bukti upload</h2>
                        <p>Peserta berbayar yang belum punya baris pembayaran. Buka transaksi untuk menandai Lunas atau Gagal secara manual, termasuk untuk membuat skenario pengujian.</p>
                    </div>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">ID Tiket</th>
                                <th scope="col">Peserta</th>
                                <th scope="col">Event</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($daftarBelumBayar)): ?>
                                <tr>
                                    <td colspan="6" class="empty-state">Semua tiket berbayar sudah upload bukti.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($daftarBelumBayar as $t): ?>
                                <tr>
                                    <td class="mono-id"><?= (int) $t['id_tiket'] ?></td>
                                    <td><?= e($t['peserta'] !== '' ? $t['peserta'] : $emptyText) ?></td>
                                    <td class="muted"><?= e($t['event'] !== '' ? $t['event'] : $emptyText) ?></td>
                                    <td><?= e($t['jumlah']) ?></td>
                                    <td><span class="badge badge-neutral"><?= e($t['status'] !== '' ? $t['status'] : 'Belum Bayar') ?></span></td>
                                    <td class="actions">
                                        <a href="pembayaran-verifikasi.php?tiket=<?= (int) $t['id_tiket'] ?>" class="actions-detail">Periksa / Tandai</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        <?php endif; ?>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
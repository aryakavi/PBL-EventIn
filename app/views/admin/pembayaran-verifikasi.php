<?php
require __DIR__ . '/../../../includes/admin/data.php';
require __DIR__ . '/../../../app/models/PembayaranRepository.php';
require __DIR__ . '/../../../app/models/PembayaranGatewayRepository.php';
require __DIR__ . '/../../../includes/storage.php';

$halamanAktif = 'pembayaran';

$idPembayaran = (int) ($_GET['id'] ?? $_POST['id_pembayaran'] ?? 0);
$idTiket      = (int) ($_GET['tiket'] ?? $_POST['id_tiket'] ?? 0);

$transaksi = [];
if ($idPembayaran > 0 && $adminRepo !== null) {
    $transaksi = $adminRepo->payment($idPembayaran);
} elseif ($idTiket > 0 && $adminRepo !== null) {
    $transaksi = $adminRepo->ticket($idTiket);
}

$formNotice = '';
$formError  = '';

$notices = [
    'verifikasi' => 'Pembayaran berhasil ditandai Lunas.',
    'tolak'      => 'Transaksi ditandai Gagal / Batal.',
    'pending'    => 'Transaksi dikembalikan ke status Menunggu.',
];

$statusParam = (string) ($_GET['status'] ?? '');
if (isset($notices[$statusParam])) {
    $formNotice = $notices[$statusParam];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = (string) ($_POST['aksi'] ?? '');
    $statusMap = [
        'verifikasi' => 'Lunas',
        'tolak'      => 'Batal',
        'pending'    => 'Menunggu',
    ];

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $formError = 'Sesi keamanan kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (!isset($statusMap[$aksi])) {
        $formError = 'Aksi verifikasi tidak dikenal.';
    } elseif (!isset($pdo) || !$pdo instanceof PDO || $adminRepo === null) {
        $formError = 'Koneksi database gagal. Status tidak diubah.';
    } elseif ($idPembayaran <= 0 && $idTiket <= 0) {
        $formError = 'Transaksi tidak ditemukan.';
    } else {
        try {
            $repo = new PembayaranRepository($pdo);

            if ($idPembayaran > 0) {
                $idTiket = $repo->setStatusByPayment($idPembayaran, $statusMap[$aksi]);
            } else {
                $repo->setTicketStatus($idTiket, $statusMap[$aksi]);
            }

            if ($idTiket === null || $idTiket <= 0) {
                $formError = 'Transaksi tidak ditemukan.';
            } else {
                $query = $idPembayaran > 0
                    ? 'id=' . $idPembayaran
                    : 'tiket=' . $idTiket;

                header('Location: pembayaran-verifikasi.php?' . $query . '&status=' . $aksi);
                exit;
            }
        } catch (Throwable $e) {
            error_log('EventIn verifikasi pembayaran error: ' . $e->getMessage());
            $formError = 'Status gagal diubah. Coba lagi.';
        }
    }
}

$tampil = static function ($nilai) use ($emptyText): string {
    $teks = trim((string) $nilai);

    return $teks === '' ? $emptyText : $teks;
};

$tanggal = static function (?string $nilai, string $format = 'd M Y, H:i'): string {
    if ($nilai === null || $nilai === '') {
        return '';
    }

    $time = strtotime($nilai);

    return $time === false ? '' : date($format, $time);
};

$statusBayar = (string) ($transaksi['status_bayar'] ?? '');
$statusUi    = match (strtolower($statusBayar)) {
    'lunas' => 'Diverifikasi',
    'menunggu' => 'Menunggu',
    'ditolak', 'gagal', 'batal' => 'Ditolak',
    'belum bayar' => 'Belum Bayar',
    default => '',
};
$badgeStatus = match ($statusUi) {
    'Diverifikasi' => 'badge-success',
    'Menunggu' => 'badge-warning',
    'Ditolak' => 'badge-danger',
    default => 'badge-neutral',
};

$buktiPath = (string) ($transaksi['bukti_pembayaran'] ?? '');
$buktiUrl  = $buktiPath !== '' ? storage_public_url($buktiPath) : '';
$buktiExt  = $buktiUrl !== ''
    ? strtolower(pathinfo((string) (parse_url($buktiUrl, PHP_URL_PATH) ?: ''), PATHINFO_EXTENSION))
    : '';
$buktiGambar = in_array($buktiExt, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

$punyaTransaksi = $transaksi !== [];

/* --- Transaksi gateway (Midtrans), kalau ada --- */
$gatewayInfo = [];
if ($adminRepo !== null && isset($pdo) && $pdo instanceof PDO) {
    $idTiketGateway = (int) ($transaksi['id_tiket'] ?? $idTiket);

    if ($idTiketGateway > 0) {
        $gatewayRow = (new PembayaranGatewayRepository($pdo))->latestForTicket($idTiketGateway);

        if ($gatewayRow !== false) {
            $raw = $gatewayRow['raw_response'] ?? null;
            $raw = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
            $raw = is_array($raw) ? $raw : [];

            $gatewayInfo = [
                'order_id'  => (string) ($gatewayRow['order_id'] ?? ''),
                'status'    => (string) ($gatewayRow['transaction_status'] ?? ''),
                'tipe'      => (string) ($raw['payment_type'] ?? ''),
                'redirect'  => (string) ($raw['redirect_url'] ?? ''),
                'waktu'     => $tanggal($gatewayRow['created_at'] ?? null),
            ];
        }
    }
}
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
        <div class="page-head">
            <a class="back-link" href="pembayaran.php">&larr; Kembali ke Verifikasi Pembayaran</a>
            <h1>Periksa Transaksi</h1>
            <p>Periksa bukti transfer dan detail peserta sebelum memverifikasi.</p>
        </div>

        <section class="form-card">
            <div class="form-card-header detail-title-row">
                <div>
                    <h2>Detail Transaksi</h2>
                    <p>
                        <?php if ($idPembayaran > 0): ?>
                            <?= e('ID Pembayaran: ' . $idPembayaran) ?>
                        <?php elseif ($idTiket > 0): ?>
                            <?= e('ID Tiket: ' . $idTiket . ' (belum upload bukti)') ?>
                        <?php else: ?>
                            <?= e($emptyText) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <span class="badge <?= $badgeStatus ?>"><?= e($statusUi !== '' ? $statusUi : $emptyText) ?></span>
            </div>

            <?php if ($formNotice !== ''): ?>
                <p class="form-notice"><?= e($formNotice) ?></p>
            <?php endif; ?>

            <?php if ($formError !== ''): ?>
                <p class="form-notice form-notice--error"><?= e($formError) ?></p>
            <?php endif; ?>

            <?php if (!$punyaTransaksi): ?>
                <p class="form-notice form-notice--error">Transaksi tidak ditemukan.</p>
            <?php else: ?>
                <div class="detail-body">
                    <!-- Kolom Bukti Transfer -->
                    <div class="proof-col">
                        <span class="proof-title">Lampiran Bukti Transfer</span>
                        <div class="proof-preview">
                            <?php if ($buktiGambar): ?>
                                <a href="<?= e($buktiUrl) ?>" target="_blank" rel="noopener">
                                    <img class="proof-image" src="<?= e($buktiUrl) ?>" alt="Bukti transfer peserta">
                                </a>
                            <?php elseif ($buktiUrl !== ''): ?>
                                <span class="proof-icon">&#128196;</span>
                                <a class="proof-link" href="<?= e($buktiUrl) ?>" target="_blank" rel="noopener">Buka lampiran</a>
                            <?php else: ?>
                                <span class="proof-icon">&#128196;</span>
                                <span>Belum ada bukti upload</span>
                            <?php endif; ?>
                        </div>
                        <span class="proof-filename"><?= e($tampil($buktiPath)) ?></span>
                        <span class="proof-title">METODE</span>
                        <span class="proof-value"><?= e($tampil($transaksi['metode'] ?? '')) ?></span>
                    </div>

                    <!-- Kolom Informasi Transaksi -->
                    <div class="info-col">
                        <div class="info-item">
                            <span class="info-label">NAMA PESERTA</span>
                            <strong class="info-value"><?= e($tampil($transaksi['peserta'] ?? '')) ?></strong>
                        </div>
                        <div class="info-item">
                            <span class="info-label">EMAIL</span>
                            <span class="info-text"><?= e($tampil($transaksi['email'] ?? '')) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIM / NIP</span>
                            <span class="info-text"><?= e($tampil($transaksi['nim'] ?? '')) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">EVENT DIIKUTI</span>
                            <strong class="info-value"><?= e($tampil($transaksi['nama_event'] ?? '')) ?></strong>
                        </div>
                        <div class="info-item">
                            <span class="info-label">KATEGORI</span>
                            <span class="info-text"><?= e($tampil($transaksi['nama_kategori'] ?? '')) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">PENYELENGGARA</span>
                            <span class="info-text"><?= e($tampil($transaksi['nama_ukm'] ?? '')) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NOMINAL</span>
                            <strong class="info-value"><?= e(format_rupiah($transaksi['nominal'] ?? 0)) ?></strong>
                        </div>
                        <div class="info-item">
                            <span class="info-label">KODE QR TIKET</span>
                            <span class="info-text mono-id"><?= e($tampil($transaksi['kode_qr'] ?? '')) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">WAKTU UNGGAH BUKTI</span>
                            <span class="info-text"><?= e($tampil($tanggal($transaksi['waktu_unggah'] ?? null) . (!empty($transaksi['waktu_unggah']) ? ' WIB' : ''))) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">TANGGAL DAFTAR</span>
                            <span class="info-text"><?= e($tampil($tanggal($transaksi['tanggal_daftar'] ?? null))) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">CHECK-IN</span>
                            <span class="info-text"><?= e($tampil($tanggal($transaksi['waktu_checkin'] ?? null))) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">SERTIFIKAT</span>
                            <span class="info-text"><?= e($tampil($transaksi['status_sertifikat'] ?? '')) ?></span>
                        </div>

                        <?php if ($gatewayInfo !== []): ?>
                            <div class="info-item">
                                <span class="info-label">TRANSAKSI MIDTRANS</span>
                                <span class="info-text mono-id"><?= e($tampil($gatewayInfo['order_id'])) ?></span>
                                <span class="info-text">
                                    Status gateway: <strong><?= e($tampil($gatewayInfo['status'])) ?></strong>
                                    <?php if ($gatewayInfo['tipe'] !== ''): ?>
                                        &middot; <?= e($gatewayInfo['tipe']) ?>
                                    <?php endif; ?>
                                    <?php if ($gatewayInfo['waktu'] !== ''): ?>
                                        &middot; <?= e($gatewayInfo['waktu']) ?>
                                    <?php endif; ?>
                                </span>
                                <?php if ($gatewayInfo['redirect'] !== ''): ?>
                                    <a class="proof-link" href="<?= e($gatewayInfo['redirect']) ?>" target="_blank" rel="noopener">
                                        Buka halaman Midtrans
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post" class="detail-actions">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <?php if ($idPembayaran > 0): ?>
                                <input type="hidden" name="id_pembayaran" value="<?= (int) $idPembayaran ?>">
                            <?php else: ?>
                                <input type="hidden" name="id_tiket" value="<?= (int) $idTiket ?>">
                            <?php endif; ?>

                            <button type="submit" name="aksi" value="tolak" class="btn btn-outline">Tolak / Gagal</button>
                            <button type="submit" name="aksi" value="pending" class="btn btn-outline">Kembalikan ke Menunggu</button>
                            <button type="submit" name="aksi" value="verifikasi" class="btn btn-primary">Verifikasi / Lunas</button>
                        </form>
                        <p class="verify-hint">
                            Admin bisa mengubah status kapan saja. Gunakan tombol di atas untuk membuat skenario
                            Menunggu, Gagal, atau Lunas.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
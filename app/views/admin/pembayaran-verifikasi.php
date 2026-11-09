<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'pembayaran';

$idPembayaran = (int) ($_GET['id'] ?? 0);
$transaksi    = $adminRepo !== null ? $adminRepo->payment($idPembayaran) : [];

$formNotice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formNotice = 'Mode baca-saja: status pembayaran tidak diubah karena koneksi database hanya untuk membaca data.';
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
    default => '',
};
$badgeStatus = match ($statusUi) {
    'Diverifikasi' => 'badge-success',
    'Menunggu' => 'badge-warning',
    'Ditolak' => 'badge-danger',
    default => 'badge-neutral',
};
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
                    <p><?= e($idPembayaran > 0 ? 'ID Pembayaran: ' . $idPembayaran : $emptyText) ?></p>
                </div>
                <span class="badge <?= $badgeStatus ?>"><?= e($statusUi !== '' ? $statusUi : $emptyText) ?></span>
            </div>

            <?php if ($formNotice !== ''): ?>
                <p class="form-notice"><?= e($formNotice) ?></p>
            <?php endif; ?>

            <div class="detail-body">
                <!-- Kolom Bukti Transfer -->
                <div class="proof-col">
                    <span class="proof-title">Lampiran Bukti Transfer</span>
                    <div class="proof-preview">
                        <span class="proof-icon">&#128196;</span>
                        <span>Lampiran Bukti Transfer</span>
                    </div>
                    <span class="proof-filename"><?= e($tampil($transaksi['bukti_pembayaran'] ?? '')) ?></span>
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
                        <span class="info-text"><?= e($tampil($tanggal($transaksi['waktu_unggah'] ?? null) . ($transaksi['waktu_unggah'] ? ' WIB' : ''))) ?></span>
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

                    <form action="" method="post" class="detail-actions">
                        <button type="submit" name="aksi" value="tolak" class="btn btn-outline">Tolak Transaksi</button>
                        <button type="submit" name="aksi" value="verifikasi" class="btn btn-primary">Verifikasi Pembayaran</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
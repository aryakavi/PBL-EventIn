<?php
$activeNav = 'tiket';
$base = '../../';

require __DIR__ . '/../../includes/data.php';
require __DIR__ . '/../../includes/koneksi.php';
require __DIR__ . '/../../app/models/SiteRepository.php';

$emptyText = 'kosong, masih kurang';

$sesi = $_SESSION['user'] ?? null;
if (!is_array($sesi) || empty($sesi['id_pengguna'])) {
    header('Location: ' . $base . $routes['login']);
    exit;
}

if (is_admin_role($sesi['role'] ?? '')) {
    header('Location: ' . $base . $routes['admin-dashboard']);
    exit;
}

$siteRepo = isset($pdo) && $pdo instanceof PDO ? new SiteRepository($pdo) : null;
$idPembayaran = (int) ($_GET['id'] ?? 0);
$bayar = $siteRepo !== null
    ? $siteRepo->pembayaranPeserta((int) $sesi['id_pengguna'], $idPembayaran)
    : [];

$statusBayar = strtolower((string) ($bayar['status_bayar'] ?? ''));
if (str_starts_with($statusBayar, 'lunas')) {
    $status = 'sukses';
} elseif (str_starts_with($statusBayar, 'menunggu')) {
    $status = 'menunggu';
} else {
    $status = 'gagal';
}

$statusAktif = $konfigurasiStatus[$status];

$emailPeserta = (string) ($bayar['email'] ?? $sesi['email'] ?? '');
$emailHtml = '<strong>' . e($emailPeserta !== '' ? $emailPeserta : $emptyText) . '</strong>';
$statusAktif['deskripsi'] = str_replace('{email}', $emailHtml, $statusAktif['deskripsi']);

$tanggalDaftar = !empty($bayar['tanggal_daftar']) ? strtotime((string) $bayar['tanggal_daftar']) : false;
$tahunInvoice = $tanggalDaftar !== false ? date('Y', $tanggalDaftar) : date('Y');

$mulai = !empty($bayar['tanggal_mulai']) ? strtotime((string) $bayar['tanggal_mulai']) : false;
$selesai = !empty($bayar['tanggal_selesai']) ? strtotime((string) $bayar['tanggal_selesai']) : false;
$waktuEvent = $mulai !== false
    ? date('d M Y', $mulai) . ' | ' . date('H:i', $mulai) . ' - ' . ($selesai !== false ? date('H:i', $selesai) : '-') . ' WIB'
    : $emptyText;
$waktuUnggah = !empty($bayar['waktu_unggah'])
    ? date('d M Y, H:i', strtotime((string) $bayar['waktu_unggah'])) . ' WIB'
    : $emptyText;

$transaksi = [
    'nomor'   => !empty($bayar['id_tiket'])
        ? '#INV-' . $tahunInvoice . '-EVIN-' . str_pad((string) (int) $bayar['id_tiket'], 6, '0', STR_PAD_LEFT)
        : $emptyText,
    'event'   => ($bayar['nama_event'] ?? '') !== '' ? $bayar['nama_event'] : $emptyText,
    'tiket'   => '1x Tiket ' . (($bayar['nama_kategori'] ?? '') !== '' ? $bayar['nama_kategori'] : $emptyText),
    'waktu'   => $waktuEvent,
    'tempat'  => ($bayar['lokasi'] ?? '') !== '' ? $bayar['lokasi'] : $emptyText,
    'metode'  => ($bayar['metode'] ?? '') !== '' ? $bayar['metode'] : $emptyText,
    'tanggal' => $waktuUnggah,
    'total'   => format_rupiah($bayar['nominal'] ?? 0),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Konfirmasi Status Pembayaran';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<!-- Navbar Peserta -->
<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<main class="confirm-container">
    <!-- Stepper -->
    <div class="stepper">
        <div class="step-item is-done">
            <span class="step-circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="m5 12 4 4 10-10"></path></svg></span>
            <span class="step-label">Pembayaran</span>
        </div>
        <div class="pay-step-line"></div>
        <div class="step-item is-active">
            <span class="step-circle">2</span>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <!-- Kartu Utama Status Konfirmasi -->
    <section class="confirm-card theme-<?= e($statusAktif['tema']) ?>">
        <div class="confirm-icon-ring">
            <span class="confirm-icon-inner">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $ikonStatus[$statusAktif['ikon']] ?></svg>
            </span>
        </div>

        <span class="confirm-pill"><?= e($statusAktif['pill']) ?></span>
        <h1 class="confirm-title"><?= e($statusAktif['judul']) ?></h1>
        <p class="confirm-desc"><?= $statusAktif['deskripsi'] ?></p>

        <!-- Kotak Rincian Invoice -->
        <div class="invoice-box">
            <div class="invoice-top">
                <div class="invoice-meta">
                    <span class="invoice-label">NOMOR TRANSAKSI</span>
                    <strong class="invoice-number"><?= e($transaksi['nomor']) ?></strong>
                </div>
                <span class="status-pill pill-<?= e($statusAktif['tema']) ?>">
                    <span class="status-dot"></span>
                    <?= e($statusAktif['badge_label']) ?>
                </span>
            </div>

            <div class="invoice-section">
                <span class="invoice-label">NAMA EVENT &amp; SESI</span>
                <strong class="invoice-event"><?= e($transaksi['event']) ?></strong>
                <span class="invoice-sub"><?= e($transaksi['tiket']) ?></span>
            </div>

            <div class="invoice-grid">
                <div class="invoice-col">
                    <span class="invoice-label">WAKTU &amp; TEMPAT</span>
                    <strong class="invoice-val"><?= e($transaksi['waktu']) ?></strong>
                    <span class="invoice-sub"><?= e($transaksi['tempat']) ?></span>
                </div>
                <div class="invoice-col">
                    <span class="invoice-label">METODE PEMBAYARAN</span>
                    <strong class="invoice-val"><?= e($transaksi['metode']) ?></strong>
                    <span class="invoice-sub"><?= e($transaksi['tanggal']) ?></span>
                </div>
            </div>

            <div class="invoice-total-row">
                <span class="invoice-total-label">Total Pembayaran</span>
                <strong class="invoice-total-val total-<?= e($statusAktif['tema']) ?>">
                    <?= e($transaksi['total']) ?>
                </strong>
            </div>
        </div>

        <!-- Tombol Aksi -->
        <div class="confirm-actions">
            <a href="<?= e($statusAktif['btn_utama']['href']) ?>" class="btn <?= e($statusAktif['btn_utama']['class']) ?> confirm-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $ikonTombol[$statusAktif['btn_utama']['ikon']] ?></svg>
                <?= $statusAktif['btn_utama']['teks'] ?>
            </a>
            <a href="<?= e($statusAktif['btn_kedua']['href']) ?>" class="btn btn-outline confirm-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $ikonTombol[$statusAktif['btn_kedua']['ikon']] ?></svg>
                <?= $statusAktif['btn_kedua']['teks'] ?>
            </a>
        </div>

        <a href="<?= e($base . $routes['home']) ?>" class="back-home-link">&larr; Kembali ke Beranda Jelajah Event</a>
    </section>

    <!-- Badge Jaminan Keamanan Bawah -->
    <div class="trust-badges">
        <div class="trust-pill">
            <span class="trust-icon trust-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $ikonTrust['shield'] ?></svg></span>
            <span>100% Terverifikasi Otomatis</span>
        </div>
        <div class="trust-pill">
            <span class="trust-icon trust-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $ikonTrust['lock'] ?></svg></span>
            <span>Enkripsi 256-Bit SSL Kampus</span>
        </div>
        <div class="trust-pill">
            <span class="trust-icon trust-orange"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><?= $ikonTrust['headset'] ?></svg></span>
            <span>Dukungan Mahasiswa 24/7</span>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>
</html>

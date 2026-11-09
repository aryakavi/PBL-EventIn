<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'event';

$idEvent = (int) ($_GET['id'] ?? 0);
$detail  = $adminRepo !== null ? $adminRepo->event($idEvent) : [];

$formNotice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formNotice = 'Mode baca-saja: event tidak dihapus karena koneksi database hanya untuk membaca data.';
}

$tampil = static function ($nilai) use ($emptyText): string {
    $teks = trim((string) $nilai);

    return $teks === '' ? $emptyText : $teks;
};

$tanggal = static function (?string $nilai): string {
    if ($nilai === null || $nilai === '') {
        return '';
    }

    $time = strtotime($nilai);

    return $time === false ? '' : date('d M Y, H:i', $time);
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Hapus Event';
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
            <a class="back-link" href="event.php">&larr; Kembali ke Kelola Event</a>
            <h1>Hapus Event</h1>
            <p>Periksa kembali data event sebelum menghapusnya.</p>
        </div>

        <section class="form-card">
            <div class="form-card-header">
                <h2>Konfirmasi Hapus</h2>
                <p>Event yang dihapus tidak bisa dikembalikan.</p>
            </div>

            <?php if ($formNotice !== ''): ?>
                <p class="form-notice"><?= e($formNotice) ?></p>
            <?php endif; ?>

            <div class="danger-box">
                <strong><?= e($tampil($detail['nama_event'] ?? '')) ?></strong>
                <span><?= e($tampil($detail['nama_kategori'] ?? '')) ?> &middot; <?= e($tampil($tanggal($detail['tanggal_mulai'] ?? null))) ?></span>
                <span><?= e($tampil($detail['lokasi'] ?? '')) ?> &middot; <?= (int) ($detail['jumlah_peserta'] ?? 0) ?> peserta terdaftar</span>
            </div>

            <form action="" method="post" class="form-actions">
                <a class="btn btn-outline" href="event.php">Batal</a>
                <button type="submit" class="btn btn-danger">Hapus Event</button>
            </form>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
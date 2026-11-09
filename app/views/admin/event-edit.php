<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'event';

$idEvent = (int) ($_GET['id'] ?? 0);
$detail  = $adminRepo !== null ? $adminRepo->event($idEvent) : [];

$formNotice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formNotice = 'Mode baca-saja: perubahan tidak disimpan karena koneksi database hanya untuk membaca data.';
}

$tampil = static function ($nilai) use ($emptyText): string {
    $teks = trim((string) $nilai);

    return $teks === '' ? $emptyText : $teks;
};

$tanggalInput = static function (?string $nilai): string {
    if ($nilai === null || $nilai === '') {
        return '';
    }

    $time = strtotime($nilai);

    return $time === false ? '' : date('Y-m-d', $time);
};

$nilai = static function (string $kunci) use ($detail): string {
    return (string) ($_POST[$kunci] ?? $detail[$kunci] ?? '');
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Edit Event';
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
            <h1>Edit Event</h1>
            <p>Perbarui informasi event lalu simpan perubahan.</p>
        </div>

        <section class="form-card">
            <div class="form-card-header">
                <h2><?= e($tampil($detail['nama_event'] ?? '')) ?></h2>
                <p>ID Event: <?= $idEvent > 0 ? (int) $idEvent : e($emptyText) ?></p>
            </div>

            <?php if ($formNotice !== ''): ?>
                <p class="form-notice"><?= e($formNotice) ?></p>
            <?php endif; ?>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="nama_event">Nama Event</label>
                            <input type="text" id="nama_event" name="nama_event" class="form-control"
                                   value="<?= e($nilai('nama_event')) ?>" placeholder="Masukkan nama event resmi.">
                        </div>

                        <div class="form-group">
                            <label for="kategori_event">Kategori Event</label>
                            <select id="kategori_event" name="id_kategori" class="form-control form-control-sm">
                                <option value="" disabled>Pilih kategori..</option>
                                <?php foreach ($kategoriEvent as $k): ?>
                                    <option value="<?= (int) $k['id'] ?>"
                                        <?= (int) $nilai('id_kategori') === (int) $k['id'] ? ' selected' : '' ?>>
                                        <?= e($k['nama']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (empty($kategoriEvent)): ?>
                                <span class="field-hint"><?= e($emptyText) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-col">
                        <div class="form-group">
                            <span class="form-label">Banner Event</span>
                            <label for="banner" class="upload-box">
                                <span class="upload-icon">&uarr;</span>
                                <span><?= e($tampil($detail['poster'] ?? '')) ?></span>
                                <input type="file" id="banner" name="banner" accept="image/*" hidden>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tgl_mulai">Tanggal Mulai</label>
                        <input type="datetime-local" id="tgl_mulai" name="tanggal_mulai" class="form-control form-control-md"
                               value="<?= e($nilai('tanggal_mulai') !== '' ? date('Y-m-d\TH:i', strtotime($nilai('tanggal_mulai'))) : '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="tgl_selesai">Tanggal Selesai</label>
                        <input type="datetime-local" id="tgl_selesai" name="tanggal_selesai" class="form-control form-control-md"
                               value="<?= e($nilai('tanggal_selesai') !== '' ? date('Y-m-d\TH:i', strtotime($nilai('tanggal_selesai'))) : '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="kapasitas">Kapasitas Kursi</label>
                        <input type="number" id="kapasitas" name="kuota" class="form-control form-control-sm"
                               value="<?= e($nilai('kuota')) ?>" placeholder="Contoh: 150">
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga Tiket (Rp)</label>
                        <input type="number" id="harga" name="harga" class="form-control form-control-sm"
                               value="<?= e($nilai('harga')) ?>" placeholder="Contoh: 75000">
                    </div>

                    <div class="form-group form-group--full">
                        <label for="lokasi">Lokasi Event</label>
                        <input type="text" id="lokasi" name="lokasi" class="form-control"
                               value="<?= e($nilai('lokasi')) ?>" placeholder="Contoh: Aula Gedung C">
                    </div>

                    <div class="form-group form-group--full">
                        <label for="deskripsi">Deskripsi Event</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4"><?= e($nilai('deskripsi')) ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <a class="btn btn-outline" href="event.php">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>
<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'event';

$q        = trim($_GET['q'] ?? '');
$kategori = $_GET['kategori'] ?? '';
$status   = $_GET['status'] ?? '';

$filtered = array_values(array_filter($daftarEvent, function ($ev) use ($q, $kategori, $status) {
    if ($q !== '' && stripos($ev['nama'], $q) === false && stripos($ev['lokasi'], $q) === false) {
        return false;
    }
    if ($kategori !== '' && $ev['kategori'] !== $kategori) {
        return false;
    }
    if ($status !== '' && $ev['status'] !== $status) {
        return false;
    }
    return true;
}));

$perPage  = 5;
$total    = count($filtered);
$lastPage = max(1, (int) ceil($total / $perPage));
$page     = min($lastPage, max(1, (int) ($_GET['page'] ?? 1)));
$hasil    = array_slice($filtered, ($page - 1) * $perPage, $perPage);

$queryParams = array_filter([
    'q'        => $q,
    'kategori' => $kategori,
    'status'   => $status,
], static fn($v) => $v !== '' && $v !== null);

$pageUrl = static function (int $p) use ($queryParams): string {
    $params = $queryParams;
    $params['page'] = $p;
    return '?' . http_build_query($params);
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Event | EventIn</title>
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
                <h1>Kelola Event</h1>
                <p>Atur publikasi, pantau kuota, saring pendaftaran, dan cetak sertifikat kegiatan mahasiswa.</p>
            </div>
            <a href="#form-event" class="btn btn-primary">Buat Event Baru</a>
        </div>

        <!-- Bar Pencarian & Filter -->
        <form class="filter-bar" method="get" action="">
            <input type="text" name="q" class="filter-search" placeholder="Cari event Anda.." value="<?= e($q) ?>">
            <select name="kategori" class="filter-select">
                <option value="">Kategori</option>
                <option value="Seminar"<?= $kategori === 'Seminar' ? ' selected' : '' ?>>Seminar</option>
                <option value="Workshop"<?= $kategori === 'Workshop' ? ' selected' : '' ?>>Workshop</option>
                <option value="Webinar"<?= $kategori === 'Webinar' ? ' selected' : '' ?>>Webinar</option>
                <option value="Kompetisi"<?= $kategori === 'Kompetisi' ? ' selected' : '' ?>>Kompetisi</option>
            </select>
            <select name="status" class="filter-select">
                <option value="">Status</option>
                <option value="Aktif"<?= $status === 'Aktif' ? ' selected' : '' ?>>Aktif</option>
                <option value="Draft"<?= $status === 'Draft' ? ' selected' : '' ?>>Draft</option>
                <option value="Selesai"<?= $status === 'Selesai' ? ' selected' : '' ?>>Selesai</option>
            </select>
            <button type="submit" class="btn btn-outline">Saring</button>
        </form>

        <!-- Tabel Data Event -->
        <section class="event-section">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Nama Event</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Lokasi</th>
                            <th scope="col">Peserta</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($hasil)): ?>
                            <tr>
                                <td colspan="6" class="muted" style="text-align:center">Tidak ada event yang cocok dengan filter.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($hasil as $ev): ?>
                            <?php
                            $badgeClass = $ev['status'] === 'Aktif' ? 'badge-info' : 'badge-neutral';
                            ?>
                            <tr>
                                <td><strong><?= e($ev['nama']) ?></strong></td>
                                <td class="muted"><?= e($ev['tanggal']) ?></td>
                                <td class="muted"><?= e($ev['lokasi']) ?></td>
                                <td><strong><?= e($ev['peserta']) ?></strong></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= e($ev['status']) ?></span></td>
                                <td class="actions-icon">
                                    <a href="#" class="icon-btn" title="Edit">&#9998;</a>
                                    <a href="#" class="icon-btn" title="Lihat">&#128065;</a>
                                    <a href="#" class="icon-btn" title="Hapus">&times;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($lastPage > 1): ?>
                <nav class="pagination" aria-label="Navigasi halaman">
                    <?php if ($page > 1): ?>
                        <a href="<?= e($pageUrl($page - 1)) ?>" class="page-item" rel="prev" aria-label="Sebelumnya">&lt;</a>
                    <?php else: ?>
                        <span class="page-item" aria-disabled="true">&lt;</span>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $lastPage; $i++): ?>
                        <a href="<?= e($pageUrl($i)) ?>"
                           class="page-item<?= $i === $page ? ' is-active' : '' ?>"
                           <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $lastPage): ?>
                        <a href="<?= e($pageUrl($page + 1)) ?>" class="page-item" rel="next" aria-label="Berikutnya">&gt;</a>
                    <?php else: ?>
                        <span class="page-item" aria-disabled="true">&gt;</span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </section>

        <!-- Card Pratinjau Formulir Buat Event Baru -->
        <section class="form-card" id="form-event">
            <div class="form-card-header">
                <h2>Pratinjau Formulir Buat Event Baru</h2>
                <p>Contoh layout visual pembuatan pendaftaran event kemahasiswaan.</p>
            </div>

            <form action="" method="post" enctype="multipart/form-data">
                <div class="form-grid">
                    <!-- Kolom Kiri -->
                    <div class="form-col">
                        <div class="form-group">
                            <label for="nama_event">Nama Event </label>
                            <input type="text" id="nama_event" name="nama_event" class="form-control" placeholder="Masukkan nama event resmi.">
                        </div>

                        <div class="form-group">
                            <label for="kategori_event">Kategori Event</label>
                            <select id="kategori_event" name="kategori" class="form-control form-control-sm">
                                <option value="" selected disabled>Pilih kategori..</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Webinar">Webinar</option>
                                <option value="Kompetisi">Kompetisi</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kolom Kanan (Banner) -->
                    <div class="form-col">
                        <div class="form-group">
                            <span class="form-label">Banner Event *</span>
                            <label for="banner" class="upload-box">
                                <span class="upload-icon">&uarr;</span>
                                <span>Pilih berkas gambar banner event..</span>
                                <input type="file" id="banner" name="banner" accept="image/*" hidden>
                            </label>
                        </div>
                    </div>

                    <!-- Baris Tanggal -->
                    <div class="form-group">
                        <label for="tgl_mulai">Tanggal Mulai</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" class="form-control form-control-md">
                    </div>

                    <div class="form-group">
                        <label for="tgl_selesai">Tanggal Selesai</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" class="form-control form-control-md">
                    </div>

                    <!-- Baris Kapasitas & Harga -->
                    <div class="form-group">
                        <label for="kapasitas">Kapasitas Kursi</label>
                        <input type="number" id="kapasitas" name="kapasitas" class="form-control form-control-sm" placeholder="Contoh: 150">
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga Tiket (Rp)</label>
                        <input type="text" id="harga" name="harga" class="form-control form-control-sm" placeholder="Contoh: 75.000">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="reset" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Event</button>
                </div>
            </form>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js"></script>
</body>
</html>
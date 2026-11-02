<?php
/**
 * Halaman "Tiket Saya" untuk peserta.
 *
 * Variabel dari index.php:
 * - $tiketController : TiketController
 * - $daftarTiket     : array tiket sesuai tab & filter
 * - $jumlah          : array jumlah tiket per tab
 * - $daftarKategori  : array nama kategori
 * - $tab             : tab aktif (aktif|tidak-tersedia|dibatalkan)
 * - $keyword         : kata kunci pencarian
 * - $kategori        : kategori terpilih
 * - $pengguna        : data pengguna dari session
 */
$activeNav = 'tiket';
$namaPengguna = $pengguna['nama'] ?? 'Peserta';

$tabs = [
    'aktif' => 'Aktif',
    'tidak-tersedia' => 'Tidak Tersedia',
    'dibatalkan' => 'Dibatalkan',
];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiket Saya - EventIn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Geist+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/tiket.css">
</head>

<body class="page-tiket">

    <?php require __DIR__ . '/../../../includes/navbar.php'; ?>

    <main class="tiket-page">
        <div class="tiket-container">

            <nav class="tiket-breadcrumb" aria-label="Breadcrumb">
                <a href="<?= e($routes['home']) ?>">Beranda</a>
                <span aria-hidden="true">/</span>
                <span>Tiket Saya</span>
            </nav>

            <header class="tiket-head">
                <div class="tiket-head-text">
                    <span class="tiket-eyebrow">// PORTAL TIKET MAHASISWA PESERTA</span>
                    <h1>Tiket Elektronik Saya</h1>
                    <p>Kelola tiket event Anda, unduh E-Tiket, dan lakukan check-in di lokasi acara.</p>
                </div>
                <a class="btn btn-outline tiket-sync" href="?page=tiket&amp;tab=<?= e($tab) ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M21 12a9 9 0 1 1-3-6.7"></path>
                        <path d="M21 4v5h-5"></path>
                    </svg>
                    Sinkronisasi Tiket
                </a>
            </header>

            <section class="tiket-toolbar" aria-label="Filter tiket">
                <div class="tiket-tabs" role="tablist">
                    <?php foreach ($tabs as $slug => $label): ?>
                        <a class="tiket-tab<?= $slug === $tab ? ' is-active' : '' ?>"
                            role="tab"
                            aria-selected="<?= $slug === $tab ? 'true' : 'false' ?>"
                            href="<?= e($tiketController->tabUrl($slug, $keyword, $kategori)) ?>">
                            <?= e($label) ?> (<?= (int) ($jumlah[$slug] ?? 0) ?>)
                        </a>
                    <?php endforeach; ?>
                </div>

                <form class="tiket-filter" method="get" action="">
                    <input type="hidden" name="page" value="tiket">
                    <input type="hidden" name="tab" value="<?= e($tab) ?>">

                    <label class="tiket-search">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                        <input type="search" name="q" value="<?= e($keyword) ?>"
                            placeholder="Cari nama event..." aria-label="Cari nama event">
                    </label>

                    <label class="tiket-select">
                        <select name="kategori" aria-label="Filter kategori" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($daftarKategori as $namaKategori): ?>
                                <option value="<?= e($namaKategori) ?>"<?= $namaKategori === $kategori ? ' selected' : '' ?>>
                                    <?= e($namaKategori) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <button type="submit" class="btn btn-primary tiket-filter-submit">Cari</button>
                </form>
            </section>

            <section class="tiket-list" aria-label="Daftar tiket">
                <?php if (empty($daftarTiket)): ?>
                    <div class="tiket-empty">
                        <strong>Tidak ada tiket pada tab ini.</strong>
                        <p>Ubah kata kunci, kategori, atau pilih tab lain untuk melihat tiket Anda.</p>
                    </div>
                <?php endif; ?>

                <?php foreach ($daftarTiket as $index => $tiket):
                    $poster = $tiketController->posterUrl($tiket);
                    $statusBayar = $tiketController->labelStatusBayar((string) $tiket['status_bayar']);
                    $kelasStatus = $tiketController->kelasStatusBayar((string) $tiket['status_bayar']);
                    $sudahCheckIn = !empty($tiket['check_in_at']);
                    ?>
                    <details class="tiket-item"<?= $index === 0 ? ' open' : '' ?>>
                        <summary class="tiket-summary">
                            <div class="tiket-thumb<?= $poster ? '' : ' tiket-thumb--fallback' ?>"
                                <?php if ($poster): ?>style="background-image: url('<?= e($poster) ?>');"<?php endif; ?>>
                                <?php if (!$poster): ?>
                                    <span><?= e($tiket['singkatan'] ?? 'EventIn') ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="tiket-summary-body">
                                <span class="tiket-cat"><?= e(strtoupper($tiket['nama_kategori'])) ?></span>
                                <h2><?= e($tiket['nama_event']) ?></h2>
                                <p class="tiket-meta">
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                                            <path d="M8 3v4M16 3v4M3 11h18"></path>
                                        </svg>
                                        <?= e($tiketController->formatTanggalSingkat($tiket)) ?>
                                    </span>
                                    <span>
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z"></path>
                                            <circle cx="12" cy="10" r="2.5"></circle>
                                        </svg>
                                        <?= e($tiket['lokasi']) ?>
                                    </span>
                                </p>
                            </div>

                            <span class="tiket-toggle">
                                <span class="tiket-toggle-open">Lihat Detail</span>
                                <span class="tiket-toggle-close">Tutup Detail</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </span>
                        </summary>

                        <div class="tiket-detail">
                            <div class="tiket-qr">
                                <div class="tiket-qr-frame">
                                    <div class="tiket-qr-canvas"
                                        data-qr="<?= e($tiket['kode_qr']) ?>"
                                        role="img"
                                        aria-label="QR code tiket <?= e($tiket['kode_qr']) ?>"></div>
                                </div>
                                <strong>Scan saat check-in</strong>
                                <small>Verifikasi tiket pada pintu masuk acara</small>
                            </div>

                            <div class="tiket-info">
                                <dl class="tiket-info-grid">
                                    <div class="tiket-info-row">
                                        <dt>ID TIKET</dt>
                                        <dd class="mono"><?= e($tiket['kode_qr']) ?></dd>
                                    </div>
                                    <div class="tiket-info-row">
                                        <dt>NAMA PESERTA</dt>
                                        <dd><?= e($namaPengguna) ?></dd>
                                    </div>
                                    <div class="tiket-info-row">
                                        <dt>METODE PEMBAYARAN</dt>
                                        <dd><?= e($tiket['metode'] ?? 'Belum Ada') ?></dd>
                                    </div>
                                    <div class="tiket-info-row">
                                        <dt>WAKTU</dt>
                                        <dd><?= e($tiketController->formatWaktu($tiket)) ?></dd>
                                    </div>
                                    <div class="tiket-info-row">
                                        <dt>STATUS PEMBAYARAN</dt>
                                        <dd>
                                            <span class="tiket-status <?= e($kelasStatus) ?>"><?= e($statusBayar) ?></span>
                                            <span class="tiket-checkin <?= $sudahCheckIn ? 'is-in' : '' ?>">
                                                <?= $sudahCheckIn ? 'Sudah check-in' : 'Belum check-in' ?>
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="tiket-info-row">
                                        <dt>PENYELENGGARA</dt>
                                        <dd><?= e($tiket['nama_ukm'] ?? $tiket['singkatan'] ?? 'EventIn') ?></dd>
                                    </div>
                                </dl>

                                <div class="tiket-actions">
                                    <button type="button" class="btn btn-primary" data-print-tiket>
                                        Unduh E-Tiket
                                    </button>
                                    <a class="btn btn-outline"
                                        href="<?= e($tiketController->googleCalendarUrl($tiket)) ?>"
                                        target="_blank" rel="noopener">
                                        Simpan ke Google Calendar
                                    </a>
                                    <a class="btn btn-outline"
                                        href="<?= e($tiketController->mapsUrl($tiket)) ?>"
                                        target="_blank" rel="noopener">
                                        Petunjuk Lokasi Gedung
                                    </a>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php endforeach; ?>
            </section>

            <aside class="tiket-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="M12 11v5M12 8h.01"></path>
                </svg>
                <div>
                    <strong>Pusatkan Akses &amp; Keamanan Tiket Masuk</strong>
                    <p>Jangan bagikan QR code Anda kepada siapa pun. Petugas hanya akan memindai kode tiket pada pintu masuk acara untuk menghindari penyalahgunaan.</p>
                </div>
            </aside>

        </div>
    </main>

    <?php require __DIR__ . '/../../../includes/footer.php'; ?>

    <script src="assets/js/qrcode.min.js"></script>
    <script src="assets/js/tiket.js"></script>
</body>

</html>

<?php
if (!isset($_SESSION['user'])) {
    header('Location: ?page=login');
    exit;
}

if (isset($ev)) {

    $base      = '../../';
    $activeNav = 'explore';

    require_once __DIR__ . '/../../includes/data.php';

    $peserta = $pembayaranPeserta;

?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <?php
        $pageTitle  = $ev['nama_event'];
        $assetBase  = '../../assets';
        $pageStyles = ['style.css'];
        require __DIR__ . '/../../includes/head.php';
        ?>
    </head>

    <body>

        <!-- Navbar Peserta -->
        <?php require __DIR__ . '/../../includes/navbar.php'; ?>

        <!-- Konten Utama Detail Event -->
        <main class="event-detail-container">
            <!-- Layout 2 Kolom -->
            <div class="event-detail-grid">
                <!-- Kolom Kiri: Banner, Judul, Penyelenggara, & Tab Deskripsi -->
                <div class="event-detail-main">
                    <div class="event-hero-banner">
                        <img
                            src="<?= e($storageModel->getImage($ev['poster'])) ?>"
                            alt="<?= e($ev['nama_event']) ?>">
                    </div>

                    <h1 class="event-detail-title"><?= e($ev['nama_event']) ?></h1>

                    <div class="organizer-bar">
                        <span class="organizer-badge">
                            <img
                                src="<?= e($storageModel->getImage($ev['logo'])) ?>"
                                alt="Poster <?= e($ev['nama_event']) ?>">
                        </span>
                        <div class="organizer-info">
                            <strong>Diselenggarakan oleh:</strong>
                            <span><?= e($ev['nama_ukm']) . ' (' . e($ev['singkatan']) . ') - POLINEMA' ?></span>
                        </div>
                    </div>

                    <!-- Navigasi Tab -->
                    <div class="detail-tabs" role="tablist">
                        <button type="button" class="detail-tab is-active" data-tab-target="tab-deskripsi">Deskripsi Acara</button>
                        <button type="button" class="detail-tab" data-tab-target="tab-lokasi">Informasi Lokasi</button>
                        <button type="button" class="detail-tab" data-tab-target="tab-kontak">Kontak Penyelenggara</button>
                    </div>

                    <!-- Isi Konten Tab -->
                    <div class="detail-tab-panels">
                        <div class="detail-panel" id="tab-deskripsi">
                            <p><?= e($ev['deskripsi']) ?></p>
                        </div>

                        <div class="detail-panel" id="tab-lokasi" hidden>
                            <p>Tempat penyelenggaraan event adalah <strong><?= e($ev['lokasi']) ?></strong>. Peserta diharapkan hadir 15 menit sebelum sesi dimulai untuk pemindaian QR tiket.</p>
                        </div>

                        <div class="detail-panel" id="tab-kontak" hidden>
                            <p>Hubungi kontak admin berikut: <strong><?= e($ev['nama']) . ' (' . e($ev['no_hp']) . ')' ?></strong>. Silakan hubungi sekretariat <?= e($ev['singkatan']) ?> melalui kanal informasi resmi kampus untuk pertanyaan lebih lanjut.</p>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Kartu Harga & Pendaftaran -->
                <aside class="event-detail-sidebar">
                    <div class="booking-card">
                        <span class="booking-price-label">HARGA TIKET MASUK</span>
                        <strong class="booking-price-value">Rp.<?= e(format_angka($ev['harga'])) ?></strong>

                        <hr class="booking-divider">

                        <ul class="booking-meta-list">
                            <li class="booking-meta-item">
                                <span class="meta-icon-box">T</span>
                                <?php if (format_tanggal($ev['tanggal_mulai']) == format_tanggal($ev['tanggal_selesai'])) { ?>
                                    <span><?= e(format_tanggal($ev['tanggal_mulai'])) ?> (<?= e(format_jam($ev['tanggal_mulai'])) ?>-<?= e(format_jam($ev['tanggal_selesai'])) ?>)</span>
                                <?php } else { ?>
                                    <span><?= e(format_tanggal_jam($ev['tanggal_mulai'])) ?> - <?= e(format_tanggal_jam($ev['tanggal_selesai'])) ?></span>
                                <?php } ?>
                            </li>
                            <li class="booking-meta-item">
                                <span class="meta-icon-box">L</span>
                                <span><?= e($ev['lokasi']) ?></span>
                            </li>
                        </ul>

                        <div class="quota-box">
                            <div class="quota-header">
                                <span class="muted">Kapasitas Tersisa</span>
                                <strong><?= (int)$ev['kuota'] - $ev['kuota_terkonfirmasi'] ?> / <?= (int)$ev['kuota'] ?> Kursi</strong>
                            </div>
                            <div class="quota-progress">
                                <div class="quota-bar" style="width: <?= (int)($ev['kuota_terkonfirmasi'] / $ev['kuota'] * 100) ?>%;"></div>
                            </div>
                        </div>
                        <form action="<?= $ev['harga'] == 0 ? $routes['konfirmasi'] : $routes['pembayaran'] ?>" method="post">
                            <?php if ($ev['harga'] == 0) { ?>
                                <input type="hidden" name="status" value="Lunas">
                            <?php } ?>
                            <input type="hidden" name="id_event" value=<?= $ev['id_event'] ?>>
                            <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
                        </form>

                        <hr class="booking-divider">

                        <div class="share-box">
                            <span class="share-title">Bagikan Event:</span>
                            <div class="share-buttons">
                                <a href="#" class="share-btn" aria-label="Bagikan ke WhatsApp">W</a>
                                <a href="#" class="share-btn" aria-label="Bagikan ke Facebook">F</a>
                                <a href="#" class="share-btn" aria-label="Bagikan ke Twitter">T</a>
                                <a href="#" class="share-btn" aria-label="Salin Tautan">G</a>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </main>

        <?php require __DIR__ . '/../../includes/footer.php'; ?>

        <script src="../../assets/js/main.js"></script>

        <script>
            // Interaksi perpindahan tab pada halaman Detail Event
            (function() {
                var tabs = document.querySelectorAll('.detail-tab');
                var panels = document.querySelectorAll('.detail-panel');

                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function() {
                        var targetId = tab.getAttribute('data-tab-target');

                        tabs.forEach(function(t) {
                            t.classList.remove('is-active');
                        });
                        panels.forEach(function(p) {
                            if (p.id === targetId) {
                                p.removeAttribute('hidden');
                            } else {
                                p.setAttribute('hidden', '');
                            }
                        });

                        tab.classList.add('is-active');
                    });
                });
            })();
        </script>
    </body>

    </html>
<?php } ?>
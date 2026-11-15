<?php
$base      = '../../';
$activeNav = 'explore';

require __DIR__ . '/../../includes/data.php';

$peserta = $pembayaranPeserta;

?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = $event['judul'];
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
            </div>

            <h1 class="event-detail-title"><?= e($event['judul']) ?></h1>

            <div class="organizer-bar">
                <span class="organizer-badge"><?= e($event['inisial_org']) ?></span>
                <div class="organizer-info">
                    <strong>Diselenggarakan oleh:</strong>
                    <span><?= e($event['penyelenggara']) ?></span>
                </div>
            </div>

            <!-- Navigasi Tab -->
            <div class="detail-tabs" role="tablist">
                <button type="button" class="detail-tab is-active" data-tab-target="tab-deskripsi">Deskripsi Acara</button>
                <button type="button" class="detail-tab" data-tab-target="tab-jadwal">Jadwal Sesi</button>
                <button type="button" class="detail-tab" data-tab-target="tab-lokasi">Informasi Lokasi</button>
                <button type="button" class="detail-tab" data-tab-target="tab-kontak">Kontak Penyelenggara</button>
            </div>

            <!-- Isi Konten Tab -->
            <div class="detail-tab-panels">
                <div class="detail-panel" id="tab-deskripsi">
                    <p><?= e($event['paragraf_1']) ?></p>
                    <p><?= e($event['paragraf_2']) ?></p>
                </div>

                <div class="detail-panel" id="tab-jadwal" hidden>
                    <p><strong>Sesi 1 (09:00 - 11:00 WIB):</strong> Pengenalan arsitektur No-Code dan perancangan antarmuka aplikasi.</p>
                    <p><strong>Sesi 2 (11:00 - 13:00 WIB):</strong> Integrasi logika data, pengujian prototipe, dan evaluasi proyek peserta.</p>
                </div>

                <div class="detail-panel" id="tab-lokasi" hidden>
                    <p>Kegiatan dilaksanakan secara luring di <strong><?= e($event['lokasi']) ?></strong>. Peserta diharapkan hadir 15 menit sebelum sesi dimulai untuk pemindaian QR tiket.</p>
                </div>

                <div class="detail-panel" id="tab-kontak" hidden>
                    <p>Penyelenggara: <strong><?= e($event['penyelenggara']) ?></strong>. Silakan hubungi sekretariat HMTI melalui kanal informasi resmi kampus untuk pertanyaan lebih lanjut.</p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Kartu Harga & Pendaftaran -->
        <aside class="event-detail-sidebar">
            <div class="booking-card">
                <span class="booking-price-label">HARGA TIKET MASUK</span>
                <strong class="booking-price-value"><?= e($event['harga']) ?></strong>

                <hr class="booking-divider">

                <ul class="booking-meta-list">
                    <li class="booking-meta-item">
                        <span class="meta-icon-box">T</span>
                        <span><?= e($event['waktu']) ?></span>
                    </li>
                    <li class="booking-meta-item">
                        <span class="meta-icon-box">L</span>
                        <span><?= e($event['lokasi']) ?></span>
                    </li>
                </ul>

                <div class="quota-box">
                    <div class="quota-header">
                        <span class="muted">Kapasitas Tersisa</span>
                        <strong><?= (int)$event['kursi_tersisa'] ?> / <?= (int)$event['kursi_total'] ?> Kursi</strong>
                    </div>
                    <div class="quota-progress">
                        <div class="quota-bar" style="width: <?= (int)$event['persen_terisi'] ?>%;"></div>
                    </div>
                </div>

                <a href="<?= e($base . 'app/views/pembayaran.php') ?>" class="btn btn-primary btn-block">Daftar Sekarang</a>

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
(function () {
    var tabs = document.querySelectorAll('.detail-tab');
    var panels = document.querySelectorAll('.detail-panel');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var targetId = tab.getAttribute('data-tab-target');

            tabs.forEach(function (t) {
                t.classList.remove('is-active');
            });
            panels.forEach(function (p) {
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

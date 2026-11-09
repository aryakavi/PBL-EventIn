<?php
$activeNav = 'tiket';
$base = '../../';
require __DIR__ . '/../../includes/data.php';

$peserta = $pembayaranPeserta;

// Anggap pengguna sudah login (auth asli belum tersambung) agar navbar menampilkan profil.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id_pengguna' => 1,
        'nama'        => $pembayaranPeserta['nama'],
        'email'       => '',
        'role'        => $pembayaranPeserta['role'],
    ];
}

$jmlStatus = ['aktif' => 0, 'selesai' => 0, 'batal' => 0];
foreach ($daftarTiket as $t) {
    $jmlStatus[$t['status']]++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Tiket Elektronik Saya';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<!-- Navbar Peserta -->
<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<!-- Konten Utama Tiket Elektronik -->
<main class="ticket-container">
    <!-- Header Halaman -->
    <div class="ticket-header">
        <div>
            <h1>Tiket Elektronik Saya</h1>
            <p>Kelola tiket event aktif, riwayat kegiatan, dan akses e-tiket barcode untuk check-in di lokasi.</p>
        </div>
        <button type="button" class="btn btn-outline btn-sync" onclick="window.location.reload()">
            <?= icon('refresh') ?>
            Sinkronisasi Tiket
        </button>
    </div>

    <!-- Bar Tab Status & Pencarian -->
    <div class="ticket-toolbar">
        <div class="ticket-tabs">
            <button type="button" class="ticket-tab is-active" data-status-tab="aktif">Aktif (<?= $jmlStatus['aktif'] ?>)</button>
            <button type="button" class="ticket-tab" data-status-tab="selesai">Telah Selesai (<?= $jmlStatus['selesai'] ?>)</button>
            <button type="button" class="ticket-tab" data-status-tab="batal">Dibatalkan (<?= $jmlStatus['batal'] ?>)</button>
        </div>

        <div class="ticket-filters">
            <div class="ticket-search-wrap">
                <span class="ticket-search-icon" aria-hidden="true">
                    <?= icon('search') ?>
                </span>
                <input type="text" class="ticket-search-input" placeholder="Cari nama event tiket..." data-ticket-search>
            </div>
            <select class="filter-select ticket-cat-select" aria-label="Filter Kategori" data-ticket-cat>
                <option value="">Semua Kategori</option>
                <option value="workshop">Workshop</option>
                <option value="seminar">Seminar</option>
            </select>
        </div>
    </div>

    <!-- Daftar Kartu Tiket -->
    <section class="ticket-list">
        <?php foreach ($daftarTiket as $t): ?>
            <article class="ticket-card<?= $t['terbuka'] ? ' is-open' : '' ?>" id="<?= e($t['id']) ?>" data-nama="<?= e(mb_strtolower($t['judul'])) ?>" data-kategori="<?= e(mb_strtolower($t['kategori'])) ?>" data-status="<?= e($t['status']) ?>">
                <!-- Bagian Atas (Ringkasan Event) -->
                <div class="ticket-summary">
                    <img src="<?= e($t['gambar']) ?>" alt="<?= e($t['judul']) ?>" class="ticket-thumb">

                    <div class="ticket-summary-info">
                        <h2 class="ticket-title"><?= e($t['judul']) ?></h2>
                        <div class="ticket-meta">
                            <span><?= icon('calendar') ?> <?= e($t['tanggal']) ?></span>
                            <span class="meta-dot">&bull;</span>
                            <span><?= icon('pin') ?> <?= e($t['lokasi']) ?></span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline ticket-toggle-btn" data-toggle-ticket="<?= e($t['id']) ?>">
                        <span class="toggle-text"><?= $t['terbuka'] ? 'Sembunyikan Detail' : 'Lihat Detail' ?></span>
                        <span class="toggle-chevron">
                            <?= icon($t['terbuka'] ? 'chevron-up' : 'chevron-down') ?>
                        </span>
                    </button>
                </div>

                <!-- Bagian Detail E-Tiket (Bisa Dibuka/Tutup) -->
                <div class="ticket-detail" <?= $t['terbuka'] ? '' : 'hidden' ?>>
                    <!-- Kolom Kiri: QR Code -->
                    <div class="ticket-qr-box">
                        <div class="ticket-qr-frame">
                            <e?= $ikonQr ?>
                        </div>
                        <div class="ticket-qr-caption">
                            <strong><span class="qr-mini-icon" aria-hidden="true"><?= icon('shield-check') ?></span> Scan saat tiba di lokasi</strong>
                            <span>Validitas tiket diverifikasi secara real-time</span>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Informasi Kredensial & Tombol Aksi -->
                    <div class="ticket-info-col">
                        <div class="ticket-cred-box">
                            <div class="cred-grid">
                                <div class="cred-item">
                                    <span class="cred-label">KODE TIKET ELEKTRONIK</span>
                                    <div class="cred-code-row">
                                        <strong class="cred-value cred-mono"><?= e($t['kode']) ?></strong>
                                        <button type="button" class="copy-btn" data-copy="<?= e($t['kode']) ?>" title="Salin Kode"><?= icon('clipboard') ?></button>
                                    </div>
                                </div>

                                <div class="cred-item">
                                    <span class="cred-label">PEMILIK TIKET RESMI</span>
                                    <strong class="cred-value"><?= e($t['pemilik']) ?></strong>
                                </div>

                                <div class="cred-item">
                                    <span class="cred-label">NOMOR INDUK MAHASISWA (NIM)</span>
                                    <strong class="cred-value cred-mono"><?= e($t['nim']) ?></strong>
                                </div>

                                <div class="cred-item">
                                    <span class="cred-label">HAK AKSES / TINGKAT</span>
                                    <strong class="cred-value">
                                        <?= e($t['akses']) ?>
                                        <span class="verified-badge" title="Terverifikasi" aria-hidden="true"><?= icon('check') ?></span>
                                    </strong>
                                </div>
                            </div>

                            <div class="cred-footer">
                                <span><strong class="seat-icon" aria-hidden="true"><?= icon('seat') ?></strong> Alokasi Meja: <strong><?= e($t['meja']) ?></strong></span>
                                <span>Sesi: <strong><?= e($t['sesi']) ?></strong></span>
                            </div>
                        </div>

                        <div class="ticket-actions">
                            <a href="#" class="btn btn-primary"><?= icon('download') ?> Unduh E-Tiket (PDF)</a>
                            <a href="#" class="btn btn-outline"><?= icon('calendar') ?> Simpan ke Google Calendar</a>
                            <a href="#" class="btn btn-outline"><?= icon('map') ?> Panduan Lokasi Gedung</a>
                        </div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>

    <!-- Kotak Info Keamanan Tiket -->
    <aside class="security-notice">
        <span class="notice-icon">i</span>
        <div class="notice-content">
            <strong>Panduan Akses &amp; Keamanan Tiket Masuk</strong>
            <p>Tunjukkan QR Code pada panitia registrasi di pintu masuk gedung untuk verifikasi presensi dan penerbitan sertifikat SKPI. Dilarang membagikan barcode atau kode tiket kepada pihak lain untuk mencegah duplikasi hak akses.</p>
        </div>
    </aside>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>
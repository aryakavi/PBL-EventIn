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
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"></path><path d="M21 3v5h-5"></path></svg>
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
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"></circle><path d="m21 21-4.3-4.3"></path></svg>
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
                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg> <?= e($t['tanggal']) ?></span>
                            <span class="meta-dot">&bull;</span>
                            <span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg> <?= e($t['lokasi']) ?></span>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline ticket-toggle-btn" data-toggle-ticket="<?= e($t['id']) ?>">
                        <span class="toggle-text"><?= $t['terbuka'] ? 'Sembunyikan Detail' : 'Lihat Detail' ?></span>
                        <span class="toggle-chevron">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><?= $t['terbuka'] ? '<path d="m6 15 6-6 6 6"></path>' : '<path d="m6 9 6 6 6-6"></path>' ?></svg>
                        </span>
                    </button>
                </div>

                <!-- Bagian Detail E-Tiket (Bisa Dibuka/Tutup) -->
                <div class="ticket-detail" <?= $t['terbuka'] ? '' : 'hidden' ?>>
                    <!-- Kolom Kiri: QR Code -->
                    <div class="ticket-qr-box">
                        <div class="ticket-qr-frame">
                            <svg viewBox="0 0 120 120" width="136" height="136" fill="#0f172a" aria-label="QR Code Tiket">
                                <path d="M10 10h34v34H10V10zm6 6v22h22V16H16zm6 6h10v10H22V22zm54-12h34v34H76V10zm6 6v22h22V16H82zm6 6h10v10H88V22zM10 76h34v34H10V76zm6 6v22h22V82H16zm6 6h10v10H22V88zm30-78h8v14h-8V10zm12 10h6v14h-6V20zM52 44h16v8H52v-8zM16 54h16v8H16v-8zm22 0h12v8H38v-8zm38 0h14v8H76v-8zm20 0h14v8H96v-8zM52 76h12v12H52V76zm22 0h8v18h-8V76zm14 0h22v8H88v-8zM52 96h16v14H52V96zm22 6h16v8H74v-8zm22-12h14v20H96V90z"/>
                                <rect x="54" y="54" width="12" height="12" rx="2" fill="#f97316"/>
                            </svg>
                        </div>
                        <div class="ticket-qr-caption">
                            <strong><span class="qr-mini-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 12 2 2 4-4"></path><path d="M12 3a9 9 0 1 0 9 9"></path></svg></span> Scan saat tiba di lokasi</strong>
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
                                        <button type="button" class="copy-btn" data-copy="<?= e($t['kode']) ?>" title="Salin Kode"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"></rect><path d="M5 15V5a2 2 0 0 1 2-2h10"></path></svg></button>
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
                                        <span class="verified-badge" title="Terverifikasi" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="m5 12 4 4 10-10"></path></svg></span>
                                    </strong>
                                </div>
                            </div>

                            <div class="cred-footer">
                                <span><strong class="seat-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 19V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13"></path><path d="M4 19h16M9 8h6M9 12h6"></path></svg></strong> Alokasi Meja: <strong><?= e($t['meja']) ?></strong></span>
                                <span>Sesi: <strong><?= e($t['sesi']) ?></strong></span>
                            </div>
                        </div>

                        <div class="ticket-actions">
                            <a href="#" class="btn btn-primary" data-print-tiket><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14"></path></svg> Unduh E-Tiket (PDF)</a>
                            <a href="https://calendar.google.com/calendar/r/eventedit?text=<?= rawurlencode($t['judul']) ?>&location=<?= rawurlencode($t['lokasi']) ?>" target="_blank" rel="noopener" class="btn btn-outline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="4" width="18" height="17" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg> Simpan ke Google Calendar</a>
                            <a href="https://www.google.com/maps/search/?api=1&query=<?= rawurlencode($t['lokasi'] . ', Politeknik Negeri Malang') ?>" target="_blank" rel="noopener" class="btn btn-outline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m9 4-6 2v14l6-2 6 2 6-2V4l-6 2-6-2Z"></path><path d="M9 4v14M15 6v14"></path></svg> Panduan Lokasi Gedung</a>
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
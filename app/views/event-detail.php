<?php
$activeNav = 'explore';
$base = '../../';
require __DIR__ . '/../../includes/data.php';

$idEvent = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Fallback bila data belum tersedia / koneksi database gagal.
$acara = [
    'id_event'       => 0,
    'nama_event'     => 'Workshop Pembuatan Aplikasi Seluler Tanpa Kode (No-Code App)',
    'deskripsi'      => 'Pelatihan intensif membangun aplikasi seluler tanpa menulis kode. Peserta akan mempelajari alur kerja no-code, menyusun prototipe, dan mempublikasikan aplikasi pertama dalam satu hari.',
    'tanggal_mulai'  => '2026-11-15 09:00:00',
    'tanggal_selesai' => '2026-11-15 15:00:00',
    'lokasi'         => 'Lab Multimedia Gedung D, Kampus Utama',
    'kuota'          => 50,
    'harga'          => 75000,
    'poster'         => null,
    'nama_kategori'  => 'Workshop',
    'nama_ukm'       => 'Unit Kegiatan Mahasiswa Teknologi Informasi',
    'singkatan'      => 'UKM TI',
    'pembuat_nama'   => 'Admin UKM TI',
    'pembuat_email'  => 'admin.ti@gmail.com',
    'pembuat_hp'     => '081234567895',
];

// Ambil detail event dari Supabase (read-only). Output error koneksi disembunyikan.
ob_start();
require_once __DIR__ . '/../../includes/koneksi.php';
ob_end_clean();

if (isset($pdo)) {
    try {
        $sql = "SELECT e.id_event, e.nama_event, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai,
                       e.lokasi, e.kuota, e.harga, e.poster,
                       k.nama_kategori,
                       p.nama AS pembuat_nama, p.email AS pembuat_email, p.no_hp AS pembuat_hp,
                       u.nama_ukm, u.singkatan
                FROM events e
                LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
                LEFT JOIN pengguna p ON p.id_pengguna = e.id_pembuat
                LEFT JOIN admin_ukm a ON a.id_pengguna = e.id_pembuat
                LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
                " . ($idEvent > 0 ? 'WHERE e.id_event = :id' : '') . "
                ORDER BY e.tanggal_mulai ASC
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($idEvent > 0 ? ['id' => $idEvent] : []);
        $row = $stmt->fetch();

        if ($row) {
            $acara = array_merge($acara, array_filter($row, static fn($v) => $v !== null && $v !== ''));
        }
    } catch (Throwable $e) {
        // Pakai data fallback.
    }
}

$mulai   = new DateTime($acara['tanggal_mulai']);
$selesai = new DateTime($acara['tanggal_selesai'] ?: $acara['tanggal_mulai']);

if ($selesai <= $mulai) {
    $selesai = (clone $mulai)->modify('+2 hours');
}

$durasiMenit = max(60, (int) (($selesai->getTimestamp() - $mulai->getTimestamp()) / 60));
$titik = static fn(float $rasio): DateTime => (clone $mulai)->modify('+' . (int) round($durasiMenit * $rasio) . ' minutes');

$sesi = [
    ['Registrasi & Check-in Peserta', $titik(0), $titik(0.15), 'Verifikasi e-tiket dan pengambilan name tag di meja registrasi.'],
    ['Pembukaan & Sambutan', $titik(0.15), $titik(0.3), 'Sambutan penyelenggara serta penjelasan agenda kegiatan.'],
    ['Sesi Inti & Praktik', $titik(0.3), $titik(0.85), 'Penyampaian materi utama dan pendampingan praktik peserta.'],
    ['Penutupan & Sertifikat', $titik(0.85), $selesai, 'Tanya jawab, penutupan, dan informasi unduhan e-sertifikat.'],
];

$lokasiQuery = rawurlencode($acara['lokasi'] . ', Politeknik Negeri Malang');
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $lokasiQuery;

$nomorWa = preg_replace('/\D/', '', (string) $acara['pembuat_hp']);
if (str_starts_with($nomorWa, '0')) {
    $nomorWa = '62' . substr($nomorWa, 1);
}
$waUrl = $nomorWa !== '' ? 'https://wa.me/' . $nomorWa : '#';

$penyelenggara = $acara['nama_ukm'] ?: ($acara['singkatan'] ?: 'EventIn Campus');
$emailKontak   = $acara['pembuat_email'] ?: 'hello@eventin.id';
$narahubung    = $acara['pembuat_nama'] ?: 'Panitia EventIn';

$login = isset($_SESSION['user']);
$daftarHref = $base . $routes['pembayaran'] . ($idEvent > 0 ? '?id=' . $idEvent : '');
$loginHref  = $base . $routes['login'];
$bagikanUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '');

$tanggalLengkap = date('d M Y', $mulai->getTimestamp());
$jamLengkap     = $mulai->format('H:i') . ' - ' . $selesai->format('H:i') . ' WIB';

$gambarAcara = event_image((int) $acara['id_event']);
if ($gambarAcara) {
    $gambarAcara = $base . $gambarAcara;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = $acara['nama_event'];
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<main class="acara-page">
    <nav class="acara-breadcrumb" aria-label="Breadcrumb">
        <a href="<?= e($base . $routes['home']) ?>">Beranda</a>
        <span aria-hidden="true">/</span>
        <a href="<?= e($base . $routes['explore']) ?>">Jelajah Event</a>
        <span aria-hidden="true">/</span>
        <span><?= e($acara['nama_kategori']) ?></span>
    </nav>

    <div class="acara-grid">
        <div class="acara-main">
            <div class="acara-cover" role="img" aria-label="Banner <?= e($acara['nama_event']) ?>"
                <?php if ($gambarAcara): ?>style="background-image: url('<?= e($gambarAcara) ?>');"<?php endif; ?>>
                <span class="acara-badge"><?= e(strtoupper($acara['nama_kategori'])) ?></span>
            </div>

            <h1 class="acara-title"><?= e($acara['nama_event']) ?></h1>

            <div class="acara-organizer">
                <span class="acara-organizer-mark"><?= e(mb_strtoupper(mb_substr($penyelenggara, 0, 1))) ?></span>
                <div>
                    <strong><?= e($penyelenggara) ?></strong>
                    <span>Penyelenggara Event</span>
                </div>
            </div>

            <nav class="acara-tabs" aria-label="Bagian detail event">
                <a class="acara-tab is-active" href="#deskripsi">Deskripsi Acara</a>
                <a class="acara-tab" href="#jadwal">Jadwal Sesi</a>
                <a class="acara-tab" href="#lokasi">Informasi Lokasi</a>
                <a class="acara-tab" href="#kontak">Kontak Penyelenggara</a>
            </nav>

            <section class="acara-section" id="deskripsi">
                <h2>Deskripsi Acara</h2>
                <p><?= nl2br(e($acara['deskripsi'])) ?></p>
                <ul class="acara-perks">
                    <li>E-sertifikat resmi terintegrasi SKPI</li>
                    <li>Akses penuh ke seluruh sesi kegiatan</li>
                    <li>Konsumsi dan goodie bag peserta</li>
                </ul>
            </section>

            <section class="acara-section" id="jadwal">
                <h2>Jadwal Sesi</h2>
                <?php foreach ($sesi as $i => [$judul, $dari, $sampai, $ket]): ?>
                    <div class="acara-session">
                        <span class="acara-session-time">
                            <?= e($dari->format('H:i')) ?><br>
                            <?= e($sampai->format('H:i')) ?> WIB
                        </span>
                        <div class="acara-session-body">
                            <strong>Sesi <?= $i + 1 ?> &middot; <?= e($judul) ?></strong>
                            <span><?= e($ket) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="acara-section" id="lokasi">
                <h2>Informasi Lokasi</h2>
                <div class="acara-location">
                    <span class="acara-pin" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z"></path>
                            <circle cx="12" cy="10" r="2.5"></circle>
                        </svg>
                    </span>
                    <div>
                        <strong><?= e($acara['lokasi']) ?></strong>
                        <p>Politeknik Negeri Malang, Jl. Soekarno-Hatta No. 9, Malang</p>
                        <p class="acara-location-note">Peserta disarankan hadir 30 menit sebelum sesi dimulai untuk proses check-in.</p>
                        <a class="acara-map-link" href="<?= e($mapsUrl) ?>" target="_blank" rel="noopener">Buka di Google Maps &rarr;</a>
                    </div>
                </div>
            </section>

            <section class="acara-section" id="kontak">
                <h2>Kontak Penyelenggara</h2>
                <div class="acara-contact">
                    <div class="acara-contact-item">
                        <span class="acara-contact-label">PENYELENGGARA</span>
                        <strong><?= e($penyelenggara) ?></strong>
                    </div>
                    <div class="acara-contact-item">
                        <span class="acara-contact-label">NARAHUBUNG</span>
                        <strong><?= e($narahubung) ?></strong>
                    </div>
                    <div class="acara-contact-item">
                        <span class="acara-contact-label">EMAIL</span>
                        <a href="mailto:<?= e($emailKontak) ?>"><?= e($emailKontak) ?></a>
                    </div>
                    <div class="acara-contact-item">
                        <span class="acara-contact-label">WHATSAPP</span>
                        <a href="<?= e($waUrl) ?>" target="_blank" rel="noopener">
                            <?= e($acara['pembuat_hp'] ?: '-') ?>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <aside class="acara-aside">
            <div class="acara-price-card">
                <span class="acara-price-label">HARGA TIKET</span>
                <strong class="acara-price"><?= e(format_rupiah($acara['harga'])) ?></strong>

                <div class="acara-fact">
                    <span aria-hidden="true">&#128197;</span>
                    <span><?= e($tanggalLengkap) ?><br><small><?= e($jamLengkap) ?></small></span>
                </div>
                <div class="acara-fact">
                    <span aria-hidden="true">&#128205;</span>
                    <span><?= e($acara['lokasi']) ?></span>
                </div>
                <div class="acara-fact">
                    <span aria-hidden="true">&#128101;</span>
                    <span>Kuota <?= e((string) $acara['kuota']) ?> peserta</span>
                </div>

                <a class="btn btn-primary acara-cta" href="<?= e($login ? $daftarHref : $loginHref) ?>">
                    <?= $login ? 'Daftar Sekarang' : 'Masuk untuk Mendaftar' ?>
                </a>

                <button type="button" class="btn btn-outline acara-cta" data-copy="<?= e($bagikanUrl) ?>">
                    Salin Tautan Event
                </button>

                <p class="acara-secure">Pembayaran aman dan tiket elektronik langsung diterbitkan setelah verifikasi.</p>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>

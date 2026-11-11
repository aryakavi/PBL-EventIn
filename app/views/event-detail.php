<?php
$activeNav = 'explore';
$base = '../../';
require __DIR__ . '/../../includes/data.php';
require __DIR__ . '/../../includes/koneksi.php';
require __DIR__ . '/../../app/models/SiteRepository.php';
require __DIR__ . '/../../app/controllers/PendaftaranController.php';

$emptyText = 'kosong, masih kurang';

$idEvent = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$siteRepo = isset($pdo) && $pdo instanceof PDO ? new SiteRepository($pdo) : null;

if ($siteRepo === null) {
    header('Location: ' . $base . $routes['home']);
    exit;
}

$daftarNotice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'daftar') {
    $sesi = $_SESSION['user'] ?? null;

    if (!is_array($sesi) || empty($sesi['id_pengguna'])) {
        header('Location: ' . $base . $routes['login']);
        exit;
    }

    if (!csrf_valid($_POST['csrf_token'] ?? null)) {
        $daftarNotice = 'Sesi keamanan kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } else {
        $idEventPost = (int) ($_POST['id_event'] ?? $idEvent);
        $hasil = (new PendaftaranController($pdo))->daftar((int) $sesi['id_pengguna'], $idEventPost);

        if ($hasil['success']) {
            $status = strtolower((string) ($hasil['status_bayar'] ?? ''));
            $tujuan = str_starts_with($status, 'lunas')
                ? $base . $routes['tiket']
                : $base . $routes['pembayaran'] . '?id_tiket=' . (int) $hasil['id_tiket'];

            header('Location: ' . $tujuan);
            exit;
        }

        $daftarNotice = $hasil['message'];
    }
}

$acara = $idEvent > 0 ? $siteRepo->event($idEvent) : $siteRepo->sorotan();

// Event tidak ditemukan: kembali ke daftar event.
if (empty($acara)) {
    header('Location: ' . $base . $routes['home']);
    exit;
}

foreach (['nama_event', 'deskripsi', 'lokasi', 'nama_kategori', 'nama_ukm', 'singkatan', 'pembuat_nama', 'pembuat_email', 'pembuat_hp'] as $kunci) {
    if (!isset($acara[$kunci]) || trim((string) $acara[$kunci]) === '') {
        $acara[$kunci] = $emptyText;
    }
}

if (empty($acara['tanggal_mulai'])) {
    $acara['tanggal_mulai'] = date('Y-m-d H:i:s');
}
if (empty($acara['tanggal_selesai'])) {
    $acara['tanggal_selesai'] = $acara['tanggal_mulai'];
}

$mulai   = new DateTime($acara['tanggal_mulai']);
$selesai = new DateTime($acara['tanggal_selesai'] ?: $acara['tanggal_mulai']);

if ($selesai <= $mulai) {
    $selesai = (clone $mulai)->modify('+2 hours');
}

$lokasiQuery = rawurlencode($acara['lokasi']);
$mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $lokasiQuery;

$nomorWa = preg_replace('/\D/', '', (string) $acara['pembuat_hp']);
if (str_starts_with($nomorWa, '0')) {
    $nomorWa = '62' . substr($nomorWa, 1);
}
$waUrl = $nomorWa !== '' ? 'https://wa.me/' . $nomorWa : '#';

$penyelenggara = $acara['nama_ukm'];
$emailKontak   = $acara['pembuat_email'];
$narahubung    = $acara['pembuat_nama'];

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
                <div class="acara-session">
                    <span class="acara-session-time">
                        <?= e($mulai->format('H:i')) ?><br>
                        <?= e($selesai->format('H:i')) ?> WIB
                    </span>
                    <div class="acara-session-body">
                        <strong><?= e($mulai->format('d M Y')) ?></strong>
                        <span><?= e($emptyText) ?></span>
                    </div>
                </div>
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

                <?php if ($daftarNotice !== ''): ?>
                    <div class="profil-alert profil-alert--error" role="alert"><?= e($daftarNotice) ?></div>
                <?php endif; ?>

                <?php if ($login): ?>
                    <form method="post" class="acara-cta-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="daftar">
                        <input type="hidden" name="id_event" value="<?= (int) $acara['id_event'] ?>">
                        <button type="submit" class="btn btn-primary acara-cta">Daftar Sekarang</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-primary acara-cta" href="<?= e($loginHref) ?>">Masuk untuk Mendaftar</a>
                <?php endif; ?>

                <button type="button" class="btn btn-outline acara-cta" data-copy="<?= e($bagikanUrl) ?>">
                    Salin Tautan Event
                </button>

                <p class="acara-secure">Pembayaran aman dan tiket elektronik langsung diterbitkan setelah verifikasi.</p>
            </div>
        </aside>
    </div>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>
</html>

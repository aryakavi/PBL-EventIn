<?php
/**
 * Data & helper untuk landing page EventIn.
 * Semua konten dipisah di sini supaya index.php tetap bersih.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Token CSRF untuk form POST. */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['_csrf'];
}

/** Validasi token CSRF. */
function csrf_valid(?string $token): bool
{
    $sessionToken = $_SESSION['_csrf'] ?? '';

    return is_string($token)
        && $sessionToken !== ''
        && hash_equals((string) $sessionToken, $token);
}

/** Escape output HTML. */
function e($text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

/**
 * Versi aset berdasarkan waktu ubah file, dipakai sebagai query string
 * supaya browser tidak memakai CSS/JS lama dari cache.
 *
 * @param string $path Path aset relatif terhadap root proyek, mis. "assets/css/style.css".
 */
function asset_version(string $path): string
{
    $file = __DIR__ . '/../' . ltrim($path, '/');

    return is_file($file) ? (string) filemtime($file) : '1';
}

/** URL aset lengkap dengan versi cache-busting. */
function asset_url(string $path): string
{
    return ltrim($path, '/') . '?v=' . asset_version($path);
}


/** Cek apakah role termasuk admin/penyelenggara. */
function is_admin_role(?string $role): bool
{
    return in_array(strtolower((string) $role), ['admin', 'admin_ukm', 'organizer'], true);
}

/** Label role yang ramah dibaca. */
function role_label(?string $role): string
{
    return match (strtolower((string) $role)) {
        'admin_ukm', 'admin', 'organizer' => 'ADMIN ORGANISASI',
        'pengguna', 'peserta' => 'PESERTA',
        default => strtoupper((string) $role) !== '' ? strtoupper((string) $role) : 'PESERTA',
    };
}

/** Ambil inisial dari nama (maksimal 2 huruf). */
function user_initials(string $nama): string
{
    $kata = preg_split('/\s+/', trim($nama)) ?: [];
    $inisial = '';

    foreach ($kata as $k) {
        if ($k === '') {
            continue;
        }
        $inisial .= mb_strtoupper(mb_substr($k, 0, 1));
        if (mb_strlen($inisial) >= 2) {
            break;
        }
    }

    return $inisial !== '' ? $inisial : 'U';
}

/** Format angka menjadi rupiah sederhana. */
function format_rupiah($angka): string
{
    return 'Rp ' . number_format((float) $angka, 0, ',', '.');
}


function format_tanggal($tanggal)
{
    $format = new IntlDateFormatter(
        'id_ID',
        IntlDateFormatter::LONG,
        IntlDateFormatter::SHORT,
        'Asia/Jakarta'
    );

    return $format->format(strtotime($tanggal));
}


/**
 * Cari file gambar event di assets/img (event-1.jpg / .jpeg / .png / .webp).
 * Mengembalikan path relatif, atau null kalau belum ada filenya.
 */
function event_image(int $n): ?string
{
    foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
        $rel = "assets/img/event-{$n}.{$ext}";
        if (is_file(__DIR__ . '/../' . $rel)) {
            return $rel;
        }
    }
    return null;
}

$routes = [
    'home'              => 'index.php',
    'explore'           => 'index.php#explore',
    'partners'          => 'index.php#partners',
    'faq'               => 'index.php#faq',
    'login'             => 'login.php',
    'register'          => 'register.php',
    'register-admin'    => 'register-admin.php',
    'register_admin'    => 'register-admin.php',
    'tiket'             => 'app/views/tiket-saya.php',
    'detail'            => 'app/views/event-detail.php',
    'pembayaran'        => 'app/views/pembayaran.php',
    'profil'            => 'app/views/profil.php',
    'edit-profil'       => 'app/views/edit-profil.php',
    'logout'            => 'logout.php',
    'admin-dashboard'   => 'app/views/admin/index.php',
    'admin-profil'      => 'app/views/admin/profil.php',
    'admin-edit-profil' => 'app/views/admin/edit-profil.php',
];

$steps = [
    ['1', 'Daftar Akun',       'Buat profil mahasiswa menggunakan alamat email resmi institusi kampus Anda.'],
    ['2', 'Pilih Event',       'Temukan kegiatan menarik sesuai minat akademik dan organisasi.'],
    ['3', 'Ikuti Event',       'Hadiri sesi secara langsung maupun online via tautan video terintegrasi.'],
    ['4', 'Sertifikat Digital','Unduh sertifikat resmi otomatis langsung dari dashboard akun Anda.'],
];

$features = [
    ['F1', 'Dasbor Analitik',        'Pantau demografi dan statistik jumlah pendaftar secara langsung.'],
    ['F2', 'E-Sertifikat Otomatis',  'Buat template dan kirim sertifikat massal dalam satu klik.'],
    ['F3', 'Manajemen Presensi',     'Sistem absensi berbasis pindai QR code yang cepat dan aman.'],
    ['F4', 'Laporan Terpadu',        'Unduh berkas laporan pendaftaran dan keuangan yang siap saji.'],
];

$faqs = [
    ['Apakah platform ini gratis bagi seluruh mahasiswa?',               'Pendaftaran akun gratis. Biaya hanya berlaku bila event yang Anda pilih berbayar.'],
    ['Bagaimana cara melakukan verifikasi e-sertifikat yang sah?',       'Buka menu Tiket Saya, lalu periksa kode sertifikat pada detail tiket Anda.'],
    ['Metode pembayaran apa saja yang saat ini didukung?',               'Transfer bank, dompet digital, dan QRIS sesuai metode yang tersedia pada tiket.'],
    ['Bagaimana jika saya terlambat menghadiri event yang sudah dibeli?','Kehadiran mengikuti kebijakan penyelenggara masing-masing event.'],
    ['Bagaimana cara mengajukan pengembalian dana (refund)?',            'Hubungi penyelenggara event melalui kontak yang tertera pada halaman detail event.'],
];

$footerNav     = ['Beranda', 'Jelajah Event', 'Mitra Kampus'];
$footerSupport = ['Pusat Bantuan', 'Syarat & Ketentuan', 'Kebijakan Privasi'];
$socials       = ['Fb', 'Ig', 'X', 'In'];

$konfigurasiStatus = [
    'gagal' => [
        'tema'        => 'danger',
        'ikon'        => 'times',
        'pill'        => 'PEMBAYARAN GAGAL ATAU KADALUARSA',
        'judul'       => 'Pemesanan Tiket Gagal!',
        'deskripsi'   => 'Maaf, pembayaran Anda tidak berhasil diverifikasi atau telah melewati batas waktu pembayaran yang ditentukan. Silakan lakukan pemesanan ulang tiket Anda.',
        'badge_label' => 'Gagal / Kadaluarsa',
        'btn_utama'   => ['teks' => 'Pesan Ulang Tiket', 'ikon' => 'refresh', 'href' => 'pembayaran.php', 'class' => 'btn-danger'],
        'btn_kedua'   => ['teks' => 'Hubungi Bantuan', 'ikon' => 'chat', 'href' => '#'],
    ],
    'menunggu' => [
        'tema'        => 'warning',
        'ikon'        => 'clock',
        'pill'        => 'PEMBAYARAN SEDANG DIVERIFIKASI',
        'judul'       => 'Pembayaran Sedang Diverifikasi!',
        'deskripsi'   => 'Terima kasih, pembayaran Anda sedang diproses dan diverifikasi otomatis oleh sistem. Proses ini biasanya memakan waktu beberapa menit. Kami akan segera memperbarui status Anda.',
        'badge_label' => 'Sedang Diverifikasi',
        'btn_utama'   => ['teks' => 'Muat Ulang Status', 'ikon' => 'refresh', 'href' => '?status=menunggu', 'class' => 'btn-primary'],
        'btn_kedua'   => ['teks' => 'Unduh Invoice (PDF)', 'ikon' => 'download', 'href' => '#'],
    ],
    'sukses' => [
        'tema'        => 'success',
        'ikon'        => 'check',
        'pill'        => 'PEMBAYARAN BERHASIL & TERKONFIRMASI',
        'judul'       => 'Pemesanan Tiket Berhasil!',
        'deskripsi'   => 'Terima kasih, pembayaran Anda telah diverifikasi otomatis oleh sistem. E-tiket resmi dan invoice digital telah dikirimkan ke <strong>{email}</strong>.',
        'badge_label' => 'Lunas Otomatis',
        'btn_utama'   => ['teks' => 'Lihat &amp; Unduh Tiket Saya', 'ikon' => 'ticket', 'href' => 'tiket-saya.php', 'class' => 'btn-primary'],
        'btn_kedua'   => ['teks' => 'Unduh Invoice (PDF)', 'ikon' => 'download', 'href' => '#'],
    ],
];

// Peta ikon SVG untuk halaman konfirmasi pembayaran
$ikonStatus = [
    'times' => '<path d="M18 6 6 18M6 6l12 12"></path>',
    'clock' => '<circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>',
    'check' => '<path d="m5 12 4 4 10-10"></path>',
];

$ikonTombol = [
    'refresh'  => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8"></path><path d="M21 3v5h-5"></path>',
    'chat'     => '<path d="M21 12a8 8 0 0 1-11.5 7.2L4 21l1.8-5.5A8 8 0 1 1 21 12Z"></path>',
    'download' => '<path d="M12 3v12"></path><path d="m7 10 5 5 5-5"></path><path d="M5 21h14"></path>',
    'ticket'   => '<path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4Z"></path><path d="M14 6v12"></path>',
];

$ikonTrust = [
    'shield'  => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6Z"></path><path d="m9 12 2 2 4-4"></path>',
    'lock'    => '<rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path>',
    'headset' => '<path d="M4 14v-2a8 8 0 0 1 16 0v2"></path><rect x="2" y="14" width="4" height="6" rx="1"></rect><rect x="18" y="14" width="4" height="6" rx="1"></rect><path d="M20 20a3 3 0 0 1-3 2h-3"></path>',
];

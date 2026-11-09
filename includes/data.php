<?php
/**
 * Data & helper untuk landing page EventIn.
 * Semua konten dipisah di sini supaya index.php tetap bersih.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/icons.php';

/** Escape output HTML. */
function e(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
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
    'home'      => 'index.php',
    'explore'   => '#',   // explore-events.php
    'partners'  => '#partners',
    'login'     => 'app/views/login.php',
    'register'  => 'app/views/register.php',
    'register-admin' => 'app/views/register-admin.php',
    'tiket'     => 'app/views/tiket-saya.php',
    'detail'    => '#',   // event-detail.php
];

$stats = [
    ['500+', 'Event Terdaftar'],
    ['10K+', 'Peserta Aktif'],
    ['100+', 'Organisasi Kampus'],
];

$featured = [
    'ticket_title' => 'Simposium Nasional AI & Masa Depan Pendidikan Tinggi',
    'ticket_meta'  => 'Rabu, 25 April 2026 | Auditorium Gedung Sipil Lt. 8',
    'ticket_owner' => 'Budi Gunawan',
    'title'        => 'Simposium AI & Pendidikan Tinggi',
    'desc'         => 'Menghadirkan para ahli komputasi awan dan praktisi akademis guna mendiskusikan integrasi teknologi kecerdasan buatan dalam kurikulum masa kini. Dapatkan sertifikat berstandar nasional dan jalin relasi langsung dengan pelaku industri terkemuka.',
    'perks'        => [
        'Sertifikat Resmi Terintegrasi SKPI',
        'Sesi Networking Eksklusif Pembicara',
        'Konsumsi Lengkap & Goodie Bag',
    ],
];

$partners = ['Logo Mitra 1', 'Logo Mitra 2', 'Logo Mitra 3', 'Logo Mitra 4', 'Logo Mitra 5', 'Logo Mitra 6'];

$categories = [
    'semua'    => 'Semua Kategori',
    'Seminar'  => 'Seminar',
    'Workshop' => 'Workshop',
    'Kompetisi'=> 'Kompetisi',
    'Webinar'  => 'Webinar',
];

$cardCategories = ['semua', 'Workshop', 'Kompetisi', 'Webinar', 'Seminar', 'Workshop'];
$events = [];
foreach ($cardCategories as $i => $cat) {
    $n = $i + 1;
    $events[] = [
        'n'        => $n,
        'category' => $cat,
        'title'    => "Judul Event Eksklusif Ke-{$n} Kampus Utama",
        'date'     => '24 Maret 2026',
        'location' => 'Aula Utama Gedung C',
        'price'    => 'Rp 45.000',
    ];
}

$steps = [
    ['1', 'Daftar Akun',       'Buat profil mahasiswa menggunakan alamat email resmi institusi kampus Anda.'],
    ['2', 'Pilih Event',       'Temukan ratusan kegiatan menarik sesuai minat akademik dan organisasi.'],
    ['3', 'Ikuti Event',       'Hadiri sesi secara langsung maupun online via tautan video terintegrasi.'],
    ['4', 'Sertifikat Digital','Unduh sertifikat resmi otomatis langsung dari dashboard akun Anda.'],
];

$features = [
    ['F1', 'Dasbor Analitik',        'Pantau demografi dan statistik jumlah pendaftar secara langsung.'],
    ['F2', 'E-Sertifikat Otomatis',  'Buat template dan kirim sertifikat massal dalam satu klik.'],
    ['F3', 'Manajemen Presensi',     'Sistem absensi berbasis pindai QR code yang cepat dan aman.'],
    ['F4', 'Laporan Terpadu',        'Unduh berkas laporan pendaftaran dan keuangan yang siap saji.'],
];

$orgs = [
    ['Himpunan Mahasiswa Jurusan 1', '120 Anggota Terdaftar'],
    ['Himpunan Mahasiswa Jurusan 2', '160 Anggota Terdaftar'],
    ['Himpunan Mahasiswa Jurusan 3', '200 Anggota Terdaftar'],
    ['Unit Kegiatan Mahasiswa 4',    '178 Anggota Terdaftar'],
    ['Unit Kegiatan Mahasiswa 5',    '210 Anggota Terdaftar'],
    ['Unit Kegiatan Mahasiswa 6',    '242 Anggota Terdaftar'],
];


$faqs = [
    ['Apakah platform ini gratis bagi seluruh mahasiswa?',               'bayar.'],
    ['Bagaimana cara melakukan verifikasi e-sertifikat yang sah?',       'kepo amat.'],
    ['Metode pembayaran apa saja yang saat ini didukung?',               'banyak.'],
    ['Bagaimana jika saya terlambat menghadiri event yang sudah dibeli?','hangus deh.'],
    ['Bagaimana cara mengajukan pengembalian dana (refund)?',            'gabisa wlee .'],
];

$footerNav     = ['Beranda', 'Jelajah Event', 'Mitra Kampus'];
$footerSupport = ['Pusat Bantuan', 'Syarat & Ketentuan', 'Kebijakan Privasi'];
$socials       = ['Fb', 'Ig', 'X', 'In'];

$pembayaranPeserta = [
    'nama'    => 'Aryakavi',
    'role'    => 'PESERTA',
    'inisial' => 'A',
];

$ringkasanPesanan = [
    'judul'    => 'Workshop Pembuatan Aplikasi Seluler Tanpa Kode (No-Code App)',
    'tiket'    => '1x Tiket Akses Mandiri Utama',
    'subtotal' => 'Rp 75.000',
    'admin'    => 'Rp 2.500',
    'total'    => 'Rp 77.500',
];

$daftarTiket = [
    [
        'id'           => 'tiket-1',
        'kategori'     => 'WORKSHOP',
        'status_tiket' => 'TERKONFIRMASI',
        'status'       => 'aktif',
        'judul'        => 'Workshop Pembuatan Aplikasi Seluler Tanpa Kode',
        'tanggal'      => 'Sabtu, 28 April 2026',
        'lokasi'       => 'Gedung D Lab Multimedia',
        'gambar'       => 'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&w=300&q=80',
        'kode'         => 'EVIN-9872109-APP',
        'pemilik'      => 'Shin Ryujin (Aryakavi)',
        'nim'          => '240910291',
        'akses'        => 'Akses Penuh + Sertifikat Utama',
        'meja'         => 'Lab Multimedia 2 - Meja 14',
        'sesi'         => 'Pagi (08:30 - 15:30 WIB)',
        'terbuka'      => false,
    ],
    [
        'id'           => 'tiket-2',
        'kategori'     => 'SEMINAR',
        'status_tiket' => 'TERKONFIRMASI',
        'status'       => 'aktif',
        'judul'        => 'Simposium Nasional Masa Depan Teknologi Blockchain',
        'tanggal'      => 'Selasa, 12 Mei 2026',
        'lokasi'       => 'Rektorat Lt. 4 Polinema',
        'gambar'       => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=300&q=80',
        'kode'         => 'EVIN-5541023-BLK',
        'pemilik'      => 'Shin Ryujin (Aryakavi)',
        'nim'          => '240910291',
        'akses'        => 'Peserta Reguler + E-Sertifikat SKPI',
        'meja'         => 'Auditorium Lt. 4 - Kursi B12',
        'sesi'         => 'Pagi (09:00 - 13:00 WIB)',
        'terbuka'      => false,
    ],
    [
        'id'           => 'tiket-3',
        'kategori'     => 'SEMINAR',
        'status_tiket' => 'SELESAI',
        'status'       => 'selesai',
        'judul'        => 'Webinar Kewirausahaan Startup Digital',
        'tanggal'      => 'Kamis, 10 April 2026',
        'lokasi'       => 'Online Zoom Meeting',
        'gambar'       => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=300&q=80',
        'kode'         => 'EVIN-2210847-WEB',
        'pemilik'      => 'Shin Ryujin (Aryakavi)',
        'nim'          => '240910291',
        'akses'        => 'Peserta Reguler + E-Sertifikat SKPI',
        'meja'         => 'Online - Ruang Utama',
        'sesi'         => 'Siang (13:00 - 16:00 WIB)',
        'terbuka'      => false,
    ],
    [
        'id'           => 'tiket-4',
        'kategori'     => 'WORKSHOP',
        'status_tiket' => 'DIBATALKAN',
        'status'       => 'batal',
        'judul'        => 'Workshop UI/UX Design Thinking',
        'tanggal'      => 'Jumat, 3 April 2026',
        'lokasi'       => 'Lab Multimedia D',
        'gambar'       => 'https://images.unsplash.com/photo-1586717791821-3f44a563fa4c?auto=format&fit=crop&w=300&q=80',
        'kode'         => 'EVIN-3390412-UIX',
        'pemilik'      => 'Shin Ryujin (Aryakavi)',
        'nim'          => '240910291',
        'akses'        => 'Akses Penuh',
        'meja'         => '-',
        'sesi'         => 'Pagi (08:00 - 12:00 WIB)',
        'terbuka'      => false,
    ],
];

$daftarPembayaran = [
    [
        'id'      => 'TX-100293',
        'peserta' => 'Shin Ryujin',
        'event'   => 'Simposium Nasional AI',
        'event_lengkap' => 'Simposium Nasional AI & Masa Depan Pendidikan Tinggi',
        'jumlah'  => 'Rp 75.000',
        'status'  => 'Menunggu',
        'metode'  => 'Virtual Account Bank Mandiri',
        'waktu'   => '24 April 2026, 14:32 WIB',
        'berkas'  => 'bni-transfer.png',
    ],
    [
        'id'      => 'TX-100294',
        'peserta' => 'Park Jinyoung',
        'event'   => 'Workshop No-Code App',
        'event_lengkap' => 'Workshop No-Code App Maker untuk Mahasiswa',
        'jumlah'  => 'Rp 75.000',
        'status'  => 'Diverifikasi',
        'metode'  => 'QRIS Kampus',
        'waktu'   => '24 April 2026, 15:10 WIB',
        'berkas'  => 'qris-jinyoung.png',
    ],
    [
        'id'      => 'TX-100295',
        'peserta' => 'Dewi Kurniasari',
        'event'   => 'Workshop No-Code App',
        'event_lengkap' => 'Workshop No-Code App Maker untuk Mahasiswa',
        'jumlah'  => 'Rp 75.000',
        'status'  => 'Menunggu',
        'metode'  => 'Transfer Bank BCA',
        'waktu'   => '25 April 2026, 09:15 WIB',
        'berkas'  => 'bca-dewi.jpg',
    ],
    [
        'id'      => 'TX-100296',
        'peserta' => 'Dewi Lestari',
        'event'   => 'Simposium Nasional AI',
        'event_lengkap' => 'Simposium Nasional AI & Masa Depan Pendidikan Tinggi',
        'jumlah'  => 'Rp 75.000',
        'status'  => 'Diverifikasi',
        'metode'  => 'Virtual Account Bank BNI',
        'waktu'   => '25 April 2026, 11:40 WIB',
        'berkas'  => 'bni-lestari.png',
    ],
    [
        'id'      => 'TX-100297',
        'peserta' => 'Ahmad Dani',
        'event'   => 'Simposium Nasional AI',
        'event_lengkap' => 'Simposium Nasional AI & Masa Depan Pendidikan Tinggi',
        'jumlah'  => 'Rp 75.000',
        'status'  => 'Ditolak',
        'metode'  => 'Transfer Bank BRI',
        'waktu'   => '25 April 2026, 13:05 WIB',
        'berkas'  => 'bri-ahmad.png',
    ],
];

$transaksiKonfirmasi = [
    'nomor'   => '#INV-2026-EVIN-847291',
    'event'   => 'Workshop Pembuatan Aplikasi Seluler Tanpa Kode (No-Code App)',
    'tiket'   => '1x Tiket Akses Mandiri Utama • Kursi Reguler',
    'waktu'   => 'Sabtu, 28 April 2026 | 09:00 - 13:00 WIB',
    'tempat'  => 'Lab Multimedia Gedung D, Kampus Utama',
    'metode'  => 'QRIS Mandiri Otomatis',
    'tanggal' => '24 April 2026, 14:32 WIB',
    'total'   => 'Rp 77.500',
];

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


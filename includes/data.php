<?php
/**
 * Data & helper untuk landing page EventIn.
 * Semua konten dipisah di sini supaya index.php tetap bersih.
 */

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
    'login'     => '#',
    'register'  => '#',
    'detail'    => '#',   // event-detail.php
];

$stats = [
    ['500+', 'Event Terdaftar'],
    ['10K+', 'Peserta Aktif'],
    ['100+', 'Mitra Industri'],
    ['50+',  'Mitra Kampus'],
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

/*
 * Desain Figma hanya menampilkan pertanyaan (versi tertutup).
 * Isi 'a' di bawah ini masih placeholder, ganti sesuai kebutuhan tugas.
 */
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

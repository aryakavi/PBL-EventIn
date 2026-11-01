<?php
// Data contoh (nanti bisa diganti query database)

require_once __DIR__ . '/../data.php';

$organizer = [
    'nama'  => 'HMTI',
    'role'  => 'ORGANIZER',
    'inisial' => 'H',
];

$menu = [
    ['label' => 'Dashboard',  'href' => 'index.php',       'key' => 'dashboard'],
    ['label' => 'Event Saya', 'href' => 'event.php',       'key' => 'event'],
    ['label' => 'Pembayaran', 'href' => 'pembayaran.php',  'key' => 'pembayaran'],
    ['label' => 'Qr-Checker', 'href' => 'qr.php',          'key' => 'qr'],
    ['label' => 'Laporan',    'href' => 'laporan.php',     'key' => 'laporan'],
];

$kpi = [
    ['label' => 'Total Event',       'nilai' => '24',           'tren' => '+4 event baru bulan ini',     'arah' => 'up'],
    ['label' => 'Total Peserta',     'nilai' => '1.240',        'tren' => '+12% pendaftar baru',         'arah' => 'up'],
    ['label' => 'Pendapatan Bersih', 'nilai' => 'Rp 1.676.767', 'tren' => '+8.4% vs target',             'arah' => 'up'],
    ['label' => 'Event Aktif',       'nilai' => '8',            'tren' => '3 memerlukan absensi hari ini', 'arah' => 'down'],
];

$aktivitas = [
    ['event' => 'Simposium Nasional AI',  'tanggal' => '24 Apr 2026', 'peserta' => 'Budi Gunawan',  'status' => 'Lunas'],
    ['event' => 'Workshop No-Code App',   'tanggal' => '22 Apr 2026', 'peserta' => 'Ahmad Dani',    'status' => 'Lunas'],
    ['event' => 'Webinar Blockchain',     'tanggal' => '20 Apr 2026', 'peserta' => 'Ayu Tingting',  'status' => 'Lunas'],
    ['event' => 'Lomba UI/UX Kampus',     'tanggal' => '19 Apr 2026', 'peserta' => 'Gojo Satoru',   'status' => 'Lunas'],
    ['event' => 'Hackathon Mahasiswa',    'tanggal' => '11 Apr 2026', 'peserta' => 'Leif Ashbourn', 'status' => 'Lunas'],
];

$daftarEvent = [
    ['nama' => 'Simposium Nasional AI 2026',    'tanggal' => '25 April 2026', 'lokasi' => 'Gedung Rektorat',     'peserta' => '120/150', 'status' => 'Aktif',   'kategori' => 'Seminar'],
    ['nama' => 'Workshop No-Code App Maker',    'tanggal' => '28 April 2026', 'lokasi' => 'Lab Multimedia D',    'peserta' => '35/50',   'status' => 'Aktif',   'kategori' => 'Workshop'],
    ['nama' => 'Webinar Kewirausahaan Startup', 'tanggal' => '02 Mei 2026',   'lokasi' => 'Online Zoom Meeting', 'peserta' => '250/300', 'status' => 'Draft',   'kategori' => 'Webinar'],
    ['nama' => 'Kompetisi UI/UX Nasional',      'tanggal' => '15 Mei 2026',   'lokasi' => 'Gedung Teater Utama', 'peserta' => '80/100',  'status' => 'Aktif',   'kategori' => 'Kompetisi'],
    ['nama' => 'Coding Camp Mahasiswa 2026',    'tanggal' => '01 Juni 2026',  'lokasi' => 'Aula Gedung C',       'peserta' => '12/40',   'status' => 'Draft',   'kategori' => 'Workshop'],
    ['nama' => 'Pelepasan Alumni & Job Fair',   'tanggal' => '10 Juni 2026',  'lokasi' => 'Plaza Kampus Utama',  'peserta' => '500/500', 'status' => 'Selesai', 'kategori' => 'Seminar'],
];
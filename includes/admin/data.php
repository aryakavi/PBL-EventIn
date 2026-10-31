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

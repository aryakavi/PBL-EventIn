<?php
// Data contoh (nanti bisa diganti query database)

require_once __DIR__ . '/../data.php';

$organizer = [
    'nama'  => 'HMTI',
    'role'  => 'ORGANIZER',
    'inisial' => 'H',
];

$daftarQrEvent = [
    'simposium-ai'     => 'Simposium Nasional AI',
    'workshop-nocode'  => 'Workshop No-Code App',
    'kompetisi-uiux'   => 'Kompetisi UI/UX Nasional',
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

$daftarPembayaran = [
    ['id' => 'TX-100293', 'peserta' => 'Shin Ryujin',    'event' => 'Simposium Nasional AI', 'jumlah' => 'Rp 75.000', 'status' => 'Menunggu',     'metode' => 'Virtual Account Bank Mandiri', 'waktu' => '24 April 2026, 14:32 WIB', 'berkas' => 'mandiri-transfer.png'],
    ['id' => 'TX-100294', 'peserta' => 'Park Jinyoung',  'event' => 'Workshop No-Code App',  'jumlah' => 'Rp 75.000', 'status' => 'Diverifikasi', 'metode' => 'Virtual Account BNI',          'waktu' => '23 April 2026, 09:15 WIB', 'berkas' => 'bni-transfer.png'],
    ['id' => 'TX-100295', 'peserta' => 'Dewi Kurniasari','event' => 'Workshop No-Code App',  'jumlah' => 'Rp 75.000', 'status' => 'Menunggu',     'metode' => 'Virtual Account BCA',          'waktu' => '22 April 2026, 20:47 WIB', 'berkas' => 'bca-transfer.jpg'],
    ['id' => 'TX-100296', 'peserta' => 'Dewi Lestari',   'event' => 'Simposium Nasional AI', 'jumlah' => 'Rp 75.000', 'status' => 'Diverifikasi', 'metode' => 'Virtual Account Bank Mandiri', 'waktu' => '21 April 2026, 11:05 WIB', 'berkas' => 'mandiri-transfer.png'],
    ['id' => 'TX-100297', 'peserta' => 'Ahmad Dani',     'event' => 'Simposium Nasional AI', 'jumlah' => 'Rp 75.000', 'status' => 'Ditolak',      'metode' => 'QRIS',                         'waktu' => '20 April 2026, 16:23 WIB', 'berkas' => 'qris-bukti.jpg'],
];

$presensiTerkini = [
    ['inisial' => 'DA', 'nama' => 'Dani Alamsyah', 'waktu' => '10:45 WIB', 'status' => 'Hadir'],
    ['inisial' => 'SA', 'nama' => 'Siska Amelia',  'waktu' => '10:44 WIB', 'status' => 'Hadir'],
    ['inisial' => 'RG', 'nama' => 'Rony Gunawan',  'waktu' => '10:42 WIB', 'status' => 'Hadir'],
    ['inisial' => 'FA', 'nama' => 'Farah Adiba',   'waktu' => '10:39 WIB', 'status' => 'Hadir'],
    ['inisial' => 'BS', 'nama' => 'Bayu Saputra',  'waktu' => '10:35 WIB', 'status' => 'Hadir'],
];

$kpiQr = [
    ['label' => 'Total Hadir',          'nilai' => '150',   'tren' => '75% tingkat kehadiran',        'arah' => 'up'],
    ['label' => 'Belum Hadir',          'nilai' => '50',    'tren' => '25% sisa kuota hadir',         'arah' => 'down'],
    ['label' => 'Persentase Kehadiran', 'nilai' => '75.0%', 'tren' => 'Standar target minimal terpenuhi', 'arah' => 'up'],
];

$kpiLaporan = [
    ['label' => 'Total Pendaftar',   'nilai' => '3.245',         'tren' => 'Seluruh event semester ini',    'arah' => 'up'],
    ['label' => 'Total Hadir',       'nilai' => '2.890',         'tren' => '89% konversi kehadiran',        'arah' => 'up'],
    ['label' => 'Total Pendapatan',  'nilai' => 'Rp 48.250.000', 'tren' => 'Lunas terverifikasi bank',      'arah' => 'up'],
    ['label' => 'Tingkat Kehadiran', 'nilai' => '89.1%',         'tren' => '+2.4% peningkatan semester lalu', 'arah' => 'up'],
];

$rincianLaporan = [
    ['nama' => 'Shin Ryujin',  'email' => 'ryujin@mahasiswa.ac.id', 'status' => 'Lunas',    'kehadiran' => 'Hadir',       'sertifikat' => 'Sudah Dikirim', 'kategori' => 'Seminar',    'tanggal' => '2026-03-12'],
    ['nama' => 'Siti Rahma',   'email' => 'siti@mahasiswa.ac.id',   'status' => 'Lunas',    'kehadiran' => 'Hadir',       'sertifikat' => 'Sudah Dikirim', 'kategori' => 'Workshop',   'tanggal' => '2026-04-08'],
    ['nama' => 'Rian Pratama', 'email' => 'rian@mahasiswa.ac.id',   'status' => 'Tertunda', 'kehadiran' => 'Tidak Hadir', 'sertifikat' => 'Ditangguhkan',  'kategori' => 'Kompetisi',  'tanggal' => '2026-04-25'],
    ['nama' => 'Dewi Lestari', 'email' => 'dewi@mahasiswa.ac.id',   'status' => 'Lunas',    'kehadiran' => 'Hadir',       'sertifikat' => 'Sudah Dikirim', 'kategori' => 'Seminar',    'tanggal' => '2026-02-19'],
    ['nama' => 'Ahmad Dani',   'email' => 'ahmad@mahasiswa.ac.id',  'status' => 'Ditolak',  'kehadiran' => 'Tidak Hadir', 'sertifikat' => 'Ditangguhkan',  'kategori' => 'Workshop',   'tanggal' => '2026-01-30'],
];
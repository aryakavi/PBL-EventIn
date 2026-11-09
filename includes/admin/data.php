<?php
/**
 * Data dashboard penyelenggara.
 *
 * Semua data diambil langsung dari Supabase (tabel events, tiket, pembayaran,
 * pengguna, kehadiran, kategori_event, ukm, admin_ukm) dalam mode baca-saja.
 * Bila sebuah kolom/relasi belum terisi, tampilkan $emptyText.
 */

require_once __DIR__ . '/../data.php';
require_once __DIR__ . '/../../app/models/AdminRepository.php';

/** Teks pengganti saat data belum tersedia di database. */
$emptyText = 'kosong, masih kurang';

// Koneksi database; pesan error disembunyikan supaya tidak merusak halaman.
if (!isset($pdo)) {
    ob_start();
    require_once __DIR__ . '/../koneksi.php';
    ob_end_clean();
}

$adminRepo = isset($pdo) && $pdo instanceof PDO ? new AdminRepository($pdo) : null;

$adminSession = $_SESSION['user'] ?? null;
$isAdminUser  = is_array($adminSession) && is_admin_role($adminSession['role'] ?? '');
$adminNama    = $isAdminUser ? (string) $adminSession['nama'] : 'Admin Organisasi';

$ukmAdmin = [];
if ($adminRepo !== null && $isAdminUser) {
    $ukmAdmin = $adminRepo->organizer((int) ($adminSession['id_pengguna'] ?? 0));
}

$organizer = [
    'nama'       => $adminNama,
    'role'       => $isAdminUser ? role_label($adminSession['role'] ?? '') : 'ADMIN ORGANISASI',
    'inisial'    => user_initials($adminNama),
    'email'      => $isAdminUser ? (string) ($adminSession['email'] ?? '') : '',
    'nim'        => $isAdminUser ? (string) ($adminSession['nim'] ?? '') : '',
    'no_hp'      => $isAdminUser ? (string) ($adminSession['no_hp'] ?? '') : '',
    'organisasi' => $ukmAdmin['nama_ukm'] ?? $emptyText,
    'id_pengguna' => $isAdminUser ? (int) ($adminSession['id_pengguna'] ?? 0) : 0,
];

$menu = [
    ['label' => 'Dashboard',  'href' => 'index.php',       'key' => 'dashboard'],
    ['label' => 'Event Saya', 'href' => 'event.php',       'key' => 'event'],
    ['label' => 'Pembayaran', 'href' => 'pembayaran.php',  'key' => 'pembayaran'],
    ['label' => 'Qr-Checker', 'href' => 'qr.php',          'key' => 'qr'],
    ['label' => 'Laporan',    'href' => 'laporan.php',     'key' => 'laporan'],
];

$tanggalId = static function (?string $nilai, string $format = 'd M Y'): string {
    if ($nilai === null || $nilai === '' || str_starts_with($nilai, '0000')) {
        return '';
    }

    $time = strtotime($nilai);

    return $time === false ? '' : date($format, $time);
};

$statusPembayaran = static function (?string $status): string {
    return match (strtolower((string) $status)) {
        'lunas', 'diverifikasi', 'sukses' => 'Diverifikasi',
        'menunggu', 'tertunda', 'pending' => 'Menunggu',
        'ditolak', 'gagal', 'batal' => 'Ditolak',
        default => '',
    };
};

/* ---------- Kategori ---------- */
$kategoriEvent = [];
if ($adminRepo !== null) {
    foreach ($adminRepo->kategori() as $k) {
        $kategoriEvent[] = [
            'id'   => (int) $k['id_kategori'],
            'nama' => (string) $k['nama_kategori'],
        ];
    }
}

/* ---------- Event ---------- */
$daftarEvent = [];
$eventRows = $adminRepo !== null ? $adminRepo->events() : [];

foreach ($eventRows as $ev) {
    $mulai   = $ev['tanggal_mulai'] ?? null;
    $selesai = $ev['tanggal_selesai'] ?? null;
    $status  = 'Aktif';

    if ($selesai !== null && $selesai !== '' && strtotime((string) $selesai) < time()) {
        $status = 'Selesai';
    }

    $peserta = (int) ($ev['jumlah_peserta'] ?? 0);
    $kuota   = $ev['kuota'] !== null ? (int) $ev['kuota'] : 0;

    $daftarEvent[] = [
        'id'             => (int) $ev['id_event'],
        'nama'           => (string) ($ev['nama_event'] ?? ''),
        'tanggal'        => $tanggalId($mulai) ?: $emptyText,
        'tanggal_mulai'  => $mulai,
        'tanggal_selesai' => $selesai,
        'lokasi'         => (string) ($ev['lokasi'] ?? ''),
        'peserta'        => $peserta . '/' . ($kuota > 0 ? $kuota : '?'),
        'jumlah_peserta' => $peserta,
        'kuota'          => $kuota,
        'status'         => $status,
        'kategori'       => (string) ($ev['nama_kategori'] ?? ''),
        'id_kategori'    => (int) ($ev['id_kategori'] ?? 0),
        'penyelenggara'  => (string) ($ev['nama_ukm'] ?? ''),
        'singkatan'      => (string) ($ev['singkatan'] ?? ''),
        'pembuat'        => (string) ($ev['pembuat_nama'] ?? ''),
        'pembuat_email'  => (string) ($ev['pembuat_email'] ?? ''),
        'pembuat_hp'     => (string) ($ev['pembuat_hp'] ?? ''),
        'deskripsi'      => (string) ($ev['deskripsi'] ?? ''),
        'harga'          => (float) ($ev['harga'] ?? 0),
        'poster'         => (string) ($ev['poster'] ?? ''),
    ];
}

/* ---------- Pembayaran ---------- */
$daftarPembayaran = [];
$paymentRows = $adminRepo !== null ? $adminRepo->payments() : [];

foreach ($paymentRows as $p) {
    $daftarPembayaran[] = [
        'id'         => (string) ($p['id_pembayaran'] ?? ''),
        'id_tiket'   => (int) ($p['id_tiket'] ?? 0),
        'id_event'   => (int) ($p['id_event'] ?? 0),
        'peserta'    => (string) ($p['peserta'] ?? ''),
        'email'      => (string) ($p['email'] ?? ''),
        'nim'        => (string) ($p['nim'] ?? ''),
        'event'      => (string) ($p['nama_event'] ?? ''),
        'kategori'   => (string) ($p['nama_kategori'] ?? ''),
        'jumlah'     => format_rupiah($p['nominal'] ?? 0),
        'nominal'    => (float) ($p['nominal'] ?? 0),
        'status'     => $statusPembayaran($p['status_bayar'] ?? ''),
        'status_raw' => (string) ($p['status_bayar'] ?? ''),
        'kode_qr'    => (string) ($p['kode_qr'] ?? ''),
        'metode'     => (string) ($p['metode'] ?? ''),
        'waktu'      => $tanggalId($p['waktu_unggah'] ?? null, 'd M Y, H:i') . ' WIB',
        'berkas'     => (string) ($p['bukti_pembayaran'] ?? ''),
        'daftar'     => $tanggalId($p['tanggal_daftar'] ?? null),
        'check_in'   => $tanggalId($p['waktu_checkin'] ?? null, 'd M Y, H:i'),
        'sertifikat' => (string) ($p['status_sertifikat'] ?? ''),
    ];
}

/* ---------- Statistik dashboard ---------- */
$total = $adminRepo !== null
    ? $adminRepo->totals()
    : ['total_event' => 0, 'event_aktif' => 0, 'total_tiket' => 0, 'total_peserta' => 0,
       'pendapatan' => 0.0, 'total_hadir' => 0, 'belum_hadir' => 0, 'persen_hadir' => 0.0];

$kpi = [
    [
        'label' => 'Total Event',
        'nilai' => (string) $total['total_event'],
        'tren'  => $total['event_aktif'] . ' event masih berjalan',
        'arah'  => 'up',
    ],
    [
        'label' => 'Total Peserta',
        'nilai' => (string) $total['total_peserta'],
        'tren'  => $total['total_tiket'] . ' tiket terdaftar',
        'arah'  => 'up',
    ],
    [
        'label' => 'Pendapatan Bersih',
        'nilai' => format_rupiah($total['pendapatan']),
        'tren'  => 'dari tiket berstatus Lunas',
        'arah'  => 'up',
    ],
    [
        'label' => 'Event Aktif',
        'nilai' => (string) $total['event_aktif'],
        'tren'  => $total['belum_hadir'] . ' peserta belum check-in',
        'arah'  => $total['belum_hadir'] > 0 ? 'down' : 'up',
    ],
];

/* ---------- Aktivitas pendaftaran terbaru ---------- */
$aktivitas = [];
$activityRows = $adminRepo !== null ? $adminRepo->activity(5) : [];

foreach ($activityRows as $a) {
    $aktivitas[] = [
        'id'      => (string) ($a['id_pembayaran'] ?? ''),
        'id_tiket' => (int) ($a['id_tiket'] ?? 0),
        'event'   => (string) ($a['nama_event'] ?? ''),
        'tanggal' => $tanggalId($a['tanggal_daftar'] ?? null),
        'peserta' => (string) ($a['peserta'] ?? ''),
        'status'  => $statusPembayaran($a['status_bayar'] ?? ''),
    ];
}

/* ---------- Presensi & statistik QR ---------- */
$presensiTerkini = [];
$attendanceRows = $adminRepo !== null ? $adminRepo->attendance(5) : [];

foreach ($attendanceRows as $h) {
    $nama = (string) ($h['peserta'] ?? '');
    $presensiTerkini[] = [
        'inisial' => $nama !== '' ? user_initials($nama) : '-',
        'nama'    => $nama,
        'event'   => (string) ($h['nama_event'] ?? ''),
        'waktu'   => $tanggalId($h['waktu_checkin'] ?? null, 'H:i') . ' WIB',
        'status'  => 'Hadir',
    ];
}

$kpiQr = [
    [
        'label' => 'Total Hadir',
        'nilai' => (string) $total['total_hadir'],
        'tren'  => 'peserta sudah check-in',
        'arah'  => 'up',
    ],
    [
        'label' => 'Belum Hadir',
        'nilai' => (string) $total['belum_hadir'],
        'tren'  => 'peserta belum check-in',
        'arah'  => 'down',
    ],
    [
        'label' => 'Persentase Kehadiran',
        'nilai' => number_format($total['persen_hadir'], 1, ',', '.') . '%',
        'tren'  => 'dari ' . $total['total_tiket'] . ' tiket terdaftar',
        'arah'  => $total['persen_hadir'] >= 50 ? 'up' : 'down',
    ],
];

/* ---------- Laporan ---------- */
$rincianLaporan = [];
$reportRows = $adminRepo !== null ? $adminRepo->reportRows() : [];

foreach ($reportRows as $r) {
    $status = match (strtolower((string) ($r['status_bayar'] ?? ''))) {
        'lunas' => 'Lunas',
        'menunggu' => 'Tertunda',
        default => 'Ditolak',
    };

    $hadir = !empty($r['waktu_checkin']);

    $rincianLaporan[] = [
        'nama'       => (string) ($r['nama'] ?? ''),
        'email'      => (string) ($r['email'] ?? ''),
        'status'     => $status,
        'kehadiran'  => $hadir ? 'Hadir' : 'Tidak Hadir',
        'sertifikat' => (string) ($r['status_sertifikat'] ?? ''),
        'kategori'   => (string) ($r['nama_kategori'] ?? ''),
        'tanggal'    => $tanggalId($r['tanggal_daftar'] ?? null, 'Y-m-d'),
    ];
}

$kpiLaporan = [
    [
        'label' => 'Total Pendaftar',
        'nilai' => (string) $total['total_tiket'],
        'tren'  => 'seluruh tiket terdaftar',
        'arah'  => 'up',
    ],
    [
        'label' => 'Total Hadir',
        'nilai' => (string) $total['total_hadir'],
        'tren'  => 'peserta hadir di lokasi',
        'arah'  => 'up',
    ],
    [
        'label' => 'Total Pendapatan',
        'nilai' => format_rupiah($total['pendapatan']),
        'tren'  => 'dari tiket berstatus Lunas',
        'arah'  => 'up',
    ],
    [
        'label' => 'Tingkat Kehadiran',
        'nilai' => number_format($total['persen_hadir'], 1, ',', '.') . '%',
        'tren'  => 'dihitung dari tiket & kehadiran',
        'arah'  => $total['persen_hadir'] >= 50 ? 'up' : 'down',
    ],
];
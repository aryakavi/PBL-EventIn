<?php
/**
 * Export rincian pendaftar ke berkas CSV.
 * Membaca data langsung dari Supabase lewat includes/admin/data.php (baca-saja).
 */
require __DIR__ . '/../../../includes/admin/data.php';

$tanggal  = $_GET['tanggal'] ?? '';
$kategori = $_GET['kategori'] ?? '';

$baris = array_values(array_filter($rincianLaporan, static function ($row) use ($tanggal, $kategori) {
    if ($tanggal !== '' && $row['tanggal'] !== $tanggal) {
        return false;
    }
    if ($kategori !== '' && $row['kategori'] !== $kategori) {
        return false;
    }
    return true;
}));

$namaBerkas = 'laporan-eventin-' . date('Ymd-His') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $namaBerkas . '"');

$out = fopen('php://output', 'w');

fputcsv($out, ['Nama', 'Email Kampus', 'Status Pembayaran', 'Kehadiran', 'Sertifikat', 'Kategori', 'Tanggal']);

foreach ($baris as $row) {
    fputcsv($out, [
        $row['nama'],
        $row['email'],
        $row['status'],
        $row['kehadiran'],
        $row['sertifikat'],
        $row['kategori'],
        $row['tanggal'],
    ]);
}

fclose($out);
exit;

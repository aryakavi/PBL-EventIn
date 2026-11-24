<?php
require_once __DIR__.'/../models/Tiket.php';
require_once __DIR__.'/../models/Kehadiran.php';

class AbsensiController
{
    private Tiket $tiketModel;
    private Kehadiran $kehadiranModel;


    public function __construct(PDO $pdo)
    {
        $this->tiketModel = new Tiket($pdo);
        $this->kehadiranModel = new Kehadiran($pdo);
    }

    public function absensi($kodeQr, $idEvent)
    {
        $idPetugas = $_SESSION['user']['id_admin_ukm'] ?? null;

        if (!$idPetugas) {
            return [
                'success' => false,
                'message' => 'Sesi petugas tidak ditemukan.'
            ];
        }

        // Cari tiket berdasarkan kode QR.
        $tiket = $this->tiketModel->getByKodeQr($kodeQr);

        if (!$tiket || $tiket['id_event'] != $idEvent) {
            return [
                'success' => false,
                'message' => 'Tiket tidak ditemukan untuk event ini.'
            ];
        }

        // Periksa success pembayaran.
        if ($tiket['status_bayar'] !== 'Lunas') {
            return [
                'success' => false,
                'message' => 'Tiket ini belum melunasi pembayaran.'
            ];
        }

        // Periksa apakah tiket sudah pernah check-in.
        if ($tiket['waktu_checkin'] !== null) {
            return [
                'success' => false,
                'message' => 'Tiket ini sudah melakukan check-in.'
            ];
        }

        try {

            // Insert data ke tabel kehadiran.
            $this->kehadiranModel->insert(
                $tiket['id_tiket'],
                $idPetugas
            );
            return [
                'success' => true,
                'message' => 'Absensi berhasil dicatat.'
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencatat absensi.'
            ];
        }
    }
}

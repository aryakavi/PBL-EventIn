<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Tiket.php';
require_once __DIR__ . '/../models/SiteRepository.php';

/**
 * Alur pendaftaran peserta ke sebuah event.
 * Membuat satu baris tiket, lalu halaman pembayaran mengunggah bukti.
 */
class PendaftaranController
{
    private PDO $db;
    private Tiket $tiketModel;
    private SiteRepository $siteRepo;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
        $this->tiketModel = new Tiket($pdo);
        $this->siteRepo = new SiteRepository($pdo);
    }

    /**
     * @return array{success: bool, message: string, id_tiket?: int, status_bayar?: string}
     */
    public function daftar(int $idPengguna, int $idEvent): array
    {
        if ($idPengguna <= 0 || $idEvent <= 0) {
            return ['success' => false, 'message' => 'Data pendaftaran tidak lengkap.'];
        }

        $acara = $this->siteRepo->event($idEvent);

        if (empty($acara) || (int) ($acara['id_event'] ?? 0) !== $idEvent) {
            return ['success' => false, 'message' => 'Event tidak ditemukan.'];
        }

        $selesai = !empty($acara['tanggal_selesai']) ? strtotime((string) $acara['tanggal_selesai']) : false;

        if ($selesai !== false && $selesai < time()) {
            return ['success' => false, 'message' => 'Event sudah selesai. Pendaftaran ditutup.'];
        }

        $existing = $this->tiketModel->findByUserAndEvent($idPengguna, $idEvent);

        if ($existing !== false) {
            return [
                'success'      => true,
                'message'      => 'Anda sudah terdaftar di event ini.',
                'id_tiket'     => (int) $existing['id_tiket'],
                'status_bayar' => (string) $existing['status_bayar'],
            ];
        }

        $kuota = (int) ($acara['kuota'] ?? 0);

        if ($kuota > 0) {
            $stmt = $this->db->prepare('SELECT count(*) FROM tiket WHERE id_event = :id_event');
            $stmt->execute(['id_event' => $idEvent]);

            if ((int) $stmt->fetchColumn() >= $kuota) {
                return ['success' => false, 'message' => 'Kuota event sudah penuh.'];
            }
        }

        $nominal = (float) ($acara['harga'] ?? 0);
        $statusAwal = $nominal > 0 ? 'Belum Bayar' : 'Lunas';
        $kodeQr = 'QR-' . strtoupper(bin2hex(random_bytes(6)));

        try {
            $idTiket = $this->tiketModel->create(
                $idPengguna,
                $idEvent,
                $nominal,
                $kodeQr,
                $statusAwal
            );
        } catch (PDOException $e) {
            error_log('EventIn daftar event error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Pendaftaran gagal disimpan. Coba lagi atau hubungi admin.',
            ];
        }

        return [
            'success'      => true,
            'message'      => $nominal > 0
                ? 'Pendaftaran berhasil. Silakan unggah bukti pembayaran.'
                : 'Pendaftaran berhasil. Event ini gratis.',
            'id_tiket'     => $idTiket,
            'status_bayar' => $statusAwal,
        ];
    }
}
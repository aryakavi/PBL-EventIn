<?php
require_once __DIR__ . '/../models/Tiket.php';
require_once __DIR__ . '/../models/Kehadiran.php';
class PendaftaranController
{
    private Tiket $tiketModel;
    private Event $eventModel;
    private Kehadiran $kehadiranModel;


    public function __construct(PDO $pdo)
    {
        $this->tiketModel = new Tiket($pdo);
        $this->eventModel = new Event($pdo);
        $this->kehadiranModel = new Kehadiran($pdo);
    }

    public function pesanTiket(int $id_event, int $id_pengguna, string $status)
    {

        if (!in_array($status, ['Lunas', 'Menunggu'], true)) {
            return [
                "success" => false,
                "message" => "Status pembayaran tidak valid"
            ];
        }

        $terdaftar = $this->tiketModel->countJumlahPendaftar($id_event);
        $event = $this->eventModel->getEventById($id_event);
        $kuota = (int)$event['kuota'];
        if ($terdaftar >= $kuota) {
            return [
                "success" => false,
                "message" => "Kuota sudah penuh"
            ];
        }

        $id_tiket = $this->tiketModel->buatTiket(
            $id_event,
            $id_pengguna,
            $status
        );

        return [
            "success" => true,
            "status" => $status,
            "message" => "Pendaftaran berhasil",
            "id_tiket" => $id_tiket
        ];
    }

    public function fetchHalamanTiket(int $id_pengguna)
    {
        return $this->tiketModel->getTiketByUserID($id_pengguna);
    }

    public function fetchHalamanQr($id_ukm)
    {
        $daftarQrEvent = $this->eventModel->getEventByIdUkm($id_ukm);
        $presensi = $this->kehadiranModel->getLatestPerUkm($id_ukm);
        $presensiTerkini = [];
        foreach ($presensi as $p) {
            $kata = preg_split('/\s+/', trim($p['nama']));
            $inisial = '';
            foreach (array_slice($kata, 0, 2) as $k) {
                $inisial .= mb_substr($k, 0, 1);
            }
            $jam = date('H:i', strtotime($p['waktu_checkin']));
            $presensiTerkini[] = [
                'inisial' => $inisial,
                'nama' => $p['nama'],
                'waktu' => $jam,
                'status' => 'Hadir'
            ];
        }
        require __DIR__ . '/../views/admin/qr.php';
    }
}

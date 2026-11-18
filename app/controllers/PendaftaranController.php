<?php
require_once __DIR__ . '/../models/Tiket.php';
class PendaftaranController
{
    private Tiket $tiketModel;
    private Event $eventModel;


    public function __construct(PDO $pdo)
    {
        $this->tiketModel = new Tiket($pdo);
        $this->eventModel = new Event($pdo);
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

    public function fetchHalamanTiket(int $id_pengguna){
        return $this->tiketModel->getTiketByUserID($id_pengguna);
    }
}

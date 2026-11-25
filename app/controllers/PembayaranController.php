<?php
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/RekeningUkm.php';
class Pembayaran
{
    private Event $eventModel;
    private RekeningUkm $rekeningModel;
    public function __construct(PDO $pdo)
    {
        $this->eventModel = new Event($pdo);
        $this->rekeningModel = new RekeningUkm($pdo);
    }
    public function fetchHalamanPembayaran(int $id_event)
    {
        $event = $this->eventModel->getEventById($id_event);

        $ringkasanPesanan = [
            'judul'    => $event['nama_event'],
            'tiket'    => '1x Tiket Akses Mandiri Utama',
            'subtotal' => $event['harga'],
            'total'    => $event['harga'],
        ];

        $rekInfo = $this->rekeningModel->getRekeningPerUkm($event['id_ukm']);

        return [
            'ringkasanPesanan' => $ringkasanPesanan,
            'rekInfo' => $rekInfo
        ];
    }
}

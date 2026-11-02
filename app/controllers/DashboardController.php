<?php
require_once __DIR__.'/../models/Event.php';
require_once __DIR__.'/../models/Ukm.php';
require_once __DIR__.'/../models/User.php';
class DashboardController
{
    private Ukm $ukmModel;
    private User $pesertaModel;
    private Event $eventModel;

    public function __construct(PDO $pdo)
    {
        $this->ukmModel= new Ukm($pdo);
        $this->pesertaModel= new User($pdo); 
        $this->eventModel= new Event($pdo);
    }

    public function index(): array|null
    {
        $totalPeserta = $this->pesertaModel->countPeserta();
        $totalEvent = $this->eventModel->countEvent();
        $totalUKM = $this->ukmModel->countUKM();

        return [
            [$totalPeserta, 'Event Terdaftar'],
            [$totalEvent, 'Peserta Aktif'],
            [$totalUKM, 'Organisasi Kampus'],
        ];
    }

    public function recentEvent():array|null
    {
        return $this->eventModel->mostRecentEvent();
    }
}

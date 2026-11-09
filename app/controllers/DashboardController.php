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
        $totalPeserta = $this->pesertaModel->countPesertaMhs();
        $totalEvent = $this->eventModel->countEvent();
        $totalUKM = $this->ukmModel->countUKM();

        return [
            [$totalPeserta, 'Peserta Aktif'],
            [$totalEvent, 'Event Terdaftar'],
            [$totalUKM, 'Organisasi Kampus'],
        ];
    }

    public function recentEvent():array|null
    {
        return $this->eventModel->mostRecentEvent();
    }

    public function getUkm():array|null
    {
        return $this->ukmModel->getUkmEvent();
    }
}

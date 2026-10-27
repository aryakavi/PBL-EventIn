<?php
require __DIR__.'/../models/DashboardModel.php';
class DashboardController
{
    private DashboardModel $dashboardModel;

    public function __construct(PDO $pdo)
    {
        $this->dashboardModel = new DashboardModel($pdo);
    }

    public function index(): array|null
    {
        $totalPeserta = $this->dashboardModel->countPeserta();
        $totalEvent = $this->dashboardModel->countEvent();
        $totalUKM = $this->dashboardModel->countUKM();

        return [
            [$totalPeserta, 'Event Terdaftar'],
            [$totalEvent, 'Peserta Aktif'],
            [$totalUKM, 'Organisasi Kampus'],
        ];
    }
}

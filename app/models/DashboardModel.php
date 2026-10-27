<?php

class DashboardModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function countPeserta(): int
    {
        $sql = "SELECT COUNT(*) FROM pengguna WHERE role = 'pengguna'";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function countEvent(): int
    {
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function countUKM(): int 
    {
        $sql = "SELECT COUNT(*) FROM ukm";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function mostRecentEvent(): array|null
    {
        $sql="SELECT e.nama_event, e.tanggal_mulai, 
         FROM events ORDER BY id_event LIMIT 1";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }
}

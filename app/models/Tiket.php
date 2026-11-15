<?php
class Tiket
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function countSisaKuota(int $id_event)
    {
        $sql = "SELECT COUNT(*) AS total
            FROM tiket
            WHERE status_bayar = 'Lunas'
            AND id_event = :id_event";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_event' => $id_event]);

        return (int) $stmt->fetchColumn();
    }
}

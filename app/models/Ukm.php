<?php
class Ukm
{
    private PDO $pdo;
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function countUKM(): int
    {
        $sql = "SELECT COUNT(*) FROM ukm";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }
}

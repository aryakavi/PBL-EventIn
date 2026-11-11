<?php
class Ukm
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function countUKM(): int
    {
        $sql = "SELECT COUNT(*) FROM ukm";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function getUkmEvent()
    {
        $sql = "SELECT u.nama_ukm, u.id_ukm, u.deskripsi, u.singkatan, u.logo,
        COUNT(e.id_event) as jumlah_event FROM ukm u 
        LEFT JOIN admin_ukm ak on u.id_ukm = ak.id_ukm
        LEFT JOIN events e on ak.id_admin_ukm = e.id_pembuat
        GROUP BY u.id_ukm";

        $result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getUkms(): array
    {
        $sql = "SELECT id_ukm, nama_ukm, singkatan, deskripsi
                FROM ukm
                ORDER BY nama_ukm ASC";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

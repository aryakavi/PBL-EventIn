<?php

class Kehadiran
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert($idTiket, $idPetugas)
    {
        $this->pdo->beginTransaction();
        $sql = "insert into kehadiran (
                id_tiket,
                id_petugas,
                waktu_checkin
            ) values (
                :id_tiket,
                :id_petugas,
                now()
            )";

        $stmt = $this->pdo->prepare($sql);
        $this->pdo->commit();
        return $stmt->execute([
            ':id_tiket' => $idTiket,
            ':id_petugas' => $idPetugas
        ]);
    }
    
}

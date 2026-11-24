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

    public function getLatestPerUkm($id_ukm)
    {
        $sql="SELECT k.*, p.nama
        FROM kehadiran k
        INNER JOIN admin_ukm ak on k.id_petugas = ak.id_admin_ukm
        INNER JOIN ukm u on ak.id_ukm = u.id_ukm
        INNER JOIN tiket t on k.id_tiket = t.id_tiket
        INNER JOIN pengguna p on t.id_pengguna = p.id_pengguna
        WHERE u.id_ukm = :id_ukm";

        $stmt=$this->pdo->prepare($sql);
        $stmt->execute([
            'id_ukm'=>$id_ukm
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

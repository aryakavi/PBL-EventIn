<?php
class RekeningUkm
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRekeningPerUkm(int $id_ukm){
        $sql="SELECT * FROM rekening_ukm
        WHERE id_ukm = :id_ukm";

        $stmt=$this->pdo->prepare($sql);
        $stmt->execute(['id_ukm'=>$id_ukm]);
        return $stmt->fetch();
    }
}

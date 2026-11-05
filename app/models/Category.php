<?php 
class Category
{
    private PDO $pdo;
    public function __construct(PDO $pdo)
    {
        $this->pdo=$pdo;
    }

    public function getAll():array|null
    {
        $sql="SELECT * FROM kategori_event;";
        $result= $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}

?>
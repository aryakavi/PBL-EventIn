
<?php

class Event
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT
                    e.id_event,
                    e.nama_event,
                    e.tanggal_mulai,
                    e.lokasi,
                    e.harga,
                    e.poster,
                    k.nama_kategori
                FROM events e
                JOIN kategori_event k
                    ON e.id_kategori = k.id_kategori
                ORDER BY e.tanggal_mulai ASC";

        return $this->pdo->query($sql)->fetchAll();
    }
}

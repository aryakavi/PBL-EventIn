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

    public function countEvent(): int
    {
        return (int) $this->pdo->query('SELECT count(*) FROM events')->fetchColumn();
    }

    public function mostRecentEvent(): array|false
    {
        return $this->pdo->query(
            'SELECT id_event, nama_event, tanggal_mulai, lokasi
             FROM events
             ORDER BY tanggal_mulai DESC
             LIMIT 1'
        )->fetch();
    }
}

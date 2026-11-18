
<?php
class Tiket
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function countJumlahPendaftar(int $id_event)
    {
        $sql = "SELECT COUNT(*) AS total
            FROM tiket
            WHERE status_bayar = 'Lunas'
            AND id_event = :id_event";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_event' => $id_event]);

        return (int) $stmt->fetchColumn();
    }

    public function buatTiket(int $id_event, int $id_pengguna, string $status): int
    {
        do {
            $kode_qr = strtoupper(bin2hex(random_bytes(4)));

            $sql = "SELECT COUNT(*) FROM tiket WHERE kode_qr = :kode_qr";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['kode_qr' => $kode_qr]);

            $exists = $stmt->fetchColumn() > 0;
        } while ($exists);

        $sql = "INSERT INTO tiket (
            id_event,
            id_pengguna,
            kode_qr,
            status_bayar,
            tanggal_daftar
        ) VALUES (
            :id_event,
            :id_pengguna,
            :kode_qr,
            :status_bayar,
            CURRENT_TIMESTAMP
        )
        RETURNING id_tiket";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'id_event' => $id_event,
            'id_pengguna' => $id_pengguna,
            'kode_qr' => $kode_qr,
            'status_bayar' => $status
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function getTiketByUserID(int $id_pengguna)
    {
        $sql = "SELECT * FROM tiket t
        INNER JOIN pengguna p on t.id_pengguna= p.id_pengguna
        INNER JOIN events e ON t.id_event = e.id_event
        INNER JOIN kategori_event k ON e.id_kategori = k.id_kategori
        WHERE t.id_pengguna = :id_pengguna";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_pengguna' => $id_pengguna
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

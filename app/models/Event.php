
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
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function mostRecentEvent(): array|null
    {
        $sql = "SELECT e.nama_event, e.tanggal_mulai,
        e.tanggal_mulai, e.lokasi,e.deskripsi, u.singkatan
        FROM events e
        JOIN admin_ukm ak ON e.id_pembuat = ak.id_admin_ukm
        JOIN ukm u ON ak.id_ukm = u.id_ukm
        ORDER BY id_event DESC LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $hari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        $tanggal = new DateTime($result['tanggal_mulai']);
        $namaHari = $hari[(int) $tanggal->format('w')];
        $result['hari'] = $namaHari;
        return $result;
    }
}

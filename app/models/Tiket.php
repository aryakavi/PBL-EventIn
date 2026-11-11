<?php

/**
 * Model tiket peserta.
 * Satu baris tiket = satu pendaftaran peserta pada sebuah event.
 */
class Tiket
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Potongan kondisi SQL untuk tiap tab status tiket.
     *
     * aktif          : pembayaran lunas dan event belum selesai.
     * tidak-tersedia : pembayaran belum lunas, dibatalkan tidak termasuk,
     *                  atau event sudah selesai.
     * dibatalkan     : status pembayaran diawali kata "batal".
     */
    private function kondisiStatus(string $status): string
    {
        return match ($status) {
            'dibatalkan' => "AND t.status_bayar ILIKE 'batal%'",
            'tidak-tersedia' => "AND t.status_bayar NOT ILIKE 'batal%'
                                 AND (t.status_bayar NOT ILIKE 'lunas%' OR e.tanggal_selesai < NOW())",
            default => "AND t.status_bayar ILIKE 'lunas%'
                        AND e.tanggal_selesai >= NOW()",
        };
    }

    /**
     * Ambil daftar tiket milik satu peserta.
     */
    public function getByPengguna(
        int $idPengguna,
        string $status = 'aktif',
        string $keyword = '',
        string $kategori = ''
    ): array {
        $sql = "SELECT
                    t.id_tiket,
                    t.kode_qr,
                    t.nominal,
                    t.status_bayar,
                    t.status_sertifikat,
                    t.url_sertifikat,
                    t.tanggal_daftar,
                    t.check_in_at,
                    e.id_event,
                    e.nama_event,
                    e.deskripsi,
                    e.tanggal_mulai,
                    e.tanggal_selesai,
                    e.lokasi,
                    e.harga,
                    e.poster,
                    k.nama_kategori,
                    u.singkatan,
                    u.nama_ukm,
                    (
                        SELECT p.metode
                        FROM pembayaran p
                        WHERE p.id_tiket = t.id_tiket
                        ORDER BY p.id_pembayaran DESC
                        LIMIT 1
                    ) AS metode
                FROM tiket t
                JOIN events e ON e.id_event = t.id_event
                JOIN kategori_event k ON k.id_kategori = e.id_kategori
                LEFT JOIN admin_ukm ak ON ak.id_admin_ukm = e.id_pembuat
                LEFT JOIN ukm u ON u.id_ukm = ak.id_ukm
                WHERE t.id_pengguna = :id_pengguna
                " . $this->kondisiStatus($status);

        $params = ['id_pengguna' => $idPengguna];

        if ($keyword !== '') {
            $sql .= " AND e.nama_event ILIKE :keyword";
            $params['keyword'] = '%' . $keyword . '%';
        }

        if ($kategori !== '') {
            $sql .= " AND k.nama_kategori = :kategori";
            $params['kategori'] = $kategori;
        }

        $sql .= " ORDER BY e.tanggal_mulai ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Jumlah tiket per tab status.
     *
     * @return array<string, int>
     */
    public function countByStatus(int $idPengguna): array
    {
        $jumlah = [];

        foreach (['aktif', 'tidak-tersedia', 'dibatalkan'] as $status) {
            $sql = "SELECT COUNT(*)
                    FROM tiket t
                    JOIN events e ON e.id_event = t.id_event
                    WHERE t.id_pengguna = :id_pengguna
                    " . $this->kondisiStatus($status);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id_pengguna' => $idPengguna]);
            $jumlah[$status] = (int) $stmt->fetchColumn();
        }

        return $jumlah;
    }

    /**
     * Kategori event yang pernah diikuti peserta (untuk filter).
     *
     * @return array<int, string>
     */
    public function kategoriPengguna(int $idPengguna): array
    {
        $sql = "SELECT DISTINCT k.nama_kategori
                FROM tiket t
                JOIN events e ON e.id_event = t.id_event
                JOIN kategori_event k ON k.id_kategori = e.id_kategori
                WHERE t.id_pengguna = :id_pengguna
                ORDER BY k.nama_kategori ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_pengguna' => $idPengguna]);

        return array_column($stmt->fetchAll(), 'nama_kategori');
    }

    public function findByUserAndEvent(int $idPengguna, int $idEvent): array|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT id_tiket, id_event, id_pengguna, kode_qr, nominal, status_bayar
             FROM tiket
             WHERE id_pengguna = :id_pengguna AND id_event = :id_event
             ORDER BY id_tiket DESC
             LIMIT 1'
        );

        $stmt->execute([
            'id_pengguna' => $idPengguna,
            'id_event'    => $idEvent,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Satu tiket milik peserta, lengkap dengan data event.
     */
    public function findForUser(int $idTiket, int $idPengguna): array|false
    {
        $sql = 'SELECT t.id_tiket, t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat,
                       t.tanggal_daftar, t.check_in_at,
                       e.id_event, e.nama_event, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai,
                       e.lokasi, e.harga, e.poster,
                       k.nama_kategori, u.nama_ukm, u.singkatan
                FROM tiket t
                JOIN events e ON e.id_event = t.id_event
                LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
                LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
                LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
                WHERE t.id_tiket = :id_tiket AND t.id_pengguna = :id_pengguna
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_tiket'    => $idTiket,
            'id_pengguna' => $idPengguna,
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Buat tiket baru. Status awal Belum Bayar untuk event berbayar,
     * atau Lunas untuk event gratis.
     */
    public function create(
        int $idPengguna,
        int $idEvent,
        float $nominal,
        string $kodeQr,
        string $statusBayar
    ): int {
        $sql = 'INSERT INTO tiket
                    (id_event, id_pengguna, kode_qr, nominal, status_bayar,
                     status_sertifikat, tanggal_daftar)
                VALUES
                    (:id_event, :id_pengguna, :kode_qr, :nominal, :status_bayar,
                     :status_sertifikat, NOW())
                RETURNING id_tiket';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id_event'           => $idEvent,
            'id_pengguna'        => $idPengguna,
            'kode_qr'            => $kodeQr,
            'nominal'            => $nominal,
            'status_bayar'       => $statusBayar,
            'status_sertifikat'  => 'Menunggu',
        ]);

        return (int) $stmt->fetchColumn();
    }
}

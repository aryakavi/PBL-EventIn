<?php

/**
 * Data publik situs (landing page, halaman auth, halaman peserta).
 *
 * Semua method hanya menjalankan SELECT supaya data Supabase tetap utuh.
 */
class SiteRepository
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /** @return array<int, array<string, mixed>> */
    private function rows(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** @return array<string, mixed> */
    private function row(string $sql, array $params = []): array
    {
        $rows = $this->rows($sql, $params);

        return $rows[0] ?? [];
    }

    /**
     * Angka ringkas untuk hero landing page dan halaman autentikasi.
     *
     * @return array{event: int, peserta: int, organisasi: int}
     */
    public function totals(): array
    {
        $row = $this->row(
            "SELECT
                (SELECT count(*) FROM events) AS total_event,
                (SELECT count(*) FROM ukm) AS total_organisasi,
                (SELECT count(DISTINCT id_pengguna) FROM tiket WHERE id_pengguna IS NOT NULL) AS total_peserta"
        );

        return [
            'event'       => (int) ($row['total_event'] ?? 0),
            'peserta'     => (int) ($row['total_peserta'] ?? 0),
            'organisasi'  => (int) ($row['total_organisasi'] ?? 0),
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function kategori(): array
    {
        return $this->rows(
            'SELECT id_kategori, nama_kategori
             FROM kategori_event
             ORDER BY nama_kategori ASC'
        );
    }

    /**
     * UKM beserta jumlah event yang pernah dibuat.
     *
     * @return array<int, array<string, mixed>>
     */
    public function organisasi(): array
    {
        return $this->rows(
            'SELECT u.id_ukm, u.nama_ukm, u.singkatan,
                    (SELECT count(*)
                     FROM admin_ukm a
                     JOIN events e ON e.id_pembuat = a.id_admin_ukm
                     WHERE a.id_ukm = u.id_ukm) AS jumlah_event
             FROM ukm u
             ORDER BY u.nama_ukm ASC'
        );
    }

    /**
     * Event dengan pendaftar terbanyak untuk bagian "Sorotan Minggu Ini".
     *
     * @return array<string, mixed>
     */
    public function sorotan(): array
    {
        return $this->row(
            'SELECT e.id_event, e.nama_event, e.deskripsi, e.tanggal_mulai, e.lokasi, e.harga,
                    k.nama_kategori, u.nama_ukm, u.singkatan,
                    p.nama AS pembuat_nama, p.email AS pembuat_email, p.no_hp AS pembuat_hp,
                    (SELECT count(*) FROM tiket t WHERE t.id_event = e.id_event) AS jumlah_peserta
             FROM events e
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN pengguna p ON p.id_pengguna = a.id_pengguna
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             ORDER BY jumlah_peserta DESC, e.tanggal_mulai ASC
             LIMIT 1'
        );
    }

    /** @return array<string, mixed> */
    public function event(int $id): array
    {
        return $this->row(
            'SELECT e.id_event, e.nama_event, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai,
                    e.lokasi, e.kuota, e.harga, e.poster,
                    k.nama_kategori, u.nama_ukm, u.singkatan,
                    p.nama AS pembuat_nama, p.email AS pembuat_email, p.no_hp AS pembuat_hp,
                    (SELECT count(*) FROM tiket t WHERE t.id_event = e.id_event) AS jumlah_peserta
             FROM events e
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN pengguna p ON p.id_pengguna = a.id_pengguna
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             WHERE e.id_event = :id',
            ['id' => $id]
        );
    }

    /** @return array<string, mixed> */
    public function eventBerikutnya(): array
    {
        return $this->row(
            'SELECT e.id_event, e.nama_event, e.deskripsi, e.tanggal_mulai, e.tanggal_selesai,
                    e.lokasi, e.kuota, e.harga, e.poster,
                    k.nama_kategori, u.nama_ukm, u.singkatan,
                    (SELECT count(*) FROM tiket t WHERE t.id_event = e.id_event) AS jumlah_peserta
             FROM events e
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             WHERE e.tanggal_selesai >= CURRENT_TIMESTAMP
             ORDER BY e.tanggal_mulai ASC
             LIMIT 1'
        );
    }

    /** @return array<string, mixed> */
    public function pengguna(int $id): array
    {
        return $this->row(
            'SELECT id_pengguna, nama, email, no_hp, nim, role
             FROM pengguna
             WHERE id_pengguna = :id',
            ['id' => $id]
        );
    }

    /**
     * Pembayaran milik satu peserta. Tanpa $idPembayaran, ambil yang terbaru.
     *
     * @return array<string, mixed>
     */
    public function pembayaranPeserta(int $idPengguna, int $idPembayaran = 0): array
    {
        $sql = 'SELECT pb.id_pembayaran, pb.bukti_pembayaran, pb.metode, pb.waktu_unggah,
                       t.id_tiket, t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat, t.tanggal_daftar,
                       e.id_event, e.nama_event, e.tanggal_mulai, e.tanggal_selesai, e.lokasi, e.harga,
                       k.nama_kategori
                FROM pembayaran pb
                JOIN tiket t ON t.id_tiket = pb.id_tiket
                JOIN events e ON e.id_event = t.id_event
                LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
                WHERE t.id_pengguna = :id_pengguna';

        $params = ['id_pengguna' => $idPengguna];

        if ($idPembayaran > 0) {
            $sql .= ' AND pb.id_pembayaran = :id_pembayaran';
            $params['id_pembayaran'] = $idPembayaran;
        }

        $sql .= ' ORDER BY pb.waktu_unggah DESC LIMIT 1';

        return $this->row($sql, $params);
    }
}
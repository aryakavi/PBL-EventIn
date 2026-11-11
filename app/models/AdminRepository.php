<?php
/**
 * Akses data dashboard penyelenggara ke Supabase (mode baca-saja).
 *
 * Semua method di sini hanya menjalankan SELECT. Tidak ada INSERT,
 * UPDATE, atau DELETE supaya data Supabase tetap aman.
 */
class AdminRepository
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

    public function organizer(int $idPengguna): array
    {
        return $this->row(
            'SELECT a.id_admin_ukm, a.id_ukm, u.nama_ukm, u.singkatan, u.deskripsi
             FROM admin_ukm a
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             WHERE a.id_pengguna = :id
             LIMIT 1',
            ['id' => $idPengguna]
        );
    }

    public function kategori(): array
    {
        return $this->rows(
            'SELECT id_kategori, nama_kategori
             FROM kategori_event
             ORDER BY nama_kategori ASC'
        );
    }

    public function events(): array
    {
        return $this->rows(
            'SELECT e.id_event, e.id_pembuat, e.id_kategori, e.nama_event, e.deskripsi,
                    e.tanggal_mulai, e.tanggal_selesai, e.lokasi, e.kuota, e.harga, e.poster,
                    k.nama_kategori,
                    p.nama AS pembuat_nama, p.email AS pembuat_email, p.no_hp AS pembuat_hp,
                    u.nama_ukm, u.singkatan,
                    (SELECT count(*) FROM tiket t WHERE t.id_event = e.id_event) AS jumlah_peserta
             FROM events e
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN pengguna p ON p.id_pengguna = a.id_pengguna
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             ORDER BY e.tanggal_mulai DESC'
        );
    }

    public function event(int $id): array
    {
        return $this->row(
            'SELECT e.id_event, e.id_pembuat, e.id_kategori, e.nama_event, e.deskripsi,
                    e.tanggal_mulai, e.tanggal_selesai, e.lokasi, e.kuota, e.harga, e.poster,
                    e.created_at,
                    k.nama_kategori,
                    p.nama AS pembuat_nama, p.email AS pembuat_email, p.no_hp AS pembuat_hp,
                    u.nama_ukm, u.singkatan,
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

    public function eventTickets(int $id): array
    {
        return $this->rows(
            'SELECT t.id_tiket, t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat,
                    t.url_sertifikat, t.tanggal_daftar, t.check_in_at,
                    p.nama, p.email, p.nim,
                    (SELECT max(h.waktu_checkin) FROM kehadiran h WHERE h.id_tiket = t.id_tiket) AS waktu_checkin
             FROM tiket t
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             WHERE t.id_event = :id
             ORDER BY t.tanggal_daftar ASC',
            ['id' => $id]
        );
    }

    public function payments(): array
    {
        return $this->rows(
            'SELECT pb.id_pembayaran, pb.id_tiket, pb.bukti_pembayaran, pb.metode, pb.waktu_unggah,
                    t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat, t.tanggal_daftar,
                    p.nama AS peserta, p.email, p.nim,
                    e.id_event, e.nama_event,
                    k.nama_kategori,
                    (SELECT max(h.waktu_checkin) FROM kehadiran h WHERE h.id_tiket = t.id_tiket) AS waktu_checkin
             FROM pembayaran pb
             LEFT JOIN tiket t ON t.id_tiket = pb.id_tiket
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             ORDER BY pb.waktu_unggah DESC'
        );
    }

    public function payment(int $id): array
    {
        return $this->row(
            'SELECT pb.id_pembayaran, pb.id_tiket, pb.bukti_pembayaran, pb.metode, pb.waktu_unggah,
                    t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat, t.tanggal_daftar,
                    t.check_in_at, t.url_sertifikat,
                    p.nama AS peserta, p.email, p.nim, p.no_hp,
                    e.id_event, e.nama_event, e.lokasi, e.tanggal_mulai, e.tanggal_selesai, e.harga,
                    k.nama_kategori,
                    u.nama_ukm, u.singkatan,
                    (SELECT max(h.waktu_checkin) FROM kehadiran h WHERE h.id_tiket = t.id_tiket) AS waktu_checkin
             FROM pembayaran pb
             LEFT JOIN tiket t ON t.id_tiket = pb.id_tiket
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             WHERE pb.id_pembayaran = :id',
            ['id' => $id]
        );
    }

    public function activity(int $limit = 5): array
    {
        return $this->rows(
            'SELECT t.id_tiket, t.tanggal_daftar, t.status_bayar,
                    p.nama AS peserta,
                    e.nama_event,
                    pb.id_pembayaran
             FROM tiket t
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN pembayaran pb ON pb.id_tiket = t.id_tiket
             ORDER BY t.tanggal_daftar DESC
             LIMIT ' . max(1, $limit)
        );
    }

    public function attendance(int $limit = 5): array
    {
        return $this->rows(
            'SELECT h.id_kehadiran, h.waktu_checkin,
                    p.nama AS peserta,
                    e.nama_event
             FROM kehadiran h
             LEFT JOIN tiket t ON t.id_tiket = h.id_tiket
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             ORDER BY h.waktu_checkin DESC
             LIMIT ' . max(1, $limit)
        );
    }

    public function reportRows(): array
    {
        return $this->rows(
            "SELECT t.id_tiket, t.tanggal_daftar, t.status_bayar, t.status_sertifikat,
                    p.nama, p.email,
                    e.nama_event, k.nama_kategori,
                    (SELECT max(h.waktu_checkin) FROM kehadiran h WHERE h.id_tiket = t.id_tiket) AS waktu_checkin
             FROM tiket t
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             ORDER BY t.tanggal_daftar DESC"
        );
    }

    public function unpaidTickets(): array
    {
        return $this->rows(
            "SELECT t.id_tiket, t.kode_qr, t.nominal, t.status_bayar, t.tanggal_daftar,
                    p.nama AS peserta, p.email, p.nim,
                    e.id_event, e.nama_event,
                    k.nama_kategori
             FROM tiket t
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             WHERE t.nominal > 0
               AND NOT EXISTS (SELECT 1 FROM pembayaran pb WHERE pb.id_tiket = t.id_tiket)
             ORDER BY t.tanggal_daftar DESC"
        );
    }

    public function ticket(int $id): array
    {
        return $this->row(
            "SELECT t.id_tiket, t.kode_qr, t.nominal, t.status_bayar, t.status_sertifikat,
                    t.url_sertifikat, t.tanggal_daftar, t.check_in_at,
                    p.nama AS peserta, p.email, p.nim, p.no_hp,
                    e.id_event, e.nama_event, e.lokasi, e.tanggal_mulai, e.tanggal_selesai, e.harga,
                    k.nama_kategori,
                    u.nama_ukm, u.singkatan,
                    (SELECT max(h.waktu_checkin) FROM kehadiran h WHERE h.id_tiket = t.id_tiket) AS waktu_checkin
             FROM tiket t
             LEFT JOIN pengguna p ON p.id_pengguna = t.id_pengguna
             LEFT JOIN events e ON e.id_event = t.id_event
             LEFT JOIN kategori_event k ON k.id_kategori = e.id_kategori
             LEFT JOIN admin_ukm a ON a.id_admin_ukm = e.id_pembuat
             LEFT JOIN ukm u ON u.id_ukm = a.id_ukm
             WHERE t.id_tiket = :id",
            ['id' => $id]
        );
    }

    public function totals(): array
    {
        $row = $this->row(
            "SELECT
                (SELECT count(*) FROM events) AS total_event,
                (SELECT count(*) FROM events WHERE tanggal_selesai >= CURRENT_TIMESTAMP) AS event_aktif,
                (SELECT count(*) FROM tiket) AS total_tiket,
                (SELECT count(DISTINCT id_pengguna) FROM tiket WHERE id_pengguna IS NOT NULL) AS total_peserta,
                (SELECT coalesce(sum(nominal), 0) FROM tiket WHERE status_bayar = 'Lunas') AS pendapatan
            "
        );

        $hadir = $this->row('SELECT count(*) AS jumlah FROM kehadiran');

        $totalTiket = (int) ($row['total_tiket'] ?? 0);
        $totalHadir = (int) ($hadir['jumlah'] ?? 0);

        return [
            'total_event'    => (int) ($row['total_event'] ?? 0),
            'event_aktif'    => (int) ($row['event_aktif'] ?? 0),
            'total_tiket'    => $totalTiket,
            'total_peserta'  => (int) ($row['total_peserta'] ?? 0),
            'pendapatan'     => (float) ($row['pendapatan'] ?? 0),
            'total_hadir'    => $totalHadir,
            'belum_hadir'    => max(0, $totalTiket - $totalHadir),
            'persen_hadir'   => $totalTiket > 0 ? ($totalHadir / $totalTiket) * 100 : 0.0,
        ];
    }
}
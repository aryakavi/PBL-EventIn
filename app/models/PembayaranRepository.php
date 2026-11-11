<?php
declare(strict_types=1);

/**
 * Tulis data pembayaran dan ubah status tiket.
 *
 * Status tiket yang dipakai aplikasi:
 * - Belum Bayar : tiket dibuat, peserta belum upload bukti.
 * - Menunggu    : bukti sudah diunggah, menunggu verifikasi admin.
 * - Lunas       : sudah diverifikasi admin.
 * - Batal       : ditolak admin / gagal.
 */
class PembayaranRepository
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function latestForTicket(int $idTiket): array|false
    {
        $sql = 'SELECT id_pembayaran, id_tiket, bukti_pembayaran, metode, waktu_unggah
                FROM pembayaran
                WHERE id_tiket = :id_tiket
                ORDER BY waktu_unggah DESC NULLS LAST, id_pembayaran DESC
                LIMIT 1';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_tiket' => $idTiket]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Simpan pembayaran. Tabel pembayaran punya UNIQUE(id_tiket), jadi
     * upload ulang memperbarui baris yang sama (upsert), bukan menambah baris.
     */
    public function create(int $idTiket, string $buktiPath, string $metode): int
    {
        $sql = 'INSERT INTO pembayaran (id_tiket, bukti_pembayaran, metode, waktu_unggah)
                VALUES (:id_tiket, :bukti, :metode, NOW())
                ON CONFLICT (id_tiket) DO UPDATE
                SET bukti_pembayaran = EXCLUDED.bukti_pembayaran,
                    metode = EXCLUDED.metode,
                    waktu_unggah = EXCLUDED.waktu_unggah
                RETURNING id_pembayaran';

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id_tiket' => $idTiket,
            'bukti'    => $buktiPath,
            'metode'   => $metode,
        ]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Insert pembayaran lalu set tiket menjadi Menunggu dalam satu transaksi.
     */
    public function createAndMarkPending(int $idTiket, string $buktiPath, string $metode): int
    {
        $ownTransaction = !$this->db->inTransaction();

        if ($ownTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $idPembayaran = $this->create($idTiket, $buktiPath, $metode);
            $this->setTicketStatus($idTiket, 'Menunggu');

            if ($ownTransaction) {
                $this->db->commit();
            }

            return $idPembayaran;
        } catch (Throwable $e) {
            if ($ownTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    public function setTicketStatus(int $idTiket, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE tiket SET status_bayar = :status WHERE id_tiket = :id_tiket'
        );

        $stmt->execute([
            'status'    => $status,
            'id_tiket'  => $idTiket,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Ubah status tiket lewat id pembayaran. Mengembalikan id_tiket atau null.
     */
    public function setStatusByPayment(int $idPembayaran, string $status): ?int
    {
        $ownTransaction = !$this->db->inTransaction();

        if ($ownTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT id_tiket FROM pembayaran WHERE id_pembayaran = :id'
            );
            $stmt->execute(['id' => $idPembayaran]);
            $idTiket = $stmt->fetchColumn();

            if ($idTiket === false) {
                if ($ownTransaction) {
                    $this->db->rollBack();
                }

                return null;
            }

            $this->setTicketStatus((int) $idTiket, $status);

            if ($ownTransaction) {
                $this->db->commit();
            }

            return (int) $idTiket;
        } catch (Throwable $e) {
            if ($ownTransaction && $this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}
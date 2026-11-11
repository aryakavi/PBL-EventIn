<?php
declare(strict_types=1);

/**
 * Penyimpan transaksi gateway pembayaran (Midtrans).
 *
 * Tabel `pembayaran_gateway` sengaja dipisah dari `pembayaran` karena
 * `pembayaran` menyimpan bukti transfer manual dan punya UNIQUE(id_tiket).
 * Satu tiket boleh punya banyak percobaan transaksi gateway.
 *
 * Kalau tabel belum dibuat di Supabase, semua method mengembalikan nilai
 * kosong dan alur transfer manual tetap berjalan.
 */
class PembayaranGatewayRepository
{
    private const TABLE = 'public.pembayaran_gateway';

    private PDO $db;

    /** @var bool|null Cache hasil cek tabel. */
    private ?bool $available = null;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /** True kalau tabel `pembayaran_gateway` sudah ada. */
    public function isAvailable(): bool
    {
        if ($this->available !== null) {
            return $this->available;
        }

        try {
            $stmt = $this->db->prepare('SELECT to_regclass(:tabel)');
            $stmt->execute(['tabel' => self::TABLE]);
            $this->available = $stmt->fetchColumn() !== null;
        } catch (Throwable $e) {
            error_log('EventIn cek pembayaran_gateway: ' . $e->getMessage());
            $this->available = false;
        }

        return $this->available;
    }

    /**
     * Transaksi gateway terakhir untuk satu tiket.
     *
     * @return array<string, mixed>|false
     */
    public function latestForTicket(int $idTiket): array|false
    {
        if (!$this->isAvailable() || $idTiket <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT order_id, id_tiket, transaction_status, gross_amount, raw_response, created_at, paid_at
                 FROM ' . self::TABLE . '
                 WHERE id_tiket = :id_tiket
                 ORDER BY created_at DESC
                 LIMIT 1'
            );
            $stmt->execute(['id_tiket' => $idTiket]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('EventIn baca pembayaran_gateway: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @return array<string, mixed>|false
     */
    public function findByOrderId(string $orderId): array|false
    {
        if (!$this->isAvailable() || $orderId === '') {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'SELECT order_id, id_tiket, transaction_status, gross_amount, raw_response, created_at, paid_at
                 FROM ' . self::TABLE . '
                 WHERE order_id = :order_id
                 LIMIT 1'
            );
            $stmt->execute(['order_id' => $orderId]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            error_log('EventIn baca pembayaran_gateway: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Simpan transaksi baru. Order ID harus unik.
     *
     * @param array<string, mixed>|null $raw
     */
    public function create(string $orderId, int $idTiket, string $status, float $grossAmount, ?array $raw = null): bool
    {
        if (!$this->isAvailable() || $orderId === '' || $idTiket <= 0) {
            return false;
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ' . self::TABLE . '
                    (order_id, id_tiket, transaction_status, gross_amount, raw_response)
                 VALUES (:order_id, :id_tiket, :status, :gross, :raw)
                 ON CONFLICT (order_id) DO UPDATE
                 SET transaction_status = EXCLUDED.transaction_status,
                     gross_amount = EXCLUDED.gross_amount,
                     raw_response = EXCLUDED.raw_response'
            );

            $stmt->execute([
                'order_id' => $orderId,
                'id_tiket' => $idTiket,
                'status'   => $status,
                'gross'    => $grossAmount,
                'raw'      => $raw !== null ? json_encode($raw, JSON_UNESCAPED_SLASHES) : null,
            ]);

            return true;
        } catch (Throwable $e) {
            error_log('EventIn simpan pembayaran_gateway: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Perbarui status transaksi. Idempoten: aman dipanggil berkali-kali
     * dengan status yang sama.
     *
     * @param array<string, mixed>|null $raw
     * @param bool $markPaid Isi paid_at kalau belum terisi.
     */
    public function updateStatus(string $orderId, string $status, ?array $raw = null, bool $markPaid = false): bool
    {
        if (!$this->isAvailable() || $orderId === '') {
            return false;
        }

        $setPaid = $markPaid ? ', paid_at = COALESCE(paid_at, CURRENT_TIMESTAMP)' : '';

        try {
            $stmt = $this->db->prepare(
                'UPDATE ' . self::TABLE . '
                 SET transaction_status = :status,
                     raw_response = COALESCE(raw_response, \'{}\'::jsonb)
                                    || COALESCE(CAST(:raw AS jsonb), \'{}\'::jsonb)' . $setPaid . '
                 WHERE order_id = :order_id'
            );

            $stmt->execute([
                'order_id' => $orderId,
                'status'   => $status,
                'raw'      => $raw !== null ? json_encode($raw, JSON_UNESCAPED_SLASHES) : null,
            ]);

            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            error_log('EventIn update pembayaran_gateway: ' . $e->getMessage());

            return false;
        }
    }
}
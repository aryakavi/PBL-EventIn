<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Tiket.php';
require_once __DIR__ . '/../models/PembayaranGatewayRepository.php';
require_once __DIR__ . '/../../includes/midtrans.php';

/**
 * Alur pembayaran online lewat Midtrans Snap.
 *
 * Tiga titik masuk:
 * - start()               : peserta menekan tombol "Bayar Online".
 * - handleNotification()  : webhook dari Midtrans.
 * - sync()                : cek ulang status saat peserta kembali ke situs.
 */
class MidtransController
{
    private PDO $db;
    private Tiket $tiketModel;
    private PembayaranGatewayRepository $gatewayRepo;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
        $this->tiketModel = new Tiket($pdo);
        $this->gatewayRepo = new PembayaranGatewayRepository($pdo);
    }

    public function gatewayReady(): bool
    {
        return MidtransClient::isReady() && $this->gatewayRepo->isAvailable();
    }

    /**
     * Buat transaksi Snap untuk satu tiket milik peserta.
     *
     * @param array<string, mixed> $sesi
     * @return array{success: bool, message: string, token?: string, redirect_url?: string, order_id?: string}
     */
    public function start(int $idTiket, int $idPengguna, array $sesi): array
    {
        if (!MidtransClient::isReady()) {
            return [
                'success' => false,
                'message' => 'Pembayaran online belum aktif. Hubungi admin event.',
            ];
        }

        if (!$this->gatewayRepo->isAvailable()) {
            return [
                'success' => false,
                'message' => 'Tabel pembayaran_gateway belum dibuat di database. Hubungi admin.',
            ];
        }

        $tiket = $this->tiketModel->findForUser($idTiket, $idPengguna);

        if ($tiket === false || empty($tiket)) {
            return ['success' => false, 'message' => 'Tiket tidak ditemukan.'];
        }

        if (str_starts_with(strtolower((string) ($tiket['status_bayar'] ?? '')), 'lunas')) {
            return ['success' => false, 'message' => 'Tiket ini sudah lunas.'];
        }

        $nominal = (float) ($tiket['nominal'] ?? 0);

        if ($nominal <= 0) {
            return ['success' => false, 'message' => 'Event ini gratis, tidak perlu pembayaran.'];
        }

        $orderId = $this->buildOrderId($idTiket);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) round($nominal),
            ],
            'item_details' => [[
                'id'       => 'TIKET-' . $idTiket,
                'price'    => (int) round($nominal),
                'quantity' => 1,
                'name'     => mb_substr((string) ($tiket['nama_event'] ?? 'Tiket Event'), 0, 50),
            ]],
            'customer_details' => [
                'first_name' => mb_substr((string) ($sesi['nama'] ?? 'Peserta'), 0, 40),
                'email'      => (string) ($sesi['email'] ?? ''),
                'phone'      => (string) ($sesi['no_hp'] ?? ''),
            ],
            'credit_card' => ['secure' => true],
        ];

        $finishUrl = $this->finishUrl($idTiket);

        if ($finishUrl !== '') {
            $params['callbacks'] = ['finish' => $finishUrl];
        }

        $result = MidtransClient::createSnapTransaction($params);

        if (!$result['success']) {
            return $result;
        }

        $this->gatewayRepo->create($orderId, $idTiket, 'pending', $nominal, [
            'order_id'      => $orderId,
            'token'         => $result['token'] ?? '',
            'redirect_url'  => $result['redirect_url'] ?? '',
            'gross_amount'  => (int) round($nominal),
            'created_from'  => 'snap',
        ]);

        try {
            (new PembayaranRepositoryProxy($this->db))->setTicketStatus($idTiket, 'Menunggu');
        } catch (Throwable $e) {
            error_log('EventIn set status Menunggu (midtrans): ' . $e->getMessage());
        }

        return [
            'success'      => true,
            'message'      => 'Transaksi dibuat. Lanjutkan pembayaran.',
            'token'        => (string) ($result['token'] ?? ''),
            'redirect_url' => (string) ($result['redirect_url'] ?? ''),
            'order_id'     => $orderId,
        ];
    }

    /**
     * Tangani notifikasi Midtrans.
     *
     * Selalu idempoten. Mengembalikan kode HTTP yang harus dipakai endpoint.
     *
     * @param array<string, mixed> $payload
     * @return array{code: int, message: string}
     */
    public function handleNotification(array $payload): array
    {
        if (!MidtransClient::verifySignature($payload)) {
            // Jangan pernah mempercayai notifikasi tanpa signature valid.
            return ['code' => 401, 'message' => 'Signature tidak valid.'];
        }

        $orderId = (string) ($payload['order_id'] ?? '');

        if ($orderId === '') {
            return ['code' => 400, 'message' => 'order_id kosong.'];
        }

        $row = $this->gatewayRepo->findByOrderId($orderId);

        if ($row === false) {
            return ['code' => 404, 'message' => 'Order tidak dikenal.'];
        }

        return $this->applyPayload($row, $payload);
    }

    /**
     * Cek ulang status ke Midtrans lalu simpan.
     *
     * @return array{success: bool, message: string, status?: string}
     */
    public function sync(string $orderId): array
    {
        $row = $this->gatewayRepo->findByOrderId($orderId);

        if ($row === false) {
            return ['success' => false, 'message' => 'Order tidak ditemukan.'];
        }

        $status = MidtransClient::getStatus($orderId);

        if (!$status['success']) {
            return ['success' => false, 'message' => $status['message']];
        }

        $hasil = $this->applyPayload($row, $status['payload'] ?? []);

        return [
            'success' => $hasil['code'] === 200,
            'message' => $hasil['message'],
            'status'  => (string) ($row['transaction_status'] ?? ''),
        ];
    }

    /**
     * @param array<string, mixed> $row
     * @param array<string, mixed> $payload
     * @return array{code: int, message: string}
     */
    private function applyPayload(array $row, array $payload): array
    {
        $orderId = (string) ($row['order_id'] ?? '');
        $idTiket = (int) ($row['id_tiket'] ?? 0);

        if ($orderId === '' || $idTiket <= 0) {
            return ['code' => 400, 'message' => 'Data order tidak lengkap.'];
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus       = (string) ($payload['fraud_status'] ?? '');
        $statusTiket       = MidtransClient::mapStatus($transactionStatus, $fraudStatus);

        if ($statusTiket === null) {
            // Status tidak dikenal: simpan payload, jangan ubah tiket.
            $this->gatewayRepo->updateStatus($orderId, $transactionStatus !== '' ? $transactionStatus : 'unknown', $payload);

            return ['code' => 200, 'message' => 'Status diabaikan: ' . $transactionStatus];
        }

        $nominal = (float) ($payload['gross_amount'] ?? 0);

        if ($statusTiket === 'Lunas' && !$this->amountMatches($idTiket, $nominal)) {
            error_log('EventIn Midtrans nominal tidak cocok untuk order ' . $orderId . ': ' . $nominal);

            $this->gatewayRepo->updateStatus($orderId, $transactionStatus, $payload);

            return ['code' => 200, 'message' => 'Nominal tidak cocok, status tiket tidak diubah.'];
        }

        $this->gatewayRepo->updateStatus($orderId, $transactionStatus, $payload, $statusTiket === 'Lunas');

        try {
            (new PembayaranRepositoryProxy($this->db))->setTicketStatus($idTiket, $statusTiket);
        } catch (Throwable $e) {
            error_log('EventIn set status tiket (midtrans): ' . $e->getMessage());

            return ['code' => 500, 'message' => 'Gagal menyimpan status tiket.'];
        }

        return ['code' => 200, 'message' => 'Status tiket menjadi ' . $statusTiket];
    }

    private function amountMatches(int $idTiket, float $nominal): bool
    {
        try {
            $stmt = $this->db->prepare('SELECT nominal FROM tiket WHERE id_tiket = :id LIMIT 1');
            $stmt->execute(['id' => $idTiket]);
            $nilai = $stmt->fetchColumn();

            if ($nilai === false) {
                return false;
            }

            return abs((float) $nilai - $nominal) < 0.01;
        } catch (Throwable $e) {
            error_log('EventIn cek nominal tiket: ' . $e->getMessage());

            return false;
        }
    }

    private function buildOrderId(int $idTiket): string
    {
        return 'EVT-' . $idTiket . '-' . date('ymdHis') . '-' . bin2hex(random_bytes(3));
    }

    private function finishUrl(int $idTiket): string
    {
        $config = MidtransClient::config();

        if ($config['finish_url'] !== '') {
            $base = $config['finish_url'];
        } else {
            $proto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
            $scheme = $proto !== ''
                ? (str_contains($proto, 'https') ? 'https' : 'http')
                : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');

            $host = (string) ($_SERVER['HTTP_HOST'] ?? '');

            if ($host === '') {
                return '';
            }

            $base = $scheme . '://' . $host . '/midtrans-finish.php';
        }

        $separator = str_contains($base, '?') ? '&' : '?';

        return $base . $separator . 'id_tiket=' . $idTiket;
    }
}

/**
 * Pembungkus tipis supaya controller ini tidak perlu memuat repository
 * pembayaran manual kalau tidak dipakai.
 */
final class PembayaranRepositoryProxy
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function setTicketStatus(int $idTiket, string $status): bool
    {
        require_once __DIR__ . '/../models/PembayaranRepository.php';

        return (new PembayaranRepository($this->db))->setTicketStatus($idTiket, $status);
    }
}
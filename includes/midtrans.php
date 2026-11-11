<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Klien Midtrans Snap.
 *
 * Hanya memakai cURL / HTTP stream bawaan PHP, tanpa Composer, supaya
 * image Docker tidak perlu dependency tambahan.
 *
 * Kredensial dibaca dari app_config() (environment Render atau file .env).
 * Server key TIDAK boleh pernah dikirim ke browser.
 */
class MidtransClient
{
    public static function config(): array
    {
        $config = app_config();

        $production = (bool) $config['midtrans_production'];

        return [
            'server_key'  => trim((string) $config['midtrans_server_key']),
            'client_key'  => trim((string) $config['midtrans_client_key']),
            'production'  => $production,
            'snap_base'   => $production
                ? 'https://app.midtrans.com'
                : 'https://app.sandbox.midtrans.com',
            'api_base'    => $production
                ? 'https://api.midtrans.com'
                : 'https://api.sandbox.midtrans.com',
            'finish_url'  => trim((string) $config['midtrans_finish_url']),
        ];
    }

    /** True kalau server key sudah diisi, jadi tombol bayar online boleh tampil. */
    public static function isReady(): bool
    {
        return self::config()['server_key'] !== '';
    }

    /** URL snap.js untuk dipakai di halaman pembayaran. */
    public static function snapScriptUrl(): string
    {
        return self::config()['snap_base'] . '/snap/snap.js';
    }

    public static function clientKey(): string
    {
        return self::config()['client_key'];
    }

    /**
     * Buat transaksi Snap, kembalikan token dan redirect_url.
     *
     * @param array<string, mixed> $params
     * @return array{success: bool, message: string, token?: string, redirect_url?: string}
     */
    public static function createSnapTransaction(array $params): array
    {
        $config = self::config();

        if ($config['server_key'] === '') {
            return [
                'success' => false,
                'message' => 'MIDTRANS_SERVER_KEY belum diisi. Pembayaran online belum aktif.',
            ];
        }

        $result = self::request(
            'POST',
            $config['snap_base'] . '/snap/v1/transactions',
            $config['server_key'],
            $params
        );

        $body = json_decode($result['body'], true);
        $body = is_array($body) ? $body : [];

        if ($result['status'] < 200 || $result['status'] >= 300) {
            $pesan = $body['error_messages'][0]
                ?? $body['status_message']
                ?? ('HTTP ' . $result['status']);

            error_log('EventIn Midtrans snap error: ' . mb_substr((string) $result['body'], 0, 500));

            return [
                'success' => false,
                'message' => 'Gagal membuat transaksi Midtrans: ' . (string) $pesan,
            ];
        }

        if (empty($body['token'])) {
            return ['success' => false, 'message' => 'Midtrans tidak mengembalikan token.'];
        }

        return [
            'success'      => true,
            'message'      => 'Transaksi Midtrans dibuat.',
            'token'        => (string) $body['token'],
            'redirect_url' => (string) ($body['redirect_url'] ?? ''),
        ];
    }

    /**
     * Ambil status transaksi langsung dari Midtrans.
     *
     * @return array{success: bool, message: string, payload?: array<string, mixed>}
     */
    public static function getStatus(string $orderId): array
    {
        $config = self::config();

        if ($config['server_key'] === '') {
            return ['success' => false, 'message' => 'MIDTRANS_SERVER_KEY belum diisi.'];
        }

        if ($orderId === '') {
            return ['success' => false, 'message' => 'Order ID kosong.'];
        }

        $result = self::request(
            'GET',
            $config['api_base'] . '/v2/' . rawurlencode($orderId) . '/status',
            $config['server_key']
        );

        $body = json_decode($result['body'], true);
        $body = is_array($body) ? $body : [];

        if ($result['status'] < 200 || $result['status'] >= 300) {
            return [
                'success' => false,
                'message' => 'Status Midtrans tidak ditemukan (HTTP ' . $result['status'] . ').',
            ];
        }

        return ['success' => true, 'message' => 'OK', 'payload' => $body];
    }

    /**
     * Verifikasi signature notifikasi Midtrans.
     *
     * SHA512(order_id + status_code + gross_amount + ServerKey)
     *
     * Tanpa ini siapa pun bisa mengirim POST palsu dan menandai tiket lunas.
     *
     * @param array<string, mixed> $payload
     */
    public static function verifySignature(array $payload): bool
    {
        $config = self::config();

        if ($config['server_key'] === '') {
            return false;
        }

        $orderId    = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $gross      = (string) ($payload['gross_amount'] ?? '');
        $signature  = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $gross === '' || $signature === '') {
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $gross . $config['server_key']);

        return hash_equals($expected, $signature);
    }

    /**
     * Terjemahkan status Midtrans menjadi status tiket aplikasi.
     *
     * @return string|null Null kalau status tidak dikenal.
     */
    public static function mapStatus(string $transactionStatus, string $fraudStatus = ''): ?string
    {
        $status = strtolower(trim($transactionStatus));
        $fraud  = strtolower(trim($fraudStatus));

        return match ($status) {
            'capture' => $fraud === 'challenge' ? 'Menunggu' : 'Lunas',
            'settlement' => 'Lunas',
            'pending' => 'Menunggu',
            'deny', 'cancel', 'expire', 'failure', 'refund', 'partial_refund' => 'Batal',
            default => null,
        };
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return array{status: int, body: string}
     */
    private static function request(string $method, string $url, string $serverKey, ?array $payload = null): array
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($serverKey . ':'),
            'Expect:',
        ];

        $body = $payload !== null ? json_encode($payload, JSON_UNESCAPED_SLASHES) : null;
        $body = $body === false ? '{}' : $body;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);

            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_CUSTOMREQUEST  => $method,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT        => 45,
            ];

            if ($body !== null) {
                $options[CURLOPT_POSTFIELDS] = $body;
            }

            curl_setopt_array($ch, $options);

            $response = curl_exec($ch);
            $status   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error    = curl_error($ch);

            unset($ch);

            return [
                'status' => $status,
                'body'   => $response === false ? $error : (string) $response,
            ];
        }

        $context = stream_context_create([
            'http' => [
                'method'        => $method,
                'header'        => implode("\r\n", $headers),
                'content'       => $body ?? '',
                'timeout'       => 45,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        $status = 0;

        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $match) === 1) {
                    $status = (int) $match[1];
                }
            }
        }

        return [
            'status' => $status,
            'body'   => $response === false ? 'request gagal' : (string) $response,
        ];
    }
}
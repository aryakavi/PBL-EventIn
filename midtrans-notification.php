<?php
declare(strict_types=1);

/**
 * Endpoint webhook Midtrans.
 *
 * URL ini didaftarkan di Midtrans Dashboard > Settings > Configuration >
 * Payment Notification URL. Harus dapat diakses publik lewat HTTPS dan
 * tidak boleh memakai sesi atau autentikasi tambahan.
 *
 * Contoh: https://<domain>/midtrans-notification.php
 */

require_once __DIR__ . '/includes/koneksi.php';
require_once __DIR__ . '/app/controllers/MidtransController.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Gunakan POST.']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode((string) $raw, true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Body bukan JSON valid.']);
    exit;
}

if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(503);
    echo json_encode(['ok' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}

try {
    $hasil = (new MidtransController($pdo))->handleNotification($payload);
} catch (Throwable $e) {
    error_log('EventIn webhook Midtrans: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Kesalahan internal.']);
    exit;
}

http_response_code($hasil['code']);
echo json_encode(['ok' => $hasil['code'] === 200, 'message' => $hasil['message']]);
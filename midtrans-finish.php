<?php
declare(strict_types=1);

/**
 * Halaman tujuan setelah peserta menyelesaikan pembayaran di Snap.
 *
 * Midtrans menambahkan parameter order_id, status_code, dan
 * transaction_status ke URL ini. Status tetap diverifikasi ulang ke API
 * Midtrans, karena parameter URL bisa diubah siapa saja.
 */

require_once __DIR__ . '/includes/data.php';
require_once __DIR__ . '/includes/koneksi.php';
require_once __DIR__ . '/app/controllers/MidtransController.php';

$orderId = (string) ($_GET['order_id'] ?? '');
$idTiket = (int) ($_GET['id_tiket'] ?? 0);

$tujuan = 'app/views/pembayaran.php';
$query  = [];

if (isset($pdo) && $pdo instanceof PDO && $orderId !== '') {
    $gatewayRepo = new PembayaranGatewayRepository($pdo);
    $baris = $gatewayRepo->findByOrderId($orderId);

    if ($baris !== false) {
        $idTiket = (int) ($baris['id_tiket'] ?? $idTiket);

        try {
            (new MidtransController($pdo))->sync($orderId);
        } catch (Throwable $e) {
            error_log('EventIn sync midtrans finish: ' . $e->getMessage());
        }
    }
}

if ($idTiket > 0) {
    $query['id_tiket'] = $idTiket;
}

$query['midtrans'] = 'selesai';

header('Location: ' . $tujuan . '?' . http_build_query($query));
exit;
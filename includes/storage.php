<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../app/models/Storage.php';

/**
 * URL publik file di Supabase Storage.
 * Kalau nilai yang disimpan sudah berupa URL, kembalikan apa adanya.
 */
function storage_public_url(string $path, ?string $bucket = null): string
{
    if (trim($path) === '') {
        return '';
    }

    return (new Storage(null, null, $bucket))->url($path);
}

/**
 * Upload bukti pembayaran ke Supabase Storage.
 *
 * Pembungkus tipis di atas kelas Storage supaya view tetap memakai
 * bentuk array hasil, bukan exception.
 *
 * @param array<string, mixed> $file Satu entri dari $_FILES.
 * @return array{success: bool, message: string, path?: string, url?: string}
 */
function storage_upload_bukti(array $file, int $idTiket): array
{
    if ($idTiket <= 0) {
        return ['success' => false, 'message' => 'Tiket tidak valid.'];
    }

    try {
        $config  = app_config();
        $storage = new Storage();
        $path    = $storage->upload($file, (string) $config['bukti_folder'], $idTiket . '-');
    } catch (Throwable $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }

    return [
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diunggah.',
        'path'    => $path,
        'url'     => $storage->url($path),
    ];
}
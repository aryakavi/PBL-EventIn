<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * URL publik file di Supabase Storage.
 * Kalau nilai yang disimpan sudah berupa URL, kembalikan apa adanya.
 */
function storage_public_url(string $path, ?string $bucket = null): string
{
    $path = trim($path);

    if ($path === '') {
        return '';
    }

    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }

    $config = app_config();
    $bucket = $bucket !== null && $bucket !== '' ? $bucket : $config['supabase_bucket'];

    return $config['supabase_url']
        . '/storage/v1/object/public/'
        . rawurlencode($bucket)
        . '/'
        . ltrim($path, '/');
}

/**
 * Upload bukti pembayaran ke Supabase Storage.
 *
 * @param array<string, mixed> $file Satu entri dari $_FILES.
 * @return array{success: bool, message: string, path?: string, url?: string}
 */
function storage_upload_bukti(array $file, int $idTiket): array
{
    if ($idTiket <= 0) {
        return ['success' => false, 'message' => 'Tiket tidak valid.'];
    }

    if (!isset($file['tmp_name'], $file['error'])) {
        return ['success' => false, 'message' => 'Pilih file bukti pembayaran terlebih dahulu.'];
    }

    if ((int) $file['error'] !== UPLOAD_ERR_OK) {
        $pesan = match ((int) $file['error']) {
            UPLOAD_ERR_NO_FILE    => 'Pilih file bukti pembayaran terlebih dahulu.',
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran file terlalu besar.',
            default               => 'Upload gagal (kode ' . (int) $file['error'] . '). Coba file lain.',
        };

        return ['success' => false, 'message' => $pesan];
    }

    if (!is_uploaded_file((string) $file['tmp_name'])) {
        return ['success' => false, 'message' => 'File upload tidak valid.'];
    }

    $config = app_config();
    $size = (int) ($file['size'] ?? 0);

    if ($size <= 0) {
        return ['success' => false, 'message' => 'File kosong.'];
    }

    if ($size > (int) $config['max_upload']) {
        return [
            'success' => false,
            'message' => 'Ukuran file maksimal ' . number_format($config['max_upload'] / 1024 / 1024, 1, ',', '.') . ' MB.',
        ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file((string) $file['tmp_name']);

    $allowed = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'application/pdf' => 'pdf',
    ];

    if (!isset($allowed[$mime])) {
        return ['success' => false, 'message' => 'Format file harus JPG, PNG, WEBP, atau PDF.'];
    }

    $key = trim((string) $config['supabase_key']);

    if ($key === '') {
        return [
            'success' => false,
            'message' => 'SUPABASE_SERVICE_ROLE_KEY belum diisi. Upload ke Supabase Storage belum bisa dilakukan.',
        ];
    }

    $folder = trim((string) $config['bukti_folder'], '/');
    $path = $folder
        . '/'
        . date('Y/m')
        . '/'
        . $idTiket
        . '-'
        . date('His')
        . '-'
        . bin2hex(random_bytes(4))
        . '.'
        . $allowed[$mime];

    $endpoint = $config['supabase_url']
        . '/storage/v1/object/'
        . rawurlencode($config['supabase_bucket'])
        . '/'
        . $path;

    $body = file_get_contents((string) $file['tmp_name']);

    if ($body === false) {
        return ['success' => false, 'message' => 'Gagal membaca file upload.'];
    }

    $headers = [
        'Authorization: Bearer ' . $key,
        'apikey: ' . $key,
        'Content-Type: ' . $mime,
        'Cache-Control: 3600',
        'x-upsert: true',
    ];

    $result = storage_http_post($endpoint, $headers, $body);

    if ($result['status'] < 200 || $result['status'] >= 300) {
        return [
            'success' => false,
            'message' => 'Upload ke Supabase Storage gagal (HTTP ' . $result['status'] . '). Periksa bucket, key, dan policy Storage.',
        ];
    }

    return [
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diunggah.',
        'path'    => $path,
        'url'     => storage_public_url($path),
    ];
}

/**
 * POST biner ke Supabase Storage. Pakai cURL kalau tersedia,
 * kalau tidak pakai HTTP stream bawaan PHP.
 *
 * @param array<int, string> $headers
 * @return array{status: int, body: string}
 */
function storage_http_post(string $endpoint, array $headers, string $body): array
{
    if (function_exists('curl_init')) {
        $ch = curl_init($endpoint);

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array_merge($headers, ['Expect:']),
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT        => 45,
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        return [
            'status' => $status,
            'body'   => $response === false ? $error : (string) $response,
        ];
    }

    $context = stream_context_create([
        'http' => [
            'method'        => 'POST',
            'header'        => implode("\r\n", $headers),
            'content'       => $body,
            'timeout'       => 45,
            'ignore_errors' => true,
        ],
    ]);

    $response = @file_get_contents($endpoint, false, $context);
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
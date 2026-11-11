<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/config.php';

/**
 * Klien sederhana Supabase Storage.
 *
 * Dipakai untuk mengunggah bukti pembayaran ke bucket `image_event`
 * dan membentuk URL publik file yang sudah tersimpan.
 *
 * Kredensial dibaca dari app_config() (environment Render atau file .env).
 * Jangan pernah menulis key langsung di kelas ini.
 */
class Storage
{
    private const ALLOWED = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'image/webp'      => 'webp',
        'application/pdf' => 'pdf',
    ];

    private string $supabaseUrl;
    private string $apiKey;
    private string $bucket;
    private int $maxBytes;

    public function __construct(
        ?string $url = null,
        ?string $key = null,
        ?string $bucket = null,
        ?int $maxBytes = null
    ) {
        $config = app_config();

        $this->supabaseUrl = rtrim(
            $url !== null && $url !== '' ? $url : (string) $config['supabase_url'],
            '/'
        );
        $this->apiKey   = trim($key ?? (string) $config['supabase_key']);
        $this->bucket   = $bucket !== null && $bucket !== '' ? $bucket : (string) $config['supabase_bucket'];
        $this->maxBytes = $maxBytes ?? (int) $config['max_upload'];
    }

    /**
     * Unggah satu entri $_FILES ke bucket.
     *
     * @param array<string, mixed> $file
     * @param string $folder Folder tujuan, tanpa garis miring di ujung.
     * @param string $prefix Awalan nama file, misal "12-" untuk id tiket.
     * @return string Path relatif di dalam bucket, siap disimpan ke database.
     */
    public function upload(array $file, string $folder, string $prefix = ''): string
    {
        if (!isset($file['tmp_name'], $file['error'], $file['size'])) {
            throw new RuntimeException('Pilih file yang akan diunggah terlebih dahulu.');
        }

        $error = (int) $file['error'];

        if ($error !== UPLOAD_ERR_OK) {
            throw new RuntimeException(match ($error) {
                UPLOAD_ERR_NO_FILE    => 'Pilih file yang akan diunggah terlebih dahulu.',
                UPLOAD_ERR_INI_SIZE,
                UPLOAD_ERR_FORM_SIZE  => 'Ukuran file terlalu besar.',
                default               => 'Upload gagal (kode ' . $error . '). Coba file lain.',
            });
        }

        if (!is_uploaded_file((string) $file['tmp_name'])) {
            throw new RuntimeException('File upload tidak valid.');
        }

        $size = (int) $file['size'];

        if ($size <= 0) {
            throw new RuntimeException('File kosong.');
        }

        if ($size > $this->maxBytes) {
            throw new RuntimeException(
                'Ukuran file maksimal '
                . number_format($this->maxBytes / 1024 / 1024, 1, ',', '.')
                . ' MB.'
            );
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);

        if (!is_string($mime) || !isset(self::ALLOWED[$mime])) {
            throw new RuntimeException('Format file harus JPG, PNG, WEBP, atau PDF.');
        }

        if ($this->apiKey === '') {
            throw new RuntimeException(
                'SUPABASE_SERVICE_ROLE_KEY belum diisi. Upload ke Supabase Storage belum bisa dilakukan.'
            );
        }

        $folder = trim($folder, '/');

        if ($folder === '' || str_contains($folder, '..')) {
            throw new RuntimeException('Folder tujuan tidak valid.');
        }

        $path = $folder
            . '/'
            . date('Y/m')
            . '/'
            . $prefix
            . bin2hex(random_bytes(8))
            . '.'
            . self::ALLOWED[$mime];

        $body = file_get_contents((string) $file['tmp_name']);

        if ($body === false) {
            throw new RuntimeException('Gagal membaca file upload.');
        }

        $result = $this->post($path, $mime, $body);

        if ($result['status'] < 200 || $result['status'] >= 300) {
            // Detail Supabase hanya masuk log, jangan ditampilkan ke pengguna.
            error_log(
                'EventIn storage upload ' . $result['status'] . ': '
                . mb_substr((string) $result['body'], 0, 300)
            );

            throw new RuntimeException(
                'Upload ke Supabase Storage gagal (HTTP ' . $result['status']
                . '). Periksa bucket, key, dan policy Storage.'
            );
        }

        return $path;
    }

    /**
     * URL publik sebuah file. Nilai yang sudah berupa URL dikembalikan apa adanya.
     */
    public function url(string $path): string
    {
        $path = trim($path);

        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        $segments = array_map('rawurlencode', explode('/', ltrim($path, '/')));

        return $this->supabaseUrl
            . '/storage/v1/object/public/'
            . rawurlencode($this->bucket)
            . '/'
            . implode('/', $segments);
    }

    /**
     * POST biner ke Supabase Storage. Pakai cURL kalau tersedia,
     * kalau tidak pakai HTTP stream bawaan PHP.
     *
     * @return array{status: int, body: string}
     */
    private function post(string $path, string $mime, string $body): array
    {
        $endpoint = $this->supabaseUrl
            . '/storage/v1/object/'
            . rawurlencode($this->bucket)
            . '/'
            . implode('/', array_map('rawurlencode', explode('/', $path)));

        $headers = [
            'apikey: ' . $this->apiKey,
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: ' . $mime,
            'Cache-Control: 3600',
            'Expect:',
        ];

        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);

            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $body,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT        => 45,
            ]);

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
}
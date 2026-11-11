<?php
declare(strict_types=1);

/**
 * Konfigurasi aplikasi.
 *
 * Nilai dibaca dari environment (Render) atau file .env di root proyek.
 * Fallback lokal dipertahankan supaya aplikasi tetap jalan sebelum .env dibuat.
 */
function app_config(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $envFile = dirname(__DIR__) . '/.env';

    if (is_file($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = ltrim(trim($line), "\xEF\xBB\xBF");

            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '') {
                continue;
            }

            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];

                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            if (getenv($key) === false) {
                putenv($key . '=' . $value);
                $_ENV[$key] = $value;
            }
        }
    }

    $config = [
        'db_host'    => env_value('DB_HOST', 'aws-0-ap-southeast-1.pooler.supabase.com'),
        'db_port'    => env_value('DB_PORT', '5432'),
        'db_name'    => env_value('DB_NAME', 'postgres'),
        'db_user'    => env_value('DB_USER', 'postgres.aypwdgvqcizpsnccrfwc'),
        'db_pass'    => env_value('DB_PASS', '0VGsfE6Teeyu3hIu'),
        'db_sslmode' => env_value('DB_SSLMODE', 'require'),

        'supabase_url'    => rtrim(env_value('SUPABASE_URL', 'https://aypwdgvqcizpsnccrfwc.supabase.co'), '/'),
        'supabase_key'    => env_value(
            'SUPABASE_SERVICE_ROLE_KEY',
            env_value(
                'SUPABASE_ANON_KEY',
                env_value('SUPABASE_PUBLISHABLE_KEY', env_value('SUPABASE_KEY', ''))
            )
        ),
        'supabase_bucket' => env_value('SUPABASE_BUCKET', 'image_event'),
        'bukti_folder'    => trim(env_value('SUPABASE_BUKTI_FOLDER', 'bukti_pembayaran'), '/'),
        'max_upload'      => (int) env_value('MAX_UPLOAD_BYTES', (string) (5 * 1024 * 1024)),

        'midtrans_server_key' => env_value('MIDTRANS_SERVER_KEY', ''),
        'midtrans_client_key' => env_value('MIDTRANS_CLIENT_KEY', ''),
        'midtrans_production' => in_array(
            strtolower(env_value('MIDTRANS_IS_PRODUCTION', 'false')),
            ['1', 'true', 'yes', 'on'],
            true
        ),
        'midtrans_finish_url' => env_value('MIDTRANS_FINISH_URL', ''),
    ];

    return $config;
}

function env_value(string $key, string $default = ''): string
{
    $value = getenv($key);

    if ($value === false || $value === '') {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? '';
    }

    return $value !== '' ? (string) $value : $default;
}
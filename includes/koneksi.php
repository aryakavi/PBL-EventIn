<?php

require_once __DIR__ . '/config.php';

$config = app_config();

$host    = $config['db_host'];
$port    = $config['db_port'];
$dbname  = $config['db_name'];
$user    = $config['db_user'];
$password = $config['db_pass'];
$sslmode = $config['db_sslmode'];

$pdo = null;
$dbError = null;

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=$sslmode",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Jangan tampilkan kredensial atau detail koneksi ke pengguna.
    $dbError = 'Koneksi database gagal. Periksa konfigurasi DB di environment.';
    error_log('EventIn DB error: ' . $e->getMessage());
}
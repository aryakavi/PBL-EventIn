
<?php

$host = 'aws-0-ap-southeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.aypwdgvqcizpsnccrfwc';
$password = '0VGsfE6Teeyu3hIu';

try {
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo '<pre>';
    echo htmlspecialchars($e->getMessage());
    echo '</pre>';
}

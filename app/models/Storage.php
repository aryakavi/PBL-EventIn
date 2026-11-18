<?php

class Storage
{
    private $supabaseUrl = 'https://aypwdgvqcizpsnccrfwc.supabase.co';
    private $apiKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImF5cHdkZ3ZxY2l6cHNuY2NyZndjIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc5MTkxODkxMCwiZXhwIjoyMTA3NDk0OTEwfQ.Ns_9FarrfdZJySFafzO4Q_F1Q-rEu3P4oWjIeIw-q6g';

    private $bucket = 'image_event';
    public function upload($file, $folder)
    {

        // Validasi file
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Upload gambar gagal.');
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('Ukuran gambar maksimal 5 MB.');
        }

        $mime = mime_content_type($file['tmp_name']);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowed[$mime])) {
            throw new Exception('Format gambar tidak didukung.');
        }

        $filename = $folder . '/' . uniqid() . '.' . $allowed[$mime];

        $url = $this->supabaseUrl . '/storage/v1/object/'
            . $this->bucket . '/' . $filename;

        $content = file_get_contents($file['tmp_name']);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $this->apiKey,
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: ' . $mime
            ]
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($status !== 200 && $status !== 201) {
            throw new Exception('Gagal menyimpan gambar: ' . $response);
        }

        return $filename;
    }

    public function getImage(string $path)
    {
        return $this->supabaseUrl . '/storage/v1/object/public/' . $this->bucket . '/' . $path;
    }
}

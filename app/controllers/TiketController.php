<?php

require_once __DIR__ . '/../models/Tiket.php';

/**
 * Controller halaman "Tiket Saya" untuk peserta.
 */
class TiketController
{
    private Tiket $tiketModel;

    public const STATUS_VALID = ['aktif', 'tidak-tersedia', 'dibatalkan'];

    private const HARI = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
    ];

    private const BULAN = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    public function __construct(PDO $pdo)
    {
        $this->tiketModel = new Tiket($pdo);
    }

    /**
     * Daftar tiket peserta sesuai tab, kata kunci, dan kategori.
     */
    public function index(
        int $idPengguna,
        string $status = 'aktif',
        string $keyword = '',
        string $kategori = ''
    ): array {
        if (!in_array($status, self::STATUS_VALID, true)) {
            $status = 'aktif';
        }

        return $this->tiketModel->getByPengguna(
            $idPengguna,
            $status,
            trim($keyword),
            trim($kategori)
        );
    }

    /**
     * Jumlah tiket per tab.
     *
     * @return array<string, int>
     */
    public function jumlahPerStatus(int $idPengguna): array
    {
        return $this->tiketModel->countByStatus($idPengguna);
    }

    /**
     * @return array<int, string>
     */
    public function daftarKategori(int $idPengguna): array
    {
        return $this->tiketModel->kategoriPengguna($idPengguna);
    }

    /**
     * "Selasa, 10 Nov 2026 | 09:00 - 12:00 WIB"
     */
    public function formatWaktu(array $tiket): string
    {
        $mulai = new DateTime($tiket['tanggal_mulai'], new DateTimeZone('Asia/Jakarta'));
        $selesai = new DateTime($tiket['tanggal_selesai'], new DateTimeZone('Asia/Jakarta'));

        return sprintf(
            '%s, %d %s %s | %s - %s WIB',
            self::HARI[(int) $mulai->format('w')],
            (int) $mulai->format('j'),
            self::BULAN[(int) $mulai->format('n')],
            $mulai->format('Y'),
            $mulai->format('H:i'),
            $selesai->format('H:i')
        );
    }

    /**
     * "10 Nov 2026 | 09:00 WIB" untuk ringkasan kartu tiket.
     */
    public function formatTanggalSingkat(array $tiket): string
    {
        $mulai = new DateTime($tiket['tanggal_mulai'], new DateTimeZone('Asia/Jakarta'));

        return sprintf(
            '%d %s %s | %s WIB',
            (int) $mulai->format('j'),
            self::BULAN[(int) $mulai->format('n')],
            $mulai->format('Y'),
            $mulai->format('H:i')
        );
    }

    /**
     * URL gambar poster event, atau null kalau file belum tersedia.
     */
    public function posterUrl(array $tiket): ?string
    {
        $kandidat = [];

        if (!empty($tiket['poster'])) {
            $kandidat[] = 'assets/img/' . basename($tiket['poster']);
        }

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $kandidat[] = "assets/img/event-{$tiket['id_event']}.{$ext}";
        }

        foreach ($kandidat as $rel) {
            if (is_file(__DIR__ . '/../../' . $rel)) {
                return $rel;
            }
        }

        return null;
    }

    /**
     * Tautan "Simpan ke Google Calendar" untuk satu tiket.
     */
    public function googleCalendarUrl(array $tiket): string
    {
        $zonaJakarta = new DateTimeZone('Asia/Jakarta');
        $zonaUtc = new DateTimeZone('UTC');

        $mulai = new DateTime($tiket['tanggal_mulai'], $zonaJakarta);
        $selesai = new DateTime($tiket['tanggal_selesai'], $zonaJakarta);
        $mulai->setTimezone($zonaUtc);
        $selesai->setTimezone($zonaUtc);

        $params = [
            'action' => 'TEMPLATE',
            'text' => $tiket['nama_event'],
            'dates' => $mulai->format('Ymd\THis\Z') . '/' . $selesai->format('Ymd\THis\Z'),
            'details' => 'Tiket EventIn ' . $tiket['kode_qr'],
            'location' => $tiket['lokasi'],
        ];

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Tautan peta lokasi event.
     */
    public function mapsUrl(array $tiket): string
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($tiket['lokasi']);
    }

    /**
     * Label status pembayaran yang ramah dibaca.
     */
    public function labelStatusBayar(string $statusBayar): string
    {
        $status = strtolower(trim($statusBayar));

        if (str_starts_with($status, 'lunas')) {
            return 'Lunas';
        }

        if (str_starts_with($status, 'batal')) {
            return 'Dibatalkan';
        }

        if (str_starts_with($status, 'menunggu')) {
            return 'Menunggu Verifikasi';
        }

        return $statusBayar !== '' ? $statusBayar : 'Belum Bayar';
    }

    /**
     * Kelas CSS untuk badge status pembayaran.
     */
    public function kelasStatusBayar(string $statusBayar): string
    {
        $status = strtolower(trim($statusBayar));

        if (str_starts_with($status, 'lunas')) {
            return 'is-lunas';
        }

        if (str_starts_with($status, 'batal')) {
            return 'is-batal';
        }

        return 'is-menunggu';
    }

    /**
     * URL tab yang mempertahankan filter aktif.
     */
    public function tabUrl(string $tab, string $keyword, string $kategori): string
    {
        $params = ['page' => 'tiket', 'tab' => $tab];

        if ($keyword !== '') {
            $params['q'] = $keyword;
        }

        if ($kategori !== '') {
            $params['kategori'] = $kategori;
        }

        return '?' . http_build_query($params);
    }
}

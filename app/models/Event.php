
<?php
class Event
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $sql = "SELECT
                    e.id_event,
                    e.nama_event,
                    e.tanggal_mulai,
                    e.lokasi,
                    e.harga,
                    e.poster,
                    k.nama_kategori
                FROM events e
                JOIN kategori_event k
                    ON e.id_kategori = k.id_kategori
                ORDER BY e.tanggal_mulai ASC";

        return $this->pdo->query($sql)->fetchAll();
    }

    public function countEvent(): int
    {
        $sql = "SELECT COUNT(*) FROM events";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }

    public function mostRecentEvent(): array|null
    {
        $sql = "SELECT e.nama_event, e.tanggal_mulai,
        e.tanggal_mulai, e.lokasi,e.deskripsi, u.singkatan
        FROM events e
        JOIN admin_ukm ak ON e.id_pembuat = ak.id_admin_ukm
        JOIN ukm u ON ak.id_ukm = u.id_ukm
        ORDER BY id_event DESC LIMIT 1";
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $hari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        $tanggal = new DateTime($result['tanggal_mulai']);
        $namaHari = $hari[(int) $tanggal->format('w')];
        $result['hari'] = $namaHari;
        return $result;
    }



    public function insert(
        $id_pembuat,
        $id_kategori,
        $nama_event,
        $deskripsi,
        $tanggal_mulai,
        $tanggal_selesai,
        $lokasi,
        $kuota,
        $harga,
        $poster
    ) {

        // Validasi ID pembuat
        if (!filter_var($id_pembuat, FILTER_VALIDATE_INT) || $id_pembuat <= 0) {
            return [
                "success" => false,
                "message" => "ID pembuat tidak valid."
            ];
        }

        // Validasi kategori
        if (!filter_var($id_kategori, FILTER_VALIDATE_INT) || $id_kategori <= 0) {
            return [
                "success" => false,
                "message" => "Kategori event wajib dipilih."
            ];
        }

        // Validasi teks
        if (empty(trim($nama_event))) {
            return [
                "success" => false,
                "message" => "Nama event wajib diisi."
            ];
        }

        if (empty(trim($deskripsi))) {
            return [
                "success" => false,
                "message" => "Deskripsi event wajib diisi."
            ];
        }

        if (empty(trim($lokasi))) {
            return [
                "success" => false,
                "message" => "Lokasi event wajib diisi."
            ];
        }

        // Validasi tanggal
        try {
            $mulai = new DateTime($tanggal_mulai);
            $selesai = new DateTime($tanggal_selesai);
            $hariIni = new DateTime('today');
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => "Format tanggal event tidak valid."
            ];
        }

        if ($mulai < $hariIni) {
            return [
                "success" => false,
                "message" => "Tanggal mulai event tidak boleh sebelum hari ini."
            ];
        }

        if ($selesai <= $mulai) {
            return [
                "success" => false,
                "message" => "Tanggal selesai harus setelah tanggal mulai."
            ];
        }

        // Validasi kuota
        if (
            !filter_var($kuota, FILTER_VALIDATE_INT) ||
            $kuota <= 0
        ) {
            return [
                "success" => false,
                "message" => "Kuota harus berupa bilangan bulat lebih dari 0."
            ];
        }

        // Validasi harga
        if (!is_numeric($harga) || $harga < 0) {
            return [
                "success" => false,
                "message" => "Harga harus berupa angka dan tidak boleh negatif."
            ];
        }

        // Validasi poster
        if (empty(trim($poster))) {
            return [
                "success" => false,
                "message" => "Poster event wajib diunggah."
            ];
        }

        // Simpan ke database
        try {
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO events (
            id_pembuat, id_kategori, nama_event, deskripsi,
            tanggal_mulai, tanggal_selesai, lokasi, kuota, harga, poster
        ) VALUES (
            :id_pembuat, :id_kategori, :nama_event, :deskripsi,
            :tanggal_mulai, :tanggal_selesai, :lokasi, :kuota, :harga, :poster
        )";

            $query = $this->pdo->prepare($sql);

            $query->execute([
                'id_pembuat' => $id_pembuat,
                'id_kategori' => $id_kategori,
                'nama_event' => trim($nama_event),
                'deskripsi' => trim($deskripsi),
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai,
                'lokasi' => trim($lokasi),
                'kuota' => $kuota,
                'harga' => $harga,
                'poster' => $poster
            ]);

            $this->pdo->commit();

            return [
                "success" => true,
                "message" => "Event berhasil ditambahkan."
            ];
        } catch (PDOException $e) {

            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                "success" => false,
                "message" => "Gagal menambahkan event. Silakan coba lagi."
            ];
        }
    }
}

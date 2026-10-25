
declare(strict_types=1);
<?php
class User
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT *
                FROM pengguna
                WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUkms(): array
    {
        $sql = "SELECT id_ukm, nama, singkatan, deskripsi
                FROM ukm
                ORDER BY nama ASC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registerPengguna(
        string $nama,
        string $email,
        ?string $noHp,
        ?string $nim,
        string $passwordHash
    ): int {
        $sql = "INSERT INTO pengguna
                    (nama, email, no_hp, nim, password_hash, role)
                VALUES
                    (:nama, :email, :no_hp, :nim, :password_hash, :role)
                RETURNING id_pengguna";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'nama' => $nama,
            'email' => $email,
            'no_hp' => $noHp,
            'nim' => $nim,
            'password_hash' => $passwordHash,
            'role' => 'pengguna'
        ]);

        return (int) $stmt->fetchColumn();
    }

    public function registerAdmin(
        string $nama,
        string $email,
        ?string $noHp,
        ?string $nim,
        string $passwordHash,
        int $idUkm
    ): int {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO pengguna
                        (nama, email, no_hp, nim, password_hash, role)
                    VALUES
                        (:nama, :email, :no_hp, :nim, :password_hash, :role)
                    RETURNING id_pengguna";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'nama' => $nama,
                'email' => $email,
                'no_hp' => $noHp,
                'nim' => $nim,
                'password_hash' => $passwordHash,
                'role' => 'admin_ukm'
            ]);

            $idPengguna = (int) $stmt->fetchColumn();

            $sql = "INSERT INTO admin_ukm (id_pengguna, id_ukm)
                    VALUES (:id_pengguna, :id_ukm)";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                'id_pengguna' => $idPengguna,
                'id_ukm' => $idUkm
            ]);

            $this->db->commit();

            return $idPengguna;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}

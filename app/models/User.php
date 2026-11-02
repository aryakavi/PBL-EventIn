<?php
class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT *
                FROM pengguna
                WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);
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

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPeserta(): int
    {
        $sql = "SELECT COUNT(*) FROM pengguna WHERE role = 'pengguna'";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
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

        $stmt = $this->pdo->prepare($sql);

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
            $this->pdo->beginTransaction();

            $sql = "INSERT INTO pengguna
                        (nama, email, no_hp, nim, password_hash, role)
                    VALUES
                        (:nama, :email, :no_hp, :nim, :password_hash, :role)
                    RETURNING id_pengguna";

            $stmt = $this->pdo->prepare($sql);

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

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'id_pengguna' => $idPengguna,
                'id_ukm' => $idUkm
            ]);

            $this->pdo->commit();

            return $idPengguna;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}

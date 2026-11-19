<?php
class AdminUkm
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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
                'role' => 'admin'
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


<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $userModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel = new User($pdo);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getUkms(): array
    {
        return $this->userModel->getUkms();
    }

    public function registerPengguna(array $data): array
    {
        $nama = trim($data['nama'] ?? '');
        $email = trim($data['email'] ?? '');
        $noHp = trim($data['no_hp'] ?? '');
        $nim = trim($data['nim'] ?? '');
        $password = $data['password'] ?? '';
        $konfirmasi = $data['konfirmasi_password'] ?? '';

        if (
            $nama === '' ||
            $email === '' ||
            $password === '' ||
            $konfirmasi === ''
        ) {
            return [
                'success' => false,
                'message' => 'Nama, email, dan password wajib diisi.'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid.'
            ];
        }

        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'Password minimal 8 karakter.'
            ];
        }

        if ($password !== $konfirmasi) {
            return [
                'success' => false,
                'message' => 'Konfirmasi password tidak cocok.'
            ];
        }

        if ($this->userModel->findByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar.'
            ];
        }

        try {
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $this->userModel->registerPengguna(
                $nama,
                $email,
                $noHp !== '' ? $noHp : null,
                $nim !== '' ? $nim : null,
                $passwordHash
            );

            return [
                'success' => true,
                'message' => 'Registrasi berhasil. Silakan login.'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Registrasi gagal. Periksa kembali data Anda.'
            ];
        }
    }

    public function registerAdmin(array $data): array
    {
        $nama = trim($data['nama'] ?? '');
        $email = trim($data['email'] ?? '');
        $noHp = trim($data['no_hp'] ?? '');
        $nim = trim($data['nim'] ?? '');
        $password = $data['password'] ?? '';
        $konfirmasi = $data['konfirmasi_password'] ?? '';
        $idUkm = filter_var(
            $data['id_ukm'] ?? null,
            FILTER_VALIDATE_INT
        );

        if (
            $nama === '' ||
            $email === '' ||
            $password === '' ||
            $konfirmasi === ''
        ) {
            return [
                'success' => false,
                'message' => 'Nama, email, dan password wajib diisi.'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Format email tidak valid.'
            ];
        }

        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'Password minimal 8 karakter.'
            ];
        }

        if ($password !== $konfirmasi) {
            return [
                'success' => false,
                'message' => 'Konfirmasi password tidak cocok.'
            ];
        }

        if (!$idUkm || $idUkm < 1) {
            return [
                'success' => false,
                'message' => 'Silakan pilih UKM yang valid.'
            ];
        }

        if ($this->userModel->findByEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email sudah terdaftar.'
            ];
        }

        $ukms = $this->userModel->getUkms();
        $idUkms = array_column($ukms, 'id_ukm');

        if (!in_array($idUkm, $idUkms)) {
            return [
                'success' => false,
                'message' => 'UKM yang dipilih tidak ditemukan.'
            ];
        }

        try {
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $this->userModel->registerAdmin(
                $nama,
                $email,
                $noHp !== '' ? $noHp : null,
                $nim !== '' ? $nim : null,
                $passwordHash,
                $idUkm
            );

            return [
                'success' => true,
                'message' => 'Registrasi admin berhasil. Silakan login.'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Registrasi admin gagal. Periksa kembali data Anda.'
            ];
        }
    }

    public function login(string $email, string $password): array
    {
        $email = trim($email);

        if ($email === '' || $password === '') {
            return [
                'success' => false,
                'message' => 'Email dan password wajib diisi.'
            ];
        }

        $user = $this->userModel->findByEmail($email);

        if (
            !$user ||
            !password_verify($password, $user['password_hash'])
        ) {
            return [
                'success' => false,
                'message' => 'Email atau password salah.'
            ];
        }

        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id_pengguna' => $user['id_pengguna'],
            'nama' => $user['nama'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        return [
            'success' => true,
            'message' => 'Login berhasil.'
        ];
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}

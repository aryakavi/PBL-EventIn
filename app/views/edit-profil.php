<?php
$activeNav = 'profil';
$base = '../../';
require __DIR__ . '/../../includes/data.php';

$profil = [
    'nama'  => $_SESSION['user']['nama'] ?? $pembayaranPeserta['nama'],
    'email' => $_SESSION['user']['email'] ?? 'aryakavi@polinema.ac.id',
    'nim'   => $_SESSION['user']['nim'] ?? '240910291',
    'no_hp' => $_SESSION['user']['no_hp'] ?? '081234567890',
    'role'  => $_SESSION['user']['role'] ?? $pembayaranPeserta['role'],
];

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $nim   = trim($_POST['nim'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $noHp  = trim($_POST['no_hp'] ?? '');

    if ($nama === '' || $email === '') {
        $error = 'Nama lengkap dan email wajib diisi.';
        $profil = array_merge($profil, ['nama' => $nama, 'nim' => $nim, 'email' => $email, 'no_hp' => $noHp]);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
        $profil = array_merge($profil, ['nama' => $nama, 'nim' => $nim, 'email' => $email, 'no_hp' => $noHp]);
    } else {
        // Simpan ke sesi saja: data pada tabel Supabase tidak diubah.
        if (!isset($_SESSION['user'])) {
            $_SESSION['user'] = [
                'id_pengguna' => 1,
                'role'        => $profil['role'],
            ];
        }

        $_SESSION['user']['nama']  = $nama;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['nim']   = $nim;
        $_SESSION['user']['no_hp'] = $noHp;

        header('Location: profil.php?status=saved');
        exit;
    }
}

$profil['inisial'] = user_initials($profil['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Edit Profil';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<main class="profil-page">
    <nav class="profil-breadcrumb" aria-label="Breadcrumb">
        <a href="profil.php">Profil Anda</a>
        <span aria-hidden="true">/</span>
        <span>Edit Profil</span>
    </nav>

    <header class="profil-head">
        <h1>Edit Data Anda</h1>
        <p>Perbarui informasi akun peserta Anda dengan data yang valid.</p>
    </header>

    <?php if ($error !== ''): ?>
        <div class="profil-alert profil-alert--error" role="alert">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <section class="profil-card">
        <div class="profil-hero">
            <div class="profil-identity">
                <span class="profil-avatar"><?= e($profil['inisial']) ?></span>
                <div>
                    <h2><?= e($profil['nama']) ?></h2>
                    <span class="profil-role"><?= e(role_label($profil['role'])) ?></span>
                </div>
            </div>
            <a href="profil.php" class="btn btn-outline">Kembali ke Profil</a>
        </div>

        <form class="profil-form" method="post" action="">
            <div class="profil-grid">
                <div class="profil-field">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?= e($profil['nama']) ?>" required>
                </div>

                <div class="profil-field">
                    <label for="nim">Nomor Induk (NIM / NIP / NIK)</label>
                    <input type="text" id="nim" name="nim" value="<?= e($profil['nim']) ?>"
                           placeholder="Contoh: 2141720001">
                </div>

                <div class="profil-field">
                    <label for="email">Email Institusi</label>
                    <input type="email" id="email" name="email" value="<?= e($profil['email']) ?>" required>
                </div>

                <div class="profil-field">
                    <label for="no_hp">Nomor Telepon (WhatsApp)</label>
                    <input type="tel" id="no_hp" name="no_hp" value="<?= e($profil['no_hp']) ?>"
                           placeholder="Contoh: 08123456789">
                </div>
            </div>

            <p class="profil-note">Perubahan disimpan pada sesi akun Anda. Data pada tabel Supabase tidak diubah.</p>

            <div class="profil-actions">
                <a href="profil.php" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </section>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>
</html>

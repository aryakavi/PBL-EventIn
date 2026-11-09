<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'profil';

$profil = $organizer;
$sukses = false;
$error  = '';

// Muat data terbaru dari Supabase (read-only) untuk mengisi formulir.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !empty($organizer['id_pengguna'])) {
    ob_start();
    require_once __DIR__ . '/../../../includes/koneksi.php';
    ob_end_clean();

    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT nama, email, no_hp, nim FROM pengguna WHERE id_pengguna = :id LIMIT 1");
            $stmt->execute(['id' => $organizer['id_pengguna']]);
            $row = $stmt->fetch();

            if ($row) {
                $profil['nama']  = $row['nama'] !== '' ? $row['nama'] : $profil['nama'];
                $profil['email'] = $row['email'] ?: $profil['email'];
                $profil['no_hp'] = $row['no_hp'] ?: $profil['no_hp'];
                $profil['nim']   = $row['nim'] ?: $profil['nim'];
            }
        } catch (Throwable $e) {
            // Pakai data sesi.
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nim  = trim($_POST['nim'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $noHp = trim($_POST['no_hp'] ?? '');

    if ($nama === '' || $email === '') {
        $error = 'Nama lengkap dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $_SESSION['user']['nama']  = $nama;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['nim']   = $nim;
        $_SESSION['user']['no_hp'] = $noHp;

        $profil['nama']  = $nama;
        $profil['nim']   = $nim;
        $profil['email'] = $email;
        $profil['no_hp'] = $noHp;

        // Sinkronkan data navbar pada request yang sama.
        $organizer['nama']    = $nama;
        $organizer['email']   = $email;
        $organizer['inisial'] = user_initials($nama);

        $sukses = true;
    }
}

$profil['inisial'] = user_initials($profil['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Edit Profil Admin';
$assetBase  = '../../../assets';
$pageStyles = ['admin.css'];
require __DIR__ . '/../../../includes/head.php';
?>
</head>
<body>

<?php include __DIR__ . '/../../../includes/admin/navbar.php'; ?>

<div class="layout">
    <?php include __DIR__ . '/../../../includes/admin/sidebar.php'; ?>

    <main class="content">
        <nav class="admin-breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Dashboard</a>
            <span aria-hidden="true">/</span>
            <a href="profil.php">Profil Admin</a>
            <span aria-hidden="true">/</span>
            <span>Edit Profil</span>
        </nav>

        <div class="welcome">
            <div>
                <h1>Edit Data Admin Organisasi</h1>
                <p>Perbarui informasi akun penyelenggara event Anda.</p>
            </div>
        </div>

        <section class="profile-hero">
            <div class="profile-hero-id">
                <span class="profile-hero-avatar"><?= e($profil['inisial']) ?></span>
                <div>
                    <h2><?= e($profil['nama']) ?></h2>
                    <span class="profile-role">ADMIN ORGANISASI</span>
                </div>
            </div>
            <a href="profil.php" class="btn btn-outline">Kembali ke Profil</a>
        </section>

        <section class="form-card">
            <div class="form-card-header">
                <h2>Informasi Admin</h2>
                <p>Pastikan data yang Anda masukkan sesuai dengan dokumen resmi organisasi.</p>
            </div>

            <?php if ($sukses): ?>
                <div class="admin-alert admin-alert--success" role="status">
                    Perubahan profil berhasil disimpan.
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="admin-alert admin-alert--error" role="alert">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                               value="<?= e($profil['nama']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="nim">Nomor Induk Mahasiswa (NIM)</label>
                        <input type="text" id="nim" name="nim" class="form-control"
                               value="<?= e($profil['nim']) ?>" placeholder="Contoh: 240910291">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Institusi</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= e($profil['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">Nomor Telepon (WhatsApp)</label>
                        <input type="tel" id="no_hp" name="no_hp" class="form-control"
                               value="<?= e($profil['no_hp']) ?>" placeholder="Contoh: 08123456789">
                    </div>

                    <div class="form-group">
                        <label for="organisasi">Asal Organisasi</label>
                        <input type="text" id="organisasi" class="form-control"
                               value="<?= e($profil['organisasi']) ?>" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="profil.php" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>

<?php
$base      = '../../';
$activeNav = '';

require __DIR__ . '/../../includes/data.php';

// Anggap pengguna sudah login (auth asli belum tersambung).
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Jika form disimpan, perbarui data tampilan sementara lalu kembali ke mode lihat profil.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profil['nama_lengkap'] = trim($_POST['nama_lengkap'] ?? '') ?: $profil['nama_lengkap'];
    $profil['nomor_induk']  = trim($_POST['nomor_induk'] ?? '') ?: $profil['nomor_induk'];
    $profil['email']        = trim($_POST['email'] ?? '') ?: $profil['email'];
    $profil['telepon']      = trim($_POST['telepon'] ?? '') ?: $profil['telepon'];

    header('Location: profil.php');
    exit;
}

$isEdit = isset($_GET['mode']) && $_GET['mode'] === 'edit';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = $isEdit ? 'Edit Data Anda' : 'Profil Anda';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<!-- Navbar ringkas: tombol kembali & akun -->
<header class="navbar user-navbar">
    <div class="navbar-left">
        <a href="<?= e($base . $routes['home']) ?>" class="navbar-back" aria-label="Kembali"><?= icon('arrow-left') ?></a>
        <span class="logo-box">EI</span>
        <strong class="brand-name">EventIn</strong>
    </div>

    <div class="navbar-right">
        <div class="profile">
            <span class="profile-name"><?= e($profil['nama_singkat']) ?></span>
            <span class="profile-role"><?= e($profil['role']) ?></span>
        </div>
        <span class="avatar"><?= e($profil['inisial_nav']) ?></span>
        <div class="user-menu-wrap">
            <button class="user-menu-btn" type="button" aria-label="Menu akun" aria-expanded="false"><?= icon('menu') ?></button>
            <div class="user-menu-dropdown" hidden>
                <a href="profil.php" class="user-menu-item">Edit Profil</a>
                <a href="#" class="user-menu-item user-menu-item--danger">Logout</a>
            </div>
        </div>
    </div>
</header>

<main class="profile-container">
    <?php if (!$isEdit): ?>
        <!-- ================= TAMPILAN 1: LIHAT PROFIL ================= -->
        <div class="profile-page-header">
            <div>
                <h1>Profil Anda</h1>
                <p>Kelola Informasi Akun Anda</p>
            </div>
            <a href="?mode=edit" class="btn btn-primary btn-icon">
                <span>&#9998;</span> Edit Data
            </a>
        </div>

        <!-- Kartu Identitas Atas -->
        <section class="profile-banner-card">
            <div class="profile-banner-left">
                <span class="profile-initials-box"><?= e($profil['inisial_card']) ?></span>
                <h2 class="profile-banner-name"><?= e($profil['nama_lengkap']) ?></h2>
            </div>
        </section>

        <!-- Kartu Informasi Detail -->
        <section class="profile-detail-card">
            <h3 class="profile-card-title">Informasi Pribadi</h3>

            <dl class="profile-info-list">
                <div class="profile-info-row">
                    <dt>Nama Lengkap</dt>
                    <dd><?= e($profil['nama_lengkap']) ?></dd>
                </div>
                <div class="profile-info-row">
                    <dt>Nomor Induk (NIM/NIP/NIK)</dt>
                    <dd><?= e($profil['nomor_induk']) ?></dd>
                </div>
                <div class="profile-info-row">
                    <dt>Email Pribadi</dt>
                    <dd><?= e($profil['email']) ?></dd>
                </div>
                <div class="profile-info-row">
                    <dt>Nomor Telepon (WhatsApp)</dt>
                    <dd><?= e($profil['telepon']) ?></dd>
                </div>
            </dl>
        </section>

    <?php else: ?>
        <!-- ================= TAMPILAN 2: EDIT DATA PROFIL ================= -->
        <a href="profil.php" class="profile-back-link"><?= icon('arrow-left') ?> Kembali ke Profil</a>

        <div class="profile-page-header">
            <div>
                <h1>Edit Data Anda</h1>
                <p>Perbarui informasi pribadi dan kontak akun Anda.</p>
            </div>
        </div>

        <!-- Kartu Identitas Atas (Mode Edit) -->
        <section class="profile-banner-card">
            <div class="profile-banner-left">
                <span class="profile-initials-box"><?= e($profil['inisial_card']) ?></span>
                <h2 class="profile-banner-name"><?= e($profil['nama_lengkap']) ?></h2>
            </div>
            <span class="edit-mode-pill">&#9998; Mode edit</span>
        </section>

        <!-- Kartu Formulir Edit -->
        <section class="profile-detail-card">
            <div class="profile-form-header">
                <h3 class="profile-card-title">Informasi Pribadi</h3>
                <p>Pastikan data yang Anda masukkan benar dan dapat dihubungi.</p>
            </div>

            <form action="profil.php" method="post">
                <div class="profile-form-grid">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" class="profile-input"
                               value="<?= e($profil['nama_lengkap']) ?>"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="form-group">
                        <label for="nomor_induk">Nomor Induk (NIM/NIP/NIK)</label>
                        <input type="text" id="nomor_induk" name="nomor_induk" class="profile-input"
                               value="<?= e($profil['nomor_induk']) ?>"
                               placeholder="Masukkan NIM, NIP, atau NIK">
                    </div>

                    <div class="form-group">
                        <label for="email">Email Pribadi</label>
                        <input type="email" id="email" name="email" class="profile-input"
                               value="<?= e($profil['email']) ?>"
                               placeholder="Masukkan email pribadi">
                        <span class="field-hint">Gunakan alamat email pribadi Anda.</span>
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor Telepon (WhatsApp)</label>
                        <input type="text" id="telepon" name="telepon" class="profile-input"
                               value="<?= e($profil['telepon']) ?>"
                               placeholder="Masukkan nomor WhatsApp">
                        <span class="field-hint">Gunakan nomor aktif yang terhubung dengan WhatsApp.</span>
                    </div>
                </div>

                <!-- Kotak Petunjuk -->
                <div class="profile-form-notice">
                    <span class="form-notice-icon">i</span>
                    <span>Lengkapi kolom yang ingin diubah, lalu klik Simpan Perubahan untuk memperbarui profil.</span>
                </div>

                <!-- Footer Tombol Simpan & Batal -->
                <div class="profile-form-footer">
                    <span class="footer-note">Perubahan disimpan setelah Anda menekan tombol simpan.</span>
                    <div class="profile-form-actions">
                        <a href="profil.php" class="btn btn-outline">Batal</a>
                        <button type="submit" class="btn btn-primary btn-icon">
                            <span>&#128190;</span> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </section>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>

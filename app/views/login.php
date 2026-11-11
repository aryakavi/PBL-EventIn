<?php
$activeNav = '';
$base = '../../';
?>

<!DOCTYPE html>
<html lang="id">

<head>
<?php
$pageTitle  = 'Masuk Akun';
$assetBase  = '../../assets';
$pageStyles = ['style.css', 'auth.css'];
require __DIR__ . '/../../includes/head.php';
require __DIR__ . '/../../includes/icons.php';
?>
</head>

<body class="page-auth">

    <?php require __DIR__ . '/../../includes/navbar.php'; ?>
    <?php
    if (isset($_SESSION['registerMessage'])) { ?>
        <script>
            alert('Registrasi berhasil, silahkan login')
        </script>
    <?php unset($_SESSION['registerMessage']);
    }  ?>

    <main class="auth-page">
        <div class="auth-container auth-container--login">
            <div class="auth-login-grid">
                <section class="auth-card">
                    <h1 class="auth-title">Masuk ke EventIn</h1>
                    <p class="auth-subtitle">Kelola tiket event kampus, sertifikat SKPI, dan pendaftaran kegiatan akademik Anda.</p>
                    <?php if (isset($loginResult) && !$loginResult['success']): ?>
                        <p class="auth-error">
                            <?= e($loginResult['message']) ?>
                        </p>
                    <?php endif; ?>

                    <form class="auth-form" action="?page=login" method="POST">
                        <div class="field">
                            <label for="email">Email Institusi Kampus / Pribadi</label>
                            <div class="input-wrap">
                                <?= icon('card') ?>
                                <input type="email" id="email" name="email"
                                    placeholder="Mahasiswa@polinema.ac.id"
                                    autocomplete="email">
                            </div>
                        </div>

                        <div class="field">
                            <div class="label-row">
                                <label for="password">Kata Sandi</label>
                                <a class="link-orange" href="#">Lupa Kata Sandi?</a>
                            </div>
                            <div class="input-wrap input-wrap--toggle">
                                <?= icon('lock') ?>
                                <input type="password" id="password" name="password" placeholder="Masukkan minimal 8 karakter"
                                    autocomplete="current-password">
                                <button class="password-toggle" type="button" data-toggle-password="password"
                                    aria-label="Tampilkan kata sandi">
                                    <?= icon('eye') ?>
                                </button>
                            </div>
                        </div>

                        <label class="checkbox-row">
                            <input type="checkbox" name="remember" value="1">
                            <span>Ingat sesi saya di perangkat ini</span>
                        </label>

                        <button class="auth-submit" type="submit">
                            Masuk Sekarang
                            <?= icon('arrow-right') ?>
                        </button>                    </form>

                    <div class="auth-switch">
                        Belum memiliki akun?
                        <a class="link-orange" href="<?= e($base . ($routes['register'] ?? '#')) ?>">Daftar sebagai Peserta</a>
                        <span class="dot" aria-hidden="true">&bull;</span>
                        <a class="link-blue" href="<?= e($base . ($routes['register-admin'] ?? '#')) ?>">Daftarkan Admin</a>
                    </div>
                </section>

                <aside class="auth-aside">
                    <div class="showcase">
                        <h2>Satu Akun untuk Seluruh Pengalaman Akademik</h2>
                        <p>Sinkronisasi otomatis portofolio kegiatan kemahasiswaan, sertifikat digital tervalidasi kode unik, dan tiket elektronik instan.</p>
                    </div>

                    <div class="auth-stats">
                        <div class="auth-stat">
                            <span class="auth-stat-icon">
                                <?= icon('stat-users') ?>
                            </span>
                            <strong><?= isset($cardData) ? $cardData['jumlah_peserta'] : '' ?></strong>
                            <span>Mahasiswa Aktif Terdaftar</span>
                        </div>

                        <div class="auth-stat">
                            <span class="auth-stat-icon">
                                <?= icon('calendar') ?>
                            </span>
                            <strong><?= isset($cardData) ? $cardData['jumlah_event'] : '' ?></strong>
                            <span>Event &amp; Seminar Kampus</span>
                        </div>
                    </div>

                    <div class="auth-note">
                        <span class="auth-note-icon">
                            <?= icon('note') ?>
                        </span>
                        <div>
                            <strong>Pengurus BEM / Himpunan?</strong>
                            <p>Kelola presensi QR dan cetak rekap kehadiran langsung via dashboard mitra.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>

</body>

</html>
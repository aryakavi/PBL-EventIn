<?php
require __DIR__ . '/../../includes/data.php';
$activeNav = '';
$base = '../../';
?>

<!DOCTYPE html>
<html lang="id">

<head>
<?php
$pageTitle  = 'Daftar sebagai Admin Organisasi';
$assetBase  = '../../assets';
$pageStyles = ['style.css', 'auth.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>

<body class="page-auth">

    <?php require __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="auth-page">
        <div class="auth-container auth-container--wide">
            <section class="auth-banner">
                <h1>Satu Akun untuk Seluruh Pengalaman Akademik</h1>
                <p>Sinkronisasi otomatis portofolio kegiatan kemahasiswaan, sertifikat digital tervalidasi kode unik, dan tiket elektronik instan.</p>
            </section>

            <section class="auth-card auth-card--wide">
                <h1 class="auth-title">Daftar sebagai Admin Organisasi</h1>
                <p class="auth-subtitle">Kelola tiket event kampus, sertifikat SKPI, dan pendaftaran kegiatan akademik Anda.</p>

                <form class="auth-form">
                    <div class="field">
                        <label for="nama">Nama Lengkap</label>
                        <div class="input-wrap">
                            <?= icon('user') ?>
                            <input type="text" id="nama" name="nama"
                                placeholder="Raditya Arya Maulana" autocomplete="name">
                        </div>
                    </div>

                    <div class="field">
                        <label for="nim">Nomor Induk (NIM / NIP / NIK)</label>
                        <div class="input-wrap">
                            <?= icon('card') ?>
                            <input type="text" id="nim" name="nim"
                                placeholder="2141720001" autocomplete="off">
                        </div>
                    </div>

                    <div class="field">
                        <label for="email">Email Institusi Kampus / Pribadi</label>
                        <div class="input-wrap">
                            <?= icon('email') ?>
                            <input type="email" id="email" name="email"
                                placeholder="mahasiswa@polinema.ac.id"
                                autocomplete="email">
                        </div>
                    </div>

                    <div class="field">
                        <label for="no_hp">No. Telepon (Whatsapp)</label>
                        <div class="input-wrap">
                            <?= icon('phone') ?>
                            <input type="tel" id="no_hp" name="no_hp"
                                placeholder="08123456789" autocomplete="tel">
                        </div>
                    </div>

                    <div class="field">
                        <label for="organisasi">Asal Organisasi</label>
                        <div class="input-wrap">
                            <?= icon('building') ?>
                            <select id="organisasi" name="organisasi">
                                <option value="" selected disabled>Pilih organisasi..</option>
                                <option value="BEM">BEM (Badan Eksekutif Mahasiswa)</option>
                                <option value="DPM">DPM (Dewan Perwakilan Mahasiswa)</option>
                                <option value="HIMA">HIMA (Himpunan Mahasiswa Jurusan)</option>
                                <option value="UKM">UKM (Unit Kegiatan Mahasiswa)</option>
                                <option value="Komunitas">Komunitas / Club</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Kata Sandi</label>
                        <div class="input-wrap input-wrap--toggle">
                            <?= icon('lock') ?>
                            <input type="password" id="password" name="password" placeholder="Masukkan minimal 8 karakter"
                                autocomplete="new-password">
                            <button class="password-toggle" type="button" data-toggle-password="password"
                                aria-label="Tampilkan kata sandi">
                                <?= icon('eye') ?>
                            </button>
                        </div>
                    </div>

                    <div class="field">
                        <label for="konfirmasi_password">Ulangi Kata Sandi</label>
                        <div class="input-wrap input-wrap--toggle">
                            <?= icon('lock') ?>
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password"
                                placeholder="Masukkan ulang minimal 8 karakter" autocomplete="new-password">
                            <button class="password-toggle" type="button" data-toggle-password="konfirmasi_password"
                                aria-label="Tampilkan ulangi kata sandi">
                                <?= icon('eye') ?>
                            </button>
                        </div>
                    </div>

                    <label class="checkbox-row">
                        <input type="checkbox" name="remember" value="1">
                        <span>Ingat sesi saya di perangkat ini</span>
                    </label>

                    <button class="auth-submit auth-submit--blue" type="submit">
                        Daftar Sekarang
                        <?= icon('arrow-right') ?>
                    </button>
                </form>

                <div class="auth-switch">
                    Sudah memiliki akun?
                    <a class="link-blue" href="<?= e($base . ($routes['login'] ?? '#')) ?>">Masuk di sini</a>
                    <span class="dot" aria-hidden="true">&bull;</span>
                    <a class="link-orange" href="<?= e($base . ($routes['register'] ?? '#')) ?>">Daftar sebagai Peserta</a>
                </div>
            </section>

            <div class="auth-stats auth-stats--wide">
                <div class="auth-stat">
                    <span class="auth-stat-icon">
                        <?= icon('stat-users') ?>
                    </span>
                    <strong>10.000+</strong>
                    <span>Mahasiswa Aktif Terdaftar</span>
                </div>

                <div class="auth-stat">
                    <span class="auth-stat-icon">
                        <?= icon('calendar') ?>
                    </span>
                    <strong>500+</strong>
                    <span>Event &amp; Seminar Kampus</span>
                </div>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>
</body>

</html>

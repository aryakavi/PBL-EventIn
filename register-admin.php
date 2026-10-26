<?php

require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/koneksi.php';
require __DIR__ . '/app/controllers/AuthController.php';

$auth = new AuthController($pdo);
$ukms = $auth->getUkms();

$error = '';
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    $result = $auth->registerAdmin($_POST);

    if ($result['success']) {
        header('Location: login.php?status=registered');
        exit;
    }

    $error = $result['message'];
}

$pageTitle = 'Daftar sebagai Admin Organisasi - EventIn';
$activeNav = '';
require __DIR__ . '/includes/auth-header.php';

?>

<main class="auth-page">
    <div class="auth-container auth-container--wide">
        <section class="auth-banner">
            <h1>Satu Akun untuk Seluruh Pengalaman Akademik</h1>
            <p>Sinkronisasi otomatis portofolio kegiatan kemahasiswaan, sertifikat digital tervalidasi kode unik, dan tiket elektronik instan.</p>
        </section>

        <section class="auth-card auth-card--wide">
            <span class="auth-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                    <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                </svg>
                Autentikasi Kampus Terpadu
            </span>

            <h1 class="auth-title">Daftar sebagai Admin Organisasi</h1>
            <p class="auth-subtitle">Kelola tiket event kampus, sertifikat SKPI, dan pendaftaran kegiatan akademik Anda.</p>

            <?php if ($error !== ''): ?>
                <div class="auth-alert auth-alert--error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"></circle>
                        <path d="M12 8v5M12 16h.01"></path>
                    </svg>
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post">
                <div class="field">
                    <label for="nama">Nama Lengkap</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M5 21a7 7 0 0 1 14 0"></path>
                        </svg>
                        <input type="text" id="nama" name="nama" value="<?= e($old['nama'] ?? '') ?>"
                            placeholder="contoh: Raditya Arya Maulana" autocomplete="name" required>
                    </div>
                </div>

                <div class="field">
                    <label for="nim">Nomor Induk (NIM / NIP / NIK)</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <circle cx="8" cy="11" r="2"></circle>
                            <path d="M5.5 16a2.5 2.5 0 0 1 5 0M14 10h4M14 14h4"></path>
                        </svg>
                        <input type="text" id="nim" name="nim" value="<?= e($old['nim'] ?? '') ?>"
                            placeholder="contoh: 2141720001" autocomplete="off" required>
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email Institusi Kampus / Pribadi</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                            <path d="m3 7 9 6 9-6"></path>
                        </svg>
                        <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>"
                            placeholder="contoh: mahasiswa@polinema.ac.id atau kudalompat@gmail.com"
                            autocomplete="email" required>
                    </div>
                </div>

                <div class="field">
                    <label for="no_hp">No. Telepon (Whatsapp)</label>
                    <div class="input-wrap">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.9a2 2 0 0 1-.4 2.1L8.1 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.9.6 2.9.7a2 2 0 0 1 1.6 1.9Z"></path>
                        </svg>
                        <input type="tel" id="no_hp" name="no_hp" value="<?= e($old['no_hp'] ?? '') ?>"
                            placeholder="contoh: 08123456789" autocomplete="tel">
                    </div>
                </div>

                <div class="field">
                    <label for="id_ukm">Asal Organisasi</label>
                    <div class="input-wrap input-wrap--select">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                            <path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1"></path>
                        </svg>
                        <select id="id_ukm" name="id_ukm" required>
                            <option value="">Pilih asal organisasi</option>
                            <?php foreach ($ukms as $ukm): ?>
                                <option value="<?= (int) $ukm['id_ukm'] ?>"
                                    <?= (string) ($old['id_ukm'] ?? '') === (string) $ukm['id_ukm'] ? 'selected' : '' ?>>
                                    <?= e($ukm['nama']) ?><?= $ukm['singkatan'] ? ' (' . e($ukm['singkatan']) . ')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <div class="label-row">
                        <label for="password">Kata Sandi</label>
                        <a class="link-orange" href="#">Lupa Kata Sandi?</a>
                    </div>
                    <div class="input-wrap input-wrap--toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="8" cy="15" r="4"></circle>
                            <path d="m11 12 9-9M17 6l2 2M14 9l2 2"></path>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="Masukkan minimal 8 karakter"
                            autocomplete="new-password" required>
                        <button class="password-toggle" type="button" data-toggle-password="password"
                            aria-label="Tampilkan kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="field">
                    <div class="label-row">
                        <label for="konfirmasi_password">Ulangi Kata Sandi</label>
                        <a class="link-orange" href="#">Lupa Kata Sandi?</a>
                    </div>
                    <div class="input-wrap input-wrap--toggle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="8" cy="15" r="4"></circle>
                            <path d="m11 12 9-9M17 6l2 2M14 9l2 2"></path>
                        </svg>
                        <input type="password" id="konfirmasi_password" name="konfirmasi_password"
                            placeholder="Masukkan ulang minimal 8 karakter" autocomplete="new-password" required>
                        <button class="password-toggle" type="button" data-toggle-password="konfirmasi_password"
                            aria-label="Tampilkan ulangi kata sandi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="checkbox-row">
                    <input type="checkbox" name="remember" value="1" checked>
                    <span>Ingat sesi saya di perangkat ini</span>
                </label>

                <button class="auth-submit auth-submit--blue" type="submit">
                    Daftar Sekarang
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6"></path>
                    </svg>
                </button>
            </form>

            <div class="auth-switch">
                Sudah memiliki akun?
                <a class="link-blue" href="<?= e($routes['login']) ?>">Masuk di sini</a>
                <span class="dot" aria-hidden="true">&bull;</span>
                <a class="link-orange" href="<?= e($routes['register']) ?>">Daftar sebagai Peserta</a>
            </div>
        </section>

        <div class="auth-stats auth-stats--wide">
            <div class="auth-stat">
                <span class="auth-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <strong>10.000+</strong>
                <span>Mahasiswa Aktif Terdaftar</span>
            </div>

            <div class="auth-stat">
                <span class="auth-stat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="17" rx="2"></rect>
                        <path d="M16 2v4M8 2v4M3 10h18"></path>
                    </svg>
                </span>
                <strong>500+</strong>
                <span>Event &amp; Seminar Kampus</span>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/includes/auth-footer.php'; ?>

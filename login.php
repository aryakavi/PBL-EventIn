<?php

require __DIR__ . '/includes/data.php';
require __DIR__ . '/includes/koneksi.php';
require __DIR__ . '/app/controllers/AuthController.php';

$auth = new AuthController($pdo);

$error = '';
$notice = '';
$oldEmail = '';

if (($_GET['status'] ?? '') === 'registered') {
    $notice = 'Registrasi berhasil. Silakan masuk dengan akun baru Anda.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldEmail = trim($_POST['email'] ?? '');
    $result = $auth->login($oldEmail, $_POST['password'] ?? '');

    if ($result['success']) {
        if (!empty($_POST['remember'])) {
            $params = session_get_cookie_params();

            setcookie(session_name(), session_id(), [
                'expires' => time() + 60 * 60 * 24 * 30,
                'path' => $params['path'] ?: '/',
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        header('Location: index.php');
        exit;
    }

    $error = $result['message'];
}

$pageTitle = 'Masuk Akun - EventIn';
$activeNav = '';
require __DIR__ . '/includes/auth-header.php';

?>

<main class="auth-page">
    <div class="auth-container auth-container--login">
        <div class="auth-login-grid">
            <section class="auth-card">
                <span class="auth-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                    </svg>
                    Autentikasi Kampus Terpadu
                </span>

                <h1 class="auth-title">Masuk ke EventIn</h1>
                <p class="auth-subtitle">Kelola tiket event kampus, sertifikat SKPI, dan pendaftaran kegiatan akademik Anda.</p>

                <?php if ($notice !== ''): ?>
                    <div class="auth-alert auth-alert--success" role="status">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="m5 12 4 4L19 6"></path>
                        </svg>
                        <?= e($notice) ?>
                    </div>
                <?php endif; ?>

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
                        <label for="email">Email Institusi Kampus / Pribadi</label>
                        <div class="input-wrap">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                <circle cx="8" cy="11" r="2"></circle>
                                <path d="M5.5 16a2.5 2.5 0 0 1 5 0M14 10h4M14 14h4"></path>
                            </svg>
                            <input type="email" id="email" name="email" value="<?= e($oldEmail) ?>"
                                placeholder="contoh: mahasiswa@polinema.ac.id atau kudalompat@gmail.com"
                                autocomplete="email" required>
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
                                autocomplete="current-password" required>
                            <button class="password-toggle" type="button" data-toggle-password="password"
                                aria-label="Tampilkan kata sandi">
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

                    <button class="auth-submit" type="submit">
                        Masuk Sekarang
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"></path>
                        </svg>
                    </button>
                </form>

                <div class="auth-switch">
                    Belum memiliki akun?
                    <a class="link-orange" href="<?= e($routes['register']) ?>">Daftar sebagai Peserta</a>
                    <span class="dot" aria-hidden="true">&bull;</span>
                    <a class="link-blue" href="<?= e($routes['register_admin']) ?>">Daftarkan Organisasi</a>
                </div>
            </section>

            <aside class="auth-aside">
                <div class="showcase">
                    <span class="showcase-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M12 3l7 3v5c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6l7-3Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                        Terkredibilitas SKPI &amp; Terhubung Kampus
                    </span>

                    <h2>Satu Akun untuk Seluruh Pengalaman Akademik</h2>
                    <p>Sinkronisasi otomatis portofolio kegiatan kemahasiswaan, sertifikat digital tervalidasi kode unik, dan tiket elektronik instan.</p>

                    <div class="mini-ticket">
                        <div class="mini-ticket-top">
                            <span class="mini-ticket-label">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <path d="M3 9h18M8 5v4M16 5v4"></path>
                                </svg>
                                E-Tiket &amp; SKPI Resmi
                            </span>
                            <span class="verified-badge">Terverifikasi</span>
                        </div>
                        <hr class="mini-ticket-line">
                        <div class="mini-ticket-body">
                            <div>
                                <h3>Simposium Nasional AI &amp; Big Data</h3>
                                <p>Politeknik Negeri Malang</p>
                            </div>
                            <span class="mini-qr" aria-hidden="true">
                                <svg viewBox="0 0 32 32" fill="currentColor">
                                    <path d="M4 4h9v9H4V4Zm2 2v5h5V6H6Zm13-2h9v9h-9V4Zm2 2v5h5V6h-5ZM4 19h9v9H4v-9Zm2 2v5h5v-5H6Zm13-2h3v3h-3v-3Zm5 0h4v3h-4v-3Zm-5 5h4v4h-4v-4Zm6 3h2v2h-2v-2Z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="auth-stats">
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

                <div class="auth-note">
                    <span class="auth-note-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="4" y="3" width="16" height="18" rx="2"></rect>
                            <path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1"></path>
                        </svg>
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

<?php require __DIR__ . '/includes/auth-footer.php'; ?>

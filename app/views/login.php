<?php
require __DIR__ . '/../../includes/data.php';
$activeNav = '';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk Akun - EventIn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=Geist+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>

<body class="page-auth">

    <?php require __DIR__ . '/../../includes/navbar.php'; ?>

    <main class="auth-page">
        <div class="auth-container auth-container--login">
            <div class="auth-login-grid">
                <section class="auth-card">
                    <h1 class="auth-title">Masuk ke EventIn</h1>
                    <p class="auth-subtitle">Kelola tiket event kampus, sertifikat SKPI, dan pendaftaran kegiatan akademik Anda.</p>

                    <form class="auth-form">
                        <div class="field">
                            <label for="email">Email Institusi Kampus / Pribadi</label>
                            <div class="input-wrap">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                                    <circle cx="8" cy="11" r="2"></circle>
                                    <path d="M5.5 16a2.5 2.5 0 0 1 5 0M14 10h4M14 14h4"></path>
                                </svg>
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
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <circle cx="8" cy="15" r="4"></circle>
                                    <path d="m11 12 9-9M17 6l2 2M14 9l2 2"></path>
                                </svg>
                                <input type="password" id="password" name="password" placeholder="Masukkan minimal 8 karakter"
                                    autocomplete="current-password">
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
                            <input type="checkbox" name="remember" value="1">
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
                        <a class="link-orange" href="<?= e($routes['register'] ?? '#') ?>">Daftar sebagai Peserta</a>
                        <span class="dot" aria-hidden="true">&bull;</span>
                        <a class="link-blue" href="#">Daftarkan Organisasi</a>
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

    <?php require __DIR__ . '/../../includes/footer.php'; ?>

    <script src="../../assets/js/main.js"></script>
</body>

</html>
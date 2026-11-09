<?php
require __DIR__ . '/../../../includes/admin/data.php';
$halamanAktif = 'profil';

$profil = $organizer;

// Ambil data terbaru dari Supabase (read-only). Jika koneksi gagal, pakai data sesi/contoh.
ob_start();
require_once __DIR__ . '/../../../includes/koneksi.php';
ob_end_clean();

if (isset($pdo) && !empty($organizer['id_pengguna'])) {
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

        $stmt = $pdo->prepare("SELECT u.nama_ukm, u.singkatan
                               FROM admin_ukm a
                               JOIN ukm u ON u.id_ukm = a.id_ukm
                               WHERE a.id_pengguna = :id
                               LIMIT 1");
        $stmt->execute(['id' => $organizer['id_pengguna']]);
        $ukm = $stmt->fetch();

        if ($ukm) {
            $profil['organisasi'] = $ukm['singkatan']
                ? $ukm['nama_ukm'] . ' (' . $ukm['singkatan'] . ')'
                : $ukm['nama_ukm'];
        }
    } catch (Throwable $e) {
        // Biarkan data sesi/contoh dipakai.
    }
}

$profil['inisial'] = user_initials($profil['nama']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Profil Admin Organisasi';
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
        <div class="welcome">
            <div>
                <h1>Profil Admin Organisasi</h1>
                <p>Kelola informasi akun dan data organisasi yang Anda wakili di EventIn.</p>
            </div>
            <a href="index.php" class="btn btn-outline">Kembali ke Dashboard</a>
        </div>

        <section class="profile-hero">
            <div class="profile-hero-id">
                <span class="profile-hero-avatar"><?= e($profil['inisial']) ?></span>
                <div>
                    <h2><?= e($profil['nama']) ?></h2>
                    <span class="profile-role">ADMIN ORGANISASI</span>
                </div>
            </div>
            <a href="edit-profil.php" class="btn btn-primary">Edit Profil</a>
        </section>

        <section class="form-card">
            <div class="form-card-header">
                <h2>Informasi Admin</h2>
                <p>Data pribadi yang digunakan untuk verifikasi akun penyelenggara.</p>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <span class="info-label">NAMA LENGKAP</span>
                    <strong class="info-value"><?= e($profil['nama']) ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">NOMOR INDUK MAHASISWA (NIM)</span>
                    <strong class="info-value"><?= e($profil['nim'] !== '' ? $profil['nim'] : '-') ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">EMAIL INSTITUSI</span>
                    <strong class="info-value"><?= e($profil['email'] !== '' ? $profil['email'] : '-') ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">NOMOR TELEPON</span>
                    <strong class="info-value"><?= e($profil['no_hp'] !== '' ? $profil['no_hp'] : '-') ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">ASAL ORGANISASI</span>
                    <strong class="info-value"><?= e($profil['organisasi']) ?></strong>
                </div>
                <div class="info-item">
                    <span class="info-label">STATUS AKUN</span>
                    <strong class="info-value">Aktif &amp; Terverifikasi</strong>
                </div>
            </div>
        </section>
    </main>
</div>

<div class="sidebar-overlay" data-sidebar-close></div>

<script src="../../../assets/js/admin.js?v=<?= e(asset_version('assets/js/admin.js')) ?>"></script>
</body>
</html>

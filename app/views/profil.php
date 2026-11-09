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

// Ambil data terbaru dari Supabase (read-only) bila pengguna sudah login.
ob_start();
require_once __DIR__ . '/../../includes/koneksi.php';
ob_end_clean();

if (isset($pdo) && !empty($_SESSION['user']['id_pengguna'])) {
    try {
        $stmt = $pdo->prepare("SELECT nama, email, no_hp, nim FROM pengguna WHERE id_pengguna = :id LIMIT 1");
        $stmt->execute(['id' => (int) $_SESSION['user']['id_pengguna']]);
        $row = $stmt->fetch();

        if ($row) {
            $profil['nama']  = $row['nama'] !== '' ? $row['nama'] : $profil['nama'];
            $profil['email'] = $row['email'] ?: $profil['email'];
            $profil['no_hp'] = $row['no_hp'] ?: $profil['no_hp'];
            $profil['nim']   = $row['nim'] ?: $profil['nim'];
        }
    } catch (Throwable $e) {
        // Pakai data sesi/contoh bila koneksi gagal.
    }
}

$profil['inisial'] = user_initials($profil['nama']);
$tersimpan = ($_GET['status'] ?? '') === 'saved';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<?php
$pageTitle  = 'Profil Anda';
$assetBase  = '../../assets';
$pageStyles = ['style.css'];
require __DIR__ . '/../../includes/head.php';
?>
</head>
<body>

<?php require __DIR__ . '/../../includes/navbar.php'; ?>

<main class="profil-page">
    <header class="profil-head">
        <h1>Profil Anda</h1>
        <p>Kelola informasi akun peserta dan data kontak Anda.</p>
    </header>

    <?php if ($tersimpan): ?>
        <div class="profil-alert profil-alert--success" role="status">
            Perubahan profil berhasil disimpan.
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
            <a href="edit-profil.php" class="btn btn-primary">Edit Profil</a>
        </div>

        <dl class="profil-rows">
            <div class="profil-row">
                <dt>Nama Lengkap</dt>
                <dd><?= e($profil['nama']) ?></dd>
            </div>
            <div class="profil-row">
                <dt>Nomor Induk (NIM / NIP / NIK)</dt>
                <dd><?= e($profil['nim'] !== '' ? $profil['nim'] : '-') ?></dd>
            </div>
            <div class="profil-row">
                <dt>Email Institusi</dt>
                <dd><?= e($profil['email'] !== '' ? $profil['email'] : '-') ?></dd>
            </div>
            <div class="profil-row">
                <dt>Nomor Telepon (WhatsApp)</dt>
                <dd><?= e($profil['no_hp'] !== '' ? $profil['no_hp'] : '-') ?></dd>
            </div>
            <div class="profil-row">
                <dt>Status Akun</dt>
                <dd>Aktif &amp; Terverifikasi</dd>
            </div>
        </dl>
    </section>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js"></script>
</body>
</html>

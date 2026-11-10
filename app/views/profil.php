<?php
$activeNav = 'profil';
$base = '../../';
require __DIR__ . '/../../includes/data.php';
require __DIR__ . '/../../includes/koneksi.php';
require __DIR__ . '/../../app/models/SiteRepository.php';

$emptyText = 'kosong, masih kurang';

$sesi = $_SESSION['user'] ?? null;
if (!is_array($sesi) || empty($sesi['id_pengguna'])) {
    header('Location: ' . $base . $routes['login']);
    exit;
}

$profil = [
    'nama'  => (string) ($sesi['nama'] ?? ''),
    'email' => (string) ($sesi['email'] ?? ''),
    'nim'   => (string) ($sesi['nim'] ?? ''),
    'no_hp' => (string) ($sesi['no_hp'] ?? ''),
    'role'  => (string) ($sesi['role'] ?? 'pengguna'),
];

// Ambil data terbaru dari Supabase (read-only) bila pengguna sudah login.
if (isset($pdo) && $pdo instanceof PDO) {
    $row = (new SiteRepository($pdo))->pengguna((int) $sesi['id_pengguna']);

    if ($row) {
        $profil['nama']  = $row['nama'] !== '' ? $row['nama'] : $profil['nama'];
        $profil['email'] = $row['email'] ?: $profil['email'];
        $profil['no_hp'] = $row['no_hp'] ?: $profil['no_hp'];
        $profil['nim']   = $row['nim'] ?: $profil['nim'];
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
                <dd><?= e($profil['nama'] !== '' ? $profil['nama'] : $emptyText) ?></dd>
            </div>
            <div class="profil-row">
                <dt>Nomor Induk (NIM / NIP / NIK)</dt>
                <dd><?= e($profil['nim'] !== '' ? $profil['nim'] : $emptyText) ?></dd>
            </div>
            <div class="profil-row">
                <dt>Email Institusi</dt>
                <dd><?= e($profil['email'] !== '' ? $profil['email'] : $emptyText) ?></dd>
            </div>
            <div class="profil-row">
                <dt>Nomor Telepon (WhatsApp)</dt>
                <dd><?= e($profil['no_hp'] !== '' ? $profil['no_hp'] : $emptyText) ?></dd>
            </div>
            <div class="profil-row">
                <dt>Status Akun</dt>
                <dd>Aktif &amp; Terverifikasi</dd>
            </div>
        </dl>
    </section>
</main>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../assets/js/main.js?v=<?= e(asset_version('assets/js/main.js')) ?>"></script>
</body>
</html>

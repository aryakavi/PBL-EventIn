<?php
// Variabel yang di-set sebelum include:
//   $pageTitle  - judul halaman tanpa suffix (default 'EventIn')
//   $assetBase  - path relatif ke folder assets/ (mis. '../../assets')
//   $pageStyles - array nama file CSS di assets/css/ (mis. ['style.css', 'auth.css'])
$pageTitle  = $pageTitle ?? 'EventIn';
$pageStyles = $pageStyles ?? ['style.css'];
$assetBase  = $assetBase ?? 'assets';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | EventIn</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
<?php foreach ($pageStyles as $css): ?>
<link rel="stylesheet" href="<?= e($assetBase) ?>/css/<?= e($css) ?>">
<?php endforeach; ?>

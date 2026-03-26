<?php
// shared.php — Public file access via share token
require_once 'config.php';

$token = trim($_GET['token'] ?? '');

$sharesFile = __DIR__ . '/data/shares.json';
$shares     = (file_exists($sharesFile)) ? json_decode(file_get_contents($sharesFile), true) : [];

$error = '';
$file  = null;

if (empty($token)) {
    $error = 'Invalid share link.';
} elseif (empty($shares[$token])) {
    $error = 'Share link not found or has been revoked.';
} else {
    $info     = $shares[$token];
    $fullPath = realpath(UPLOAD_DIR . $info['path']);
    $uploadRoot = realpath(UPLOAD_DIR);
    if (!$fullPath || strpos($fullPath, $uploadRoot) !== 0 || !is_file($fullPath)) {
        $error = 'File no longer exists.';
    } else {
        $file = $info;
        $file['full_path'] = $fullPath;
        $file['size']      = filesize($fullPath);
        $file['mime']      = mime_content_type($fullPath);
        $file['modified']  = filemtime($fullPath);
    }
}

// Handle download action
if (!$error && isset($_GET['dl'])) {
    header('Content-Type: ' . $file['mime']);
    header('Content-Disposition: attachment; filename="' . basename($file['full_path']) . '"');
    header('Content-Length: ' . $file['size']);
    readfile($file['full_path']);
    exit;
}

function fmtSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes/1073741824,2).' GB';
    if ($bytes >= 1048576)    return number_format($bytes/1048576,2).' MB';
    if ($bytes >= 1024)       return number_format($bytes/1024,2).' KB';
    return $bytes.' bytes';
}
?><!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TemboDrive — Shared File</title>
<link rel="stylesheet" href="assets/css/style.css">
<style>
  .share-page { display:flex; align-items:center; justify-content:center; min-height:100vh; padding:2rem; }
  .share-card { background:var(--navy-800); border:1px solid var(--border-md); border-radius:var(--radius); padding:2.5rem 2rem; max-width:480px; width:100%; text-align:center; box-shadow:var(--shadow); }
  .share-icon { font-size:3.5rem; margin-bottom:1rem; }
  .share-name { font-size:1.1rem; font-weight:700; color:var(--text-100); margin-bottom:.4rem; word-break:break-all; }
  .share-meta { font-size:.8rem; color:var(--text-400); margin-bottom:2rem; display:flex; justify-content:center; gap:1.5rem; }
  .share-error { color:var(--danger); font-size:.95rem; margin-top:1rem; }
  .share-brand { margin-top:2rem; font-size:.75rem; color:var(--text-400); }
  .share-brand a { color:var(--purple-300); text-decoration:none; }
</style>
</head>
<body>
<div class="share-page">
  <div class="share-card">
    <div class="nav-logo" style="margin:0 auto 1.5rem;width:48px;height:48px;font-size:1.5rem">🐘</div>

    <?php if ($error): ?>
      <div class="share-icon">⚠️</div>
      <p class="share-error"><?php echo htmlspecialchars($error); ?></p>
    <?php else: ?>
      <?php
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $icons = ['pdf'=>'📄','jpg'=>'🖼️','jpeg'=>'🖼️','png'=>'🖼️','gif'=>'🖼️','webp'=>'🖼️',
                  'mp4'=>'🎥','avi'=>'🎥','mov'=>'🎥','mp3'=>'🎵','wav'=>'🎵',
                  'zip'=>'📦','rar'=>'📦','doc'=>'📝','docx'=>'📝','xls'=>'📊','xlsx'=>'📊',
                  'txt'=>'📄','js'=>'📄','php'=>'📄','json'=>'📄'];
        $icon = $icons[$ext] ?? '📄';
      ?>
      <div class="share-icon"><?php echo $icon; ?></div>
      <div class="share-name"><?php echo htmlspecialchars($file['name']); ?></div>
      <div class="share-meta">
        <span><?php echo fmtSize($file['size']); ?></span>
        <span><?php echo date('d M Y', $file['modified']); ?></span>
      </div>
      <a href="?token=<?php echo urlencode($token); ?>&dl=1" class="btn btn-primary btn-block" style="justify-content:center;gap:.5rem;text-decoration:none">
        ⬇️ Download
      </a>
    <?php endif; ?>

    <div class="share-brand">Shared via <a href="dashboard.php">TemboDrive 🐘</a></div>
  </div>
</div>
</body>
</html>

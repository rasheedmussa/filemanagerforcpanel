<?php
// bulk_zip.php — Stream a ZIP of selected files/folders to browser
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }

$paths = json_decode($_POST['paths'] ?? '[]', true);
if (empty($paths) || !is_array($paths)) { http_response_code(400); exit('No files selected'); }
if (!class_exists('ZipArchive'))         { http_response_code(500); exit('ZipArchive unavailable'); }

$uploadRoot = realpath(UPLOAD_DIR);
foreach ($paths as $p) {
    $real = realpath(UPLOAD_DIR . ltrim($p, '/'));
    if (!$real || strpos($real, $uploadRoot) !== 0) { http_response_code(403); exit('Access denied'); }
}

$tmpFile = tempnam(sys_get_temp_dir(), 'tembo_');
$zip     = new ZipArchive();

if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500); exit('Cannot create ZIP');
}

foreach ($paths as $p) {
    $p    = ltrim($p, '/');
    $full = UPLOAD_DIR . $p;
    addToZip($zip, $full, basename($p));
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="TemboDrive_download.zip"');
header('Content-Length: ' . filesize($tmpFile));
header('Cache-Control: private, max-age=0');
readfile($tmpFile);
unlink($tmpFile);
exit;

function addToZip($zip, $src, $zipPath) {
    if (is_file($src)) {
        $zip->addFile($src, $zipPath);
    } elseif (is_dir($src)) {
        $zip->addEmptyDir($zipPath);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            addToZip($zip, $src . '/' . $item, $zipPath . '/' . $item);
        }
    }
}

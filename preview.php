<?php
// preview.php — Preview a file (image, PDF, text, video, audio)
require_once 'config.php';

$filePath = trim($_GET['path'] ?? '');

if (empty($filePath)) { http_response_code(400); exit('Invalid path'); }

$uploadRoot = realpath(UPLOAD_DIR);
$realPath   = realpath(UPLOAD_DIR . ltrim($filePath, '/'));
if (!$realPath) $realPath = realpath($filePath); // legacy calls with full relative path

if (!$realPath || strpos($realPath, $uploadRoot) !== 0) { http_response_code(403); exit('Access denied'); }
if (!is_file($realPath)) { http_response_code(404); exit('Not found'); }

$filePath  = $realPath;
$fileType  = mime_content_type($filePath);
$filename  = basename($filePath);
$ext       = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$fileSize  = filesize($filePath);

// ── Images ──────────────────────────────────────────────────────
if (strpos($fileType, 'image/') === 0) {
    header('Content-Type: ' . $fileType);
    header('Content-Length: ' . $fileSize);
    readfile($filePath);

// ── PDF ─────────────────────────────────────────────────────────
} elseif ($fileType === 'application/pdf') {
    header('Content-Type: application/pdf');
    header('Content-Length: ' . $fileSize);
    readfile($filePath);

// ── Video & Audio (with range-request support) ───────────────────
} elseif (strpos($fileType, 'video/') === 0 || strpos($fileType, 'audio/') === 0) {
    header('Accept-Ranges: bytes');
    header('Content-Type: ' . $fileType);

    if (!empty($_SERVER['HTTP_RANGE'])) {
        preg_match('/bytes=(\d+)-(\d*)/', $_SERVER['HTTP_RANGE'], $m);
        $start  = (int)$m[1];
        $end    = isset($m[2]) && $m[2] !== '' ? (int)$m[2] : $fileSize - 1;
        $length = $end - $start + 1;

        http_response_code(206);
        header("Content-Range: bytes $start-$end/$fileSize");
        header("Content-Length: $length");

        $fp = fopen($filePath, 'rb');
        fseek($fp, $start);
        $rem = $length;
        while ($rem > 0 && !feof($fp)) {
            $chunk = (int)min(65536, $rem);
            echo fread($fp, $chunk);
            $rem -= $chunk;
        }
        fclose($fp);
    } else {
        header('Content-Length: ' . $fileSize);
        readfile($filePath);
    }

// ── Text / Code ──────────────────────────────────────────────────
} elseif (
    strpos($fileType, 'text/') === 0 ||
    in_array($ext, ['txt','php','js','css','html','htm','json','xml','md','py','sql','csv','ini','conf','log','sh','bat'])
) {
    header('Content-Type: text/plain; charset=utf-8');
    readfile($filePath);

// ── Unsupported ──────────────────────────────────────────────────
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<div style='padding:1.5rem;font-family:sans-serif'>";
    echo "<p><strong>".htmlspecialchars($filename)."</strong></p>";
    echo "<p>Type: ".htmlspecialchars($fileType)."</p>";
    echo "<p>Size: ".number_format($fileSize)." bytes</p>";
    echo "<p><a href='download.php?path=".urlencode($filePath)."'>Download</a> to view this file.</p>";
    echo "</div>";
}

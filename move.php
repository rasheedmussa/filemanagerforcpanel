<?php
// move.php — Move or copy a file/folder to a destination
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$action  = trim($_POST['action']  ?? 'move'); // 'move' or 'copy'
$source  = trim($_POST['source']  ?? '', '/');
$destDir = trim($_POST['dest']    ?? '', '/');

$response = ['success' => false, 'message' => ''];

try {
    if (empty($source)) throw new Exception('No source specified');

    $uploadRoot  = realpath(UPLOAD_DIR);
    $sourceFull  = realpath(UPLOAD_DIR . $source);
    if (!$sourceFull || strpos($sourceFull, $uploadRoot) !== 0) throw new Exception('Access denied');
    if (!file_exists($sourceFull)) throw new Exception('Source not found');

    // Destination directory
    $destFull = $destDir ? UPLOAD_DIR . $destDir . '/' : UPLOAD_DIR;
    if (!is_dir($destFull)) throw new Exception('Destination not found');

    // Security check on destination
    $realDest = realpath($destFull);
    if (!$realDest || strpos($realDest, $uploadRoot) !== 0) throw new Exception('Access denied');

    $targetPath = rtrim($destFull, '/') . '/' . basename($sourceFull);

    if (realpath($targetPath) === $sourceFull) throw new Exception('Source and destination are the same');

    if (file_exists($targetPath)) {
        $ext  = pathinfo($targetPath, PATHINFO_EXTENSION);
        $base = pathinfo($targetPath, PATHINFO_FILENAME);
        $i    = 1;
        while (file_exists($realDest . '/' . $base . '_' . $i . ($ext ? '.' . $ext : ''))) $i++;
        $targetPath = $realDest . '/' . $base . '_' . $i . ($ext ? '.' . $ext : '');
    }

    if ($action === 'copy') {
        copyRecursive($sourceFull, $targetPath);
        $response['success'] = true;
        $response['message'] = 'Copied successfully';
    } else {
        if (!rename($sourceFull, $targetPath)) throw new Exception('Move failed');
        $response['success'] = true;
        $response['message'] = 'Moved successfully';
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

function copyRecursive($src, $dst) {
    if (is_dir($src)) {
        mkdir($dst, 0755, true);
        foreach (scandir($src) as $item) {
            if ($item === '.' || $item === '..') continue;
            copyRecursive($src . '/' . $item, $dst . '/' . $item);
        }
    } else {
        copy($src, $dst);
    }
}

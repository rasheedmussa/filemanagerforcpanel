<?php
// zip_action.php — Create ZIP from folder, or extract ZIP
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$action = trim($_POST['action'] ?? '');
$path   = trim($_POST['path']   ?? '', '/');

$response = ['success' => false, 'message' => ''];

try {
    if (!class_exists('ZipArchive')) throw new Exception('ZipArchive extension not available');

    if (empty($path)) throw new Exception('No path specified');

    $uploadRoot = realpath(UPLOAD_DIR);
    $fullPath   = realpath(UPLOAD_DIR . $path);
    if (!$fullPath || strpos($fullPath, $uploadRoot) !== 0) throw new Exception('Access denied');

    if ($action === 'create') {
        // Create ZIP from a folder
        if (!is_dir($fullPath)) throw new Exception('Source is not a folder');

        $zipName = basename($fullPath) . '.zip';
        $zipPath = dirname($fullPath) . '/' . $zipName;

        // Avoid overwrite
        $i = 1;
        while (file_exists($zipPath)) {
            $zipPath = dirname($fullPath) . '/' . basename($fullPath) . '_' . $i . '.zip';
            $i++;
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) throw new Exception('Cannot create ZIP');
        addDirToZip($zip, $fullPath, basename($fullPath));
        $zip->close();

        $response['success'] = true;
        $response['message'] = basename($zipPath) . ' created';

    } elseif ($action === 'extract') {
        // Extract ZIP file
        if (!is_file($fullPath)) throw new Exception('Source is not a file');
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if ($ext !== 'zip') throw new Exception('File is not a ZIP archive');

        $extractDir = dirname($fullPath) . '/' . pathinfo($fullPath, PATHINFO_FILENAME);
        if (!is_dir($extractDir)) mkdir($extractDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($fullPath) !== true) throw new Exception('Cannot open ZIP');
        $zip->extractTo($extractDir);
        $zip->close();

        $response['success'] = true;
        $response['message'] = 'Extracted to ' . basename($extractDir) . '/';

    } else {
        throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

function addDirToZip($zip, $dir, $zipDir) {
    $zip->addEmptyDir($zipDir);
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $dir . '/' . $item;
        $zp   = $zipDir . '/' . $item;
        if (is_file($full)) {
            $zip->addFile($full, $zp);
        } elseif (is_dir($full)) {
            addDirToZip($zip, $full, $zp);
        }
    }
}

<?php
// bulk_action.php — Bulk delete selected files/folders
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$action = trim($_POST['action'] ?? '');
$paths  = json_decode($_POST['paths'] ?? '[]', true);

$response = ['success' => false, 'message' => ''];

try {
    if (empty($paths) || !is_array($paths)) throw new Exception('No files selected');

    $uploadRoot = realpath(UPLOAD_DIR);

    // Validate all paths first
    foreach ($paths as $p) {
        $real = realpath(UPLOAD_DIR . ltrim($p, '/'));
        if (!$real || strpos($real, $uploadRoot) !== 0) throw new Exception('Access denied');
    }

    if ($action === 'delete') {
        $count = 0;
        foreach ($paths as $p) {
            $full = UPLOAD_DIR . ltrim($p, '/');
            if (is_dir($full)) {
                if (deleteRecursive($full)) $count++;
            } elseif (is_file($full)) {
                if (unlink($full)) $count++;
            }
        }
        $response['success'] = true;
        $response['message'] = $count . ' item(s) deleted';
    } else {
        throw new Exception('Unknown action');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

function deleteRecursive($dir) {
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
        if ($item === '.' || $item === '..') continue;
        deleteRecursive($dir . '/' . $item);
    }
    return rmdir($dir);
}

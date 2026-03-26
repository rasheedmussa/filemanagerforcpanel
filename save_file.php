<?php
// save_file.php — Save edited text/code file content
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$path    = trim($_POST['path']    ?? '', '/');
$content = $_POST['content'] ?? '';

$response = ['success' => false, 'message' => ''];

try {
    if (empty($path)) throw new Exception('No path specified');

    $uploadRoot = realpath(UPLOAD_DIR);
    $fullPath   = realpath(UPLOAD_DIR . $path);
    if (!$fullPath || strpos($fullPath, $uploadRoot) !== 0) throw new Exception('Access denied');
    if (!is_file($fullPath)) throw new Exception('File not found');

    // Only allow editing text/code files
    $editableExts = ['txt','php','js','css','html','htm','json','xml','md','py','sql','csv','ini','conf','log','sh','bat'];
    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if (!in_array($ext, $editableExts)) throw new Exception('File type not editable');

    if (file_put_contents($fullPath, $content) === false) throw new Exception('Failed to save file');

    $response['success'] = true;
    $response['message'] = 'Saved successfully';
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

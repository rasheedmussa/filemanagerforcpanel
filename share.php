<?php
// share.php — Generate a public share link for a file
require_once 'config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit;
}

$path = trim($_POST['path'] ?? '', '/');
$response = ['success' => false, 'message' => ''];

try {
    if (empty($path)) throw new Exception('No path specified');

    $uploadRoot = realpath(UPLOAD_DIR);
    $fullPath   = realpath(UPLOAD_DIR . $path);
    if (!$fullPath || strpos($fullPath, $uploadRoot) !== 0) throw new Exception('Access denied');
    if (!is_file($fullPath)) throw new Exception('Only files can be shared');

    // Ensure data/ directory exists and is protected
    $dataDir = __DIR__ . '/data/';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
        file_put_contents($dataDir . '.htaccess', "Order deny,allow\nDeny from all\n");
    }

    $sharesFile = $dataDir . 'shares.json';
    $shares = file_exists($sharesFile) ? json_decode(file_get_contents($sharesFile), true) : [];
    if (!is_array($shares)) $shares = [];

    // Reuse existing token for the same path
    foreach ($shares as $tok => $info) {
        if ($info['path'] === $path) {
            $token = $tok;
            break;
        }
    }

    if (!isset($token)) {
        $token = bin2hex(random_bytes(12));
        $shares[$token] = ['path' => $path, 'name' => basename($path), 'created' => time()];
        file_put_contents($sharesFile, json_encode($shares, JSON_PRETTY_PRINT));
    }

    // Build share URL
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $url   = $proto . '://' . $host . $dir . '/shared.php?token=' . $token;

    $response['success'] = true;
    $response['url']     = $url;
    $response['token']   = $token;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);

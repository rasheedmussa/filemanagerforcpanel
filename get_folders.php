<?php
// get_folders.php — Return flat list of all folders for move/copy modal
require_once 'config.php';
header('Content-Type: application/json');

echo json_encode(['success' => true, 'folders' => listFolders('', 0)]);

function listFolders($subDir, $depth) {
    if ($depth > 6) return [];
    $base   = UPLOAD_DIR;
    $target = $subDir ? rtrim($base, '/') . '/' . $subDir . '/' : $base;
    if (!is_dir($target)) return [];

    $result = [];
    foreach (scandir($target) as $item) {
        if ($item === '.' || $item === '..') continue;
        $full = $target . $item;
        if (!is_dir($full)) continue;
        $relPath  = ($subDir ? $subDir . '/' : '') . $item;
        $result[] = ['name' => $item, 'path' => $relPath, 'depth' => $depth];
        foreach (listFolders($relPath, $depth + 1) as $sub) {
            $result[] = $sub;
        }
    }
    return $result;
}

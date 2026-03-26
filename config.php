<?php
// config.php - Common functions (no database required)

session_start();

define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Get files and folders from filesystem
function getFilesFromDirectory($subDir = '') {
    $baseDir = UPLOAD_DIR;
    $targetDir = $subDir ? rtrim($baseDir, '/') . '/' . ltrim($subDir, '/') . '/' : $baseDir;

    if (!is_dir($targetDir)) {
        return [];
    }

    $files = [];
    $items = scandir($targetDir);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $targetDir . $item;
        $relativePath = ($subDir ? $subDir . '/' : '') . $item;
        $isFolder = is_dir($fullPath);

        $files[] = [
            'name'      => $item,
            'path'      => $relativePath,
            'is_folder' => $isFolder,
            'size'      => $isFolder ? 0 : filesize($fullPath),
            'modified'  => filemtime($fullPath),
            'type'      => $isFolder ? 'folder' : mime_content_type($fullPath),
        ];
    }

    return $files;
}

// Sanitize filename
function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9\._-]/', '_', $filename);
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Get file icon based on extension
function getFileIcon($filename, $isFolder = false) {
    if ($isFolder) return '📁';

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $icons = [
        'pdf'  => '📄',
        'doc'  => '📝', 'docx' => '📝',
        'xls'  => '📊', 'xlsx' => '📊',
        'ppt'  => '📽️', 'pptx' => '📽️',
        'txt'  => '📄',
        'jpg'  => '🖼️', 'jpeg' => '🖼️', 'png' => '🖼️', 'gif' => '🖼️', 'webp' => '🖼️',
        'mp4'  => '🎥', 'avi' => '🎥', 'mov' => '🎥',
        'mp3'  => '🎵', 'wav' => '🎵',
        'zip'  => '📦', 'rar' => '📦', '7z' => '📦',
    ];
    return $icons[$ext] ?? '📄';
}

// Auth stubs — replace with real login system when ready
function isLoggedIn() {
    return true;
}
function getUser() {
    return ['name' => 'Admin', 'role' => 'admin'];
}
?>

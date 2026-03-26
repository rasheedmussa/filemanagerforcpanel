<?php
// dashboard.php
require_once 'config.php';

$currentDir = isset($_GET['dir']) ? trim($_GET['dir'], '/') : '';
$currentPath = $currentDir ? "/$currentDir" : '';

$files = getFilesFromDirectory($currentDir);

usort($files, function($a, $b) {
    if ($a['is_folder'] != $b['is_folder']) return $b['is_folder'] - $a['is_folder'];
    return strcmp($a['name'], $b['name']);
});

$totalSize = 0;
foreach ($files as $f) $totalSize += $f['size'];

$breadcrumbs = [];
if ($currentDir) {
    $parts = explode('/', $currentDir);
    $path = '';
    foreach ($parts as $part) {
        $path .= ($path ? '/' : '') . $part;
        $breadcrumbs[] = ['name' => $part, 'path' => $path];
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TemboDrive</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="dashboard">

    <!-- HEADER -->
    <header class="dashboard-header">
        <div class="header-left">
            <div class="header-logo">
                <div class="logo-icon">🐘</div>
                <h1 data-i18n="appTitle">TemboDrive</h1>
            </div>
            <nav class="breadcrumb">
                <a href="dashboard.php" data-i18n="home">Home</a>
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <span class="sep">/</span>
                    <a href="dashboard.php?dir=<?php echo urlencode($crumb['path']); ?>">
                        <?php echo htmlspecialchars($crumb['name']); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
        <div class="header-right">
            <!-- Language toggle -->
            <button class="ctrl-btn" id="langToggle" onclick="toggleLang()">
                <span class="icon">🌐</span>
                <span id="langLabel">SW</span>
            </button>
            <!-- Theme toggle -->
            <button class="ctrl-btn" id="themeToggle" onclick="toggleTheme()">
                <span class="icon" id="themeIcon">☀️</span>
                <span id="themeLabel">Light</span>
            </button>
        </div>
    </header>

    <div class="dashboard-content">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <!-- Upload -->
            <div class="sidebar-section">
                <div class="sidebar-section-title" data-i18n="uploadTitle">Upload Files</div>
                <div class="upload-area" id="uploadArea">
                    <span class="upload-icon">☁️</span>
                    <div class="upload-text-main" data-i18n="uploadMain">Drop files here</div>
                    <div class="upload-text-sub" data-i18n="uploadSub">or click to browse</div>
                    <input type="file" id="fileInput" multiple style="display:none">
                </div>

                <label class="custom-check">
                    <input type="checkbox" id="folderUpload">
                    <span data-i18n="uploadFolder">Upload Folder</span>
                </label>

                <div id="progressContainer" style="display:none;" class="progress-wrap">
                    <div class="progress-track">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-label" id="progressText">0%</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="sidebar-section">
                <div class="sidebar-section-title" data-i18n="actionsTitle">Actions</div>
                <div class="btn-actions">
                    <button class="btn btn-primary" onclick="createNewFolder()">
                        <span>📁</span> <span data-i18n="newFolder">New Folder</span>
                    </button>
                    <button class="btn btn-ghost" onclick="refreshFiles()">
                        <span>🔄</span> <span data-i18n="refresh">Refresh</span>
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="sidebar-section">
                <div class="sidebar-section-title" data-i18n="storageTitle">Storage Info</div>
                <div class="stat-item">
                    <span class="stat-label" data-i18n="totalFiles">Total Files</span>
                    <span class="stat-value"><?php echo count($files); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label" data-i18n="totalSize">Total Size</span>
                    <span class="stat-value"><?php echo formatFileSize($totalSize); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label" data-i18n="currentDir">Directory</span>
                    <span class="stat-value">/<?php echo htmlspecialchars($currentDir ?: 'root'); ?></span>
                </div>
            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <!-- Toolbar -->
            <div class="toolbar">
                <div class="search-wrap">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" data-i18n-placeholder="searchPlaceholder" placeholder="Search files...">
                </div>
                <div class="sort-wrap">
                    <select id="sortSelect">
                        <option value="name" data-i18n="sortName">Sort by Name</option>
                        <option value="date" data-i18n="sortDate">Sort by Date</option>
                        <option value="size" data-i18n="sortSize">Sort by Size</option>
                    </select>
                </div>
            </div>

            <!-- File List -->
            <div class="file-table-wrap">
                <?php if (empty($files)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📂</div>
                        <p data-i18n="noFiles">No files in this directory</p>
                    </div>
                <?php else: ?>
                    <div class="file-header">
                        <div data-i18n="colName">Name</div>
                        <div data-i18n="colType">Type</div>
                        <div data-i18n="colSize">Size</div>
                        <div data-i18n="colDate">Modified</div>
                        <div data-i18n="colActions">Actions</div>
                    </div>
                    <div class="file-list" id="fileList">
                        <?php foreach ($files as $index => $file): ?>
                        <div class="file-item"
                             data-index="<?php echo $index; ?>"
                             data-name="<?php echo htmlspecialchars($file['name']); ?>"
                             data-folder="<?php echo (int)$file['is_folder']; ?>"
                             data-path="<?php echo htmlspecialchars($file['path']); ?>"
                             data-size="<?php echo $file['size']; ?>"
                             data-date="<?php echo $file['modified']; ?>">

                            <div class="file-name">
                                <span class="file-icon"><?php echo getFileIcon($file['name'], $file['is_folder']); ?></span>
                                <?php if ($file['is_folder']): ?>
                                    <a href="dashboard.php?dir=<?php echo urlencode($currentDir . ($currentDir ? '/' : '') . $file['name']); ?>" class="folder-link">
                                        <?php echo htmlspecialchars($file['name']); ?>
                                    </a>
                                <?php else: ?>
                                    <span><?php echo htmlspecialchars($file['name']); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="file-type">
                                <?php echo $file['is_folder'] ? '<span data-i18n="typeFolder">Folder</span>' : htmlspecialchars($file['type']); ?>
                            </div>

                            <div class="file-size">
                                <?php echo $file['is_folder'] ? '—' : formatFileSize($file['size']); ?>
                            </div>

                            <div class="file-date">
                                <?php echo date('d M Y, H:i', $file['modified']); ?>
                            </div>

                            <div class="file-actions">
                                <?php if (!$file['is_folder']): ?>
                                    <button class="icon-btn" onclick="previewFile('<?php echo htmlspecialchars($file['path']); ?>')" title="Preview">👁️</button>
                                    <button class="icon-btn" onclick="downloadFile('<?php echo htmlspecialchars($file['path']); ?>')" title="Download">⬇️</button>
                                <?php endif; ?>
                                <button class="icon-btn" onclick="renameFile(<?php echo $index; ?>, '<?php echo addslashes($file['name']); ?>', '<?php echo addslashes($file['path']); ?>')" title="Rename">✏️</button>
                                <button class="icon-btn danger" onclick="deleteFile('<?php echo addslashes($file['path']); ?>')" title="Delete">🗑️</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div class="toast-container" id="toastContainer"></div>

<!-- RENAME MODAL -->
<div id="renameModal" class="modal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" data-i18n="renameTitle">Rename</span>
            <button class="modal-close" onclick="closeModal('renameModal')">&times;</button>
        </div>
        <input type="text" id="renameInput" class="modal-input" data-i18n-placeholder="renamePlaceholder">
        <button class="btn btn-primary" style="width:100%" onclick="confirmRename()" data-i18n="renameBtn">Rename</button>
    </div>
</div>

<!-- CREATE FOLDER MODAL -->
<div id="createFolderModal" class="modal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title" data-i18n="folderTitle">New Folder</span>
            <button class="modal-close" onclick="closeModal('createFolderModal')">&times;</button>
        </div>
        <input type="text" id="folderNameInput" class="modal-input" data-i18n-placeholder="folderPlaceholder">
        <button class="btn btn-primary" style="width:100%" onclick="confirmCreateFolder()" data-i18n="folderBtn">Create</button>
    </div>
</div>

<!-- PREVIEW MODAL -->
<div id="previewModal" class="modal">
    <div class="modal-box" style="max-width:820px">
        <div class="modal-header">
            <span class="modal-title" data-i18n="previewTitle">Preview</span>
            <button class="modal-close" onclick="closeModal('previewModal')">&times;</button>
        </div>
        <div id="previewContent"></div>
    </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>

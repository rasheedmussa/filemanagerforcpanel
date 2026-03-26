// main.js - File Manager with Dark Mode + Bilingual (SW/EN)

// ── TRANSLATIONS ────────────────────────────────────────────────
const i18n = {
  en: {
    appTitle: 'TemboDrive',
    home: 'Home',
    uploadTitle: 'Upload Files',
    uploadMain: 'Drop files here',
    uploadSub: 'or click to browse',
    uploadFolder: 'Upload Folder',
    actionsTitle: 'Actions',
    newFolder: 'New Folder',
    refresh: 'Refresh',
    storageTitle: 'Storage Info',
    totalFiles: 'Total Files',
    totalSize: 'Total Size',
    currentDir: 'Directory',
    searchPlaceholder: 'Search files...',
    sortName: 'Sort by Name',
    sortDate: 'Sort by Date',
    sortSize: 'Sort by Size',
    colName: 'Name',
    colType: 'Type',
    colSize: 'Size',
    colDate: 'Modified',
    colActions: 'Actions',
    noFiles: 'No files in this directory',
    typeFolder: 'Folder',
    renameTitle: 'Rename',
    renamePlaceholder: 'New name...',
    renameBtn: 'Rename',
    folderTitle: 'New Folder',
    folderPlaceholder: 'Folder name...',
    folderBtn: 'Create',
    previewTitle: 'Preview',
    themeLight: 'Light',
    themeDark: 'Dark',
    deleteConfirm: 'Are you sure you want to delete this?',
    uploadSuccess: 'Upload successful!',
    uploadFail: 'Upload failed',
    renameSuccess: 'Renamed successfully',
    renameFail: 'Rename failed',
    deleteSuccess: 'Deleted successfully',
    deleteFail: 'Delete failed',
    folderSuccess: 'Folder created',
    folderFail: 'Failed to create folder',
    loading: 'Loading preview...',
    previewError: 'Error loading preview',
    enterName: 'Please enter a name',
  },
  sw: {
    appTitle: 'TemboDrive',
    home: 'Nyumbani',
    uploadTitle: 'Pakia Faili',
    uploadMain: 'Buruta faili hapa',
    uploadSub: 'au bonyeza kuchagua',
    uploadFolder: 'Pakia Folda',
    actionsTitle: 'Vitendo',
    newFolder: 'Folda Mpya',
    refresh: 'Onyesha Upya',
    storageTitle: 'Taarifa ya Hifadhi',
    totalFiles: 'Jumla ya Faili',
    totalSize: 'Ukubwa Wote',
    currentDir: 'Saraka',
    searchPlaceholder: 'Tafuta faili...',
    sortName: 'Panga kwa Jina',
    sortDate: 'Panga kwa Tarehe',
    sortSize: 'Panga kwa Ukubwa',
    colName: 'Jina',
    colType: 'Aina',
    colSize: 'Ukubwa',
    colDate: 'Ilirekebishwa',
    colActions: 'Vitendo',
    noFiles: 'Hakuna faili katika saraka hii',
    typeFolder: 'Folda',
    renameTitle: 'Badilisha Jina',
    renamePlaceholder: 'Jina jipya...',
    renameBtn: 'Badilisha',
    folderTitle: 'Folda Mpya',
    folderPlaceholder: 'Jina la folda...',
    folderBtn: 'Tengeneza',
    previewTitle: 'Onyesho',
    themeLight: 'Mwanga',
    themeDark: 'Giza',
    deleteConfirm: 'Una uhakika unataka kufuta hii?',
    uploadSuccess: 'Imepakiwa!',
    uploadFail: 'Upakiaji umeshindwa',
    renameSuccess: 'Jina limebadilishwa',
    renameFail: 'Kubadilisha jina kumeshindwa',
    deleteSuccess: 'Imefutwa',
    deleteFail: 'Kufuta kumeshindwa',
    folderSuccess: 'Folda imetengenezwa',
    folderFail: 'Kutengeneza folda kumeshindwa',
    loading: 'Inapakia onyesho...',
    previewError: 'Hitilafu ya kupakia onyesho',
    enterName: 'Tafadhali ingiza jina',
  }
};

// ── STATE ────────────────────────────────────────────────────────
let currentLang  = localStorage.getItem('fm_lang')  || 'en';
let currentTheme = localStorage.getItem('fm_theme') || 'dark';
let currentDir   = '';
let currentRenamePath = '';
let currentRenameName = '';

// ── INIT ─────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  currentDir = params.get('dir') || '';

  applyTheme(currentTheme);
  applyLang(currentLang);
  initUpload();
  initSearchSort();
  initModalClose();
  initKeyboard();
});

// ── THEME ─────────────────────────────────────────────────────────
function applyTheme(theme) {
  currentTheme = theme;
  document.documentElement.setAttribute('data-theme', theme);
  localStorage.setItem('fm_theme', theme);

  const t = i18n[currentLang];
  const icon  = document.getElementById('themeIcon');
  const label = document.getElementById('themeLabel');
  if (theme === 'dark') {
    if (icon)  icon.textContent  = '☀️';
    if (label) label.textContent = t.themeLight;
  } else {
    if (icon)  icon.textContent  = '🌙';
    if (label) label.textContent = t.themeDark;
  }
}

function toggleTheme() {
  applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
}

// ── LANGUAGE ──────────────────────────────────────────────────────
function applyLang(lang) {
  currentLang = lang;
  localStorage.setItem('fm_lang', lang);

  const t = i18n[lang];
  const label = document.getElementById('langLabel');
  if (label) label.textContent = lang === 'en' ? 'SW' : 'EN';

  // Update text nodes
  document.querySelectorAll('[data-i18n]').forEach(el => {
    const key = el.getAttribute('data-i18n');
    if (t[key] !== undefined) el.textContent = t[key];
  });

  // Update placeholders
  document.querySelectorAll('[data-i18n-placeholder]').forEach(el => {
    const key = el.getAttribute('data-i18n-placeholder');
    if (t[key] !== undefined) el.placeholder = t[key];
  });

  // Refresh theme label in new lang
  applyTheme(currentTheme);
}

function toggleLang() {
  applyLang(currentLang === 'en' ? 'sw' : 'en');
}

function t(key) {
  return (i18n[currentLang] && i18n[currentLang][key]) || key;
}

// ── TOAST ─────────────────────────────────────────────────────────
function showToast(msg, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.textContent = msg;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// ── UPLOAD ────────────────────────────────────────────────────────
function initUpload() {
  const uploadArea  = document.getElementById('uploadArea');
  const fileInput   = document.getElementById('fileInput');

  if (!uploadArea || !fileInput) return;

  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev =>
    uploadArea.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); })
  );

  ['dragenter', 'dragover'].forEach(ev =>
    uploadArea.addEventListener(ev, () => uploadArea.classList.add('dragover'))
  );

  ['dragleave', 'drop'].forEach(ev =>
    uploadArea.addEventListener(ev, () => uploadArea.classList.remove('dragover'))
  );

  uploadArea.addEventListener('drop', e => handleFiles(e.dataTransfer.files));
  uploadArea.addEventListener('click', () => fileInput.click());
  fileInput.addEventListener('change', e => handleFiles(e.target.files));
}

function handleFiles(files) {
  const isFolderMode = document.getElementById('folderUpload')?.checked;
  const formData = new FormData();

  if (isFolderMode) {
    formData.append('folderUpload', 'true');
    for (const f of files) {
      formData.append('files[]', f);
      formData.append('webkitRelativePath[]', f.webkitRelativePath || f.name);
    }
  } else {
    for (const f of files) formData.append('files[]', f);
  }

  formData.append('currentDir', currentDir);
  sendUpload(formData);
}

function sendUpload(formData) {
  const container = document.getElementById('progressContainer');
  const fill      = document.getElementById('progressFill');
  const label     = document.getElementById('progressText');

  if (container) container.style.display = 'block';

  const xhr = new XMLHttpRequest();

  xhr.upload.addEventListener('progress', e => {
    if (e.lengthComputable) {
      const pct = Math.round((e.loaded / e.total) * 100);
      if (fill)  fill.style.width   = pct + '%';
      if (label) label.textContent  = pct + '%';
    }
  });

  xhr.addEventListener('load', () => {
    if (container) container.style.display = 'none';
    try {
      const res = JSON.parse(xhr.responseText);
      if (res.success) {
        showToast(t('uploadSuccess'), 'success');
        setTimeout(() => location.reload(), 800);
      } else {
        showToast(t('uploadFail') + ': ' + (res.message || ''), 'error');
      }
    } catch {
      showToast(xhr.status === 200 ? t('uploadSuccess') : t('uploadFail'),
                xhr.status === 200 ? 'success' : 'error');
      if (xhr.status === 200) setTimeout(() => location.reload(), 800);
    }
  });

  xhr.addEventListener('error', () => {
    if (container) container.style.display = 'none';
    showToast(t('uploadFail'), 'error');
  });

  xhr.open('POST', 'upload.php');
  xhr.send(formData);
}

// ── SEARCH & SORT ─────────────────────────────────────────────────
function initSearchSort() {
  document.getElementById('searchInput')?.addEventListener('input', filterFiles);
  document.getElementById('sortSelect')?.addEventListener('change', sortFiles);
}

function filterFiles() {
  const term = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.file-item').forEach(item => {
    const name = item.dataset.name?.toLowerCase() || '';
    item.style.display = name.includes(term) ? '' : 'none';
  });
}

function sortFiles() {
  const by       = document.getElementById('sortSelect').value;
  const list     = document.getElementById('fileList');
  const items    = Array.from(list?.querySelectorAll('.file-item') || []);

  items.sort((a, b) => {
    if (by === 'name') return (a.dataset.name || '').localeCompare(b.dataset.name || '');
    if (by === 'date') return (b.dataset.date || 0) - (a.dataset.date || 0);
    if (by === 'size') return (b.dataset.size || 0) - (a.dataset.size || 0);
    return 0;
  });

  items.forEach(item => list.appendChild(item));
}

// ── MODALS ────────────────────────────────────────────────────────
function initModalClose() {
  document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', e => {
      if (e.target === modal) closeModal(modal.id);
    });
  });
}

function initKeyboard() {
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal.active').forEach(m => closeModal(m.id));
    }
  });
}

function openModal(id) {
  const m = document.getElementById(id);
  if (m) m.classList.add('active');
}

function closeModal(id) {
  const m = document.getElementById(id);
  if (m) m.classList.remove('active');
}

// ── FOLDER ────────────────────────────────────────────────────────
function createNewFolder() {
  const input = document.getElementById('folderNameInput');
  if (input) input.value = '';
  openModal('createFolderModal');
  setTimeout(() => input?.focus(), 100);
}

function confirmCreateFolder() {
  const name = document.getElementById('folderNameInput')?.value.trim();
  if (!name) { showToast(t('enterName'), 'error'); return; }

  const fd = new FormData();
  fd.append('folderName', name);
  fd.append('currentDir', currentDir);

  fetch('create_folder.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      closeModal('createFolderModal');
      if (data.success) {
        showToast(t('folderSuccess'), 'success');
        setTimeout(() => location.reload(), 600);
      } else {
        showToast(t('folderFail') + ': ' + (data.message || ''), 'error');
      }
    })
    .catch(() => { closeModal('createFolderModal'); showToast(t('folderFail'), 'error'); });
}

// ── RENAME ────────────────────────────────────────────────────────
function renameFile(index, name, path) {
  currentRenamePath = path;
  currentRenameName = name;
  const input = document.getElementById('renameInput');
  if (input) input.value = name;
  openModal('renameModal');
  setTimeout(() => input?.focus(), 100);
}

function confirmRename() {
  const newName = document.getElementById('renameInput')?.value.trim();
  if (!newName) { showToast(t('enterName'), 'error'); return; }
  if (newName === currentRenameName) { closeModal('renameModal'); return; }

  const fd = new FormData();
  fd.append('filePath', currentRenamePath);
  fd.append('newName', newName);

  fetch('rename.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      closeModal('renameModal');
      if (data.success) {
        showToast(t('renameSuccess'), 'success');
        setTimeout(() => location.reload(), 600);
      } else {
        showToast(t('renameFail') + ': ' + (data.message || ''), 'error');
      }
    })
    .catch(() => { closeModal('renameModal'); showToast(t('renameFail'), 'error'); });
}

// ── DELETE ────────────────────────────────────────────────────────
function deleteFile(path) {
  if (!confirm(t('deleteConfirm'))) return;

  const fd = new FormData();
  fd.append('filePath', path);

  fetch('delete.php', { method: 'POST', body: fd })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        showToast(t('deleteSuccess'), 'success');
        setTimeout(() => location.reload(), 600);
      } else {
        showToast(t('deleteFail') + ': ' + (data.message || ''), 'error');
      }
    })
    .catch(() => showToast(t('deleteFail'), 'error'));
}

// ── DOWNLOAD ──────────────────────────────────────────────────────
function downloadFile(path) {
  window.location.href = 'download.php?path=' + encodeURIComponent(path);
}

// ── PREVIEW ───────────────────────────────────────────────────────
function previewFile(path) {
  const content = document.getElementById('previewContent');
  if (!content) return;

  content.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--text-muted)">${t('loading')}</div>`;
  openModal('previewModal');

  fetch('preview.php?path=' + encodeURIComponent(path))
    .then(response => {
      const ct = response.headers.get('content-type') || '';
      if (ct.startsWith('image/')) {
        return response.blob().then(blob => {
          const img = document.createElement('img');
          img.src = URL.createObjectURL(blob);
          img.style.maxWidth = '100%';
          content.innerHTML = '';
          content.appendChild(img);
        });
      } else if (ct === 'application/pdf') {
        return response.blob().then(blob => {
          const iframe = document.createElement('iframe');
          iframe.src = URL.createObjectURL(blob);
          iframe.style.cssText = 'width:100%;height:70vh;border:none';
          content.innerHTML = '';
          content.appendChild(iframe);
        });
      } else {
        return response.text().then(text => {
          const pre = document.createElement('pre');
          pre.textContent = text;
          content.innerHTML = '';
          content.appendChild(pre);
        });
      }
    })
    .catch(() => {
      content.innerHTML = `<div style="text-align:center;padding:2rem;color:var(--danger)">${t('previewError')}</div>`;
    });
}

// ── REFRESH ───────────────────────────────────────────────────────
function refreshFiles() {
  location.reload();
}

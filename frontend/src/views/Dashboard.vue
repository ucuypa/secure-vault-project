<template>
  <div class="dashboard-layout">

    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <router-link to="/dashboard" class="nav-item active">
          <img src="/padlockwebdev.svg" alt="Logo" class="logo" />
        </router-link>
      </div>
      <nav class="icon-nav">
        <div class="nav-item" :class="{ active: currentFilter === 'all' }" @click="setFilter('all')">
          <HomeIcon class="icon" />
          <span>Home</span>
        </div>

        <div class="nav-item" :class="{ active: currentFilter === 'folders' }" @click="setFilter('folders')">
          <Folder class="icon" />
          <span>Folder</span>
        </div>

        <div class="nav-item">
          <router-link to="/activity" class="nav-item" style="text-decoration: none;">
            <Bell class="icon" />
            <span>Activity</span>
          </router-link>
        </div>

        <div class="nav-item">
          <router-link to="/profile" class="nav-item">
            <User class="icon" />
            <span>Profile</span>
          </router-link>
        </div>
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <h2 class="menu-title">Home</h2>

      <div class="menu-item" :class="{ active: currentFilter === 'all' }" @click="setFilter('all')">
        <LayoutGrid class="icon-small" />
        <span>All Files</span>
      </div>

      <div class="menu-item">
        <Users class="icon-small" />
        <span>Photos</span>
      </div>

      <div class="menu-item">
        <Trash2 class="icon-small" />
        <span>Documents</span>
      </div>

      <div class="menu-item">
        <Trash2 class="icon-small" />
        <span>Videos</span>
      </div>

      <div class="menu-item">
        <Trash2 class="icon-small" />
        <span>Others</span>
      </div>

      <!-- Quick Access Section -->
      <div class="quick-access-section">
        <div class="section-header">
          <span class="section-label-bold">Quick access</span>
          <button class="btn-icon-tiny">
            <Plus class="icon-tiny" />
          </button>
        </div>

        <!-- Collapsible Items -->
        <div class="collapsible-wrapper">
          <div class="menu-item collapsible-header">
            <ChevronDown class="icon-tiny" />
            <span>Starred</span>
          </div>

          <div class="menu-item collapsible-header">
            <ChevronDown class="icon-tiny" />
            <span>Untitled</span>
          </div>
        </div>
      </div>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
          <div class="search-wrapper">
            <Search class="search-icon" />
            <input v-model="searchQuery" type="text" placeholder="Search files and folders..." class="search-input" />
          </div>
        </div>

        <router-link to="/profile" style="text-decoration: none; color: inherit;">
          <div class="user-avatar">
            {{ userInitial }}
          </div>
        </router-link>
      </header>

      <section class="content-area">
        <div class="content-header">
          <!-- Tombol Back dihapus karena Dashboard adalah root -->
          <h1 class="page-title">{{ currentFilter === 'folders' ? 'Folders' : 'All Files' }}</h1>
          <div class="action-buttons">
            <button @click="triggerFileUpload" :disabled="isUploading" class="btn-new">
              <Plus v-if="!isUploading" class="icon-small" /> {{ isUploading ? ' Encrypting...' : 'New' }}
            </button>
            <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden-input"
              accept=".pdf, .doc, .docx, .jpg, .png, .zip">
            <button @click="openFolderModal" class="btn-action">
              <FolderPlus class="icon-small" /> New folder
            </button>
          </div>
        </div>

        <div class="table-wrapper">
          <table class="file-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Date modified</th>
                <th>File size</th>
                <th class="text-right">Sort</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in filteredFiles" :key="file.id" class="file-row" @click="openFolder(file)"
                style="cursor: pointer;">
                <td>
                  <div class="name-wrapper">
                    <component :is="getFileIcon(file.mime_type)" class="icon file-icon"
                      :class="getIconColor(file.mime_type)" />
                    <span class="file-name">{{ file.original_name }}</span>
                  </div>
                </td>
                <td class="text-muted">{{ formatDate(file.created_at) }}</td>
                <td class="text-muted">{{ file.mime_type === 'directory' ? '--' : formatSize(file.file_size) }}</td>
                <td class="text-right">
                  <button class="btn-icon">
                    <MoreVertical class="icon" />
                  </button>
                </td>
              </tr>
              <tr v-if="files.length === 0">
                <td colspan="4" class="empty-state">No files found. Start by uploading one!</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden-input">
    <div v-if="showFolderModal" class="modal-overlay" @click.self="closeFolderModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>Create new folder</h3>
          <button @click="closeFolderModal" class="btn-icon">
            <X class="icon-small" />
          </button>
        </div>

        <div class="modal-body">
          <input v-model="newFolderName" type="text" placeholder="Folder name" class="modal-input"
            @keyup.enter="submitFolder" autofocus />
        </div>

        <div class="modal-footer">
          <button @click="closeFolderModal" class="btn-cancel">Cancel</button>
          <button @click="submitFolder" :disabled="!newFolderName.trim() || isCreatingFolder" class="btn-confirm">
            {{ isCreatingFolder ? 'Creating...' : 'Create' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router'; // Import useRouter
import axios from '../api/axios';
import {
  Settings, Home as HomeIcon, Folder, LayoutGrid, Search, Plus,
  Upload, FolderPlus, ChevronDown, MoreVertical, ArrowDown,
  FileText, FileImage, FileCode, File as FileGeneric,
  User, X, Bell, Grip, Image as ImageIcon, Users, FileQuestion, Trash2, ChevronRight
} from 'lucide-vue-next'; // Hapus ArrowLeft dari import karena sudah tidak dipakai

const router = useRouter(); // Inisialisasi router
const files = ref([]);
const user = JSON.parse(localStorage.getItem('user') || '{}');
const userInitial = computed(() => user.name ? user.name[0].toUpperCase() : 'U');
const fileInput = ref(null);
const isUploading = ref(false);

const showFolderModal = ref(false);
const newFolderName = ref('');
const isCreatingFolder = ref(false);

const triggerFileUpload = () => {
  fileInput.value.click();
};

const fetchFiles = async () => {
  try {
    const response = await axios.get('/files', {
      params: { parent_id: null } // Dashboard selalu memuat file root (parent_id: null)
    });
    files.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch files", error);
  }
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    alert('File size exceeds 10MB limit.');
    event.target.value = '';
    return;
  }

  const formData = new FormData();
  formData.append('file', file);
  // Upload dari Dashboard selalu ke root

  isUploading.value = true;

  try {
    const response = await axios.post('/files', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    alert('Success: ' + response.data.message);
    fetchFiles();
  } catch (error) {
    console.error('Upload error:', error);
    alert('Upload failed. Make sure file is valid and try again.');
  } finally {
    isUploading.value = false;
    event.target.value = '';
  }
};

const getFileIcon = (mime) => {
  if (mime === 'directory') return Folder;
  if (mime.includes('image')) return FileImage;
  if (mime.includes('pdf')) return FileText;
  if (mime.includes('javascript') || mime.includes('json')) return FileCode;
  return FileGeneric;
};

const getIconColor = (mime) => {
  if (mime === 'directory') return 'color-folder';
  if (mime.includes('pdf')) return 'color-danger';
  if (mime.includes('image')) return 'color-info';
  return 'color-muted';
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const formatSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const openFolderModal = () => {
  newFolderName.value = '';
  showFolderModal.value = true;
};

const closeFolderModal = () => {
  showFolderModal.value = false;
  newFolderName.value = '';
};

const submitFolder = async () => {
  if (!newFolderName.value.trim()) return;

  isCreatingFolder.value = true;
  try {
    await axios.post('/files', {
      name: newFolderName.value.trim(),
      parent_id: null // Buat folder dari Dashboard selalu di root
    });

    fetchFiles();
    closeFolderModal();
  } catch (error) {
    console.error("Error creating folder:", error);
    alert("Failed to create folder.");
  } finally {
    isCreatingFolder.value = false;
  }
};

const currentFilter = ref('all');

const setFilter = (filterType) => {
  currentFilter.value = filterType;
};

const folderList = computed(() => {
  return files.value.filter(file => file.mime_type === 'directory');
});

const searchQuery = ref('');

const filteredFiles = computed(() => {
  let result = files.value;

  if (currentFilter.value === 'folders') {
    result = result.filter(file => file.mime_type === 'directory');
  }

  if (searchQuery.value.trim() !== '') {
    const keyword = searchQuery.value.toLowerCase();
    result = result.filter(file =>
      file.original_name.toLowerCase().includes(keyword)
    );
  }

  return result;
});

// Arahkan ke URL folder baru saat diklik ganda
const openFolder = (folder) => {
  if (folder.mime_type !== 'directory') return;

  // Pindah halaman sambil membawa ID (params) dan Nama Folder (query)
  router.push({
    path: `/folder/${folder.id}`,
    query: { name: folder.original_name } // Ini kuncinya!
  });
};

onMounted(fetchFiles);
</script>

<style scoped>
/* =========================================
   CSS VARIABLES FOR DARK THEME
========================================= */

.nav-item,
.user-avatar {
  text-decoration: none;
  cursor: pointer;
}

.nav-item {
  color: inherit;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.dashboard-layout {
  --bg-main: #121212;
  --bg-sidebar: #1a1a1a;
  --bg-hover: #2a2a2a;
  --bg-button: #ffab00;
  --bg-button-hover: #ffc107;

  --border-color: #2e2e2e;

  --text-primary: #ffffff;
  --text-secondary: #a0a0a0;
  --text-muted: #6e6e6e;
  --text-inverse: #000000;

  display: flex;
  height: 100vh;
  background-color: var(--bg-main);
  color: var(--text-primary);
}

/* =========================================
   SIDEBARS
========================================= */
.sidebar-iconic {
  width: 72px;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px 0;
  gap: 32px;
}

.icon-brand {
  padding: 2px;
  border-radius: 5px;
  cursor: pointer;
}

.logo-placeholder {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
}

.icon-nav {
  display: flex;
  flex-direction: column;
  gap: 16px;
  width: 100%;
}

.nav-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
  align-items: center;
  color: var(--text-secondary);
  cursor: pointer;
  transition: color 0.2s;
}

.nav-item.active,
.nav-item:hover {
  color: var(--text-primary);
}

.nav-item span {
  font-size: 11px;
  font-weight: 500;
  margin-top: 4px;
}

.sidebar-menu {
  width: 260px;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
  padding: 24px 16px;
}

.menu-title {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 24px;
  padding: 0 8px;
  color: var(--text-primary);
}

.menu-section {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-bottom: 24px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 8px;
  color: var(--text-secondary);
  cursor: pointer;
  gap: 12px;
  font-size: 14px;
  font-weight: 400;
}

.menu-item.active {
  background-color: var(--bg-hover);
  color: var(--text-primary);
  font-weight: 500;
}

/* =========================================
   MAIN CONTENT & HEADER
========================================= */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.top-header {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 32px;
  border-bottom: 1px solid var(--border-color);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 24px;
  flex: 1;
}

.btn-new {
  background-color: var(--bg-button);
  color: var(--text-inverse);
  border: none;
  border-radius: 6px;
  padding: 8px 20px;
  font-weight: 600;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-new:hover {
  background-color: var(--bg-button-hover);
}

.search-wrapper {
  position: relative;
  width: 100%;
  max-width: 500px;
}

.search-input {
  width: 100%;
  background-color: var(--bg-sidebar);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 8px 16px 8px 40px;
  color: var(--text-primary);
  outline: none;
}

.search-input:focus {
  border-color: var(--text-muted);
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  color: var(--text-secondary);
}

.user-avatar {
  width: 32px;
  height: 32px;
  background-color: #f97316;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  cursor: pointer;
}

/* =========================================
   CONTENT AREA & TABLE
========================================= */
.content-area {
  padding: 32px;
  overflow-y: auto;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
}

.action-buttons {
  display: flex;
  gap: 12px;
}

.btn-action {
  background-color: var(--bg-hover);
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.btn-action:hover {
  background-color: #333;
}

/* =========================================
   TABLE STYLES (Fixed Borders)
========================================= */
.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

.file-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.file-table th,
.file-table td {
  padding: 16px;
  border-bottom: 1px solid var(--border-color);
  vertical-align: middle;
}

.file-table th {
  color: var(--text-secondary);
  font-weight: 500;
  font-size: 13px;
}

.file-row {
  transition: background-color 0.2s;
}

.file-row:hover td {
  background-color: var(--bg-sidebar);
}

/* =========================================
   TABLE CONTENT WRAPPERS
========================================= */
.name-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
}

.file-name {
  color: var(--text-primary);
  font-size: 14px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 400px;
}

.text-muted {
  color: var(--text-secondary);
  font-size: 14px;
}

.text-right {
  text-align: right;
}

.btn-icon {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
}

.btn-icon:hover {
  color: var(--text-primary);
  background-color: var(--border-color);
}

.empty-state {
  text-align: center;
  color: var(--text-muted);
  padding: 40px 0 !important;
}

/* =========================================
   UTILITIES & ICONS
========================================= */
.icon {
  width: 20px;
  height: 20px;
}

.file-icon {
  flex-shrink: 0;
  width: 20px;
  height: 20px;
}

.icon-small {
  width: 16px;
  height: 16px;
}

.icon-tiny {
  width: 12px;
  height: 12px;
}

.hidden-input {
  display: none;
}

/* Dynamic Icon Colors */
.color-folder {
  color: #ffca28;
}

.color-danger {
  color: #ef4444;
}

.color-info {
  color: #3b82f6;
}

.color-muted {
  color: #9ca3af;
}

/* =========================================
   MODAL POP-UP (NEW FOLDER)
========================================= */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-card {
  background-color: var(--bg-sidebar);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
  display: flex;
  flex-direction: column;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-color);
}

.modal-header h3 {
  font-size: 16px;
  font-weight: 500;
  color: var(--text-primary);
}

.modal-body {
  padding: 20px;
}

.modal-input {
  width: 100%;
  background-color: var(--bg-main);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 10px 14px;
  color: var(--text-primary);
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s;
}

.modal-input:focus {
  border-color: var(--bg-button);
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 16px 20px;
  border-top: 1px solid var(--border-color);
  background-color: var(--bg-main);
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
}

.btn-cancel {
  background-color: transparent;
  color: var(--text-secondary);
  border: 1px solid var(--border-color);
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel:hover {
  background-color: var(--bg-hover);
  color: var(--text-primary);
}

.btn-confirm {
  background-color: var(--bg-button);
  color: var(--text-inverse);
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}

.btn-confirm:hover:not(:disabled) {
  background-color: var(--bg-button-hover);
}

.btn-confirm:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* =========================================
   SIDEBAR FOLDER SECTION
========================================= */
.folder-list-section {
  margin-top: 32px;
}

.section-label {
  font-size: 11px;
  font-weight: 600;
  color: var(--text-muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 12px;
  padding: 0 8px;
}

.truncate-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  width: 100%;
}

/* =========================================
   QUICK ACCESS SECTION
========================================= */
.quick-access-section {
  padding: 0 4px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px;
  margin-bottom: 4px;
}

.section-label-bold {
  font-size: 12px;
  font-weight: 700;
  color: #ffffff;
}

.btn-icon-tiny {
  background: none;
  border: none;
  color: var(--text-secondary);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon-tiny:hover {
  color: #ffffff;
}

.collapsible-wrapper {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.collapsible-header {
  gap: 8px;
  padding: 8px;
  font-size: 13px;
  color: #dddddd;
}

.collapsible-content {
  padding: 4px 8px 16px 28px;
  /* Indentasi ke dalam */
}

.empty-drag-text {
  font-size: 13px;
  color: var(--text-muted);
  line-height: 1.5;
}
</style>
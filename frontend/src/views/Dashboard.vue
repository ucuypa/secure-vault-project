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
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <h2 class="menu-title">Home</h2>

      <div class="menu-item" :class="{ active: currentFilter === 'all' }" @click="setFilter('all')">
        <LayoutGrid class="icon-small" />
        <span>All Files</span>
      </div>

      <div class="menu-item" :class="{ active: currentFilter === 'photos' }" @click="setFilter('photos')">
        <ImageIcon class="icon-small" />
        <span>Photos</span>
      </div>

      <div class="menu-item" :class="{ active: currentFilter === 'documents' }" @click="setFilter('documents')">
        <FileText class="icon-small" />
        <span>Documents</span>
      </div>

      <div class="menu-item" :class="{ active: currentFilter === 'videos' }" @click="setFilter('videos')">
        <Video class="icon-small" />
        <span>Videos</span>
      </div>

      <div class="menu-item" :class="{ active: currentFilter === 'others' }" @click="setFilter('others')">
        <File class="icon-small" />
        <span>Others</span>
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

        <div class="profile-dropdown-wrapper">

          <div class="user-avatar" @click="toggleProfileDropdown">
            <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-small" />
            <span v-else>{{ userInitial }}</span>
          </div>

          <div v-if="showProfileDropdown" class="profile-dropdown" @click.stop>

            <div class="profile-header">
              <span class="user-email">{{ user.email || 'user@gmail.com' }}</span>
              <button class="btn-icon-tiny" @click="closeProfileDropdown">
                <X class="icon-small" />
              </button>
            </div>

            <div class="profile-body">
              <div class="user-avatar-large">
                <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-large" />
                <span v-else>{{ userInitial }}</span>
              </div>
              <p class="profile-greeting">Hi, {{ user.name || 'User' }}!</p>
            </div>

            <div class="dropdown-divider-full"></div>

            <router-link to="/profile" class="profile-menu-item" @click="closeProfileDropdown">
              <User class="icon-small" />
              <span>Manage account</span>
            </router-link>

            <div class="dropdown-divider-full"></div>

            <div class="profile-menu-item" @click="handleLogout">
              <LogOut class="icon-small" />
              <span>Log out</span>
            </div>

          </div>
        </div>
      </header>

      <section class="content-area">
        <div class="content-header">
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
                <td class="text-right dropdown-cell">
                  <button class="btn-icon" @click.stop="toggleDropdown(file.id)">
                    <MoreVertical class="icon" />
                  </button>

                  <div v-if="activeDropdownId === file.id" class="action-dropdown" @click.stop>
                    <div class="dropdown-item" @click="downloadFile(file)">
                      <Download class="icon-small" />
                      <span>Download</span>
                    </div>
                    <div class="dropdown-item" @click="renameFile(file)">
                      <Edit2 class="icon-small" />
                      <span>Rename</span>
                    </div>
                    <div class="dropdown-item" @click="showFileInfo(file)">
                      <Info class="icon-small" />
                      <span>File information</span>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="dropdown-item text-danger" @click="deleteFile(file)">
                      <Trash2 class="icon-small" />
                      <span>Delete</span>
                    </div>
                  </div>
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

    <div v-if="showRenameModal" class="modal-overlay" @click.self="closeRenameModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>Rename</h3>
          <button @click="closeRenameModal" class="btn-icon">
            <X class="icon-small" />
          </button>
        </div>
        <div class="modal-body">
          <input v-model="newFileName" type="text" placeholder="New name" class="modal-input"
            @keyup.enter="submitRename" autofocus />
        </div>
        <div class="modal-footer">
          <button @click="closeRenameModal" class="btn-cancel">Cancel</button>
          <button @click="submitRename" :disabled="!newFileName.trim() || isRenaming" class="btn-confirm">
            {{ isRenaming ? 'Saving...' : 'Rename' }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="showInfoModal" class="modal-overlay" @click.self="closeInfoModal">
      <div class="modal-card">
        <div class="modal-header">
          <h3>File Information</h3>
          <button @click="closeInfoModal" class="btn-icon">
            <X class="icon-small" />
          </button>
        </div>
        <div class="modal-body info-body">
          <div class="info-row">
            <span class="info-label">Name</span>
            <span class="info-value">{{ fileInfoData?.original_name }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Type</span>
            <span class="info-value">{{ fileInfoData?.mime_type === 'directory' ? 'Folder' : fileInfoData?.mime_type
            }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Size</span>
            <span class="info-value">{{ fileInfoData?.mime_type === 'directory' ? '--' :
              formatSize(fileInfoData?.file_size) }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Uploaded</span>
            <span class="info-value">{{ formatDate(fileInfoData?.created_at) }}</span>
          </div>
          <div class="info-row" v-if="fileInfoData?.mime_type !== 'directory'">
            <span class="info-label">Security</span>
            <span class="info-value text-success flex-align">
              <Lock class="icon-tiny" style="margin-right: 4px;" /> AES-256 Encrypted
            </span>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="closeInfoModal" class="btn-confirm">Understood</button>
        </div>
      </div>
    </div>

    <ConfirmModal :show="showDeleteModal" title="Delete Confirmation"
      :message="`Are you sure you want to permanently delete '${fileToDelete?.original_name}'?`" confirmText="Delete"
      @close="showDeleteModal = false" @confirm="executeDelete" />

    <div v-if="activeDropdownId" class="invisible-overlay" @click="closeDropdown"></div>
    <div v-if="showProfileDropdown" class="invisible-overlay" @click="closeProfileDropdown"></div>

    <NotificationModal :show="showNotificationModal" :type="notificationType" :title="notificationTitle"
      :message="notificationMessage" @close="showNotificationModal = false" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from '../api/axios';
import {
  Settings, Home as HomeIcon, Folder, LayoutGrid, Search, Plus,
  Upload, FolderPlus, ChevronDown, MoreVertical, ArrowDown,
  FileText, FileImage, FileCode, File as FileGeneric,
  User, X, Bell, Grip, Image as ImageIcon, Users, FileQuestion, Trash2, ChevronRight, Video, File, Download, Edit2, Info, LogOut, Lock
} from 'lucide-vue-next';
import NotificationModal from '../components/NotificationModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue'; // PASTIKAN KOMPONEN INI DI-IMPORT

const router = useRouter();
const files = ref([]);
const user = JSON.parse(localStorage.getItem('user') || '{}');
const userInitial = computed(() => user.name ? user.name[0].toUpperCase() : 'U');
const profileImageUrl = ref(null);
const fileInput = ref(null);
const isUploading = ref(false);

const showFolderModal = ref(false);
const newFolderName = ref('');
const isCreatingFolder = ref(false);
const activeDropdownId = ref(null);
const showProfileDropdown = ref(false);

// State Notification Modal
const showNotificationModal = ref(false);
const notificationType = ref('error');
const notificationTitle = ref('');
const notificationMessage = ref('');

// State Delete Modal
const showDeleteModal = ref(false);
const fileToDelete = ref(null);

// State Rename Modal
const showRenameModal = ref(false);
const fileToRename = ref(null);
const newFileName = ref('');
const isRenaming = ref(false);

// State Info Modal
const showInfoModal = ref(false);
const fileInfoData = ref(null);

const triggerNotification = (type, title, message) => {
  notificationType.value = type;
  notificationTitle.value = title;
  notificationMessage.value = message;
  showNotificationModal.value = true;
};

const toggleProfileDropdown = () => { showProfileDropdown.value = !showProfileDropdown.value; };
const closeProfileDropdown = () => { showProfileDropdown.value = false; };
const toggleDropdown = (id) => { activeDropdownId.value = activeDropdownId.value === id ? null : id; };
const closeDropdown = () => { activeDropdownId.value = null; };

// ==========================================
// RENAME LOGIC
// ==========================================
const renameFile = (file) => {
  closeDropdown();
  fileToRename.value = file;
  newFileName.value = file.original_name;
  showRenameModal.value = true;
};

const closeRenameModal = () => {
  showRenameModal.value = false;
  fileToRename.value = null;
  newFileName.value = '';
};

const submitRename = async () => {
  if (!newFileName.value.trim() || newFileName.value === fileToRename.value.original_name) {
    closeRenameModal();
    return;
  }

  isRenaming.value = true;
  try {
    await axios.put(`/files/${fileToRename.value.id}`, {
      original_name: newFileName.value.trim()
    });

    // Update state lokal
    const index = files.value.findIndex(f => f.id === fileToRename.value.id);
    if (index !== -1) {
      files.value[index].original_name = newFileName.value.trim();
    }

    triggerNotification('success', 'Rename Successful', `File renamed to "${newFileName.value.trim()}".`);
    closeRenameModal();
  } catch (error) {
    console.error("Error renaming:", error);
    triggerNotification('error', 'Rename Failed', 'Failed to rename the file. Please try again.');
  } finally {
    isRenaming.value = false;
  }
};

// ==========================================
// FILE INFO LOGIC
// ==========================================
const showFileInfo = (file) => {
  closeDropdown();
  fileInfoData.value = file;
  showInfoModal.value = true;
};

const closeInfoModal = () => {
  showInfoModal.value = false;
  fileInfoData.value = null;
};

// ==========================================
// DELETE LOGIC
// ==========================================
const deleteFile = (file) => {
  closeDropdown();
  fileToDelete.value = file;
  showDeleteModal.value = true; // Munculkan ConfirmModal
};

const executeDelete = async () => {
  if (!fileToDelete.value) return;

  try {
    await axios.delete(`/files/${fileToDelete.value.id}`);

    // Hapus file dari tabel secara real-time
    files.value = files.value.filter(f => f.id !== fileToDelete.value.id);

    showDeleteModal.value = false; // Tutup modal
    triggerNotification('success', 'File Deleted', `"${fileToDelete.value.original_name}" has been permanently removed.`);
  } catch (error) {
    console.error("Error saat menghapus:", error);
    triggerNotification('error', 'Deletion Failed', 'Failed to delete the file. Please try again.');
  } finally {
    fileToDelete.value = null;
  }
};

// ==========================================
// DOWNLOAD LOGIC
// ==========================================
const downloadFile = async (file) => {
  closeDropdown();

  if (file.mime_type === 'directory') {
    triggerNotification('error', 'Download Failed', 'Cannot download a folder directly.');
    return;
  }

  try {
    const response = await axios.get(`/files/${file.id}/download`, {
      responseType: 'blob'
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', file.original_name);
    document.body.appendChild(link);
    link.click();

    link.remove();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Error saat mendownload:", error);
    triggerNotification('error', 'Download Failed', 'The file could not be downloaded. It may have been deleted.');
  }
};

// ==========================================
// UPLOAD & FETCHING LOGIC
// ==========================================

const triggerFileUpload = () => { fileInput.value.click(); };

const fetchFiles = async () => {
  try {
    const response = await axios.get('/files', { params: { parent_id: null } });
    files.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch files", error);
  }
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    triggerNotification('error', 'Upload Failed', 'File size exceeds 10MB limit.');
    event.target.value = '';
    return;
  }

  const formData = new FormData();
  formData.append('file', file);

  isUploading.value = true;

  try {
    const response = await axios.post('/files', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    triggerNotification('success', 'Upload Successful', response.data.message);
    fetchFiles();
  } catch (error) {
    console.error('Upload error:', error);
    triggerNotification('error', 'Upload Failed', 'Upload failed. Make sure the file is valid.');
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

const openFolderModal = () => { newFolderName.value = ''; showFolderModal.value = true; };
const closeFolderModal = () => { showFolderModal.value = false; newFolderName.value = ''; };

const submitFolder = async () => {
  if (!newFolderName.value.trim()) return;

  isCreatingFolder.value = true;
  try {
    await axios.post('/files', { name: newFolderName.value.trim(), parent_id: null });
    fetchFiles();
    closeFolderModal();
    triggerNotification('success', 'Folder Created', `Folder "${newFolderName.value}" has been created.`);
  } catch (error) {
    console.error("Error creating folder:", error);
    triggerNotification('error', 'Folder Creation Failed', 'Failed to create folder.');
  } finally {
    isCreatingFolder.value = false;
  }
};

const currentFilter = ref('all');
const setFilter = (filterType) => { currentFilter.value = filterType; };
const searchQuery = ref('');

const filteredFiles = computed(() => {
  let result = files.value;

  if (currentFilter.value === 'folders') { result = result.filter(file => file.mime_type === 'directory'); }
  else if (currentFilter.value === 'photos') { result = result.filter(file => file.mime_type.includes('image')); }
  else if (currentFilter.value === 'videos') { result = result.filter(file => file.mime_type.includes('video')); }
  else if (currentFilter.value === 'documents') {
    result = result.filter(file => file.mime_type.includes('pdf') || file.mime_type.includes('document') || file.mime_type.includes('msword') || file.mime_type.includes('text'));
  }
  else if (currentFilter.value === 'others') {
    result = result.filter(file => file.mime_type !== 'directory' && !file.mime_type.includes('image') && !file.mime_type.includes('video') && !file.mime_type.includes('pdf') && !file.mime_type.includes('document') && !file.mime_type.includes('msword') && !file.mime_type.includes('text'));
  }

  if (searchQuery.value.trim() !== '') {
    const keyword = searchQuery.value.toLowerCase();
    result = result.filter(file => file.original_name.toLowerCase().includes(keyword));
  }

  result = result.sort((a, b) => {
    const isFolderA = a.mime_type === 'directory';
    const isFolderB = b.mime_type === 'directory';
    if (isFolderA && !isFolderB) return -1;
    if (!isFolderA && isFolderB) return 1;
    return a.original_name.localeCompare(b.original_name);
  });

  return result;
});

const openFolder = (folder) => {
  if (folder.mime_type !== 'directory') return;
  router.push({ path: `/folder/${folder.id}`, query: { name: folder.original_name } });
};

const handleLogout = () => {
  if (confirm('Logout?')) {
    localStorage.clear();
    router.push('/login');
  }
  closeProfileDropdown();
};

onMounted(() => {
  fetchFiles();

  // Ambil foto profil dari localStorage
  const savedImage = localStorage.getItem('profileImage');
  if (savedImage) {
    profileImageUrl.value = savedImage;
  }
});
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
  --color-success: #10b981;

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

/* Cari class .user-avatar dan tambahkan overflow: hidden jika belum ada */
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

  /* TAMBAHKAN INI AGAR GAMBAR TIDAK KELUAR DARI LINGKARAN */
  overflow: hidden;
}

/* Tambahkan class ini untuk mengatur ukuran gambarnya */
.avatar-img-small,
.avatar-img-large {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Khusus Dashboard, pastikan user-avatar-large juga punya overflow: hidden */
.user-avatar-large {
  width: 56px;
  height: 56px;
  background-color: #f97316;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 24px;
  color: white;

  /* TAMBAHKAN INI */
  overflow: hidden;
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
   TABLE STYLES 
========================================= */
.table-wrapper {
  width: 100%;
  overflow-x: visible;
  flex: 0 0 auto;
  margin-bottom: auto;
  padding-bottom: 150px;
}

.file-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  height: max-content;
}

.file-table th,
.file-table td {
  padding: 12px 16px;
  border-bottom: 1px solid var(--border-color);
  vertical-align: middle;
}

.file-table th {
  color: var(--text-secondary);
  font-weight: 500;
  font-size: 13px;
  padding-bottom: 16px;
}

.file-row {
  transition: background-color 0.2s;
  height: 56px;
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

.flex-align {
  display: flex;
  align-items: center;
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

.text-success {
  color: var(--color-success);
}

/* =========================================
   MODAL STYLES (GENERAL)
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

/* FILE INFO SPECIFIC */
.info-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px dashed var(--border-color);
  padding-bottom: 8px;
}

.info-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.info-label {
  color: var(--text-secondary);
  font-size: 13px;
}

.info-value {
  color: var(--text-primary);
  font-size: 13px;
  font-weight: 500;
  text-align: right;
  max-width: 65%;
  word-break: break-word;
}

/* =========================================
   ACTION DROPDOWN (TRIPLE DOT MENU)
========================================= */
.dropdown-cell {
  position: relative;
}

.invisible-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 90;
}

.action-dropdown {
  position: absolute;
  right: 40px;
  top: 10px;
  background-color: #2a2a2a;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 8px 0;
  min-width: 200px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
  z-index: 999;
  display: flex;
  flex-direction: column;
  text-align: left;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  font-size: 13px;
  color: var(--text-primary);
  cursor: pointer;
  transition: background-color 0.2s;
}

.dropdown-item:hover {
  background-color: #3a3a3a;
}

.dropdown-item .icon-small {
  color: var(--text-secondary);
}

.dropdown-divider {
  height: 1px;
  background-color: var(--border-color);
  margin: 4px 0;
}

.text-danger {
  color: #ef4444 !important;
}

.text-danger .icon-small {
  color: #ef4444;
}

/* =========================================
   PROFILE DROPDOWN MODAL
========================================= */
.profile-dropdown-wrapper {
  position: relative;
}

.profile-dropdown {
  position: absolute;
  top: 48px;
  right: 0;
  background-color: #262626;
  border: 1px solid var(--border-color);
  border-radius: 8px;
  width: 260px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.6);
  z-index: 100;
  display: flex;
  flex-direction: column;
}

.profile-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 16px;
}

.user-email {
  font-size: 12px;
  color: var(--text-secondary);
  flex: 1;
  text-align: center;
  padding-left: 20px;
}

.profile-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 12px 16px 20px 16px;
  gap: 12px;
}

.user-avatar-large {
  width: 56px;
  height: 56px;
  background-color: #f97316;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 24px;
  color: white;
}

.profile-greeting {
  font-size: 16px;
  color: var(--text-primary);
  font-weight: 500;
}

.dropdown-divider-full {
  height: 1px;
  background-color: var(--border-color);
  width: 100%;
}

.profile-menu-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 20px;
  font-size: 14px;
  color: var(--text-primary);
  cursor: pointer;
  transition: background-color 0.2s;
  text-decoration: none;
}

.profile-menu-item:hover {
  background-color: #333333;
}

.profile-menu-item .icon-small {
  color: var(--text-secondary);
}
</style>
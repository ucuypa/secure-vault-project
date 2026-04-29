<template>
  <div class="dashboard-layout">
    
    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <Settings class="icon" />
      </div>
      <nav class="icon-nav">
        <div class="nav-item active">
          <HomeIcon class="icon" />
          <span>Home</span>
        </div>
        <div class="nav-item">
          <Folder class="icon" />
          <span>Folder</span>
        </div>
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <h2 class="menu-title">Home</h2>
      <div class="menu-item active">
        <LayoutGrid class="icon-small" />
        <span>All Files</span>
      </div>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
          <button @click="triggerFileUpload" class="btn-new">
            <Plus class="icon-small" /> New
          </button>
          
          <div class="search-wrapper">
            <Search class="search-icon" />
            <input type="text" placeholder="Search" class="search-input" />
          </div>
        </div>

        <div class="user-avatar">
          {{ userInitial }}
        </div>
      </header>

      <section class="content-area">
        <div class="content-header">
          <h1 class="page-title">All Files</h1>
          <div class="action-buttons">
            <button @click="triggerFileUpload" :disabled="isUploading" class="btn-new">
              <Plus v-if="!isUploading" class="icon-small" /> {{ isUploading ? ' Encrypting and Uploading...' : 'New' }}
            </button>
            <input type="file" ref="fileInput" @change="handleFileUpload" class="hidden-input" accept=".pdf, .doc, .docx, .jpg, .png, .zip">
            <button class="btn-action">
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
              <tr v-for="file in files" :key="file.id" class="file-row">
                <td class="col-name">
                   <component :is="getFileIcon(file.mime_type)" class="icon file-icon" :class="getIconColor(file.mime_type)" />
                   <span class="file-name">{{ file.original_name }}</span>
                </td>
                <td class="col-date">{{ formatDate(file.created_at) }}</td>
                <td class="col-size">{{ formatSize(file.file_size) }}</td>
                <td class="col-action text-right">
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from '../api/axios';
import { 
  Settings, Home as HomeIcon, Folder, LayoutGrid, Search, Plus, 
  Upload, FolderPlus, ChevronDown, MoreVertical, ArrowDown,
  FileText, FileImage, FileCode, File as FileGeneric
} from 'lucide-vue-next';

const files = ref([]);
const fileInput = ref(null);
const isUploading = ref(false); //State loading indicator

const triggerFileUpload = () => {
  fileInput.value.click();
};

const fetchFiles = async () => {
  try {
    const response = await axios.get('/files');
    files.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch files", error);
  }
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // Check file size limit
  if (file.size > 10 * 1024 * 1024) {
    alert('File size exceeds 10MB limit.');
    event.target.value = ''; // Reset file input
    return;
  }

  const formData = new FormData();
  formData.append('file', file);

  isUploading.value = true;

  try {
    // Send to laravel backend
    const response = await axios.post('/files', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });

    alert('Success: ' + response.data.message);
    fetchFiles(); // Refresh file list
  } catch (error) {
    // Error handling
    console.error('Upload error:', error);
    alert('Upload failed. Make sure file is valid and try again.');
  } finally {
    isUploading.value = false;
    event.target.value = ''; // Reset file input
  }
};

const getFileIcon = (mime) => {
  if (mime.includes('image')) return FileImage;
  if (mime.includes('pdf')) return FileText;
  if (mime.includes('javascript') || mime.includes('json')) return FileCode;
  return FileGeneric;
};

const getIconColor = (mime) => {
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

onMounted(fetchFiles);
</script>

<style scoped>
/* =========================================
   CSS VARIABLES FOR DARK THEME
========================================= */
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
  width: 64px;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 24px 0;
  gap: 32px;
}

.icon-brand {
  background-color: var(--text-muted);
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
}

.icon-nav {
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: var(--text-secondary);
  cursor: pointer;
  transition: color 0.2s;
}

.nav-item.active, .nav-item:hover {
  color: var(--text-primary);
}

.nav-item span {
  font-size: 10px;
  margin-top: 4px;
}

.sidebar-menu {
  width: 220px;
  background-color: var(--bg-sidebar);
  border-right: 1px solid var(--border-color);
  padding: 24px 16px;
}

.menu-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 24px;
  padding: 0 8px;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  border-radius: 6px;
  color: var(--text-secondary);
  cursor: pointer;
  gap: 12px;
}

.menu-item.active {
  background-color: var(--bg-hover);
  color: var(--text-primary);
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

.table-wrapper {
  width: 100%;
  overflow-x: auto;
}

.file-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.file-table th {
  color: var(--text-secondary);
  font-weight: 500;
  font-size: 13px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--border-color);
}

.file-table td {
  padding: 16px 0;
  border-bottom: 1px solid var(--border-color);
  font-size: 14px;
}

.file-row:hover td {
  background-color: var(--bg-sidebar);
}

.col-name {
  display: flex;
  align-items: center;
  gap: 50px;
  padding-left: 8px;
}

.file-name {
  color: var(--text-primary);
  font-size: 14px;
  white-space: nowrap; /* Mencegah nama file turun ke bawah jika terlalu panjang */
  overflow: hidden;
  text-overflow: ellipsis;
}

.col-date, .col-size {
  color: var(--text-secondary);
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

.text-right {
  text-align: right;
}

.inline {
  display: inline;
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
.color-danger { color: #ef4444; }
.color-info { color: #3b82f6; }
.color-muted { color: #9ca3af; }
</style>
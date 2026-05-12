<template>
  <div class="dashboard-layout">

    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <router-link to="/dashboard" class="nav-item active">
          <img src="/padlockwebdev.svg" alt="Logo" class="logo" />
        </router-link>
      </div>
      <nav class="icon-nav">
        <router-link to="/dashboard" class="nav-item" style="text-decoration: none;">
          <HomeIcon class="icon" />
          <span>Home</span>
        </router-link>

        <router-link to="/dashboard" class="nav-item" style="text-decoration: none;">
          <Folder class="icon" />
          <span>Folder</span>
        </router-link>

        <router-link to="/activity" class="nav-item active" style="text-decoration: none;">
          <Bell class="icon" />
          <span>Activity</span>
        </router-link>
      </nav>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
          <div class="search-wrapper">
            <Search class="search-icon" />
            <input type="text" placeholder="Search activity logs..." class="search-input" />
          </div>
        </div>

        <div class="profile-dropdown-wrapper">
          <div class="user-avatar" @click="toggleProfileDropdown">
            <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-small" />
            <span v-else>{{ userInitial }}</span>
          </div>

          <div v-if="showProfileDropdown" class="profile-dropdown" @click.stop>
            <div class="profile-header">
              <span class="user-email">{{ userObj.email || 'user@gmail.com' }}</span>
              <button class="btn-icon-tiny" @click="closeProfileDropdown">
                <X class="icon-small" />
              </button>
            </div>

            <div class="profile-body">
              <div class="user-avatar-large">
                <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-small" />
                <span v-else>{{ userInitial }}</span>
              </div>
              <p class="profile-greeting">Hi, {{ userObj.name || 'User' }}!</p>
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
          <h1 class="page-title">Activity Logs</h1>
        </div>

        <div class="table-wrapper">
          <table class="file-table">
            <thead>
              <tr>
                <th>Action</th>
                <th>Target File</th>
                <th>IP Address</th>
                <th>Date & Time</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id" class="file-row">
                <td>
                  <span :class="['badge', getBadgeClass(log.action)]">
                    {{ log.action }}
                  </span>
                </td>
                <td>
                  <div class="name-wrapper">
                    <span v-if="log.vault_file" class="file-name text-primary">
                      {{ log.vault_file.original_name }}
                    </span>
                    <span v-else class="text-muted">--</span>
                  </div>
                </td>
                <td class="text-muted">{{ log.ip_address }}</td>
                <td class="text-muted">{{ formatDate(log.created_at) }}</td>
              </tr>
              <tr v-if="logs.length === 0">
                <td colspan="4" class="empty-state">No activity logs found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>

    <div v-if="showProfileDropdown" class="invisible-overlay" @click="closeProfileDropdown"></div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router'; // Tambahkan useRouter
import axios from '../api/axios.js';
import {
  Home as HomeIcon, Folder, Bell, User,
  LayoutGrid, Trash2, Plus, ChevronDown, Search,
  Image as ImageIcon, FileText, Video, File, X, LogOut // Import X dan LogOut
} from 'lucide-vue-next';

const router = useRouter(); // Inisialisasi router
const logs = ref([]);
const profileImageUrl = ref(null);

// State Dropdown
const showProfileDropdown = ref(false);

// Get user info for Avatar
const userObj = JSON.parse(localStorage.getItem('user') || '{}');
const userInitial = computed(() => userObj.name ? userObj.name[0].toUpperCase() : 'U');

// Fungsi Dropdown
const toggleProfileDropdown = () => { showProfileDropdown.value = !showProfileDropdown.value; };
const closeProfileDropdown = () => { showProfileDropdown.value = false; };

// Fungsi Logout
const handleLogout = () => {
  if (confirm('Logout?')) {
    localStorage.clear();
    router.push('/login');
  }
  closeProfileDropdown();
};

const fetchLogs = async () => {
  try {
    const response = await axios.get('/activity');
    logs.value = response.data.data;
  } catch (error) {
    console.error("Failed to fetch activity logs", error);
  }
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString(undefined, options);
};

const getBadgeClass = (action) => {
  if (!action) return 'badge-muted';
  const act = action.toUpperCase();
  if (act.includes('LOGIN') || act.includes('UPLOAD') || act.includes('SUCCESS')) return 'badge-success';
  if (act.includes('DELETE') || act.includes('FAILED') || act.includes('ERROR')) return 'badge-danger';
  return 'badge-info';
};

onMounted(() => {
  fetchLogs();

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
  overflow: hidden;
}

.avatar-img-small {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* =========================================
   PROFILE DROPDOWN
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
  overflow: hidden;
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

.invisible-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  z-index: 90;
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
   BADGES (Khusus Activity)
========================================= */
.badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.05em;
}

.badge-success {
  background-color: rgba(16, 185, 129, 0.15);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-danger {
  background-color: rgba(239, 68, 68, 0.15);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.badge-info {
  background-color: rgba(59, 130, 246, 0.15);
  color: #3b82f6;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-muted {
  background-color: rgba(156, 163, 175, 0.15);
  color: #9ca3af;
  border: 1px solid rgba(156, 163, 175, 0.3);
}
</style>
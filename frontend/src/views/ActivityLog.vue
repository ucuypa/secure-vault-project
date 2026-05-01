<template>
  <div class="dashboard-layout">
    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <router-link to="/dashboard" class="nav-item">
          <img src="/padlockwebdev.svg" alt="Logo" class="logo" />
        </router-link>
      </div>
      <nav class="icon-nav">
        <div class="nav-item">
          <router-link to="/dashboard" class="nav-item" style="text-decoration: none;">
            <HomeIcon class="icon" />
            <span>Home</span>
          </router-link>
        </div>

        <div class="nav-item">
          <router-link to="/dashboard" class="nav-item" style="text-decoration: none;">
            <Folder class="icon" />
            <span>Folder</span>
          </router-link>
        </div>

        <div class="nav-item active">
          <router-link to="/activity" class="nav-item active" style="text-decoration: none;">
            <Bell class="icon" />
            <span>Activity</span>
          </router-link>
        </div>

        <div class="nav-item">
          <router-link to="/profile" class="nav-item" style="text-decoration: none;">
            <User class="icon" />
            <span>Profile</span>
          </router-link>
        </div>
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <h2 class="menu-title">Home</h2>

      <div class="menu-item">
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

      <div class="quick-access-section">
        <div class="section-header">
          <span class="section-label-bold">Quick access</span>
          <button class="btn-icon-tiny">
            <Plus class="icon-tiny" />
          </button>
        </div>

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
            <input type="text" placeholder="Search activity logs..." class="search-input" />
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
                    <span v-if="log.vault_file_id" class="file-name text-primary">
                      File ID: {{ log.vault_file_id }}
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
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import activityService from '../api/activity.js';
import { 
  Home as HomeIcon, Folder, Bell, User, 
  LayoutGrid, Users, Trash2, Plus, ChevronDown, Search 
} from 'lucide-vue-next';

const logs = ref([]);

// Get user info for Avatar
const userObj = JSON.parse(localStorage.getItem('user') || '{}');
const userInitial = computed(() => userObj.name ? userObj.name[0].toUpperCase() : 'U');

const fetchLogs = async () => {
  try {
    const response = await activityService.getLogs();
    logs.value = response.data;
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

onMounted(fetchLogs);
</script>

<style scoped>
/* =========================================
   EXACT COPY OF DASHBOARD CSS
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

/* Sidebars */
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

.menu-item.active,
.menu-item:hover {
  background-color: var(--bg-hover);
  color: var(--text-primary);
}

/* Quick Access */
.quick-access-section {
  padding: 0 4px;
  margin-top: 24px;
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

/* Main Content & Header */
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
  color: white;
}

/* Content Area & Table */
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

.name-wrapper {
  display: flex;
  align-items: center;
  gap: 12px;
}

.text-primary {
  color: var(--text-primary);
}

.text-muted {
  color: var(--text-secondary);
  font-size: 14px;
}

.empty-state {
  text-align: center;
  color: var(--text-muted);
  padding: 40px 0 !important;
}

/* Icons */
.icon { width: 20px; height: 20px; }
.icon-small { width: 16px; height: 16px; }
.icon-tiny { width: 12px; height: 12px; }

/* =========================================
   ACTIVITY SPECIFIC STYLES (BADGES)
========================================= */
.badge {
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: bold;
  letter-spacing: 0.05em;
  display: inline-block;
}

.badge-success {
  background-color: rgba(16, 185, 129, 0.15);
  color: #10b981;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.badge-info {
  background-color: rgba(59, 130, 246, 0.15);
  color: #3b82f6;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.badge-danger {
  background-color: rgba(239, 68, 68, 0.15);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.badge-muted {
  background-color: rgba(156, 163, 175, 0.15);
  color: #9ca3af;
  border: 1px solid rgba(156, 163, 175, 0.3);
}
</style>
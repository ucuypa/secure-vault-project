<template>
  <div class="dashboard-layout">

    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <router-link to="/dashboard" class="nav-item">
          <img src="/padlockwebdev.svg" alt="Logo" />
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
        <div class="nav-item">
          <router-link to="/activity" class="nav-item" style="text-decoration: none;">
            <Bell class="icon" />
            <span>Activity</span>
          </router-link>
        </div>
        <div class="nav-item active">
          <router-link to="/profile" class="nav-item active" style="text-decoration: none;">
            <User class="icon" />
            <span>Profile</span>
          </router-link>
        </div>
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <h2 class="menu-title">Profile</h2>

      <div class="menu-nav-list">
        <div class="menu-item active">
          <User class="icon-small" />
          <span>My Account</span>
        </div>
        <div style="height: 1px; background-color: var(--border-color); margin: 16px 0;"></div>

        <div class="menu-item logout-item" @click="handleLogout">
          <LogOut class="icon-small" />
          <span>Logout</span>
        </div>
      </div>
    </aside>

    <main class="main-content">

      <header class="top-header">
        <div class="header-left">
          <div class="search-wrapper">
            <Search class="search-icon" />
            <input type="text" placeholder="Search settings..." class="search-input" />
          </div>
        </div>

        <div class="user-avatar">
          <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-small" />
          <span v-else>{{ userInitial }}</span>
        </div>
      </header>

      <section class="content-area">
        <div class="content-header">
          <h1 class="page-title">Profile Settings</h1>
        </div>

        <div class="profile-wrapper">
          <div class="profile-main-card">
            <div class="profile-header-content">
              <div class="avatar-section-wrapper">
                <div class="avatar-upload-box" @click="triggerFileSelect" title="Click to change profile picture">
                  <img v-if="profileImageUrl" :src="profileImageUrl" class="profile-img-large" />
                  <div v-else class="large-avatar-placeholder">{{ userInitial }}</div>

                  <div class="avatar-hover-overlay">
                    <Pencil class="icon-white" />
                  </div>
                </div>

                <button v-if="profileImageUrl" class="btn-delete-photo-side" @click.stop="triggerRemoveProfile"
                  title="Remove photo">
                  <Trash2 class="icon-trash-action" />
                  <span>Remove Photo</span>
                </button>
              </div>

              <input type="file" ref="fileInputRef" class="hidden-input" accept="image/*" @change="handleFileChange" />

              <div class="user-info-brief">
                <h2>{{ profileForm.name || 'Loading...' }}</h2>
                <p class="text-muted">{{ profileForm.email || '...' }}</p>
                <span class="status-badge">Personal Account</span>
              </div>
            </div>

            <div class="card-divider"></div>

            <form @submit.prevent="updateProfile" class="profile-form-full">
              <div class="form-section">
                <h3 class="section-subtitle">Account Information</h3>
                <div class="form-group">
                  <label>Full Name</label>
                  <input v-model="profileForm.name" type="text" class="form-control" placeholder="Enter your full name"
                    required />
                </div>
              </div>

              <div class="form-section">
                <h3 class="section-subtitle">Security</h3>
                <div class="form-group">
                  <label>Change Password</label>
                  <input v-model="profileForm.password" type="password" class="form-control"
                    placeholder="Enter new password (leave blank to keep current)" />
                </div>
                <div class="form-group" v-if="profileForm.password">
                  <label>Confirm Password</label>
                  <input v-model="profileForm.password_confirmation" type="password" class="form-control"
                    placeholder="Re-type new password" />
                </div>
              </div>

              <div class="form-footer">
                <button type="button" class="btn-secondary" @click="resetForm">Reset</button>
                <button type="submit" class="btn-primary" :disabled="isSaving">
                  {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </main>
    <ConfirmModal :show="showConfirmModal" title="Remove Confirmation"
      message="Are you sure you want to remove this? This action cannot be undone." confirmText="Remove"
      @close="showConfirmModal = false" @confirm="executeRemove" />

    <NotificationModal :show="showNotificationModal" :type="notificationType" :title="notificationTitle"
      :message="notificationMessage" @close="showNotificationModal = false" />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import profileService from '../api/profile.js';
import {
  Home as HomeIcon, Folder, User, Bell, Search,
  LogOut, Pencil, Trash2
} from 'lucide-vue-next';
import NotificationModal from '../components/NotificationModal.vue';
import ConfirmModal from '../components/ConfirmModal.vue';

const router = useRouter();
const isSaving = ref(false);
const fileInputRef = ref(null);
const profileImageUrl = ref(null);

const showNotificationModal = ref(false);
const notificationType = ref('error');
const notificationTitle = ref('');
const notificationMessage = ref('');
const showLogoutModal = ref(false);
const showConfirmModal = ref(false);

const triggerNotification = (type, title, message) => {
  notificationType.value = type;
  notificationTitle.value = title;
  notificationMessage.value = message;
  showNotificationModal.value = true;
};

const triggerRemoveProfile = () => {
  showConfirmModal.value = true;
};

const executeRemove = async () => {
  try {
    localStorage.removeItem('profileImage');

    // Hapus URL lokal agar UI langsung update
    profileImageUrl.value = null;

    // Tutup modal
    showConfirmModal.value = false;

    // Munculkan notifikasi sukses
    triggerNotification('success', 'Photo Removed', 'Your profile photo has been removed.');
  } catch (error) {
    console.error('Failed to remove photo:', error);
    showConfirmModal.value = false;
    triggerNotification('error', 'Action Failed', 'Failed to remove photo. Please try again.');
  }
};

const profileForm = ref({
  name: '',
  email: '', // Disimpan di state hanya untuk ditampilkan di header brief
  password: '',
  password_confirmation: ''
});

const userInitial = computed(() => {
  return profileForm.value.name ? profileForm.value.name.charAt(0).toUpperCase() : 'U';
});

const fetchProfileData = async () => {
  try {
    const response = await profileService.getProfile();
    const userData = response.data.data;

    profileForm.value.name = userData.name;
    profileForm.value.email = userData.email;

    localStorage.setItem('user', JSON.stringify(userData));
  } catch (error) {
    console.error("Gagal mengambil data profil:", error);
  }
};

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  profileForm.value.name = user.name || '';
  profileForm.value.email = user.email || '';

  const savedImage = localStorage.getItem('profileImage');
  if (savedImage) profileImageUrl.value = savedImage;

  fetchProfileData();
});

const triggerFileSelect = () => fileInputRef.value.click();

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      profileImageUrl.value = e.target.result;
      localStorage.setItem('profileImage', e.target.result);
    };
    reader.readAsDataURL(file);
  }
};

const updateProfile = async () => {
  if (profileForm.value.password && profileForm.value.password !== profileForm.value.password_confirmation) {
    triggerNotification('error', 'Password Mismatch', 'Password confirmation does not match!');
    return;
  }

  isSaving.value = true;

  try {
    // Hanya mengirimkan data nama dan password (tanpa email)
    const response = await profileService.updateProfile({
      name: profileForm.value.name,
      password: profileForm.value.password,
      password_confirmation: profileForm.value.password_confirmation
    });

    const updatedUser = response.data.data;
    localStorage.setItem('user', JSON.stringify(updatedUser));

    profileForm.value.password = '';
    profileForm.value.password_confirmation = '';

    triggerNotification('success', 'Profile Updated', 'Profile saved successfully!');
  } catch (error) {
    console.error("Error updating profile:", error);
    if (error.response && error.response.data && error.response.data.message) {
      triggerNotification('error', 'Update Failed', `Update failed: ${error.response.data.message}`);
    } else {
      triggerNotification('error', 'Update Failed', 'Failed to update profile. Please check your inputs.');
    }
  } finally {
    isSaving.value = false;
  }
};

const resetForm = () => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  profileForm.value.name = user.name;
  profileForm.value.password = '';
  profileForm.value.password_confirmation = '';
};

const handleLogout = () => {
  showLogoutModal.value = true;
};

const executeLogout = () => {
  showLogoutModal.value = false; // Tutup modal dulu
  localStorage.removeItem('user');
  localStorage.removeItem('token');
  router.push('/login');
};
</script>

<style scoped>
/* =========================================
   PAGE STYLES
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
  --bg-card: #1e1e1e;
  --border-color: #2e2e2e;
  --text-primary: #ffffff;
  --text-secondary: #a0a0a0;
  --text-muted: #6e6e6e;
  --text-inverse: #000000;
  --color-danger: #ef4444;

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

.menu-item.active {
  background-color: var(--bg-hover);
  color: var(--text-primary);
  font-weight: 500;
}

.menu-item:hover {
  background-color: var(--bg-hover);
  color: var(--text-primary);
}

.logout-item:hover {
  color: var(--color-danger);
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
  background-color: var(--bg-button);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  cursor: pointer;
  color: var(--text-inverse);
  overflow: hidden;
}

.avatar-img-small {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Content Area */
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

/* Icons */
.icon {
  width: 20px;
  height: 20px;
}

.icon-small {
  width: 16px;
  height: 16px;
}

.icon-white {
  width: 24px;
  height: 24px;
  color: #fff;
}

.icon-trash-action {
  width: 16px;
  height: 16px;
}

/* =========================================
   PROFILE SPECIFIC STYLES
========================================= */
.profile-wrapper {
  max-width: 800px;
}

.profile-main-card {
  background-color: var(--bg-sidebar);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  overflow: hidden;
}

.profile-header-content {
  padding: 32px;
  display: flex;
  align-items: center;
  gap: 32px;
}

.avatar-section-wrapper {
  display: flex;
  align-items: center;
  gap: 20px;
}

.avatar-upload-box {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  overflow: hidden;
  background-color: var(--bg-button);
  cursor: pointer;
  position: relative;
}

.profile-img-large {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.large-avatar-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  font-weight: 800;
  color: var(--text-inverse);
}

.avatar-hover-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s;
}

.avatar-upload-box:hover .avatar-hover-overlay {
  opacity: 1;
}

.btn-delete-photo-side {
  background: var(--bg-main);
  border: 1px solid var(--border-color);
  color: var(--text-secondary);
  padding: 8px 12px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 12px;
  transition: 0.2s;
}

.btn-delete-photo-side:hover {
  color: var(--color-danger);
  border-color: var(--color-danger);
  background-color: rgba(239, 68, 68, 0.1);
}

.user-info-brief h2 {
  font-size: 20px;
  font-weight: 600;
  margin-bottom: 4px;
}

.text-muted {
  color: var(--text-secondary);
  font-size: 14px;
  margin-bottom: 12px;
}

.status-badge {
  display: inline-block;
  background-color: rgba(255, 171, 0, 0.15);
  color: var(--bg-button);
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: bold;
  letter-spacing: 0.05em;
  border: 1px solid rgba(255, 171, 0, 0.3);
}

.card-divider {
  height: 1px;
  background-color: var(--border-color);
}

/* FORM STYLES */
.profile-form-full {
  padding: 32px;
}

.section-subtitle {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 16px;
}

.form-section {
  margin-bottom: 32px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 13px;
  color: var(--text-secondary);
}

.form-control {
  background: var(--bg-main);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  padding: 12px;
  color: var(--text-primary);
  outline: none;
  font-size: 14px;
  transition: border-color 0.2s;
}

.form-control:focus {
  border-color: var(--bg-button);
}

.disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.form-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid var(--border-color);
}

.btn-primary {
  background: var(--bg-button);
  color: var(--text-inverse);
  border: none;
  padding: 10px 24px;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  font-size: 14px;
}

.btn-primary:hover:not(:disabled) {
  background: var(--bg-button-hover);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-secondary {
  background: transparent;
  border: 1px solid var(--border-color);
  color: var(--text-primary);
  padding: 10px 24px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-secondary:hover {
  background: var(--bg-hover);
}

.hidden-input {
  display: none;
}
</style>
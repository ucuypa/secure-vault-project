<template>
  <div class="dashboard-layout">
    
    <aside class="sidebar-iconic">
      <div class="icon-brand">
        <Settings class="icon" />
      </div>
      <nav class="icon-nav">
        <router-link to="/dashboard" class="nav-item">
          <HomeIcon class="icon" />
          <span>Home</span>
        </router-link>
        <router-link to="/dashboard" class="nav-item">
          <Folder class="icon" />
          <span>Folder</span>
        </router-link>
        <router-link to="/profile" class="nav-item active">
          <User class="icon" />
          <span>Profile</span>
        </router-link>
      </nav>
    </aside>

    <aside class="sidebar-menu">
      <div class="menu-nav-list">
        <div class="menu-item active">
          <User class="icon-small" />
          <span>My Account</span>
        </div>
        <div class="divider-menu"></div>
        <div class="menu-item logout-item" @click="handleLogout">
          <LogOut class="icon-small" />
          <span>Logout</span>
        </div>
      </div>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
          <h1 class="page-title">Profile Settings</h1>
        </div>
        <div class="header-right">
          <div class="user-avatar-header">
            <img v-if="profileImageUrl" :src="profileImageUrl" class="avatar-img-small" />
            <span v-else>{{ userInitial }}</span>
          </div>
        </div>
      </header>

      <section class="content-area">
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

                <button 
                  v-if="profileImageUrl" 
                  class="btn-delete-photo-side" 
                  @click.stop="removeProfilePicture" 
                  title="Remove photo"
                >
                  <Trash2 class="icon-trash-action" />
                  <span>Remove Photo</span>
                </button>
              </div>

              <input 
                type="file" 
                ref="fileInputRef" 
                class="hidden-input" 
                accept="image/*" 
                @change="handleFileChange"
              />

              <div class="user-info-brief">
                <h2>{{ profileForm.name }}</h2>
                <p>{{ profileForm.email }}</p>
                <span class="status-badge">Personal Account</span>
              </div>
            </div>

            <div class="card-divider"></div>

            <form @submit.prevent="updateProfile" class="profile-form-full">
              <div class="form-section">
                <h3 class="section-subtitle">Account Information</h3>
                <div class="input-grid">
                  <div class="form-group">
                    <label>Full Name</label>
                    <input v-model="profileForm.name" type="text" class="form-control" placeholder="Enter your full name" />
                  </div>
                  <div class="form-group">
                    <label>Email Address</label>
                    <input v-model="profileForm.email" type="email" class="form-control disabled" disabled />
                    <p class="field-hint"></p>
                  </div>
                </div>
              </div>

              <div class="form-section">
                <h3 class="section-subtitle">Security</h3>
                <div class="form-group">
                  <label>Change Password</label>
                  <input 
                    v-model="profileForm.password" 
                    type="password" 
                    class="form-control" 
                    placeholder="Enter new password (leave blank to keep current)" 
                  />
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { 
  Settings, Home as HomeIcon, Folder, User, 
  LogOut, Pencil, Trash2 
} from 'lucide-vue-next';

const router = useRouter();
const isSaving = ref(false);
const fileInputRef = ref(null);
const profileImageUrl = ref(null);

const profileForm = ref({ name: '', email: '', password: '' });

const userInitial = computed(() => {
  return profileForm.value.name ? profileForm.value.name.charAt(0).toUpperCase() : 'U';
});

onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  profileForm.value.name = user.name || 'Satrio Akbar';
  profileForm.value.email = user.email || 'akbar@gmail.com';
  const savedImage = localStorage.getItem('profileImage');
  if (savedImage) profileImageUrl.value = savedImage;
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

const removeProfilePicture = () => {
  if (confirm('Remove profile picture?')) {
    profileImageUrl.value = null;
    localStorage.removeItem('profileImage');
  }
};

const updateProfile = async () => {
  isSaving.value = true;
  await new Promise(res => setTimeout(res, 800));
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  user.name = profileForm.value.name;
  localStorage.setItem('user', JSON.stringify(user));
  isSaving.value = false;
  alert('Profile saved!');
};

const resetForm = () => {
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  profileForm.value.name = user.name;
  profileForm.value.password = '';
};

const handleLogout = () => {
  if (confirm('Logout?')) {
    localStorage.clear();
    router.push('/login');
  }
};
</script>

<style scoped>
/* GLOBAL & VARIABLES */
.dashboard-layout {
  --bg-dark: #0f0f0f;
  --bg-sidebar: #181818;
  --bg-card: #1e1e1e;
  --border-color: #2a2a2a;
  --color-primary: #ffab00;
  --color-danger: #ff4444;
  --text-primary: #ffffff;
  --text-secondary: #888888;
  display: flex; height: 100vh; background-color: var(--bg-dark); color: var(--text-primary);
}

/* SIDEBAR ICONIC - Perbaikan Warna Setting Ikon */
.sidebar-iconic {
  width: 64px; border-right: 1px solid var(--border-color); display: flex;
  flex-direction: column; align-items: center; padding: 24px 0; gap: 40px; background-color: var(--bg-sidebar);
}
.icon-brand { color: var(--text-primary); } /* Kembali ke Putih */

.icon-nav { display: flex; flex-direction: column; gap: 24px; align-items: center; width: 100%; }
.nav-item { display: flex; flex-direction: column; align-items: center; gap: 6px; color: var(--text-secondary); text-decoration: none; font-size: 10px; }
.nav-item.active { color: var(--text-primary); }
.icon { width: 22px; height: 22px; }

/* MAIN LAYOUT */
.sidebar-menu { width: 240px; background-color: var(--bg-sidebar); border-right: 1px solid var(--border-color); padding: 24px 16px; }
.main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; }
.top-header { height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; border-bottom: 1px solid var(--border-color); }
.user-avatar-header { width: 32px; height: 32px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #000; overflow: hidden; }
.avatar-img-small { width: 100%; height: 100%; object-fit: cover; }

/* PROFILE AVATAR SECTION */
.profile-wrapper { max-width: 800px; margin: 40px auto; padding: 0 24px 60px; }
.profile-main-card { background-color: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; }
.profile-header-content { padding: 40px; display: flex; align-items: center; gap: 32px; }

.avatar-section-wrapper { display: flex; align-items: center; gap: 20px; }

.avatar-upload-box {
  width: 90px; height: 90px; border-radius: 50%; overflow: hidden;
  background-color: var(--color-primary); cursor: pointer; position: relative;
}
.profile-img-large { width: 100%; height: 100%; object-fit: cover; }
.large-avatar-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 800; color: #000; }
.avatar-hover-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.2s; }
.avatar-upload-box:hover .avatar-hover-overlay { opacity: 1; }

/* TOMBOL SAMPAH DI SAMPING */
.btn-delete-photo-side {
  background: #252525; border: 1px solid var(--border-color); color: var(--text-secondary);
  padding: 8px 12px; border-radius: 8px; display: flex; align-items: center; gap: 8px;
  cursor: pointer; font-size: 12px; transition: 0.2s;
}
.btn-delete-photo-side:hover { color: var(--color-danger); border-color: var(--color-danger); background: #2a1515; }
.icon-trash-action { width: 16px; height: 16px; }

/* FORM STYLES */
.profile-form-full { padding: 40px; }
.input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
.form-group label { font-size: 13px; color: var(--text-secondary); }
.form-control { background: #121212; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; color: #fff; outline: none; }
.form-control:focus { border-color: var(--color-primary); }
.disabled { opacity: 0.5; }
.form-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; }
.btn-primary { background: var(--color-primary); color: #000; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 700; cursor: pointer; }
.btn-secondary { background: transparent; border: 1px solid var(--border-color); color: #fff; padding: 12px 24px; border-radius: 8px; cursor: pointer; }

.hidden-input { display: none; }
.menu-title { font-size: 18px; margin-bottom: 20px; }
.menu-item { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 6px; color: var(--text-secondary); cursor: pointer; }
.menu-item.active { background: #252525; color: #fff; }
.logout-item:hover { color: var(--color-danger); }
</style>
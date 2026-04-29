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
      <h2 class="menu-title">Settings</h2>
      <div class="menu-item active">
        <User class="icon-small" />
        <span>My Account</span>
      </div>
    </aside>

    <main class="main-content">
      <header class="top-header">
        <div class="header-left">
           <h1 class="page-title" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">Profile Settings</h1>
        </div>

        <div class="user-avatar">
          {{ userInitial }}
        </div>
      </header>

      <section class="content-area">
        <div class="profile-card" style="max-width: 600px; margin: 40px auto; background: var(--bg-sidebar); padding: 30px; border-radius: 12px; border: 1px solid var(--border-color);">
          <div style="margin-bottom: 30px; display: flex; align-items: center; gap: 20px;">
            <div style="width: 70px; height: 70px; background: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: bold; color: black;">
              {{ userInitial }}
            </div>
            <div>
              <h2 style="color: white; margin: 0;">{{ name }}</h2>
              <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">{{ email }}</p>
            </div>
          </div>

          <form @submit.prevent="handleUpdate" style="display: flex; flex-direction: column; gap: 20px;">
            <div class="field-group">
              <label style="display: block; color: var(--text-secondary); font-size: 12px; margin-bottom: 8px;">Full Name</label>
              <input v-model="name" type="text" style="width: 100%; background: var(--bg-dark); border: 1px solid var(--border-color); padding: 12px; border-radius: 8px; color: white; outline: none;">
            </div>

            <div class="field-group">
              <label style="display: block; color: var(--text-secondary); font-size: 12px; margin-bottom: 8px;">Email Address</label>
              <input v-model="email" type="email" disabled style="width: 100%; background: var(--bg-dark); border: 1px solid var(--border-color); padding: 12px; border-radius: 8px; color: #555; cursor: not-allowed;">
            </div>

            <button type="submit" style="background: var(--color-primary); color: black; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px;">
              Save Changes
            </button>
          </form>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { 
  Settings, Home as HomeIcon, Folder, User 
} from 'lucide-vue-next';

const name = ref('');
const email = ref('');
const userInitial = ref('U');

onMounted(() => {
  // Ambil data user dari localStorage
  const user = JSON.parse(localStorage.getItem('user') || '{}');
  name.value = user.name || 'User';
  email.value = user.email || '';
  userInitial.value = name.value.charAt(0).toUpperCase();
});

const handleUpdate = () => {
  alert('Profile update simulation successful!');
  // Update inisial secara real-time
  userInitial.value = name.value.charAt(0).toUpperCase();
};
</script>

<style scoped>
/* Pastikan CSS layout tetap konsisten dengan Dashboard */
.dashboard-layout {
  display: flex;
  height: 100vh;
  background-color: var(--bg-dark);
}

.nav-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  color: var(--text-secondary);
  text-decoration: none;
  font-size: 11px;
}

.nav-item.active {
  color: var(--text-primary);
}

.user-avatar {
  width: 32px;
  height: 32px;
  background-color: var(--color-primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: black;
}
</style>
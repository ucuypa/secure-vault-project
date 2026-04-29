<template>
  <div class="login-page">
    <div class="login-card">
      <img src="/padlockwebdev.svg" alt="Logo" class="logo" />
      <h1 class="welcome-title">Welcome back.</h1>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="field-group">
          <label class="field-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="field-input"
          />
        </div>

        <div class="field-group">
          <label class="field-label">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="field-input"
          />
          <div class="forgot-wrapper">
            <router-link to="/ForgetPassword" class="forgot-link">Forgotten your password?</router-link>
          </div>
        </div>

        <div class="bottom-section">
          <p class="register-text">
            Don't have an account?
            <router-link to="/register" class="register-link">Register</router-link>
          </p>
          <button type="submit" :disabled="loading" class="login-btn">
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import authService from '../api/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const form = ref({ email: '', password: '' });

const handleLogin = async () => {
  loading.value = true;
  try {
    await authService.login(form.value);
    router.push('/dashboard');
  } catch (error) {
    alert(error.response?.data?.message || 'Invalid credentials');
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.logo {
  width: 64px;         /* Sesuaikan ukuran */
  height: auto;
  margin: 0 auto;      /* Membuat gambar ke tengah secara horizontal */
  display: block;      /* Menghilangkan whitespace di bawah gambar */
  filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.1)); /* Sedikit efek glowing */
}

.login-card {
  /* ... gaya yang sudah ada ... */
  padding-top: 40px;   /* Sesuaikan padding atas agar tidak terlalu mepet logo */
}

.login-page {
  min-height: 100vh;
  background-color: var(--bg-main);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.page-label {
  position: fixed;
  top: 14px;
  left: 16px;
  font-size: 13px;
  font-weight: 400;
  color: var(--text-secondary);
  letter-spacing: 0.01em;
}

.login-card {
  background-color: var(--bg-card);
  border-radius: 12px;
  padding: 56px 40px; 
  width: 100%;
  max-width: 380px; 
  min-height: 630px; 
  display: flex;
  flex-direction: column;
  justify-content: center; 
  gap: 32px;
}

.welcome-title {
  font-size: 24px;
  font-weight: 300;
  color: var(--text-primary);
  text-align: center;
  letter-spacing: -0.02em;
  line-height: 1.2;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.field-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.field-label {
  font-size: 13px;
  font-weight: 400;
  color: var(--text-muted);
  letter-spacing: 0.01em;
}

.field-input {
  width: 100%;
  height: 38px;
  background-color: var(--bg-input);
  border: 1px solid var(--border-color);
  border-radius: 6px;
  padding: 0 12px;
  font-size: 14px;
  font-weight: 400;
  color: var(--text-primary);
  outline: none;
  transition: border-color 0.15s ease;
}

.field-input:focus {
  border-color: var(--color-primary);
}

.forgot-wrapper {
  display: flex;
  justify-content: flex-end;
  margin-top: 4px;
}

.forgot-link {
  font-size: 12px;
  font-weight: 400;
  font-style: italic;
  color: var(--text-link);
  text-decoration: underline;
  text-underline-offset: 2px;
  cursor: pointer;
  transition: color 0.15s ease;
}

.forgot-link:hover {
  color: var(--color-primary);
}

.bottom-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  margin-top: 24px;
}

.register-text {
  font-size: 13px;
  font-weight: 400;
  color: var(--text-secondary);
}

.register-link {
  font-weight: 700;
  color: var(--text-primary);
  text-decoration: none;
  margin-left: 2px;
  transition: color 0.15s ease;
}

.register-link:hover {
  color: var(--color-primary);
}

.login-btn {
  width: 100%;
  height: 42px;
  background-color: var(--color-primary);
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
  cursor: pointer;
  letter-spacing: 0.01em;
  transition: background-color 0.15s ease, transform 0.1s ease;
}

.login-btn:hover:not(:disabled) {
  background-color: var(--color-primary-hover);
}

.login-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.login-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
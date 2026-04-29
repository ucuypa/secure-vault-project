<template>
  <div class="register-page">
    <div class="register-card">
      <h1 class="welcome-title">Create Account.</h1>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="field-group">
          <label class="field-label">Full Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="field-input"
          />
        </div>

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
        </div>

        <div class="field-group">
          <label class="field-label">Confirm Password</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            class="field-input"
          />
        </div>

        <div class="bottom-section">
          <p class="redirect-text">
            Already have an account?
            <router-link to="/login" class="redirect-link">Login</router-link>
          </p>
          <button type="submit" :disabled="loading" class="register-btn">
            {{ loading ? 'Initializing...' : 'Initialize Vault' }}
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
const form = ref({ name: '', email: '', password: '', password_confirmation: '' });

const handleRegister = async () => {
  loading.value = true;
  try {
    await authService.register(form.value);
    alert('Account created! Unlocking login...');
    router.push('/login');
  } catch (error) {
    alert(error.response?.data?.message || 'Registration failed');
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

.register-page {
  min-height: 100vh;
  background-color: var(--bg-main);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  padding: 20px;
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

.register-card {
  background-color: var(--bg-card);
  border-radius: 12px;
  padding: 48px 40px; 
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

.register-form {
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

.bottom-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 14px;
  margin-top: 24px;
}

.redirect-text {
  font-size: 13px;
  font-weight: 400;
  color: var(--text-secondary);
}

.redirect-link {
  font-weight: 700;
  color: var(--text-primary);
  text-decoration: none;
  margin-left: 2px;
  transition: color 0.15s ease;
}

.redirect-link:hover {
  color: var(--color-primary);
}

.register-btn {
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

.register-btn:hover:not(:disabled) {
  background-color: var(--color-primary-hover);
}

.register-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.register-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
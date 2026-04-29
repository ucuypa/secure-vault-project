<template>
  <div class="forget-page">
    <span class="page-label">Forget Password</span>
    
    <div class="forget-card">
      <button @click="router.push('/login')" class="close-btn" aria-label="Close">
        ✕
      </button>

      <img src="/padlockwebdev.svg" alt="Logo" class="logo" />
      
      <h1 class="welcome-title">Reset your password.</h1>

      <form @submit.prevent="handleReset" class="forget-form">
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
          <label class="field-label">New Password</label>
          <input
            v-model="form.newPassword"
            type="password"
            required
            class="field-input"
          />
        </div>

        <div class="field-group">
          <label class="field-label">Confirm Password</label>
          <input
            v-model="form.confirmPassword"
            type="password"
            required
            class="field-input"
          />
        </div>

        <div class="bottom-section">
          <button type="submit" :disabled="loading" class="submit-btn">
            {{ loading ? 'Confirming...' : 'Confirm' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const loading = ref(false);
const form = ref({ email: '', newPassword: '', confirmPassword: '' });

const handleReset = async () => {
  if (form.value.newPassword !== form.value.confirmPassword) {
    alert("Passwords do not match!");
    return;
  }

  loading.value = true;
  try {
    // This is where the API call will go
    // await authService.resetPassword(form.value);
    
    // Simulating a network request for now
    await new Promise(resolve => setTimeout(resolve, 1000)); 
    
    alert('Password successfully reset!');
    router.push('/login');
  } catch (error) {
    alert(error.response?.data?.message || 'Failed to reset password');
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

.forget-page {
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

.forget-card {
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
  position: relative; 
}

.close-btn {
  position: absolute;
  top: 20px;
  left: 20px;
  background: none;
  border: none;
  color: var(--text-muted);
  font-size: 18px;
  cursor: pointer;
  padding: 4px;
  transition: color 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  color: var(--text-primary);
  border-color: transparent;
}

.logo {
  width: 64px;        
  height: auto;
  margin: 0 auto;      
  display: block;      
  filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.1)); 
}

.welcome-title {
  font-size: 24px;
  font-weight: 300;
  color: var(--text-primary);
  text-align: center;
  letter-spacing: -0.02em;
  line-height: 1.2;
  margin-top: 4px;
}

.forget-form {
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
  margin-top: 16px;
}

.submit-btn {
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

.submit-btn:hover:not(:disabled) {
  background-color: var(--color-primary-hover);
}

.submit-btn:active:not(:disabled) {
  transform: scale(0.98);
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="bg-indigo-600 p-8 text-white text-center">
        <div class="inline-block p-3 bg-indigo-500 rounded-full mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
          </svg>
        </div>
        <h1 class="text-2xl font-bold">SECURE VAULT</h1>
        <p class="text-indigo-100 text-sm">Enter credentials to unlock your data</p>
      </div>

      <div class="p-8">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Email Address</label>
            <input v-model="form.email" type="email" required placeholder="yusuf@ub.ac.id" 
              class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">Security Password</label>
            <input v-model="form.password" type="password" required placeholder="••••••••" 
              class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none transition-all">
          </div>
          
          <button type="submit" :disabled="loading"
            class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg shadow-lg disabled:opacity-50 transition-all">
            {{ loading ? 'Verifying...' : 'Unlock Vault' }}
          </button>
        </form>

        <div class="mt-6 text-center text-sm">
          <span class="text-gray-500">Need a secure account?</span>
          <router-link to="/register" class="ml-2 text-indigo-600 font-bold hover:underline">Register Now</router-link>
        </div>
      </div>
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
    alert(error.response?.data?.message || 'Access Denied: Invalid Credentials');
  } finally {
    loading.value = false;
  }
};
</script>
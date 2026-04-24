<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-900 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="bg-blue-600 p-6 text-white text-center">
        <h1 class="text-3xl font-extrabold tracking-tight">SECURE VAULT</h1>
        <p class="text-blue-100 mt-2">Personal Encrypted File Manager</p>
      </div>
      
      <div class="p-8">
        <form @submit.prevent="handleLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Email</label>
            <input v-model="form.email" type="email" placeholder="yusuf@example.com" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700">Password</label>
            <input v-model="form.password" type="password" placeholder="••••••••" class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
          </div>
          
          <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
            Unlock Vault
          </button>
        </form>
        
        <div class="mt-8 text-center border-t pt-6">
          <span class="text-gray-500 text-sm">New user?</span>
          <router-link to="/register" class="ml-2 text-blue-600 font-semibold hover:text-blue-800">Create account</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { authService } from '../api/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({ email: '', password: '' });

const handleLogin = async () => {
  try {
    await authService.login(form.value);
    router.push('/dashboard');
  } catch (error) {
    alert('Invalid credentials. Access Denied.');
  }
};
</script>
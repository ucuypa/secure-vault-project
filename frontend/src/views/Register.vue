<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-xl p-8 border border-gray-100">
      <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Create Account</h2>
      <p class="text-gray-500 mb-8">Set up your encrypted workspace.</p>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input v-model="form.email" type="email" required class="mt-1 block w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" required class="mt-1 block w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input v-model="form.password_confirmation" type="password" required class="mt-1 block w-full px-4 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <button type="submit" class="w-full bg-gray-900 text-white py-3 rounded-lg font-bold hover:bg-black transition-colors">
          Initialize Vault
        </button>
      </form>

      <div class="mt-6 text-center">
        <router-link to="/login" class="text-sm text-indigo-600 hover:underline">Back to Secure Login</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import authService from '../api/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({ name: '', email: '', password: '', password_confirmation: '' });

const handleRegister = async () => {
  try {
    await authService.register(form.value);
    alert('Account created! Unlocking login...');
    router.push('/login');
  } catch (error) {
    alert(error.response?.data?.message || 'Registration failed');
  }
};
</script>
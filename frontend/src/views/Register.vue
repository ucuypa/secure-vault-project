<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Create Secure Vault</h2>
      
      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name</label>
          <input v-model="form.name" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email Address</label>
          <input v-model="form.email" type="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input v-model="form.password" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
          Register Account
        </button>
      </form>
      
      <p class="mt-4 text-center text-sm text-gray-600">
        Already have an account? <router-link to="/login" class="text-blue-600 hover:underline">Login here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { authService } from '../api/auth';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({ name: '', email: '', password: '' });

const handleRegister = async () => {
  try {
    await authService.register(form.value);
    alert('Registration successful! Please login.');
    router.push('/login');
  } catch (error) {
    alert(error.response.data.message || 'Registration failed');
  }
};
</script>
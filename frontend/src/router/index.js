import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
<<<<<<< HEAD
import FolderView from '../views/FolderView.vue'
=======
import ForgetPassword from '../views/ForgetPassword.vue';
>>>>>>> ff5d0bd9046deff261aabc5a76a60f335a5655a5

const routes = [
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
  { path: '/ForgetPassword', name: 'ForgetPassword', component: ForgetPassword },
  { path: '/', redirect: '/login' },
  { path: '/profile', name: 'profile',component: () => import('../views/Profile.vue') },
  { path: '/folder/:id', name: 'FolderView', component: FolderView, props: true }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
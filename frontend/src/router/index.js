import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import FolderView from '../views/FolderView.vue'
import ForgetPassword from '../views/ForgetPassword.vue';
import ActivityLog from '../views/ActivityLog.vue';

const routes = [
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
  { path: '/ForgetPassword', name: 'ForgetPassword', component: ForgetPassword },
  { path: '/activity', name: 'ActivityLog', component: ActivityLog },
  { path: '/', redirect: '/login' },
  { path: '/profile', name: 'profile',component: () => import('../views/Profile.vue') },
  { path: '/folder/:id', name: 'FolderView', component: FolderView, props: true }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
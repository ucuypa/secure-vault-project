import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Dashboard from '../views/Dashboard.vue'
import ForgetPassword from '../views/ForgetPassword.vue';

const routes = [
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/dashboard', name: 'Dashboard', component: Dashboard },
  { path: '/ForgetPassword', name: 'ForgetPassword', component: ForgetPassword },
  { path: '/', redirect: '/login' },
  { path: '/profile', name: 'profile',component: () => import('../views/Profile.vue')
}
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
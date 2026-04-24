import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Dashboard from '../views/Dashboard.vue';

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    // Keamanan: Jangan biarkan user masuk dashboard tanpa login
    beforeEnter: (to, from, next) => {
      if (!localStorage.getItem('token')) {
        next('/login');
      } else {
        next();
      }
    }
  },
  {
    path: '/',
    redirect: '/login' // Jika buka root, lempar ke login
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
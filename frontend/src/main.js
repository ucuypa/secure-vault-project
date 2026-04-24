import { createApp } from 'vue'
import App from './App.vue'
import router from './router' // Impor router
import './style.css' // Import Tailwind

const app = createApp(App)
app.use(router) // Gunakan router
app.mount('#app')
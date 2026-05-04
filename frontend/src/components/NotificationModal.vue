<template>
  <div v-if="show" class="modal-overlay" @click.self="closeModal">
    <div class="modal-card">
      
      <div class="modal-header">
        <div class="header-title">
          <CheckCircle v-if="type === 'success'" class="icon-success" />
          <AlertCircle v-else class="icon-danger" />
          
          <h3>{{ title }}</h3>
        </div>
        <button @click="closeModal" class="btn-icon">
          <X class="icon-small" />
        </button>
      </div>

      <div class="modal-body">
        <p class="notification-message">{{ message }}</p>
      </div>

      <div class="modal-footer">
        <button 
          @click="closeModal" 
          :class="['btn-confirm', type === 'success' ? 'btn-success' : 'btn-danger']"
        >
          {{ type === 'success' ? 'Awesome' : 'Understood' }}
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { X, AlertCircle, CheckCircle } from 'lucide-vue-next'; // Imported CheckCircle for success

const props = defineProps({
  show: Boolean,
  type: {
    type: String,
    default: 'error' // Defaults to error, but can be 'success'
  },
  title: String,
  message: String
});

const emit = defineEmits(['close']);

const closeModal = () => {
  emit('close');
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-card {
  background-color: #1a1a1a;
  border: 1px solid #2e2e2e;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
  display: flex;
  flex-direction: column;
  animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #2e2e2e;
}

.header-title {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-title h3 {
  font-size: 16px;
  font-weight: 600;
  color: #ffffff;
  margin: 0;
}

/* Dynamic Icon Colors */
.icon-danger {
  width: 20px;
  height: 20px;
  color: #ef4444; /* Red */
}

.icon-success {
  width: 20px;
  height: 20px;
  color: #10b981; /* Emerald Green */
}

.modal-body {
  padding: 24px 20px;
}

.notification-message {
  color: #a0a0a0;
  font-size: 14px;
  line-height: 1.5;
  margin: 0;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  padding: 16px 20px;
  border-top: 1px solid #2e2e2e;
  background-color: #121212;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
}

.btn-icon {
  background: none;
  border: none;
  color: #a0a0a0;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: 0.2s;
}

.btn-icon:hover {
  color: #ffffff;
  background-color: #2e2e2e;
}

.btn-confirm {
  border: none;
  padding: 8px 20px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  color: #ffffff;
}

/* Dynamic Button Colors */
.btn-danger {
  background-color: #ef4444;
}
.btn-danger:hover {
  background-color: #dc2626;
}

.btn-success {
  background-color: #10b981;
}
.btn-success:hover {
  background-color: #059669;
}
</style>
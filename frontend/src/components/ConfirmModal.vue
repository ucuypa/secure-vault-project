<template>
    <div v-if="show" class="modal-overlay" @click.self="closeModal">
        <div class="modal-card">

            <div class="modal-header">
                <div class="header-title">
                    <HelpCircle class="icon-warning" />
                    <h3>{{ title || 'Confirmation' }}</h3>
                </div>
                <button @click="closeModal" class="btn-icon">
                    <X class="icon-small" />
                </button>
            </div>

            <div class="modal-body">
                <p class="notification-message">{{ message }}</p>
            </div>

            <div class="modal-footer">
                <button @click="closeModal" class="btn-cancel">Cancel</button>
                <button @click="confirmAction" class="btn-confirm btn-danger">
                    {{ confirmText || 'Yes, proceed' }}
                </button>
            </div>

        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { X, HelpCircle } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: String
});

// Kita butuh 2 emit: untuk menutup modal, dan untuk mengeksekusi aksi (logout)
const emit = defineEmits(['close', 'confirm']);

const closeModal = () => {
    emit('close');
};

const confirmAction = () => {
    emit('confirm');
};
</script>

<style scoped>
/* Mewarisi gaya yang sama persis dengan NotificationModal sebelumnya */
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
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.icon-warning {
    width: 20px;
    height: 20px;
    color: #f59e0b;
    /* Warna Oranye */
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
    gap: 12px;
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
}

.btn-icon:hover {
    color: #ffffff;
    background-color: #2e2e2e;
}

.btn-cancel {
    background-color: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-primary);
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background-color: var(--bg-hover);
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

.btn-danger {
    background-color: #ef4444;
}

.btn-danger:hover {
    background-color: #dc2626;
}
</style>
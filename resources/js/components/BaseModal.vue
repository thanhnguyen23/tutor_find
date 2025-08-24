<template>
<div v-if="isOpen" class="modal-overlay" @click="emit('close')">
    <div class="modal-container" :class="sizeClass" @click.stop>
        <div class="modal-content-wrapper">
            <div class="modal-header">
                <div class="modal-title-container">
                    <h2 class="modal-title" v-if="title">{{ title }}</h2>
                    <p v-if="description" class="modal-description">{{ description }}</p>
                </div>

                <button class="close-button" @click="emit('close')">
                    <svg class="icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <!-- <div class="separation"></div> -->
            <div class="modal-content">
                <slot></slot>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import {
    computed
} from 'vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'medium'
    }
});

const emit = defineEmits(['close']);

const sizeClass = computed(() => {
    return {
        'max-w-sm': props.size === 'small',
        'max-w-md': props.size === 'medium',
        'max-w-lg': props.size === 'large'
    };
});
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-container {
    background: white;
    border-radius: 2rem;
    width: 100%;
    max-width: 480px;
    margin: 16px;
    overflow: hidden;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content-wrapper::-webkit-scrollbar {
    width: 0.5rem;     /* Chiều rộng scrollbar dọc */
    height: 0.5rem;    /* Chiều cao scrollbar ngang */
}

.modal-content-wrapper::-webkit-scrollbar-track {
    background: #f3f4f6; /* Màu nền track */
    border-radius: 8px;
}

.modal-content-wrapper::-webkit-scrollbar-thumb {
    background-color: var(--gray-400); /* Màu thanh kéo */
    border-radius: 8px;
    border: 2px solid #f3f4f6; /* Tạo khoảng cách với track */
}

.modal-content-wrapper {
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header, .modal-content {
    padding: 1.8rem;
}
.modal-content {
    padding-top: 0 !important;
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.modal-title {
    font-size: var(--font-size-heading-5);
    line-height: 1.2;
    font-weight: 700;
    color: #111827;
    margin: 0;
}

.modal-description {
    font-size: var(--font-size-base);
    color: #6b7280;
    text-align: left;
    margin: 0;
}

.close-button {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    transition: color 0.3s ease;
    position: absolute;
    right: 1.5rem;
}

.close-button:hover {
    color: #111827;
}

.max-w-sm {
    max-width: var(--bs-breakpoint-sm) !important;
}

.max-w-md {
    max-width: var(--bs-breakpoint-md) !important;
}

.max-w-lg {
    max-width: var(--bs-breakpoint-lg) !important;
}

.separation {
    margin: 1rem 0;
}

@media (max-width: 868px) {
    .modal-title {
        font-size: var(--font-size-medium);
    }
    .modal-container {
        padding: 0;
        margin: 0;
        max-height: 100%;
        width: 100%;
        height: 100%;
        border-radius: 0;
    }
}

@media (max-width: 640px) {
    .modal-container {
        padding: 0;
        margin: 0;
        max-height: 100%;
        width: 100%;
        height: 100%;
        border-radius: 0;
    }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

@keyframes slideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>

<template>
<div v-if="isOpen" class="modal-overlay" @click="emit('close')">
    <div class="modal-container" :class="sizeClass" @click.stop>
        <div class="modal-content">
            <div class="modal-header" v-if="title">
                <div class="modal-title-container">
                    <h2 class="modal-title">{{ title }}</h2>
                    <p v-if="description" class="modal-description">{{ description }}</p>
                </div>

                <button class="close-button" @click="emit('close')">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <slot></slot>
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
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    margin: 16px;
    max-height: 60vh;
    overflow-y: auto;
    animation: fadeIn 0.3s ease-in-out;
}

.modal-content {
    padding: 2rem;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
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
}

.close-button:hover {
    color: #111827;
}

.close-button svg {
    width: 24px;
    height: 24px;
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

@media (max-width: 640px) {
    .modal-content {
        padding: 24px;
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

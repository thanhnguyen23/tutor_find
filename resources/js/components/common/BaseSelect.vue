<template>
    <div class="base-select" :class="{ 'is-open': isOpen, 'width-full': widthFull }">
        <div v-if="label" class="label-group" :class="{
            'label-small': size === 'small',
            'label-base': size === 'base',
            'label-medium': size === 'medium',
            'label-large': size === 'large'
        }">
            <label>{{ label }}</label>
            <span v-if="required" class="required">*</span>
        </div>
        <div class="select-button" :class="[customClass, sizeClass, { 'error': showError }]" @click="toggleDropdown">
            <span>{{ displayValue || placeholder }}</span>
            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
        <div v-if="showError" class="error-message">
            {{ errorMessage }}
        </div>
        <div v-if="isOpen" class="select-dropdown">
            <div v-if="type === 'search'" class="search-input">
                <div class="icon-search-wrapper">
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" v-model="searchQuery" :placeholder="searchPlaceholder">
            </div>
            <div class="select-list format-scrollbar">
                <div v-for="option in filteredOptions"
                     :key="option.id"
                     class="select-option"
                     :class="{ disabled: option.disabled }"
                     @click="!option.disabled && selectOption(option)">
                    <div class="checkbox">
                        <svg v-if="isSelected(option)" class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span>{{ option.name }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number, Object],
        default: ''
    },
    options: {
        type: Array,
        required: true
    },
    placeholder: {
        type: String,
        default: 'Chọn'
    },
    searchPlaceholder: {
        type: String,
        default: 'Tìm kiếm...'
    },
    type: {
        type: String,
        default: 'no-search',
        validator: (value) => ['search', 'no-search'].includes(value)
    },
    customClass: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: ''
    },
    required: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'medium',
        validator: (v) => ['small', 'base', 'medium', 'large'].includes(v)
    },
    widthFull: {
        type: Boolean,
        default: true
    },
    error: {
        type: Boolean,
        default: false
    },
    errorMessage: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const isOpen = ref(false);
const searchQuery = ref('');

const sizeClass = computed(() => {
    return {
        'select-small': props.size === 'small',
        'select-base': props.size === 'base',
        'select-medium': props.size === 'medium',
        'select-large': props.size === 'large',
    };
});

const displayValue = computed(() => {
    if (!props.modelValue) return '';
    const option = props.options.find(opt => opt.id == props.modelValue || opt.value == props.modelValue);
    return option ? (option.name || option.label) : '';
});

const filteredOptions = computed(() => {
    if (!searchQuery.value || props.type === 'no-search') return props.options;
    return props.options.filter(option =>
        (option.name || option.label).toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const showError = computed(() => {
    return props.error || (props.required && !props.modelValue);
});

const errorMessage = computed(() => {
    if (props.errorMessage) return props.errorMessage;
    if (props.required && !props.modelValue) return 'Trường này là bắt buộc';
    return '';
});

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const selectOption = (option) => {
    emit('update:modelValue', option.id !== undefined ? option.id : option.value);
    isOpen.value = false;
    searchQuery.value = '';
};

const isSelected = (option) => {
    return props.modelValue === (option.id !== undefined ? option.id : option.value);
};

const handleClickOutside = (event) => {
    if (!event.target.closest('.base-select')) {
        isOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
.width-full {
    width: 100%;
}
.label-group {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 0.3rem;
}
.label-group label {
    display: block;
    font-weight: 500;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    font-size: var(--font-size-base);
}
/* .label-small {
    font-size: var(--font-size-small);
}
.label-base {
    font-size: var(--font-size-base);
}
.label-medium {
    font-size: var(--font-size-base);
}
.label-large {
    font-size: var(--font-size-medium);
} */

.required {
    color: #dc2626;
}
.base-select {
    position: relative;
}

.select-button {
    width: 100%;
    padding: 12px 16px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    font-size: var(--font-size-base);
    gap: 0.5rem;
}

.select-small {
    padding: 0.5rem 0.75rem;
    height: 2rem;
}

.select-base {
    padding: 0.65rem 0.65rem;
    height: 2.5rem;
}

.select-medium {
    padding: 0.68rem 0.95rem;
    height: 2.9rem;
}

.select-large {
    padding: 1rem 1.5rem;
    height: 3.3rem;
}

.select-button:hover {
    background-color: rgb(244 244 245);
    color: black;
}

.select-button.error {
    border-color: #ef4444;
    background-color: #fef2f2;
}

.error-message {
    color: #ef4444;
    font-size: var(--font-size-mini);
    margin-top: 0.25rem;
    display: block;
}

.select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    margin-top: 4px;
    z-index: 10;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.search-input {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0.8rem 0.8rem 0.8rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.icon-search-wrapper {
    padding: 0 0.8rem;
}

.search-input input {
    width: 100%;
    padding: 0;
    border: none;
    outline: none;
    font-size: var(--font-size-base);
}

.select-list {
    padding: 4px 0;
    max-height: 300px;
    overflow-y: auto;
}

.select-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: var(--font-size-small);
}

.select-option:hover {
    background: #f3f4f6;
}

.select-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.checkbox {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkbox svg {
    color: black;
}

.select-option.selected .checkbox svg {
    color: black;
}

@media (max-width: 868px) {
    .label-group label {
        font-size: var(--font-size-small);
    }

    .select-button {
        font-size: var(--font-size-small);
    }

    .select-option {
        font-size: var(--font-size-small);
    }
}
</style>

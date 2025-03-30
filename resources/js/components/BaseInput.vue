<script setup>
import {
    computed
} from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    type: {
        type: String,
        default: 'text'
    },
    placeholder: {
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
    icon: {
        type: String,
        default: ''
    },
    unit: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    min: {
        type: [Number, String],
        default: null
    },
    max: {
        type: [Number, String],
        default: null
    },
    step: {
        type: [Number, String],
        default: null
    },
    disabled: {
        type: Boolean,
        default: false
    },
    size: {
        type: String,
        default: 'medium'
    }
});

const emit = defineEmits(['update:modelValue']);

const hasIcon = computed(() => !!props.icon);
const hasUnit = computed(() => !!props.unit);
const hasLabel = computed(() => !!props.label);
const hasError = computed(() => !!props.error);

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
<div class="form-group">
    <div v-if="hasLabel" class="label-group">
        <label>{{ label }}</label>
        <span v-if="required" class="required">*</span>
    </div>

    <div class="input-wrapper" :class="{
      'with-icon': hasIcon,
      'with-unit': hasUnit,
      'has-error': hasError
    }">
        <template v-if="hasIcon">
            <div class="icon" v-html="icon"></div>
        </template>

        <input :type="type" :value="modelValue" :placeholder="placeholder" :required="required" :min="min" :max="max" :step="step" :disabled="disabled" :class="{
            'input-small': size === 'small',
            'input-medium': size === 'medium',
            'input-large': size === 'large'
        }" @input="updateValue">

        <span v-if="hasUnit" class="unit">{{ unit }}</span>
    </div>

    <span v-if="error" class="error-message">{{ error }}</span>
</div>
</template>

<style scoped>
.form-group {
    width: 100%;
}

.label-group {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 8px;
}

.label-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
}

.required {
    color: #dc2626;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper.with-icon input {
    padding-left: 2.5rem;
}

.input-wrapper.with-unit input {
    padding-right: 4rem;
}

.input-wrapper.has-error input {
    border-color: #dc2626;
}

.icon {
    position: absolute;
    left: 12px;
    color: #6B7280;
    display: flex;
    align-items: center;
}

.icon :deep(svg) {
    width: 1.1rem;
    height: 1.1rem;
}

input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s;
}

.input-small {
    padding: 0.5rem 0.75rem;
}

.input-medium {
    padding: 0.75rem 1rem;
}

.input-large {
    padding: 1rem 1.5rem;
}

input:focus {
    outline: none;
    border-color: #111827;
}

input:disabled {
    background: #F3F4F6;
    cursor: not-allowed;
}

.unit {
    position: absolute;
    right: 12px;
    color: #6B7280;
    font-size: 0.875rem;
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 4px;
    display: block;
}
</style>

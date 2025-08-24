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
    },
    rows: {
        type: Number,
        default: 4
    }
});

const emit = defineEmits(['update:modelValue']);

const hasUnit = computed(() => !!props.unit);
const hasLabel = computed(() => !!props.label);
const hasError = computed(() => !!props.error);

const updateValue = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>

<template>
<div class="form-group">
    <div v-if="hasLabel" class="label-group" :class="{
            'label-small': size === 'small',
            'label-base': size === 'base',
            'label-medium': size === 'medium',
            'label-large': size === 'large',
            'label-xl': size === 'xl'
        }">
        <label>{{ label }}</label>
        <span v-if="required" class="required">*</span>
    </div>

    <div class="input-wrapper" :class="{
      'with-icon': $slots.icon,
      'with-unit': hasUnit,
      'has-error': hasError
    }">
        <template v-if="$slots.icon">
            <div class="icon">
                <slot name="icon" />
            </div>
        </template>

        <template v-if="type === 'textarea'">
            <textarea :value="modelValue" :placeholder="placeholder" :rows="rows" :required="required" :min="min" :max="max" :disabled="disabled" @input="updateValue"></textarea>
        </template>
        <template v-else>
            <input :type="type" :value="modelValue" :placeholder="placeholder" :required="required" :min="min" :max="max" :step="step" :disabled="disabled" :class="{
                'input-small': size === 'small',
                'input-base': size === 'base',
                'input-medium': size === 'medium',
                'input-large': size === 'large',
                'input-xl': size === 'xl'
            }" @input="updateValue">
        </template>

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
    margin-bottom: 0.3rem;
}

.label-small {
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
}

.label-group label {
    display: block;
    font-weight: 500;
    color: var(--gray-700);
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
    padding-left: 2.7rem;
}

.input-wrapper.with-unit input {
    padding-right: 4rem;
}

.input-wrapper.has-error input {
    border-color: #dc2626;
}

.icon {
    position: absolute;
    transform: translateY(-50%);
    top: 50%;
    left: 1rem;
    color: #9ca3af;
    line-height: 1;
}

input, textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    transition: all 0.2s;
    font-weight: normal;
    font-family: "Gill Sans", sans-serif;
}

.input-small {
    padding: 0.5rem 0.75rem;
    height: 2rem;
}

.input-base {
    padding: 0.65rem 0.65rem;
    height: 2.5rem;
}

.input-medium {
    padding: 0.68rem 0.95rem;
    height: 2.9rem;
}

.input-large {
    padding: 1rem 1.5rem;
    height: 3.2rem;
}

.input-xl {
    padding: 1.3rem 1.7rem;
    height: 3.6rem;
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

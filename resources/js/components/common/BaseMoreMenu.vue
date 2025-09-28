<template>
<div class="more-menu" ref="menuRoot">
    <slot name="activator" :toggle="toggle" :open="isOpen">
        <button class="btn-sm btn-secondary more-btn" @click.stop="toggle">
            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="1"></circle>
                <circle cx="19" cy="12" r="1"></circle>
                <circle cx="5" cy="12" r="1"></circle>
            </svg>
        </button>
    </slot>

    <div v-if="isOpen" class="more-menu-dropdown">
        <slot :close="close"></slot>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const isOpen = ref(false);
const menuRoot = ref(null);

const open = () => { isOpen.value = true; };
const close = () => { isOpen.value = false; };
const toggle = () => { isOpen.value = !isOpen.value; };

const onGlobalClick = (e) => {
    if (!menuRoot.value) return;
    if (!menuRoot.value.contains(e.target)) {
        close();
    }
};

const onKeydown = (e) => {
    if (e.key === 'Escape') close();
};

onMounted(() => {
    document.addEventListener('click', onGlobalClick);
    document.addEventListener('keydown', onKeydown);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onGlobalClick);
    document.removeEventListener('keydown', onKeydown);
});
</script>

<style scoped>
.more-menu {
    position: relative;
}

.more-btn {
    min-width: 40px;
    padding: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.more-menu-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 160px;
    overflow: hidden;
}

.more-menu-item {
    width: 100%;
    padding: 0.75rem 1rem;
    border: none;
    background: transparent;
    color: #374151;
    font-size: var(--font-size-small);
    text-align: left;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s ease;
}

.more-menu-item:hover {
    background: #f3f4f6;
}

.more-menu-item:first-child {
    border-bottom: 1px solid #e5e7eb;
}
</style>



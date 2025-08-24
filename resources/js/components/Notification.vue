<template>
    <div class="pops-container-general">
        <div class="pop-wrapper" :class="{ 'active': showNotification.show, [showNotification.type]: showNotification.type }">
            <div class="pop-icon">
                <i class="fa-solid fa-triangle-exclamation icon"></i>
            </div>
            <div class="pop-content">
                <span class="title">{{ getTitleByType(showNotification.type) }}</span>
                <span class="content">{{ showNotification.message }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useStore } from "vuex";

export default {
    setup() {
        const store = useStore();
        const showNotification = computed(() => store.state.showNotification);

        const getTitleByType = () => {
            switch (showNotification.type) {
                case "success":
                    return "Thành công";
                case "error":
                    return "Thất bại";
                case "warring":
                    return "Lưu ý";
                case "info":
                    return "Thông báo";
            }
        }

        return {
            showNotification,
            getTitleByType
        };
    },
};
</script>

<style scoped>
.pops-container-general {
    --icon-size: 32px;
    --color: #FF3636;
    position: fixed;
    top: 10px;
    right: 10px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: flex-start;
    z-index: 999999999;
    pointer-events: none;
    transform: translateX(0);
    opacity: 1;
}

.pop-wrapper {
    pointer-events: all;
    transition: all .3s ease;
    transform: translateX(100%);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: default;
    padding: 0.7rem 1rem;
    border-radius: 0.8rem;
    gap: 0.7rem;
}

.pop-wrapper .pop-content {
    display: grid;
}

.pop-wrapper .pop-content .title {
    font-weight: 600;
    font-size: var(--font-size-base);
}

.pop-wrapper .pop-content .content {
    font-size: var(--font-size-small);
}

.pops-container-general .pop-wrapper.active {
    transform: translateX(0);
    opacity: 1;
    display: flex;
    align-items: center;
}

.pops-container-general .pop-wrapper.error.active {
    color: var(--color);
    background: #fff1f1;
}

.pops-container-general .pop-wrapper.success.active {
    color: #198754;
    background: #d1e7dd;
}

.pops-container-general .pop-wrapper.error .pop-icon {
    background: var(--color);
}

.pops-container-general .pop-wrapper.success .pop-icon {
    background: #5bca8d;
}

.pops-container-general .pop-wrapper.wraning.active {
    color: var(--yellow-600);
    background: var(--yellow-50);
}

.pops-container-general .pop-wrapper.info.active {
    color: var(--blue-600);
    background: var(--blue-50);
}

.pops-container-general .pop-wrapper.wraning .pop-icon {
    background: var(--yellow-400);
}

.pops-container-general .pop-wrapper.info .pop-icon {
    background: var(--blue-400);
}

.pops-container-general .pop-wrapper .pop-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 2rem;
}

.pops-container-general .pop-wrapper .pop-icon .icon {
    color: white
}
</style>

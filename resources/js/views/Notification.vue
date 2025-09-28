<script setup>
import {
    ref,
    reactive,
    onMounted,
    getCurrentInstance,
    computed,
    watch,
} from 'vue';

const {
    proxy
} = getCurrentInstance();

const notifications = ref([]);
const meta = ref(null);
const activeTab = ref('all');

const tabs = [{
        id: 'all',
        name: 'Tất cả',
        icon: 'all'
    },
    {
        id: 'unread',
        name: 'Chưa đọc',
        icon: 'overview'
    },
    {
        id: 'read',
        name: 'Đã đọc',
        icon: 'read'
    },
];

// Tính toán giá trị is_read dựa vào tab đang chọn
const currentIsRead = computed(() => {
    switch (activeTab.value) {
        case 'unread':
            return 0;
        case 'read':
            return 1;
        default:
            return null;
    }
});

// Gọi API
const fetchNotifications = async (page = 1) => {
    const params = {
        page
    };
    if (currentIsRead.value !== null) {
        params.is_read = currentIsRead.value;
    }

    const response = await proxy.$api.apiGet('notifications', params);
    notifications.value = response.data || [];
    meta.value = response.meta || null;
};

// Xử lý chuyển trang
const handleChangePage = (page) => {
    fetchNotifications(page);
};

// Đánh dấu tất cả là đã đọc
const markAllAsRead = async () => {
    await proxy.$api.apiPost('notifications/mark-all-read');
    await fetchNotifications(meta.value?.current_page || 1);
    proxy.$notification.success('Đã đánh dấu tất cả là đã đọc!');
};

// Đánh dấu 1 thông báo là đã đọc
const markNotificationAsRead = async (id) => {
    await proxy.$api.apiPost(`notifications/${id}/mark-read`);
    await fetchNotifications(meta.value?.current_page || 1);
};

// Tự động fetch khi tab thay đổi
watch(activeTab, () => fetchNotifications(1));

// Khởi động lần đầu
onMounted(() => fetchNotifications());
</script>

<template>
<div class="notification-page">
    <div class="container">
        <div class="nav-tabs">
            <div class="nav-tabs-wrapper">
                <button v-for="tab in tabs" :key="tab.id" :class="['tab', { active: activeTab === tab.id }]" @click="activeTab = tab.id">
                    <span class="icon"></span>
                    <span>{{ tab.name }}</span>
                </button>
            </div>
            <button class="btn-lg btn-secondary w-max mark-all_read" @click="markAllAsRead">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 7 17l-5-5"></path>
                    <path d="m22 10-7.5 7.5L13 16"></path>
                </svg>
                Đánh dấu tất cả là đã đọc
            </button>
        </div>

        <div class="content-main">
            <div class="notification-list">
                <div v-for="notification in notifications" :key="notification.id" class="notification-card" :class="{ 'is-read': notification.is_read }" @click="markNotificationAsRead(notification.id)">
                    <div class="notification-header">
                        <div class="notification-left">
                            <div class="icon-wrapper">
                                <img v-if="notification.icon" :src="notification.icon" class="icon-lg" />
                            </div>
                            <div class="title-wrapper">
                                <span class="title-main">{{ notification.name }}</span>
                                <span class="sub-title">Lịch học</span>
                                <span v-if="!notification.is_read" class="dot-unread"></span>
                            </div>
                        </div>
                        <div class="notification-right">
                            <div class="notification-status" :class="{ 'is-read': notification.is_read }"></div>
                        </div>
                    </div>
                    <div class="desc">{{ notification.description }}</div>
                    <div class="notification-content">
                        <div class="time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                <path d="M8 2v4"></path>
                                <path d="M16 2v4"></path>
                                <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg>
                            <span>{{ $helper.formatDate(notification.created_at) }}</span>
                        </div>
                        <div class="user" v-if="notification.user">
                            <div class="user_avatar"></div>
                            <span class="user_name">{{ notification.user.full_name }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <base-pagination v-if="meta" :meta="meta" @changePage="handleChangePage" />
        </div>
    </div>
</div>
</template>

<style scoped>
.notification-page {
    padding: 2rem 0;
}

.notification-page .container {
    display: grid;
    gap: 1.5rem;
}

.nav-tabs {
    border: none;
    display: flex;
    gap: 1rem;
}

.nav-tabs-wrapper {
    display: flex;
    background: var(--gray-50);
    border-radius: 10px;
    width: 100%;
}

.tab {
    flex: 1;
    padding: 0.8rem 1.3rem;
    border: none;
    background: transparent;
    color: var(--color-primary);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 0.25rem;
    white-space: nowrap;
    line-height: 1;
    font-size: var(--font-size-base);
}

.tab.active {
    background: white;
    color: var(--color-primary);
    border-radius: 10px;
}

.notification-list {
    display: grid;
    gap: 1rem;
}

.notification-card {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    border-radius: 10px;
    padding: 1rem 1.3rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.notification-card.is-read {
    border-left-color: var(--gray-500);
}

.notification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notification-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.notification-card .desc {
    font-size: var(--font-size-heading-6);
    color: var(--gray-500);
}

.notification-left {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

.notification-left .icon-wrapper {
    background: var(--gray-50);
    border-radius: 2rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    border: 1px solid var(--gray-200);
}

.notification-left .title-wrapper {
    display: grid;
    line-height: 1.4;
}

.notification-left .title-wrapper .title-main {
    font-weight: 600;
    font-size: var(--font-size-heading-6);
}

.notification-left .title-wrapper .sub-title {
    font-size: var(--font-size-small);
    font-weight: 500;
    color: var(--color-primary);
}
.notification-card.is-read .sub-title {
    color: var(--gray-500);
}

.notification-content {
    display: flex;
    font-size: var(--font-size-base);
    gap: 1.2rem;
    color: var(--gray-500);
}

.notification-content .time {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.notification-content .user {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.notification-content .user .user_avatar {
    width: 1rem;
    height: 1rem;
    border-radius: 100%;
    background: var(--gray-200);
}

.notification-content .status {
    background: var(--color-primary-transparent);
    border: 1px solid var(--color-primary-light);
    padding: 0.1rem 0.7rem;
    border-radius: 2rem;
    color: var(--color-primary);
    font-size: var(--font-size-small) !important;
}

.notification-status {
    width: 0.8rem;
    height: 0.8rem;
    background: var(--blue-500);
    border-radius: 100%;
}

.notification-status.is-read {
    background: var(--gray-500);
}

@media (max-width: 768px) {
    .nav-tabs {
        display: grid;
    }

    .mark-all_read {
        width: 100%;
    }
}
</style>

<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
    getCurrentInstance
} from 'vue';
import {
    useRouter
} from 'vue-router';
import {
    useStore
} from 'vuex';

const store = useStore();
const router = useRouter();
const {
    proxy
} = getCurrentInstance();
const showDropdown = ref(false);
const showNotifications = ref(false);

const userData = computed(() => store.state.userData);
const notifications = computed(() => store.state.notifications || []);

const fetchNotifications = async () => {
    try {
        const res = await proxy.$api.apiGet('notifications', {
            is_read: 0
        });
        store.dispatch('updateNotifications', res.data || []);
    } catch (e) {
        store.dispatch('updateNotifications', []);
    }
};

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length);

const checkAuth = computed(() => {
    if (store.getters.isAuthenticated) {
        return true;
    }
    return false;
});

const openLoginModal = () => {
    router.push('/login');
};

const openRegisterModal = () => {
    router.push('/register');
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) showNotifications.value = false;
};

const toggleNotifications = async () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        showDropdown.value = false;
        await fetchNotifications();
    }
};

const handleLogout = () => {
    store.dispatch('clearAuth');
    router.push({
        name: 'login'
    });
};

const markAllAsRead = async () => {
    try {
        await proxy.$api.apiPost('notifications/mark-all-read');
        await fetchNotifications();
    } catch (e) {
        // noop
    }
};

onMounted(() => {})
</script>

<template>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo" @click="router.push('/')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open h-5 w-5 text-primary-foreground">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span class="logo-text">EduTutor</span>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-link">Trang chủ</a>
                <a href="#" class="nav-link">Tìm gia sư</a>
                <a href="#" class="nav-link">Về chúng tôi</a>
                <a href="#" class="nav-link">Liên hệ</a>
            </nav>

            <div class="auth-buttons" v-if="!checkAuth">
                <button class="btn-no-bg card-item border-r-2" @click="openLoginModal">
                    Đăng nhập
                </button>
                <button class="btn-md btn-primary card-item border-r-2" @click="openRegisterModal">
                    Đăng ký
                </button>
            </div>

            <div class="user-menu" v-else>
                <div class="question-card user-info">
                    <svg class="icon-lg" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path d="M10.125 8.875C10.125 7.83947 10.9645 7 12 7C13.0355 7 13.875 7.83947 13.875 8.875C13.875 9.56245 13.505 10.1635 12.9534 10.4899C12.478 10.7711 12 11.1977 12 11.75V13" stroke="currentColor" stroke-width="1.9440000000000002" stroke-linecap="round"></path>
                            <circle cx="12" cy="16" r="1" fill="currentColor"></circle>
                            <path d="M7 3.33782C8.47087 2.48697 10.1786 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 10.1786 2.48697 8.47087 3.33782 7" stroke="currentColor" stroke-width="1.9440000000000002" stroke-linecap="round"></path>
                        </g>
                    </svg>
                </div>

                <div class="user-liked-card user-info">
                    <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                    </svg>
                </div>

                <div class="notification-button-card user-info" @click="toggleNotifications">
                    <svg class="icon-xl" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
                </div>

                <!-- Notification Panel -->
                <div v-if="showNotifications" class="notification-panel">
                    <div class="notification-header">
                        <h3>Thông báo</h3>
                        <button @click="markAllAsRead" class="mark-all-read">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 7 17l-5-5"></path>
                                <path d="m22 10-7.5 7.5L13 16"></path>
                            </svg>
                            Đánh dấu tất cả là đã đọc
                        </button>
                    </div>
                    <div class="notification-list">
                        <div v-for="notification in notifications" :key="notification.id" :class="['notification-item', { 'unread': !notification.is_read }]">
                            <div class="notification-icon">
                                <img v-if="notification.icon" :src="notification.icon" class="icon-lg" />
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">{{ notification.name }}</div>
                                <div class="notification-message">{{ notification.description }}</div>
                                <!-- <div class="notification-time">{{ notification.time }}</div> -->
                            </div>
                            <div class="notification-status" :class="{ 'unread': !notification.is_read }"></div>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <router-link to="/notification" class="view-all">Xem tất cả thông báo</router-link>
                    </div>
                </div>

                <div class="user-info" @click="toggleDropdown">
                    <img v-if="userData.avatar" :src="userData.avatar" alt="User avatar" class="user-avatar">
                    <div v-else class="user-avatar">{{ $helper.getFirstCharacterOfLastName(userData.full_name) }}</div>
                    <!-- <span class="user-name">{{ userData.full_name }}</span> -->
                </div>

                <div class="dropdown-menu" v-if="showDropdown">
                    <router-link to="/profile" class="dropdown-item card-item">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Hồ sơ
                    </router-link>
                    <router-link to="/booking/manager" class="dropdown-item card-item">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4b5563" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 2v4"></path>
                            <path d="M16 2v4"></path>
                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                            <path d="M3 10h18"></path>
                        </svg>
                        Lịch học
                    </router-link>
                    <router-link to="/classroom-manager" class="dropdown-item card-item">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                        </svg>
                        Lớp học
                    </router-link>
                    <router-link to="/message" class="dropdown-item card-item">
                        <svg class="icon-md" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M8 18L10.29 20.29C10.514 20.5156 10.7804 20.6946 11.0739 20.8168C11.3674 20.9389 11.6821 21.0018 12 21.0018C12.3179 21.0018 12.6326 20.9389 12.9261 20.8168C13.2196 20.6946 13.486 20.5156 13.71 20.29L16 18H18C19.0609 18 20.0783 17.5786 20.8284 16.8285C21.5786 16.0783 22 15.0609 22 14V7C22 5.93913 21.5786 4.92178 20.8284 4.17163C20.0783 3.42149 19.0609 3 18 3H6C4.93913 3 3.92172 3.42149 3.17157 4.17163C2.42142 4.92178 2 5.93913 2 7V14C2 15.0609 2.42142 16.0783 3.17157 16.8285C3.92172 17.5786 4.93913 18 6 18H8Z" stroke="#000000" stroke-width="1.656" stroke-linecap="round" stroke-linejoin="round"></path> <g clip-path="url(#clip0_8_46)"> <path d="M16 12C15.87 12.0016 15.7409 11.9778 15.62 11.93C15.4971 11.8781 15.3852 11.8035 15.29 11.7101C15.2001 11.6179 15.1287 11.5092 15.08 11.39C15.0296 11.266 15.0025 11.1338 15 11C15.0011 10.7376 15.1053 10.4863 15.29 10.3C15.3825 10.2033 15.4952 10.1282 15.62 10.0801C15.8031 10.0047 16.0044 9.98535 16.1984 10.0245C16.3924 10.0637 16.5705 10.1596 16.71 10.3C16.8947 10.4863 16.9989 10.7376 17 11C16.9975 11.1338 16.9704 11.266 16.92 11.39C16.8713 11.5092 16.7999 11.6179 16.71 11.7101C16.6166 11.8027 16.5057 11.876 16.3839 11.9258C16.2621 11.9755 16.1316 12.0007 16 12Z" fill="#000000"></path> <path d="M12 12C11.87 12.0016 11.7409 11.9778 11.62 11.93C11.4971 11.8781 11.3852 11.8035 11.29 11.7101C11.2001 11.6179 11.1287 11.5092 11.08 11.39C11.0296 11.266 11.0025 11.1338 11 11C11.0011 10.7376 11.1053 10.4863 11.29 10.3C11.3825 10.2033 11.4952 10.1282 11.62 10.0801C11.8031 10.0047 12.0044 9.98535 12.1984 10.0245C12.3924 10.0637 12.5705 10.1596 12.71 10.3C12.8947 10.4863 12.9989 10.7376 13 11C12.9975 11.1338 12.9704 11.266 12.92 11.39C12.8713 11.5092 12.7999 11.6179 12.71 11.7101C12.6166 11.8027 12.5057 11.876 12.3839 11.9258C12.2621 11.9755 12.1316 12.0007 12 12Z" fill="#000000"></path> <path d="M8 12C7.86999 12.0016 7.74091 11.9778 7.62 11.93C7.49713 11.8781 7.38519 11.8035 7.29001 11.7101C7.20006 11.6179 7.12873 11.5092 7.07999 11.39C7.0296 11.266 7.0025 11.1338 7 11C7.0011 10.7376 7.10526 10.4863 7.29001 10.3C7.3825 10.2033 7.49516 10.1282 7.62 10.0801C7.80305 10.0047 8.00435 9.98535 8.19839 10.0245C8.39244 10.0637 8.57048 10.1596 8.70999 10.3C8.89474 10.4863 8.9989 10.7376 9 11C8.9975 11.1338 8.9704 11.266 8.92001 11.39C8.87127 11.5092 8.79994 11.6179 8.70999 11.7101C8.61655 11.8027 8.50575 11.876 8.38391 11.9258C8.26207 11.9755 8.13161 12.0007 8 12Z" fill="#000000"></path> </g> <defs> <clipPath id="clip0_8_46"> <rect width="10" height="2" fill="white" transform="translate(7 10)"></rect> </clipPath> </defs> </g></svg>
                        Tin nhắn
                    </router-link>
                    <a href="#" class="dropdown-item card-item">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Cài đặt
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item card-item text-red" @click.prevent="handleLogout">
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
</template>

<style scoped>
.header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 100;
}

.container {
    margin: 0 auto;
}

.header-content {
    height: var(--height-header);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    color: var(--color-primary);
}

.logo-image {
    height: 40px;
    width: auto;
}

.logo-text {
    font-size: 24px;
    font-weight: 700;
    color: var(--color-primary);
    line-height: 1;
}

.nav-menu {
    display: flex;
    gap: 32px;
}

.nav-link {
    color: #4b5563;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
}

.nav-link:hover {
    color: #111827;
}

.auth-buttons {
    display: flex;
    gap: 1.3rem;
}

.login-button {
    padding: 8px 16px;
    background: white;
    border-radius: 8px;
    border: none;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.register-button {
    padding: 8px 16px;
    background: black;
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.user-menu {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.user-info:hover {
    color: var(--color-primary);
}

.user-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--gray-500);
}

.user-name {
    font-weight: 500;
    color: #111827;
}

.dropdown-icon {
    width: 16px;
    height: 16px;
    color: #6b7280;
}

.dropdown-menu {
    position: absolute;
    display: block;
    width: 100%;
    top: 100%;
    right: 0;
    margin-top: 8px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    min-width: 200px;
    padding: 8px;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    color: #374151;
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: var(--gray-50);
}

.dropdown-item.active,
.dropdown-item:active {
    background: var(--gray-50);
}

.dropdown-divider {
    margin: 8px 0;
    border: none;
    border-top: 1px solid #e5e7eb;
}

.text-red {
    color: #dc2626;
}

@media (max-width: 1024px) {
    .nav-menu {
        display: none;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 16px;
    }

    .logo-text {
        display: none;
    }

    .auth-buttons {
        gap: 8px;
    }

    .login-button,
    .register-button {
        padding: 8px 12px;
        font-size: 14px;
    }
}

/* New Notification Styles */
.notification-button-card {
    position: relative;
    cursor: pointer;
    border-radius: 100%;
    transition: background-color 0.2s;
}

.notification-icon {
    color: #4b5563;
}

.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: black;
    color: white;
    font-size: 0.7rem;
    width: 1rem;
    height: 1rem;
    border-radius: 100%;
    border: 2px solid white;
    font-weight: 500;
    display: flex;
    justify-content: center;
    align-items: center;
}

.notification-panel {
    position: absolute;
    top: 100%;
    right: 0;
    width: 380px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    margin-top: 0.5rem;
    z-index: 50;
}

.notification-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-header h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.mark-all-read {
    font-size: 0.875rem;
    color: #6b7280;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.mark-all-read:hover {
    background-color: #f3f4f6;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    padding: 1rem;
    display: flex;
    gap: 1rem;
    border-bottom: 1px solid #e5e7eb;
    transition: background-color 0.2s;
    cursor: pointer;
}

.notification-item.unread {
    outline: none;
}

.notification-item:hover {
    background-color: #f9fafb;
}

.notification-item .notification-icon {
    flex-shrink: 0;
    width: 2.8rem;
    height: 2.8rem;
    background: white;
    border-radius: 50%;
    border: 1px solid var(--gray-300);
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    color: #111827;
    margin-bottom: 0.25rem;
}

.notification-message {
    color: #6b7280;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.notification-time {
    color: #9ca3af;
    font-size: 0.75rem;
}

.notification-footer {
    padding: 1rem;
    text-align: center;
    border-top: 1px solid #e5e7eb;
}

.notification-status {
    width: 0.5rem;
    height: 0.5rem;
    background: var(--gray-500);
    border-radius: 100%;
}

.notification-status.unread {
    background: var(--blue-500);
}

.view-all {
    color: #4b5563;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.view-all:hover {
    color: #111827;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .notification-panel {
        width: calc(100vw - 2rem);
        position: fixed;
        top: 80px;
        right: 1rem;
    }
}
</style>

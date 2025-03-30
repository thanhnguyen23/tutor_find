<script setup>
import {
    ref,
    computed
} from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { verifyToken } from '@api/auth';

const store = useStore();
const router = useRouter();
const showDropdown = ref(false);

const userData = computed(() => store.state.userData);

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
};

const handleLogout = () => {
    store.dispatch('clearAuth');
    router.push('/login');
};
</script>

<template>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo" @click="router.push('/')">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-book-open h-5 w-5 text-primary-foreground"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                <span class="logo-text">EduTutor</span>
            </div>

            <nav class="nav-menu">
                <a href="#" class="nav-link">Trang chủ</a>
                <a href="#" class="nav-link">Tìm gia sư</a>
                <a href="#" class="nav-link">Khóa học</a>
                <a href="#" class="nav-link">Về chúng tôi</a>
                <a href="#" class="nav-link">Liên hệ</a>
            </nav>

            <div class="auth-buttons" v-if="!checkAuth">
                <button class="login-button" @click="openLoginModal">
                    Đăng nhập
                </button>
                <button class="register-button" @click="openRegisterModal">
                    Đăng ký
                </button>
            </div>

            <div class="user-menu" v-else>
                <div class="user-info" @click="toggleDropdown">
                    <!-- <img v-if="userData.avatar" :src="userData.avatar" alt="User avatar" class="user-avatar"> -->
                    <span class="user-name">{{ userData.full_name }}</span>
                    <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div class="dropdown-menu" v-if="showDropdown">
                    <router-link to="/profile" class="dropdown-item">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Hồ sơ
                    </router-link>
                    <a href="#" class="dropdown-item">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Cài đặt
                    </a>
                    <hr class="dropdown-divider">
                    <a href="#" class="dropdown-item text-red" @click.prevent="handleLogout">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
}

.logo-image {
    height: 40px;
    width: auto;
}

.logo-text {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
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
    gap: 16px;
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

.login-button:hover {
    background: #f3f4f6;
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
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.user-info:hover {
    background: #f3f4f6;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
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
    background: #f3f4f6;
}

.dropdown-item .icon {
    width: 20px;
    height: 20px;
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
</style>

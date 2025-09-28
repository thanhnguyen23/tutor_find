<template>
<div class="main-login">
    <div class="container-login">
        <div class="header">
            <h2>Đăng nhập tài khoản</h2>
            <p>Đăng nhập để tiếp tục</p>
        </div>

        <div class="type-selector">
            <button :class="['type-button', { active: formType === 'email' }]" @click="formType = 'email'">
                Email
            </button>
            <button :class="['type-button', { active: formType === 'phone' }]" @click="formType = 'phone'">
                Số điện thoại
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="login-form">
            <div class="form-group" v-if="formType === 'email'">
                <base-input
                  v-model="formData.email"
                  type="email"
                  label="Email"
                  placeholder="example@gmail.com"
                  :error="formData.errors.email"
                  required
                >
                  <template #icon>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                  </template>
                </base-input>
            </div>

            <div class="form-group" v-if="formType === 'phone'">
                <base-input
                  v-model="formData.phone"
                  type="phone"
                  label="Số điện thoại"
                  placeholder="0123456789"
                  :error="formData.errors.phone"
                  required
                >
                  <template #icon>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </template>
                </base-input>
            </div>

            <div class="form-group">
                <div class="label-group">
                    <label for="password">Mật khẩu</label>
                    <a href="#" class="forgot-password">Quên mật khẩu?</a>
                </div>
                <base-input
                  v-model="formData.password"
                  :type="showPassword ? 'text' : 'password'"
                  label=""
                  placeholder="Nhập mật khẩu"
                  :error="formData.errors.password"
                  required
                >
                  <template #icon>
                    <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </template>
                  <template #unit>
                    <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                        <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                  </template>
                </base-input>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-xl btn-primary" :disabled="isLoading">
                    <span v-if="!isLoading">Đăng nhập</span>
                    <span v-else class="loading-spinner"></span>
                </button>
                <span v-if="formData.errors.general" class="error-message">{{ formData.errors.general }}</span>
            </div>

            <div class="divider">
                <span>Hoặc đăng nhập với</span>
            </div>

            <div class="social-login">
                <button type="button" class="social-button google">
                    <img src="/images/google.svg" alt="Google" class="social-icon">
                    Google
                </button>
                <button type="button" class="social-button facebook">
                    <img src="/images/facebook.svg" alt="Facebook" class="social-icon">
                    Facebook
                </button>
            </div>

            <p class="register-prompt">
                Chưa có tài khoản?
                <a href="#" @click.prevent="openRegisterModal">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>
</template>

<script setup>
import {
    ref,
    reactive,
    getCurrentInstance
} from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';
import { login } from '@api/auth';

const { proxy } = getCurrentInstance();
const router = useRouter();
const store = useStore();

const showPassword = ref(false);
const formType = ref('email');
const isLoading = ref(false);

const formData = reactive({
    email: '',
    phone: '',
    password: '',
    remember: false,
    errors: {}
});

const handleSubmit = async () => {
    try {
        isLoading.value = true;
        const response = await login(formData);

        store.dispatch('updateAuth', { token: response.token, user: response.user });
        // Làm mới thông báo sau khi đăng nhập
        try {
            const res = await proxy.$api.apiGet('notifications', { is_read: 0 });
            store.dispatch('updateNotifications', res.data || []);
        } catch (e) {
            store.dispatch('updateNotifications', []);
        }
        router.push('/');

        // if (response.data.user.role === 0) {
        //     router.push('/student/dashboard');
        // } else {
        //     router.push('/tutor/dashboard');
        // }
    } catch (error) {
        if (error.response?.data?.errors) {
            formData.errors = error.response.data.errors;
        } else {
            formData.errors.general = error.response.data.message;
        }
    } finally {
        isLoading.value = false;
    }
};

const openRegisterModal = () => {
    router.push('/register');
};
</script>

<style scoped>
.main-login {
    background: #fcfcfc;
    padding: 1rem;
}
.container-login {
    max-width: 30rem;
    padding: 2rem;
    margin: 2rem auto;
    background-color: white;
    border-radius: 8px;
}

.header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.header h2 {
    font-size: 24px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.7rem;
}

.header p {
    font-size: 16px;
    color: #6b7280;
    margin-top: 0.5rem;
}

.type-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 8px;
}

.type-button {
    padding: 10px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.type-button.active {
    background: white;
    color: black;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.login-form {
    display: grid;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

.label-group {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.form-input {
    width: 100%;
    padding: 12px 16px 12px 40px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-input:focus {
    outline: none;
    border-color: black;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

.form-input.error {
    border-color: #dc2626;
}

.error-message {
    color: #dc2626;
    font-size: 14px;
}

.toggle-password {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
}

.toggle-password svg {
    width: 20px;
    height: 20px;
}

.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #374151;
    cursor: pointer;
}

.remember-me input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: black;
}

.forgot-password {
    color: black;
    text-decoration: none;
    font-size: var(--font-size-mini);
}

.forgot-password:hover {
    text-decoration: underline;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.divider {
    position: relative;
    text-align: center;
    margin: 1rem 0;
}

.divider::before,
.divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: calc(50% - 70px);
    height: 1px;
    background: #e5e7eb;
}

.divider::before {
    left: 0;
}

.divider::after {
    right: 0;
}

.divider span {
    background: white;
    padding: 0 16px;
    color: #6b7280;
    font-size: 14px;
}

.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.social-button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.social-button:hover {
    background: #f3f4f6;
}

.social-icon {
    width: 20px;
    height: 20px;
}

.register-prompt {
    text-align: center;
    color: #374151;
    margin: 0;
}

.register-prompt a {
    color: black;
    text-decoration: none;
    font-weight: 500;
}

.register-prompt a:hover {
    text-decoration: underline;
}

@media (max-width: 640px) {
    .social-login {
        grid-template-columns: 1fr;
    }
}
</style>

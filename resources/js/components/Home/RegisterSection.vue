<template>
<section class="register-section">
    <div class="container">
        <div class="register-content">
            <div class="left-content">
                <div class="section-badge">Bắt đầu ngay hôm nay</div>
                <h2 class="section-title">Bắt đầu hành trình học tập của bạn ngay hôm nay</h2>
                <p class="section-description">
                    Đăng ký ngay để được tư vấn miễn phí và tìm gia sư phù hợp với nhu cầu của bạn
                </p>
                <div class="features">
                    <div class="feature-item">
                        <span class="feature-label">24/7</span>
                        <span class="feature-text">Hỗ trợ</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-label">100%</span>
                        <span class="feature-text">Đảm bảo</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-label">14 ngày</span>
                        <span class="feature-text">Dùng thử</span>
                    </div>
                </div>
                <button class="register-button">Đăng ký ngay</button>
            </div>
            <div class="right-content">
                <div class="register-form">
                    <h3 class="form-title">Đăng ký nhận tư vấn miễn phí</h3>
                    <form @submit.prevent="handleSubmit">
                        <div class="form-group">
                            <input type="text" placeholder="Nhập họ và tên của bạn" v-model="form.name" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" placeholder="Nhập số điện thoại của bạn" v-model="form.phone" required>
                        </div>
                        <div class="form-group">
                            <input type="email" placeholder="Nhập email của bạn" v-model="form.email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" placeholder="Ví dụ: Toán, Tiếng Anh, Vật lý..." v-model="form.subjects" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="submit-button">Gửi thông tin</button>
                        </div>
                        <div class="form-terms">
                            Bằng cách gửi thông tin, bạn đồng ý với <a href="#" class="link">Điều khoản dịch vụ</a> và <a href="#" class="link">Chính sách bảo mật</a> của chúng tôi.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
import { reactive } from 'vue';

const form = reactive({
    name: '',
    phone: '',
    email: '',
    subjects: ''
});

const handleSubmit = () => {
    // Handle form submission
    console.log('Form submitted:', form);
};
</script>

<style scoped>
.container {
    margin: auto;
}

.register-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}

.section-badge {
    display: inline-block;
    padding: 6px 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
    color: white;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 16px;
    color: white;
}

.section-description {
    font-size: 16px;
    color: #9ca3af;
    margin-bottom: 32px;
    line-height: 1.6;
}

.features {
    display: flex;
    gap: 32px;
    margin-bottom: 32px;
}

.feature-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.feature-label {
    font-size: 24px;
    font-weight: 700;
    color: white;
}

.feature-text {
    font-size: 14px;
    color: #9ca3af;
}

.register-button {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    background: white;
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.register-button:hover {
    background: #f3f4f6;
}

.register-form {
    background: #262626;
    padding: 32px;
    border-radius: 16px;
}

.form-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 24px;
    color: white;
}

.form-group {
    margin-bottom: 16px;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    background: #333333;
    border: 1px solid #404040;
    border-radius: 8px;
    color: white;
    font-size: 16px;
}

.form-group input::placeholder {
    color: #9ca3af;
}

.form-group input:focus {
    outline: none;
    border-color: #6b7280;
}

.submit-button {
    width: 100%;
    padding: 12px 24px;
    background: white;
    color: #1a1a1a;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.submit-button:hover {
    background: #f3f4f6;
}

.form-terms {
    font-size: 14px;
    color: #9ca3af;
    text-align: center;
    margin-top: 16px;
}

.link {
    color: white;
    text-decoration: underline;
}

.link:hover {
    color: #f3f4f6;
}

@media (max-width: 1024px) {
    .register-content {
        grid-template-columns: 1fr;
        gap: 48px;
    }
}

@media (max-width: 768px) {
    .section-title {
        font-size: 30px;
    }

    .features {
        gap: 24px;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 16px;
    }

    .section-title {
        font-size: 24px;
    }

    .features {
        flex-direction: column;
        gap: 16px;
    }

    .register-form {
        padding: 24px;
    }
}
</style>

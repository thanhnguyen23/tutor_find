<template>
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <!-- Left Content -->
            <div class="hero-left">
                <div class="hero-badge">
                    Nền tảng gia sư hàng đầu
                </div>
                <h1 class="hero-title">
                    Học tập hiệu quả cùng gia sư chất lượng
                </h1>
                <p class="hero-description">
                    Kết nối với những gia sư xuất sắc nhất để nâng cao kiến thức và đạt được mục tiêu học tập của bạn.
                </p>
                <div class="hero-buttons">
                    <router-link to="/tutors" class="btn btn-primary">
                        Tìm gia sư ngay
                        <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>
                    <router-link to="/about" class="btn btn-secondary">
                        Tìm hiểu thêm
                    </router-link>
                </div>

                <!-- Stats -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Gia sư chất lượng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">10k+</div>
                        <div class="stat-label">Học sinh tin tưởng</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Đánh giá tích cực</div>
                    </div>
                </div>
            </div>

            <!-- Right Content -->
            <div class="hero-right">
                <div class="hero-image">
                    <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>

                    <!-- Floating Card - Online Tutors -->
                    <div class="floating-card tutor-card">
                        <div class="avatar-group">
                            <div class="avatar"></div>
                            <div class="avatar"></div>
                            <div class="avatar"></div>
                        </div>
                        <div class="tutor-info">
                            <div class="tutor-title">Gia sư xuất sắc</div>
                            <div class="tutor-status">đang online</div>
                        </div>
                    </div>

                    <!-- Floating Card - Results -->
                    <div class="floating-card result-card">
                        <!-- <div class="result-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div> -->
                        <div class="result-info">
                            <div class="result-title">
                                <div class="icon">
                                    <img src="/images/educationLevel/elementary.svg" alt="">
                                </div>
                                <span>Kết quả thực tế</span>
                            </div>
                            <div class="result-stats">
                                <div>Điểm số tăng 25%</div>
                                <div>Tự tin học tập hơn</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
</script>

<style scoped>
.hero {
    width: 100%;
    background: white;
    padding: 64px 0;
}

.hero-content {
    display: flex;
    gap: 64px;
    align-items: flex-start;
}

/* Left Content Styles */
.hero-left {
    flex: 1;
}

.hero-badge {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 24px;
}

.hero-title {
    font-size: var(--font-size-heading-1);
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: -0.02em;
    margin-bottom: 16px;
}

.hero-description {
    font-size: var(--font-size-medium);
    color: #6b7280;
    margin-bottom: 32px;
    line-height: 1.6;
}

.hero-buttons {
    display: flex;
    gap: 16px;
    margin-bottom: 48px;
}

.btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #000;
    color: white;
}

.btn-primary:hover {
    background: #1a1a1a;
    transform: translateY(-2px);
}

.btn-secondary {
    background: white;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-icon {
    width: 20px;
    height: 20px;
    margin-left: 8px;
}

/* Stats Styles */
.hero-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.stat-item {
    text-align: left;
}

.stat-number {
    font-size: var(--font-size-heading-2);
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: var(--font-size-smail);
    color: #6b7280;
}

/* Right Content Styles */
.hero-right {
    flex: 1;
}

.hero-image {
    position: relative;
    aspect-ratio: 4/3;
    background: #f3f4f6;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.placeholder-icon {
    width: 48px;
    height: 48px;
    color: #9ca3af;
}

/* Floating Cards */
.floating-card {
    position: absolute;
    background: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    animation: float 3s ease-in-out infinite;
}

.tutor-card {
    top: 16px;
    right: 16px;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 24px;
}

.avatar-group {
    display: flex;
    margin-right: -8px;
}

.avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid white;
    background: #e5e7eb;
    margin-right: -8px;
}

.tutor-info {
    font-size: var(--font-size-small);
    font-weight: 500;
    line-height: 1.3;
}

.tutor-status {
    /* color: #10b981; */
}

.result-card {
    bottom: 16px;
    left: 16px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    animation-delay: 0.2s;
}

.result-icon {
    width: 40px;
    height: 40px;
    background: #dbeafe;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.result-icon svg {
    width: 24px;
    height: 24px;
    color: #2563eb;
}

.result-info {
    font-size: var(--font-size-small);
}

.result-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    font-weight: 500;
    margin-bottom: 4px;
}

.result-title .icon {
    background: #e7e7e8;
    border-radius: 2rem;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.result-title .icon img {
    width: 20px;
    height: 20px;
}

.result-stats {
    color: #6b7280;
}

.result-stats div {
    position: relative;
    padding-left: 15px;
}

.result-stats div::before {
    content: "";
    position: absolute;
    left: 0;
    top: 6px;
    width: 10px;
    height: 10px;
    background: #10b981;
    border-radius: 50%;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* Responsive Styles */
@media (max-width: 1024px) {
    .hero-content {
        flex-direction: column;
    }

    .hero-title {
        font-size: 44px;
    }

    .hero-left, .hero-right {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .hero {
        padding: 48px 0;
    }

    .hero-title {
        font-size: 36px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (max-width: 640px) {

    .hero-stats {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .stat-item {
        text-align: center;
    }

    .floating-card {
        padding: 8px;
    }

    .result-card {
        display: none;
    }
}
</style>

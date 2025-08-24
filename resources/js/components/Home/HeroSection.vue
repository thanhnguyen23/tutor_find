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
                    <button class="btn-xl btn-primary">
                        Tìm gia sư ngay
                        <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    <button class="btn-xl btn-secondary">
                        Tìm hiểu thêm
                    </button>
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
                    <!-- <img src="/images/banner.webp" alt=""> -->

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
                                    <img class="icon-md" src="/images/educationLevel/elementary.svg" alt="">
                                </div>
                                <span>Kết quả thực tế</span>
                            </div>
                            <div class="result-stats">
                                <div>Điểm số tăng</div>
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
.hero-content {
    display: flex;
    gap: 5rem;
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
    color: var(--color-primary);
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
    background-image: url('/images/banner.webp');
    background-size: cover;
    background-position: center;
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
    .hero-title {
        font-size: 36px;
    }

    .hero-buttons {
        flex-direction: column;
        margin: 0;
    }

    .hero-stats {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .hero-stats {
        display: none;
    }

    .hero-content {
        gap: 3rem;
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

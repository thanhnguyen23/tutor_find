<template>
<section class="why-choose-section">
    <div class="container">
        <div class="section-badge">Dịch vụ chất lượng</div>
        <h2 class="section-title">Tại sao chọn chúng tôi?</h2>
        <p class="section-description">
            Chúng tôi cung cấp dịch vụ gia sư chất lượng cao với nhiều ưu điểm vượt trội
        </p>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="feature-title">Gia sư chất lượng</h3>
                <p class="feature-description">
                    Đội ngũ gia sư được tuyển chọn kỹ lưỡng, có kinh nghiệm và trình độ chuyên môn cao
                </p>
                <ul class="feature-list">
                    <li>Tốt nghiệp từ các trường đại học hàng đầu</li>
                    <li>Có kinh nghiệm giảng dạy tối thiểu 2 năm</li>
                    <li>Được đánh giá và kiểm tra kỹ lưỡng</li>
                </ul>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="feature-title">Phương pháp hiệu quả</h3>
                <p class="feature-description">
                    Phương pháp giảng dạy cá nhân hóa, phù hợp với từng học sinh và mục tiêu học tập
                </p>
                <ul class="feature-list">
                    <li>Đánh giá năng lực và xác định mục tiêu</li>
                    <li>Lộ trình học tập được thiết kế riêng</li>
                    <li>Báo cáo tiến độ định kỳ và điều chỉnh</li>
                </ul>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="feature-title">Linh hoạt thời gian</h3>
                <p class="feature-description">
                    Lịch học linh hoạt, phù hợp với thời gian của học sinh và gia đình
                </p>
                <ul class="feature-list">
                    <li>Học trực tuyến hoặc trực tiếp tại nhà</li>
                    <li>Lịch học linh hoạt, kể cả cuối tuần</li>
                    <li>Dễ dàng thay đổi lịch học khi cần</li>
                </ul>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
</script>

<style scoped>
.why-choose-section {
    text-align: center;
    padding: 60px 0;
    background: white;
}

.container {
    margin: 0 auto;
}

.section-badge {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 16px;
}

.section-description {
    font-size: 16px;
    color: #6b7280;
    text-align: center;
    max-width: 800px;
    margin: 0 auto 48px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.feature-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: #f3f4f6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px;
}

.icon {
    width: 32px;
    height: 32px;
    color: #374151;
}

.feature-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 12px;
}

.feature-description {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 24px;
    line-height: 1.5;
}

.feature-list {
    text-align: left;
    list-style: none;
    padding: 0;
}

.feature-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 12px;
    color: #374151;
    font-size: 14px;
}

.feature-list li::before {
    content: "";
    position: absolute;
    left: 0;
    top: 8px;
    width: 12px;
    height: 12px;
    background: #10b981;
    border-radius: 50%;
}

@media (max-width: 1024px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .why-choose-section {
        padding: 64px 0;
    }

    .section-title {
        font-size: 30px;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .feature-card {
        padding: 24px;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 16px;
    }

    .section-title {
        font-size: 24px;
    }

    .feature-icon {
        width: 48px;
        height: 48px;
    }

    .icon {
        width: 24px;
        height: 24px;
    }
}
</style>

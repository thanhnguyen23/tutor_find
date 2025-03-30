<template>
<section class="reviews-section">
    <div class="container">
        <div class="section-badge">
            <span>Đánh giá từ khách hàng</span>
        </div>
        <h2 class="section-title">Phụ huynh và học sinh nói gì về chúng tôi</h2>
        <p class="section-description">
            Những đánh giá chân thực từ phụ huynh và học sinh đã sử dụng dịch vụ của chúng tôi
        </p>

        <div class="reviews-grid">
            <!-- Review 1 -->
            <div class="review-card">
                <div class="rating">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                </div>
                <p class="review-text">
                    "Gia sư của EduTutor đã giúp con tôi tiến bộ rất nhiều trong học tập. Phương pháp giảng dạy rất hiệu quả và phù hợp với con. Điểm số đã tăng lên đáng kể chỉ sau 2 tháng học."
                </p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <img src="/images/avatar-placeholder.jpg" alt="Nguyễn Thị B">
                    </div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">Nguyễn Thị B</h4>
                        <p class="reviewer-title">Phụ huynh học sinh lớp 10</p>
                    </div>
                </div>
            </div>

            <!-- Review 2 -->
            <div class="review-card">
                <div class="rating">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                </div>
                <p class="review-text">
                    "Tôi đã thử nhiều dịch vụ gia sư khác nhau nhưng EduTutor là lựa chọn tốt nhất. Gia sư không chỉ giỏi chuyên môn mà còn rất tâm lý, biết cách truyền đạt kiến thức dễ hiểu."
                </p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <img src="/images/avatar-placeholder.jpg" alt="Trần Văn D">
                    </div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">Trần Văn D</h4>
                        <p class="reviewer-title">Phụ huynh học sinh lớp 12</p>
                    </div>
                </div>
            </div>

            <!-- Review 3 -->
            <div class="review-card">
                <div class="rating">
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                    <span class="star">★</span>
                </div>
                <p class="review-text">
                    "Nhờ có gia sư từ EduTutor mà tôi đã đạt được điểm cao trong kỳ thi đại học. Cảm ơn đã luôn kiên nhẫn và tận tâm hướng dẫn tôi trong suốt thời gian qua."
                </p>
                <div class="reviewer-info">
                    <div class="reviewer-avatar">
                        <img src="/images/avatar-placeholder.jpg" alt="Lê Minh E">
                    </div>
                    <div class="reviewer-details">
                        <h4 class="reviewer-name">Lê Minh E</h4>
                        <p class="reviewer-title">Học sinh lớp 12</p>
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
.reviews-section {
    padding: 60px 0;
}

.section-badge {
    margin: auto;
    width: 100%;
    text-align: center;
}

.section-badge span {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
    text-align: center;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 16px;
    color: #111827;
}

.section-description {
    font-size: 16px;
    color: #6b7280;
    text-align: center;
    max-width: 800px;
    margin: 0 auto 48px;
}

.reviews-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 32px;
}

.review-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.review-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.rating {
    margin-bottom: 16px;
}

.star {
    color: #fbbf24;
    font-size: 20px;
}

.review-text {
    color: #4b5563;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 24px;
    font-style: italic;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.reviewer-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    overflow: hidden;
}

.reviewer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.reviewer-details {
    flex: 1;
}

.reviewer-name {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px;
}

.reviewer-title {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
}

@media (max-width: 1024px) {
    .reviews-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }
}

@media (max-width: 768px) {
    .reviews-section {
        padding: 64px 0;
    }

    .section-title {
        font-size: 30px;
    }

    .reviews-grid {
        grid-template-columns: 1fr;
    }

    .review-card {
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
}
</style>

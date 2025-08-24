<template>
    <div class="content-section">
        <div class="content-card">
            <!-- Review Header -->
            <div class="review-header">
                <h3 class="section-title">Đánh giá từ học viên</h3>
                <div class="rating-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    4.9 <span>(124 đánh giá)</span>
                </div>
            </div>

            <!-- Rating Overview -->
            <div class="rating-overview">
                <div class="rating-score">
                    <h2>4.9</h2>
                    <div class="rating-stars">
                        <div v-for="i in 5" :key="i" class="star-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                    </div>
                    <div class="total-reviews">124 đánh giá</div>
                </div>

                <div class="rating-bars">
                    <div v-for="rating in ratingStats" :key="rating.stars" class="rating-bar-item">
                        <div class="stars">{{ rating.stars }}</div>
                        <div class="bar-wrapper">
                            <div class="bar" :style="{ width: rating.percentage + '%' }"></div>
                        </div>
                        <div class="percentage">{{ rating.percentage }}%</div>
                    </div>
                </div>

                <div class="rating-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Tỷ lệ phản hồi</div>
                            <div class="stat-value">100%</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Thời gian phản hồi</div>
                            <div class="stat-value">< 2 giờ</div>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-label">Hồ sơ đã xác minh</div>
                            <div class="stat-value verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review List -->
            <div class="review-list">
                <div v-for="review in reviews" :key="review.id" class="review-item">
                    <div class="review-author">
                        <img :src="review.avatar" :alt="review.name" class="author-avatar">
                        <div class="author-info">
                            <div class="author-name">{{ review.name }}</div>
                            <div class="review-date">{{ review.date }}</div>
                        </div>
                        <div class="review-rating">
                            <div class="rating-stars">
                                <svg v-for="i in review.rating" :key="i" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="review-content">{{ review.content }}</div>
                    <div class="review-subject">{{ review.subject }}</div>
                </div>
            </div>

            <!-- Load More Button -->
            <button class="btn-lg btn-secondary w-100">Xem thêm đánh giá</button>
        </div>
    </div>
</template>

<script setup>
const ratingStats = [
    { stars: 5, percentage: 85 },
    { stars: 4, percentage: 10 },
    { stars: 3, percentage: 3 },
    { stars: 2, percentage: 1 },
    { stars: 1, percentage: 1 }
];

const reviews = [
    {
        id: 1,
        name: 'Lan Pham',
        date: 'June 15, 2023',
        rating: 5,
        content: 'Dr. Nguyen is an exceptional mathematics tutor. His teaching methods made complex calculus concepts much easier to understand. He is patient, knowledgeable, and always prepared for our sessions. Thanks to his guidance, I was able to improve my grades significantly and develop a genuine interest in mathematics. I highly recommend him to anyone struggling with math!',
        subject: 'Calculus',
        avatar: '/images/default-avatar.png'
    },
    {
        id: 2,
        name: 'Vinh Tran',
        date: 'May 22, 2023',
        rating: 5,
        content: 'I had been struggling with physics for months before I started lessons with Dr. Minh. His approach to teaching is incredibly effective. He explains complex theories in simple terms and provides practical examples that make the concepts stick. After just a few sessions, I noticed a significant improvement in my understanding and grades. He\'s also very flexible with scheduling and always punctual.',
        subject: 'Physics',
        avatar: '/images/default-avatar.png'
    },
    {
        id: 3,
        name: 'Huong Le',
        date: 'April 14, 2023',
        rating: 4,
        content: 'Dr. Minh helped me prepare for my university entrance exams. His deep knowledge of statistics and patient teaching style made a huge difference. He provided excellent study materials and practice problems that were very similar to the actual exam questions. I would definitely recommend him to anyone looking to improve their math skills.',
        subject: 'Statistics',
        avatar: '/images/default-avatar.png'
    }
];
</script>

<style scoped>
@import url('@css/UserDetail.css');

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.rating-badge {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #18181b;
    font-weight: 600;
}

.rating-badge svg {
    color: #facc15;
}

.rating-badge span {
    color: #71717a;
    font-weight: normal;
}

.rating-overview {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 2rem;
    padding: 2rem;
    background: #fafafa;
    border-radius: 0.75rem;
    margin-bottom: 2rem;
}

.rating-score {
    text-align: center;
}

.rating-score h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #18181b;
    margin: 0 0 0.5rem 0;
}

.rating-stars {
    display: flex;
    gap: 0.25rem;
    color: #facc15;
    margin-bottom: 0.5rem;
}

.total-reviews {
    color: #71717a;
    font-size: 0.875rem;
}

.rating-bars {
    padding: 0.5rem 0;
}

.rating-bar-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.rating-bar-item .stars {
    width: 1rem;
    color: #71717a;
    font-size: 0.875rem;
}

.bar-wrapper {
    flex: 1;
    height: 0.5rem;
    background: #e4e4e7;
    border-radius: 9999px;
    overflow: hidden;
}

.bar {
    height: 100%;
    background: #facc15;
    border-radius: 9999px;
}

.percentage {
    width: 2.5rem;
    color: #71717a;
    font-size: 0.875rem;
    text-align: right;
}

.rating-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding-left: 2rem;
    border-left: 1px solid #e4e4e7;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 0.5rem;
    color: #71717a;
}

.stat-content {
    flex: 1;
}

.stat-label {
    color: #71717a;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.stat-value {
    color: #18181b;
    font-weight: 500;
}

.stat-value.verified {
    color: #059669;
}

.review-list {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    margin-bottom: 2rem;
}

.review-item {
    padding-bottom: 2rem;
    border-bottom: 1px solid #e4e4e7;
}

.review-item:last-child {
    padding-bottom: 0;
    border-bottom: none;
}

.review-author {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.author-avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    object-fit: cover;
}

.author-info {
    flex: 1;
}

.author-name {
    font-weight: 600;
    color: #18181b;
    margin-bottom: 0.25rem;
}

.review-date {
    color: #71717a;
    font-size: 0.875rem;
}

.review-rating {
    color: #facc15;
}

.review-content {
    color: #18181b;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.review-subject {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: #f4f4f5;
    border-radius: 9999px;
    color: #71717a;
    font-size: 0.875rem;
}

.btn-load-more {
    width: 100%;
    padding: 0.75rem;
    background: white;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    color: #18181b;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-load-more:hover {
    background: #f4f4f5;
}

@media (max-width: 768px) {
    .rating-overview {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .rating-stats {
        padding-left: 0;
        border-left: none;
        border-top: 1px solid #e4e4e7;
        padding-top: 1.5rem;
    }
}
</style>

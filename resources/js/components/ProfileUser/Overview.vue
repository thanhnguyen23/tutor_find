<template>
<div class="overview">
    <div class="section upcoming-schedule-section">
        <div class="section-header">
            <div class="header-left">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                <span>Lịch học sắp tới</span>
            </div>
            <a href="#" class="view-all">
                <span>Xem tất cả</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        </div>

        <div v-if="upcomingLessons.length === 0" class="card-no-data">
            <div class="logo-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </div>

            <div class="content-wrapper">
                <h4>Chưa có lịch học sắp tới</h4>
                <p>Hãy thêm lịch học của bạn để tăng tiến trong quá trình học tập</p>
            </div>
        </div>

        <!-- Upcoming lessons list -->
        <div v-else class="upcoming-list">
            <div class="upcoming-item" v-for="item in upcomingLessons" :key="item.id">
                <div class="upcoming-date">
                    <div class="upcoming-date-dow">{{ formatDayOfWeek(item.date) }}</div>
                    <div class="upcoming-date-day">{{ formatDay(item.date) }}</div>
                    <div class="upcoming-date-month">{{ formatMonth(item.date) }}</div>
                </div>
                <div class="upcoming-main">
                    <div class="upcoming-meta">
                        <span class="upcoming-subject">{{ item.subject?.name || 'N/A' }}</span>
                        <span class="upcoming-time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>{{ item.time_slot_start.name }} - {{ item.time_slot_end.name }}</span>
                        </span>
                        <span class="upcoming-study">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            <span>{{ item.study_location?.name || 'nhà gia sư' }}</span>
                        </span>
                    </div>
                    <div class="upcoming-title">{{ item.user?.full_name || 'Gia sư' }}</div>
                    <div class="upcoming-comment" v-if="item.note">
                        <span>
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            Ghi chú cho gia sư:
                        </span>
                        <span>{{ item.note }}</span>
                    </div>
                </div>
                <div class="upcoming-status" :class="getStatusClass(item.status)">{{ getStatusText(item.status) }}</div>
            </div>
        </div>
    </div>

    <div class="section recent-messages-section">
        <div class="section-header">
            <div class="header-left">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                <span>Tin nhắn gần đây</span>
            </div>
            <a href="#" class="view-all">
                <span>Xem tất cả</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"></path></svg>
            </a>
        </div>
        <div class="recent-message-list">
            <div class="recent-message-item" v-for="msg in fakeMessages" :key="msg.id">
                <div class="msg-avatar"></div>
                <div class="msg-main">
                    <div class="msg-header">
                        <span class="msg-name">{{ msg.name }}</span>
                        <span class="period-badge">{{ msg.subject }}</span>
                    </div>
                    <div class="msg-content">{{ msg.content }}</div>
                    <div class="msg-footer">
                        <span class="msg-date">{{ msg.date }}</span>
                        <span class="msg-status">{{ msg.status }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đánh giá của tôi -->
    <div class="section my-reviews-section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                <span>Đánh giá của tôi</span>
            </div>
            <a href="#" class="view-all">
                <span>Viết đánh giá</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-sm"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
            </a>
        </div>
        <div class="my-review-list">
            <div class="my-review-item" v-for="review in fakeReviews" :key="review.id">
                <div class="review-avatar"></div>
                <div class="review-main">
                    <div class="review-header">
                        <span class="review-name">{{ review.name }}</span>
                        <span class="period-badge">{{ review.subject }}</span>
                        <span class="review-date">{{ review.date }}</span>
                    </div>
                    <div class="review-rating">
                        <span v-for="n in 5" :key="n">
                            <svg v-if="n <= review.rating" class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </span>
                        <span class="review-rating-value">{{ review.rating }}/5</span>
                    </div>
                    <div class="review-content">{{ review.content }}</div>
                    <div class="review-footer">
                        <span class="review-likes">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-sm"><path d="M7 10v12"></path><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z"></path></svg>
                            <span>{{ review.likes }} người thấy hữu ích</span>
                        </span>
                        <a href="#" class="review-edit">Chỉnh sửa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import {
    getCurrentInstance
} from 'vue';

const {proxy} = getCurrentInstance();
const upcomingLessons = ref([]);
const loading = ref(true);

// Fake data for other sections (keeping as is)
const fakeMessages = [
    {
        id: 1,
        name: 'Thầy Nguyễn Văn A',
        subject: 'Toán học',
        content: 'Em đã làm bài tập về nhà chưa? Thầy sẽ kiểm tra vào buổi học tới.',
        date: '14/1/2024',
        status: 'Từ gia sư'
    },
    {
        id: 2,
        name: 'Cô Trần Thị B',
        subject: 'Vật lý',
        content: 'Cảm ơn cô đã giải thích rất chi tiết. Em đã hiểu rõ hơn về dao động cơ học.',
        date: '13/1/2024',
        status: 'Đã gửi'
    },
    {
        id: 3,
        name: 'Thầy Lê Văn C',
        subject: 'Hóa học',
        content: 'Buổi học mai em có thể đến sớm 15 phút không? Thầy muốn ôn lại bài cũ.',
        date: '13/1/2024',
        status: 'Từ gia sư'
    }
];

const fakeReviews = [
    {
        id: 1,
        name: 'Thầy Nguyễn Văn A',
        subject: 'Toán học',
        date: '2024-01-10',
        rating: 4,
        content: 'Thầy dạy rất tận tâm và dễ hiểu. Em đã cải thiện được nhiều trong môn Toán.',
        likes: 12
    }
];

const fetchUpcomingLessons = async () => {
    try {
        loading.value = true;
        const response = await proxy.$api.apiGet('bookings/coming-lessons');
        if (response.success) {
            upcomingLessons.value = response.data;
        }
    } catch (error) {
        console.error('Error fetching upcoming lessons:', error);
        upcomingLessons.value = [];
    } finally {
        loading.value = false;
    }
};

const formatDayOfWeek = (dateString) => {
    const date = new Date(dateString);
    const days = ['CN', 'Th 2', 'Th 3', 'Th 4', 'Th 5', 'Th 6', 'Th 7'];
    return days[date.getDay()];
};

const formatDay = (dateString) => {
    const date = new Date(dateString);
    return date.getDate();
};

const formatMonth = (dateString) => {
    const date = new Date(dateString);
    const months = ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6', 'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'];
    return months[date.getMonth()];
};

const getStatusClass = (status) => {
    const statusClasses = {
        'pending': 'status-pending',
        'confirmed': 'status-confirmed',
        'completed': 'status-completed',
        'cancelled': 'status-cancelled'
    };
    return statusClasses[status] || 'status-pending';
};

const getStatusText = (status) => {
    const statusTexts = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'completed': 'Đã hoàn thành',
        'cancelled': 'Đã hủy'
    };
    return statusTexts[status] || 'Chờ xác nhận';
};

// Lifecycle
onMounted(() => {
    fetchUpcomingLessons();
});
</script>

<style scoped>
@import url('@css/profile.css');

.overview {
    display: grid;
    gap: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 500;
    font-size: var(--font-size-heading-5);
}

.view-all {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: black;
    font-size: var(--font-size-small);
}

.empty-state svg {
    margin-bottom: 0.5rem;
    color: #94a3b8;
}

.upcoming-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 0.7rem;
}
.upcoming-item {
    display: flex;
    align-items: center;
    background: #f1f5f94d;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.1rem 1.2rem;
    gap: 1.2rem;
    position: relative;
}
.upcoming-date {
    min-width: 48px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.5rem 1.5rem;
    font-weight: 600;
}
.upcoming-date-dow {
    font-size: 0.69rem;
    color: #475569;
}
.upcoming-date-day {
    font-size: 1.3rem;
    color: #18181b;
    font-weight: 700;
}
.upcoming-date-month {
    font-size: 0.66rem;
    color: #475569;
}
.upcoming-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.upcoming-meta {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    font-size: 0.75rem;
    font-weight: 500;
}
.upcoming-subject {
    background: transparent;
    border: 1px solid #e2e8f0;
    border-radius: 2rem;
    padding: 0.1rem 0.5rem;
}
.upcoming-time,
.upcoming-study {
    display: flex;
    align-items: center;
    gap: 0.2rem;
    color: #64748b;
}
.upcoming-time svg,
.upcoming-study svg {
    width: 0.8rem;
    height: 0.8rem;
}
.upcoming-online {
    color: #6366f1;
    display: flex;
    align-items: center;
}
.upcoming-title {
    font-size: 1.08rem;
    font-weight: 500;
    color: #18181b;
}
.upcoming-comment {
    max-width: 400px;
    display: grid;
    gap: 0.2rem;
    font-size: var(--font-size-mini);
    line-height: 1.1rem;
    color: #64748b;
}
.upcoming-comment span:first-child {
    color: #18181b;
}
.upcoming-status {
    text-align: right;
    font-size: 0.75rem;
    line-height: 1rem;
    font-weight: 600;
}

.recent-message-list, .my-review-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 0.7rem;
}
.recent-message-item, .my-review-item {
    display: flex;
    align-items: flex-start;
    background: #f1f5f94d;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.2rem;
    gap: 1.2rem;
}
.msg-avatar, .review-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e5e7eb;
    flex-shrink: 0;
}
.msg-main, .review-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}
.msg-header, .review-header {
    display: flex;
    align-items: center;
    gap: 0.7rem;
    font-weight: 500;
    font-size: 0.95rem;
}
.msg-name, .review-name {
    color: #18181b;
}
.msg-content, .review-content {
    font-size: var(--font-size-small);
    color: #64748b;
}
.msg-footer, .review-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.2rem;
    font-size: var(--font-size-mini);
    color: #64748b;
}
.msg-date, .review-date {
    color: #64748b;
}
.msg-status {
    font-weight: 500;
}
.review-rating {
    display: flex;
    align-items: center;
    gap: 0.2rem;
    margin: 0;
}
.review-rating-value {
    margin-left: 0.5rem;
    color: #18181b;
    font-size: 0.9rem;
    font-weight: 500;
}
.review-likes {
    color: #64748b;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}
.review-edit {
    margin-left: auto;
    color: black;
    font-size: 0.8rem;
    text-decoration: none;
    cursor: pointer;
}
</style>

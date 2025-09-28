<script setup>
import {
    defineEmits,
    ref,
    onMounted,
    computed
} from 'vue'
import {
    useRouter,
    useRoute
} from 'vue-router'
import {
    useStore
} from 'vuex'
import {
    getCurrentInstance
} from 'vue';

const {
    proxy
} = getCurrentInstance();
const router = useRouter()
const store = useStore()
const route = useRoute()

const bookingData = ref(null)
const endTimeText = computed(() => bookingData.value?.end_time_text || '')

onMounted(async () => {
    const bookingId = route.params.id
    if (bookingId) {
        try {
            const response = await proxy.$api.apiGet(`bookings/${bookingId}`)
            if (response) {
                bookingData.value = response.data;
            } else {
                router.push({
                    name: 'home'
                })
            }
        } catch (e) {
            router.push({
                name: 'home'
            })
        }
    }
})
</script>
<template>
<div class="booking-success-page" v-if="bookingData?.user">
    <div class="container">
        <div class="lesson-info-layout">
            <div class="lesson-info-left">
                <div class="info-card confirm-main-card">
                    <div class="booking-success-header">
                        <div class="booking-success-icon-row">
                            <div class="booking-success-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,2313,2337">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="booking-success-title">Yêu cầu đã được gửi thành công!</div>
                        <div class="booking-success-desc">Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi</div>
                    </div>
                    <!-- <div class="booking-success-pending-box">
                                <svg class="booking-success-pending-icon" width="22" height="22" fill="none" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" /></svg>
                                <span class="booking-success-pending-text">
                                    <b>Chờ gia sư xác nhận</b><br>Gia sư sẽ xem xét yêu cầu của bạn và phản hồi trong vòng 24 giờ. Bạn sẽ nhận được thông báo qua email.
                                </span>
                            </div> -->

                    <div class="booking-information" v-if="bookingData">
                        <div class="card-header">
                            <p class="booking-info-title">Thông tin đặt lịch</p>
                            <p class="booking-status">{{ bookingData.statusText }}</p>
                        </div>
                        <div class="tutor-card">
                            <div class="card-content tutor-content-horizontal">
                                <div class="tutor-avatar">
                                    <img :src="bookingData.tutor?.avatar || '/images/default-avatar.png'" />
                                </div>
                                <div class="tutor-info">
                                    <div class="left">
                                        <div class="tutor-name">{{ bookingData.tutor?.full_name }}</div>
                                        <div class="tutor-rating">
                                            <span class="star">
                                                <svg viewBox="0 0 24 24" fill="#facc15">
                                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                                                </svg>
                                                4.9
                                            </span>
                                            <span class="review-count">(127 đánh giá)
                                            </span>
                                        </div>
                                        <div class="tutor-location">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z" />
                                                <circle cx="12" cy="10" r="3" />
                                            </svg>
                                            {{ bookingData.tutor?.address || 'Chưa cập nhật' }}
                                        </div>
                                    </div>
                                    <div class="right">
                                        <div class="tutor-email">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,4905,4914">
                                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                            </svg>
                                            {{ bookingData.tutor?.email || 'Chưa cập nhật' }}
                                        </div>
                                        <div class="tutor-phone">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,4712,4721">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                            {{ bookingData.tutor?.phone || 'Chưa cập nhật' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="lesson-card">
                            <div class="lesson-details-grid">
                                <div class="lesson-detail-item">
                                    <div class="detail-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                            <path d="M22 10v6"></path>
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                        </svg>
                                    </div>
                                    <div class="detail-value">
                                        <span class="title">Môn học</span>
                                        <span class="sub-title">{{ bookingData.subject?.name }}</span>
                                    </div>
                                </div>
                                <div class="lesson-detail-item">
                                    <div class="detail-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                            <path data-v-6304ec4f="" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                            <path data-v-6304ec4f="" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                        </svg>
                                    </div>
                                    <div class="detail-value">
                                        <span class="title">Cấp độ</span>
                                        <span class="sub-title">{{ bookingData.education_level?.name }}</span>
                                    </div>
                                </div>
                                <div class="lesson-detail-item">
                                    <div class="detail-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                            <path d="M8 2v4"></path>
                                            <path d="M16 2v4"></path>
                                            <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                            <path d="M3 10h18"></path>
                                        </svg>
                                    </div>
                                    <div class="detail-value">
                                        <span class="title">Thời gian</span>
                                        <span class="sub-title">{{ bookingData.date }}</span>
                                        <span class="title">{{ bookingData.time_slot?.name }} - {{ endTimeText }} ({{ $helper.formatDuration(bookingData.duration) }})</span>
                                    </div>
                                </div>
                                <div class="lesson-detail-item" v-if="bookingData.tutor_session">
                                    <div class="detail-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/></svg>
                                    </div>
                                    <div class="detail-value">
                                        <span class="title">Loại buổi học</span>
                                        <span class="sub-title">{{ bookingData.tutor_session?.name }}</span>
                                    </div>
                                </div>
                                <template v-if="bookingData.is_package">
                                    <div class="lesson-detail-item">
                                        <div class="detail-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                                <path data-v-6304ec4f="" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                                <path data-v-6304ec4f="" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                            </svg>
                                        </div>
                                        <div class="detail-value">
                                            <span class="title">Gói học</span>
                                            <span class="sub-title">{{ bookingData.package?.name }}</span>
                                            <span class="info-detail-sub">{{ bookingData.package?.number_of_lessons }} buổi học</span>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="lesson-detail-item">
                                        <div class="detail-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                        </div>
                                        <div class="detail-value">
                                            <span class="title">Học phí mỗi giờ</span>
                                            <span class="sub-title">{{ $helper.formatCurrency(bookingData.hourly_rate) }}</span>
                                            <span class="title">Thời lượng: {{ $helper.formatDuration(bookingData.duration) }}</span>
                                        </div>
                                    </div>
                                </template>
                                <div class="lesson-detail-item" v-if="bookingData.study_location">
                                    <div class="detail-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z"></path>
                                            <circle cx="12" cy="10" r="3"></circle>
                                        </svg>
                                    </div>
                                    <div class="detail-value">
                                        <span class="title">Địa điểm</span>
                                        <span class="sub-title">
                                            {{ bookingData.study_location.name }}
                                        </span>
                                        <span class="title">{{ bookingData.location }}</span>
                                    </div>
                                </div>
                                <template v-if="bookingData.note">
                                    <div class="lesson-detail-item">
                                        <div class="detail-label">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                                <path d="M10 9H8"></path>
                                                <path d="M16 13H8"></path>
                                                <path d="M16 17H8"></path>
                                            </svg>
                                        </div>
                                        <div class="detail-value">
                                            <span class="title">Ghi chú</span>
                                            <span class="sub-title">{{ bookingData.note }}</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="booking-info-bottom-row">
                                <div class="booking-info-code">
                                    <span class="booking-info-code-label">Mã đặt lịch</span>
                                    <div class="request-code">
                                        <span class="booking-info-code-value">{{ bookingData.request_code }}</span>
                                        <span class="booking-info-code-copy">
                                            <svg width="18" height="18" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                <rect x="9" y="9" width="13" height="13" rx="2" />
                                                <path d="M5 15V5a2 2 0 0 1 2-2h10" /></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="booking-info-total">
                                    <span class="booking-info-total-label">Tổng thanh toán</span>
                                    <span class="booking-payment_status">{{ bookingData.payment.statusText }}</span>
                                    <span class="booking-info-total-value">{{ $helper.formatCurrency(bookingData.total_price) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Thêm giao diện các bước tiếp theo -->
                    <div class="next-steps-box">
                        <div class="next-steps-title">Các bước tiếp theo</div>
                        <ul class="next-steps-list">
                            <li class="step active">
                                <span class="step-number">1</span>
                                <span class="step-content">
                                    <span>Chờ gia sư xác nhận</span>
                                    <span>Gia sư sẽ phản hồi trong vòng 24 giờ</span>
                                </span>
                            </li>
                            <li class="step">
                                <span class="step-number">2</span>
                                <span class="step-content">
                                    <span>Nhận thông báo</span>
                                    <span>Bạn sẽ nhận được email và SMS thông báo</span>
                                </span>
                            </li>
                            <li class="step">
                                <span class="step-number">3</span>
                                <span class="step-content">
                                    <span>Thanh toán</span>
                                    <span>Thanh toán để bắt đầu buổi học</span>
                                </span>
                            </li>
                            <li class="step">
                                <span class="step-number">4</span>
                                <span class="step-content">
                                    <span>Bắt đầu học</span>
                                    <span>Chuẩn bị tài liệu và bắt đầu buổi học</span>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="booking-actions-row">
                        <button class="btn-md btn-primary">
                            <span class="icon">
                                <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" /></svg>
                            </span>
                            Xem lịch học
                        </button>
                        <button class="btn-md btn-secondary">
                            <span class="icon">
                                <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <rect x="2" y="7" width="20" height="14" rx="2" />
                                    <path d="M16 3h-8a2 2 0 0 0-2 2v2h12V5a2 2 0 0 0-2-2z" />
                                    <path d="M8 11h8" />
                                    <path d="M8 15h6" /></svg>
                            </span>
                            Nhắn tin cho gia sư
                        </button>
                        <button class="btn-md btn-secondary">
                            <span class="icon">
                                <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M15 19l-7-7 7-7" /></svg>
                            </span>
                            Về trang chủ
                        </button>
                    </div>

                    <div class="support-box">
                        <div class="support-title">Cần hỗ trợ?</div>
                        <div class="support-desc">Liên hệ với chúng tôi nếu bạn có bất kỳ câu hỏi nào</div>
                        <div class="support-actions">
                            <button class="btn-md btn-secondary">
                                <span class="icon">
                                    <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                    </svg>
                                </span>
                                Hotline
                            </button>
                            <button class="btn-md btn-secondary">
                                <span class="icon">
                                    <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                    </svg>
                                </span>
                                Email
                            </button>
                            <button class="btn-md btn-secondary">
                                <span class="icon">
                                    <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </span>
                                Tải hóa đơn
                            </button>
                            <button class="btn-md btn-secondary">
                                <span class="icon">
                                    <svg width="20" height="20" fill="none" stroke="#18181b" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="3.5"></circle>
                                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 8 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 8a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 8 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09A1.65 1.65 0 0 0 16 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 8c.14.31.22.65.22 1v.09A1.65 1.65 0 0 0 21 12c0 .35-.08.69-.22 1z"></path>
                                    </svg>
                                </span>
                                Chia sẻ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="user-info_mobile" v-if="bookingData?.user">
    <div class="tutor-card">
        <div class="tutor-content-horizontal">
            <div class="tutor-avatar">
                <img :src="bookingData.user.avatar || bookingData.user.user_avatar || '/images/default-avatar.png'" />
            </div>
            <div class="tutor-info">
                <div class="left">
                    <div class="tutor-name">{{ bookingData.user?.full_name }}</div>
                    <div class="tutor-rating">
                        <span class="star">
                            <svg viewBox="0 0 24 24" fill="#facc15">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                            4.9
                        </span>
                        <span class="review-count">(127 đánh giá)
                        </span>
                    </div>
                    <div class="tutor-location">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ bookingData.user?.address || 'Chưa cập nhật' }}
                    </div>
                </div>
                <div class="right">
                    <div class="tutor-email">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,4905,4914">
                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                        </svg>
                        {{ bookingData.user?.email || 'Chưa cập nhật' }}
                    </div>
                    <div class="tutor-phone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,4712,4721">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                        {{ bookingData.user?.phone || 'Chưa cập nhật' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
@import url("@css/BookingCommon.css");

.booking-information {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1.5rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #dfe1e3;
}

.action {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.lesson-info-layout {
    display: flex;
    gap: 2rem;
    align-items: center;
    justify-content: center;
    padding: 2rem 0 4rem 0;
    position: relative;
}

.lesson-info-left {
    width: 50%;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.info-card {
    display: grid;
    gap: 1.5rem;
    padding: 0;
    border: none;
    overflow: hidden;
    box-shadow: none;
}

.card-content {
    background: white;
}

.lesson-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem 2.5rem;
}

.lesson-detail-item {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

/* .detail-label {
    font-size: 0.97rem;
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.1rem;
} */

.detail-label i {
    color: #222;
    font-size: 1.08em;
}

.detail-value {
    display: flex;
    flex-direction: column;
}

.detail-value .sub-title {
    font-size: var(--font-size-heading-6);
    color: #222;
    font-weight: 500;
}

.detail-value .title {
    color: #6b7280;
    font-size: var(--font-size-small);
}

.order-summary-card .card-content {
    padding: 1.4rem;
}

.order-row {
    display: flex;
    justify-content: space-between;
    font-size: 1rem;
    color: #4b5563;
    margin-bottom: 0.7rem;
}

.order-label {
    font-size: 0.97rem;
}

.order-value {
    font-size: var(--font-size-heading-6);
    font-weight: 500;
}

.order-value.total {
    color: #222;
    font-size: 1.18rem;
    font-weight: 700;
}

.order-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.18rem;
    font-weight: 700;
    color: #222;
    margin: 1.1rem 0 0.7rem 0;
    border-top: 1px solid #eee;
    padding-top: 0.7rem;
}

.order-benefit-box {
    background: #f6f7f9;
    border-radius: 12px;
    padding: 1.1rem 1rem 1.1rem 1rem;
    margin-top: 1.1rem;
}

.order-benefits {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 0.97rem;
    color: #222;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.order-benefits li {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.icon-check {
    width: 1.1em;
    height: 1.1em;
    flex-shrink: 0;
}

@media (max-width: 1100px) {
    .lesson-info-layout {
        flex-direction: column;
        gap: 2.5rem;
    }

    .lesson-info-right {
        flex-direction: row;
        gap: 1.5rem;
    }

    .lesson-info-right>* {
        flex: 1;
    }
}

@media (max-width: 700px) {
    .lesson-info-layout {
        padding: 0.5rem 0 2rem 0;
    }

    .info-card {
        padding: 0;
    }
}

.booking-success-page {
    background: #fcfcfd;
}

.booking-success-header {
    text-align: center;
}

.booking-success-icon-row {
    display: flex;
    justify-content: center;
    align-items: center;
}

.booking-success-icon {
    background: black;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.7rem;
}

.booking-success-icon svg {
    color: white;
}

.booking-success-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: black;
}

.booking-success-desc {
    color: #6b7280;
}

.booking-success-pending-box {
    background: #fefce8;
    border: 1.5px solid #fde68a;
    border-radius: 8px;
    color: #a16207;
    display: flex;
    align-items: center;
    gap: 0.7rem;
    padding: 1.1rem;
    font-size: var(--font-size-small);
}

.booking-success-pending-icon {
    flex-shrink: 0;
}

.booking-success-pending-text {
    display: inline-block;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: none;
    border: none;
    padding: 0;
}

.booking-info-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0;
}

.booking-status {
    margin: 0;
    color: white;
    background: black;
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    border-radius: 2rem;
    width: max-content;
}

.booking-payment_status {
    margin: 0;
    color: white;
    background: #198754;
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    border-radius: 2rem;
    width: max-content;
}

.booking-info-bottom-row {
    margin-top: 1.2rem;
    display: flex;
    align-items: center;
    gap: 1.2rem;
    flex-wrap: wrap;
    justify-content: space-between;
}

.booking-info-code {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    font-size: var(--font-size-heading-6);
}

.booking-info-code-label {
    color: #6b7280;
    font-weight: 500;
}

.booking-info-code-value {
    color: #18181b;
    letter-spacing: 1px;
}

.booking-info-code-copy {
    cursor: pointer;
    color: #6b7280;
    transition: color 0.2s;
}

.request-code {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.booking-info-code-copy:hover {
    color: #18181b;
}

.booking-info-total {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.1rem;
}

.booking-info-total-label {
    color: #6b7280;
    font-weight: 500;
    font-size: 1.25rem;
}

.booking-info-total-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: #18181b;
}

.booking-info-total-sub {
    color: #6b7280;
    font-size: 0.97em;
    font-weight: 400;
}

.tutor-email,
.tutor-phone {
    color: #6b7280;
    font-size: var(--font-size-mini);
    display: flex;
    align-items: center;
    gap: 0.3em;
}

.tutor-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

.tutor-info svg {
    width: 0.9rem;
    height: 0.9rem;
}

.tutor-card {
    background: white;
}

.tutor-content-horizontal {
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #dfe1e3;
}

/* Các bước tiếp theo */
.next-steps-box {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #dfe1e3;
    padding: 1.5rem;
}

.next-steps-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.next-steps-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.next-steps-list .step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.2rem;
    color: #6b7280;
}

.next-steps-list .step:last-child {
    margin-bottom: 0;
}

.next-steps-list .step-number {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f3f4f6;
    color: black;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: var(--font-size-mini);
    margin-top: 2px;
}

.next-steps-list .step.active .step-number {
    background: black;
    color: #fff;
}

.next-steps-list .step-content {
    display: flex;
    flex-direction: column;
}

.next-steps-list .step-content span:first-child {
    color: #18181b;
    font-weight: 500;
}

/* Hỗ trợ */
.support-box {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 2rem 1.5rem 1.5rem 1.5rem;
    text-align: center;
}

.support-title {
    font-size: 1.15rem;
    font-weight: 700;
    margin-bottom: 0.3rem;
}

.support-desc {
    color: #6b7280;
    margin-bottom: 1.2rem;
    font-size: 1rem;
}

.support-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
}

.support-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.5rem 1.2rem;
    font-size: 1rem;
    color: #18181b;
    cursor: pointer;
    transition: background 0.2s, border 0.2s;
    font-weight: 500;
}

.support-btn:hover {
    background: #f3f4f6;
}

.support-btn .icon {
    display: flex;
    align-items: center;
    font-size: 1.2em;
}

/* Hàng button điều hướng */
.booking-actions-row {
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
}

.booking-actions-row button {
    width: 100%;
}

.booking-actions-row .icon {
    display: flex;
    align-items: center;
    font-size: 1.2em;
}

@media (max-width: 1024px) {
    .lesson-info-left {
        width: 100%;
    }

    .booking-actions-row {
        display: flex;
        flex-wrap: wrap;
    }
}
</style>

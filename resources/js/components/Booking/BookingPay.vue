<template>
    <div class="container">
        <div class="lesson-info-layout booking-content">
            <!-- Left Column: Payment Interface -->
            <div class="lesson-info-left">
                <div class="info-card payment-main-card">
                    <!-- <div class="payment-header card-header">
                        <span>Thanh toán chuyển khoản ngân hàng</span>
                        <div class="expiration-timer-container">
                            <span class="expiration-label">Sẽ hết hạn sau:</span>
                            <span class="expiration-timer">{{ formatTime(remainingTime) }}</span>
                        </div>
                    </div> -->
                    <div class="card-content bank-content">
                        <div class="bank-content-container">
                            <div class="qr-content">
                                <h4>Thanh toán chuyển khoản ngân hàng</h4>
                                <div class="qr-code">
                                    <div class="qr-placeholder">
                                        <img :src="qrUrl || '/images/payment/qr.png'" alt="QR Code" class="qr-code" />

                                        <div class="qr-info-wrapper">
                                            <div class="round-button"></div>

                                            <div class="qr-info">
                                                <span class="qr-info_label text-small">Hết hạn sau:</span>
                                                <span class="qr-info_value text-small">{{ formatTime(remainingTime) }}</span>
                                            </div>
                                            <div class="qr-info">
                                                <span class="qr-info_label">Số tiền:</span>
                                                <span class="qr-info_value">{{ $helper.formatCurrency(paymentInfo?.amount || totalPrice) }}</span>
                                            </div>

                                            <div class="round-button"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="qr-instructions">
                                    <p class="qr-instructions-text">
                                        <span>Mở <b>ứng dụng ngân hàng</b> của bạn và <b>quét mã QR</b> này để hoàn tất thanh toán</span>
                                    </p>
                                    <p class="qr-instructions-text">Gặp khó khăn khi thanh toán? Xem <a href="https://sepay.vn/huong-dan-thanh-toan" target="_blank">Hướng dẫn</a></p>
                                </div>

                                <div class="bank-info">
                                    <div class="bank-info_row">
                                        <span class="bank-info_label">Ngân hàng</span>
                                        <span class="bank-info_value">{{ paymentInfo?.bank?.toUpperCase?.() || 'MBBANK' }}</span>
                                    </div>
                                    <div class="bank-info_row">
                                        <span class="bank-info_label">Chủ tài khoản</span>
                                        <span class="bank-info_value">NGUYEN TIEN THANH</span>
                                    </div>
                                    <div class="bank-info_row">
                                        <span class="bank-info_label">Số tài khoản</span>
                                        <span class="bank-info_value">{{ paymentInfo?.account_number || '19919397979' }}</span>
                                    </div>
                                    <div class="bank-info_row">
                                        <span class="bank-info_label">Số tiền</span>
                                        <span class="bank-info_value">{{ $helper.formatCurrency(paymentInfo?.amount || totalPrice) }}</span>
                                    </div>
                                    <div class="bank-info_row">
                                        <span class="bank-info_label">Nội dung chuyển khoản</span>
                                        <span class="bank-info_value">{{ paymentInfo?.reference_code || 'BOOKING-XXXX' }}</span>
                                    </div>
                                    <span class="text-white text-small">Lưu ý: Vui lòng giữ nguyên nội dung <span class="font-medium">{{ paymentInfo?.reference_code || 'BOOKING-XXXX' }}</span> khi chuyển khoản để hệ thống tự xác nhận thanh toán</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Processing Status -->
                        <div v-if="isProcessing" class="payment-processing">
                            <div class="processing-spinner">
                                <div class="spinner"></div>
                            </div>
                            <div class="processing-text">Đang xử lý thanh toán...</div>
                        </div>

                        <!-- Real-time Payment Status -->
                        <div v-if="paymentStatus" :class="['payment-status', paymentStatus.type]">
                            <span class="status-icon">
                                <svg v-if="paymentStatus.type === 'success'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>
                                <svg v-else-if="paymentStatus.type === 'error'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            </span>
                            <span class="status-text">{{ paymentStatus.message }}</span>
                        </div>
                    </div>
                </div>

                <div class="action payment-action-row">
                    <button class="btn-md btn-secondary" @click="handleBack">Quay lại</button>
                    <button
                        class="btn-md btn-primary"
                        @click="handlePayment"
                        :disabled="isProcessing"
                    >
                        <span v-if="isProcessing">Đang xử lý...</span>
                        <span v-else>Hoàn tất thanh toán <i class="fas fa-arrow-right"></i></span>
                    </button>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="lesson-info-right" v-if="tutorInfo">
                <div class="info-card order-summary-card">
                    <div class="card-header">Thông tin đặt lịch</div>
                    <div class="card-content">
                        <div class="order-row">
                            <span class="order-label">Gia sư:</span>
                            <span class="order-value">{{ tutorInfo.full_name || tutorInfo.user_full_name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Môn học:</span>
                            <span class="order-value">{{ bookingData.subject.name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Cấp độ:</span>
                            <span class="order-value">{{ bookingData.level.name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Ngày học:</span>
                            <span class="order-value">{{ $helper.formatDate(bookingData.date) }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Thời gian:</span>
                            <span class="order-value">{{ bookingData.time.display }}</span>
                        </div>
                        <div class="order-row" v-if="bookingData.tutorSession">
                            <span class="order-label">Loại buổi học:</span>
                            <span class="order-value">{{ bookingData.tutorSession.name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Địa điểm:</span>
                            <span class="order-value">
                                <template v-if="bookingData.studyLocation">
                                    {{ bookingData.studyLocation.name || bookingData.studyLocation.label }}
                                </template>
                                <template v-else>
                                    Chưa cập nhật
                                </template>
                            </span>
                        </div>
                        <template v-if="bookingData.note">
                            <div class="order-row">
                                <span class="summary-label">Ghi chú:</span>
                                <span class="summary-value">{{ bookingData.note }}</span>
                            </div>
                        </template>
                        <template v-if="bookingData.isPackage">
                            <div class="order-row">
                                <span class="order-label">Gói học</span>
                                <span class="order-value">{{ bookingData.package.name }}</span>
                            </div>
                            <div class="order-row">
                                <span class="order-label">Số buổi học</span>
                                <span class="order-value">{{ bookingData.package.number_of_lessons }} buổi</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="order-row">
                                <span class="order-label">Học phí mỗi giờ</span>
                                <span class="order-value">{{ $helper.formatCurrency(hourlyPrice) }}</span>
                            </div>
                            <div class="order-row">
                                <span class="order-label">Thời lượng</span>
                                <span class="order-value">{{ $helper.formatDuration(lessonDurationHours) }}</span>
                            </div>
                        </template>
                        <div class="order-total">
                            <span class="order-label">Tổng cộng</span>
                            <span class="order-value total">{{ $helper.formatCurrency(totalPrice) }}</span>
                        </div>
                        <div class="order-benefit-box">
                            <ul class="order-benefits">
                                <li v-for="item in orderBenefits" :key="item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-check" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <path d="m9 11 3 3L22 4"></path>
                                    </svg>
                                    {{ item }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="info-card support-card">
                    <div class="card-content support-content">
                        <div class="support-row">
                            <span class="support-icon">
                                <svg class="icon-lg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="2" width="20" height="20" rx="5" />
                                    <path d="M8 10h.01" />
                                    <path d="M16 10h.01" />
                                    <path d="M12 16v.01" />
                                </svg>
                            </span>
                            <span class="support-title">Cần hỗ trợ?</span>
                        </div>
                        <div class="support-desc">Đội ngũ hỗ trợ của chúng tôi luôn sẵn sàng giúp đỡ bạn với mọi câu hỏi.</div>
                        <button class="btn-lg w-100 btn-secondary">Liên hệ hỗ trợ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile View -->
    <div class="user-info_mobile" v-if="tutorInfo">
        <div class="tutor-card">
            <div class="tutor-content-horizontal">
                <div class="tutor-avatar">
                    <img :src="tutorInfo.avatar || tutorInfo.user_avatar || '/images/default-avatar.png'" />
                </div>
                <div class="tutor-info-block">
                    <div class="tutor-name">{{ tutorInfo.full_name || tutorInfo.user_full_name }}</div>
                    <div class="tutor-rating">
                        <span class="star">
                            <svg class="icon-star" viewBox="0 0 24 24" fill="#facc15">
                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                            {{ tutorInfo.rating || tutorInfo.user_rating || '4.9' }}
                        </span>
                        <span class="review-count">({{ tutorInfo.review_count || tutorInfo.user_review_count || '127' }} đánh giá)</span>
                    </div>
                    <div class="tutor-location">
                        <svg class="icon-location" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        {{ tutorInfo.address || tutorInfo.user_address || 'Quận 1, TP.HCM' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="separation"></div>

        <div class="user-info_mobile_action">
            <button class="btn-md border-r-2 btn-secondary" @click="handleBack">
                <i class="fas fa-arrow-left"></i> Quay lại
            </button>
            <button
                class="btn-md border-r-2 btn-primary"
                @click="handlePayment"
                :disabled="isProcessing"
            >
                <span v-if="isProcessing">Đang xử lý...</span>
                <span v-else>Thanh toán <i class="fas fa-arrow-right"></i></span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { defineEmits, ref, onMounted, computed, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import moment from 'moment'
import { useStore } from 'vuex'
import { getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();
const router = useRouter()
const store = useStore()
const myUid = computed(() => store.state?.userData?.uid);

const props = defineProps({
    tutorInfo: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['submit', 'back'])
const handleBack = () => emit('back');

const isProcessing = ref(false);
const paymentStatus = ref(null);
const paymentInfo = ref(null);
const qrUrl = ref('');
let echoChannel = null;

const bookingData = ref({
    subject: {
        id: '',
        name: ''
    },
    level: {
        id: '',
        name: ''
    },
    date: '',
    time: {
        start: '',
        end: '',
        display: ''
    },
    package: null,
    hourly_price: 0,
    total_price: 0,
    isPackage: false,
    studyLocation: null,
    note: ''
});

const remainingTime = ref(900);
let timerInterval = null;

const orderBenefits = [
    "Thanh toán an toàn và bảo mật",
    "Đảm bảo hoàn tiền nếu không hài lòng",
    "Hỗ trợ 24/7 từ đội ngũ EduTutor"
];

const getBookingData = () => {
    const storedData = sessionStorage.getItem('bookingData');
    const data = JSON.parse(storedData);

    if (validateBookingData(data)) {
        bookingData.value = {
            ...data,
            hourly_price: hourlyPrice.value,
            total_price: totalPrice.value
        };
    } else {
        handleBack();
    }
};

const getBookingPaymentInfo = () => {
    const storedPayment = sessionStorage.getItem('bookingPaymentInfo');
    if (storedPayment) {
        const parsed = JSON.parse(storedPayment);
        paymentInfo.value = parsed;
        qrUrl.value = parsed.qr_url;
        if (parsed.expired_at) {
            const diff = moment(parsed.expired_at).diff(moment(), 'seconds');
            remainingTime.value = Math.max(0, diff);
        } else if (parsed.remaining_seconds != null) {
            remainingTime.value = parsed.remaining_seconds;
        }
    }
}

const initPayments = () => {
    if (window.Echo && myUid.value) {
        echoChannel = window.Echo.private(`payments.${myUid.value}`)
        .listen('.payment.status.updated', (payload) => {
            console.log('thanhf')
            const matchBooking = paymentInfo.value?.booking_id ? payload.booking_id === paymentInfo.value.booking_id : true;
            if (!matchBooking) return;

            if (payload.status === 'success') {
                paymentStatus.value = { type: 'success', message: 'Thanh toán thành công. Cảm ơn bạn!' };
                stopTimer();
                setTimeout(() => {
                    sessionStorage.removeItem('bookingPaymentInfo');
                    sessionStorage.removeItem('bookingData');

                    const id = payload.booking_id || paymentInfo.value?.booking_id;

                    router.push({
                        name: 'booking-success',
                        params: { id: id },
                    })
                }, 1200);
            } else if (payload.status === 'non_payment') {
                paymentStatus.value = { type: 'error', message: 'Đơn hàng đã hết hạn. Vui lòng tạo đơn hàng mới.' };
                stopTimer();
                setTimeout(() => {
                    sessionStorage.removeItem('bookingPaymentInfo');
                    emit('back');
                }, 1500);
            }
        });
    }
}

const hourlyPrice = computed(() => {
    if (!bookingData.value.subject.id || !bookingData.value.level.id) {
        return 0;
    }
    const selectedSubject = props.tutorInfo.user_subjects.find(
        s => s.subject_id === bookingData.value.subject.id
    );
    if (!selectedSubject) return 0;

    const selectedLevel = selectedSubject.user_subject_levels.find(
        lvl => lvl.education_level_id === bookingData.value.level.id
    );
    return selectedLevel ? selectedLevel.price : 0;
});

// Duration comes from Tutor Session chosen in previous step
const lessonDurationHours = computed(() => {
    const hours = bookingData.value.tutorSession?.duration_hours;
    const num = parseFloat(hours);
    return Number.isFinite(num) ? num : 0;
});

const totalPrice = computed(() => {
    if (bookingData.value.isPackage && bookingData.value.package) {
        return bookingData.value.package.price;
    }
    return hourlyPrice.value * lessonDurationHours.value;
});

// Format time for countdown timer
const formatTime = (seconds) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
};

const startTimer = () => {
    timerInterval = setInterval(() => {
        if (paymentInfo.value?.expired_at) {
            const diff = moment(paymentInfo.value.expired_at).diff(moment(), 'seconds');
            remainingTime.value = Math.max(0, diff);
        } else if (remainingTime.value > 0) {
            remainingTime.value--;
        }
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
};

const submitSepayPayment = async () => {
    try {
        isProcessing.value = true;
        paymentStatus.value = {
            type: 'processing',
            message: 'Đang tạo mã QR thanh toán...'
        };

        const payload = {
            amount: totalPrice.value,
            orderInfo: `thanhtoanbooking`,
            locale: 'vn',
            uid: props.tutorInfo.uid,
            subject_id: parseInt(bookingData.value.subject.id),
            education_level_id: parseInt(bookingData.value.level.id),
            date: bookingData.value.date,
            start_time_id: parseInt(bookingData.value.time.start),
            end_time_id: parseInt(bookingData.value.time.end),
            note: bookingData.value.note || '',
            hourly_rate: hourlyPrice.value,
            duration: lessonDurationHours.value,
            package_id: bookingData.value.isPackage ? bookingData.value.package.id : null,
            num_sessions: bookingData.value.isPackage ? bookingData.value.package.number_of_lessons : null,
            total_price: totalPrice.value,
            is_package: bookingData.value.isPackage ?? null,
            study_location_id: bookingData.value.studyLocation ? bookingData.value.studyLocation.id : null,
            payment_method: 1,
            payment_method_code: 'sepay'
        };

        // Simulate sePay payment processing
        await new Promise(resolve => setTimeout(resolve, 2000));

        paymentStatus.value = {
            type: 'success',
            message: 'Mã QR đã được tạo. Vui lòng quét mã để thanh toán.'
        };

        // Start countdown timer
        startTimer();

    } catch (error) {
        paymentStatus.value = {
            type: 'error',
            message: 'Không thể tạo mã QR thanh toán. Vui lòng thử lại.'
        };
        proxy.$notification.error('Có lỗi khi tạo thanh toán sePay');
    } finally {
        isProcessing.value = false;
    }
};

const handlePayment = () => {
    submitSepayPayment();
};

const validateBookingData = (bookingData) => {
    if (!bookingData) {
        return false;
    }

    if (!bookingData.subject?.id || !bookingData.subject?.name) {
        return false;
    }

    if (!bookingData.level?.id || !bookingData.level?.name) {
        return false;
    }

    if (!bookingData.date || bookingData.date.trim() === '') {
        return false;
    }

    if (!bookingData.time?.start || !bookingData.time?.end || !bookingData.time?.display) {
        return false;
    }

    if (bookingData.hourly_price < 0 || bookingData.total_price < 0) {
        return false;
    }

    return true;
}

onMounted(() => {
    getBookingData();
    getBookingPaymentInfo();
    startTimer();
    initPayments();
});

onUnmounted(() => {
    stopTimer();
    if (echoChannel && window.Echo) {
        try {
            window.Echo.leave(`private-payments.${myUid.value}`);
        } catch (e) {}
    }
});
</script>

<style scoped>
@import url("@css/BookingCommon.css");

.lesson-info-left .card-header {
    background: var(--color-primary);
    color: white;
}

.booking-summary-section {
    margin-bottom: 2rem;
}

.summary-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #18181b;
    margin-bottom: 1rem;
}

.summary-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.2rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-label {
    font-weight: 500;
    color: #4b5563;
    font-size: 0.95rem;
}

.summary-value {
    color: #18181b;
    font-weight: 400;
    text-align: right;
    font-size: 0.95rem;
}

/* sePay Payment Interface */
.sepay-payment-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
}

.sepay-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.sepay-logo {
    display: flex;
    align-items: center;
    justify-content: center;
}

.sepay-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2563eb;
}

/* Order Expiration Timer */
.order-expiration {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.expiration-icon {
    color: #f59e0b;
    flex-shrink: 0;
}

.expiration-timer-container {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.expiration-label {
    font-size: 0.9rem;
    font-weight: 500;
}

.expiration-timer {
    font-size: 0.9rem;
    font-weight: 700;
}

/* QR Payment Section */
.bank-content {
    background: var(--color-primary);
}
.bank-content-container {
    display: flex;
    gap: 1rem;
    padding: 1rem 0;
}
.qr-payment-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.bank-info {
    flex: 1;
    margin-top: 1rem;
}
.bank-info .bank-info_row {
    display: flex;
    justify-content: space-between;
    font-size: 1rem;
    margin-bottom: 0.7rem;
    color: white;
    font-size: var(--font-size-base);
}

.qr-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}

.qr-content{
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    flex: 1;
}

.qr-content h4 {
    font-size: var(--font-size-large);
    color: white;
}

.qr-code {
    width: fit-content;
    height: 100%;
    background: white;
    border-radius: 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 0 0 0;
}

.qr-code img {
    width: 400px;
    height: 400px;
}

.qr-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.qr-instructions {
    text-align: center;
}

.qr-instructions h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.5rem;
}

.qr-instructions p {
    font-size: var(--font-size-small);
    color: white;
    margin: 0;
}

.qr-info-wrapper {
    padding: 1rem 1.5rem;
    margin-top: 1rem;
    border-top: 2px dashed var(--gray-300);
    display: grid;
    gap: 0.5rem;
    width: 100%;
    position: relative;
}

.qr-info-wrapper .round-button:first-child {
    background: var(--color-primary);
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 100%;
    position: absolute;
    top: -26%;
    left: -3%;
}

.qr-info-wrapper .round-button:last-child {
    background: var(--color-primary);
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 100%;
    position: absolute;
    top: -26%;
    right: -3%;
}


.qr-info {
    display: flex;
    justify-content: space-between;
}

.qr-info_label {
    font-size: var(--font-size-base);
}

.qr-info_value {
    font-size: var(--font-size-heading-6);
    color: var(--color-primary);
    font-weight: 600;
}

.payment-amount {
    padding: 1rem 2rem;
    background: white;
    border: 2px solid #2563eb;
    border-radius: 8px;
    text-align: center;
}

.amount-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
}

.amount-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2563eb;
}

/* Supported Banks */
.supported-banks {
    margin-top: 1.5rem;
}

.banks-title {
    font-size: 1rem;
    font-weight: 600;
    color: #18181b;
    margin-bottom: 1rem;
}

.banks-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.bank-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.bank-item:hover {
    border-color: #2563eb;
    background: #f8fafc;
}

.bank-logo {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.bank-name {
    font-size: 0.8rem;
    color: #6b7280;
    text-align: center;
    font-weight: 500;
}

/* Payment Status */
.payment-status {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.payment-status.success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.payment-status.error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
}

.payment-status.processing {
    background: #eff6ff;
    border: 1px solid #93c5fd;
    color: #1d4ed8;
}

.status-icon {
    flex-shrink: 0;
}

.status-text {
    font-weight: 500;
}

/* Payment Processing */
.payment-processing {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 2rem;
    background: #f8fafc;
    border-radius: 8px;
    margin-top: 1rem;
}

.processing-spinner {
    width: 40px;
    height: 40px;
}

.spinner {
    width: 100%;
    height: 100%;
    border: 3px solid #e5e7eb;
    border-top: 3px solid #2563eb;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.processing-text {
    font-size: 1rem;
    color: #6b7280;
    font-weight: 500;
}

@media (max-width: 1024px) {
    .banks-grid {
        grid-template-columns: repeat(3, 1fr);
    }

    .qr-code img {
        width: 280px;
        height: 280px;
    }
}

@media (max-width: 768px) {
    .banks-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .order-expiration {
        flex-direction: column;
        text-align: center;
    }
}
</style>

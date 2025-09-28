<template>
<div class="container">
    <div class="lesson-info-layout booking-content">
        <!-- Left Column: Xác nhận thông tin -->
        <div class="lesson-info-left">
            <div class="info-card confirm-main-card">
                <div class="confirm-header card-header">Xác nhận thông tin đặt lịch</div>
                <div class="card-content">
                    <div class="confirm-main-row">
                        <div class="confirm-card confirm-card-grid">
                            <div class="confirm-card-title">Thông tin buổi học</div>
                            <div class="confirm-info-grid">
                                <div class="confirm-label">Môn học:</div>
                                <div class="confirm-value">{{ bookingData.subject.name }}</div>
                                <div class="confirm-label">Cấp độ:</div>
                                <div class="confirm-value">{{ bookingData.level.name }}</div>
                                <div class="confirm-label">Ngày học:</div>
                                <div class="confirm-value">{{ $helper.formatDate(bookingData.date) }}</div>
                                <div class="confirm-label">Thời gian:</div>
                                <div class="confirm-value">{{ bookingData.time.display }}</div>
                                <template v-if="bookingData.tutorSession">
                                    <div class="confirm-label">Loại buổi học:</div>
                                    <div class="confirm-value">{{ bookingData.tutorSession.name }}</div>
                                </template>
                                <div class="confirm-label">Địa điểm:</div>
                                <div class="confirm-value">
                                    <template v-if="bookingData.studyLocation">
                                        {{ bookingData.studyLocation.name || bookingData.studyLocation.label }}
                                    </template>
                                    <template v-else>
                                        Chưa cập nhật
                                    </template>
                                </div>
                                <template v-if="bookingData.note">
                                    <div class="confirm-label">Ghi chú:</div>
                                    <div class="confirm-value">{{ bookingData.note }}</div>
                                </template>
                            </div>
                        </div>
                        <div class="confirm-card confirm-card-grid">
                            <div class="confirm-card-title">Hình thức thanh toán</div>
                            <div class="confirm-info-grid">
                                <div class="confirm-label">Hình thức:</div>
                                <div class="confirm-value">{{ bookingData.isPackage?'Đặt theo gói' : 'Tính theo giờ' }}</div>
                                <template v-if="bookingData.isPackage">
                                    <div class="confirm-label">Gói học:</div>
                                    <div class="confirm-value">{{ bookingData.package.name }}</div>
                                    <div class="confirm-label">Số buổi học:</div>
                                    <div class="confirm-value">{{ bookingData.package.number_of_lessons }} buổi</div>
                                </template>
                                <template v-else>
                                    <div class="confirm-label">Thời lượng:</div>
                                    <div class="confirm-value">{{ $helper.formatDuration(lessonDurationHours) }}</div>
                                    <div class="confirm-label">Học phí mỗi giờ:</div>
                                    <div class="confirm-value">{{ $helper.formatCurrency(hourlyPrice) }}</div>
                                </template>
                                <div class="confirm-label"><b>Tổng học phí:</b></div>
                                <div class="confirm-value"><b>{{ $helper.formatCurrency(totalPrice) }}</b></div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="payment-method-section">
                        <div class="payment-method-title">Chọn phương thức thanh toán</div>
                        <div class="payment-methods-grid">
                            <div v-for="method in paymentMethods" :key="method.id" class="payment-method-item" :class="{ active: selectedPaymentMethod?.code === method.code }" @click="selectPaymentMethod(method)">
                                <div class="payment-method-icon">
                                    <img :src="method.icon" :alt="method.name" />
                                </div>
                                <div class="payment-method-info">
                                    <div class="payment-method-name">{{ method.name }}</div>
                                    <div class="payment-method-desc">{{ method.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="confirm-alert">
                        <svg width="22" height="22" fill="none" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <path d="M12 16v-4" />
                            <path d="M12 8h.01" />
                        </svg>
                        <span>Sau khi gửi yêu cầu đặt lịch, gia sư sẽ xem xét và phản hồi trong vòng 24 giờ. Bạn sẽ nhận được thông báo qua email khi gia sư chấp nhận yêu cầu.</span>
                    </div>
                </div>
            </div>
            <div class="action confirm-action-row">
                <button class="btn-md btn-secondary" @click="handleBack">Quay lại</button>
                <button class="btn-md btn-primary" @click="handleSubmit" :disabled="isSubmitting || isSelfBooking || !selectedPaymentMethod">
                    <span v-if="isSubmitting">Đang xử lý...</span>
                    <span v-else>Thanh toán và đặt lịch <i class="fas fa-arrow-right"></i></span>
                </button>
                <div v-if="isSelfBooking" class="alert alert-danger mt-2">Bạn không thể đặt lịch với chính mình!</div>
            </div>
        </div>
        <!-- Right Column giữ nguyên như các bước trước -->
        <div class="lesson-info-right" v-if="tutorInfo">
            <div class="info-card tutor-card">
                <div class="card-header">Thông tin gia sư</div>
                <div class="card-content tutor-content-horizontal">
                    <div class="tutor-avatar">
                        <img :src="tutorInfo.avatar || tutorInfo.user_avatar || '/images/default-avatar.png'" />
                    </div>
                    <div class="tutor-info-block">
                        <div class="tutor-name">{{ tutorInfo.full_name || tutorInfo.user_full_name }}</div>
                        <div class="tutor-rating">
                            <span class="star"><svg class="icon-star" viewBox="0 0 24 24" fill="#facc15">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg> {{ tutorInfo.rating || tutorInfo.user_rating || '4.9' }}</span>
                            <span class="review-count">({{ tutorInfo.review_count || tutorInfo.user_review_count || '127' }} đánh giá)</span>
                        </div>
                        <div class="tutor-location"><svg class="icon-location" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z" />
                                <circle cx="12" cy="10" r="3" /></svg> {{ tutorInfo.address || tutorInfo.user_address || 'Quận 1, TP.HCM' }}</div>
                    </div>
                </div>
            </div>
            <div class="info-card order-summary-card">
                <div class="card-header">Tóm tắt đơn hàng</div>
                <div class="card-content">
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
                    <div class="order-row" v-if="selectedPaymentMethodInfo?.fee">
                        <span class="order-label">Phí thanh toán</span>
                        <span class="order-value">{{ selectedPaymentMethodInfo.fee }}</span>
                    </div>
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
                                <path d="M12 16v.01" /></svg>
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

<div class="user-info_mobile" v-if="tutorInfo">
    <div class="tutor-card">
        <div class="tutor-content-horizontal">
            <div class="tutor-avatar">
                <img :src="tutorInfo.avatar || tutorInfo.user_avatar || '/images/default-avatar.png'" />
            </div>
            <div class="tutor-info-block">
                <div class="tutor-name">{{ tutorInfo.full_name || tutorInfo.user_full_name }}</div>
                <div class="tutor-rating">
                    <span class="star"><svg class="icon-star" viewBox="0 0 24 24" fill="#facc15">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" /></svg> {{ tutorInfo.rating || tutorInfo.user_rating || '4.9' }}</span>
                    <span class="review-count">({{ tutorInfo.review_count || tutorInfo.user_review_count || '127' }} đánh giá)</span>
                </div>
                <div class="tutor-location"><svg class="icon-location" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z" />
                        <circle cx="12" cy="10" r="3" /></svg> {{ tutorInfo.address || tutorInfo.user_address || 'Quận 1, TP.HCM' }}</div>
            </div>
        </div>
    </div>

    <div class="separation"></div>

    <div class="user-info_mobile_action">
        <button class="btn-md border-r-2 btn-secondary" @click="handleBack"><i class="fas fa-arrow-left"></i> Quay lại</button>
        <button class="btn-md border-r-2 btn-primary" @click="handleSubmit" :disabled="isSubmitting || !selectedPaymentMethod">
            <span v-if="isSubmitting">Đang xử lý...</span>
            <span v-else>Hoàn tất đặt lịch <i class="fas fa-arrow-right"></i></span>
        </button>
    </div>
</div>
</template>


<script setup>
import {
    defineEmits,
    ref,
    onMounted,
    computed,
    getCurrentInstance
} from 'vue'
import {
    useRouter
} from 'vue-router'
import {
    useStore
} from 'vuex'
import moment from 'moment'
import payApi from '../../api/pay'

// ============================
// Core setup
// ============================
const { proxy } = getCurrentInstance()
const router = useRouter()
const store = useStore()
const myUid = computed(() => store.state.auth?.user?.uid)
const isSelfBooking = computed(() => props.tutorInfo?.uid === myUid.value)

const props = defineProps({
    tutorInfo: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['submit', 'back', 'next'])
const handleBack = () => emit('back')

// ============================
// State chính
// ============================
const STORAGE_KEY = 'bookingData'
const isSubmitting = ref(false)
const selectedPaymentMethod = ref({})
const paymentMethods = ref(store.state.configuration.paymentMethods)

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
    isPackage: false
})

const orderBenefits = [
    "Thanh toán trực tiếp cho gia sư tại nơi học",
    "Đảm bảo hoàn tiền nếu không hài lòng",
    "Hỗ trợ 24/7 từ đội ngũ EduTutor"
]

// ============================
// Computed properties
// ============================
const selectedSubject = computed(() =>
    props.tutorInfo?.user_subjects?.find(s => s.subject_id === bookingData.value.subject.id)
)

const selectedLevel = computed(() =>
    selectedSubject.value?.user_subject_levels?.find(
        lvl => lvl.education_level_id === bookingData.value.level.id
    )
)

const timeOptions = computed(() =>
    proxy.$helper.handleTimeSlot(store.state.configuration.timeSlots || [], bookingData.value.date)
)

const hourlyPrice = computed(() => selectedLevel.value?.price || 0)

// Duration comes from selected Tutor Session
const lessonDurationHours = computed(() => {
    const hours = bookingData.value.tutorSession?.duration_hours
    const num = parseFloat(hours)
    return Number.isFinite(num) ? num : 0
})

const totalPrice = computed(() => {
    if (bookingData.value.isPackage && bookingData.value.package) {
        return bookingData.value.package.price
    }
    return hourlyPrice.value * lessonDurationHours.value
})

const selectedPaymentMethodInfo = computed(() =>
    paymentMethods.value.find(method => method.code === selectedPaymentMethod.value)
)

// ============================
// Utility functions
// ============================
const loadBookingData = () => {
    const raw = sessionStorage.getItem(STORAGE_KEY)
    return raw ? JSON.parse(raw) : null
}

const validateBookingData = (data) => {
    if (!data) return false

    const hasRequiredFields = !!(
        data.subject?.id && data.subject?.name &&
        data.level?.id && data.level?.name &&
        data.date && data.time?.start && data.time?.end &&
        data.hourly_price >= 0 && data.total_price >= 0
    )

    if (!hasRequiredFields) return false

    // Check if selected time slot is not in the past
    const now = new Date()
    const currentTime = moment(now).format('HH:mm')
    const currentDate = moment(now).format('YYYY-MM-DD')
    const isToday = data.date === currentDate

    if (isToday && data.time?.start) {
        const startTimeObj = timeOptions.value.find(t => t.id === data.time.start)
        if (startTimeObj && startTimeObj.time) {
            const slotTime = moment(startTimeObj.time, 'HH:mm').format('HH:mm')
            if (slotTime <= currentTime) {
                proxy.$notification?.error?.('Thời gian đã chọn đã qua, vui lòng chọn thời gian khác')
                return false
            }
        }
    }

    return true
}

// ============================
// Handlers
// ============================
const selectPaymentMethod = (method) => {
    selectedPaymentMethod.value = method
}

const submitSepayPayment = async () => {
    try {
        isSubmitting.value = true
        const payload = {
            amount: totalPrice.value,
            orderInfo: `thanhtoanbooking`,
            locale: 'vn',
            uid: props.tutorInfo.uid,
            subject_id: parseInt(bookingData.value.subject.id),
            education_level_id: parseInt(bookingData.value.level.id),
            date: bookingData.value.date,
            time_slot_id: parseInt(bookingData.value.time.start),
            tutor_session_id: parseInt(bookingData.value.tutorSession?.id || activeTutorSessionId.value),
            note: bookingData.value.note || '',
            hourly_rate: hourlyPrice.value,
            duration: lessonDurationHours.value,
            package_id: bookingData.value.isPackage ? bookingData.value.package.id : null,
            num_sessions: bookingData.value.isPackage ? bookingData.value.package.number_of_lessons : null,
            total_price: totalPrice.value,
            is_package: bookingData.value.isPackage ?? null,
            study_location_id: bookingData.value.studyLocation ? bookingData.value.studyLocation.value : null,
            payment_method: selectedPaymentMethod.value?.id,
            payment_method_code: selectedPaymentMethod.value?.code,
        }

        const res = await payApi.createSepayBooking(payload)
        if (res?.success) {
            const paymentData = res.data
            sessionStorage.setItem('bookingPaymentInfo', JSON.stringify(paymentData))
            emit('next')
        } else {
            proxy.$notification?.error?.(res?.message || 'Tạo thanh toán thất bại')
        }
    } catch (e) {
        proxy.$notification?.error?.('Có lỗi khi tạo thanh toán sePay')
    } finally {
        isSubmitting.value = false
    }
}

const paymentStrategies = {
    sepay: submitSepayPayment
}

const handleSubmit = () => {
    if (!selectedPaymentMethod.value) {
        proxy.$notification?.error?.('Vui lòng chọn phương thức thanh toán')
        return
    }

    const method = selectedPaymentMethod.value.code
    const fn = paymentStrategies[method] || submitSepayPayment
    fn()
}

// ============================
// Lifecycle
// ============================
onMounted(() => {
    const data = loadBookingData()

    if (validateBookingData(data)) {
        bookingData.value = {
            ...data,
            hourly_price: hourlyPrice.value,
            total_price: totalPrice.value
        }
    } else {
        handleBack()
    }

    // Set default payment method
    selectedPaymentMethod.value = { code: 'cash' }
})
</script>


<style scoped>
@import url("@css/BookingCommon.css");

.confirm-main-row {
    display: flex;
    gap: 2rem;
    margin-bottom: 1.2rem;
}

.confirm-card {
    width: 100%;
}

.confirm-card-title {
    font-size: 1.08rem;
    font-weight: 500;
    color: #18181b;
    margin-bottom: 0.7rem;
}

.confirm-info-grid {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 0.7rem 1.2rem;
    font-size: 0.98rem;
    background: #f9fafb;
    padding: 1rem;
    border-radius: 5px;
}

.confirm-label {
    color: #4b5563;
    font-weight: 500;
}

.confirm-value {
    color: #18181b;
    font-weight: 400;
    text-align: right;
}

/* Payment Method Styles */
.payment-method-section {
    margin: 1.5rem 0;
}

.payment-method-title {
    font-size: 1.08rem;
    font-weight: 500;
    color: #18181b;
    margin-bottom: 1rem;
}

.payment-methods-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.8rem;
}

.payment-method-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
}

.payment-method-item:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.payment-method-item.active {
    border-color: #18181b;
    background: #f8fafc;
}

.payment-method-radio {
    display: flex;
    align-items: center;
    justify-content: center;
}

.radio-circle {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    transition: all 0.2s ease;
}

.radio-circle.checked {
    border-color: #18181b;
}

.radio-circle.checked::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: #18181b;
    border-radius: 50%;
}

.payment-method-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.payment-method-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.payment-method-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.payment-method-name {
    font-size: 1rem;
    font-weight: 500;
    color: #18181b;
}

.payment-method-desc {
    font-size: 0.9rem;
    color: #6b7280;
}

.payment-method-fee {
    display: flex;
    align-items: center;
}

.fee-text {
    font-size: 0.9rem;
    color: #059669;
    font-weight: 500;
}

.confirm-alert {
    background: #fefce8;
    border: 1.5px solid #fde68a;
    border-radius: 8px;
    color: #a16207;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 1.1rem 1.3rem;
    margin: 1.2rem 0 0 0;
    font-size: var(--font-size-small);
}

.confirm-alert svg {
    flex-shrink: 0;
    margin-top: 0.1em;
}

@media (max-width: 1024px) {
    .confirm-main-row {
        flex-direction: column;
        gap: 1.2rem;
    }

    .card-content {
        padding: 1.4rem;
    }

    .lesson-info-right .order-summary-card {
        display: none;
    }

    .payment-method-item {
        padding: 0.8rem;
    }

    .payment-method-icon {
        width: 32px;
        height: 32px;
    }
}
</style>

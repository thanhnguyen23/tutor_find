<template>
<div class="container">
    <div class="lesson-info-layout booking-content">
        <!-- Left Column: Chọn hình thức học -->
        <div class="lesson-info-left">
            <!-- Thông tin buổi học -->
            <div class="info-card lesson-card">
                <div class="card-header">Thông tin buổi học</div>
                <div class="card-content">
                    <div class="lesson-details-grid">
                        <div class="lesson-detail-item">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                    <path d="M22 10v6"></path>
                                    <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                </svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Môn học</span>
                                <span class="sub-title">{{ bookingData.subject.name }}</span>
                            </div>
                        </div>
                        <div class="lesson-detail-item">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Ngày học</span>
                                <span class="sub-title">{{ $helper.formatDate(bookingData.date) }}</span>
                            </div>
                        </div>
                        <div class="lesson-detail-item">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <path data-v-6304ec4f="" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path data-v-6304ec4f="" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Cấp độ</span>
                                <span class="sub-title">{{ bookingData.level.name }}</span>
                            </div>
                        </div>
                        <div class="lesson-detail-item">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Thời gian</span>
                                <span class="sub-title">{{ bookingData.time.display }}</span>
                            </div>
                        </div>
                        <div class="lesson-detail-item" v-if="bookingData.tutorSession">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/></svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Loại buổi học</span>
                                <span class="sub-title">{{ bookingData.tutorSession?.name }}</span>
                            </div>
                        </div>

                        <div class="lesson-detail-item">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0Z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Địa điểm</span>
                                <span class="sub-title">
                                    <template v-if="bookingData.studyLocation">
                                        {{ bookingData.studyLocation.name || bookingData.studyLocation.label }}
                                    </template>
                                    <template v-else>
                                        Chưa cập nhật
                                    </template>
                                </span>
                            </div>
                        </div>
                        <div class="lesson-detail-item" v-if="bookingData.note">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Ghi chú</span>
                                <span class="sub-title">{{ bookingData.note }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="info-card method-card">
                <div class="card-header method-header custom-method-header">
                    <span class="custom-method-title">Chọn hình thức học</span>
                    <label class="custom-switch">
                        <span class="custom-switch-label">Đặt theo gói</span>
                        <input type="checkbox" v-model="isPackage" :disabled="availablePackages.length === 0" />
                        <span class="custom-slider"></span>
                    </label>
                </div>
                <div class="card-content method-content">
                    <div class="method-tabs">
                        <button class="method-tab" :class="{active: !isPackage}" @click="isPackage = false; handlePackageSelect(null)">Tính theo giờ</button>
                        <button
                        class="method-tab"
                        :class="{ active: isPackage, disable: availablePackages.length === 0 }"
                        :disabled="availablePackages.length === 0"
                        @click="isPackage = true"
                        >
                        Đặt theo gói
                        </button>
                    </div>
                    <div v-if="!isPackage">
                        <div class="fee-card custom-fee-card">
                            <div class="fee-header custom-fee-header">
                                <div class="fee-icon">
                                    <svg class="icon-xl" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                </div>
                                <div>
                                    <div class="fee-title custom-fee-title">Học phí theo giờ</div>
                                    <div class="fee-desc custom-fee-desc">Thanh toán dựa trên số giờ học thực tế</div>
                                </div>
                            </div>
                            <div class="fee-details-grid">
                                <div class="fee-label">Học phí mỗi giờ:</div>
                                <div class="fee-value">{{ $helper.formatCurrency(hourlyPrice) }}</div>
                                <div class="fee-label">Thời lượng:</div>
                                <div class="fee-value">{{ $helper.formatDuration(lessonDurationHours) }}</div>
                            </div>
                            <div class="fee-divider"></div>
                            <div class="fee-details-grid fee-total-row">
                                <div class="fee-label fee-total-label">Tổng học phí:</div>
                                <div class="fee-value fee-total-value">{{ $helper.formatCurrency(totalPrice) }}</div>
                            </div>
                            <div class="fee-alert custom-fee-alert" v-if="availablePackages.length > 0">
                                <svg width="20" height="20" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                <span>Bạn có thể tiết kiệm chi phí bằng cách đăng ký các gói học với nhiều ưu đãi hơn.</span>
                            </div>
                        </div>
                    </div>
                    <div v-else>
                        <div class="package-cards custom-package-cards">
                            <div v-for="(pkg, idx) in availablePackages" :key="pkg.id" :class="['custom-package-card', { 'selected': selected === idx, 'popular': idx === 0 }]">
                                <div v-if="idx === 1" class="popular-label">Phổ biến nhất</div>
                                <div class="custom-package-title">{{ pkg.name }}</div>
                                <div class="custom-package-sub">{{ pkg.number_of_lessons }} buổi học</div>
                                <div class="custom-package-price">{{ $helper.formatCurrency(pkg.price) }}</div>
                                <div class="custom-package-price-hour">{{ $helper.formatCurrency(pkg.price / pkg.number_of_lessons) }}/buổi</div>
                                <ul class="custom-package-features">
                                    <li v-for="(feature, i) in pkg.features" :key="i">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                                        <span>{{ feature.feature }}</span>
                                    </li>
                                </ul>
                                <button :class="['btn-md w-100', { 'btn-primary': selected === idx, 'btn-secondary': selected !== idx }]" @click="handlePackageSelect(idx)">
                                    {{ selected === idx ? 'Đã chọn' : 'Chọn gói này' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="action">
                <button class="btn-md btn-secondary" @click="handleBack">Quay lại</button>
                <button class="btn-md btn-primary" @click="handleNext" :disabled="isSelfBooking">Tiếp tục <i class="fas fa-arrow-right"></i></button>
                <div v-if="isSelfBooking" class="alert alert-danger mt-2">Bạn không thể đặt lịch với chính mình!</div>
            </div>
        </div>

        <!-- Right Column: Thông tin gia sư, tóm tắt đơn hàng, hỗ trợ -->
        <div class="lesson-info-right">
            <!-- Thông tin gia sư -->
            <div class="info-card tutor-card">
                <div class="card-header">Thông tin gia sư</div>
                <div class="card-content tutor-content-horizontal">
                    <div class="tutor-avatar">
                        <img :src="tutorInfo.avatar || '/images/default-avatar.png'" />
                    </div>
                    <div class="tutor-info-block">
                    <div class="tutor-name">{{ tutorInfo.full_name }}</div>
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
            <!-- Tóm tắt đơn hàng -->
            <div class="info-card order-summary-card">
                <div class="card-header">Tóm tắt đơn hàng</div>
                <div class="card-content">
                    <div class="order-row">
                        <span class="order-label">Môn học</span>
                        <span class="order-value">{{ bookingData.subject.name }}</span>
                    </div>
                    <div class="order-row">
                        <span class="order-label">Cấp độ</span>
                        <span class="order-value">{{ bookingData.level.name }}</span>
                    </div>
                    <div class="order-row">
                        <span class="order-label">Ngày học</span>
                        <span class="order-value">{{ $helper.formatDate(bookingData.date) }}</span>
                    </div>
                    <div class="order-row">
                        <span class="order-label">Thời gian</span>
                        <span class="order-value">{{ bookingData.time.display }}</span>
                    </div>
                    <!-- <div class="order-row">
                        <span class="order-label">Địa điểm</span>
                        <span class="order-value">{{ bookingData.location || 'Chưa cập nhật' }}</span>
                    </div> -->
                    <template v-if="!isPackage || selected === null">
                        <div class="order-row">
                            <span class="order-label">Học phí mỗi giờ</span>
                            <span class="order-value">{{ $helper.formatCurrency(hourlyPrice) }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Thời lượng</span>
                            <span class="order-value">{{ $helper.formatDuration(lessonDurationHours) }}</span>
                        </div>
                    </template>
                    <template v-else>
                        <div class="order-row">
                            <span class="order-label">Gói học</span>
                            <span class="order-value">{{ availablePackages[selected]?.name }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Số buổi học</span>
                            <span class="order-value">{{ totalLessons }} buổi</span>
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
            <!-- Cần hỗ trợ? -->
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
        <button class="btn-md border-r-2 btn-primary" @click="handleNext">Tiếp tục <i class="fas fa-arrow-right"></i></button>
    </div>
</div>
</template>

<script setup>
import { ref, computed, onMounted, watch, getCurrentInstance } from 'vue'
import { useStore } from 'vuex'
import moment from 'moment'

// ============================
// Core setup
// ============================
const { proxy } = getCurrentInstance()
const store = useStore()

const props = defineProps({
    tutorInfo: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['next', 'back'])
const handleNext = () => emit('next')
const handleBack = () => emit('back')

// ============================
// State chính
// ============================
const STORAGE_KEY = 'bookingData'
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
    location: '',
    package: null,
    hourly_price: 0,
    total_price: 0,
    total_hourly_price: 0,
    tutorSession: null
})

const orderBenefits = [
    "Đảm bảo hoàn tiền nếu không hài lòng",
    "Hỗ trợ 24/7 từ đội ngũ EduTutor"
]

const isPackage = ref(false)
const selected = ref(null)
const myUid = computed(() => store.state.auth?.user?.uid || localStorage.getItem('uid'))
const isSelfBooking = computed(() => props.tutorInfo?.uid === myUid.value)

// ============================
// Computed properties
// ============================
const availablePackages = computed(() => {
    if (!bookingData.value.subject.id || !bookingData.value.level.id) {
        return []
    }
    return props.tutorInfo.user_packages.filter(
        item => item.subject_id === bookingData.value.subject.id &&
        item.level_id === bookingData.value.level.id
    )
})

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

// Duration now comes from selected Tutor Session instead of start/end times
const lessonDurationHours = computed(() => {
    const hours = bookingData.value.tutorSession?.duration_hours
    const num = parseFloat(hours)
    return Number.isFinite(num) ? num : 0
})

const totalPrice = computed(() => {
    if (isPackage.value && selected.value !== null) {
        const selectedPackage = availablePackages.value[selected.value]
        return selectedPackage ? selectedPackage.price : 0
    }
    return hourlyPrice.value * lessonDurationHours.value
})

const totalLessons = computed(() => {
    if (isPackage.value && selected.value !== null) {
        const selectedPackage = availablePackages.value[selected.value]
        return selectedPackage ? selectedPackage.number_of_lessons : 1
    }
    return 1
})

const hourlyPrice = computed(() => selectedLevel.value?.price || 0)

// ============================
// Utility functions
// ============================
const saveBookingData = (data) => {
    sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data))
}

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
const handlePackageSelect = (index) => {
    selected.value = index
    if (index !== null) {
        const selectedPackage = availablePackages.value[index]
        bookingData.value.package = {
            id: selectedPackage.id,
            name: selectedPackage.name,
            price: selectedPackage.price,
            number_of_lessons: selectedPackage.number_of_lessons
        }
        bookingData.value.total_price = selectedPackage.price
    } else {
        bookingData.value.package = null
        bookingData.value.total_price = hourlyPrice.value * lessonDurationHours.value
    }
    saveBookingData({
        ...bookingData.value,
        isPackage: isPackage.value
    })
}

// ============================
// Watchers
// ============================
watch(isPackage, (newValue) => {
    if (newValue) {
        selected.value = 0
        const selectedPackage = availablePackages.value[0]
        if (selectedPackage) {
            bookingData.value.package = {
                id: selectedPackage.id,
                name: selectedPackage.name,
                price: selectedPackage.price,
                number_of_lessons: selectedPackage.number_of_lessons
            }
            bookingData.value.total_price = selectedPackage.price
        }
    } else {
        selected.value = null
        bookingData.value.package = null
        bookingData.value.total_price = hourlyPrice.value * lessonDurationHours.value
    }
    saveBookingData({
        ...bookingData.value,
        isPackage: isPackage.value
    })
})

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

        if (data.isPackage) {
            isPackage.value = true
            const index = availablePackages.value.findIndex(
                p => p.id === bookingData.value.package?.id
            )
            if (index !== -1) {
                selected.value = index
            }
        }
    } else {
        handleBack()
    }
})
</script>

<style scoped>
@import url("@css/BookingCommon.css");

.custom-method-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(90deg, #f3f4f6 0%, #ededed 100%);
    border-bottom: 1px solid #e1e1e1;
    padding: 1.1rem 1.4rem 1.1rem 1.4rem;
}
.custom-method-title {
    font-size: var(--font-size-medium);
    font-weight: 700;
    color: #18181b;
    letter-spacing: -0.01em;
}
.custom-switch {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1rem;
    font-weight: 500;
    color: #6b7280;
    user-select: none;
}
.custom-switch-label {
    margin-right: 0.3rem;
}
.custom-switch input[type="checkbox"] {
    opacity: 0;
    width: 0;
    height: 0;
    position: absolute;
}
.custom-slider {
    position: relative;
    display: inline-block;
    width: 38px;
    height: 22px;
    background: #e5e7eb;
    border-radius: 22px;
    transition: background 0.2s;
    box-shadow: 0 1px 4px 0 rgba(0,0,0,0.04);
}
.custom-slider:before {
    content: "";
    position: absolute;
    left: 3px;
    top: 3px;
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 1px 4px 0 rgba(0,0,0,0.08);
    transition: transform 0.22s cubic-bezier(.4,0,.2,1);
}
.custom-switch input:checked + .custom-slider {
    background: black;
}
.custom-switch input:checked + .custom-slider:before {
    transform: translateX(16px);
    background: #fff;
}

.method-tabs {
    display: flex;
    gap: 0.5rem;
    background: #f6f7f9;
    border-radius: 8px;
    padding: 0.3rem;
    margin-bottom: 1.2rem;
}
.method-tab {
    flex: 1;
    background: transparent;
    border: none;
    border-radius: 6px;
    font-size: var(--font-size-heading-6);
    font-weight: 600;
    color: #222;
    padding: 0.5rem 0;
    transition: background 0.2s;
}
.method-tab.active {
    background: #fff;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03);
}
.method-tab.disable {
    cursor: not-allowed;
}

.custom-fee-card {
    border: 1px solid #d1d5db;
    border-radius: 12px;
    background: #fff;
    padding: 1.5rem;
    margin-top: 0.7rem;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.03);
}
.custom-fee-header {
    display: flex;
    align-items: center;
    gap: 1.1rem;
    margin-bottom: 1.1rem;
}
.fee-icon {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    box-shadow: 0 1px 4px 0 rgba(0,0,0,0.04);
}
.custom-fee-title {
    font-size: 1.18rem;
    font-weight: 700;
    color: #18181b;
    margin-bottom: 0.1rem;
}
.custom-fee-desc {
    font-size: 1rem;
    color: #6b7280;
    font-weight: 400;
}
.fee-details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1.5rem;
    margin-bottom: 0.2rem;
    font-size: 1.08rem;
}
.fee-label {
    color: #4b5563;
    font-weight: 400;
}
.fee-value {
    color: #222;
    font-weight: 500;
    text-align: right;
}
.fee-divider {
    border-top: 1.5px solid #e5e7eb;
    margin: 1.1rem 0 1.1rem 0;
}
.fee-total-row {
    margin-top: 0.2rem;
    font-size: 1.13rem;
}
.fee-total-label {
    font-weight: 700;
}
.fee-total-value {
    color: #18181b;
    font-size: 1.18rem;
    font-weight: 700;
}
.custom-fee-alert {
    background: #eaf2ff;
    border-radius: 8px;
    padding: 0.9rem 1.2rem;
    margin-top: 1.3rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: #2563eb;
    font-size: 1.01rem;
}

.custom-package-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
    justify-content: center;
    margin-top: 1.5rem;
}
.custom-package-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: none;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 250px;
    max-width: 300px;
    position: relative;
    transition: border 0.2s, box-shadow 0.2s;
}
.custom-package-card.selected {
    border: 2.5px solid #18181b;
    box-shadow: 0 2px 12px 0 rgba(0,0,0,0.07);
}
.custom-package-card.popular {
    border: 2.5px solid #18181b;
}
.popular-label {
    position: absolute;
    top: -1rem;
    left: 50%;
    transform: translateX(-50%);
    background: #18181b;
    color: #fff;
    font-size: var(--font-size-mini);
    font-weight: 600;
    border-radius: 1.2rem;
    padding: 0.25rem 1.2rem;
    z-index: 2;
    box-shadow: 0 2px 8px 0 rgba(0,0,0,0.08);
    letter-spacing: 0.01em;
}
.custom-package-title {
    font-size: 1.18rem;
    font-weight: 700;
    color: #18181b;
    margin-bottom: 0.2rem;
    text-align: center;
}
.custom-package-sub {
    color: #71717a;
    font-size: 1rem;
    margin-bottom: 1.1rem;
    text-align: center;
}
.custom-package-price {
    font-size: var(--font-size-heading-4);
    font-weight: 700;
    color: #18181b;
    margin-bottom: 0.2rem;
    text-align: center;
}
.custom-package-price-hour {
    color: #71717a;
    font-size: 1.08rem;
    margin-bottom: 1.1rem;
    text-align: center;
}
.custom-package-features {
    list-style: none;
    padding: 0;
    margin: 0 0 1.2rem 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    width: 100%;
}
.custom-package-features li {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    color: #22c55e;
    font-size: 1.05rem;
    font-weight: 500;
}
.custom-package-features li svg {
    width: 1.15em;
    height: 1.15em;
    flex-shrink: 0;
    margin-top: 0.18em;
    display: block;
}
.custom-package-features li span {
    color: #18181b;
    font-weight: 500;
    line-height: 1.5;
    word-break: break-word;
}
.custom-package-cards button {
    margin-top: auto;
}

@media (max-width: 1024px) {
    .custom-fee-card {
        padding: 1.5rem;
    }
}
</style>

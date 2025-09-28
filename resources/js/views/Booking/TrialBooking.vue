<script setup>
import {
    ref,
    computed,
    onMounted,
    getCurrentInstance,
    watch,
    nextTick,
    toRefs,
    reactive
} from 'vue'
import {
    useRoute,
    useRouter
} from 'vue-router'
import {
    useStore
} from 'vuex'

// ============================
// Constants & Enums
// ============================
const STORAGE_KEY = 'bookingData'
const REQUIRED_FIELDS = ['subjectId', 'levelId', 'date', 'timeSlotId', 'tutorSessionId']
const BOOKING_MODE = 'trial'

// ============================
// Core setup
// ============================
const {
    proxy
} = getCurrentInstance()
const route = useRoute()
const router = useRouter()
const store = useStore()

// ============================
// Props
// ============================
const props = defineProps({
    tutorInfo: {
        type: Object,
        required: true
    },
    currentUser: {
        type: Object,
        required: false,
        default: null
    }
})

// ============================
// Reactive State
// ============================
const showEditModal = ref(true)
const isInitializing = ref(false)
const showValidationErrors = ref(false)
const isSubmitting = ref(false)
const locationType = ref(null)
const activeTutorSessionId = ref(null)

// Booking data với structure rõ ràng hơn
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
    hourlyPrice: 0,
    totalPrice: 0,
    studyLocation: null,
    transportationFee: null,
    maxDistance: null,
    note: '',
    tutorSession: null
})

// Edit form với validation tốt hơn
const editForm = ref({
    subjectId: '',
    levelId: '',
    date: '',
    timeSlotId: '',
    tutorSessionId: ''
})

// ============================
// Computed Properties (Optimized)
// ============================
const tutorInfo = toRefs(props).tutorInfo
const currentUser = toRefs(props).currentUser

// Cache configuration data
const configuration = computed(() => store.state.configuration || {})

const subjectOptions = computed(() =>
    tutorInfo.value?.user_subjects?.map(s => ({
        id: s.subject_id,
        name: s.subject_name
    })) || []
)

const allLevelOptions = computed(() => {
    const levels = []
    tutorInfo.value?.user_subjects?.forEach(subject => {
        subject.user_subject_levels?.forEach(level => {
            levels.push({
                id: level.education_level_id,
                name: level.education_level,
                subject_id: subject.subject_id,
                price: level.price
            })
        })
    })
    return levels
})

const editLevelOptions = computed(() =>
    allLevelOptions.value.filter(level => level.subject_id === editForm.value.subjectId)
)

const timeOptions = computed(() =>
    proxy.$helper.handleTimeSlot(
        configuration.value.timeSlots || [],
        editForm.value.date
    )
)

const locationTypes = computed(() => {
    const studyLocations = configuration.value.studyLocations || []
    const tutorStudyLocations = tutorInfo.value?.user_study_locations || []
    const tutorLocationIds = new Set(tutorStudyLocations.map(loc => loc.study_location_id))

    return studyLocations
        .filter(loc => tutorLocationIds.has(loc.id))
        .map(loc => {
            const tutorLocation = tutorStudyLocations.find(tl => tl.study_location_id === loc.id)
            return {
                value: loc.id,
                label: loc.name?.trim(),
                transportation_fee: tutorLocation?.transportation_fee,
                max_distance: tutorLocation?.max_distance,
                description: loc.description,
                icon: loc.icon,
                home_tutor: loc.home_tutor,
                home_user: loc.home_user
            }
        })
})

// Optimized subject/level selection
const selectedSubject = computed(() =>
    tutorInfo.value?.user_subjects?.find(s => s.subject_id === bookingData.value.subject.id)
)

const selectedLevel = computed(() =>
    selectedSubject.value?.user_subject_levels?.find(
        lvl => lvl.education_level_id === bookingData.value.level.id
    )
)

const tutorSessionOptions = computed(() => {
    const sessions = configuration.value.tutorSessions || []
    const allowedSessionId = tutorInfo.value?.tutor_session_id
    return allowedSessionId ? sessions.filter(s => s.id === allowedSessionId) : sessions
})

const selectedTutorSession = computed(() => {
    const sessionId = hasFreeQuota.value && tutorInfo.value?.tutor_session_id
        ? tutorInfo.value.tutor_session_id
        : activeTutorSessionId.value
    return tutorSessionOptions.value.find(s => s.id === sessionId)
})

// Pricing computations
const hasFreeQuota = computed(() => currentUser.value?.free_study)
const lessonDurationHours = computed(() =>
    parseFloat(selectedTutorSession.value?.duration_hours || 0) || 0
)
const lessonDurationMinutes = computed(() =>
    Math.round(lessonDurationHours.value * 60)
)
const hourlyPrice = computed(() => selectedLevel.value?.price || 0)
const totalPrice = computed(() => hourlyPrice.value * lessonDurationHours.value)
const estimatedTrialPrice = computed(() =>
    hasFreeQuota.value?0 : Math.round(hourlyPrice.value * lessonDurationHours.value)
)

// Available time slots for editing
const availableEditTimeSlots = computed(() => {
    if (!editForm.value.date || !tutorInfo.value?.user_weekly_time_slots) return []

    const dayOfWeek = proxy.$helper.getDayOfWeek(editForm.value.date)
    if (dayOfWeek === null) return []

    return tutorInfo.value.user_weekly_time_slots.filter(
        slot => slot.day_of_week_code === dayOfWeek
    )
})

const validSelectableTimes = computed(() => {
    if (!editForm.value.date || availableEditTimeSlots.value.length === 0) {
        return timeOptions.value.map(t => ({
            ...t,
            disabled: true
        }))
    }

    const availableIds = new Set(availableEditTimeSlots.value.map(s => s.time_slot_id))
    return timeOptions.value.map(point => ({
        ...point,
        disabled: point.disabled || !availableIds.has(point.id)
    }))
})

// Form validation
const isFormValid = computed(() =>
    REQUIRED_FIELDS.every(field => !!editForm.value[field])
)

// ============================
// Utility Functions
// ============================
const isStudentHome = (type) => {
    const studyLoc = configuration.value.studyLocations?.find(l => l.id === type.value)
    return !!studyLoc?.home_user
}

const addHoursToTime = (startTime, hours) => {
    try {
        const [h, m] = (startTime || '00:00').split(':').map(n => parseInt(n, 10) || 0)
        const minutesToAdd = Math.round((hours || 0) * 60)
        const totalMinutes = h * 60 + m + minutesToAdd
        const endHour = Math.floor((totalMinutes % (24 * 60)) / 60)
        const endMinute = totalMinutes % 60

        return `${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`
    } catch {
        return ''
    }
}

const validateField = (fieldName) =>
    showValidationErrors.value && !editForm.value[fieldName]

// ============================
// Storage Management
// ============================
const saveBookingData = (data) => {
    try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify(data))
    } catch (error) {
        console.warn('Failed to save booking data:', error)
    }
}

const loadBookingData = () => {
    try {
        const raw = sessionStorage.getItem(STORAGE_KEY)
        return raw?JSON.parse(raw) : null
    } catch (error) {
        console.warn('Failed to load booking data:', error)
        return null
    }
}

const clearBookingData = () => {
    try {
        sessionStorage.removeItem(STORAGE_KEY)
    } catch (error) {
        console.warn('Failed to clear booking data:', error)
    }
}

// ============================
// Form Handlers
// ============================
const resetTutorSession = () => {
    if (hasFreeQuota.value && tutorInfo.value?.tutor_session_id) {
        editForm.value.tutorSessionId = tutorInfo.value.tutor_session_id
        activeTutorSessionId.value = tutorInfo.value.tutor_session_id
    } else {
        editForm.value.tutorSessionId = ''
        activeTutorSessionId.value = null
    }
}

const onEditSubjectChange = (newSubjectId) => {
    editForm.value.subjectId = newSubjectId
    editForm.value.levelId = ''
    editForm.value.timeSlotId = ''
    resetTutorSession()
}

const onEditLevelChange = (newLevelId) => {
    editForm.value.levelId = newLevelId
    editForm.value.timeSlotId = ''
    resetTutorSession()
}

const onEditTimeSlotChange = (newTimeId) => {
    editForm.value.timeSlotId = newTimeId
}

const onSelectTutorSession = (id) => {
    activeTutorSessionId.value = id
    editForm.value.tutorSessionId = id
}

// ============================
// Location Management
// ============================
const initializeLocationDefault = () => {
    if (locationTypes.value.length > 0) {
        locationType.value = locationTypes.value[0].value
        bookingData.value.studyLocation = locationTypes.value[0]
    }
}

const updateLocationData = (locationId) => {
    const studyLocation = configuration.value.studyLocations?.find(l => l.id === locationId)
    const tutorLocation = tutorInfo.value?.user_study_locations?.find(
        tl => tl.study_location_id === locationId
    )

    bookingData.value.studyLocation = studyLocation || null
    bookingData.value.transportationFee = tutorLocation?.transportation_fee || null
    bookingData.value.maxDistance = tutorLocation?.max_distance || null
}

// ============================
// Modal & Form Management
// ============================
const loadDataEditForm = async (data) => {
    isInitializing.value = true

    editForm.value.subjectId = bookingData.value.subject.id
    editForm.value.levelId = bookingData.value.level.id
    editForm.value.date = bookingData.value.date
    editForm.value.timeSlotId = bookingData.value.time?.start || ''

    resetTutorSession()
    showEditModal.value = true

    await nextTick()
    isInitializing.value = false
}

const openEditModal = async () => {
    await loadDataEditForm()
    showEditModal.value = true
}

const handleEditSave = () => {
    if (!isFormValid.value) {
        showValidationErrors.value = true
        return
    }

    const selectedSubject = subjectOptions.value.find(s => s.id === editForm.value.subjectId)
    const selectedLevel = allLevelOptions.value.find(l => l.id === editForm.value.levelId)
    const startTimeObj = timeOptions.value.find(t => t.id === editForm.value.timeSlotId)
    const session = selectedTutorSession.value
    const endTimeText = startTimeObj?        addHoursToTime(startTimeObj.name || startTimeObj.time, session?.duration_hours || 0) :
        ''

    // Update booking data
    Object.assign(bookingData.value, {
        subject: {
            id: editForm.value.subjectId,
            name: selectedSubject?.name || ''
        },
        level: {
            id: editForm.value.levelId,
            name: selectedLevel?.name || ''
        },
        date: editForm.value.date,
        time: {
            start: editForm.value.timeSlotId,
            end: endTimeText,
            display: (startTimeObj && session)?`${startTimeObj.name} - ${endTimeText}` : ''
        },
        tutorSession: session || null
    })

    // Save to session storage
    const bookingDataToSave = {
        ...bookingData.value,
        mode: BOOKING_MODE
    }

    saveBookingData(bookingDataToSave)
    showEditModal.value = false
    showValidationErrors.value = false
}

const handleModalClose = () => {
   showEditModal = false
}

// ============================
// API & Submission
// ============================
const submitTrialBooking = async () => {
    if (!isFormValid.value) {
       showEditModal = true
       showValidationErrors = true
        return
    }

   isSubmitting = true

    try {
        const payload = {
            uid: route.params.uid,
            subject_id: bookingData.value.subject.id,
            education_level_id: bookingData.value.level.id,
            date: bookingData.value.date,
            time_slot_id: editForm.value.timeSlotId,
            tutor_session_id: editForm.value.tutorSessionId,
            note: bookingData.value.note || '',
            hourly_rate: hasFreeQuota.value?0 : hourlyPrice.value,
            duration: lessonDurationHours.value,
            study_location_id: bookingData.value.studyLocation?.value || null,
        }

        const response = await proxy.$api.apiPost('bookings/trial', payload)

        if (response?.success) {
            proxy.$notification?.success?.(response?.message || 'Đặt lịch học thử thành công')

            const bookingId = response?.data?.booking?.id
            if (bookingId) {
                // Update user data if API returns new free_study value
                const freeStudy = response?.data?.free_study
                if (typeof freeStudy !== 'undefined') {
                    const currentUserData = store.getters?.userData || {}
                    await store.dispatch('updateUserData', {
                        ...currentUserData,
                        free_study: freeStudy
                    })
                }

                clearBookingData()
                await router.push({
                    name: 'booking-success',
                    params: {
                        id: bookingId
                    },
                })
            }
        } else {
            proxy.$notification?.error?.(response?.message || 'Đặt lịch học thử thất bại')
        }
    } catch (error) {
        console.error('Trial booking submission error:', error)
        proxy.$notification?.error?.('Có lỗi xảy ra khi đặt lịch học thử')
    } finally {
       isSubmitting = false
    }
}

const handleNext = () => submitTrialBooking()

// ============================
// Watchers
// ============================
watch(() =>locationType, updateLocationData)

watch(bookingData, (newData) => {
    saveBookingData(newData)
}, {
    deep: true
})

watch(() => editForm.value.date, (newDate) => {
    if (newDate && !isInitializing) {
        editForm.value.timeSlotId = ''
    }
})

// ============================
// Lifecycle
// ============================
onMounted(async () => {
    const storedData = loadBookingData()

    if (storedData) {
        Object.assign(bookingData.value, storedData)
    }

    initializeLocationDefault()

    if (storedData) {
        await loadDataEditForm(storedData)
    }
})

// ============================
// Expose for template
// ============================
defineExpose({
    bookingData,
    editForm,

    // Computed
    subjectOptions,
    editLevelOptions,
    timeOptions,
    locationTypes,
    tutorSessionOptions,
    selectedTutorSession,
    hasFreeQuota,
    hourlyPrice,
    totalPrice,
    estimatedTrialPrice,
    lessonDurationHours,
    lessonDurationMinutes,
    validSelectableTimes,
    isFormValid,

    // Methods
    openEditModal,
    handleEditSave,
    handleModalClose,
    handleNext,
    onEditSubjectChange,
    onEditLevelChange,
    onEditTimeSlotChange,
    onSelectTutorSession,
    validateField,
    isStudentHome
})
</script>

<template>
<div class="container">
    <div class="lesson-info-layout booking-content">
        <div class="lesson-info-left">
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
                                <span class="sub-title">{{ $helper.dataIsNull(bookingData.subject.name) }}</span>
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
                                <span class="sub-title">{{ $helper.dataIsNull(bookingData.level.name) }}</span>
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
                                <span class="sub-title">{{ $helper.dataIsNull($helper.formatDate(bookingData.date)) }}</span>
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
                                <span class="sub-title">{{ $helper.dataIsNull(bookingData.time.display) }} ({{ lessonDurationMinutes }} phút)</span>
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
                        <div class="lesson-detail-item" v-if="bookingData.tutorSession">
                            <div class="detail-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 8h10"/></svg>
                            </div>
                            <div class="detail-value">
                                <span class="title">Loại buổi học</span>
                                <span class="sub-title">{{ bookingData.tutorSession?.name }}</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn-md btn-secondary btn-edit" @click="openEditModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                            <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path>
                        </svg>
                        <span>Chỉnh sửa</span>
                    </button>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header">Chọn nơi học</div>
                <div class="card-content">
                    <div class="location-type-group">
                        <div v-for="type in locationTypes" :key="type.value" :class="['location-type-option', { selected: locationType === type.value }]" @click="locationType = type.value">
                            <div class="location-content">
                                <div class="right-content">
                                    <div class="option-icon">
                                        <img :src="type.icon" class="icon-md">
                                    </div>
                                    <div class="option-main">
                                        <span class="option-label">
                                            <span class="label">{{ type.label }}</span>
                                            <span v-if="type.popular" class="popular-badge">Phổ biến</span>
                                            <span v-if="type.transportation_fee" class="extra-badge">{{ $helper.formatCurrency(type.transportation_fee) }}/km</span>
                                        </span>
                                        <span class="option-desc" v-if="type.home_user">{{ type.description }} {{ type.max_distance }}km</span>
                                        <span class="option-desc" v-else>{{ tutorInfo.provinces_name }}, {{ tutorInfo.districts_name }}</span>
                                    </div>
                                </div>
                                <div class="left-content">
                                    <span class="option-radio">
                                        <input type="radio" :checked="locationType === type.value" :value="type.value" />
                                    </span>
                                </div>
                            </div>
                            <template v-if="isStudentHome(type) && locationType === type.value">
                                <div class="student-address-block">
                                    <label class="address-label">Địa chỉ của bạn <span class="required">*</span></label>
                                    <base-input v-model="bookingData.location" placeholder="Nhập địa chỉ đầy đủ (số nhà, đường, phường/xã, quận/huyện)" size="medium" />
                                    <div class="address-warning">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <line x1="12" y1="8" x2="12" y2="12" />
                                            <line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                                        Vui lòng nhập địa chỉ chính xác để gia sư có thể kiểm tra khoảng cách di chuyển. Nếu nằm ngoài bán kính {{ type.max_distance }}km, gia sư có thể từ chối yêu cầu.
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-card note-card">
                <div class="card-header">Ghi chú cho gia sư</div>
                <div class="card-content">
                    <div class="location-input-wrapper"></div>
                    <div class="note-section">
                        <base-input v-model="bookingData.note" type="textarea" placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt cho gia sư..." size="large" />
                        <div class="note-helper">Hãy cung cấp thông tin chi tiết về nhu cầu học tập để gia sư có thể chuẩn bị tốt nhất.</div>
                    </div>
                </div>
            </div>

            <div class="action">
                <div></div>
                <button class="btn-md border-r-2 btn-primary" @click="handleNext">Tiếp tục <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>

        <div class="lesson-info-right" v-if="tutorInfo">
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
                                <circle cx="12" cy="10" r="3" /></svg> {{ tutorInfo.address }}</div>
                    </div>
                </div>
            </div>

            <div class="info-card order-summary-card">
                <div class="card-header">Tóm tắt đơn hàng</div>
                <div class="card-content">
                    <div class="order-row">
                        <span class="order-label">Học phí mỗi giờ</span>
                        <span class="order-value">{{ $helper.formatCurrency(hourlyPrice) }}</span>
                    </div>
                    <div class="order-row">
                        <span class="order-label">Thời lượng</span>
                        <span class="order-value">{{ lessonDurationMinutes }} phút</span>
                    </div>
                    <div class="order-total">
                        <span class="order-label">Tổng cộng</span>
                        <!-- <span class="order-value total">{{ $helper.formatCurrency(totalPrice) }}</span> -->
                        <span class="order-value total">
                            <template v-if="hasFreeQuota">
                                Miễn phí
                            </template>
                            <template v-else>
                                {{ $helper.formatCurrency(estimatedTrialPrice) }}
                            </template>
                        </span>
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
                <img :src="tutorInfo.avatar || '/images/default-avatar.png'" />
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
                        <circle cx="12" cy="10" r="3" /></svg> {{ tutorInfo.address }}</div>
            </div>
        </div>
    </div>

    <div class="separation"></div>

    <div class="user-info_mobile_action">
        <button class="btn-md btn-secondary" @click="handleBack"><i class="fas fa-arrow-left"></i> Quay lại</button>
        <button class="btn-md btn-primary" @click="handleNext">Tiếp tục <i class="fas fa-arrow-right"></i></button>
    </div>
</div>

<base-modal :isOpen="showEditModal" title="Chỉnh sửa thông tin buổi học" @close="handleModalClose" size="large">
    <form @submit.prevent="handleEditSave">
        <div class="edit-grid">
            <base-datepicker v-model="editForm.date" class="col-span-2" />
            <div class="separation col-span-2"></div>

            <div class="form-group">
                <base-select v-model="editForm.subjectId" :options="subjectOptions" label="Môn học" placeholder="Chọn môn học" @update:modelValue="onEditSubjectChange" :required="true" :error="validateField('subjectId')" error-message="Vui lòng chọn môn học" />
            </div>
            <div class="form-group">
                <base-select v-model="editForm.levelId" :options="editLevelOptions" label="Cấp độ" placeholder="Chọn cấp độ" :disabled="!editForm.subjectId" @update:modelValue="onEditLevelChange" :required="true" :error="validateField('levelId')" error-message="Vui lòng chọn cấp độ" />
            </div>
            <div class="form-group col-span-2">
                <label class="label text-small">Khung giờ</label>
                <div class="time-select-grid">
                    <button v-for="t in validSelectableTimes" :key="'time-' + t.id" type="button" class="time-chip" :class="{ selected: editForm.value?.timeSlotId === t.id || editForm.timeSlotId === t.id, disabled: t.disabled, inactive: !editForm.date || availableEditTimeSlots.length === 0 }" @click="(!t.disabled && editForm.date && availableEditTimeSlots.length > 0) && onEditTimeSlotChange(t.id)">
                        {{ t.name || t.time }}
                    </button>
                </div>
                <div v-if="validateField('timeSlotId')" class="error-text">Vui lòng chọn khung giờ</div>
            </div>

            <div class="separation col-span-2"></div>

            <div class="form-group col-span-2">
                <label class="label text-small">Loại buổi học</label>

                <!-- Free trial users see tutor's configured session -->
                <div v-if="hasFreeQuota && tutorInfo?.tutor_session" class="session-list">
                    <div class="session-item selected free-trial-session">
                        <div class="left-icon active">
                            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="content">
                            <div class="title-row">
                                <div class="title">{{ tutorInfo.tutor_session.time }}</div>
                                <span class="badge free-badge">Miễn phí</span>
                            </div>
                            <div class="meta">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>Thời lượng: {{ proxy.$helper.formatDuration(tutorInfo.tutor_session.duration_hours) }}</span>
                            </div>
                            <div class="desc">{{ tutorInfo.tutor_session.description }}</div>
                        </div>
                        <div class="right-radio">
                            <span class="radio checked"></span>
                        </div>
                    </div>
                </div>

                <!-- Paid trial users can select session -->
                <div v-else class="session-list">
                    <div v-for="s in tutorSessionOptions" :key="s.id" class="session-item" :class="{ selected: activeTutorSessionId === s.id }" @click="onSelectTutorSession(s.id)">
                        <div class="left-icon" :class="{ active: activeTutorSessionId === s.id }">
                            <svg v-if="s.recommended" class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14"></path>
                                <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" />
                            </svg>
                            <svg v-else class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" /></svg>
                        </div>
                        <div class="content">
                            <div class="title-row">
                                <div class="title">{{ s.time }}</div>
                                <span v-if="s.recommended" class="badge">Gợi ý</span>
                            </div>
                            <div class="meta">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" /></svg>
                                <span>Thời lượng: {{ proxy.$helper.formatDuration(s.duration_hours) }}</span>
                            </div>
                            <div class="desc">{{ s.description }}</div>
                        </div>
                        <div class="right-radio"><span class="radio" :class="{ checked: activeTutorSessionId === s.id }"></span></div>
                    </div>
                </div>
            </div>
            <div class="col-span-2" v-if="freeStudyTime">
                <div class="free-trial-info">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span>Buổi học thử miễn phí: {{ lessonDurationMinutes }} phút</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-md btn-primary btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</base-modal>
</template>

<style scoped>
@import url("@css/BookingCommon.css");
@import url("@css/lessonInformation.css");

.edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.col-span-2 {
    grid-column: span 2;
}

.free-trial-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: #0f766e;
    background: #ecfeff;
    border: 1px solid #a5f3fc;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
}

.free-trial-session {
    background: #f0fdf4;
    border: 2px solid #22c55e;
}

.free-badge {
    background: #22c55e;
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
}
</style>

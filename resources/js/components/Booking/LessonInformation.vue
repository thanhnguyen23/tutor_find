<script setup>
import {
    defineEmits,
    ref,
    onMounted,
    computed,
    watch,
    getCurrentInstance,
    nextTick,
    reactive
} from 'vue'
import {
    useRouter,
    useRoute
} from 'vue-router'
import {
    useStore
} from 'vuex'

// ============================
// Core setup & constants
// ============================
const {
    proxy
} = getCurrentInstance()
const router = useRouter()
const route = useRoute()
const store = useStore()

const STORAGE_KEY = 'bookingData'
const REQUIRED_FIELDS = ['subjectId', 'levelId', 'date', 'timeSlotId', 'tutorSessionId']
const ORDER_BENEFITS = [
    "Đảm bảo hoàn tiền nếu không hài lòng",
    "Hỗ trợ 24/7 từ đội ngũ EduTutor"
]

// ============================
// Props & Emits
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
    },
    mode: {
        type: String,
        required: false,
        default: 'official',
        validator: (value) => ['official', 'trial'].includes(value)
    }
})

const emit = defineEmits(['next', 'back'])

// ============================
// Reactive state
// ============================
const myUid = computed(() => store.state.auth?.user?.uid)
const isSelfBooking = computed(() => props.tutorInfo?.uid === myUid.value)
const checkModeIsTrial = computed(() => props.mode === 'trial')

// Main booking data - using reactive for better performance
const bookingData = reactive({
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
    studyLocation: null,
    transportationFee: null,
    maxDistance: null,
    note: '',
    tutorSession: null
})

// Edit form state
const editForm = reactive({
    subjectId: '',
    levelId: '',
    date: '',
    timeSlotId: '',
    tutorSessionId: ''
})

// UI state
const uiState = reactive({
    isSubmitting: false,
    showEditModal: true,
    isInitializing: false,
    showValidationErrors: false
})

const locationType = ref(null)
const activeTutorSessionId = ref(checkModeIsTrial.value?props.tutorInfo?.tutor_session_id || null : null)

// ============================
// Computed properties (organized by concern)
// ============================

// Configuration & options
const configuration = computed(() => store.state.configuration || {})
const hasFreeQuota = computed(() => !!props.currentUser?.free_study)

const subjectOptions = computed(() =>
    props.tutorInfo?.user_subjects?.map(s => ({
        id: s.subject_id,
        name: s.subject_name
    })) || []
)

const allLevelOptions = computed(() => {
    const levels = []
    props.tutorInfo?.user_subjects?.forEach(subject => {
        subject.user_subject_levels?.forEach(level => {
            levels.push({
                id: level.education_level_id,
                name: level.education_level,
                subject_id: subject.subject_id
            })
        })
    })
    return levels
})

const editLevelOptions = computed(() =>
    allLevelOptions.value.filter(level => level.subject_id === editForm.subjectId)
)

const tutorSessionOptions = computed(() => {
    const allSessions = configuration.value.tutorSessions || []
    if (checkModeIsTrial.value) {
        const allowedId = props.tutorInfo?.tutor_session_id
        return allowedId?allSessions.filter(session => session.id === allowedId) : allSessions
    }
    return allSessions
})

const locationTypes = computed(() => {
    const studyLocations = configuration.value.studyLocations || []
    const tutorStudyLocations = props.tutorInfo?.user_study_locations || []
    const tutorLocationIds = new Set(tutorStudyLocations.map(loc => loc.study_location_id))

    return studyLocations
        .filter(location => tutorLocationIds.has(location.id))
        .map(location => {
            const tutorLocation = tutorStudyLocations.find(tl => tl.study_location_id === location.id)
            return {
                value: location.id,
                label: location.name?.trim(),
                transportation_fee: tutorLocation?.transportation_fee,
                max_distance: tutorLocation?.max_distance,
                description: location.description,
                icon: location.icon,
                home_tutor: location.home_tutor,
                home_user: location.home_user
            }
        })
})

// Time-related computeds
const timeOptions = computed(() =>
    proxy.$helper.handleTimeSlot(configuration.value.timeSlots || [], editForm.date)
)

const availableEditTimeSlots = computed(() => {
    if (!editForm.date || !props.tutorInfo?.user_weekly_time_slots) return []

    const dayOfWeek = proxy.$helper.getDayOfWeek(editForm.date)
    if (dayOfWeek === null) return []

    return props.tutorInfo.user_weekly_time_slots.filter(
        slot => slot.day_of_week_code === dayOfWeek
    )
})

const validSelectableTimes = computed(() => {
    if (!editForm.date || availableEditTimeSlots.value.length === 0) {
        return timeOptions.value.map(time => ({
            ...time,
            disabled: true
        }))
    }

    const availableIds = new Set(availableEditTimeSlots.value.map(slot => slot.time_slot_id))
    return timeOptions.value.map(timePoint => ({
        ...timePoint,
        disabled: timePoint.disabled || !availableIds.has(timePoint.id)
    }))
})

// Price & session computeds
const selectedSubject = computed(() =>
    props.tutorInfo?.user_subjects?.find(subject => subject.subject_id === bookingData.subject.id)
)

const selectedLevel = computed(() =>
    selectedSubject.value?.user_subject_levels?.find(
        level => level.education_level_id === bookingData.level.id
    )
)

const selectedTutorSession = computed(() => {
    if (checkModeIsTrial.value && hasFreeQuota.value && props.tutorInfo?.tutor_session_id) {
        return tutorSessionOptions.value.find(session => session.id === props.tutorInfo.tutor_session_id)
    }
    return tutorSessionOptions.value.find(session => session.id === activeTutorSessionId.value)
})

const lessonDurationHours = computed(() =>
    parseFloat(selectedTutorSession.value?.duration_hours || 0) || 0
)

const hourlyPrice = computed(() => selectedLevel.value?.price || 0)

const totalPrice = computed(() => {
    if (checkModeIsTrial.value && hasFreeQuota.value) return 0
    return hourlyPrice.value * lessonDurationHours.value
})

// Validation
const isFormValid = computed(() =>
    REQUIRED_FIELDS.every(field => !!editForm[field])
)

// ============================
// Utility functions
// ============================
const isStudentHome = (type) => {
    const studyLocation = configuration.value.studyLocations?.find(location => location.id === type.value)
    return !!studyLocation?.home_user
}

const addHoursToTime = (startTime, hours) => {
    try {
        const [hour, minute] = (startTime || '00:00').split(':').map(n => parseInt(n, 10) || 0)
        const minutesToAdd = Math.round((hours || 0) * 60)
        const totalStartMinutes = hour * 60 + minute
        const totalEndMinutes = totalStartMinutes + minutesToAdd

        const endHour = Math.floor((totalEndMinutes % (24 * 60)) / 60)
        const endMinute = totalEndMinutes % 60
        const pad = (num) => String(num).padStart(2, '0')

        return `${pad(endHour)}:${pad(endMinute)}`
    } catch (error) {
        console.warn('Time calculation error:', error)
        return startTime
    }
}

const validateField = (fieldName) => {
    return uiState.showValidationErrors && !editForm[fieldName];
}

// ============================
// Storage utilities
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
// Event handlers
// ============================
const onEditSubjectChange = (newSubjectId) => {
    editForm.subjectId = newSubjectId
    editForm.levelId = ''
    editForm.timeSlotId = ''
}

const onEditLevelChange = (newLevelId) => {
    editForm.levelId = newLevelId
    editForm.timeSlotId = ''
}

const onEditTimeSlotChange = (newTimeId) => {
    editForm.timeSlotId = newTimeId
}

const onSelectTutorSession = (sessionId) => {
    if (checkModeIsTrial.value && hasFreeQuota.value) {
        // Locked in trial mode when user has free quota
        const lockedId = props.tutorInfo?.tutor_session_id || null
        activeTutorSessionId.value = lockedId
        editForm.tutorSessionId = lockedId || ''
        return
    }
    activeTutorSessionId.value = sessionId || null
    editForm.tutorSessionId = sessionId || ''
}

const getLocationTypeDefault = () => {
    const defaultLocation = locationTypes.value?. [0]
    locationType.value = defaultLocation?.value || null
    bookingData.studyLocation = defaultLocation || null
}

const loadDataEditForm = async () => {
    uiState.isInitializing = true

    editForm.subjectId = bookingData.subject.id
    editForm.levelId = bookingData.level.id
    editForm.date = bookingData.date
    editForm.timeSlotId = bookingData.time?.start || ''
    editForm.tutorSessionId = bookingData.tutorSession?.id || ''

    onSelectTutorSession(editForm.tutorSessionId)
    uiState.showEditModal = true

    await nextTick()
    uiState.isInitializing = false
}

const openEditModal = async () => {
    await loadDataEditForm()
    uiState.showEditModal = true
}

const handleEditSave = () => {
    if (!isFormValid.value) {
        uiState.showValidationErrors = true
        return
    }

    // Find selected options
    const selectedSubjectData = subjectOptions.value.find(s => s.id === editForm.subjectId)
    const selectedLevelData = allLevelOptions.value.find(l => l.id === editForm.levelId)
    const startTimeObj = timeOptions.value.find(t => t.id === editForm.timeSlotId)
    const session = tutorSessionOptions.value.find(s => s.id === activeTutorSessionId.value)
    const endTimeText = startTimeObj && session ? addHoursToTime(startTimeObj.name || startTimeObj.time, session.duration_hours || 0) : ''

    // Update booking data
    Object.assign(bookingData, {
        subject: {
            id: editForm.subjectId,
            name: selectedSubjectData?.name || ''
        },
        level: {
            id: editForm.levelId,
            name: selectedLevelData?.name || ''
        },
        date: editForm.date,
        time: {
            start: editForm.timeSlotId,
            end: endTimeText,
            display: (startTimeObj && session) ?
                `${startTimeObj.name || startTimeObj.time} - ${endTimeText}` :
                ''
        },
        tutorSession: session || null,
        hourly_price: (checkModeIsTrial.value && hasFreeQuota.value)?0 : hourlyPrice.value,
        total_price: (checkModeIsTrial.value && hasFreeQuota.value)?0 : totalPrice.value
    })

    uiState.showEditModal = false
    uiState.showValidationErrors = false
}

const handleModalClose = () => {
    if (isFormValid.value) {
        uiState.showEditModal = false
    } else {
        uiState.showValidationErrors = true
    }
}

const handleNext = async () => {
    if (checkModeIsTrial.value) {
        await submitTrialBooking()
        return
    }
    emit('next')
}

const handleBack = () => emit('back')

// ============================
// API submission
// ============================
const submitTrialBooking = async () => {
    if (!isFormValid.value) {
        uiState.showValidationErrors = true
        uiState.showEditModal = true
        return
    }

    uiState.isSubmitting = true

    try {
        const payload = {
            uid: route.params.uid,
            subject_id: bookingData.subject.id,
            education_level_id: bookingData.level.id,
            date: bookingData.date,
            time_slot_id: editForm.timeSlotId,
            tutor_session_id: editForm.tutorSessionId || props.tutorInfo?.tutor_session_id,
            note: bookingData.note || '',
            hourly_rate: hasFreeQuota.value?0 : (hourlyPrice.value || 0),
            duration: lessonDurationHours.value,
            study_location_id: bookingData.studyLocation?.value || null,
        }

        const response = await proxy.$api.apiPost('bookings/trial', payload)

        if (response?.success) {
            proxy.$notification?.success?.(response?.message || 'Đặt lịch học thử thành công')

            const bookingId = response?.data?.booking?.id
            if (bookingId) {
                // Update free_study quota if returned
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
                    }
                })
            }
        } else {
            proxy.$notification?.error?.(response?.message || 'Đặt lịch học thử thất bại')
        }
    } catch (error) {
        console.error('Trial booking submission error:', error)
        proxy.$notification?.error?.('Có lỗi xảy ra khi đặt lịch học thử')
    } finally {
        uiState.isSubmitting = false
    }
}

// ============================
// Watchers
// ============================
watch(() => editForm.date, (newDate) => {
    if (!newDate && !uiState.isInitializing) {
        editForm.timeSlotId = ''
        editForm.tutorSessionId = ''
    }
})

watch(locationType, (newLocationTypeValue) => {
    const studyLocation = configuration.value.studyLocations?.find(l => l.id === newLocationTypeValue)
    const tutorLocation = props.tutorInfo?.user_study_locations?.find(tl => tl.study_location_id === newLocationTypeValue)

    bookingData.studyLocation = studyLocation || null
    bookingData.transportationFee = tutorLocation?.transportation_fee || null
    bookingData.maxDistance = tutorLocation?.max_distance || null
})

// Deep watch for booking data changes
watch(bookingData, (newData) => {
    saveBookingData(newData)
}, {
    deep: true
})

// ============================
// Lifecycle
// ============================
onMounted(async () => {
    const storedData = loadBookingData()

    if (storedData) {
        Object.assign(bookingData, storedData)

        if (storedData.studyLocation) {
            locationType.value = storedData.studyLocation.value
        } else {
            getLocationTypeDefault()
        }

        await loadDataEditForm()
    } else {
        getLocationTypeDefault()
    }
})

// ============================
// Expose for template
// ============================
defineExpose({
    bookingData,
    editForm,
    uiState,
    openEditModal,
    handleEditSave,
    handleNext,
    handleBack,
    // ... other methods that need to be exposed
})
</script>

<template>
<div class="container">
    <div class="lesson-info-layout booking-content">
        <!-- Left Column -->
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
                                <span class="sub-title">{{ $helper.dataIsNull(proxy.$helper.formatDate(bookingData.date)) }}</span>
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
                                <span class="sub-title">{{ $helper.dataIsNull(bookingData.time.display) }}</span>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M7 8h10" /></svg>
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
                                            <span v-if="type.transportation_fee" class="extra-badge">{{ proxy.$helper.formatCurrency(type.transportation_fee) }}/km</span>
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
                            <!-- Hiển thị input nhập địa chỉ khi chọn tại nhà học viên -->
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

            <!-- Thông tin cho gia sư -->
            <div class="info-card note-card">
                <div class="card-header">Ghi chú cho gia sư</div>
                <div class="card-content">
                    <div class="location-input-wrapper">
                    </div>
                    <div class="note-section">
                        <base-input v-model="bookingData.note" type="textarea" placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt cho gia sư..." size="large" />
                        <div class="note-helper">Hãy cung cấp thông tin chi tiết về nhu cầu học tập để gia sư có thể chuẩn bị tốt nhất.</div>
                    </div>
                </div>
            </div>
            <div class="action">
                <button class="btn-md border-r-2 btn-secondary" @click="handleBack"><i class="fas fa-arrow-left"></i> Quay lại</button>

                <button class="btn-md border-r-2 btn-primary" @click="handleNext" :disabled="isSelfBooking" v-if="!checkModeIsTrial">
                    Tiếp tục <i class="fas fa-arrow-right"></i>
                </button>

                <button class="btn-md border-r-2 btn-primary" @click="handleNext" :disabled="isSelfBooking" v-else>
                    Đăng ký lịch học
                </button>

                <div v-if="isSelfBooking" class="alert alert-danger mt-2">Bạn không thể đặt lịch với chính mình!</div>
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
                        <span class="order-value">{{ proxy.$helper.formatCurrency(hourlyPrice) }}</span>
                    </div>
                    <div class="order-row">
                        <span class="order-label">Thời lượng</span>
                        <span class="order-value">{{ $helper.formatDuration(lessonDurationHours) }}</span>
                    </div>
                    <div class="order-total">
                        <span class="order-label">Tổng cộng</span>
                        <span class="order-value total">{{ proxy.$helper.formatCurrency(totalPrice) }}</span>
                    </div>
                    <div class="order-benefit-box">
                        <ul class="order-benefits">
                            <li v-for="item in orderBenefits" :key="item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-check" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <path d="m9 11 2 2 4-4"></path>
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

<base-modal :isOpen="uiState.showEditModal" title="Chỉnh sửa thông tin buổi học" @close="handleModalClose" size="medium">
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
                <div class="session-list">
                    <div v-for="(s, idx) in tutorSessionOptions" :key="s.id" class="session-item" :class="{ selected: activeTutorSessionId === s.id }" @click="onSelectTutorSession(s.id)">
                        <div class="left-icon" :class="{ active: activeTutorSessionId === s.id }">
                            <svg v-if="s.recommended" class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 7v14" />
                                <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" /></svg>
                            <svg v-else class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" /></svg>
                        </div>
                        <div class="content">
                            <div class="title-row">
                                <div class="title">{{ s.name }}</div>
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
                        <div class="right-radio">
                            <span class="radio" :class="{ checked: activeTutorSessionId === s.id }"></span>
                        </div>
                    </div>
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
</style>

<script setup>
import {
    defineEmits,
    ref,
    onMounted,
    computed,
    watch
} from 'vue'
import {
    useRouter
} from 'vue-router'
import BaseModal from '@/components/BaseModal.vue'
import {
    useStore
} from 'vuex'
import BaseSelect from '@/components/BaseSelect.vue'
import BaseInput from '@/components/BaseInput.vue'
import moment from 'moment'

const router = useRouter();
const store = useStore();
const myUid = computed(() => store.state.auth?.user?.uid || localStorage.getItem('uid'));
const isSelfBooking = computed(() => props.tutorInfo?.uid === myUid.value);

const props = defineProps({
    tutorInfo: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['next', 'back'])
const handleNext = () => emit('next')
const handleBack = () => emit('back')

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
    studyLocation: null,
    transportationFee: null,
    maxDistance: null,
    note: ''
})

const orderBenefits = [
    // "Thanh toán trực tiếp cho gia sư tại nơi học",
    "Đảm bảo hoàn tiền nếu không hài lòng",
    "Hỗ trợ 24/7 từ đội ngũ EduTutor"
]

const showEditModal = ref(true);
const isInitializing = ref(false);

const subjectOptions = computed(() =>
    props.tutorInfo?.user_subjects?.map(s => ({
        id: s.subject_id,
        name: s.subject_name
    })) || []
);

const allLevelOptions = computed(() => {
    let arr = [];
    props.tutorInfo?.user_subjects?.forEach(s => {
        (s.user_subject_levels || []).forEach(lvl => {
            arr.push({
                id: lvl.education_level_id,
                name: lvl.education_level,
                subject_id: s.subject_id
            });
        });
    });
    return arr;
});

const timeOptions = computed(() => store.state.configuration.timeSlots.map(t => ({
    id: t.id,
    name: t.name
})))

const locationType = ref((store.state.configuration.studyLocations && store.state.configuration.studyLocations[0]?.id) || null);

const editForm = ref({
    subjectId: '',
    levelId: '',
    date: '',
    startTime: '',
    endTime: ''
});

const editLevelOptions = computed(() =>
    allLevelOptions.value.filter(l => l.subject_id === editForm.value.subjectId)
);

const onEditSubjectChange = (newSubjectId) => {
    editForm.value.subjectId = newSubjectId;
    editForm.value.levelId = '';
    editForm.value.startTime = '';
    editForm.value.endTime = '';
};

const onEditLevelChange = (newLevelId) => {
    editForm.value.levelId = newLevelId;
    editForm.value.startTime = '';
    editForm.value.endTime = '';
};

const onEditStartTimeChange = (newTime) => {
    editForm.value.startTime = newTime;
    editForm.value.endTime = '';
};

const onEditEndTimeChange = (newTime) => {
    editForm.value.endTime = newTime;
};

const openEditModal = () => {
    isInitializing.value = true;
    editForm.value.subjectId = bookingData.value.subject.id;
    editForm.value.levelId = bookingData.value.level.id;
    editForm.value.date = bookingData.value.date;
    editForm.value.startTime = bookingData.value.time?.start || '';
    editForm.value.endTime = bookingData.value.time?.end || '';
    showEditModal.value = true;
    setTimeout(() => {
        isInitializing.value = false;
    }, 0);
};

const calculatePrices = () => {
    // Calculate hourly price
    const selectedSubjectData = props.tutorInfo.user_subjects.find(
        s => s.subject_id === bookingData.value.subject.id
    );
    if (selectedSubjectData) {
        const selectedLevelData = selectedSubjectData.user_subject_levels.find(
            lvl => lvl.education_level_id === bookingData.value.level.id
        );
        if (selectedLevelData) {
            bookingData.value.hourly_price = selectedLevelData.price;
        }
    }

    // Calculate total price
    if (bookingData.value.time.start && bookingData.value.time.end) {
        const startTime = moment(bookingData.value.time.start, 'HH:mm');
        const endTime = moment(bookingData.value.time.end, 'HH:mm');
        if (startTime.isValid() && endTime.isValid() && !endTime.isBefore(startTime)) {
            const duration = moment.duration(endTime.diff(startTime));
            const hours = duration.asHours();
            bookingData.value.total_price = bookingData.value.hourly_price * hours;
        }
    }
};

const handleEditSave = () => {
    // Validate form before saving
    if (!isFormValid.value) {
        showValidationErrors.value = true;
        return;
    }

    const selectedSubject = subjectOptions.value.find(s => s.id === editForm.value.subjectId);
    const selectedLevel = allLevelOptions.value.find(l => l.id === editForm.value.levelId);
    const startTimeObj = timeOptions.value.find(t => t.id === editForm.value.startTime);
    const endTimeObj = timeOptions.value.find(t => t.id === editForm.value.endTime);

    bookingData.value.subject = {
        id: editForm.value.subjectId,
        name: selectedSubject?selectedSubject.name : ''
    };
    bookingData.value.level = {
        id: editForm.value.levelId,
        name: selectedLevel?selectedLevel.name : ''
    };
    bookingData.value.date = editForm.value.date;
    bookingData.value.time = {
        start: editForm.value.startTime,
        end: editForm.value.endTime,
        display: (startTimeObj && endTimeObj)?`${startTimeObj.name} - ${endTimeObj.name}` : ''
    };

    calculatePrices();

    sessionStorage.setItem('bookingData', JSON.stringify(bookingData.value));

    showEditModal.value = false;
};

const hourlyPrice = computed(() => {
    return bookingData.value.hourly_price || 0;
});

const lessonDurationHours = computed(() => {
    if (!bookingData.value.time.start || !bookingData.value.time.end) {
        return 0;
    }
    const startTime = moment(bookingData.value.time.start, 'HH:mm');
    const endTime = moment(bookingData.value.time.end, 'HH:mm');

    if (!startTime.isValid() || !endTime.isValid() || endTime.isBefore(startTime)) {
        return 0;
    }

    const duration = moment.duration(endTime.diff(startTime));
    return duration.asHours();
});

const totalPrice = computed(() => {
    return bookingData.value.total_price || 0;
});

watch(bookingData, (newData) => {
    sessionStorage.setItem('bookingData', JSON.stringify(newData));
}, {
    deep: true
});

// Get day of week from date (0 = Sunday, 1 = Monday, etc.)
const getDayOfWeek = (date) => {
    if (!date) return null;
    const day = moment(date).day();
    // Convert to match your user_weekly_time_slots format (assuming 1 = Monday, 7 = Sunday)
    return day === 0?7 : day;
};

const availableEditTimeSlots = computed(() => {
    if (!editForm.value.date || !props.tutorInfo?.user_weekly_time_slots) {
        return [];
    }
    const dayOfWeek = getDayOfWeek(editForm.value.date);
    if (dayOfWeek === null) return [];

    return props.tutorInfo.user_weekly_time_slots
        .filter(slot => slot.day_of_week_id == dayOfWeek);
});

// Helper: compare time strings 'HH:mm'
function compareTime(a, b) {
    if (!a || !b) return 0;
    return a.localeCompare(b);
}

const validEditStartTimes = computed(() => {
    if (!editForm.value.date || !availableEditTimeSlots.value.length) return timeOptions.value.map(t => ({
        ...t,
        disabled: true
    }));

    return timeOptions.value.map(point => {
        const isAvailable = availableEditTimeSlots.value.some(slot =>
            compareTime(point.name, slot.time_slot_start_name) >= 0 &&
            compareTime(point.name, slot.time_slot_end_name) < 0
        );
        return {
            ...point,
            disabled: !isAvailable
        };
    });
});

const validEditEndTimes = computed(() => {
    if (!editForm.value.startTime) return timeOptions.value.map(t => ({
        ...t,
        disabled: true
    }));

    const startLabel = timeOptions.value.find(t => t.id === editForm.value.startTime)?.name;
    const containingSlot = availableEditTimeSlots.value.find(slot =>
        compareTime(startLabel, slot.time_slot_start_name) >= 0 &&
        compareTime(startLabel, slot.time_slot_end_name) < 0
    );

    if (!containingSlot) return timeOptions.value.map(t => ({
        ...t,
        disabled: true
    }));

    return timeOptions.value.map(point => {
        const afterStart = compareTime(point.name, startLabel) > 0;
        const inSlot = compareTime(point.name, containingSlot.time_slot_start_name) > 0 &&
            compareTime(point.name, containingSlot.time_slot_end_name) <= 0;

        return {
            ...point,
            disabled: !(afterStart && inSlot)
        };
    });
});
watch(() => editForm.value.date, (newDate) => {
    if (newDate && !isInitializing.value) {
        editForm.value.startTime = '';
        editForm.value.endTime = '';
    }
});

watch(() => editForm.value.startTime, (newStartTime) => {
    if (newStartTime && !isInitializing.value) {
        editForm.value.endTime = '';
    }
});

// Hide validation errors when user starts filling the form
watch(editForm, () => {
    if (showValidationErrors.value && isFormValid.value) {
        showValidationErrors.value = false;
    }
}, {
    deep: true
});

const locationTypes = computed(() => {
    const studyLocations = store.state.configuration.studyLocations || [];
    const tutorStudyLocations = props.tutorInfo?.user_study_locations || [];
    const tutorLocationIds = tutorStudyLocations.map(loc => loc.study_location_id);

    return studyLocations
        .filter(loc => tutorLocationIds.includes(loc.id))
        .map(loc => {
            const tutorLocation = tutorStudyLocations.find(tl => tl.study_location[0].id === loc.id);

            return {
                value: loc.id,
                label: loc.name?.trim(),
                transportation_fee: tutorLocation.transportation_fee,
                max_distance: tutorLocation.max_distance,
                description: loc.description,
                icon: loc.icon,
                home_tutor: loc.home_tutor,
                home_user: loc.home_user,
            };
        });
});

// Update watcher for locationType to handle new logic
watch(locationType, (val) => {
    // Find the selected location type from store
    const selected = locationTypes.value.find(t => t.value === val);
    console.log(selected);
    if (selected) {
        const studyLoc = (store.state.configuration.studyLocations || []).find(l => l.id === val);
        const tutorLocation = props.tutorInfo?.user_study_locations?.find(
            tl => tl.study_location_id === val
        );

        if (studyLoc?.home_user) {
            if (tutorLocation) {
                bookingData.value.transportationFee = tutorLocation.transportation_fee;
                bookingData.value.maxDistance = tutorLocation.max_distance;
            }
        }
        // Save the study location object to bookingData
        bookingData.value.studyLocation = studyLoc;
    }
});

onMounted(() => {
    const storedData = sessionStorage.getItem('bookingData')
    if (storedData) {
        bookingData.value = JSON.parse(storedData);
        if (bookingData.value.studyLocation) {
            locationType.value = bookingData.value.studyLocation.id;
        }
        calculatePrices();
    }
})

const isStudentHome = (type) => {
    const studyLoc = (store.state.configuration.studyLocations || []).find(l => l.id === type.value);
    return !!studyLoc?.home_user;
}

const dataIsNull = (value) => {
    if (value != null) {
        if (value.length == 0) {
            return "Chưa có dữ liệu";
        }
    }
    if (value == null) {
        return "Chưa có dữ liệu";
    }
    return value;
}

const isFormValid = computed(() => {
    return editForm.value.subjectId &&
        editForm.value.levelId &&
        editForm.value.date &&
        editForm.value.startTime &&
        editForm.value.endTime;
});

const showValidationErrors = ref(false);

const validateField = (fieldName) => {
    if (showValidationErrors.value) {
        return !editForm.value[fieldName];
    }
    return false;
};

const handleModalClose = () => {
    if (isFormValid.value) {
        showEditModal.value = false;
    } else {
        // Show validation errors
        showValidationErrors.value = true;
    }
};
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
                                    <span class="sub-title">{{ dataIsNull(bookingData.subject.name) }}</span>
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
                                    <span class="sub-title">{{ dataIsNull(bookingData.level.name) }}</span>
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
                                    <span class="sub-title">{{ dataIsNull($helper.formatDate(bookingData.date)) }}</span>
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
                                    <span class="sub-title">{{ dataIsNull(bookingData.time.display) }}</span>
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
                                            {{ bookingData.studyLocation.name }}
                                        </template>
                                        <template v-else>
                                            Chưa cập nhật
                                        </template>
                                    </span>
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
                                <!-- Hiển thị input nhập địa chỉ khi chọn tại nhà học viên -->
                                <template v-if="isStudentHome(type) && locationType === type.value">
                                    <div class="student-address-block">
                                        <label class="address-label">Địa chỉ của bạn <span class="required">*</span></label>
                                        <BaseInput v-model="bookingData.location" placeholder="Nhập địa chỉ đầy đủ (số nhà, đường, phường/xã, quận/huyện)" size="medium" />
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
                            <BaseInput v-model="bookingData.note" type="textarea" placeholder="Nhập ghi chú hoặc yêu cầu đặc biệt cho gia sư..." size="large" />
                            <div class="note-helper">Hãy cung cấp thông tin chi tiết về nhu cầu học tập để gia sư có thể chuẩn bị tốt nhất.</div>
                        </div>
                    </div>
                </div>
                <div class="action">
                    <button class="btn-md border-r-2 btn-secondary" @click="handleBack"><i class="fas fa-arrow-left"></i> Quay lại</button>
                    <button class="btn-md border-r-2 btn-primary" @click="handleNext" :disabled="isSelfBooking">Tiếp tục <i class="fas fa-arrow-right"></i></button>
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
                            <span class="order-value">{{ $helper.formatCurrency(hourlyPrice) }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Thời lượng</span>
                            <span class="order-value">{{ lessonDurationHours }} giờ</span>
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

<BaseModal :isOpen="showEditModal" title="Chỉnh sửa thông tin buổi học" @close="handleModalClose" size="large">
    <form @submit.prevent="handleEditSave">
        <div class="edit-grid">
            <base-datepicker v-model="editForm.date" class="col-span-2" />
            <div class="separation col-span-2"></div>

            <div class="form-group">
                <BaseSelect v-model="editForm.subjectId" :options="subjectOptions" label="Môn học" placeholder="Chọn môn học" @update:modelValue="onEditSubjectChange" :required="true" :error="validateField('subjectId')" error-message="Vui lòng chọn môn học" />
            </div>
            <div class="form-group">
                <BaseSelect v-model="editForm.levelId" :options="editLevelOptions" label="Cấp độ" placeholder="Chọn cấp độ" :disabled="!editForm.subjectId" @update:modelValue="onEditLevelChange" :required="true" :error="validateField('levelId')" error-message="Vui lòng chọn cấp độ" />
            </div>
            <div class="form-group">
                <BaseSelect v-model="editForm.startTime" :options="validEditStartTimes" label="Giờ bắt đầu" placeholder="Chọn giờ bắt đầu" :disabled="!editForm.date || availableEditTimeSlots.length === 0" @update:modelValue="onEditStartTimeChange" :required="true" :error="validateField('startTime')" error-message="Vui lòng chọn giờ bắt đầu" />
            </div>
            <div class="form-group">
                <BaseSelect v-model="editForm.endTime" :options="validEditEndTimes" label="Giờ kết thúc" placeholder="Chọn giờ kết thúc" :disabled="!editForm.startTime" @update:modelValue="onEditEndTimeChange" :required="true" :error="validateField('endTime')" error-message="Vui lòng chọn giờ kết thúc" />
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-md btn-primary btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</BaseModal>
</template>

<style scoped>
@import url("@css/BookingCommon.css");

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

.date-input-modal {
    position: relative;
    display: flex;
    align-items: center;
}

.icon-calendar {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
    z-index: 1;
}

.date-input-modal input[type="date"] {
    padding-left: 2.8rem;
}

.date-picker-input {
    width: 100%;
    padding: 0.625rem 0.75rem 0.625rem 2rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    font-size: var(--font-size-mini);
    color: #374151;
    cursor: pointer;
    background: #fff;
}

.date-picker-input::-webkit-calendar-picker-indicator {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

/* Adjust styles for BaseSelect within the modal */
.base-select.form-control-lg .select-button {
    padding: 0.9rem 1.1rem;
    border: 1.5px solid #e5e7eb;
    /* Use border from edit-input */
    border-radius: 10px;
    /* Use border-radius from edit-input */
    background: #fff;
    /* Ensure white background */
}

/* Ensure dropdown position is correct */
.base-select.form-control-lg .select-dropdown {
    top: 100%;
    left: 0;
    right: 0;
    margin-top: 4px;
    /* Standard spacing below the button */
}

/* Error states are now handled by BaseSelect component */

.validation-summary {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 1rem;
    color: #ef4444;
    font-size: var(--font-size-mini);
}

.location-type-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.location-type-option {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.1rem 1.2rem;
    cursor: pointer;
    transition: border 0.2s;
    position: relative;
}

.location-type-option.selected {
    border: 1.5px solid #18181b;
    background: #f4f4f5;
}

.location-type-option.selected .option-icon {
    background: #18181b33;
}

.location-type-option .right-content {
    display: flex;
    align-items: center;
}

.location-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-icon {
    display: flex;
    align-items: center;
    margin-right: 0.7rem;
    background: #f3f4f6;
    border-radius: 2rem;
    width: 40px;
    height: 40px;
    justify-content: center;
}

.option-label {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.1rem;
}

.option-label .label {
    font-weight: 600;
    font-size: var(--font-size-heading-6);
}

.option-radio {
    display: flex;
    align-items: center;
    margin-left: 1rem;
}

.option-radio input[type="radio"] {
    accent-color: #18181b;
    width: 1rem;
    height: 1rem;
}

.popular-badge {
    background: #22c55e;
    color: #fff;
    font-size: 0.8rem;
    border-radius: 2rem;
    padding: 0.1rem 0.5rem;
    margin-left: 0.5rem;
}

.extra-badge {
    border: 1px solid #d97706;
    color: #d97706;
    background: #fffbeb;
    font-size: var(--font-size-mini);
    border-radius: 2rem;
    padding: 0.1rem 0.5rem;
    margin-left: 0.5rem;
}

.option-desc {
    color: #6b7280;
    font-size: 0.98rem;
}

.student-address-block {
    display: grid;
    gap: 0.5rem;
    margin-top: 1.2rem;
}

.address-label {
    display: flex;
    align-items: center;
    font-weight: 500;
    font-size: var(--font-size-heading-6);
    color: #18181b;
    display: block;
}

.required {
    color: red;
    font-size: 1.1em;
}

.address-warning {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    color: #d97706;
    font-size: var(--font-size-mini);
}
</style>

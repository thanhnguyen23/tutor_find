<script setup>
import {
    ref,
    computed,
    watch,
    getCurrentInstance
} from 'vue';

import { useStore } from 'vuex';

// Props
const props = defineProps({
    userDataDetail: {
        type: Object,
        required: true
    }
});

const {proxy} = getCurrentInstance();
const emit = defineEmits(['update-data']);

const store = useStore();

const form = ref({
    first_name: '',
    last_name: '',
    sex: '',
    birth_year: '',
    phone: '',
    email: '',
    address: '',
    about_you: '',
    cccd: '',
    referral_link: '',
    free_trial: false,
    tutor_session_id: '',
    provinces_id: '',
    districts_id: '',
    wards_id: '',
    address_detail: '',
});

const showProfileDescriptionModal = ref(false);
const showPersonalInfoModal = ref(false);
const showAddressModal = ref(false);
const showCCCDModal = ref(false);
const showVideoModal = ref(false);
const showFreeTrialModal = ref(false);
const isLoading = ref(false);

const genderOptions = [
    { name: 'Nam', value: 1 },
    { name: 'Nữ', value: 2 },
];

const tutorSessionOptions = computed(() => {
    return (store.state.configuration.tutorSessions || []).filter(session => session.allow_free)
});
const provinceOptions = computed(() => {
    return (store.state.configuration.provinces)
});
const districtOptions = computed(() => {
    if (!form.value.provinces_id) return [];
    return (store.state.configuration.districts || []).filter(d => d.province_id == form.value.provinces_id);
});
const wardOptions = computed(() => {
    if (!form.value.districts_id) return [];
    return (store.state.configuration.wards || []).filter(w => w.district_id == form.value.districts_id);
});

// Load user profile data from props
const loadUserProfile = () => {
    const userData = props.userDataDetail;
    form.value = {
        first_name: userData.first_name,
        last_name: userData.last_name,
        sex: userData.sex,
        birth_year: '',
        phone: userData.phone || '',
        email: userData.email || '',
        address: userData.address || '',
        cccd: userData.cccd || '',
        referral_link: userData.referral_link,
        free_trial: userData.is_free_study,
        tutor_session_id: userData.tutor_session_id || '',
        about_you: userData.about_you,
        provinces_id: userData.provinces_id || '',
        districts_id: userData.districts_id || '',
        wards_id: userData.wards_id || '',
        address_detail: userData.address || '',
    };
    // No CCCD image handling
};

const saveProfile = async () => {
    isLoading.value = true;
    try {
        const formData = new FormData();
        formData.append('first_name', form.value.first_name);
        formData.append('last_name', form.value.last_name);
        formData.append('phone', form.value.phone);
        formData.append('email', form.value.email);
        formData.append('address', form.value.address);
        formData.append('cccd', form.value.cccd);
        formData.append('referral_link', form.value.referral_link);
        formData.append('sex', form.value.sex ? 1 : 0);
        formData.append('is_free_study', form.value.free_trial ? 1 : 0);
        formData.append('tutor_session_id', form.value.tutor_session_id);
        formData.append('about_you', form.value.about_you);
        formData.append('provinces_id', form.value.provinces_id);
        formData.append('districts_id', form.value.districts_id);
        formData.append('wards_id', form.value.wards_id);
        formData.append('address', form.value.address_detail);

        // Remove CCCD image fields
        const response = await proxy.$api.apiPostFormData('me/profile-info', formData);
        if (response.data) {
            emit('update-data', response.data);
            proxy.$notification.success('Cập nhật hồ sơ thành công!');
        } else {
            proxy.$notification.error('Cập nhật hồ sơ thất bại!');
        }

        showPersonalInfoModal.value = false;
        showAddressModal.value = false;
        showCCCDModal.value = false;
        showVideoModal.value = false;
        showFreeTrialModal.value = false;
    } catch (error) {
        proxy.$notification.error(error.message || 'Có lỗi xảy ra khi lưu hồ sơ!');
    } finally {
        isLoading.value = false;
    }
};

// Remove CCCD image handlers

watch(() => props.userDataDetail, () => {
    loadUserProfile();
}, { immediate: true });

const youtubeEmbedUrl = computed(() => {
    if (!form.value.referral_link) return '';
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([\w-]{11})/;
    const match = form.value.referral_link.match(regex);
    if (match && match[1]) {
        return `https://www.youtube.com/embed/${match[1]}`;
    }
    return '';
});

const getCompletionStatus = (status) => {
    if (status === true) return 'Đã hoàn thiện';
    return 'Chưa hoàn thiện';
};

const getCompletionLabel = (key) => {
    const labels = {
        'personal_info': 'Thông tin cá nhân',
        'subjects': 'Môn học',
        'education': 'Học vấn',
        'experience': 'Kinh nghiệm',
        'schedule': 'Lịch trình',
        'languages': 'Gói dịch vụ',
        'study_form': 'Hình thức học'
    };
    return labels[key] || key;
};

// Trạng thái hiển thị dưới tên hồ sơ
const profileStatusText = computed(() => {
    const completedFlag = props.userDataDetail?.profile_completed ?? 0;
    const percent = props.userDataDetail?.profile_completion?.percent ?? 0;
    if (completedFlag === 1) return 'Đã xác thực';
    if (completedFlag === 0 && percent === 100) return 'Chờ xác thực';
    return 'Chưa hoàn thiện';
});

const getSelectedSessionName = () => {
    // First try to get from userDataDetail if available
    if (props.userDataDetail?.tutor_session?.time) {
        return props.userDataDetail.tutor_session.time;
    }
    // Fallback to tutorSessionOptions
    const selectedSession = tutorSessionOptions.value.find(session => session.id == form.value.tutor_session_id);
    return selectedSession ? selectedSession.time : '';
};

const formatDuration = (hours) => {
    if (!hours) return '0 phút';
    const totalMinutes = Math.round(hours * 60);
    if (totalMinutes < 60) {
        return `${totalMinutes} phút`;
    }
    const hourPart = Math.floor(totalMinutes / 60);
    const minutePart = totalMinutes % 60;
    if (minutePart === 0) {
        return `${hourPart} giờ`;
    }
    return `${hourPart} giờ ${minutePart} phút`;
};

// Validation computed properties for each modal
const isPersonalInfoValid = computed(() => {
    return form.value.first_name &&
           form.value.last_name &&
           form.value.sex &&
           form.value.phone &&
           form.value.email;
});

const isProfileDescriptionValid = computed(() => {
    return form.value.about_you && form.value.about_you.trim().length > 0;
});

const isVideoIntroValid = computed(() => {
    return form.value.referral_link && form.value.referral_link.trim().length > 0;
});

const isFreeTrialValid = computed(() => {
    if (!form.value.free_trial) return true; // If free trial is disabled, it's valid
    return form.value.tutor_session_id; // If enabled, must select a session
});
</script>

<template>
<!-- Loading overlay -->
<base-loading v-if="isLoading" />

<div class="profile-wrapper" v-if="!isLoading">
    <div class="profile-sidebar">
        <div class="profile-info section-card">
            <div class="avatar-main main-content">
                <div class="change-image">
                    <img :src="userDataDetail.avatar || 'https://kzmkh1q5oqyh8rwll452.lite.vusercontent.net/placeholder.svg?height=96&width=96'" alt="Avatar">
                    <div class="button-change">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z"></path><circle cx="12" cy="13" r="3"></circle></svg>
                    </div>
                </div>
                <div class="content">
                    <span class="full-name">{{ userDataDetail.full_name || 'Chưa cập nhật' }}</span>
                    <!-- <span class="role-badge">{{ userDataDetail.role || 'Gia sư' }}</span> -->
                    <div class="profile-status">
                        {{ profileStatusText }}
                    </div>
                    <div class="evaluate">
                        <div class="stars">
                            <svg class="icon-lg" v-for="i in 5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#f9ce69" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(249, 206, 105);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        </div>
                        <span>4.8</span>
                        <span>(100)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-card profile-completion-card">
            <div class="header-wrapper">
                <div class="header-left">
                    <div class="title-wrapper">
                        <span class="title-main">
                            <div class="icon-wrapper">
                                <svg class="icon-xl" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                            </div>
                            Mức độ hoàn thiện hồ sơ
                        </span>
                        <span class="sub-title">Để hồ sơ của bạn xuất hiện với học sinh, vui lòng hoàn tất các thông tin cần thiết</span>
                    </div>
                </div>
            </div>
            <div class="main-content">
                <div class="completion-progress-wrapper">
                    <div class="completion-circle">
                        <svg width="120" height="120">
                            <circle cx="60" cy="60" r="54" stroke="#e5e7eb" stroke-width="12" fill="none"/>
                            <circle
                                cx="60"
                                cy="60"
                                r="54"
                                :stroke="userDataDetail.profile_completion?.percent === 100 ? '#097ed7' : '#6366f1'"
                                stroke-width="12"
                                fill="none"
                                :stroke-dasharray="339.292"
                                :stroke-dashoffset="339.292 - (userDataDetail.profile_completion?.percent || 0) / 100 * 339.292"
                                stroke-linecap="round"
                                transform="rotate(-90 60 60)"
                            />
                            <defs>
                                <linearGradient id="gradient" x1="0" y1="0" x2="120" y2="0" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#6366f1"/>
                                    <stop offset="1" stop-color="#a5b4fc"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="percent-text">
                            {{ userDataDetail.profile_completion?.percent || 0 }}%
                        </div>
                    </div>
                    <div class="completion-status">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                        <span>{{ Object.values(userDataDetail.profile_completion?.details || {}).filter(Boolean).length }}/{{ Object.keys(userDataDetail.profile_completion?.details || {}).length }} mục đã hoàn thiện</span>
                    </div>
                </div>
                <div class="label-completion-details">Chi tiết hoàn thiện:</div>
                <div class="completion-details-grid">
                    <div
                        v-for="(status, key) in userDataDetail.profile_completion?.details"
                        :key="key"
                        class="completion-detail-card"
                        :class="{ completed: status }"
                    >
                        <div class="icon">
                            <template v-if="key === 'personal_info'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </template>
                            <template v-else-if="key === 'subjects'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                            </template>
                            <template v-else-if="key === 'education'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path><path d="M22 10v6"></path><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path></svg>
                            </template>
                            <template v-else-if="key === 'experience'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>
                            </template>
                            <template v-else-if="key === 'schedule'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                            </template>
                            <template v-else-if="key === 'languages'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                            </template>
                            <template v-else-if="key === 'study_form'">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </template>
                        </div>
                        <div class="content">
                            <div class="label">{{ getCompletionLabel(key) }}</div>
                            <div class="status" :class="{ done: status }">
                                {{ getCompletionStatus(status) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="userDataDetail.profile_completion?.percent === 100" class="completion-success-box">
                    <div class="icon">
                        <svg class="icon-lg" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <div class="content">
                        <span>Xuất sắc!</span>
                        <span>Hồ sơ của bạn đã được hoàn thiện 100%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="section-all">
        <div class="section-card heading-card" @click="showProfileDescriptionModal = true">
            <div class="header-wrapper">
                <div class="header-left">
                    <div class="icon-wrapper">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                    </div>
                    <div class="title-wrapper">
                        <span class="title-main">Mô tả hồ sơ</span>
                        <span class="sub-title">Thông tin này sẽ được đưa vào hồ sơ công khai của bạn</span>
                    </div>
                </div>
            </div>
            <div class="section-content_preview">
                <span>{{ form.about_you ? form.about_you.substring(0, 40) + (form.about_you.length > 40 ? '...' : '') : 'Vui lòng click vào để chỉnh sửa các thông tin' }}</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
        </div>

        <div class="section-card infomation-user" @click="showPersonalInfoModal = true">
            <div class="header-wrapper">
                <div class="header-left">
                    <div class="icon-wrapper">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div class="title-wrapper">
                        <span class="title-main">Thông Tin Cá Nhân</span>
                        <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
                    </div>
                </div>
            </div>
            <div class="section-content_preview">
                <span>{{ form.first_name || form.last_name ? (form.first_name + ' ' + form.last_name) : 'Vui lòng click để nhập thông tin cá nhân' }}</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
        </div>
        <base-modal :is-open="showPersonalInfoModal" title="Thông Tin Cá Nhân" @close="showPersonalInfoModal = false">
            <div class="form-grid">
                <div class="form-group">
                    <base-input v-model="form.first_name" required="true" label="Họ" placeholder="Nguyễn" />
                </div>
                <div class="form-group">
                    <base-input v-model="form.last_name" required="true" label="Tên" placeholder="Văn A" />
                </div>
                <div class="form-group">
                    <base-select v-model="form.sex" required="true" :options="genderOptions" label="Giới tính" placeholder="Chọn giới tính" />
                </div>
                <div class="form-group">
                    <base-input v-model="form.phone" required="true" label="Số điện thoại" placeholder="0123456789" />
                </div>
                <div class="form-group">
                    <base-input v-model="form.email" required="true" label="Email" placeholder="example@email.com" type="email" />
                </div>
                <div class="form-group">
                    <base-input v-model="form.cccd" label="Số CCCD" placeholder="001234567890" />
                </div>
                <div class="form-group">
                    <base-select v-model="form.provinces_id" :options="provinceOptions" label="Tỉnh/Thành phố" placeholder="Chọn tỉnh/thành phố" />
                </div>
                <div class="form-group">
                    <base-select v-model="form.districts_id" :options="districtOptions" label="Quận/Huyện" placeholder="Chọn quận/huyện" :disabled="!form.provinces_id" />
                </div>
                <div class="form-group">
                    <base-select v-model="form.wards_id" :options="wardOptions" label="Phường/Xã" placeholder="Chọn phường/xã" :disabled="!form.districts_id" />
                </div>
                <div class="form-group">
                    <base-input v-model="form.address_detail" label="Địa chỉ chi tiết" placeholder="Số nhà, tên đường..." />
                </div>
            </div>
            <div class="note-group">
                <div class="note-wrapper">
                    <div class="note-wrapper_header">
                        <i class="feather feather-check">
                            <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </i>
                        <span>Nên làm</span>
                    </div>
                    <span>Ghi rõ địa chỉ (khu vực dạy), có thể kèm hình thức linh hoạt → Ví dụ: Nhận dạy tại Q.3, Q.10 (TP.HCM)</span>
                </div>

                <div class="note-wrapper">
                    <div class="note-wrapper_header">
                        <i class="feather feather-x">
                            <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </i>
                        <span>Không nên làm</span>
                    </div>
                    <span>Ghi mơ hồ, không rõ khu vực: “ở gần trung tâm”, “ở xa thì tùy”. Viết thiếu nghiêm túc: “dạy đâu cũng được miễn có tiền”, “nhà ở đâu thì dạy đó”</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-lg btn-primary border-r-2" @click="saveProfile" :disabled="!isPersonalInfoValid">
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </base-modal>



        <div class="section-card video-intro" @click="showVideoModal = true">
            <div class="header-wrapper">
                <div class="header-left">
                    <div class="icon-wrapper">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"></path>
                            <rect x="2" y="6" width="14" height="12" rx="2"></rect>
                        </svg>
                    </div>
                    <div class="title-wrapper">
                        <span class="title-main">Video Giới Thiệu</span>
                        <span class="sub-title">Link video YouTube giới thiệu bản thân</span>
                    </div>
                </div>
            </div>
            <div class="section-content_preview">
                <span>{{ form.referral_link ? form.referral_link : 'Vui lòng click để nhập link video' }}</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
        </div>
        <base-modal :is-open="showVideoModal" title="Video Giới Thiệu" @close="showVideoModal = false">
            <div class="main-content">
                <div class="form-group full-width">
                    <base-input v-model="form.referral_link" label="Link YouTube *" placeholder="https://www.youtube.com/watch?v=..." />
                </div>
                <div v-if="youtubeEmbedUrl" class="youtube-preview">
                    <iframe :src="youtubeEmbedUrl" width="100%" height="315" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="note-group">
                <div class="note-wrapper">
                    <div class="note-wrapper_header">
                        <i class="feather feather-check">
                            <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        </i>
                        <span>Nên làm</span>
                    </div>
                    <span>Video nên dài 2-5 phút, giới thiệu về bản thân, kinh nghiệm và phương pháp giảng dạy, cam kết với học sinh và phụ huynh</span>
                </div>

                <div class="note-wrapper">
                    <div class="note-wrapper_header">
                        <i class="feather feather-x">
                            <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </i>
                        <span>Không nên làm</span>
                    </div>
                    <span>Video quá dài giới thiệu lan man về những thứ không liên quan ví dụ: nơi ở, học phí...</span>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-lg btn-primary border-r-2" @click="saveProfile" :disabled="!isVideoIntroValid">
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </base-modal>

        <base-modal :is-open="showProfileDescriptionModal" title="Mô tả hồ sơ" @close="showProfileDescriptionModal = false">
            <div class="form-grid">
                <div class="form-group">
                    <base-input v-model="form.about_you" type="textarea" rows="6" required="true" label="1. Giới thiệu bản thân" placeholder="Xin chào tôi tên là..." />
                    <div class="tips">
                        <span class="tips_header">Mẹo</span>
                        <span>Tiêu đề của bạn chính là tiêu đề cho quảng cáo! Hãy sử dụng nó để làm nổi bật kinh nghiệm và điểm khác biệt của bạn! Bạn không cần phải nêu rõ địa điểm và giá cả vì những thông tin này sẽ được đề cập ở nơi khác. Bạn là gia sư, vì vậy hãy kiểm tra chính tả và ngữ pháp, đồng thời tránh sử dụng từ viết tắt!</span>
                    </div>
                    <div class="note-group">
                        <div class="note-wrapper">
                            <div class="note-wrapper_header">
                                <i class="feather feather-check">
                                    <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                </i>
                                <span>Nên làm</span>
                            </div>
                            <span>Tốt nghiệp học viện âm nhạc và là ca sĩ, giảng dạy thanh nhạc và guitar cho mọi trình độ. Phương pháp giảng dạy thoải mái!</span>
                        </div>

                        <div class="note-wrapper">
                            <div class="note-wrapper_header">
                                <i class="feather feather-x">
                                    <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </i>
                                <span>Không nên làm</span>
                            </div>
                            <span>Dạy hát và guitar ở New York với giá 40 đô la một giờ.</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-lg btn-primary border-r-2" @click="saveProfile" :disabled="!isProfileDescriptionValid">
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </base-modal>

        <base-modal :is-open="showFreeTrialModal" title="Buổi Học Miễn Phí" @close="showFreeTrialModal = false">
            <div class="main-content">
                <div class="switch-group">
                    <div>
                        <span>Bật buổi học miễn phí</span>
                        <br>
                        <span class="switch-desc">Cho phép học sinh học thử miễn phí buổi đầu tiên</span>
                    </div>
                    <div>
                        <input type="checkbox" id="freeTrial" class="switch-input" v-model="form.free_trial" />
                        <label for="freeTrial" class="switch-label"></label>
                    </div>
                </div>
                <div v-if="form.free_trial" class="select-session select-time">
                    <div class="form-group">
                        <label class="label text-small">Buổi học miễn phí</label>
                        <div class="session-list">
                            <div v-for="(s, idx) in tutorSessionOptions" :key="s.id" class="session-item" :class="{ selected: form.tutor_session_id == s.id }" @click="form.tutor_session_id = s.id">
                                <div class="left-icon" :class="{ active: form.tutor_session_id == s.id }">
                                    <svg v-if="s.recommended" class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 7v14" />
                                        <path d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z" /></svg>
                                    <svg v-else class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z" /></svg>
                                </div>
                                <div class="content">
                                    <div class="title-row">
                                        <div class="title">{{ s.time }}</div>
                                    </div>
                                    <div class="meta">
                                        <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" /></svg>
                                        <span>Thời lượng: {{ formatDuration(s.duration_hours) }}</span>
                                    </div>
                                    <div class="desc">{{ s.description }}</div>
                                </div>
                                <div class="right-radio">
                                    <span class="radio" :class="{ checked: form.tutor_session_id == s.id }"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-lg btn-primary border-r-2" @click="saveProfile" :disabled="!isFreeTrialValid">
                    <span>Lưu thay đổi</span>
                </button>
            </div>
        </base-modal>

        <div class="section-card free-trial" @click="showFreeTrialModal = true">
            <div class="header-wrapper">
                <div class="header-left">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg">
                            <rect x="3" y="8" width="18" height="4" rx="1"></rect>
                            <path d="M12 8v13"></path>
                            <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path>
                            <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path>
                        </svg>
                    </div>
                    <div class="title-wrapper">
                        <span class="title-main">Buổi Học Miễn Phí</span>
                        <span class="sub-title">Cài đặt buổi học thử miễn phí để thu hút học sinh</span>
                    </div>
                </div>
            </div>
            <div class="section-content_preview">
                <span>{{ form.free_trial && form.tutor_session_id ? `Đã bật buổi học miễn phí (${getSelectedSessionName()})` : 'Vui lòng click để cài đặt buổi học miễn phí' }}</span>
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg>
            </div>
        </div>
    </div>
</div>
</template>

<style scoped>
@import url('@css/profileNew.css');
@import url('@css/lessonInformation.css');
.note-group {
    display: flex;
    align-items: start;
    gap: 2rem;
}
.note-wrapper, .tips {
    display: grid;
    gap: 0.2rem;
    margin-top: 1.5rem;
    font-size: var(--font-size-small);
    border-radius: 8px;
    line-height: 1.5;
    flex: 1;
}
.note-wrapper_header, .tips_header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: var(--font-size-base);
}
.feather {
    width: 1rem;
    height: 1rem;
    border-radius: 100%;
    color: #f7f7f7;
    display: flex;
    align-items: center;
    justify-content: center;
}
.feather-check {
    width: 1rem;
    height: 1rem;
    border-radius: 100%;
    background-color: #5bca8d;
    color: #f7f7f7;
    display: flex;
    align-items: center;
    justify-content: center;
}
.feather-x {
    background: #ff3636;
}

/* Disabled button styles */
.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #9ca3af;
    border-color: #9ca3af;
}

.btn-primary:disabled:hover {
    background-color: #9ca3af;
    border-color: #9ca3af;
}

</style>

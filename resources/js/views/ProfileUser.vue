<script setup>
import {
    ref,
    reactive,
    onMounted,
    getCurrentInstance,
    computed,
    watch
} from 'vue';
import Overview from '@/components/ProfileUser/Overview.vue';
import Schedule from '@/components/ProfileTutor/Schedule.vue';
import StudyMethod from '@/components/ProfileTutor/StudyMethod.vue';
import {
    useStore
} from 'vuex';
const store = useStore();
const {
    proxy
} = getCurrentInstance();

const educationLevels = computed(() => store.state.configuration.educationLevels);
const listSubjects = computed(() => store.state.configuration.subjects);
const languageOptions = computed(() => store.state.configuration.languages);
const levelOptions = computed(() => store.state.configuration.levelLanguages);

const showEditProfileModal = ref(false);
const showEditSubjectModal = ref(false);
const showAddSubjectModal = ref(false);
const showAddPackageModal = ref(false);
const selectedLevelsOfSubject = ref([]);
const userDataDetail = ref({});
const currentPackageIndexes = ref({});

const userDataAction = reactive({
    first_name: '',
    last_name: '',
    address: '',
    phone: '',
    about_you: '',
    education: {
        school_name: '',
        major: '',
        time: '',
        description: ''
    },
    experience: {
        name: '',
        position: '',
        time: '',
        description: ''
    },
    subject: {
        name: '',
        years_of_experience: '',
        levels: []
    }
});
const newSubject = reactive({
    name: '',
    years_of_experience: '',
    levels: []
});

// Language Management
const showAddLanguageModal = ref(false);
const showEditLanguageModal = ref(false);
const selectedLanguageId = ref(null);

const newLanguage = ref({
    language_id: '',
    level_language_id: '',
    is_native: false
});

const selectedLanguage = ref({
    language_id: '',
    level_language_id: '',
    is_native: false
});

const activeTab = ref('overview');

const tabs = [{
        id: 'overview',
        name: 'Tổng quan',
        icon: 'fa-regular fa-user'
    },
    {
        id: 'schedule',
        name: 'Lịch trình',
        icon: 'fa-regular fa-clock'
    },
    // {
    //     id: 'reviews',
    //     name: 'Đánh giá',
    //     icon: 'fa-regular fa-star'
    // },
    // {
    //     id: 'documents',
    //     name: 'Tài liệu',
    //     icon: 'fa-regular fa-file'
    // },
    {
        id: 'study-location',
        name: 'Phương thức học',
        icon: 'fa-regular fa-file'
    }
];

const isValidEditSubjectInput = computed(() => {
    return userDataAction.subject.years_of_experience &&
        selectedLevelsOfSubject.value.length > 0 &&
        selectedLevelsOfSubject.value.every(levelId => {
            const level = educationLevels.value.find(l => l.id === levelId);
            return level && level.price > 0;
        });
});

const isValidAddSubjectInput = computed(() => {
    return newSubject.id &&
        newSubject.years_of_experience &&
        selectedLevelsOfSubject.value.length > 0 &&
        selectedLevelsOfSubject.value.every(levelId => {
            const level = educationLevels.value.find(l => l.id === levelId);
            return level && level.price > 0;
        });
});

const toggleLevelSelection = (level) => {
    if (!selectedLevelsOfSubject.value.includes(level.id)) {
        level.price = '';
    }
};

// Lấy dữ liệu user từ API
const getUserDataDetail = async () => {
    try {
        const response = await proxy.$api.apiGet('me/profile');
        userDataDetail.value = response.data;

        Object.assign(userDataAction, {
            first_name: response.data.first_name,
            last_name: response.data.last_name,
            address: response.data.address,
            phone: response.data.phone,
            about_you: response.data.about_you
        });
    } catch (error) {
        console.error('Failed to fetch user data:', error);
    }
};

const calculateProfileCompletion = (userData) => {
    const completionDetails = {
        personal_info: false,
        education: false,
        experience: false,
        subjects: false,
        languages: false,
        schedule: false,
        study_form: false
    };

    // Check personal info completion (Thông tin cá nhân)
    completionDetails.personal_info =
        !empty(userData.first_name) &&
        !empty(userData.last_name) &&
        !empty(userData.sex) &&
        !empty(userData.phone) &&
        !empty(userData.email) &&
        !empty(userData.provinces_id) &&
        !empty(userData.districts_id) &&
        !empty(userData.wards_id) &&
        !empty(userData.address) &&
        !empty(userData.about_you) &&
        !empty(userData.cccd) &&
        !empty(userData.cccd_front) &&
        !empty(userData.cccd_back);

    // Check education records (Học vấn ít nhất 1)
    completionDetails.education = userData.user_educations?.length > 0;

    // Check experience records (Kinh nghiệm ít nhất 1)
    completionDetails.experience = userData.user_experiences?.length > 0;

    // Check subjects (Môn học giảng dạy ít nhất 1)
    completionDetails.subjects = userData.user_subjects?.length > 0;

    // Check languages (Ngôn ngữ ít nhất 1)
    completionDetails.languages = userData.user_languages?.length > 0;

    // Check weekly schedule (Lịch trình hàng tuần)
    completionDetails.schedule = userData.user_weekly_time_slots?.length > 0;

    // Check study locations (Hình thức học)
    completionDetails.study_form = userData.user_study_locations?.length > 0;

    // Calculate percentage
    const totalFields = Object.keys(completionDetails).length;
    const completedFields = Object.values(completionDetails).filter(Boolean).length;
    const percent = Math.round((completedFields / totalFields) * 100);

    // Determine if fully completed
    const completed = completedFields === totalFields;

    return {
        percent,
        completed,
        details: completionDetails
    };
};

const empty = (value) => {
    return value === undefined || value === null || value === '';
};

const updateUserData = (newData) => {
    userDataDetail.value = {
        ...userDataDetail.value,
        ...newData
    };
    // Calculate profile completion after updating data
    userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
};

const updateProfile = async () => {
    try {
        const response = await proxy.$api.apiPut('me/profile', userDataAction);
        userDataDetail.value = response.data;
        // Calculate profile completion after updating profile
        userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
        showEditProfileModal.value = false;
    } catch (error) {
        console.error('Failed to update profile:', error);
    }
};

const updateSubject = async () => {
    try {
        const levels = selectedLevelsOfSubject.value.map(levelId => {
            const level = educationLevels.value.find(l => l.id === levelId);
            return {
                level_id: levelId,
                price: level.price
            };
        });

        const response = await proxy.$api.apiPut('me/subject', {
            user_subject_id: userDataAction.subject.id,
            subject_id: userDataAction.subject.subject_id,
            years_of_experience: userDataAction.subject.years_of_experience,
            levels: levels
        });

        // Update the original data with the updated subject
        const subjectIndex = userDataDetail.value.user_subjects.findIndex(
            subject => subject.id === response.data.id
        );
        if (subjectIndex !== -1) {
            userDataDetail.value.user_subjects[subjectIndex] = response.data;
        }
        // Calculate profile completion after updating subject
        userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);

        showEditSubjectModal.value = false;
    } catch (error) {
        console.error('Failed to update subject:', error);
    }
};

const showEditSubject = (data) => {
    showEditSubjectModal.value = true;
    userDataAction.subject = {
        ...data
    };

    // Reset selected levels and prices
    selectedLevelsOfSubject.value = [];
    educationLevels.value.forEach(level => {
        level.price = '';
    });

    // Set selected levels and their prices
    if (data.user_subject_levels && data.user_subject_levels.length > 0) {
        data.user_subject_levels.forEach(levelData => {
            selectedLevelsOfSubject.value.push(levelData.level_id);
            const level = educationLevels.value.find(l => l.id === levelData.level_id);
            if (level) {
                level.price = levelData.price;
            }
        });
    }
};

const showCreateSubjectModal = () => {
    showAddSubjectModal.value = true;
    newSubject.id = '';
    newSubject.name = '';
    newSubject.years_of_experience = '';
    selectedLevelsOfSubject.value = [];
    educationLevels.value.forEach(level => {
        level.price = '';
    });
}

const addNewSubject = async () => {
    try {
        const levels = selectedLevelsOfSubject.value.map(levelId => {
            const level = educationLevels.value.find(l => l.id === levelId);
            return {
                level_id: levelId,
                price: level.price
            };
        });

        const response = await proxy.$api.apiPost('me/subject', {
            subject_id: newSubject.id,
            years_of_experience: newSubject.years_of_experience,
            levels: levels
        });

        // Update the original data with the new subject
        if (!userDataDetail.value.user_subjects) {
            userDataDetail.value.user_subjects = [];
        }
        userDataDetail.value.user_subjects.push(response.data);
        // Calculate profile completion after adding subject
        userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);

        showAddSubjectModal.value = false;
        // Reset form
        Object.assign(newSubject, {
            id: '',
            name: '',
            years_of_experience: '',
            levels: []
        });
        selectedLevelsOfSubject.value = [];
    } catch (error) {
        console.error('Failed to add subject:', error);
    }
};

const deleteSubject = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/subject/${id}`);
        userDataDetail.value.user_subjects = userDataDetail.value.user_subjects.filter(item => item.id !== id);
        // Calculate profile completion after deleting subject
        userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
    } catch (error) {
        console.error('Failed to delete subject:', error);
    }
};

const onAddSubjectSuccess = () => {
    getUserDataDetail(); // Refresh user data
    showAddSubjectModal.value = false;
    showAddPackageModal.value = true; // Mở lại modal thêm gói dịch vụ
};

const initializePackageIndexes = () => {
    if (userDataDetail.value?.user_packages) {
        userDataDetail.value.user_packages.forEach(group => {
            currentPackageIndexes.value[group.subject_id] = 0;
        });
    }
};

const addLanguage = async () => {
    try {
        const response = await proxy.$api.apiPost('me/languages', {
            language_id: newLanguage.value.language_id,
            level_language_id: newLanguage.value.level_language_id,
            is_native: newLanguage.value.is_native
        });

        if (response.data) {
            userDataDetail.value.user_languages.push(response.data);
            // Calculate profile completion after adding language
            userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
        }
        showAddLanguageModal.value = false;
        resetNewLanguage();
    } catch (error) {
        console.error('Failed to add language:', error);
    }
};

const handleEditLanguage = (language) => {
    selectedLanguageId.value = language.id;
    selectedLanguage.value = {
        language_id: language.language_id,
        level_language_id: language.level_language_id,
        is_native: language.is_native
    };
    console.log(selectedLanguage.value);
    showEditLanguageModal.value = true;
};

const updateLanguage = async () => {
    try {
        const response = await proxy.$api.apiPut(`me/languages/${selectedLanguageId.value}`, {
            language_id: selectedLanguage.value.language_id,
            level_language_id: selectedLanguage.value.level_language_id,
            is_native: selectedLanguage.value.is_native
        });

        if (response.data) {
            const index = userDataDetail.value.user_languages.findIndex(lang => lang.id === selectedLanguageId.value);
            if (index !== -1) {
                userDataDetail.value.user_languages[index] = response.data;
            }
            // Calculate profile completion after updating language
            userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
        }
        showEditLanguageModal.value = false;
    } catch (error) {
        console.error('Failed to update language:', error);
    }
};

const handleDeleteLanguage = async (id) => {
    try {
        const response = await proxy.$api.apiDelete(`me/languages/${id}`);
        if (response.success) {
            userDataDetail.value.user_languages = userDataDetail.value.user_languages.filter(item => item.id !== id);
            userDataDetail.value.profile_completion = calculateProfileCompletion(userDataDetail.value);
        }
    } catch (error) {
        console.error('Failed to delete language:', error);
    }
};

const resetNewLanguage = () => {
    newLanguage.value = {
        language_id: '',
        level_language_id: '',
        is_native: false
    };
};

const isNativeLanguage = computed((status) => {
    return !!status;
})

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

watch(() => userDataDetail.value?.user_packages, () => {
    initializePackageIndexes();
}, {
    immediate: true
});

onMounted(getUserDataDetail);

userDataDetail.value.user_subjects = [
    {
        id: 1,
        name: 'Toán học',
        level: 'THPT',
        teacher_name: 'Thầy Nguyễn Văn A',
        teacher_avatar: 'https://bizweb.dktcdn.net/100/175/849/articles/ava-fa4dcc52-baf1-45fa-88a7-5e319635fc05.jpg?v=1612760997843',
        completed_sessions: 15,
        total_sessions: 20,
        current_score: 8.5,
        goal_score: 9,
        next_session_time: '18:00:00 15/1/2024'
    },
    {
        id: 2,
        name: 'Vật lý',
        level: 'THPT',
        teacher_name: 'Cô Trần Thị B',
        teacher_avatar: 'https://bizweb.dktcdn.net/100/175/849/articles/ava-fa4dcc52-baf1-45fa-88a7-5e319635fc05.jpg?v=1612760997843',
        completed_sessions: 10,
        total_sessions: 16,
        current_score: 7.5,
        goal_score: 8.5,
        next_session_time: '19:00:00 16/1/2024'
    },
    {
        id: 3,
        name: 'Hóa học',
        level: 'THPT',
        teacher_name: 'Thầy Lê Văn C',
        teacher_avatar: 'https://bizweb.dktcdn.net/100/175/849/articles/ava-fa4dcc52-baf1-45fa-88a7-5e319635fc05.jpg?v=1612760997843',
        completed_sessions: 5,
        total_sessions: 12,
        current_score: 6.5,
        goal_score: 8,
        next_session_time: '17:30:00 17/1/2024'
    }
];
</script>

<template>
<div class="profile-container">
    <!-- Left Sidebar -->
    <div class="container">
        <div class="profile-sidebar">
            <div class="profile-info section-card">
                <div class="avatar">
                    <img :src="userDataDetail.avatar" alt="Avatar">
                </div>
                <h1 class="name">{{ userDataDetail.full_name }}</h1>
                <div class="role-badge">{{ userDataDetail.role }}</div>

                <div class="rating">
                    <div class="stars">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#eab308" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                    <span class="rating-score">4.8</span>
                    <span class="rating-count">(100 đánh giá)</span>
                </div>
                <div class="contact-info">
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-lg"><path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path><path d="M22 10v6"></path><path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path></svg>
                        </div>
                        <div class="contact-value">
                            <span class="value">{{ userDataDetail?.education_level?.name }}</span>
                            <span class="label">Cấp độ hiện tại</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-lg" src="/images/email.svg" alt="Email">
                        </div>
                        <div class="contact-value">
                            <span class="value">{{ userDataDetail.email }}</span>
                            <span class="label">Email liên hệ</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-lg" src="/images/phone.svg" alt="Phone">
                        </div>
                        <div class="contact-value">
                            <span class="value">{{ userDataDetail.phone }}</span>
                            <span class="label">Số điện thoại</span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-lg" src="/images/location.svg" alt="Location">
                        </div>
                        <div class="contact-value">
                            <span class="value">{{ userDataDetail.address || 'Chưa cập nhật' }}</span>
                            <span class="label">Địa chỉ nhà</span>
                        </div>
                    </div>
                </div>

                <div class="bio">
                    <div class="bio-content">
                        <div class="bio-label">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span>Giới thiệu</span>
                        </div>
                        {{ userDataDetail.about_you || 'Chưa cập nhật' }}
                    </div>
                </div>

                <div class="actions">
                    <button class="btn-lg btn-secondary w-100 border-md" @click="showEditProfileModal = true">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        Chỉnh sửa
                    </button>
                    <button class="btn-lg btn-primary w-100 border-md">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
                        Tin nhắn
                    </button>
                </div>
            </div>

            <!-- <div class="stats-section section-card">
                <h2 class="section-title">
                    <i class=" fas fa-chart-line"></i>
                    Thống kê
                </h2>
                <div class="stats-grid section-content">
                    <div class="stat-item">
                        <span class="stat-value">45</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-lg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span>Học sinh</span>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">320</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-lg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"></path>
                                <path d="M16 2v4"></path>
                                <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg>
                            <span>Buổi học</span>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">640</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-lg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span>Giờ dạy</span>
                        </span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">98%</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-lg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m9 12 2 2 4-4"></path>
                            </svg>
                            <span>Hoàn thành</span>
                        </span>
                    </div>
                </div>
            </div> -->

            <div class="subjects-section section-card">
                <div class="section-header">
                    <h2 class="section-title">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                        </svg>
                        Môn học hiện tại
                    </h2>
                    <p class="section-desc">Hiển hị danh sách môn học hiện tại bạn đang tham gia</p>
                </div>
                <div class="subjects-list section-content">
                    <div class="subject-progress-card" v-for="subject in userDataDetail.user_subjects" :key="subject.id" v-if="userDataDetail.user_subjects.length > 0">
                        <div class="subject-progress-header">
                            <div class="subject-title">{{ subject.name }}</div>
                            <div class="subject-level">{{ subject.level }}</div>
                        </div>
                        <div class="subject-teacher">
                            <img class="subject-avatar" :src="subject.teacher_avatar || '/images/default-avatar.png'">
                            <span class="subject-teacher-name">{{ subject.teacher_name }}</span>
                        </div>
                        <div class="subject-progress-info">
                            <span>Tiến độ: {{ subject.completed_sessions }}/{{ subject.total_sessions }} buổi</span>
                            <span class="subject-progress-percent">{{ Math.round((subject.completed_sessions/subject.total_sessions)*100) }}%</span>
                        </div>
                        <div class="subject-progress-bar">
                            <div class="subject-progress-bar-inner" :style="{width: ((subject.completed_sessions/subject.total_sessions)*100)+'%'}"></div>
                        </div>
                        <div class="subject-score-row">
                            <span>Điểm hiện tại: {{ subject.current_score }}</span>
                            <span class="subject-goal">Mục tiêu: {{ subject.goal_score }}</span>
                        </div>
                        <div class="subject-next-session">
                            <svg class="icon-sm" width="24" height="24" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                            <span>Buổi tiếp theo: {{ subject.next_session_time }}</span>
                        </div>
                    </div>
                    <div class="card-no-data" v-else>
                        <div class="logo-wrapper">
                            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                            </svg>
                        </div>
                        <div class="content-wrapper">
                            <h4>Chưa có môn học</h4>
                            <p>Tìm kiếm gia sư để cải thiện quá trình học của bạn</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navigation Tabs -->
            <!-- <div class="nav-tabs">
                <button v-for="tab in tabs"
                    :key="tab.id"
                    :class="['type-button', { active: activeTab === tab.id }]"
                    @click="activeTab = tab.id"
                >
                    <i :class="tab.icon"></i>
                    {{ tab.name }}
                </button>
            </div> -->

            <!-- Tab Content -->
            <div class="tab-content">
                <Overview :user-data-detail="userDataDetail" @update-data="updateUserData" />
            </div>
        </div>
    </div>
</div>

<base-modal :is-open="showEditProfileModal" title="Chỉnh sửa thông tin" description="Cập nhật thông tin cá nhân của bạn. Nhấn lưu khi hoàn tất." size="small" @close="showEditProfileModal = false">
    <div class="modal-edit">
        <div class="form-group-container">
            <div class="form-group">
                <label>Họ</label>
                <div>
                    <input type="text" v-model="userDataAction.first_name" placeholder="Nguyễn">
                </div>
            </div>
            <div class="form-group">
                <label>Tên</label>
                <div>
                    <input type="text" v-model="userDataAction.last_name" placeholder="Văn A">
                </div>
            </div>
        </div>

        <div class="form-group-container">
            <div class="form-group">
                <label>Địa chỉ</label>
                <div>
                    <input type="text" v-model="userDataAction.address" placeholder="Quận Cầu Giấy, Hà Nội">
                </div>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <div>
                    <input type="text" v-model="userDataAction.phone" placeholder="0912345678">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Giới thiệu bản thân</label>
            <div>
                <textarea v-model="userDataAction.about_you" placeholder="Nhập giới thiệu bản thân" rows="4"></textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditProfileModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateProfile">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<!-- Chỉnh sửa môn học -->
<base-modal :is-open="showEditSubjectModal" title="Chỉnh sửa môn học" description="Cập nhật thông tin môn học." size="small" @close="showEditSubjectModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Chọn môn học</label>
            <base-select v-model="userDataAction.subject.subject_id" :options="listSubjects" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." />
        </div>

        <div class="form-group">
            <div class="experience-input">
                <base-input v-model="userDataAction.subject.years_of_experience" type="number" label="Số năm kinh nghiệm" placeholder="Nhập số năm kinh nghiệm" unit="năm" :min="0" :step="0.5" required />
            </div>
        </div>

        <div class="level-group">
            <label>Cấp độ dạy và học phí</label>
            <div class="level-list">
                <div class="level-item" v-for="level in educationLevels" :key="level.id">
                    <div class="level-left">
                        <div class="level-icon" :class="level.class">
                            <img :src="level.image" :alt="level.name">
                        </div>
                        <input type="checkbox" :value="level.id" v-model="selectedLevelsOfSubject" @change="toggleLevelSelection(level)">
                        <div class="level-name">{{ level.name }}</div>
                    </div>
                    <base-input v-model="level.price" type="number" placeholder="Nhập học phí" unit="đ/giờ" :min="0" :step="1" :disabled="!selectedLevelsOfSubject.includes(level.id)" />
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditSubjectModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateSubject" :disabled="!isValidEditSubjectInput">Cập nhật</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showAddSubjectModal" title="Thêm môn học" description="Thêm môn học mới vào hồ sơ của bạn." size="small" @close="showAddSubjectModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Chọn môn học</label>
            <base-select v-model="newSubject.id" :options="listSubjects" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." />
        </div>

        <div class="experience-input">
            <base-input v-model="newSubject.years_of_experience" type="number" label="Số năm kinh nghiệm" placeholder="Nhập số năm kinh nghiệm" unit="năm" :min="0" :step="0.5" required />
        </div>

        <div class="level-group">
            <label>Cấp độ dạy và học phí</label>
            <div class="level-list">
                <div class="level-item" v-for="level in educationLevels" :key="level.id">
                    <div class="level-left">
                        <div class="level-icon" :class="level.class">
                            <img :src="level.image" :alt="level.name">
                        </div>
                        <input type="checkbox" :value="level.id" v-model="selectedLevelsOfSubject" @change="toggleLevelSelection(level)">
                        <div class="level-name">{{ level.name }}</div>
                    </div>
                    <div class="form-group" @click.stop>
                        <base-input v-model="level.price" type="number" placeholder="Nhập học phí" unit="nghìn đồng/buổi" :min="0" :step="1" :disabled="!selectedLevelsOfSubject.includes(level.id)" />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddSubjectModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewSubject().then(onAddSubjectSuccess)" :disabled="!isValidAddSubjectInput">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Add Language Modal -->
<base-modal :is-open="showAddLanguageModal" title="Thêm ngôn ngữ" description="Thêm ngôn ngữ mới" size="small" @close="showAddLanguageModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Ngôn ngữ</label>
            <base-select v-model="newLanguage.language_id" :options="languageOptions" placeholder="Chọn ngôn ngữ" />
        </div>

        <div class="form-group">
            <label>Trình độ</label>
            <base-select v-model="newLanguage.level_language_id" :options="levelOptions" placeholder="Chọn trình độ" />
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" v-model="newLanguage.is_native">
                Đây là ngôn ngữ bản ngữ của tôi
            </label>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddLanguageModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addLanguage">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Edit Language Modal -->
<base-modal :is-open="showEditLanguageModal" title="Chỉnh sửa ngôn ngữ" description="Chỉnh sửa thông tin ngôn ngữ" size="small" @close="showEditLanguageModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Ngôn ngữ</label>
            <base-select v-model="selectedLanguage.language_id" :options="languageOptions" placeholder="Chọn ngôn ngữ" />
        </div>

        <div class="form-group">
            <label>Trình độ</label>
            <base-select v-model="selectedLanguage.level_language_id" :options="levelOptions" placeholder="Chọn trình độ" />
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" v-model="selectedLanguage.is_native">
                Đây là ngôn ngữ bản ngữ của tôi
            </label>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditLanguageModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateLanguage">Cập nhật</button>
        </div>
    </div>
</base-modal>
</template>

<style scoped>
@import url('@css/profile.css');

.section-header {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

/* Language Section Styles */
.language-list {
    display: grid;
    gap: 1rem;
}

.language-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border: 1px solid rgb(230 231 235);
    border-radius: 8px;
}

.language-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.language-name {
    font-weight: 600;
    color: #111827;
}

.language-level {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.level-badge {
    /* background: #E5E7EB; */
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    border: 1px solid #c8c8c8;
    font-size: var(--font-size-mini);
    color: #374151;
}

.btn-add-language {
    width: 100%;
    padding: 8px;
    border: 1px dashed #D1D5DB;
    border-radius: 6px;
    background: white;
    color: #6B7280;
    font-size: var(--font-size-small);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-add-language:hover {
    background: #F9FAFB;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    user-select: none;
    font-weight: 500;
}

.checkbox-label input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
}

/* Profile Completion Card Styles */

.section-desc {
    color: #6B7280;
    font-size: 14px;
    margin: 0;
}

.progress-circle-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 32px;
}

.progress-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    position: relative;
    background: conic-gradient(
        #000000 calc(v-bind('userDataDetail.profile_completion?.percent || 0') * 3.6deg),
        #E5E7EB calc(v-bind('userDataDetail.profile_completion?.percent || 0') * 3.6deg)
    );
    margin-bottom: 12px;
}

.progress-circle::before {
    content: '';
    position: absolute;
    inset: 10px;
    border-radius: 50%;
    background: white;
}

.progress-circle-inner {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.percent {
    font-size: 24px;
    font-weight: 600;
    color: #111827;
}

.progress-text {
    font-size: 14px;
    color: #6B7280;
}

.details-title {
    font-weight: 500;
    color: #111827;
    margin-bottom: 12px;
}

.completion-list {
    display: grid;
    gap: 8px;
}

.completion-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.item-name {
    color: #111827;
    font-size: 14px;
}

.status {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 16px;
    background: white;
    color: black;
    border: 1px solid black;
}

/* .status.completed {
    background: #DCFCE7;
    color: #15803D;
}

.status.pending {
    background: #FEF9C3;
    color: #854D0E;
}

.status.missing {
    background: #FEE2E2;
    color: #B91C1C;
} */

.status-legend {
    display: flex;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid #E5E7EB;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #6B7280;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.dot.completed {
    background: #15803D;
}

.dot.pending {
    background: #854D0E;
}

.dot.missing {
    background: #B91C1C;
}

.type-button {
    flex: 1;
    padding: 0.5rem 1.5rem;
    border: none;
    background: transparent;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin: 0.2rem;
    white-space: nowrap;
}

.type-button.active {
    background: white;
    color: black;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.subjects-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}
.subject-progress-card {
    background: #f1f5f94d;
    border: 1px solid #e5e7eb;
    border-radius: 0.7rem;
    padding: 18px 20px 16px 20px;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.subject-progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.subject-title {
    font-size: var(--font-size-heading-6);
    font-weight: 500;
    color: #18181b;
}
.subject-level {
    font-size: 0.8rem;
    line-height: 1rem;
    color: #334155;
    font-weight: 600;
}
.subject-teacher {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.subject-avatar {
    width: 33px;
    height: 33px;
    border-radius: 50%;
    object-fit: cover;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
}
.subject-teacher-name {
    color: #64748b;
    font-size: var(--font-size-heading-6);
    font-size: 0.875rem;
    line-height: 1.25rem;
}
.subject-progress-info {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    line-height: 1rem;
    font-weight: 400;
    color: rgb(58, 57, 57);
}
.subject-progress-percent {
    color: #374151;
    font-weight: 500;
    font-size: 0.8rem;
    line-height: 1rem;
}
.subject-progress-bar {
    width: 100%;
    height: 6px;
    background: #f3f4f6;
    border-radius: 6px;
    overflow: hidden;
}
.subject-progress-bar-inner {
    height: 100%;
    background: black;
    border-radius: 6px;
    transition: width 0.3s;
}
.subject-score-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    line-height: 1rem;
    font-weight: 400;
    color: rgb(58, 57, 57);
}
.subject-next-session {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: #6b7280;
    font-size: 0.8rem;
    line-height: 1rem;
    font-weight: 400;
    margin-top: 0.1rem;
    padding-top: 0.6rem;
    border-top: 1px solid #e2e8f080;
}
</style>

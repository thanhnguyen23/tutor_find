<script setup>
import {
    ref,
    reactive,
    onMounted,
    getCurrentInstance,
    computed,
    watch,
} from 'vue';
import {
    useStore
} from 'vuex';
import ProfileInfoNew from '../components/ProfileTutor/ProfileInfoNew.vue';
import OverviewNew from '../components/ProfileTutor/OverviewNew.vue';
import ScheduleNew from '../components/ProfileTutor/ScheduleNew.vue';
import StudyMethodNew from '../components/ProfileTutor/StudyMethodNew.vue';

const store = useStore();
const {
    proxy
} = getCurrentInstance();

const userDataDetail = ref({});
const currentPackageIndexes = ref({});

const userDataAction = reactive({
    firstName: '',
    lastName: '',
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
const activeTab = ref('profile');

const tabs = [
    {
        id: 'profile',
        name: 'Hồ sơ',
        icon: 'profile'
    },
    {
        id: 'overview',
        name: 'Tổng quan',
        icon: 'overview'
    },
    {
        id: 'schedule',
        name: 'Lịch trình',
        icon: 'schedule'
    },
    {
        id: 'study-location',
        name: 'Nơi học',
        icon: 'method'
    }
];

// Lấy dữ liệu user từ API
const getUserDataDetail = async () => {
    try {
        const response = await proxy.$api.apiGet('me/profile');
        userDataDetail.value = response.data;

        Object.assign(userDataAction, {
            firstName: response.data.first_name,
            lastName: response.data.last_name,
            address: response.data.address,
            phone: response.data.phone,
            about_you: response.data.about_you
        });

        console.log(userDataDetail.value);
    } catch (error) {
        console.error('Failed to fetch user data:', error);
    }
};

const calculateProfileCompletion = (userData) => {
    console.log(userData);
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
        !empty(userData.cccd);

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
        ...newData,
        profile_completion: calculateProfileCompletion(newData),
    };
};

const updateProfileData = (newData) => {
    updateUserData(newData);
};

const initializePackageIndexes = () => {
    if (userDataDetail.value?.user_packages) {
        userDataDetail.value.user_packages.forEach(group => {
            currentPackageIndexes.value[group.subject_id] = 0;
        });
    }
};

watch(() => userDataDetail.value?.user_packages, () => {
    initializePackageIndexes();
}, {
    immediate: true
});

onMounted(getUserDataDetail);
</script>

<template>
<div class="profile-container">
    <!-- Left Sidebar -->
    <div class="container">
        <div class="nav-tabs">
            <div class="nav-tabs-wrapper">
                <button
                v-for="tab in tabs"
                :key="tab.id"
                :class="['tab', { active: activeTab === tab.id }]"
                @click="activeTab = tab.id"
                >
                    <!-- Hiển thị icon SVG dựa trên tab.id -->
                    <span class="icon">
                        <svg v-if="tab.id === 'profile'" class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>

                        <svg v-else-if="tab.id === 'overview'" class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18 17V9"></path><path d="M13 17V5"></path><path d="M8 17v-3"></path></svg>

                        <svg v-else-if="tab.id === 'schedule'" class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>

                        <svg v-else class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </span>
                    <span>{{ tab.name }}</span>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="page-content">

            <ProfileInfoNew v-if="activeTab === 'profile'" @update-data="updateProfileData" :user-data-detail="userDataDetail" />
            <OverviewNew v-if="activeTab === 'overview'" @update-data="updateProfileData" :user-data-detail="userDataDetail" />
            <ScheduleNew v-if="activeTab === 'schedule'" @update-data="updateProfileData" :user-data-detail="userDataDetail" />
            <StudyMethodNew v-if="activeTab === 'study-location'" @update-data="updateProfileData" :user-data-detail="userDataDetail" />
        </div>
    </div>
</div>
</template>

<style scoped>
@import url('@css/profileNew.css');
</style>

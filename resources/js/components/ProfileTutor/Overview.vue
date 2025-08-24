<script setup>
import {
    ref,
    reactive,
    onMounted,
    getCurrentInstance,
    computed,
    watch
} from 'vue';

const {
    proxy
} = getCurrentInstance();

const props = defineProps({
    userDataDetail: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update-data']);

const showEditProfileModal = ref(false);
const showEditEducationModal = ref(false);
const showEditExperienceModal = ref(false);
const showAddSubjectModal = ref(false);
const showAddEducationModal = ref(false);
const showAddExperienceModal = ref(false);
const showAddPackageModal = ref(false);
const showEditPackageModal = ref(false);
const subjectExists = ref(false);
const availableLevels = ref([]);
const selectedPackage = ref(null);
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

const reviews = ref([{
        id: 1,
        name: 'Trần Thị B',
        date: '15/03/2023',
        avatar: '/path-to-avatar.jpg',
        content: 'Thầy giáo rất tận tâm và có phương pháp giảng dạy dễ hiểu. Con tôi đã tiến bộ rất nhiều sau khi học với thầy.'
    },
    {
        id: 2,
        name: 'Lê Văn C',
        date: '20/02/2023',
        avatar: '/path-to-avatar.jpg',
        content: 'Thầy A có kiến thức chuyên môn sâu rộng và luôn kiên nhẫn giải thích cho tôi những khái niệm khó.'
    }
]);

const newEducation = reactive({
    school_name: '',
    major: '',
    time: '',
    description: ''
});

const newExperience = reactive({
    name: '',
    position: '',
    time: '',
    description: ''
});

const newPackage = reactive({
    subject_id: '',
    level_id: '',
    name: '',
    description: '',
    number_of_lessons: '',
    duration: '',
    price: '',
    features: [],
    create_subject: false,
    years_of_experience: ''
});

const packageFeatures = ref(['']);

const updateProfile = async () => {
    try {
        const response = await proxy.$api.apiPut('me/profile', userDataAction);
        emit('update-data', response.data);
        showEditProfileModal.value = false;
    } catch (error) {
        console.error('Failed to update profile:', error);
    }
};

const updateEducation = async () => {
    try {
        const response = await proxy.$api.apiPut('me/education', userDataAction.education);
        // Update the original data with the updated education
        const updatedData = {
            ...props.userDataDetail,
            user_educations: props.userDataDetail.user_educations.map(edu =>
                edu.id === response.data.id ? response.data : edu
            )
        };
        emit('update-data', updatedData);
        showEditEducationModal.value = false;
    } catch (error) {
        console.error('Failed to update education:', error);
    }
};

const updateExperience = async () => {
    try {
        const response = await proxy.$api.apiPut('me/experience', userDataAction.experience);
        // Update the original data with the updated experience
        const updatedData = {
            ...props.userDataDetail,
            user_experiences: props.userDataDetail.user_experiences.map(exp =>
                exp.id === response.data.id ? response.data : exp
            )
        };
        emit('update-data', updatedData);
        showEditExperienceModal.value = false;
    } catch (error) {
        console.error('Failed to update experience:', error);
    }
};

const showEditEducation = (data) => {
    showEditEducationModal.value = true;
    userDataAction.education = {...data};
};

const showEditExperience = (data) => {
    showEditExperienceModal.value = true;
    userDataAction.experience = {...data};
};

const addNewEducation = async () => {
    try {
        const response = await proxy.$api.apiPost('me/education', newEducation);
        // Update the original data with the new education
        const updatedData = {
            ...props.userDataDetail,
            user_educations: [...props.userDataDetail.user_educations, response.data]
        };
        emit('update-data', updatedData);
        showAddEducationModal.value = false;
        // Reset form
        Object.assign(newEducation, {
            school_name: '',
            major: '',
            time: '',
            description: ''
        });
    } catch (error) {
        console.error('Failed to add education:', error);
    }
};

const addNewExperience = async () => {
    try {
        const response = await proxy.$api.apiPost('me/experience', newExperience);
        // Update the original data with the new experience
        const updatedData = {
            ...props.userDataDetail,
            user_experiences: [...props.userDataDetail.user_experiences, response.data]
        };
        emit('update-data', updatedData);
        showAddExperienceModal.value = false;
        // Reset form
        Object.assign(newExperience, {
            name: '',
            position: '',
            time: '',
            description: ''
        });
    } catch (error) {
        console.error('Failed to add experience:', error);
    }
};

const addFeature = () => {
    packageFeatures.value.push('');
};

const removeFeature = (index) => {
    packageFeatures.value.splice(index, 1);
};

const showCreatePackageModal = () => {
    showAddPackageModal.value = true;
    Object.assign(newPackage, {
        subject_id: '',
        level_id: '',
        name: '',
        description: '',
        number_of_lessons: '',
        duration: '',
        price: '',
        features: [],
        create_subject: false,
        years_of_experience: ''
    });
    packageFeatures.value = [''];
    availableLevels.value = [];
    subjectExists.value = false;
};

const showEditPackage = (packageItem) => {
    showEditPackageModal.value = true;
    selectedPackage.value = {...packageItem};

    // Tìm subject trong userSubjects
    const subject = userSubjects.value.find(s => s.name === packageItem.subject);
    if (subject) {
        // Set subject_id và lấy levels của subject đó
        newPackage.subject_id = subject.subject_id;
        availableLevels.value = subject.user_subject_levels.map(level => ({
            id: level.level_id,
            name: level.level_name,
            price: level.price
        }));
    }

    // Cập nhật các giá trị khác của package
    Object.assign(newPackage, {
        level_id: packageItem.level_id,
        name: packageItem.name,
        description: packageItem.description,
        number_of_lessons: packageItem.number_of_lessons,
        duration: packageItem.duration,
        price: packageItem.price
    });

    // Cập nhật features
    packageFeatures.value = [...packageItem.features];
};

const addNewPackage = async () => {
    try {
        const features = packageFeatures.value.filter(f => f.trim() !== '');
        const response = await proxy.$api.apiPost('me/packages', {
            ...newPackage,
            features
        });
        // Update the original data with the new package
        const updatedData = {
            ...props.userDataDetail,
            user_packages: [...props.userDataDetail.user_packages, response.data]
        };
        emit('update-data', updatedData);
        showAddPackageModal.value = false;
    } catch (error) {
        console.error('Failed to add package:', error);
    }
};

const updatePackage = async () => {
    try {
        const features = packageFeatures.value.filter(f => f.trim() !== '');
        const response = await proxy.$api.apiPut(`me/packages/${selectedPackage.value.id}`, {
            ...newPackage,
            features
        });
        // Update the original data with the updated package
        const updatedData = {
            ...props.userDataDetail,
            user_packages: props.userDataDetail.user_packages.map(pkg =>
                pkg.id === response.data.id ? response.data : pkg
            )
        };
        emit('update-data', updatedData);
        showEditPackageModal.value = false;
    } catch (error) {
        console.error('Failed to update package:', error);
    }
};

const deleteEducation = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/education/${id}`);
        // Update the original data by removing the deleted education
        const updatedData = {
            ...props.userDataDetail,
            user_educations: props.userDataDetail.user_educations.filter(item => item.id !== id)
        };
        emit('update-data', updatedData);
    } catch (error) {
        console.error('Failed to delete education:', error);
    }
};

const deleteExperience = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/experience/${id}`);
        // Update the original data by removing the deleted experience
        const updatedData = {
            ...props.userDataDetail,
            user_experiences: props.userDataDetail.user_experiences.filter(item => item.id !== id)
        };
        emit('update-data', updatedData);
    } catch (error) {
        console.error('Failed to delete experience:', error);
    }
};

const deletePackage = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/packages/${id}`);
        // Update the original data by removing the deleted package
        const updatedData = {
            ...props.userDataDetail,
            user_packages: props.userDataDetail.user_packages.filter(item => item.id !== id)
        };
        emit('update-data', updatedData);
    } catch (error) {
        console.error('Failed to delete package:', error);
    }
};

const isValidPackageInput = computed(() => {
    return newPackage.subject_id &&
        newPackage.level_id &&
        newPackage.name &&
        newPackage.number_of_lessons &&
        newPackage.duration &&
        newPackage.price &&
        packageFeatures.value.some(f => f.trim() !== '');
});

const userSubjects = computed(() => {
    if (!props.userDataDetail?.user_subjects) return [];

    return props.userDataDetail.user_subjects;
});

const subjectOptions = computed(() => {
    const options = userSubjects.value.map(subject => ({
        id: subject.subject_id,
        name: subject.name
    }));

    // Thêm option "Thêm môn học mới"
    options.push({
        id: 'new',
        name: '+ Thêm môn học mới'
    });

    return options;
});

const handleSubjectSelect = (value) => {
    if (value === 'new') {
        newPackage.subject_id = '';
        showAddPackageModal.value = false; // Tắt modal thêm gói dịch vụ
        showAddSubjectModal.value = true;
        return;
    }

    const subject = userSubjects.value.find(s => s.subject_id === value);

    if (subject) {
        newPackage.subject_id = subject.subject_id;
        availableLevels.value = subject.user_subject_levels.map(level => ({
            id: level.level_id,
            name: level.level_name,
            price: level.price
        }));

        // Reset level_id khi đổi subject
        newPackage.level_id = '';
    } else {
        availableLevels.value = [];
        newPackage.level_id = '';
    }
};

const selectedLevelPrice = computed(() => {
    if (!newPackage.level_id || !availableLevels.value.length) return 0;
    const level = availableLevels.value.find(l => l.id === newPackage.level_id);
    return level?level.price : 0;
});

const showAllFeatures = ref({});

const toggleFeatures = (packageId) => {
    showAllFeatures.value[packageId] = !showAllFeatures.value[packageId];
};

const initializePackageIndexes = () => {
    if (props.userDataDetail?.user_packages) {
        props.userDataDetail.user_packages.forEach(group => {
            currentPackageIndexes.value[group.subject_id] = 0;
        });
    }
};

const nextPackage = (subjectId) => {
    const group = props.userDataDetail.user_packages.find(g => g.subject_id === subjectId);
    if (currentPackageIndexes.value[subjectId] < group.packages.length - 1) {
        currentPackageIndexes.value[subjectId]++;
    }
};

const prevPackage = (subjectId) => {
    if (currentPackageIndexes.value[subjectId] > 0) {
        currentPackageIndexes.value[subjectId]--;
    }
};

watch(() => props.userDataDetail?.user_packages, () => {
    initializePackageIndexes();
}, {
    immediate: true
});
</script>

<template>
<div class="overview">
    <!-- Education Section -->
    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <img class="icon-image-lg" src="/images/education.svg" />
                <span>Học vấn</span>
            </div>
            <button class="btn-add" @click="showAddEducationModal = true">
                <img class="icon-image-lg" src="/images/plus.svg" />
                thêm
            </button>
        </div>

        <div class="education-list list-card">
            <div v-for="edu in userDataDetail.user_educations" :key="edu.id" class="education-item card-item" v-if="userDataDetail.user_educations?.length > 0">
                <div class="logo-wrapper">
                    <img class="icon-image-lg" src="/images/education.svg" />
                </div>
                <div class="education-content">
                    <h4>{{ edu.school_name }}</h4>
                    <p>{{ edu.major }}</p>
                    <p class="details">{{ edu.description }}</p>
                </div>
                <div class="period-badge-container">
                    <div class="period-badge">{{ edu.time }}</div>
                    <img class="icon-image-md" src="/images/edit.svg" @click="showEditEducation(edu)" />
                    <img class="icon-image-md" src="/images/delete.svg" @click="deleteEducation(edu.id)" />
                </div>
            </div>

            <div class="card-no-data" v-else>
                <div class="logo-wrapper">
                    <img class="icon-image-lg" src="/images/education.svg" />
                </div>

                <div class="content-wrapper">
                    <h4>Chưa có thông tin học vấn</h4>
                    <p>Thêm thông tin về quá trình học tập của bạn để tăng độ tin cậy với học sinh</p>

                    <button class="btn-add-new" @click="showAddEducationModal = true">
                        <img class="icon-image-lg" src="/images/plus.svg" />
                        thêm học vấn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Experience Section -->
    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <img class="icon-image-lg" src="/images/briefcase.svg" />
                <span>Kinh nghiệm giảng dạy</span>
            </div>
            <button class="btn-add" @click="showAddExperienceModal = true">
                <img class="icon-image-lg" src="/images/plus.svg" />
                thêm
            </button>
        </div>

        <div class="experience-list list-card">
            <div v-for="exp in userDataDetail.user_experiences" :key="exp.id" class="experience-item card-item" v-if="userDataDetail.user_experiences?.length > 0">
                <div class="logo-wrapper">
                    <i class=" fas fa-building"></i>
                </div>
                <div class="experience-content">
                    <h4>{{ exp.name }}</h4>
                    <p>{{ exp.position }}</p>
                    <p class="details">{{ exp.description }}</p>
                </div>
                <div class="period-badge-container">
                    <div class="period-badge">{{ exp.time }}</div>
                    <img class="icon-image-md" src="/images/edit.svg" @click="showEditExperience(exp)" />
                    <img class="icon-image-md" src="/images/delete.svg" @click="deleteExperience(exp.id)" />
                </div>
            </div>

            <div class="card-no-data" v-else>
                <div class="logo-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-base icon-md">
                        <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        <rect width="20" height="14" x="2" y="6" rx="2"></rect>
                    </svg>
                </div>

                <div class="content-wrapper">
                    <h4>Chưa có kinh nghiệm giảng dạy</h4>
                    <p>Thêm thông tin về quá trình giảng dạy của bạn để tăng độ tin cậy với học sinh</p>

                    <button class="btn-add-new" @click="showAddExperienceModal = true">
                        <img class="icon-image-lg" src="/images/plus.svg" />
                        thêm kinh nghiệm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-base icon-md">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
                <span>Gói dịch vụ gia sư</span>
            </div>
            <button class="btn-add" @click="showCreatePackageModal">
                <img class="icon-image-lg" src="/images/plus.svg" />
                Thêm gói
            </button>
        </div>

        <div class="subject-package-list" v-if="userDataDetail.user_packages?.length > 0">
            <div v-for="subjectGroup in userDataDetail.user_packages" :key="subjectGroup.subject_id" class="subject-package-group">
                <div class="package-group-header">
                    <div class="package-subject-header">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                        </svg>
                        <h3>{{ subjectGroup.subject_name }}</h3>
                    </div>
                    <div class="package-navigation">
                        <span class="package-pagination">{{ currentPackageIndexes[subjectGroup.subject_id] + 1 }}/{{ subjectGroup.packages.length }}</span>
                        <div class="navigation-buttons">
                            <button class="nav-button" @click="prevPackage(subjectGroup.subject_id)" :disabled="currentPackageIndexes[subjectGroup.subject_id] === 0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m15 18-6-6 6-6" />
                                </svg>
                            </button>
                            <button class="nav-button" @click="nextPackage(subjectGroup.subject_id)" :disabled="currentPackageIndexes[subjectGroup.subject_id] === subjectGroup.packages.length - 1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m9 18 6-6-6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="packages-slider">
                    <div class="package-card" v-if="subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]]">
                        <div class="package-header">
                            <div class="package-title">
                                <h5>{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].name }}</h5>
                                <div class="period-badge">{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].level_name }}</div>
                            </div>
                            <div class="package-actions">
                                <img class="icon-image-md" src="/images/edit.svg" @click="showEditPackage(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]])" />
                                <img class="icon-image-md" src="/images/delete.svg" @click="deletePackage(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].id)" />
                            </div>
                        </div>
                        <div class="package-content">
                            <p class="package-description">{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].description }}</p>
                            <div class="package-info">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                                        </svg>
                                    </div>
                                    <span>{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].number_of_lessons }} buổi</span>
                                </div>
                                <div class="info-item">
                                    <div class="info-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                    </div>
                                    <span>{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].duration }} phút/buổi</span>
                                </div>
                                <div class="info-item">
                                    {{ $helper.formatCurrency(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].price) }}
                                </div>
                            </div>
                            <div class="package-features">
                                <template v-for="(feature, index) in subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].features" :key="index">
                                    <div v-if="index < 3 || showAllFeatures[subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].id]" class="feature-item">
                                        <i class="fa-regular fa-circle-check"></i>
                                        <span>{{ feature }}</span>
                                    </div>
                                </template>
                                <div v-if="subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].features.length > 3" class="feature-toggle" @click="toggleFeatures(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].id)">
                                    <span v-if="!showAllFeatures[subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].id]">
                                        + {{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].features.length - 3 }} tính năng khác
                                    </span>
                                    <span v-else>
                                        Thu gọn
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-no-data" v-else>
            <div class="logo-wrapper">
                <svg class="icon-base icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
            </div>

            <div class="content-wrapper">
                <h4>Chưa có gói dịch vụ gia sư</h4>
                <p>Thêm gói dịch vụ gia sư để tăng độ tin cậy với học sinh</p>

                <button class="btn-add-new" @click="showCreatePackageModal">
                    <img class="icon-image-lg" src="/images/plus.svg" />
                    thêm gói dịch vụ
                </button>
            </div>
        </div>
    </div>

    <!-- Performance Section -->
    <!-- <div class="section">
                <div class="section-header">
                    <div class="header-left">
                        <i class="icon fas fa-chart-line"></i>
                        <span>Hiệu suất giảng dạy</span>
                    </div>
                </div>

                <div class="performance-grid">
                    <div class="performance-card blue">
                        <div class="performance-icon">
                            <i class=" fas fa-chart-line"></i>
                        </div>
                        <div class="performance-value">25%</div>
                        <div class="performance-label">Tăng điểm trung bình</div>
                    </div>
                    <div class="performance-card green">
                        <div class="performance-icon">
                            <i class=" fas fa-bullseye"></i>
                        </div>
                        <div class="performance-value">92%</div>
                        <div class="performance-label">Tỷ lệ đạt mục tiêu</div>
                    </div>
                    <div class="performance-card purple">
                        <div class="performance-icon">
                            <i class=" fas fa-heart"></i>
                        </div>
                        <div class="performance-value">96%</div>
                        <div class="performance-label">Mức độ hài lòng</div>
                    </div>
                </div>
            </div> -->

    <!-- Reviews Section -->
    <!-- <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg class="icon-base icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                </svg>
                <span>Đánh giá gần đây</span>
            </div>
            <a href="#" class="view-all">Xem tất cả <i class=" fas fa-chevron-right"></i></a>
        </div>

        <div class="reviews-list list-card">
            <div v-for="review in reviews" :key="review.id" class="review-item card-item">
                <div class="reviewer-info">
                    <img :src="review.avatar" :alt="review.name" class="reviewer-avatar">
                    <div class="reviewer-details">
                        <h6>{{ review.name }}</h6>
                        <span class="review-date">{{ review.date }}</span>
                    </div>
                    <div class="review-rating">★★★★★</div>
                </div>
                <p class="review-content">{{ review.content }}</p>
            </div>
        </div>
    </div> -->
</div>

<base-modal :is-open="showEditProfileModal" title="Chỉnh sửa thông tin" description="Cập nhật thông tin cá nhân của bạn. Nhấn lưu khi hoàn tất." size="small" @close="showEditProfileModal = false">
    <div class="modal-edit">
        <div class="form-group-container">
            <div class="form-group">
                <label>Họ</label>
                <div>
                    <input type="text" v-model="userDataAction.firstName" placeholder="Nguyễn">
                </div>
            </div>
            <div class="form-group">
                <label>Tên</label>
                <div>
                    <input type="text" v-model="userDataAction.lastName" placeholder="Văn A">
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

<!-- Chỉnh sửa học vấn -->
<base-modal :is-open="showEditEducationModal" title="Chỉnh sửa học vấn" description="Cập nhật thông tin học vấn của bạn. Nhấn lưu khi hoàn tất." size="small" @close="showEditEducationModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Tên trường/viện</label>
            <div>
                <input type="text" v-model="userDataAction.education.school_name" placeholder="Nhập tên trường">
            </div>
        </div>
        <div class="form-group">
            <label>Bằng cấp/Chuyên ngành</label>
            <div>
                <input type="text" v-model="userDataAction.education.major" placeholder="Nhập ngành học">
            </div>
        </div>
        <div class="form-group">
            <label>Thời gian</label>
            <div>
                <input type="text" v-model="userDataAction.education.time" placeholder="VD: 2020-2024">
            </div>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết</label>
            <div>
                <textarea v-model="userDataAction.education.description" placeholder="Nhập mô tả" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditEducationModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateEducation">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<!-- Chỉnh sửa kinh nghiệm -->
<base-modal :is-open="showEditExperienceModal" title="Chỉnh sửa kinh nghiệm" description="Cập nhật thông tin kinh nghiệm của bạn. Nhấn lưu khi hoàn tất." size="small" @close="showEditExperienceModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Tên trường/viện</label>
            <div>
                <input type="text" v-model="userDataAction.experience.name" placeholder="Nhập tên trường">
            </div>
        </div>
        <div class="form-group">
            <label>Vị trí công việc</label>
            <div>
                <input type="text" v-model="userDataAction.experience.position" placeholder="Nhập vị trí công việc">
            </div>
        </div>
        <div class="form-group">
            <label>Thời gian</label>
            <div>
                <input type="text" v-model="userDataAction.experience.time" placeholder="VD: 2020-2024">
            </div>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết</label>
            <div>
                <textarea v-model="userDataAction.experience.description" placeholder="Nhập mô tả" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditExperienceModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateExperience">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showAddEducationModal" title="Thêm học vấn" description="Thêm học vấn mới vào hồ sơ của bạn." size="small" @close="showAddEducationModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Tên trường/viện</label>
            <div>
                <input type="text" v-model="newEducation.school_name" placeholder="Nhập tên trường">
            </div>
        </div>
        <div class="form-group">
            <label>Bằng cấp/Chuyên ngành</label>
            <div>
                <input type="text" v-model="newEducation.major" placeholder="Nhập ngành học">
            </div>
        </div>
        <div class="form-group">
            <label>Thời gian</label>
            <div>
                <input type="text" v-model="newEducation.time" placeholder="VD: 2020-2024">
            </div>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết</label>
            <div>
                <textarea v-model="newEducation.description" placeholder="Nhập mô tả" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddEducationModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewEducation">Thêm</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showAddExperienceModal" title="Thêm kinh nghiệm" description="Thêm kinh nghiệm mới vào hồ sơ của bạn." size="small" @close="showAddExperienceModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Tên tổ chức/trường học</label>
            <div>
                <input type="text" v-model="newExperience.name" placeholder="Nhập tên tổ chức">
            </div>
        </div>
        <div class="form-group">
            <label>Vị trí công việc</label>
            <div>
                <input type="text" v-model="newExperience.position" placeholder="Nhập vị trí công việc">
            </div>
        </div>
        <div class="form-group">
            <label>Thời gian</label>
            <div>
                <input type="text" v-model="newExperience.time" placeholder="VD: 2020-2024">
            </div>
        </div>
        <div class="form-group">
            <label>Mô tả chi tiết</label>
            <div>
                <textarea v-model="newExperience.description" placeholder="Nhập mô tả" rows="4"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddExperienceModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewExperience">Thêm</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showAddPackageModal" title="Thêm gói dịch vụ" description="Thêm gói dịch vụ gia sư mới vào hồ sơ của bạn." size="small" @close="showAddPackageModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Môn học</label>
            <base-select v-model="newPackage.subject_id" :options="subjectOptions" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." @update:model-value="handleSubjectSelect" />
        </div>

        <div class="form-group">
            <label>Cấp độ</label>
            <base-select v-model="newPackage.level_id" :options="availableLevels" type="default" placeholder="Chọn cấp độ" :disabled="!newPackage.subject_id" />
        </div>

        <div class="form-group">
            <label>Tên gói dịch vụ</label>
            <input type="text" v-model="newPackage.name" placeholder="VD: Gói cơ bản, Gói nâng cao, Gói luyện thi...">
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea v-model="newPackage.description" placeholder="Mô tả ngắn gọn về gói dịch vụ" rows="3"></textarea>
        </div>

        <div class="form-group-container">
            <div class="form-group">
                <label>Số buổi học</label>
                <input type="number" v-model="newPackage.number_of_lessons" placeholder="VD: 10" min="1">
            </div>

            <div class="form-group">
                <label>Thời lượng (phút)</label>
                <input type="number" v-model="newPackage.duration" placeholder="VD: 90" min="1">
            </div>

            <div class="form-group">
                <label>Giá (VNĐ)</label>
                <input type="number" v-model="newPackage.price" placeholder="VD: 2.000.000" min="0">
                <div v-if="selectedLevelPrice" class="price-hint">
                    Gợi ý giá: {{ $helper.formatCurrency(selectedLevelPrice) }}/giờ
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="label-group">
                <label>Tính năng/Đặc điểm của gói</label>
                <button class="btn-add" @click="addFeature">
                    <img class="icon-image-lg" src="/images/plus.svg" />
                    Thêm
                </button>
            </div>
            <div class="features-list">
                <div v-for="(feature, index) in packageFeatures" :key="index" class="feature-input">
                    <input type="text" v-model="packageFeatures[index]" placeholder="Tính năng 1">
                    <button class="remove-feature" @click="removeFeature(index)" v-if="packageFeatures.length > 1">
                        <svg class="icon-base icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddPackageModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewPackage" :disabled="!isValidPackageInput">Thêm</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showEditPackageModal" title="Chỉnh sửa gói dịch vụ" description="Cập nhật thông tin gói dịch vụ gia sư." size="small" @close="showEditPackageModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <label>Môn học</label>
            <base-select v-model="newPackage.subject_id" :options="subjectOptions" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." @update:model-value="handleSubjectSelect" />
        </div>

        <div class="form-group">
            <label>Cấp độ</label>
            <base-select v-model="newPackage.level_id" :options="availableLevels" type="default" placeholder="Chọn cấp độ" :disabled="!newPackage.subject_id" />
        </div>

        <div class="form-group">
            <label>Tên gói dịch vụ</label>
            <input type="text" v-model="newPackage.name" placeholder="VD: Gói cơ bản, Gói nâng cao, Gói luyện thi...">
        </div>

        <div class="form-group">
            <label>Mô tả</label>
            <textarea v-model="newPackage.description" placeholder="Mô tả ngắn gọn về gói dịch vụ" rows="3"></textarea>
        </div>

        <div class="form-group-container">
            <div class="form-group">
                <label>Số buổi học</label>
                <input type="number" v-model="newPackage.number_of_lessons" placeholder="VD: 10" min="1">
            </div>

            <div class="form-group">
                <label>Thời lượng (phút)</label>
                <input type="number" v-model="newPackage.duration" placeholder="VD: 90" min="1">
            </div>

            <div class="form-group">
                <label>Giá (VNĐ)</label>
                <input type="number" v-model="newPackage.price" placeholder="VD: 2.000.000" min="0">
            </div>
        </div>

        <div class="form-group">
            <div class="label-group">
                <label>Tính năng/Đặc điểm của gói</label>
                <button class="btn-add" @click="addFeature">
                    <img class="icon-image-lg" src="/images/plus.svg" />
                    Thêm
                </button>
            </div>

            <div class="features-list">
                <div v-for="(feature, index) in packageFeatures" :key="index" class="feature-input">
                    <input type="text" v-model="packageFeatures[index]" placeholder="Tính năng 1">
                    <button class="remove-feature" @click="removeFeature(index)" v-if="packageFeatures.length > 1">
                        <svg class="icon-base icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditPackageModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updatePackage" :disabled="!isValidPackageInput">Cập nhật</button>
        </div>
    </div>
</base-modal>
</template>

<style scoped>
@import url('@css/profile.css');
/* Main Content Sections */
/* .section {
    background: white;
    padding: 1.3rem var(--padding);
    display: grid;
    gap: 2rem;
} */

.overview {
    display: grid;
    gap: 2rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-add {
    /* padding: 6px 12px; */
    border: none;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 3px;
}

.list-card {
    display: grid;
    gap: 1.3rem;
}

.card-item {
    display: flex;
    gap: 1.2rem;
    /* margin-bottom: 20px; */
    border: 1px solid rgb(230 231 235);
    border-radius: 8px;
    padding: 1rem;
}

.logo-wrapper {
    width: 40px;
    height: 40px;
    background: #e3e3e5;
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.education-content,
.experience-content {
    flex: 1;
}

.education-content h4,
.experience-content h4 {
    font-size: var(--font-size-heading-6);
    font-weight: 600;
    margin: 0 0 4px 0;
}

.education-content p,
.experience-content p {
    color: #6B7280;
    margin: 0 0 4px 0;
}

.details {
    font-size: var(--font-size-small);
    color: #6B7280;
}

/* Performance Cards */
.performance-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.2rem;
}

.performance-card {
    padding: 24px;
    border-radius: 8px;
    text-align: center;
}

.performance-card.blue {
    background: #EFF6FF;
    color: #1E40AF;
}

.performance-card.green {
    background: #ECFDF5;
    color: #065F46;
}

.performance-card.purple {
    background: #F5F3FF;
    color: #5B21B6;
}

.performance-icon {
    margin-bottom: 1.2rem;
}

.performance-value {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 8px;
}

.performance-label {
    font-size: var(--font-size-small);
}

/* Reviews */
.view-all {
    color: #111827;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: var(--font-size-small);
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.review-item {
    padding: 1.2rem;
    background: #F9FAFB;
    border-radius: 8px;
    display: inline;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

.reviewer-details h6 {
    margin: 0;
    font-weight: 600;
}

.review-date {
    font-size: var(--font-size-mini);
    color: #6B7280;
}

.review-rating {
    margin-left: auto;
    color: #FBBF24;
}

.review-content {
    margin: 0;
    color: #6B7280;
    line-height: 1.5;
    font-size: var(--font-size-small);
}

@media (max-width: 1024px) {
    .profile-container {
        grid-template-columns: 1fr;
    }

    .profile-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .performance-grid {
        grid-template-columns: 1fr;
    }

    .nav-tabs {
        gap: 1.2rem;
        padding: 12px 1.2rem;
    }
}

/* Stats Section Styles */

.section-content {
    padding: 20px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.2rem;
    font-weight: 600;
    border-bottom: 1px solid #E5E7EB;
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.stat-item {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 1.2rem;
    text-align: center;
    display: grid;
    justify-content: center;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 4px;
}

.stat-label {
    color: #6B7280;
    font-size: var(--font-size-small);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

/* Subjects Section Styles */
.package-subject-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.package-subject-header h3 {
    font-size: var(--font-size-heading-6);
    margin: 0;
}

.package-subject-header .icon {
    width: 1rem;
    height: 1rem;
}

.subjects-section .card-no-data {
    border: none;
    padding: 0;
    background: none;
}

.subject-item {
    padding: 1.2rem;
    border-radius: 8px;
    margin-bottom: 12px;
}

.subject-item.expert {
    background: #FFFBEB;
}

.subject-item.experienced {
    background: #F0FDF4;
}

.subject-item.new {
    background: #F0F9FF;
}

.subject-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.8rem;
    gap: 0.5rem;
    font-weight: 600;
}

.subject-header h3 {
    font-size: var(--font-size-heading-6);
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.subject-score {
    display: flex;
    align-items: center;
    gap: 8px;
}

.subject-score img {
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.2s;
    width: 1.2rem;
    height: 1.2rem;
}

.level {
    font-size: var(--font-size-small);
}

.expert .level {
    color: #92400E;
}

.experienced .level {
    color: #065F46;
}

.new .level {
    color: #0369A1;
}

.score {
    font-size: var(--font-size-small);
    color: #6B7280;
}

.experience-info {
    display: grid;
    gap: 8px;
    font-size: var(--font-size-small);
}

.experience-info-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.experience-info-item-left {
    display: flex;
    align-items: center;
    gap: 5px;
}

.progress-bar {
    flex: 1;
    height: 6px;
    background: #ffffff;
    border-radius: 3px;
    overflow: hidden;
    margin-left: 8px;
}

.expert .progress {
    background: #F59E0B;
}

.experienced .progress {
    background: #10B981;
}

.new .progress {
    background: #3B82F6;
}

.progress {
    height: 100%;
    border-radius: 3px;
}

.btn-add-subject {
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
    margin-top: 1.5rem;
}

.btn-add-subject:hover {
    background: #F9FAFB;
}

.modal-edit {
    display: grid;
    gap: 1rem;
}

.level-group {
    display: grid;
    gap: 0.5rem;
}

.level-group label {
    font-weight: 600;
}

.checkbox-group-container {
    display: flex;
    flex-wrap: wrap;
}

.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon input {
    padding-right: 40px;
    width: 100%;
}

.input-with-icon .icon-base icon-md {
    position: absolute;
    right: 12px;
    color: #6B7280;
}

.hint-text {
    display: block;
    font-size: var(--font-size-small);
    color: #6B7280;
    margin-top: 4px;
}

.level-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    margin-top: 8px;
}

.level-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 1.2rem;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.level-item:hover {
    border-color: #111827;
}

.level-item.active {
    /* background: #F3F4F6; */
    border-color: #8d8d8d;
}

.level-name {
    font-size: var(--font-size-small);
    font-weight: 500;
}

.level-check {
    width: 20px;
    height: 20px;
    color: #111827;
}

.experience-input {
    position: relative;
    display: flex;
    align-items: center;
}

.experience-input input {
    width: 100%;
    padding-right: 50px;
}

.unit {
    position: absolute;
    right: 12px;
    color: #6B7280;
    font-size: var(--font-size-small);
}

.level-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 8px;
}

.level-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.level-left input {
    width: 1rem;
    height: 1rem;
}

.level-icon {
    width: 40px;
    height: 40px;
    background: #e3e3e5;
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.level-icon img {
    width: 18px;
    height: 18px;
}

.level-name {
    font-size: var(--font-size-small);
    font-weight: 500;
    min-width: 80px;
}

.price-input {
    position: relative;
    display: flex;
    align-items: center;
    width: 120px;
}

.price-input input {
    width: 100%;
    /* padding-right: 120px; */
}

.price-input input:disabled {
    background: #F3F4F6;
    cursor: not-allowed;
}

.level-hint {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    font-size: var(--font-size-small);
    color: #6B7280;
}

.selected-count {
    font-weight: 500;
    color: #111827;
}

.btn-md btn-primary {
    background: #111827;
}

.subject-select {
    position: relative;
}

.subject-select-button {
    width: 100%;
    padding: 12px 1.2rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    color: #6b7280;
    font-size: var(--font-size-small);
}

.subject-select-button:hover {
    background-color: rgb(244 244 245);
    color: black;
}

.subject-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    margin-top: 4px;
    z-index: 10;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.search-input {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0.8rem 0.8rem 0.8rem 0;
    border-bottom: 1px solid #e5e7eb;
}

.icon-search-wrapper {
    padding: 0 0.8rem;
}

.search-input input {
    width: 100%;
    padding: 0;
    border: none;
}

.search-icon {
    color: #9ca3af;
    margin: 0 0.8rem;
}

.subject-list {
    padding: 4px 0;
    max-height: 300px;
    overflow-y: auto;
}

.subject-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem 1rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.subject-option:hover {
    background: #f3f4f6;
}

.checkbox {
    width: 1.2rem;
    height: 1.2rem;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkbox svg {
    color: black;
}

.subject-option.selected .checkbox svg {
    color: black;
}

/* Package Styles */
.packages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.package-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.package-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

.package-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.package-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.package-title h5 {
    margin: 0;
    font-size: var(--font-size-heading-5);
    font-weight: 600;
    color: #111827;
}

.package-actions {
    display: flex;
    gap: 0.75rem;
}

.package-content {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.package-description {
    margin: 0;
    color: #6B7280;
    font-size: var(--font-size-small);
    line-height: 1.5;
}

.package-info {
    display: flex;
    gap: 1rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #374151;
    font-weight: 500;
    font-size: var(--font-size-small);
}

.info-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6B7280;
}

.package-features {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    padding-top: 1rem;
    border-top: 1px solid #E5E7EB;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: var(--font-size-small);
    color: #374151;
}

.feature-item svg {
    color: #10B981;
    flex-shrink: 0;
}

.form-group-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group input,
.form-group textarea {
    padding: 0.625rem;
    border: 1px solid #D1D5DB;
    border-radius: 0.375rem;
    font-size: var(--font-size-small);
    width: 100%;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #9CA3AF;
}

.features-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.feature-input {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.feature-input input {
    flex: 1;
}

.remove-feature {
    padding: 0.25rem;
    border: none;
    background: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.subject-warning {
    background: #FEF3C7;
    border: 1px solid #FCD34D;
    border-radius: 0.375rem;
    padding: 0.75rem;
    margin-bottom: 1rem;
}

.subject-warning p {
    color: #92400E;
    margin: 0 0 0.5rem 0;
    font-size: var(--font-size-small);
}

.create-subject-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.create-subject-option input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
}

.create-subject-option label {
    font-size: var(--font-size-small);
    color: #374151;
    cursor: pointer;
}

.subject-option-new {
    color: #2563EB;
    font-weight: 500;
}

.price-hint {
    font-size: var(--font-size-mini);
    color: #6B7280;
    margin-top: 4px;
}

.feature-toggle {
    color: #2563EB;
    font-size: var(--font-size-small);
    cursor: pointer;
    margin-top: 0.5rem;
}

.feature-toggle:hover {
    text-decoration: underline;
}

.subject-package-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.package-group-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.package-navigation {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.package-pagination {
    font-size: var(--font-size-small);
    color: #6B7280;
}

.navigation-buttons {
    display: flex;
    gap: 0.5rem;
}

.nav-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.7rem;
    height: 1.7rem;
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
}

.nav-button:hover:not(:disabled) {
    border-color: #D1D5DB;
    background: #F9FAFB;
}

.nav-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.packages-slider {
    position: relative;
    width: 100%;
}

.packages-grid {
    display: block;
}

.package-card {
    width: 100%;
    margin: 0;
}
</style>

<script setup>
    import { ref, reactive, onMounted, getCurrentInstance, computed } from 'vue';
    import { useStore } from 'vuex';
    import BaseInput from '@/components/BaseInput.vue';

    const store = useStore();
    const {proxy} = getCurrentInstance();

    const educationLevels = computed(() => store.state.configuration.educationLevels);
    const selectedLevelsOfSubject = ref([]);
    const subjectLevels = ref([]);
    const userDataDetail = ref({});
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

    const showEditProfileModal = ref(false);
    const showEditEducationModal = ref(false);
    const showEditExperienceModal = ref(false);
    const showEditSubjectModal = ref(false);

    const isValidSubjectInput = computed(() => {
        return userDataAction.subject.years_of_experience &&
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

    const addSubjectWithExperience = () => {
        if (userDataAction.subject.name && userDataAction.subject.years_of_experience) {
            const levels = selectedLevelsOfSubject.value.map(levelId => {
                const level = educationLevels.value.find(l => l.id === levelId);
                return {
                    level_id: levelId,
                    price: level.price
                };
            });

            userDataAction.subject.levels = levels;
            showEditSubjectModal.value = false;
            selectedLevelsOfSubject.value = [];
            educationLevels.value.forEach(level => level.price = '');
        }
    };

    const cancelAddSubject = () => {
        showEditSubjectModal.value = false;
        selectedLevelsOfSubject.value = [];
        educationLevels.value.forEach(level => level.price = '');
    };

    // Lấy dữ liệu user từ API
    const getUserDataDetail = async () => {
        try {
            const response = await proxy.$api.apiGet('me/profile');
            userDataDetail.value = response;

            Object.assign(userDataAction, {
                firstName: response.first_name,
                lastName: response.last_name,
                address: response.address,
                phone: response.phone,
                about_you: response.about_you
            });
        } catch (error) {
            console.error('Failed to fetch user data:', error);
        }
    };

    const updateProfile = async () => {
        try {
            const response = await proxy.$api.apiPut('me/profile', userDataAction);
            userDataDetail.value = response.data;
            showEditProfileModal.value = false;
        } catch (error) {
            console.error('Failed to update profile:', error);
        }
    };

    const updateEducation = async () => {
        try {
            const response = await proxy.$api.apiPut('me/education', userDataAction.education);
            userDataDetail.value = response.data;
            showEditEducationModal.value = false;
        } catch (error) {
            console.error('Failed to update education:', error);
        }
    };

    const updateExperience = async () => {
        try {
            const response = await proxy.$api.apiPut('me/experience', userDataAction.experience);
            userDataDetail.value = response.data;
            showEditExperienceModal.value = false;
        } catch (error) {
            console.error('Failed to update experience:', error);
        }
    };

    const updateSubject = async () => {
        try {
            const response = await proxy.$api.apiPut('me/subject', userDataAction.subject);
            userDataDetail.value = response.data;
            showEditSubjectModal.value = false;
        } catch (error) {
            console.error('Failed to update subject:', error);
        }
    };

    const showEditEducation = (data) => {
        showEditEducationModal.value = true;
        userDataAction.education = data;
    };

    const showEditExperience = (data) => {
        showEditExperienceModal.value = true;
        userDataAction.experience = data;
    };

    const showEditSubject = (data) => {
        showEditSubjectModal.value = true;
        userDataAction.subject = data;
        selectedLevelsOfSubject.value = data.levels.map(level => level.level_id);
    };

    const addLevelToSubject = (levelId) => {
        const index = selectedLevelsOfSubject.value.indexOf(levelId);
        if (index === -1) {
            selectedLevelsOfSubject.value.push(levelId);
            userDataAction.subject.levels.push({
                level_id: levelId,
                price: ''
            });
        } else {
            selectedLevelsOfSubject.value.splice(index, 1);
            userDataAction.subject.levels = userDataAction.subject.levels.filter(
                level => level.level_id !== levelId
            );
        }
    };

    const reviews = ref([
        {
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

    onMounted(getUserDataDetail);
</script>

<template>
<div class="profile-container">
    <!-- Left Sidebar -->
    <div class="container">
        <div class="profile-sidebar">
            <div class="profile-info">
                <div class="avatar">
                    <img src="/path-to-avatar.jpg" alt="Avatar">
                    <span class="status-dot"></span>
        </div>
                <h1 class="name">{{ userDataDetail.full_name }}</h1>
                <div class="role-badge">{{ userDataDetail.role }}</div>

                <div class="rating">
                    <div class="stars">★★★★★</div>
                    <span class="rating-score">4.8</span>
                    <span class="rating-count">(100 đánh giá)</span>
        </div>

                <div class="contact-info">
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-base" src="/images/email.svg" alt="Email">
        </div>
                        <span>{{ userDataDetail.email }}</span>
        </div>
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-base" src="/images/phone.svg" alt="Phone">
    </div>
                        <span>{{ userDataDetail.phone }}</span>
            </div>
                    <div class="contact-item">
                        <div class="icon-wrapper">
                            <img class="icon-base" src="/images/location.svg" alt="Location">
                </div>
                        <span>{{ userDataDetail.address || 'Chưa cập nhật' }}</span>
                </div>
            </div>

                <div class="bio">
                    Giới thiệu bản thân: {{ userDataDetail.about_you || 'Chưa cập nhật' }}
        </div>

                <div class="actions">
                    <button class="btn-edit" @click="showEditProfileModal = true">
                        <i class=" fas fa-edit"></i> Chỉnh sửa
                    </button>
                    <button class="btn-message">
                        <i class=" fas fa-comment"></i> Nhắn tin
                    </button>
                </div>
            </div>

            <div class="stats-section">
                <h2 class="section-title">
                    <i class=" fas fa-chart-line"></i>
                    Thống kê
                </h2>
                <div class="stats-grid">
            <div class="stat-item">
                        <span class="stat-value">45</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-base" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Học sinh</span>
                        </span>
            </div>
            <div class="stat-item">
                        <span class="stat-value">320</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-base" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                            <span>Buổi học</span>
                        </span>
            </div>
            <div class="stat-item">
                        <span class="stat-value">640</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-base" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>Giờ dạy</span>
                        </span>
            </div>
            <div class="stat-item">
                        <span class="stat-value">98%</span>
                        <span class="stat-label">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-base" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
                            <span>Hoàn thành</span>
                        </span>
                </div>
            </div>
            </div>

            <div class="subjects-section">
                <h2 class="section-title">
                    <svg class="icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path></svg>
                    Môn học giảng dạy
                </h2>
                <div class="subjects-list">
                    <div class="subject-item" :class="subject.class" v-for="subject in userDataDetail.user_subjects" :key="subject.id">
                        <div class="subject-header">
                            <h3>{{ subject.name }}</h3>
                            <div class="subject-score">
                                <span class="level">{{ subject.level }}</span>
                                <img src="/images/edit.svg" @click="showEditSubject(subject)" />
                            </div>
                        </div>
                        <div class="experience-info">
                            <div class="experience-info-item">
                                <div class="experience-info-item-left">
                                    <i class=" far fa-clock"></i>
                                    <span>{{ subject.years_of_experience }} năm kinh nghiệm</span>
                                </div>
                                <span class="experience-info-item-right level">{{ subject.years_of_experience }}/{{ subject.milestone }}</span>
                            </div>
                <div class="progress-bar">
                                <div class="progress" :style="{ width: subject.progress + '%' }"></div>
                </div>
                        </div>
                    </div>
                </div>
                <button class="btn-add-subject">
                    <img class="icon-base" src="/images/plus.svg" />
                    thêm
                </button>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Navigation Tabs -->
            <div class="nav-tabs">
                <div class="tab active">
                    <img class="icon-base" src="/images/user.svg" alt="User">
                    <span>Tổng quan</span>
                    </div>
                <div class="tab">
                    <i class=" fas fa-calendar"></i>
                    <span>Lịch dạy</span>
                </div>
                <div class="tab">
                    <i class=" fas fa-star"></i>
                    <span>Đánh giá</span>
            </div>
                <div class="tab">
                    <i class=" fas fa-file"></i>
                    <span>Tài liệu</span>
                </div>
        </div>

            <!-- Education Section -->
            <div class="section">
                <div class="section-header">
                    <div class="header-left">
                        <img class="icon-base" src="/images/education.svg" />
                        <span>Học vấn</span>
                    </div>
                    <button class="btn-add">
                        <img class="icon-base" src="/images/plus.svg" />
                        thêm
                    </button>
                </div>

                <div class="education-list list-card">
                    <div v-for="edu in userDataDetail.user_educations" :key="edu.id" class="education-item card-item">
                        <div class="school-logo">
                            <img class="icon-base" src="/images/education.svg" />
                        </div>
                        <div class="education-content">
                            <h4>{{ edu.school_name }}</h4>
                            <p>{{ edu.major }}</p>
                            <p class="details">{{ edu.description }}</p>
                        </div>
                        <div class="period-badge-container">
                            <div class="period-badge">{{ edu.time }}</div>
                            <img class="edit-icon" src="/images/edit.svg" @click="showEditEducation(edu)" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Experience Section -->
            <div class="section">
                <div class="section-header">
                    <div class="header-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-base"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>
                        <span>Kinh nghiệm giảng dạy</span>
                    </div>
                    <button class="btn-add">
                        <img class="icon-base" src="/images/plus.svg" />
                        thêm
                    </button>
                </div>

                <div class="experience-list list-card">
                    <div v-for="exp in userDataDetail.user_experiences" :key="exp.id" class="experience-item card-item">
                        <div class="company-logo">
                            <i class=" fas fa-building"></i>
                        </div>
                        <div class="experience-content">
                            <h4>{{ exp.name }}</h4>
                            <p>{{ exp.position }}</p>
                            <p class="details">{{ exp.description }}</p>
                        </div>
                        <div class="period-badge-container">
                            <div class="period-badge">{{ exp.time }}</div>
                            <img class="edit-icon" src="/images/edit.svg" @click="showEditExperience(exp)" />
                        </div>
                    </div>
            </div>
        </div>

            <!-- Performance Section -->
            <div class="section">
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
        </div>

            <!-- Reviews Section -->
            <div class="section">
                <div class="section-header">
                    <div class="header-left">
                        <svg class="icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
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
        </div>
    </div>
</div>
</div>

<base-modal :is-open="showEditProfileModal" title="Chỉnh sửa thông tin" description="Cập nhật thông tin cá nhân của bạn. Nhấn lưu khi hoàn tất." size="small" @close="showEditProfileModal = false">
    <div class="modal-edit">
        <div class="form-group-containner">
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

        <div class="form-group-containner">
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
            <button class="cancel-button" @click="showEditProfileModal = false">Hủy</button>
            <button class="btn-base" @click="updateProfile">Lưu thay đổi</button>
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
            <button class="cancel-button" @click="showEditEducationModal = false">Hủy</button>
            <button class="btn-base" @click="updateEducation">Lưu thay đổi</button>
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
            <button class="cancel-button" @click="showEditExperienceModal = false">Hủy</button>
            <button class="btn-base" @click="updateExperience">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<!-- Chỉnh sửa môn học -->
<base-modal :is-open="showEditSubjectModal" title="Chỉnh sửa môn học" description="Cập nhật thông tin môn học." size="small" @close="showEditSubjectModal = false">
    <div class="modal-edit">
        <div class="form-group">
            <div>
                <base-input
                    v-model="userDataAction.subject.name"
                    label="Tên môn học"
                    placeholder="Nhập tên môn học"
                    required
                />
            </div>
        </div>

        <div class="form-group">
            <div class="experience-input">
                <base-input
                    v-model="userDataAction.subject.years_of_experience"
                    type="number"
                    label="Số năm kinh nghiệm"
                    placeholder="Nhập số năm kinh nghiệm"
                    unit="năm"
                    :min="0"
                    :step="0.5"
                    required
                />
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
                        <input
                            type="checkbox"
                            :value="level.id"
                            v-model="selectedLevelsOfSubject"
                            @change="toggleLevelSelection(level)">
                        <div class="level-name">{{ level.name }}</div>
                    </div>
                    <div class="form-group" @click.stop>
                        <base-input
                            v-model="level.price"
                            type="number"
                            placeholder="Nhập học phí"
                            unit="nghìn đồng/buổi"
                            :min="0"
                            :step="1"
                            :disabled="!selectedLevelsOfSubject.includes(level.id)"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="cancel-button" @click="showEditSubjectModal = false">Hủy</button>
            <button class="btn-base" @click="updateSubject">Cập nhật</button>
        </div>
    </div>
</base-modal>

<base-modal :is-open="showExperienceModal" title="Thêm môn học" size="small" @close="cancelAddSubject">
    <div class="modal-content">
        <div class="form-group">
            <label>Số năm kinh nghiệm dạy môn {{ selectedSubject?.name }}</label>
            <div>
                <input type="number" v-model="yearsOfExperience" placeholder="Ví dụ: 3" min="0" step="0.5">
            </div>
            <span class="hint">Nhập số năm kinh nghiệm giảng dạy môn học này</span>
        </div>

        <div class="level-group">
            <label>Cấp độ dạy và học phí</label>
            <div class="level-list">
                <div class="level-item" v-for="level in educationLevels" :key="level.id">
                    <div class="level-left">
                        <div class="level-icon" :class="level.class">
                            <img :src="level.image" :alt="level.name">
                        </div>
                        <input
                            type="checkbox"
                            :value="level.id"
                            v-model="selectedLevelsOfSubject"
                            @change="toggleLevelSelection(level)">
                        <div class="level-name">{{ level.name }}</div>
                    </div>
                    <div class="form-group" @click.stop>
                        <base-input
                            v-model="level.price"
                            type="number"
                            placeholder="Nhập học phí"
                            unit="nghìn đồng/buổi"
                            :min="0"
                            :step="1"
                            :disabled="!selectedLevelsOfSubject.includes(level.id)"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="cancel-button" @click="cancelAddSubject">Hủy</button>
            <button class="btn-base" @click="addSubjectWithExperience" :disabled="!isValidSubjectInput">Thêm</button>
        </div>
    </div>
</base-modal>
</template>

<style scoped>
.profile-container {
    background: #F3F4F6;
}

.container {
    margin: 0 auto;
    padding-top: 2rem;
    padding-bottom: 2rem;
    display: grid;
    grid-template-columns: 450px 1fr;
    gap: 24px;
}

.main-content {
    --padding: 1.7rem;
}

/* Navigation Styles */
.nav-tabs {
    display: flex;
    gap: 32px;
    background: transparent;
    /* padding: 1rem 1.7rem; */
    /* margin-bottom: 0.8rem; */
}

.tab {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #6B7280;
    cursor: pointer;
    padding: 0.8rem var(--padding);
}

.tab:first-child {
    border-top-left-radius: 8px;
}

.tab.active {
    color: #111827;
    background: white;
    position: relative;
}

.tab.active::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: #111827;
}

/* Profile Sidebar */
.profile-sidebar {
    position: sticky;
    top: 24px;
}

.profile-info {
    background: white;
    border-radius: 8px;
    padding: 24px;
    text-align: center;
}

.avatar {
    border: 5px solid #e5e7eb;
    border-radius: 100%;
    width: 110px;
    height: 110px;
    margin: 0 auto 16px;
    position: relative;
}

.avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.status-dot {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 12px;
    height: 12px;
    background: #10B981;
    border-radius: 50%;
    border: 2px solid white;
}

.name {
    font-size: 24px;
    font-weight: 600;
    margin: 0 0 8px;
}

.role-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #e7e7e8;
    border: 1px solid #bebebe;
    border-radius: 16px;
    font-size: var(--font-size-small);
    font-weight: 600;
    margin-bottom: 16px;
}

.rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 24px;
}

.stars {
    color: #FBBF24;
}

.rating-score {
    font-weight: 600;
}

.rating-count {
    color: #6B7280;
    font-size: var(--font-size-small);
}

.contact-info {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.icon-wrapper {
    width: 32px;
    height: 32px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: black;
}

.contact-item span {
    text-align: left;
}

.bio {
    color: #71717a;
    line-height: 1.6;
    font-size: var(--font-size-small);
    text-align: center;
    border-top: 1px solid #e4e4e7;
    padding: 1.5rem 0;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-edit, .btn-message {
    flex: 1;
    padding: 8px;
    border-radius: 6px;
    font-size: var(--font-size-small);
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
}

.btn-edit {
    background: white;
    border: 1px solid #D1D5DB;
    color: #374151;
}

.btn-message {
    background: #111827;
    color: white;
    border: none;
}

/* Main Content Sections */
.section {
    /* border-radius: 8px; */
    /* padding: 0.5rem 2rem; */
    background: white;
    padding: 1.3rem var(--padding);
    /* margin-bottom: 24px; */
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: var(--font-size-heading-5);
}

.header-left .icon,
.section-title .icon {
    width: 22px;
    height: 22px;
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

/* Education & Experience Items */
.list-card {
    display: grid;
    gap: 1.3rem;
}

.card-item {
    display: flex;
    gap: 16px;
    /* margin-bottom: 20px; */
    background: rgb(251 251 252);
    border: 1px solid rgb(230 231 235);
    border-radius: 8px;
    padding: 1rem;
}

.school-logo, .company-logo {
    width: 40px;
    height: 40px;
    background: #e3e3e5;
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.education-content, .experience-content {
    flex: 1;
}

.education-content h4, .experience-content h4 {
    font-size: 16px;
    font-weight: 600;
    margin: 0 0 4px 0;
}

.education-content p, .experience-content p {
    color: #6B7280;
    margin: 0 0 4px 0;
}

.details {
    font-size: var(--font-size-small);
    color: #6B7280;
}

.period-badge-container {
    display: flex;
    height: max-content;
    align-items: center;
    gap: 1rem;
}

.period-badge {
    padding: 4px 12px;
    background: #F3F4F6;
    border: 1px solid #c6c6c6;
    border-radius: 2rem;
    font-size: var(--font-size-small);
    font-weight: 600;
    height: fit-content;
}

.edit-icon {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Performance Cards */
.performance-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
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
    margin-bottom: 16px;
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
    gap: 16px;
}

.review-item {
    padding: 16px;
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
        gap: 16px;
        padding: 12px 16px;
    }
}

/* Stats Section Styles */
.stats-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-top: 24px;
}

.section-title {
    display: flex;
        align-items: center;
    gap: 8px;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E5E7EB;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

.stat-item {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 16px;
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
.subjects-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-top: 24px;
}

.subjects-list {
    margin-bottom: 16px;
}

.subject-item {
    padding: 16px;
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
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-weight: 600;
}

.subject-header h3 {
    font-size: 16px;
    margin: 0;
}

.subject-score {
    display: flex;
    align-items: center;
    gap: 8px;
}
.subject-score img {
    cursor: pointer;
    width: 16px;
    height: 16px;
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

.icon-base {
    width: 20px;
    height: 20px;
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

.input-with-icon .icon-base {
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
    padding: 12px 16px;
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

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.cancel-button {
    padding: 8px 16px;
    border: 1px solid #E5E7EB;
    border-radius: 6px;
    background: white;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
}

.btn-base {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    background: #111827;
    color: white;
    font-weight: 500;
    cursor: pointer;
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
    width: 16px;
    height: 16px;
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

.modal-footer {
    margin-top: 24px;
}

.btn-base {
    background: #111827;
}
</style>

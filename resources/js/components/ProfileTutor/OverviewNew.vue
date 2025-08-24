<template>
<!-- education-section -->
<div class="section-card education-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                    <path d="M22 10v6"></path>
                    <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                </svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Học vấn</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-sm btn-secondary" @click="showAddEducationModal = true">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>
    <div class="main-content">
        <div class="education-list">
            <div class="education-card" v-for="edu in userDataDetail.user_educations" :key="edu.id">
                <div class="education-header">
                    <div class="education-left">
                        <div class="icon-wrapper">
                            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                <path d="M22 10v6"></path>
                                <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                            </svg>
                        </div>
                        <div class="title-wrapper">
                            <span class="title-main">{{ edu.school_name }}</span>
                            <span class="sub-title">{{ edu.major }}</span>
                        </div>
                    </div>
                    <div class="education-right actions-card">
                        <button class="btn-no-bg" @click="showEditEducation(edu)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        </button>
                        <button class="btn-no-bg" @click="deleteEducation(edu.id)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
                <div class="desc">{{ edu.description }}</div>
                <div class="education-content">
                    <div class="time">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                        <span>{{ edu.time }}</span>
                    </div>
                    <div class="star">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l3 6 6 1-4.5 4.5 1 6-5.5-3-5.5 3 1-6L2 9l6-1z"></path>
                        </svg>
                        <span>3.8/4.0</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="add-new-item" @click="showAddEducationModal()">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </div>
            <span class="title">Thêm học vấn mới</span>
            <span class="sub-title">Mở rộng khả năng dạy của bạn</span>
        </div>
    </div>
</div>
<!-- End education -->

<!-- experience-section -->
<div class="section-card experience-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Kinh nghiệm</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-sm btn-secondary" @click="showAddExperienceModal = true">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>
    <div class="main-content">
        <div class="experience-list">
            <div class="experience-card" v-for="exp in userDataDetail.user_experiences" :key="exp.id">
                <div class="experience-header">
                    <div class="experience-left">
                        <div class="icon-wrapper">
                            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>
                        </div>
                        <div class="title-wrapper">
                            <span class="title-main">{{ exp.name }}</span>
                            <span class="sub-title">{{ exp.position }}</span>
                        </div>
                    </div>
                    <div class="experience-right actions-card">
                        <button class="btn-no-bg" @click="showEditExperience(exp)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        </button>
                        <button class="btn-no-bg" @click="deleteExperience(exp.id)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
                <div class="desc">{{ exp.description }}</div>
                <div class="experience-content">
                    <div class="time">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                        <span>{{ exp.time }}</span>
                    </div>
                    <!-- Có thể thêm các trường khác nếu có -->
                </div>
            </div>
        </div>

        <div class="add-new-item" @click="showAddExperienceModal()">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </div>
            <span class="title">Thêm kinh nghiệm mới</span>
            <span class="sub-title">Mở rộng khả năng dạy của bạn</span>
        </div>
    </div>
</div>
<!-- End experience -->

<div class="section-card subject-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Môn học giảng dạy</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-sm btn-secondary" @click="showCreateSubjectModal()">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>
    <div class="main-content">
        <div class="subject-list">
            <div class="subject-card" v-for="subject in userDataDetail.user_subjects" :key="subject.id">
                <div class="subject-header">
                    <div class="subject-header_right">
                        <span class="name">{{ subject.name }}</span>
                        <span class="experience">{{ subject.years_of_experience }} năm kinh nghiệm</span>
                    </div>
                    <div class="actions actions-card">
                        <button class="btn-no-bg" @click="showEditSubject(subject)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        </button>
                        <button class="btn-no-bg" @click="deleteSubject(subject.id)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
                <div class="level-wrapper">
                    <div class="level">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span>{{ subject.level }}</span>
                    </div>
                    <div class="compare">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(249, 206, 105);">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span>{{ subject.years_of_experience }}/10</span>
                    </div>
                </div>
                <div class="ruler-wrapper">
                    <div class="ruler-header">
                        <span>Mức độ thành thạo</span>
                        <span class="percent">{{ subject.progress }}%</span>
                    </div>
                    <div class="ruler-center">
                        <div class="rounded-full">
                            <div class="ruler" :style="{ width: subject.progress + '%' }"></div>
                        </div>
                        <div class="rounded" :style="{ left: `calc(${subject.progress}% - 6px)` }"></div>
                    </div>
                    <div class="ruler-footer">
                        <span>Mới bắt đầu</span>
                        <span>Thành thạo</span>
                    </div>
                </div>
                <!-- <div class="information-detail-gird">
                    <div class="information-item">
                        <span>4</span>
                        <span>Đánh giá</span>
                    </div>
                    <div class="information-item">
                        <span>4</span>
                        <span>Năm kinh nghiệm</span>
                    </div>
                    <div class="information-item">
                        <span>4</span>
                        <span>Học sinh</span>
                    </div>
                </div> -->
            </div>
        </div>
        <div class="add-new-item" @click="showCreateSubjectModal()">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </div>
            <span class="title">Thêm môn học mới</span>
            <span class="sub-title">Mở rộng khả năng dạy của bạn</span>
        </div>
    </div>
</div>
<!-- End subject-taught -->

<div class="section-card subject-package-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Gói dịch vụ gia sư</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-sm btn-secondary" @click="showCreatePackageModal()">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>

    <div class="subject-package-list" v-if="userDataDetail.user_packages?.length > 0">
        <div v-for="subjectGroup in userDataDetail.user_packages" :key="subjectGroup.subject_id" class="subject-package-group">
            <div class="package-group-header">
                <div class="package-subject-header">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path  d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path  d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
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
                            <div class="icon-wrapper">
                                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                            </div>
                            <div class="title-wrapper">
                                <h5>{{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].name }}</h5>
                                <div class="subject-name">{{ subjectGroup.subject_name }} ({{ subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].level_name }})</div>
                            </div>
                        </div>
                        <div class="actions-card">
                            <button class="btn-no-bg" @click="showEditPackage(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]])">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                            </button>
                            <button class="btn-no-bg" @click="deletePackage(subjectGroup.packages[currentPackageIndexes[subjectGroup.subject_id]].id)">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                            </button>
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

    <div class="add-new-item" @click="showCreatePackageModal()">
        <div class="icon-wrapper">
            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"></path>
                <path d="M12 5v14"></path>
            </svg>
        </div>
        <span class="title">Thêm gói dịch vụ mới</span>
        <span class="sub-title">Mở rộng khả năng dạy của bạn</span>
    </div>
</div>

<!-- language-section -->
<div class="section-card language-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Ngôn ngữ</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-sm btn-secondary" @click="showAddLanguageModal = true">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                <span>Thêm mới</span>
            </button>
        </div>
    </div>
    <div class="main-content">
        <div class="language-list">
            <div class="language-card" v-for="lang in userDataDetail.user_languages" :key="lang.id">
                <div class="language-header">
                    <div class="language-header_right">
                        <span class="name">{{ lang.language }}</span>
                        <span class="experience">{{ lang.is_native ? 'Ngôn ngữ mẹ đẻ' : lang.level }}</span>
                    </div>
                    <div class="actions actions-card">
                        <button class="btn-no-bg" @click="handleEditLanguage(lang)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                        </button>
                        <button class="btn-no-bg" @click="handleDeleteLanguage(lang.id)">
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
                <div class="level-wrapper">
                    <div class="level">
                        <svg class="icon-md" style="color: var(--color-primary)" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>
                        <span>{{ lang.level }}</span>
                    </div>
                    <div class="compare" v-if="lang.is_native">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
                        <span>Ngôn ngữ mẹ đẻ</span>
                    </div>
                </div>
                <div class="ruler-wrapper">
                    <div class="ruler-header">
                        <span>Trình độ thành thạo</span>
                        <span class="percent">{{ lang.percent ? lang.percent + '%' : '' }}</span>
                    </div>
                    <div class="ruler-center">
                        <div class="rounded-full">
                            <div class="ruler" :style="{ width: (lang.percent || 60) + '%' }"></div>
                        </div>
                        <div class="rounded" :style="{ left: `calc(${lang.percent || 60}% - 6px)` }"></div>
                    </div>
                    <div class="ruler-footer">
                        <span>A1 (Cơ bản)</span>
                        <span>C2 (Thành thạo)</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="add-new-item" @click="showAddLanguageModal()">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
            </div>
            <span class="title">Thêm ngôn ngữ mới</span>
            <span class="sub-title">Mở rộng khả năng dạy của bạn</span>
        </div>
    </div>
</div>

<!-- Modal: Thêm học vấn -->
<base-modal :is-open="showAddEducationModal" title="Thêm học vấn" size="small" @close="showAddEducationModal = false">
    <div class="modal-content">
        <base-input v-model="newEducation.school_name" label="Tên trường/viện" placeholder="Nhập tên trường" />
        <base-input v-model="newEducation.major" label="Bằng cấp/Chuyên ngành" placeholder="Nhập ngành học" />
        <base-input v-model="newEducation.time" label="Thời gian" placeholder="VD: 2020-2024" />
        <base-input v-model="newEducation.description" label="Mô tả chi tiết" placeholder="Nhập mô tả" type="textarea" rows="4" />

        <div class="form-group file-group">
            <label class="file-label-top text-base">Ảnh bằng cấp *</label>
            <div class="up-file-wrapper">
                <div v-if="educationCertificatePreview" class="preview-image">
                    <img :src="educationCertificatePreview" alt="Preview bằng cấp">
                    <div class="actions-wrapper">
                        <div class="actions">
                            <button type="button" @click.prevent="triggerFile('educationCertificate')">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                            </button>
                            <button type="button" @click.prevent="removeFile('certificate')">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"></path>
                                    <path d="m6 6 12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="up-file">
                    <div class="icon-wrapper" @click="triggerFile('educationCertificate')">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                    </div>
                    <div class="content">
                        <span class="title">Tải lên ảnh bằng cấp</span>
                        <span class="sub-title">Kéo thả file hoặc <a href="#" @click.prevent="triggerFile('educationCertificate')">chọn file</a></span>
                        <span class="desc">PNG, JPG tối đa 10MB</span>
                    </div>
                </div>
                <input type="file" id="educationCertificate" accept="image/*" @change="onFileChange($event, 'certificate')" style="display:none;" />
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddEducationModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewEducation">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Sửa học vấn -->
<base-modal :is-open="showEditEducationModal" title="Chỉnh sửa học vấn" size="small" @close="showEditEducationModal = false">
    <div class="modal-content">
        <base-input v-model="userDataAction.education.school_name" label="Tên trường/viện" placeholder="Nhập tên trường" />
        <base-input v-model="userDataAction.education.major" label="Bằng cấp/Chuyên ngành" placeholder="Nhập ngành học" />
        <base-input v-model="userDataAction.education.time" label="Thời gian" placeholder="VD: 2020-2024" />
        <base-input v-model="userDataAction.education.description" label="Mô tả chi tiết" placeholder="Nhập mô tả" type="textarea" rows="4" />

        <div class="form-group file-group">
            <label class="file-label-top text-base">Ảnh bằng cấp *</label>
            <div class="up-file-wrapper">
                <div v-if="editEducationCertificatePreview" class="preview-image">
                    <img :src="editEducationCertificatePreview" alt="Preview bằng cấp">
                    <div class="actions-wrapper">
                        <div class="actions">
                            <button type="button" @click.prevent="triggerFile('editEducationCertificate')">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg>
                            </button>
                            <button type="button" @click.prevent="removeFile('edit_certificate')">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 6 6 18"></path>
                                    <path d="m6 6 12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="up-file">
                    <div class="icon-wrapper" @click="triggerFile('editEducationCertificate')">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14"></path>
                            <path d="M12 5v14"></path>
                        </svg>
                    </div>
                    <div class="content">
                        <span class="title">Tải lên ảnh bằng cấp</span>
                        <span class="sub-title">Kéo thả file hoặc <a href="#" @click.prevent="triggerFile('editEducationCertificate')">chọn file</a></span>
                        <span class="desc">PNG, JPG tối đa 10MB</span>
                    </div>
                </div>
                <input type="file" id="editEducationCertificate" accept="image/*" @change="onFileChange($event, 'edit_certificate')" style="display:none;" />
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditEducationModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateEducation">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Thêm kinh nghiệm -->
<base-modal :is-open="showAddExperienceModal" title="Thêm kinh nghiệm" size="small" @close="showAddExperienceModal = false">
    <div class="modal-content">
        <base-input v-model="newExperience.name" label="Tên tổ chức/trường học" placeholder="Nhập tên tổ chức" />
        <base-input v-model="newExperience.position" label="Vị trí công việc" placeholder="Nhập vị trí công việc" />
        <base-input v-model="newExperience.time" label="Thời gian" placeholder="VD: 2020-2024" />
        <base-input v-model="newExperience.description" label="Mô tả chi tiết" placeholder="Nhập mô tả" type="textarea" rows="4" />
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddExperienceModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewExperience">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Sửa kinh nghiệm -->
<base-modal :is-open="showEditExperienceModal" title="Chỉnh sửa kinh nghiệm" size="small" @close="showEditExperienceModal = false">
    <div class="modal-content">
        <base-input v-model="userDataAction.experience.name" label="Tên trường/viện" placeholder="Nhập tên trường" />
        <base-input v-model="userDataAction.experience.position" label="Vị trí công việc" placeholder="Nhập vị trí công việc" />
        <base-input v-model="userDataAction.experience.time" label="Thời gian" placeholder="VD: 2020-2024" />
        <base-input v-model="userDataAction.experience.description" label="Mô tả chi tiết" placeholder="Nhập mô tả" type="textarea" rows="4" />
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showEditExperienceModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="updateExperience">Lưu thay đổi</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Thêm gói dịch vụ -->
<base-modal :is-open="showAddPackageModal" title="Thêm gói dịch vụ" size="small" @close="showAddPackageModal = false">
    <div class="modal-content">
        <base-select v-model="newPackage.subject_id" :options="subjectOptions" label="Môn học" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." @update:model-value="handleSubjectSelect" />
        <base-select v-model="newPackage.level_id" :options="availableLevels" label="Cấp độ" type="default" placeholder="Chọn cấp độ" :disabled="!newPackage.subject_id" />
        <base-input v-model="newPackage.name" label="Tên gói dịch vụ" placeholder="VD: Gói cơ bản, Gói nâng cao, Gói luyện thi..." />
        <base-input v-model="newPackage.description" label="Mô tả" placeholder="Mô tả ngắn gọn về gói dịch vụ" type="textarea" rows="3" />

        <div class="form-group-container">
            <base-input v-model="newPackage.number_of_lessons" label="Số buổi học" placeholder="VD: 10" type="number" min="1" />
            <base-input v-model="newPackage.duration" label="Thời lượng (phút)" placeholder="VD: 90" type="number" min="1" />
            <div class="form-group">
                <base-input v-model="newPackage.price" label="Giá (VNĐ)" placeholder="VD: 2.000.000" type="number" min="0" />
                <div v-if="selectedLevelPrice" class="price-hint">
                    Gợi ý giá: {{ $helper.formatCurrency(selectedLevelPrice) }}/giờ
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="label-group">
                <label>Tính năng/Đặc điểm của gói</label>
                <button class="btn-no-bg" @click="addFeature">
                    <img class="icon-image-lg" src="/images/plus.svg" />
                    Thêm
                </button>
            </div>
            <div class="features-list">
                <div v-for="(feature, index) in packageFeatures" :key="index" class="feature-input">
                    <base-input v-model="packageFeatures[index]" placeholder="Tính năng 1" />
                    <button class="btn-no-bg" @click="removeFeature(index)" v-if="packageFeatures.length > 1">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

<!-- Modal: Sửa gói dịch vụ -->
<base-modal :is-open="showEditPackageModal" title="Chỉnh sửa gói dịch vụ" size="small" @close="showEditPackageModal = false">
    <div class="modal-content">
        <base-select v-model="newPackage.subject_id" :options="subjectOptions" label="Môn học" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." @update:model-value="handleSubjectSelect" />
        <base-select v-model="newPackage.level_id" :options="availableLevels" label="Cấp độ" type="default" placeholder="Chọn cấp độ" :disabled="!newPackage.subject_id" />
        <base-input v-model="newPackage.name" label="Tên gói dịch vụ" placeholder="VD: Gói cơ bản, Gói nâng cao, Gói luyện thi..." />
        <base-input v-model="newPackage.description" label="Mô tả" placeholder="Mô tả ngắn gọn về gói dịch vụ" type="textarea" rows="3" />
        <div class="form-group-container">
            <base-input v-model="newPackage.number_of_lessons" label="Số buổi học" placeholder="VD: 10" type="number" min="1" />
            <base-input v-model="newPackage.duration" label="Thời lượng (phút)" placeholder="VD: 90" type="number" min="1" />
            <base-input v-model="newPackage.price" label="Giá (VNĐ)" placeholder="VD: 2.000.000" type="number" min="0" />
        </div>
        <div class="form-group">
            <div class="label-group">
                <label>Tính năng/Đặc điểm của gói</label>
                <button class="btn-no-bg" @click="addFeature">
                    <img class="icon-image-lg" src="/images/plus.svg" />
                    Thêm
                </button>
            </div>
            <div class="features-list">
                <div v-for="(feature, index) in packageFeatures" :key="index" class="feature-input">
                    <base-input v-model="packageFeatures[index]" placeholder="Tính năng 1" />
                    <button class="btn-no-bg" @click="removeFeature(index)" v-if="packageFeatures.length > 1">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

<!-- Modal: Thêm môn học -->
<base-modal :is-open="showAddSubjectModal" title="Thêm môn học" size="small" @close="showAddSubjectModal = false">
    <div class="modal-content">
        <base-select v-model="newSubject.id" :options="listSubjects" label="Chọn môn học" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." />
        <base-input v-model="newSubject.years_of_experience" type="number" label="Số năm kinh nghiệm" placeholder="Nhập số năm kinh nghiệm" unit="năm" :min="0" :step="0.5" required />

        <div class="level-group">
            <label>Cấp độ dạy và học phí</label>
            <div class="level-list">
                <div class="level-item" v-for="level in educationLevels" :key="level.id">
                    <div class="level-left">
                        <div class="level-icon" :class="level.class">
                            <img class="icon-md" :src="level.image" :alt="level.name">
                        </div>
                        <input type="checkbox" :value="level.id" v-model="selectedLevelsOfSubject" @change="toggleLevelSelection(level)">
                        <div class="level-name">{{ level.name }}</div>
                    </div>
                    <base-input v-model="level.price" type="number" placeholder="Nhập học phí" unit="đ/giờ" :min="0" :step="1" :disabled="!selectedLevelsOfSubject.includes(level.id)" />
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddSubjectModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewSubject" :disabled="!isValidAddSubjectInput">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Chỉnh sửa môn học -->
<base-modal :is-open="showEditSubjectModal" title="Chỉnh sửa môn học" size="small" @close="showEditSubjectModal = false">
    <div class="modal-content">
        <base-select v-model="userDataAction.subject.subject_id" :options="listSubjects" label="Chọn môn học" type="search" placeholder="Chọn môn học" search-placeholder="Tìm kiếm môn học..." />
        <base-input v-model="userDataAction.subject.years_of_experience" type="number" label="Số năm kinh nghiệm" placeholder="Nhập số năm kinh nghiệm" unit="năm" :min="0" :step="0.5" required />

        <div class="level-group">
            <label>Cấp độ dạy và học phí</label>
            <div class="level-list">
                <div class="level-item" v-for="level in educationLevels" :key="level.id">
                    <div class="level-left">
                        <div class="level-icon" :class="level.class">
                            <img class="icon-md" :src="level.image" :alt="level.name">
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

<!-- Modal: Thêm ngôn ngữ -->
<base-modal :is-open="showAddLanguageModal" title="Thêm ngôn ngữ" size="small" @close="showAddLanguageModal = false">
    <div class="modal-content">
        <base-select v-model="newLanguage.language_id" :options="languageOptions" label="Ngôn ngữ" placeholder="Chọn ngôn ngữ" />
        <base-select v-model="newLanguage.level_language_id" :options="levelOptions" label="Trình độ" placeholder="Chọn trình độ" />
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" v-model="newLanguage.is_native">
                Đây là ngôn ngữ bản ngữ của tôi
            </label>
        </div>
        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showAddLanguageModal = false">Hủy</button>
            <button class="btn-md btn-primary" @click="addNewLanguage">Thêm</button>
        </div>
    </div>
</base-modal>

<!-- Modal: Sửa ngôn ngữ -->
<base-modal :is-open="showEditLanguageModal" title="Chỉnh sửa ngôn ngữ" size="small" @close="showEditLanguageModal = false">
    <div class="modal-content">
        <base-select v-model="selectedLanguage.language_id" :options="languageOptions" label="Ngôn ngữ" placeholder="Chọn ngôn ngữ" />
        <base-select v-model="selectedLanguage.level_language_id" :options="levelOptions" label="Trình độ" placeholder="Chọn trình độ" />
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

<script setup>
import {
    ref,
    reactive,
    onMounted,
    getCurrentInstance,
    computed,
    watch
} from 'vue';
import { useStore } from 'vuex';

const store = useStore();
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
const showEditSubjectModal = ref(false);
const showAddEducationModal = ref(false);
const showAddExperienceModal = ref(false);
const showAddPackageModal = ref(false);
const showEditPackageModal = ref(false);
const showAddLanguageModal = ref(false);
const showEditLanguageModal = ref(false);
const subjectExists = ref(false);
const availableLevels = ref([]);
const selectedPackage = ref(null);
const currentPackageIndexes = ref({});
const selectedLevelsOfSubject = ref([]);
const selectedLanguageId = ref(null);

// File preview refs
const educationCertificatePreview = ref(null);
const editEducationCertificatePreview = ref(null);

const userDataAction = reactive({
    firstName: '',
    lastName: '',
    address: '',
    phone: '',
    about_you: '',
    education: {
        id: '',
        school_name: '',
        major: '',
        time: '',
        description: '',
        certificate: null
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
    description: '',
    certificate: null
});

const newExperience = reactive({
    name: '',
    position: '',
    time: '',
    description: ''
});

const newPackage = ref({
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

const newSubject = reactive({
    id: '',
    name: '',
    years_of_experience: '',
    levels: []
});

const showAllFeatures = ref({});

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

const educationLevels = computed(() => store.state.configuration.educationLevels);
const listSubjects = computed(() => {
    // Lấy danh sách id các môn đã thêm
    const addedSubjectIds = (props.userDataDetail.user_subjects || []).map(s => s.subject_id);
    // Nếu đang sửa, loại trừ chính môn đang sửa khỏi danh sách disable
    let editingSubjectId = null;
    if (showEditSubjectModal.value && userDataAction.subject && userDataAction.subject.subject_id) {
        editingSubjectId = userDataAction.subject.subject_id;
    }
    return store.state.configuration.subjects.map(subject => ({
        id: subject.id,
        name: subject.name,
        disabled: addedSubjectIds.includes(subject.id) && subject.id !== editingSubjectId
    }));
});

const languageOptions = computed(() => {
    // Lấy danh sách id các ngôn ngữ đã thêm
    const addedLanguageIds = (props.userDataDetail.user_languages || []).map(l => l.language_id);
    // Nếu đang sửa, loại trừ chính ngôn ngữ đang sửa khỏi danh sách disable
    let editingLanguageId = null;
    if (showEditLanguageModal.value && selectedLanguage.value && selectedLanguage.value.language_id) {
        editingLanguageId = selectedLanguage.value.language_id;
    }
    return store.state.configuration.languages.map(lang => ({
        id: lang.id,
        name: lang.name,
        disabled: addedLanguageIds.includes(lang.id) && lang.id !== editingLanguageId
    }));
});

const levelOptions = computed(() => store.state.configuration.levelLanguages);

const updateEducation = async () => {
    try {
        const formData = new FormData();
        formData.append('id', userDataAction.education.id);
        formData.append('school_name', userDataAction.education.school_name);
        formData.append('major', userDataAction.education.major);
        formData.append('time', userDataAction.education.time);
        formData.append('description', userDataAction.education.description);

        if (userDataAction.education.certificate instanceof File) {
            formData.append('certificate', userDataAction.education.certificate);
        }

        const response = await proxy.$api.apiPostFormData('me/update-education', formData);
        // Update the original data with the updated education
        const updatedData = {
            ...props.userDataDetail,
            user_educations: props.userDataDetail.user_educations.map(edu =>
                edu.id === response.data.id ? response.data : edu
            )
        };
        emit('update-data', updatedData);
        showEditEducationModal.value = false;

        // Reset preview
        if (editEducationCertificatePreview.value) {
            URL.revokeObjectURL(editEducationCertificatePreview.value);
            editEducationCertificatePreview.value = null;
        }
        proxy.$notification.success('Cập nhật học vấn thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Cập nhật học vấn thất bại!');
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
        proxy.$notification.success('Cập nhật kinh nghiệm thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Cập nhật kinh nghiệm thất bại!');
        console.error('Failed to update experience:', error);
    }
};

const showEditEducation = (data) => {
    showEditEducationModal.value = true;
    userDataAction.education = {...data};

    // Load existing certificate image if it exists
    if (data.certificate) {
        editEducationCertificatePreview.value = data.certificate;
    } else {
        editEducationCertificatePreview.value = null;
    }
};

const showEditExperience = (data) => {
    showEditExperienceModal.value = true;
    userDataAction.experience = {...data};
};

const addNewEducation = async () => {
    try {
        const formData = new FormData();
        formData.append('school_name', newEducation.school_name);
        formData.append('major', newEducation.major);
        formData.append('time', newEducation.time);
        formData.append('description', newEducation.description);

        if (newEducation.certificate instanceof File) {
            formData.append('certificate', newEducation.certificate);
        }

        const response = await proxy.$api.apiPostFormData('me/education', formData);
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
            description: '',
            certificate: null
        });
        // Reset preview
        if (educationCertificatePreview.value) {
            URL.revokeObjectURL(educationCertificatePreview.value);
            educationCertificatePreview.value = null;
        }
        proxy.$notification.success('Thêm học vấn thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm học vấn thất bại!');
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
        proxy.$notification.success('Thêm kinh nghiệm thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm kinh nghiệm thất bại!');
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
    resetNewPackage();
    showAddPackageModal.value = true;
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
        newPackage.value.subject_id = subject.subject_id;
        availableLevels.value = subject.user_subject_levels.map(level => ({
            id: level.level_id,
            name: level.level_name,
            price: level.price
        }));
    }

    newPackage.value = {
        ...newPackage.value,
        level_id: packageItem.level_id,
        name: packageItem.name,
        description: packageItem.description,
        number_of_lessons: packageItem.number_of_lessons,
        duration: packageItem.duration,
        price: packageItem.price
    };

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
        proxy.$notification.success('Thêm gói dịch vụ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm gói dịch vụ thất bại!');
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
        proxy.$notification.success('Cập nhật gói dịch vụ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Cập nhật gói dịch vụ thất bại!');
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
        proxy.$notification.success('Xóa học vấn thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa học vấn thất bại!');
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
        proxy.$notification.success('Xóa kinh nghiệm thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa kinh nghiệm thất bại!');
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
        proxy.$notification.success('Xóa gói dịch vụ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa gói dịch vụ thất bại!');
        console.error('Failed to delete package:', error);
    }
};

const isValidPackageInput = computed(() => {
    return newPackage.value.subject_id &&
        newPackage.value.level_id &&
        newPackage.value.name &&
        newPackage.value.number_of_lessons &&
        newPackage.value.duration &&
        newPackage.value.price &&
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
        newPackage.value.subject_id = '';
        showAddPackageModal.value = false; // Tắt modal thêm gói dịch vụ
        showAddSubjectModal.value = true;
        return;
    }

    const subject = userSubjects.value.find(s => s.subject_id === value);

    if (subject) {
        newPackage.value.subject_id = subject.subject_id;
        availableLevels.value = subject.user_subject_levels.map(level => ({
            id: level.level_id,
            name: level.level_name,
            price: level.price
        }));

        // Reset level_id khi đổi subject
        newPackage.value.level_id = '';
    } else {
        availableLevels.value = [];
        newPackage.value.level_id = '';
    }
};

const selectedLevelPrice = computed(() => {
    if (!newPackage.value.level_id || !availableLevels.value.length) return 0;
    const level = availableLevels.value.find(l => l.id === newPackage.value.level_id);
    return level?level.price : 0;
});


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
        if (!props.userDataDetail.user_subjects) {
            props.userDataDetail.user_subjects = [];
        }
        const updatedData = {
            ...props.userDataDetail,
            user_subjects: [...props.userDataDetail.user_subjects, response.data]
        };
        emit('update-data', updatedData);

        showAddSubjectModal.value = false;
        // Reset form
        Object.assign(newSubject, {
            id: '',
            name: '',
            years_of_experience: '',
            levels: []
        });
        selectedLevelsOfSubject.value = [];
        proxy.$notification.success('Thêm môn học thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm môn học thất bại!');
        console.error('Failed to add subject:', error);
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
        const updatedData = {
            ...props.userDataDetail,
            user_subjects: props.userDataDetail.user_subjects.map(subject =>
                subject.id === response.data.id ? response.data : subject
            )
        };
        emit('update-data', updatedData);

        showEditSubjectModal.value = false;
        proxy.$notification.success('Cập nhật môn học thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Cập nhật môn học thất bại!');
        console.error('Failed to update subject:', error);
    }
};

const deleteSubject = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/subject/${id}`);
        const updatedData = {
            ...props.userDataDetail,
            user_subjects: props.userDataDetail.user_subjects.filter(item => item.id !== id)
        };
        emit('update-data', updatedData);
        proxy.$notification.success('Xóa môn học thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa môn học thất bại!');
        console.error('Failed to delete subject:', error);
    }
};

const addNewLanguage = async () => {
    try {
        const response = await proxy.$api.apiPost('me/languages', {
            language_id: newLanguage.value.language_id,
            level_language_id: newLanguage.value.level_language_id,
            is_native: newLanguage.value.is_native
        });
        // Update the original data with the new language
        const updatedData = {
            ...props.userDataDetail,
            user_languages: [...props.userDataDetail.user_languages, response.data]
        };
        emit('update-data', updatedData);
        showAddLanguageModal.value = false;
        resetNewLanguage();
        proxy.$notification.success('Thêm ngôn ngữ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm ngôn ngữ thất bại!');
        console.error('Failed to add language:', error);
    }
};

const updateLanguage = async () => {
    try {
        const response = await proxy.$api.apiPut(`me/languages/${selectedLanguageId.value}`, {
            language_id: selectedLanguage.value.language_id,
            level_language_id: selectedLanguage.value.level_language_id,
            is_native: selectedLanguage.value.is_native
        });
        // Update the original data with the updated language
        const updatedData = {
            ...props.userDataDetail,
            user_languages: props.userDataDetail.user_languages.map(lang =>
                lang.id === selectedLanguageId.value ? response.data : lang
            )
        };
        emit('update-data', updatedData);
        showEditLanguageModal.value = false;
        proxy.$notification.success('Cập nhật ngôn ngữ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Cập nhật ngôn ngữ thất bại!');
        console.error('Failed to update language:', error);
    }
};

const handleEditLanguage = (language) => {
    selectedLanguageId.value = language.id;
    selectedLanguage.value = {
        id: language.id,
        language_id: language.language_id,
        level_language_id: language.level_language_id,
        is_native: language.is_native
    };
    showEditLanguageModal.value = true;
};

const handleDeleteLanguage = async (id) => {
    try {
        const response = await proxy.$api.apiDelete(`me/languages/${id}`);
        if (response.success) {
            const updatedData = {
                ...props.userDataDetail,
                user_languages: props.userDataDetail.user_languages.filter(item => item.id !== id)
            };
            emit('update-data', updatedData);
            proxy.$notification.success('Xóa ngôn ngữ thành công!');
        }
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa ngôn ngữ thất bại!');
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

const resetNewPackage = () => {
    newPackage.value = {
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
    };
}

// File handling functions
const onFileChange = (event, key) => {
    const file = event.target.files[0];
    if (file) {
        // Kiểm tra kích thước file (10MB = 10 * 1024 * 1024 bytes)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            proxy.$notification.error('File quá lớn. Vui lòng chọn file nhỏ hơn 10MB.');
            event.target.value = ''; // Reset input
            return;
        }

        // Kiểm tra loại file
        if (!file.type.startsWith('image/')) {
            proxy.$notification.error('Vui lòng chọn file ảnh hợp lệ.');
            event.target.value = ''; // Reset input
            return;
        }

        if (key === 'certificate') {
            newEducation.certificate = file;
            educationCertificatePreview.value && URL.revokeObjectURL(educationCertificatePreview.value);
            educationCertificatePreview.value = URL.createObjectURL(file);
            proxy.$notification.success('Đã chọn ảnh bằng cấp');
        } else if (key === 'edit_certificate') {
            userDataAction.education.certificate = file;
            editEducationCertificatePreview.value && URL.revokeObjectURL(editEducationCertificatePreview.value);
            editEducationCertificatePreview.value = URL.createObjectURL(file);
            proxy.$notification.success('Đã chọn ảnh bằng cấp');
        }
    }
}

const triggerFile = (id) => {
    const input = document.getElementById(id);
    if (input) {
        // Reset input value để có thể chọn lại cùng file
        input.value = '';
        input.click();
    }
}

const removeFile = (key) => {
    if (key === 'certificate') {
        newEducation.certificate = null;
        if (educationCertificatePreview.value) {
            URL.revokeObjectURL(educationCertificatePreview.value);
            educationCertificatePreview.value = null;
        }
    } else if (key === 'edit_certificate') {
        userDataAction.education.certificate = null;
        if (editEducationCertificatePreview.value) {
            URL.revokeObjectURL(editEducationCertificatePreview.value);
            editEducationCertificatePreview.value = null;
        }
    }
}

// Watch for file changes to clean up previews
watch(() => newEducation.certificate, (file) => {
    if (!file && educationCertificatePreview.value) {
        URL.revokeObjectURL(educationCertificatePreview.value);
        educationCertificatePreview.value = null;
    }
});

watch(() => userDataAction.education.certificate, (file) => {
    if (!file && editEducationCertificatePreview.value) {
        URL.revokeObjectURL(editEducationCertificatePreview.value);
        editEducationCertificatePreview.value = null;
    }
});

watch(() => props.userDataDetail?.user_packages, () => {
    initializePackageIndexes();
}, {
    immediate: true
});
</script>

<style scoped>
@import url('@css/profileNew.css');

.education-list {
    display: grid;
    gap: 1rem;
}

.education-card,
.experience-card {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    border-radius: 10px;
    padding: 1rem 1.3rem;
    transition: all 0.3s ease;
}
.experience-card {
    border-left-color: var(--yellow-500);
}

.education-header,
.experience-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.education-card:hover,
.experience-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.education-card .desc,
.experience-card .desc {
    font-size: var(--font-size-heading-6);
    color: var(--gray-500);
}

.education-left,
.experience-left {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}
.education-left .icon-wrapper,
.experience-left .icon-wrapper {
    background: var(--color-primary-transparent);
    border-radius: 2rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
}

.education-left .title-wrapper,
.experience-left .title-wrapper {
    display: grid;
    line-height: 1.4;
}
.education-left .title-wrapper .title-main,
.experience-left .title-wrapper .title-main {
    font-weight: 600;
    font-size: var(--font-size-heading-6);
}
.education-left .title-wrapper .sub-title,
.experience-left .title-wrapper .sub-title {
    font-size: var(--font-size-small);
    font-weight: 500;
    color: var(--color-primary);
}

.education-content,
.experience-content {
    display: flex;
    font-size: var(--font-size-base);
    gap: 1.2rem;
}

.education-content .time,
.experience-content .time {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: var(--gray-500);
}

.education-content .star,
.experience-content .star {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.education-content .star svg,
.experience-content .star svg {
    color: var(--yellow-500);
}

.education-content .status,
.experience-content .status {
    background: var(--color-primary-transparent);
    border: 1px solid var(--color-primary-light);
    padding: 0.1rem 0.7rem;
    border-radius: 2rem;
    color: var(--color-primary);
    font-size: var(--font-size-small) !important;
}


/* package */
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
    padding: 1rem 1.3rem;
    transition: all 0.3s ease;
    border-left-color: var(--color-primary);
    border-left-width: 4px;
}

.package-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transform: translateY(-2px);
}

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

.package-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.8rem;
}

.package-title {
    display: flex;
    align-items: center;
    gap: 0.7rem;
}

.package-title .icon-wrapper {
    background: var(--color-primary-transparent);
    border-radius: 2rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
}

.package-title .title-wrapper {
    line-height: 1.6;
}

.package-title h5 {
    margin: 0;
    font-weight: 600;
    font-size: var(--font-size-heading-6);
}

.package-title .subject-name {
    font-size: var(--font-size-small);
    font-weight: 500;
    color: var(--color-primary);
}

.package-content {
    display: flex;
    flex-direction: column;
    gap: 0.9rem;
}

.package-description {
    margin: 0;
    color: var(--gray-500);
    font-size: var(--font-size-base);
    line-height: 1.5;
}

.package-info {
    display: flex;
    gap: 1rem;
}

.package-info .info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #374151;
    font-weight: 500;
    font-size: var(--font-size-small);
}

.package-info .info-icon {
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

.features-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin: 0.5rem 0;
}

.feature-input {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.feature-input input {
    flex: 1;
}

.feature-toggle {
    color: var(--color-primary);
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

.package-navigation .navigation-buttons {
    display: flex;
    gap: 0.5rem;
}

.package-navigation .nav-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 1.7rem;
    height: 1.7rem;
    border: 1px solid #E5E7EB;
    border-radius: 2rem;
    background: white;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
}

.package-navigation .nav-button:hover:not(:disabled) {
    border-color: #D1D5DB;
    background: #F9FAFB;
}

.package-navigation .nav-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.actions-card {
    /* opacity: 0; */
    display: flex;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

.price-hint {
    font-size: var(--font-size-mini);
    color: var(--gray-500);
    margin-top: 4px;
}

/* Level Group Styles */
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

.level-name {
    font-size: var(--font-size-small);
    font-weight: 500;
    width: 80px;
}

.level-check {
    width: 1.5rem;
    height: 1.5rem;
    color: #111827;
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

/* File upload styles */
.file-group {
    margin-bottom: 0;
}
.file-label-top {
    font-weight: 500;
    font-size: var(--font-size-small);
    margin-bottom: 0.5rem;
    color: #222;
}
.up-file-wrapper {
    width: 100%;
    position: relative;
}
.up-file {
    width: 100%;
    min-height: 160px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
    cursor: pointer;
    padding: 1.2rem 0.5rem;
    transition: all 0.3s ease;
}
.up-file:hover {
    border-color: #9ca3af;
    background: #f9fafb;
}
.up-file .icon-wrapper {
    width: 3rem;
    height: 3rem;
    background: var(--color-primary-transparent);
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
    cursor: pointer;
}
.up-file .content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
    text-align: center;
}
.up-file .content .title {
    font-weight: 600;
    font-size: var(--font-size-base);
    color: #374151;
}
.up-file .content .sub-title {
    font-size: var(--font-size-small);
    color: #6b7280;
}
.up-file .content .sub-title a {
    color: var(--color-primary);
    text-decoration: underline;
}
.up-file .content .desc {
    font-size: var(--font-size-mini);
    color: #9ca3af;
}
.preview-image {
    position: relative;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
}
.preview-image img {
    width: 100%;
    max-height: 160px;
    object-fit: contain;
    display: block;
    background: #f9fafb;
}
.actions-wrapper {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 2;
}
.actions {
    display: flex;
    gap: 0.5rem;
}
.actions button {
    width: 2rem;
    height: 2rem;
    background: #ffffffe6;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #374151;
}
.actions button:hover {
    background: #ffffff;
    transform: scale(1.1);
}
input[type="file"] {
    display: none;
}
</style>

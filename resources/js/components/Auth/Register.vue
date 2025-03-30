<script setup>
    import {
        ref,
        computed,
        onMounted,
        reactive
    } from 'vue';
    import {
        useRouter
    } from 'vue-router';
    import {
        useStore
    } from 'vuex';
    import {
        register
    } from '@/api/auth.js';

    const store = useStore();
    const router = useRouter();

    const educationLevels = computed(() => store.state.configuration.educationLevels);
    const listSubjects = computed(() => store.state.configuration.subjects);

    const ROLE_STUDENT = 0;
    const ROLE_TUTOR = 1;

    const showPassword = ref(false);
    const showPasswordConfirmation = ref(false);
    const isLoading = ref(false);

    const formDataErrors = reactive({});
    const formData = reactive({
        role: 0,
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        password: '',
        passwordConfirmation: '',
        educations: [{
            school_name: '',
            major: '',
            time: '',
            description: ''
        }],
        subjects: [],
        experiences: [{
            name: '',
            position: '',
            time: '',
            description: ''
        }],
        educationLevels: null,
        terms: false
    });

    const subjectSearch = ref('');
    const showSubjectDropdown = ref(false);
    const showExperienceModal = ref(false);
    const selectedSubject = ref(null);
    const yearsOfExperience = ref('');
    const selectedLevelsOfSubject = ref([]);

    const validate = () => {
        Object.keys(formDataErrors).forEach(key => delete formDataErrors[key]);

        if (formData.role != ROLE_STUDENT && formData.role != ROLE_TUTOR) {
            formDataErrors.role = 'Vai trò không hợp lệ';
        }

        if (!formData.firstName) {
            formDataErrors.firstName = 'Họ không được để trống';
        }

        if (!formData.lastName) {
            formDataErrors.lastName = 'Tên không được để trống';
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!formData.email) {
            formDataErrors.email = 'Email không được để trống';
        } else if (!emailRegex.test(formData.email)) {
            formDataErrors.email = 'Email không hợp lệ';
        }

        const phoneRegex = /^[0-9]{10}$/;
        if (!formData.phone) {
            formDataErrors.phone = 'Số điện thoại không được để trống';
        } else if (!phoneRegex.test(formData.phone)) {
            formDataErrors.phone = 'Số điện thoại phải là 10 chữ số';
        }

        if (!formData.password) {
            formDataErrors.password = 'Mật khẩu không được để trống';
        } else if (formData.password.length < 8) {
            formDataErrors.password = 'Mật khẩu phải có ít nhất 8 ký tự';
        }

        if (!formData.passwordConfirmation) {
            formDataErrors.passwordConfirmation = 'Xác nhận mật khẩu không được để trống';
        } else if (formData.password !== formData.passwordConfirmation) {
            formDataErrors.passwordConfirmation = 'Mật khẩu không khớp';
        }

        if (formData.role === ROLE_TUTOR) {
            if (!formData.educations[0].school_name) {
                formDataErrors.educations = 'Trường học không được để trống';
            }

            if (formData.subjects.length === 0) {
                formDataErrors.subjects = 'Phải chọn ít nhất một môn học';
            }

            if (!formData.experiences[0].name) {
                formDataErrors.experiences = 'Kinh nghiệm không được để trống';
            }
        } else if (formData.role === ROLE_STUDENT) {
            if (!formData.educationLevels) {
                formDataErrors.educationLevels = 'Vui lòng chọn cấp học hiện tại';
            }
        }

        if (!formData.terms) {
            formDataErrors.terms = 'Bạn phải đồng ý với điều khoản';
        }

        return Object.keys(formDataErrors).length === 0;
    };

    // Filter subjects based on search input
    const filteredSubjects = computed(() => {
        if (!subjectSearch.value) return listSubjects.value;
        return listSubjects.value.filter(subject =>
            subject.name.toLowerCase().includes(subjectSearch.value.toLowerCase())
        );
    });

    // Toggle subject selection and show modal
    const toggleSubject = (subject) => {
        const index = formData.subjects.findIndex(s => s.id === subject.id);
        if (index === -1) {
            selectedSubject.value = subject;
            yearsOfExperience.value = '';
            showExperienceModal.value = true;
        } else {
            formData.subjects.splice(index, 1);
        }
    };

    const checkSubjectSelected = (subject) => {
        return formData.subjects.some(s => s.id === subject.id);
    };

    const addSubjectWithExperience = () => {
        if (selectedSubject.value && yearsOfExperience.value) {
            formData.subjects.push({
                id: selectedSubject.value.id,
                name: selectedSubject.value.name,
                years_of_experience: yearsOfExperience.value
            });
            showExperienceModal.value = false;
            selectedSubject.value = null;
            yearsOfExperience.value = '';
        }
    };

    const cancelAddSubject = () => {
        showExperienceModal.value = false;
        selectedSubject.value = null;
        yearsOfExperience.value = '';
    };

    const removeSubject = (subject) => {
        const index = formData.subjects.findIndex(s => s.id === subject.id);
        if (index !== -1) {
            formData.subjects.splice(index, 1);
        }
    };

    const addEducationLevel = (item) => {
        formData.educationLevels = item.id;
    };

    const addEducation = () => {
        formData.educations.push({
            school_name: '',
            major: '',
            start_date: '',
            end_date: ''
        });
    };

    const removeEducation = (index) => {
        formData.educations.splice(index, 1);
    };

    const addExperience = () => {
        formData.experiences.push({
            name: '',
            position: '',
            start_date: '',
            end_date: ''
        });
    };

    const removeExperience = (index) => {
        formData.experiences.splice(index, 1);
    };

    const resetFormData = () => {
        Object.keys(formDataErrors).forEach(key => delete formDataErrors[key]);
    };

    const handleRegister = async () => {
        if (!validate()) {
            return;
        }

        try {
            isLoading.value = true;
            const response = await register(formData);

            store.dispatch('updateAuth', { token: response.token, user: response.user });
            router.push('/');

            // Redirect based on role
            /*
            if (formData.role === ROLE_STUDENT) {
                router.push('/student/dashboard');
            } else {
                router.push('/tutor/dashboard');
            }
            */
        } catch (error) {
            if (error.response?.errors) {
                Object.assign(formDataErrors, error.response.data.errors);
            } else {
                formDataErrors.general = 'Có lỗi xảy ra, vui lòng thử lại sau';
            }
        } finally {
            isLoading.value = false;
        }
    };

    const openLoginModal = () => {
        router.push('/login');
    };

    onMounted(() => {
        document.addEventListener('click', (e) => {
            const subjectSearch = document.querySelector('.subject-search');
            if (subjectSearch && !subjectSearch.contains(e.target)) {
                showSubjectDropdown.value = false;
            }
        });
    });
</script>

<template>
<div class="main-register">
    <div class="container-register">
        <div class="header">
            <h2>Đăng ký tài khoản</h2>
            <p>Tạo tài khoản để bắt đầu hành trình học tập cùng EduTutor</p>
        </div>

        <div class="role-selector">
            <button :class="['role-button', { active: formData.role === ROLE_STUDENT }]" @click="formData.role = ROLE_STUDENT; resetFormData()">
                Học sinh
            </button>
            <button :class="['role-button', { active: formData.role === ROLE_TUTOR }]" @click="formData.role = ROLE_TUTOR; resetFormData()">
                Gia sư
            </button>
        </div>

        <form @submit.prevent="handleSubmit" class="register-form">
            <div class="form-group-containner">
                <div class="form-group">
                    <div class="label-group">
                    <label for="firstName">Họ</label>
                        <span>*</span>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <input type="text" id="firstName" v-model="formData.firstName" placeholder="Nguyễn" required>
                    </div>
                    <span v-if="formDataErrors.firstName" class="error-message">{{ formDataErrors.firstName }}</span>
                </div>
                <div class="form-group">
                    <div class="label-group">
                    <label for="lastName">Tên</label>
                        <span>*</span>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <input type="text" id="lastName" v-model="formData.lastName" placeholder="Văn A" required>
                    </div>
                    <span v-if="formDataErrors.lastName" class="error-message">{{ formDataErrors.lastName }}</span>
                </div>
            </div>

            <div class="form-group">
                <div class="label-group">
                <label for="email">Email</label>
                    <span>*</span>
                </div>
                <div class="input-with-icon">
                    <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <input type="email" id="email" v-model="formData.email" placeholder="name@example.com" required>
                </div>
                <span v-if="formDataErrors.email" class="error-message">{{ formDataErrors.email }}</span>
            </div>

            <div class="form-group">
                <div class="label-group">
                <label for="phone">Số điện thoại</label>
                    <span>*</span>
                </div>
                <div class="input-with-icon">
                    <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <input type="tel" id="phone" v-model="formData.phone" placeholder="0912345678" required>
                </div>
                <span v-if="formDataErrors.phone" class="error-message">{{ formDataErrors.phone }}</span>
            </div>

            <div class="form-group-containner">
                <div class="form-group">
                    <div class="label-group">
                    <label for="password">Mật khẩu</label>
                        <span>*</span>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input :type="showPassword ? 'text' : 'password'" id="password" v-model="formData.password" placeholder="••••••" required>
                        <button type="button" class="toggle-password" @click="showPassword = !showPassword">
                            <svg v-if="!showPassword" class="icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <span v-if="formDataErrors.password" class="error-message">{{ formDataErrors.password }}</span>
                </div>

                <div class="form-group">
                    <div class="label-group">
                    <label for="passwordConfirmation">Xác nhận mật khẩu</label>
                        <span>*</span>
                    </div>
                    <div class="input-with-icon">
                        <svg class="input-icon icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <input :type="showPasswordConfirmation ? 'text' : 'password'" id="passwordConfirmation" v-model="formData.passwordConfirmation" placeholder="••••••" required>
                        <button type="button" class="toggle-password" @click="showPasswordConfirmation = !showPasswordConfirmation">
                            <svg v-if="!showPasswordConfirmation" class="icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg v-else class="icon-base" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <span v-if="formDataErrors.passwordConfirmation" class="error-message">{{ formDataErrors.passwordConfirmation }}</span>
                </div>
            </div>

            <div v-if="formData.role === 1" class="tutor-fields">
            <div class="form-group">
                    <label>Môn học giảng dạy</label>
                    <div class="subjects-input">
                        <div style="position: relative;">
                            <div class="button-with-icon">
                                <svg class="icon-base button-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                                </svg>
                                <button type="button" class="subject-select-button" @click="showSubjectDropdown = !showSubjectDropdown">
                                    <span class="placeholder-button">Tìm và chọn môn học</span>
                                    <svg class="select-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                            </div>
                            <div v-if="showSubjectDropdown" class="subject-dropdown">
                                <div class="search-input">
                                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input type="text" v-model="subjectSearch" placeholder="Tìm kiếm môn học..." @click.stop>
                                </div>
                                <div class="subject-list">
                                    <div v-for="subject in filteredSubjects" :key="subject.id" class="subject-option" :class="{ 'selected': checkSubjectSelected(subject) }" @click.stop="toggleSubject(subject)">
                                        <span class="checkbox">
                                            <svg v-if="checkSubjectSelected(subject)" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </span>
                                        {{ subject.name }}
                                    </div>
                                </div>
                            </div>

                            <span v-if="formDataErrors.subjects" class="error-message">{{ formDataErrors.subjects }}</span>
                        </div>

                        <div class="selected-subjects" v-if="formData.subjects.length">
                            <span v-for="subject in formData.subjects" :key="subject.id" class="subject-tag">
                                {{ subject.name }} ({{ subject.years_of_experience }} năm)
                                <button type="button" @click="removeSubject(subject)">
                                    <img src="/images/cancel.svg" class="icon-base" />
                                </button>
                            </span>
                        </div>
                    </div>
            </div>

            <div class="form-group">
                    <div class="label-group">
                        <label>Trường học</label>
                        <button type="button" class="add-button" @click="addEducation">
                            <svg class="icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Thêm trường học
                        </button>
                    </div>
                    <div class="education-input">
                        <div v-for="(edu, index) in formData.educations" :key="index" class="education-item">
                            <div class="form-row">
                                <div class="form-col">
                                    <label>Tên trường học</label>
                                    <div class="input-with-icon">
                                        <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                            <path d="M22 10v6"></path>
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                        </svg>
                                        <input type="text" v-model="edu.school_name" placeholder="Ví dụ: Đại học Quốc gia Hà Nội" required>
                                    </div>
                                </div>
                                <div class="form-col">
                                    <label>Chuyên ngành</label>
                                    <div class="input-with-icon">
                                        <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                            <path d="M22 10v6"></path>
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                        </svg>
                                        <input type="text" v-model="edu.major" placeholder="Ví dụ: Toán học, Vật lý" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-col">
                                <label>Thời gian</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input type="text" v-model="edu.time" placeholder="VD: 2020-2024" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <label>Mô tả chi tiết</label>
                                <div class="input">
                                    <textarea v-model="edu.description" placeholder="Nhập mô tả" rows="4"></textarea>
                                </div>
                            </div>

                            <button v-if="formData.educations.length > 1" type="button" class="remove-button" @click="removeEducation(index)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span v-if="formDataErrors.educations" class="error-message">{{ formDataErrors.educations }}</span>
            </div>

            <div class="form-group">
                    <div class="label-group">
                        <label>Kinh nghiệm giảng dạy</label>
                        <button type="button" class="add-button" @click="addExperience">
                            <svg class="icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            Thêm kinh nghiệm
                        </button>
                    </div>
                    <div class="experience-input">
                        <div v-for="(exp, index) in formData.experiences" :key="index" class="experience-item">
                            <div class="form-row">
                                <div class="form-col">
                                    <label>Tên tổ chức/trường học</label>
                                    <div class="input-with-icon">
                                        <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                            <path d="M22 10v6"></path>
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                        </svg>
                                        <input type="text" v-model="exp.name" placeholder="Ví dụ: Trường THPT Chu Văn An" required>
                                    </div>
                                </div>
                                <div class="form-col">
                                    <label>Vị trí công việc</label>
                                    <div class="input-with-icon">
                                        <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"></path>
                                            <path d="M22 10v6"></path>
                                            <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5"></path>
                                        </svg>
                                        <input type="text" v-model="exp.position" placeholder="Ví dụ: Giáo viên Toán, Gia sư Tiếng Anh" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-col">
                                <label>Thời gian</label>
                                <div class="input-with-icon">
                                    <svg class="input-icon icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                        <line x1="16" y1="2" x2="16" y2="6"></line>
                                        <line x1="8" y1="2" x2="8" y2="6"></line>
                                        <line x1="3" y1="10" x2="21" y2="10"></line>
                                    </svg>
                                    <input type="text" v-model="exp.time" placeholder="VD: 2020-2024" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <label>Mô tả chi tiết</label>
                                <div class="input">
                                    <textarea v-model="exp.description" placeholder="Nhập mô tả" rows="4"></textarea>
                                </div>
                            </div>
                            <button v-if="formData.experiences.length > 1" type="button" class="remove-button" @click="removeExperience(index)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span v-if="formDataErrors.experiences" class="error-message">{{ formDataErrors.experiences }}</span>
                </div>
            </div>

            <div class="form-group" v-if="formData.role === 0">
                <label>Cấp học hiện tại</label>
                <div class="education-level">
                    <div class="level-item" v-for="level in educationLevels" :key="level.id" :class="{ 'active': formData.educationLevels === level.id }" @click="addEducationLevel(level)">
                        <div class="icon">
                            <img :src="level.image" class="icon-base" alt="icon">
                        </div>
                        <span>{{ level.name }}</span>
                    </div>
                </div>
            </div>

            <div class="form-group terms">
                <p class="checkbox-container">
                    <input type="checkbox" v-model="formData.terms" required>
                    <span class="checkmark"></span>
                    Tôi đồng ý với <a href="#" class="link">Điều khoản dịch vụ</a> và <a href="#" class="link">Chính sách bảo mật</a>
                </p>
            </div>

            <button type="submit" class="submit-button" @click="handleRegister" :disabled="isLoading">
                <span v-if="!isLoading">Đăng ký</span>
                <span v-else class="loading-spinner"></span>
            </button>
        </form>

        <div class="divider">
            <span>HOẶC ĐĂNG KÝ VỚI</span>
        </div>

        <div class="social-login">
            <button class="social-button facebook">
                <img src="/images/facebook.svg" alt="Facebook" class="icon-base">
                Facebook
            </button>
            <button class="social-button google">
                <img src="/images/google.svg" alt="Google" class="icon-base">
                Google
            </button>
        </div>

        <p class="login-prompt">
            Đã có tài khoản? <a href="#" @click.prevent="openLoginModal">Đăng nhập</a>
        </p>
    </div>
</div>

<!-- Use BaseModal instead of custom modal -->
<base-modal :is-open="showExperienceModal" title="Thêm số năm kinh nghiệm" size="small" @close="cancelAddSubject">
    <div class="modal-content">
        <div class="form-group">
            <label>Số năm kinh nghiệm dạy môn {{ selectedSubject?.name }}</label>
            <div>
                <input type="number" v-model="yearsOfExperience" placeholder="Ví dụ: 3" min="0" step="0.5">
            </div>
            <span class="hint">Nhập số năm kinh nghiệm giảng dạy môn học này</span>
        </div>
        <div class="modal-footer">
            <button class="cancel-button" @click="cancelAddSubject">Hủy</button>
            <button class="btn-base" @click="addSubjectWithExperience" :disabled="!yearsOfExperience">Thêm</button>
        </div>
    </div>
</base-modal>
</template>


<style scoped>
.main-register {
    background: #fcfcfc;
    padding: 1rem;
    --icon-base: 18px;
}

.container-register {
    max-width: 45rem;
    padding: 2rem;
    margin: 2rem auto;
    background-color: white;
    border-radius: 8px;
}

.header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.header h2 {
    font-size: 24px;
    font-weight: 800;
    color: #111827;
    margin-bottom: 0.7rem;
}

.header p {
    font-size: 16px;
    color: #6b7280;
    margin-top: 0.5rem;
}

.role-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
    background: #f3f4f6;
    padding: 4px;
    border-radius: 8px;
}

.role-button {
    padding: 10px;
    border: none;
    border-radius: 6px;
    background: transparent;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.role-button.active {
    background: white;
    color: black;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.register-form {
    display: grid;
    gap: 1rem;
}

.form-group-containner {
    display: flex;
    gap: 1rem;
    width: 100%;
}

.form-group {
    width: 100%;
    position: relative;
}

.form-group>label {
    margin-bottom: 0.5rem;
}

.form-group>label,
.label-group>label {
    display: block;
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
}

.form-group .label-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.7rem;
}

.input-with-icon,
.button-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.button-with-icon:hover .placeholder-button,
.button-with-icon:hover .button-icon {
    color: black;
}

.input-icon,
.button-icon {
    position: absolute;
    left: 12px;
    color: #9ca3af;
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-group .input-with-icon input,
.form-group .button-with-icon button {
    padding: 0.8rem 1rem 0.8rem 2.3rem;
}

.form-group input:focus {
    outline: none;
    border-color: black;
}

.toggle-password {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
}

.education-level {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    margin-top: 1rem;
}

.level-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    gap: 0.3rem;
    border: 1px solid #f3f4f6;
    border-radius: 8px;
    padding: 0.7rem 2rem;
    font-size: var(--font-size-small);
    font-weight: 500;
}

.level-item.active {
    background-color: #f3f4f6;
    border: 1px solid #8a8c91;
    border-radius: 8px;
}

.level-item:hover {
    background-color: #f3f4f6;
    border-radius: 8px;
}

.level-item .icon {
    background: #e7e7e8;
    border-radius: 2rem;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.radio-container {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.radio-container input[type="radio"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.radio-container input[type="radio"]:checked {
    background-color: black;
    border-color: black;
}

.radio-container input[type="radio"]:focus {
    background: none;
    border: none;
    outline: none;
}

.radio-label {
    color: #374151;
    font-size: var(--font-size-small);
}

.terms {
    margin-top: 1rem;
}

.checkbox-container {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    cursor: pointer;
    font-size: var(--font-size-small);
    color: #4b5563;
    margin: 0;
}

.checkbox-container input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

.checkbox-container .link {
    color: black;
    text-decoration: none;
}

.checkbox-container .link:hover {
    text-decoration: underline;
}

.submit-button {
    width: 100%;
    padding: 12px;
    background: black;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.submit-button:hover {
    background: #1a1a1a;
}

.submit-button:disabled {
    background: #d1d5db;
    cursor: not-allowed;
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.divider {
    text-align: center;
    margin: 24px 0;
    position: relative;
}

.divider::before,
.divider::after {
    content: "";
    position: absolute;
    top: 50%;
    width: calc(50% - 100px);
    height: 1px;
    background: #e5e7eb;
}

.divider::before {
    left: 0;
}

.divider::after {
    right: 0;
}

.divider span {
    background: white;
    padding: 0 16px;
    color: #6b7280;
    font-size: var(--font-size-small);
}

.social-login {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
}

.social-button {
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.social-button:hover {
    background: #f3f4f6;
}

.login-prompt {
    text-align: center;
    color: #6b7280;
    margin: 0;
}

.login-prompt a {
    color: black;
    text-decoration: none;
    font-weight: 500;
}

.login-prompt a:hover {
    text-decoration: underline;
}

.error-message {
    color: #dc2626;
    font-size: var(--font-size-small);
    margin-top: 0.3rem;
    display: block;
}

input[type="radio"],
input[type="checkbox"] {
    accent-color: black;
}

.tutor-fields {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.education-input,
.subjects-input,
.experience-input {
    display: flex;
    flex-direction: column;
    gap: 1.3rem;
}

.education-item,
.experience-item {
    position: relative;
    padding: 1.2rem;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    display: grid;
    grid-template-columns: 2fr;
    gap: 1rem;
}

.education-input input,
.experience-input input {
    padding: 0.6rem 1rem 0.6rem 2.5rem !important;
}

.remove-button {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.25rem;
    transition: all 0.2s;
}

.remove-button:hover {
    background: #f3f4f6;
    color: #dc2626;
}

.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.input-with-icon input:focus {
    outline: none;
    border-color: #111827;
}

.input-icon {
    position: absolute;
    left: 0.75rem;
    color: #6b7280;
    pointer-events: none;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-col {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-col label {
    font-size: 0.875rem;
    color: #374151;
}

.selected-subjects {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.subject-tag {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 4px 8px;
    background: #f3f4f6;
    border-radius: 2rem;
    font-size: var(--font-size-small);
    font-weight: 500;
    color: #374151;
}

.subject-tag button {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0;
    font-size: 16px;
    line-height: 1;
}

.subject-tag button:hover {
    color: #dc2626;
}

input.has-subjects {
    padding-top: 8px;
}

.subject-search {
    position: relative;
    width: 100%;
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
    width: 16px;
    height: 16px;
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

.subject-select-button {
    width: 100%;
    padding: 12px 16px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    color: #6b7280;
    font-size: 0.875rem;
}

.subject-select-button:hover {
    background-color: rgb(244 244 245);
    color: black;
}

.select-icon {
    color: #6b7280;
}

.search-input {
    position: relative;
    display: flex;
    align-items: center;
    padding: 0.8rem 0.8rem 0.8rem 0;
    border-bottom: 1px solid #e5e7eb;
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

.selected-subjects {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 8px;
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

.subject-list {
    padding: 4px 0;
    max-height: 300px;
    overflow-y: auto;
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

.icon-base {
    width: var(--icon-base);
    height: var(--icon-base);
}

/* Keep only the button styles */
.modal-content {
    display: grid;
    gap: 1rem;
}

.hint {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

@media (max-width: 640px) {

    .form-group-containner {
        grid-template-columns: 1fr;
    }

    .social-login {
        grid-template-columns: 1fr;
    }

    .education-level {
        grid-template-columns: 1fr 1fr;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

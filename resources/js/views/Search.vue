<template>
<div class="search-page">
    <div class="container">
        <div class="header-wrapper">
            <h1 class="title">
                Tìm gia sư phù hợp với bạn
            </h1>
            <span class="sub-title">Khám phá đội ngũ gia sư chất lượng cao của chúng tôi và tìm người phù hợp nhất với nhu cầu học tập của bạn</span>
        </div>

        <div class="all-filter filter-desktop">
            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>Vị trí & Thông tin cá nhân</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="filter-group">
                            <base-select v-model="filters.provinces_id" :options="cityOptions" label="Thành phố" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.sex" :options="genderOptions" label="Giới tính" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.timeSlotStart" :options="timeSlotOptions" label="Thời gian rảnh bắt đầu" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.timeSlotEnd" :options="timeSlotOptions" label="Thời gian rảnh kết thúc" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="separation"></div>

            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group filter-group-flex2">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Học vấn & Môn học</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="filter-group">
                            <base-select v-model="filters.subject" :options="subjectOptions" label="Môn học" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.educationLevel" :options="educationLevelOptions" label="Cấp độ" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.experience" :options="experienceOptions" label="Kinh nghiệm" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="separation"></div>

            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group filter-group-flex2">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Đánh giá & Mức giá</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="slider-block">
                            <div class="slider-info">Đánh giá tối thiểu: {{ filters.rating }} sao</div>
                            <div class="custom-slider-wrapper">
                                <input type="range" min="0" max="5" step="0.5" v-model.number="filters.rating" class="custom-slider" />
                                <div class="slider-labels">
                                    <span>0 sao</span>
                                    <span>2.5 sao</span>
                                    <span>5 sao</span>
                                </div>
                            </div>
                        </div>
                        <div class="slider-block">
                            <div class="slider-info">Mức giá tối đa: {{ $helper.formatCurrency(filters.price) }} / giờ</div>
                            <div class="custom-slider-wrapper">
                                <input type="range" min="100000" max="500000" step="10000" v-model.number="filters.price" class="custom-slider" />
                                <div class="slider-labels">
                                    <span>100k</span>
                                    <span>300k</span>
                                    <span>500k</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="all-filter filter-mobile">
            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group filter-group-flex2">
                    <!-- <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Học vấn & Môn học</span>
                    </label> -->
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="filter-group d-flex align-end gap-g-1">
                            <base-select v-model="filters.subject" :options="subjectOptions" placeholder="Tất cả môn học" size="medium" widthFull="true" />
                            <button class="btn-lg btn-secondary" @click="showFilter = true">
                                <svg class="icon-lg" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 7H20" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M7 12L17 12" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M11 17H13" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="search-results" v-if="tutors.length > 0">
            <div class="results-header">
                <h2 class="results-title">Kết quả tìm kiếm ({{ tutors.length }} gia sư)</h2>
            </div>

            <div class="tutors-grid">
                <div v-for="tutor in tutors" :key="tutor.uid" class="tutor-card" @click="navigateToTutor(tutor.uid)">
                    <div class="tutor-card-wrapper">
                        <div class="tutor-header-wrapper">
                            <img :src="tutor.avatar" alt="" class="tutor-image">

                            <div class="tutor-header_detail">
                                <div class="tutor-name">
                                    <span>Nguyễn tiến thành</span>
                                    <svg aria-label="Đã xác minh" class="icon-md" fill="rgb(0, 149, 246)" height="12" role="img" viewBox="0 0 40 40" width="12"><title>Đã xác minh</title><path d="M19.998 3.094 14.638 0l-2.972 5.15H5.432v6.354L0 14.64 3.094 20 0 25.359l5.432 3.137v5.905h5.975L14.638 40l5.36-3.094L25.358 40l3.232-5.6h6.162v-6.01L40 25.359 36.905 20 40 14.641l-5.248-3.03v-6.46h-6.419L25.358 0l-5.36 3.094Zm7.415 11.225 2.254 2.287-11.43 11.5-6.835-6.93 2.244-2.258 4.587 4.581 9.18-9.18Z" fill-rule="evenodd"></path></svg>
                                </div>
                                <div class="location-name">
                                    <span>{{ tutor.provinces_name }}, {{ tutor.districts_name }}</span>
                                </div>
                            </div>

                            <button class="btn-like">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>

                        <div class="tutor-info">
                            <div class="subject-list">
                                <div class="subject-row">
                                    <span v-for="subject in tutor.user_subjects"
                                        :key="subject.id"
                                        class="subject-tag">
                                        {{ subject.subject_name }}
                                    </span>
                                </div>

                                <div class="subject-levels">
                                    <!-- <span v-for="level in getSubjectLevels(tutor.user_subjects)"
                                        :key="level.id"
                                        class="level-tag">
                                        {{ level.name }}
                                    </span> -->
                                    <span v-for="(item, idx) in tutor.user_subject_levels" :key="idx" class="level-tag">
                                        {{ item.subject_name }} - {{ item.level_name }}
                                    </span>
                                </div>
                            </div>

                            <div class="info-list">
                                <div class="info-item" v-if="tutor.user_educations && tutor.user_educations.length > 0">
                                    <img src="/images/education.svg" class="icon-md">
                                    <div class="info-wrapper">
                                        <div class="info-title">
                                            {{ tutor.user_educations[0].school_name }} - {{ tutor.user_educations[0].major }}
                                        </div>
                                        <div class="info-subtitle">
                                            {{ tutor.user_educations[0].description }}
                                        </div>
                                    </div>
                                </div>

                                <div class="info-item" v-if="tutor.user_experiences && tutor.user_experiences.length > 0">
                                    <img src="/images/briefcase.svg" class="icon-md" />
                                    <div class="info-wrapper">
                                        <div class="info-title">{{ tutor.user_experiences[0].name }}</div>
                                        <div class="info-subtitle">Kinh nghiệm làm việc</div>
                                    </div>
                                </div>

                                <div class="info-item">
                                    <img src="/images/achievement.svg" class="icon-md" />
                                    <div class="info-wrapper">
                                        <div class="info-title">Thành tích nổi bật</div>
                                        <div class="info-subtitle">Giáo viên xuất sắc năm 2019</div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-base text-gray-700">
                                {{ tutor.about_you }}
                            </div>

                            <div class="price-section" @mouseenter="showPriceDetail = tutor.id" @mouseleave="showPriceDetail = null">
                                <div class="price-range" style="position: relative;">
                                    <span>{{ $helper.getPriceRange(tutor.user_subjects) }}</span>
                                    <div v-if="showPriceDetail === tutor.id" class="price-detail-popup">
                                        <div class="popup-title">Học phí theo môn học và cấp độ:</div>
                                        <div v-for="(subject, sidx) in tutor.user_subjects" :key="subject.id" class="price-detail-subject">
                                            <div class="subject-title">{{ subject.subject_name }}</div>
                                            <table class="price-table">
                                                <tbody>
                                                    <tr v-for="level in subject.user_subject_levels" :key="level.id">
                                                        <td class="level-name">{{ level.education_level }}</td>
                                                        <td class="level-price">{{ formatPrice(level.price) }}/buổi</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.9</span>
                                    <span class="student-count">(120 học sinh)</span>
                                </div>
                            </div>

                            <div class="tutor-actions">
                                <button class="btn-xl btn-black w-100 border-r-2" @click.stop="redirectToBooking(tutor.uid)">
                                    Đặt lịch học
                                </button>
                                <button class="btn-xl btn-secondary border-r-2" @click.stop="redirectToBooking(tutor.uid)">
                                    <svg class="icon-md" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.85665 2.30447C8.2922 2.16896 10.3981 2 12 2C13.6019 2 15.7078 2.16896 17.1433 2.30447C18.4976 2.4323 19.549 3.51015 19.6498 4.85178C19.7924 6.74918 20 9.90785 20 12.2367C20 14.022 19.8781 16.2915 19.7575 18.1035C19.697 19.0119 19.6365 19.8097 19.5911 20.3806C19.5685 20.6661 19.5496 20.8949 19.5363 21.0526L19.5209 21.234L19.5154 21.2966L19.5153 21.2976C19.5153 21.2977 19.5153 21.2977 18.7441 21.2308L19.5153 21.2976C19.4927 21.5553 19.3412 21.7845 19.1122 21.9075C18.8831 22.0305 18.6072 22.0309 18.3779 21.9085L12.1221 18.5713C12.0458 18.5307 11.9542 18.5307 11.8779 18.5713L5.62213 21.9085C5.39277 22.0309 5.11687 22.0305 4.88784 21.9075C4.65881 21.7845 4.50732 21.5554 4.48466 21.2977L5.25591 21.2308C4.48466 21.2977 4.48467 21.2978 4.48466 21.2977L4.47913 21.234L4.46371 21.0526C4.45045 20.8949 4.43154 20.6661 4.40885 20.3806C4.3635 19.8097 4.30303 19.0119 4.24255 18.1035C4.12191 16.2915 4 14.022 4 12.2367C4 9.90785 4.20763 6.74918 4.3502 4.85178C4.45102 3.51015 5.50236 2.4323 6.85665 2.30447ZM5.93179 19.9971L11.1455 17.2159C11.6791 16.9312 12.3209 16.9312 12.8545 17.2159L18.0682 19.9971C18.1101 19.4598 18.1613 18.7707 18.2124 18.0019C18.3327 16.1962 18.4516 13.9687 18.4516 12.2367C18.4516 9.97099 18.2482 6.86326 18.1057 4.96632C18.0606 4.366 17.5938 3.89237 16.9969 3.83603C15.5651 3.70088 13.5225 3.53846 12 3.53846C10.4775 3.53846 8.43487 3.70088 7.00309 3.83603C6.40624 3.89237 5.9394 4.366 5.89429 4.96632C5.75175 6.86326 5.54839 9.97099 5.54839 12.2367C5.54839 13.9687 5.66734 16.1962 5.78756 18.0019C5.83874 18.7707 5.88993 19.4598 5.93179 19.9971Z" fill="#030D45"></path> </g></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <base-pagination :meta="dataPaginate" :current-page="currentPage" @changePage="changePage"></base-pagination>
    </div>
</div>

<base-modal title="Bộ lọc" :is-open="showFilter" @close="showFilter = false">
    <div class="modal-content">
        <div class="all-filter">
            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <span>Vị trí & Thông tin cá nhân</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="filter-group">
                            <base-select v-model="filters.provinces_id" :options="cityOptions" label="Thành phố" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.sex" :options="genderOptions" label="Giới tính" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.timeSlotStart" :options="timeSlotOptions" label="Thời gian rảnh bắt đầu" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.timeSlotEnd" :options="timeSlotOptions" label="Thời gian rảnh kết thúc" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="separation"></div>

            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group filter-group-flex2">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Học vấn & Môn học</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="filter-group">
                            <base-select v-model="filters.subject" :options="subjectOptions" label="Môn học" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.educationLevel" :options="educationLevelOptions" label="Cấp độ" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                        <div class="filter-group">
                            <base-select v-model="filters.experience" :options="experienceOptions" label="Kinh nghiệm" placeholder="Tất cả" size="medium" widthFull="true" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="separation"></div>

            <div class="filter-row filter-row-flex align-end">
                <div class="filter-group filter-group-flex2">
                    <label class="filter-label filter-label-bold">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>Đánh giá & Mức giá</span>
                    </label>
                    <div class="filter-row-flex filter-row_wrapper">
                        <div class="slider-block">
                            <div class="slider-info">Đánh giá tối thiểu: {{ filters.rating }} sao</div>
                            <div class="custom-slider-wrapper">
                                <input type="range" min="0" max="5" step="0.5" v-model.number="filters.rating" class="custom-slider" />
                                <div class="slider-labels">
                                    <span>0 sao</span>
                                    <span>2.5 sao</span>
                                    <span>5 sao</span>
                                </div>
                            </div>
                        </div>
                        <div class="slider-block">
                            <div class="slider-info">Mức giá tối đa: {{ $helper.formatCurrency(filters.price) }} / giờ</div>
                            <div class="custom-slider-wrapper">
                                <input type="range" min="100000" max="500000" step="10000" v-model.number="filters.price" class="custom-slider" />
                                <div class="slider-labels">
                                    <span>100k</span>
                                    <span>300k</span>
                                    <span>500k</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-lg btn-primary w-100" @click="showFilter = false">Hiển thị kết quả</button>
        </div>
    </div>
</base-modal>

</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    watch
} from 'vue';
import {
    useStore
} from 'vuex';
import {
    useRouter, useRoute
} from 'vue-router';
import BaseSelect from '@/components/BaseSelect.vue';

import {
    getCurrentInstance
} from 'vue';

const {
    proxy
} = getCurrentInstance();
const store = useStore();
const router = useRouter();
const route = useRoute();

// Filter data
const filters = ref({
    provinces_id: route.query.city ?? "",
    sex: '',
    timeSlotStart: '',
    timeSlotEnd: '',
    subject: route.query.subject ?? "",
    educationLevel: route.query.level ?? "",
    experience: route.query.experience ?? "",
    rating: 0,
    price: 500000,
});

// Search state
const tutors = ref([]);
const hasSearched = ref(false);
const currentPage = ref(1);
const showPriceDetail = ref(false);
const showFilter = ref(false);
const dataPaginate = ref({});

// Options for filters
const subjectOptions = computed(() => {
    const subjects = store.state.configuration?.subjects || [];
    return subjects.map(subject => ({
        id: subject.id,
        name: subject.name
    }));
});

const educationLevelOptions = computed(() => {
    const levels = store.state.configuration?.educationLevels || [];
    return levels.map(level => ({
        id: level.id,
        name: level.name
    }));
});

const experienceOptions = ref([{
        id: 'new',
        name: 'Mới (0-2 năm)'
    },
    {
        id: 'experienced',
        name: 'Kinh nghiệm (3-5 năm)'
    },
    {
        id: 'expert',
        name: 'Chuyên gia (5+ năm)'
    }
]);

const cityOptions = computed(() => {
    const provinces = store.state.configuration?.provinces || [];
    return provinces.map(p => ({ id: p.id, name: p.name }));
});
const genderOptions = computed(() => [
    { id: '', name: 'Tất cả' },
    { id: 1, name: 'Nam' },
    { id: 2, name: 'Nữ' }
]);
const timeSlotOptions = computed(() => {
    const timeSlots = store.state.configuration?.timeSlots || [];
    return timeSlots.map(slot => ({ id: slot.id, name: slot.name }));
});

const changePage = async (page) => {
    currentPage.value = page;
    const params = {
        page: currentPage.value
    };

    hasSearched.value = true;

    if (filters.value.provinces_id) params.provinces_id = filters.value.provinces_id;
    if (filters.value.sex) params.sex = filters.value.sex;
    if (filters.value.timeSlotStart) params.time_slot_start = filters.value.timeSlotStart;
    if (filters.value.timeSlotEnd) params.time_slot_end = filters.value.timeSlotEnd;
    if (filters.value.subject) params.subject_id = filters.value.subject;
    if (filters.value.educationLevel) params.education_level_id = filters.value.educationLevel;
    if (filters.value.experience) params.experience = filters.value.experience;

    const response = await proxy.$api.apiGet('tutors', params);

    tutors.value = Array(6).fill().flatMap(() => response.data);
    dataPaginate.value = response.meta;
}

const navigateToTutor = (uid) => {
    router.push(`/tutor/${uid}`);
};

let firstLoad = true;
const debounce = (fn, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
};
const debouncedChangePage = debounce(() => changePage(currentPage.value), 300);

const redirectToBooking = (uid) => {
    return router.push(`/booking/${uid}`);
}

watch(filters, () => {
    if (firstLoad) {
        firstLoad = false;
        return;
    }
    debouncedChangePage();
}, { deep: true });

onMounted(() => {
    if (route.query.city) filters.value.provinces_id = route.query.city;
    if (route.query.subject) filters.value.subject = route.query.subject;
    if (route.query.level) filters.value.educationLevel = route.query.level;
    if (route.query.experience) filters.value.experience = route.query.experience;
    changePage();
});

watch(() => route.query, (newQuery) => {
    if (newQuery.city) filters.value.provinces_id = newQuery.city;
    if (newQuery.subject) filters.value.subject = newQuery.subject;
    if (newQuery.level) filters.value.educationLevel = newQuery.level;
    if (newQuery.experience) filters.value.experience = newQuery.experience;
    changePage();
});
</script>

<style scoped>
.search-page {
    padding: 2rem 0;
    /* background: var(--gray-50); */
}

.search-page .container {
    display: grid;
    gap: 1rem;
}
.search-page .header-wrapper {
    text-align: center;
    margin-bottom: 3rem;
}
.search-page .title {
    font-size: var(--font-size-heading-2);
    font-weight: 900;
    margin: 0;
}

.all-filter {
    display: grid;
    border-radius: 2rem 1rem 2rem 1rem;
    margin-bottom: 1rem;
}

.filter-mobile {
    display: none;
}

.filter-row-group {
    display: flex;
    justify-content: space-between;
}

.filter-row-group > .filter-row {
    display: flex;
    gap: 1rem;
}

.filter-row {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.filter-group {
    display: grid;
    width: 100%;
}

.filter-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: var(--font-size-base);
}

.filter-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    border-top: 1px solid var(--gray-200);
}

/* Search Results */
.search-results {
    margin-top: 1rem;
}
.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.results-title {
    font-size: var(--font-size-medium);
    font-weight: 500;
    color: var(--gray-800);
}

.sort-options {
    min-width: 200px;
}

.tutors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    /* grid-template-columns: 1fr 1fr; */
    gap: 1.5rem;
}

.tutor-card {
    cursor: pointer;
    background: white;
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.10);
    border: 1px solid var(--gray-50);
}

.tutor-header_detail {
    position: absolute;
    bottom: 2rem;
    left: 1.5rem;
    color: white;
    text-shadow: 0 0 5px rgba(0, 0, 0, 0.3), 0 0 10px rgba(0, 0, 0, 0.3), 0 0 15px rgba(0, 0, 0, 0.3);
    line-height: 1.3;
}
.tutor-name {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: var(--font-size-heading-5);
    font-weight: 600;
    color: white;
}

.tutor-header-wrapper {
    position: relative;
}

.tutor-image {
    width: 100%;
    height: 300px;
    object-fit:cover;
    border-radius: 1.25rem;
    margin-bottom: 1rem;
}

.tutor-header {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: right;
    margin-bottom: 1.5rem;
}

.tutor-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: var(--font-size-mini);
    font-weight: 500;
}

.badge-yellow {
    background: #FFF3DC;
    color: #B54708;
}

.badge-green {
    background: #DCFCE7;
    color: #15803D;
}

.tutor-actions-top {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    border: 1px solid #E5E7EB;
    background: white;
    color: #6B7280;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.tutor-profile {
    position: relative;
    z-index: 1;
    display: flex;
    color: white;
    gap: 0.8rem;
}

.avatar {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: #F3F4F6;
    overflow: hidden;
    border: 2px solid black;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    flex: 1;
}

.tutor-name {
    font-size: 1rem;
    font-weight: 600;
    color: white;
    margin-bottom: 0.25rem;
}

.location-name {
    font-size: var(--font-size-small);
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    /* color: #6B7280; */
    font-size: 0.875rem;
}

.rating i {
    color: #FBBF24;
    font-size: 0.875rem;
}

.btn-like {
    position: absolute;
    background: rgb(214 214 214 / 40%);
    top: 1rem;
    right: 1rem;
    color: white;
    width: 40px;
    height: 40px;
    border: 1px solid var(--gray-300);
    border-radius: 100%;
}

.subject-levels {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.level-tag {
    padding: 0.25rem 0.75rem;
    background: #F3F4F6;
    border-radius: 1rem;
    font-size: var(--font-size-mini);
    color: #374151;
    font-weight: 500;
}

.tutor-info {
    display: grid;
    gap: 1rem;
}

.subject-list {
    display: flex;
    flex-direction: column;
}

.subject-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.subject-tag {
    padding: 0.25rem 0.75rem;
    background: var(--gray-100);
    border-radius: 1rem;
    font-size: var(--font-size-mini);
    color: #374151;
    font-weight: 600;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-wrapper {
    line-height: 1;
    display: flex;
    flex-direction: column;
    gap: 0.37rem;
}

.info-item {
    display: flex;
    gap: 0.75rem;
    color: #6B7280;
}

.info-item i {
    font-size: 1rem;
    margin-top: 0.125rem;
    width: 1rem;
    text-align: center;
}

.info-title {
    font-size: var(--font-size-base);
    font-weight: 500;
    color: black;
}

.info-subtitle {
    font-size: var(--font-size-small);
    color: #6B7280;
}

.price-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.price-range {
    font-size: var(--font-size-base);
    font-weight: 600;
    color: #111827;
}

.stats {
    display: flex;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: baseline;
    gap: 0.375rem;
    color: #6B7280;
    font-size: var(--font-size-mini);
}

.stat-item i {
    font-size: 1rem;
}

.price-detail-popup {
    position: absolute;
    left: 0;
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    padding: 1rem 1.2rem 1rem 1.2rem;
    z-index: 10;
    min-width: 260px;
    max-width: 320px;
}
.popup-title {
    font-size: var(--font-size-small);
    font-weight: 500;
    margin-bottom: 0.7em;
    color: #555;
}
.price-detail-subject {
    /* margin-bottom: 0.75rem; */
}
.subject-title {
    font-size: var(--font-size-small);
    font-weight: 500;
    margin-bottom: 0.25rem;
    color: #222;
}
.price-table {
    width: 100%;
    border-collapse: collapse;
    font-weight: 400;
    font-size: var(--font-size-mini);
}
.price-table td {
    padding: 0.15em 0.3em 0.15em 0;
    font-size: 0.97em;
    color: var(--gray-500);
}
.level-name {
    width: 60%;
}
.level-price {
    text-align: right;
    font-weight: 500;
    color: #18181B;
}
.price-detail-popup hr {
    border: none;
    border-top: 1px solid #eee;
    margin: 0.5em 0 0.5em 0;
}

.custom-slider-wrapper {
    width: 100%;
    margin-bottom: 0.3em;
    position: relative;
}
.custom-slider {
    width: 100%;
    appearance: none;
    height: 6px;
    background: #eee;
    outline: none;
    border-radius: 4px;
    transition: background 0.3s;
    margin-bottom: 0.7em;
}
.custom-slider::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #111;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.custom-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #111;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.custom-slider::-ms-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #111;
    cursor: pointer;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
}
.custom-slider:focus {
    outline: none;
    background: #ddd;
}
.slider-labels {
    display: flex;
    justify-content: space-between;
    font-size: var(--font-size-mini);
    color: #888;
    margin-top: 0.1em;
}

.filter-label-bold {
    margin-bottom: 0.5rem;
    font-weight: 600;
}
.filter-group-flex2 {
    flex: 2;
}
.slider-block {
    flex: 1;
}
.slider-info {
    font-size: 0.95em;
    margin-bottom: 0.2em;
}

.tutor-actions {
    display: flex;
    gap: 0.75rem;
}

@media (max-width: 1024px) {
    .filter-row {
        grid-template-columns: 1fr;
    }

    .filter-row_wrapper {
        display: grid;
        grid-template-columns: 2fr;
    }
}

@media (max-width: 868px) {
    .filter-row {
        grid-template-columns: 1fr;
    }

    .filter-row_wrapper {
        display: grid;
        grid-template-columns: 2fr;
    }

    .container {
        padding: 0 16px;
    }

    .filter-desktop {
        display: none;
    }

    .filter-mobile {
        display: block;
    }
}

@media (max-width: 640px) {
    .tutors-section {
        padding: 1rem;
    }

    .tutor-actions {
        flex-direction: column;
    }

    .filter-row {
        grid-template-columns: 1fr;
    }

    .filter-row_wrapper {
        display: grid;
        grid-template-columns: 2fr;
    }

    .container {
        padding: 0 16px;
    }

    .filter-desktop {
        display: none;
    }

    .filter-mobile {
        display: block;
    }
}
</style>

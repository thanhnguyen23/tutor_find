<template>
<section class="tutors-section">
    <div class="container">
        <!-- <div class="stars-suffix">
            <span>Learning made fun: 35,857 English tutors for kids</span>
        </div> -->
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
                                <i class="fa-solid fa-angle-down icon-md icon-arrow-down"></i>
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
</section>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue';
import { useRouter } from 'vue-router';

const { proxy } = getCurrentInstance();
const router = useRouter();
const tutors = ref([]);
const showPriceDetail = ref(null);

const fetchTutors = async () => {
    try {
        const response = await proxy.$api.apiGet(`tutors`);
        tutors.value = Array(6).fill().flatMap(() => response.data);
    } catch (error) {
        console.error('Error fetching tutors:', error);
    }
};

const navigateToTutor = (uid) => {
    router.push(`/tutor/${uid}`);
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
};

const redirectToBooking = (uid) => {
    return router.push(`/booking/${uid}`);
}

onMounted(() => {
    fetchTutors();
});
</script>

<style scoped>

.container {
    margin: auto;
}

.stars-suffix {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: var(--font-size-heading-4);
    font-weight: 600;
    margin-bottom: 1.5rem;
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

.tutor-image {
    width: 100%;
    height: 300px;
    object-fit:cover;
    border-radius: 1.25rem;
    margin-bottom: 1rem;
}

.tutor-header-wrapper {
    position: relative;
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

.tutor-profile {
    position: relative;
    z-index: 1;
    display: flex;
    color: white;
    gap: 0.8rem;
}

.profile-info {
    flex: 1;
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

.location-name {
    font-size: var(--font-size-small);
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    /* color: #6B7280; */
    font-size: var(--font-size-small);
}

.rating i {
    color: #FBBF24;
    font-size: var(--font-size-small);
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

.icon-arrow-down {
    margin-left: 0.5rem;
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

.tutor-actions {
    display: flex;
    gap: 0.75rem;
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
    font-size: var(--font-size-base);
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

@media (max-width: 640px) {
    .tutor-actions {
        flex-direction: column;
    }

    .btn-book,
    .btn-profile {
        width: 100%;
    }
}
</style>

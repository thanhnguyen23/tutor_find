<template>
    <div class="content-section">
        <!-- Bảng giá theo cấp độ -->
        <div class="content-card">
            <h3 class="section-title">Bảng giá theo cấp độ</h3>

            <!-- Subject tabs -->
            <div class="type-selector">
                <button
                    v-for="subject in user.user_subjects"
                    :key="subject.id"
                    :class="['type-button', { active: currentSubject === subject.subject_id }]"
                    @click="currentSubject = subject.subject_id"
                >
                    {{ subject.subject_name }}
                </button>
            </div>

            <!-- Price cards -->
            <div class="price-cards">
                <div class="price-card" :class="{ 'highlight': level.highlight }" v-for="level in getLevelsBySubject" :key="level.id">
                    <div class="price-card-header">
                        <div class="level-icon" :class="level.class">
                            <img class="icon-md" :src="`/${level.education_level_image}`" alt="Level Icon">
                        </div>
                        <div class="level-info">
                            <h4>{{ level.education_level }}</h4>
                            <p>{{ level.education_level_description }}</p>
                        </div>
                    </div>
                    <div class="price-amount">
                        {{ level.price.toLocaleString() }} <span>VNĐ/giờ</span>
                    </div>
                    <button class="btn-lg btn-secondary" :class="{ 'btn-primary': level.highlight }">
                        Chọn {{ level.education_level }}
                    </button>
                </div>
            </div>

            <p class="price-note">Tất cả các buổi học Toán học đều bao gồm tài liệu học tập cá nhân hóa, bài tập thực hành và theo dõi tiến độ.</p>
        </div>

        <!-- Gói học -->
        <div class="content-card" v-if="getPackagesBySubject.lenght > 0">
            <h3 class="section-title">Gói học</h3>
            <div class="package-cards">
                <div class="package-card" :class="{ 'highlight': pkg.highlight }" v-for="pkg in getPackagesBySubject" :key="pkg.id">
                    <div class="package-header">
                        <h5>{{ pkg.name }}</h5>
                        <p>{{ pkg.number_of_lessons }} buổi / {{ pkg.duration }} phút 1 buổi</p>
                    </div>
                    <div class="package-price">
                        <p>{{ pkg.price.toLocaleString() }} <span>VND</span></p>
                        <p v-if="pkg.savings" class="package-savings">
                            Tiết kiệm {{ pkg.savings }} so với buổi học đơn lẻ
                        </p>
                    </div>
                    <ul class="package-features">
                        <li v-for="(feature, index) in pkg.features" :key="index">
                            <div class="features-check">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>
                            </div>
                            {{ feature.feature }}
                        </li>
                    </ul>
                    <button class="btn-lg btn-secondary" :class="{ 'btn-primary': pkg.highlight }">
                        Chọn {{ pkg.name }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { defineProps } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const currentSubject = ref(props.user.user_subjects[0].subject_id);

const getLevelsBySubject = computed(() => {
    // console.log(props.user.user_subjects.find(subject => subject.id === currentSubject.value), currentSubject.value);
    return props.user.user_subjects.find(subject => subject.subject_id == currentSubject.value).user_subject_levels;
});

const getPackagesBySubject = computed(() => {
    return props.user.user_packages.filter(pkg => pkg.subject_id == currentSubject.value);
});

</script>

<style scoped>
@import url('@css/UserDetail.css');

.subject-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    padding: 0.25rem;
    background: #f4f4f5;
    border-radius: 0.5rem;
}

.subject-tab {
    padding: 0.5rem 1rem;
    border: none;
    background: transparent;
    color: #71717a;
    font-weight: 500;
    cursor: pointer;
    border-radius: 0.375rem;
}

.subject-tab.active {
    background: white;
    color: #18181b;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.price-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin: 1rem 0 1rem;
}

.price-card {
    padding: 1.5rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow: hidden;
}

.price-card.highlight {
    border-color: #18181b;
}

.price-card-header {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.level-icon {
    width: 2.1rem;
    height: 2.1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f4f4f5;
    color: #18181b;
}

.level-info h4 {
    font-size: var(--font-size-large);
    font-weight: 600;
    color: #18181b;
    margin: 0;
}

.level-info p {
    color: #71717a;
    font-size: var(--font-size-small);
    margin: 0.25rem 0 0 0;
}

.price-amount {
    font-size: var(--font-size-large);
    font-weight: 700;
    color: #18181b;
}

.price-amount span {
    font-size: var(--font-size-small);
    font-weight: 400;
    color: #71717a;
}

.price-note {
    color: #71717a;
    font-size: var(--font-size-small);
    margin-top: 1rem;
}

.package-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.package-card {
    padding: 1.5rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    overflow: hidden;
}

.package-card.highlight {
    border-color: #18181b;
}
.package-card button {
    margin-top: auto;
}

.package-header h5 {
    font-weight: 600;
    color: #18181b;
    margin-bottom: 0.2rem;
}

.package-header p {
    color: #71717a;
    font-size: var(--font-size-small);
    margin: 0;
}

.package-price p:first-child {
    font-size: var(--font-size-large);
    font-weight: 700;
    color: #18181b;
    margin: 0;
}

.package-price span {
    font-size: var(--font-size-small);
    font-weight: 400;
    color: #71717a;
}

.package-savings {
    font-size: var(--font-size-small);
    margin: 0;
    border-radius: 1rem;
    display: inline-block;
    color: #71717a;
}

.package-features {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.package-features li {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.package-features .features-check {
    background-color: #18181b1a;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 100%;
}

.package-features li svg {
    width: 1rem;
    height: 1rem;
    flex-shrink: 0;
}

.group-description {
    color: #71717a;
    margin-bottom: 1.5rem;
}

.group-table {
    border: 1px solid #e4e4e7;
    border-radius: 0.75rem;
    overflow: hidden;
}

.group-header {
    display: grid;
    grid-template-columns: 2fr 2fr 2fr 1fr;
    padding: 1rem;
    background: #f4f4f5;
    font-weight: 600;
    color: #18181b;
}

.group-row {
    display: grid;
    grid-template-columns: 2fr 2fr 2fr 1fr;
    padding: 1rem;
    align-items: center;
    border-top: 1px solid #e4e4e7;
}

.savings-badge {
    width: max-content;
    color: #059669;
    font-size: var(--font-size-small);
    padding: 0.3rem 0.5rem;
    background: #ecfdf5;
    border-radius: 1rem;
    display: inline-block;
}

.group-note {
    color: #71717a;
    font-size: var(--font-size-small);
    margin-top: 1rem;
}

.custom-package {
    background: #f4f4f5;
    border: none;
}

.custom-package-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.custom-package-content svg {
    flex-shrink: 0;
    color: #18181b;
}

.custom-package-text {
    flex: 1;
}

.custom-package-text h4 {
    font-weight: 600;
    color: #18181b;
    margin: 0 0 0.5rem 0;
}

.custom-package-text p {
    color: #71717a;
    margin: 0;
}

.btn-select {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    background: white;
    color: #18181b;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: auto;
}

.btn-select:hover {
    background: #f4f4f5;
}

.btn-dark {
    background: #18181b;
    color: white;
    border: none;
}

.btn-dark:hover {
    background: #27272a;
}

.btn-book {
    padding: 0.5rem 1rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.375rem;
    background: white;
    color: #18181b;
    font-weight: 500;
    cursor: pointer;
}

.btn-custom {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 0.5rem;
    background: #18181b;
    color: white;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
}

.highlight {
    position: relative;
}

.highlight::after {
    content: 'Phổ biến';
    position: absolute;
    top: 0.7rem;
    right: -2.8rem;
    background: #18181b;
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 3rem;
    border-radius: 0.25rem;
    transform: rotate(39deg);
}

.type-selector {
    border-radius: 0.7rem;
}

.type-button.active {
    border-radius: 0.7rem;
}

@media (max-width: 768px) {
    .type-selector {
        display: grid;
    }

    .type-button.active {
        width: 100%;
    }
}
</style>

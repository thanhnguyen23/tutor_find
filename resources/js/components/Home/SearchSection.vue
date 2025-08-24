<template>
<section class="search-section">
    <div class="container">
        <div class="search-title-wrapper">
            <div class="search-badge">Đội ngũ chuyên nghiệp</div>
            <h2 class="search-title">Tìm gia sư phù hợp với bạn</h2>
            <p class="search-description">
                Khám phá đội ngũ gia sư chất lượng cao của chúng tôi và tìm người phù hợp nhất với nhu cầu học tập của bạn
            </p>
        </div>

        <div class="search-form">
            <div class="search-row">
                <div class="form-group">
                    <base-select
                        size="medium"
                        v-model="selectedCity"
                        :options="cities"
                        placeholder="Tất cả thành phố"
                        custom-class="form-control-md"
                        label="Thành phố"
                    />
                </div>
                <div class="form-group">
                    <base-select
                        size="medium"
                        v-model="selectedSubject"
                        :options="subjects"
                        placeholder="Tất cả môn học"
                        custom-class="form-control-md"
                        label="Môn học"
                    />
                </div>

                <div class="form-group">
                    <base-select
                        size="medium"
                        v-model="selectedLevel"
                        :options="levels"
                        placeholder="Tất cả cấp độ"
                        custom-class="form-control-md"
                        label="Cấp độ"
                    />
                </div>

                <!-- <div class="form-group">
                    <label>Đánh giá</label>
                    <base-select
                        size="medium"
                        v-model="selectedRating"
                        :options="ratings"
                        placeholder="Tất cả đánh giá"
                        custom-class="form-control-md"
                    />
                </div> -->
            </div>

            <div class="search-filters">
                <div class="filter-right">
                    <base-select
                        size="medium"
                        v-model="selectedExperience"
                        :options="experiences"
                        placeholder="Tất cả kinh nghiệm"
                        custom-class="form-control-md"
                        label="Kinh nghiệm"
                    />
                </div>
                <button class="btn-lg btn-primary" @click="handleSearch">
                    Tìm kiếm
                </button>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

const router = useRouter();
const store = useStore();

const selectedCity = ref(null);
const selectedSubject = ref(null);
const selectedLevel = ref(null);
const selectedExperience = ref(null);
const selectedRating = ref(null);
const selectedSort = ref('rating');

const cities = computed(() => {
    const provinces = store.state.configuration?.provinces || [];
    return [
        { id: '', name: 'Tất cả thành phố' },
        ...provinces.map(p => ({ id: p.id, name: p.name }))
    ];
});

const subjects = computed(() => {
    const subjects = store.state.configuration?.subjects || [];
    return [
        { id: '', name: 'Tất cả môn học' },
        ...subjects.map(s => ({ id: s.id, name: s.name }))
    ];
});

const levels = computed(() => {
    const levels = store.state.configuration?.educationLevels || [];
    return [
        { id: '', name: 'Tất cả cấp độ' },
        ...levels.map(l => ({ id: l.id, name: l.name }))
    ];
});

const experiences = [
    { id: '', name: 'Tất cả' },
    { id: 'new', name: 'Mới (0-2 năm)' },
    { id: 'experienced', name: 'Kinh nghiệm (3-5 năm)' },
    { id: 'expert', name: 'Chuyên gia (5+ năm)' }
];


function handleSearch() {
    const params = {
        city: selectedCity.value,
        subject: selectedSubject.value,
        level: selectedLevel.value,
        experience: selectedExperience.value,
        // Có thể thêm rating, sort nếu muốn
    };
    // Xóa các param null/undefined hoặc 'all'
    Object.keys(params).forEach(key => {
        if (!params[key]) delete params[key];
    });
    router.push({ name: 'search', query: params });
}
</script>

<style scoped>
.search-title-wrapper {
    margin-bottom: 3rem;
    text-align: center;
}

.search-badge {
    display: inline-block;
    padding: 6px 1rem;
    background: #f3f4f6;
    border-radius: 2rem;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 1rem;
}

.search-title {
    font-size: var(--font-size-heading-3);
    font-weight: 700;
    margin-bottom: 12px;
}

.search-description {
    font-size: 1rem;
    color: #6b7280;
    margin-bottom: 32px;
    max-width: 700px;
    margin: auto;
}

.search-form {
    max-width: 60rem;
    margin: auto;
    background: white;
    border-radius: 2rem 1rem 2rem 1rem;
    padding: 1.8rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.search-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.search-filters {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}

.filter-right {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-label {
    font-size: 14px;
    color: #6b7280;
}

.btn-md {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-md svg {
    width: 20px;
    height: 20px;
}

@media (max-width: 1024px) {
    .search-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .search-title {
        font-size: 30px;
    }

    .search-filters {
        flex-wrap: wrap;
    }

    .btn-md {
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }
}

@media (max-width: 640px) {
    .search-row {
        grid-template-columns: 1fr;
    }

    .container {
        padding: 0 1rem;
    }

    .search-form {
        padding: 1rem;
    }
}
</style>

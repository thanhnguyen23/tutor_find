<template>
<div class="section-card">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                </svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Hình thức học</span>
                <span class="sub-title">Tối ưu hồ sơ để thu hút sự lựa chọn từ học sinh</span>
            </div>
        </div>
        <div class="header-right">
            <div class="submit-block">
                <button class="btn-md btn-secondary" @click="handleSubmit()" :disabled="loading">
                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                    <span v-if="loading">Đang lưu...</span>
                    <span v-else>Lưu thay đổi</span>
                </button>
                <span v-if="successMessage" class="success-message">{{ successMessage }}</span>
                <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
            </div>
        </div>
    </div>
    <div class="main-content">
        <div class="card-content">
            <div class="location-type-group">
                <div v-for="type in locationTypes" :key="type.value" :class="['location-type-option', { selected: selectedTypes.includes(type.value) }]" @click.self="toggleType(type.value)">
                    <div class="location-content">
                        <div class="right-content">
                            <div class="option-icon" v-if="isHomeTutor(type)">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                            </div>
                            <div class="option-icon" v-else-if="isStudentHome(type)">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,15794,15824"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            </div>

                            <div class="option-main">
                                <span class="option-title">{{ type.label }}</span>
                                <template v-if="isHomeTutor(type)">
                                    <span class="option-desc">Học tại nhà của gia sư</span>
                                </template>
                                <template v-else-if="isStudentHome(type)">
                                    <span class="option-desc">Thiết lập bán kính và phí di chuyển</span>
                                </template>
                                <template v-else>
                                    <span class="option-desc">{{ type.description }}</span>
                                </template>
                            </div>
                        </div>
                        <div class="left-content">
                            <span class="option-checkbox">
                                <input type="checkbox" :checked="selectedTypes.includes(type.value)" :value="type.value" @change.stop="toggleType(type.value)" />
                            </span>
                        </div>
                    </div>
                    <template v-if="isStudentHome(type) && selectedTypes.includes(type.value)">
                        <div class="student-address-block">
                            <div class="item-block">
                                <BaseInput :required="true" v-model="maxDistance[type.value]" label="Bán kính tối đa (km)" placeholder="Nhập bán kính tối đa (km)" size="base" type="number" min="1" />
                            </div>
                            <div class="item-block">
                                <BaseInput :required="true" v-model="transportationFee[type.value]" label="Phí di chuyển (VNĐ/km)" placeholder="Nhập phí di chuyển (VNĐ/km)" size="base" type="number" min="0" />
                            </div>
                        </div>

                        <div class="information-detail-gird">
                            <div class="information-item">
                                <span>{{ maxDistance[type.value] ? maxDistance[type.value] : '0' }}</span>
                                <span>Tối đa</span>
                            </div>
                            <div class="information-item">
                                <span>{{ transportationFee[type.value] ? $helper.formatCurrency(transportationFee[type.value]) : '0' }}</span>
                                <span>Phí di chuyển</span>
                            </div>
                            <div class="information-item">
                                <span>{{ transportationFee[type.value] ? $helper.formatCurrency(transportationFee[type.value]) : '0' }}/1km</span>
                                <span>Ước tính</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
</template>


<script setup>
import {
    ref,
    computed,
    watch,
    onMounted,
    getCurrentInstance
} from 'vue';
import {
    useStore,
} from 'vuex';
import BaseInput from '../BaseInput.vue';

const props = defineProps({
    userDataDetail: {
        type: Object,
        required: true
    }
});

const store = useStore();
const {proxy} = getCurrentInstance();
const selectedTypes = ref([]);
const maxDistance = ref({});
const transportationFee = ref({});

const loading = ref(false);
const userAddress = computed(() => store.state.userData?.address);

const emit = defineEmits(['update-data']);

const locationTypes = computed(() => {
    const studyLocations = store.state.configuration.studyLocations || [];
    return studyLocations.map(loc => ({
        value: loc.id,
        label: loc.name?.trim(),
        description: loc.home_tutor?'' : (loc.home_user?'' : loc.description),
        icon: loc.icon,
        home_tutor: !!loc.home_tutor,
        home_user: !!loc.home_user
    }));
});

const isStudentHome = (type) => {
    const studyLoc = (store.state.configuration.studyLocations || []).find(l => l.id === type.value);
    return !!studyLoc?.home_user;
}
const isHomeTutor = (type) => {
    const studyLoc = (store.state.configuration.studyLocations || []).find(l => l.id === type.value);
    return !!studyLoc?.home_tutor;
}

function toggleType(typeId) {
    const idx = selectedTypes.value.indexOf(typeId);
    if (idx === -1) {
        selectedTypes.value.push(typeId);
    } else {
        selectedTypes.value.splice(idx, 1);
        // Xóa dữ liệu liên quan nếu bỏ chọn
        delete maxDistance.value[typeId];
        delete transportationFee.value[typeId];
    }
}

const initFromProps = () => {
    if (props.userDataDetail?.user_study_locations) {
        selectedTypes.value = props.userDataDetail.user_study_locations.map(item => item.study_location_id);

        props.userDataDetail.user_study_locations.forEach(item => {
            if (item.max_distance !== null)
                maxDistance.value[item.study_location_id] = item.max_distance;
            if (item.transportation_fee !== null)
                transportationFee.value[item.study_location_id] = item.transportation_fee;
        });
    }
};

onMounted(initFromProps);
watch(() => props.userDataDetail?.user_study_locations, initFromProps, {
    immediate: true
});

const handleSubmit = async () => {
    loading.value = true;
    try {
        const payload = selectedTypes.value.map(typeId => {
            const type = locationTypes.value.find(t => t.value === typeId);
            let data = {
                id: typeId
            };
            if (type.home_user) {
                data.max_distance = maxDistance.value[typeId];
                data.transportation_fee = transportationFee.value[typeId];
            }
            return data;
        });
        const result = await proxy.$api.apiPost('me/studyLocation', {
            study_methods: payload
        });

        emit('update-data', {
            ...props.userDataDetail,
            user_study_locations: result.data
        });

        proxy.$notification.success('Cập nhật hình thức học thành công!');
    } catch (e) {
        proxy.$notification.error(e?.message || 'Cập nhật hình thức học thất bại!');
    } finally {
        loading.value = false;
    }
}
</script>


<style scoped>
@import url('@css/profileNew.css');

.location-type-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.location-type-option {
    display: flex;
    flex-direction: column;
    gap: 0.7rem;
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    border-radius: 10px;
    padding: 1rem;
    transition: all 0.3s ease;
    position: relative;
}

.location-type-option .right-content {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.location-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-icon {
    background: var(--color-primary-transparent);
    border-radius: 2rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
}


.option-main {
    display: flex;
    flex-direction: column;
}
.option-title {
    font-weight: 500;
    font-size: var(--font-size-heading-6);
    line-height: 1.2;
}
.option-desc {
    font-size: var(--font-size-small);
    font-weight: 500;
    color: var(--color-primary);
}

.option-checkbox {
    display: flex;
    align-items: center;
    margin-left: 1rem;
}

.option-checkbox input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 1rem;
    height: 1rem;
    border-radius: 9999px; /* Làm checkbox thành hình tròn */
    border: 1px solid var(--color-primary-light);
    background-color: white;
    cursor: pointer;
    display: inline-block;
    position: relative;
}

.option-checkbox input[type="checkbox"]:checked {
    background-color: var(--color-primary);
    display: flex;
    align-items: center;
    justify-content: center;
}

.option-checkbox input[type="checkbox"]:checked::after {
    content: '';
    position: absolute;
    width: 0.9rem;
    height: 0.9rem;
    border-radius: 9999px;
    background-color: var(--color-primary-transparent);
}

.student-address-block {
    display: flex;
    align-items: end;
    gap: 0.5rem;
    margin-top: 1.2rem;
}

.student-address-block .item-block {
    width: 100%;
}

.submit-block {
    display: flex;
    align-items: center;
    justify-content: end;
    gap: 1.5rem;
}

.success-message {
    color: #22c55e;
    font-size: 1rem;
}

.error-message {
    color: #dc2626;
    font-size: 1rem;
}
</style>

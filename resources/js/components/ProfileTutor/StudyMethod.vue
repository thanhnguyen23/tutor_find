<template>
<div class="section">
    <div class="section-header">
        <div class="header-left">
            <svg class="icon-base" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
            </svg>
            <span>Hình thức học</span>
        </div>
    </div>
    <div class="info-card">
        <div class="card-content">
            <div class="location-type-group">
                <div v-for="type in locationTypes" :key="type.value" :class="['location-type-option', { selected: selectedTypes.includes(type.value) }]" @click.self="toggleType(type.value)">
                    <div class="location-content">
                        <div class="right-content">
                            <span class="option-icon" v-html="type.icon"></span>
                            <div class="option-main">
                                <span class="option-label">
                                    <span class="label">{{ type.label }}</span>
                                </span>
                                <template v-if="isHomeTutor(type)">
                                    <span class="option-desc">{{ userAddress }}</span>
                                </template>
                                <template v-else-if="isStudentHome(type)"></template>
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
                    </template>
                </div>
            </div>
            <div class="submit-block">
                <button class="btn-md btn-primary btn-save" @click="handleSubmit()" :disabled="loading">
                    <span v-if="loading">Đang lưu...</span>
                    <span v-else>Lưu</span>
                </button>
                <span v-if="successMessage" class="success-message">{{ successMessage }}</span>
                <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
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
    } catch (e) {} finally {
        loading.value = false;
    }
}
</script>

<style scoped>
@import url('@css/profile.css');

.location-type-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.location-type-option {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.1rem 1.2rem;
    cursor: pointer;
    transition: border 0.2s;
    position: relative;
}

.location-type-option.selected {
    border: 1px solid #18181b;
    /* background: #f4f4f5; */
}

.location-type-option.selected .option-icon {
    /* background: #18181b33; */
}

.location-type-option .right-content {
    display: flex;
    align-items: center;
}

.location-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.option-icon {
    display: flex;
    align-items: center;
    margin-right: 0.7rem;
    background: #f3f4f6;
    border-radius: 2rem;
    width: 40px;
    height: 40px;
    justify-content: center;
}

.option-label {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 0.1rem;
}

.option-label .label {
    font-weight: 500;
    font-size: var(--font-size-heading-6);
}

.option-checkbox {
    display: flex;
    align-items: center;
    margin-left: 1rem;
}

.option-checkbox input[type="checkbox"] {
    accent-color: #18181b;
    width: 1rem;
    height: 1rem;
}

.option-desc {
    color: #6b7280;
    font-size: 0.98rem;
}

.student-address-block {
    display: flex;
    gap: 0.5rem;
    margin-top: 1.2rem;
}

.student-address-block .item-block {
    width: 100%;
}

.submit-block {
    margin-top: 1rem;
    display: flex;
    align-items: center;
    justify-content: end;
    gap: 1.5rem;
}

.btn-save {
    min-width: 120px;
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

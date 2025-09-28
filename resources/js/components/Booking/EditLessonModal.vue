<template>
<base-modal :isOpen="isOpen" title="Chỉnh sửa thông tin buổi học" @close="emit('close')" size="large">
    <form @submit.prevent="handleSave">
        <div class="edit-grid">
            <div class="form-group">
                <label>Môn học</label>
                <select v-model="form.subjectId" @change="onSubjectChange">
                    <option v-for="s in subjectOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Cấp độ</label>
                <select v-model="form.levelId">
                    <option v-for="l in levelOptionsFiltered" :key="l.id" :value="l.id">{{ l.name }}</option>
                </select>
            </div>
            <div class="form-group col-span-2">
                <label>Ngày học</label>
                <input type="date" v-model="form.date" />
            </div>
            <div class="form-group">
                <label>Giờ bắt đầu</label>
                <select v-model="form.startTime">
                    <option v-for="t in timeOptions" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
            <div class="form-group">
                <label>Giờ kết thúc</label>
                <select v-model="form.endTime">
                    <option v-for="t in timeOptions" :key="t.id" :value="t.id">{{ t.name }}</option>
                </select>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-md btn-secondary" @click="emit('close')">Hủy</button>
            <button type="submit" class="btn-md btn-primary">Lưu thay đổi</button>
        </div>
    </form>
</base-modal>
</template>

<script setup>
import {
    ref,
    computed,
    watch
} from 'vue'
const props = defineProps({
    isOpen: Boolean,
    bookingData: Object,
    subjectOptions: Array,
    levelOptions: Array,
    timeOptions: Array
})
const emit = defineEmits(['close', 'save'])

const form = ref({
    subjectId: '',
    levelId: '',
    date: '',
    startTime: '',
    endTime: ''
})

watch(() => props.bookingData, (val) => {
    if (val) {
        form.value.subjectId = val.subject?.id || ''
        form.value.levelId = val.level?.id || ''
        form.value.date = val.date || ''
        form.value.startTime = val.time?.start || ''
        form.value.endTime = val.time?.end || ''
    }
}, {
    immediate: true
})

const levelOptionsFiltered = computed(() => {
    return props.levelOptions.filter(l => l.subject_id === form.value.subjectId)
})

const onSubjectChange = () => {
    form.value.levelId = ''
}

const handleSave = () => {
    emit('save', {
        subjectId: form.value.subjectId,
        levelId: form.value.levelId,
        date: form.value.date,
        startTime: form.value.startTime,
        endTime: form.value.endTime
    })
}
</script>

<style scoped>
.edit-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.col-span-2 {
    grid-column: span 2;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}

select,
input[type="date"] {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.7rem 1rem;
    font-size: 1rem;
}
</style>

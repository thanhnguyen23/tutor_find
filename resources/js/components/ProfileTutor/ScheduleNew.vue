<template>
<div class="section-card schedule-section">
    <div class="header-wrapper">
        <div class="header-left">
            <div class="icon-wrapper">
                <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 2v4"></path>
                    <path d="M16 2v4"></path>
                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                    <path d="M3 10h18"></path>
                </svg>
            </div>
            <div class="title-wrapper">
                <span class="title-main">Lịch trình hàng tuần</span>
                <span class="sub-title">Quản lý thời gian biểu giảng dạy của bạn</span>
            </div>
        </div>
        <div class="header-right">
            <button class="btn-md btn-secondary" @click="showAddTimeSlotModal = true">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                <span>Thêm khung giờ</span>
            </button>
        </div>
    </div>
    <div class="main-content">
        <div class="schedule-main format-scrollbar">
            <div class="schedule-grid">
                <div class="list-days">
                    <div class="day time-header">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <div class="day" v-for="day in weekDayOptions" :key="day.id">
                        <span>{{ day.name }}</span>
                        <span>{{ day.name }}</span>
                    </div>
                </div>
                <div class="time-wrapper">
                    <div class="list-time" v-for="slot in timeSlotOptions" :key="slot.id">
                        <div class="time time-header">
                            <span>{{ slot.name }}</span>
                        </div>
                        <div class="time" v-for="day in weekDayOptions" :key="day.id + '-' + slot.id">
                            <template v-if="hasTimeSlot(day, slot)">
                                <div class="content-card free-time">
                                    <div class="content-detail">
                                        <span>{{ getSlotTimeRange(day, slot) }}</span>
                                        <span class="text-mini text-gray-500">Khung giờ trống</span>
                                    </div>
                                    <div class="slot-actions">
                                        <button @click.stop="handleEditTimeSlot(getTimeSlotId(day, slot))">
                                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.375 2.625a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"></path></svg>
                                        </button>
                                        <button @click.stop="handleDeleteTimeSlot(getTimeSlotId(day, slot))">
                                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template v-else>
                                <button class="add-new" @click="onAddSlot(day, slot)">
                                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M5 12h14"></path>
                                        <path d="M12 5v14"></path>
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal thêm khung giờ -->
    <base-modal
        :is-open="showAddTimeSlotModal"
        title="Thêm khung giờ"
        size="small"
        @close="showAddTimeSlotModal = false"
    >
        <div class="modal-content">
            <base-select
                v-model="newTimeSlot.day_of_week_id"
                :options="weekDayOptions"
                label="Ngày trong tuần"
                placeholder="Chọn ngày trong tuần"
                required="true"
            />

            <div class="form-group-container">
                <base-select
                    v-model="newTimeSlot.time_slot_id_start"
                    label="Thời gian bắt đầu"
                    :options="validStartTimes"
                    placeholder="Chọn giờ bắt đầu"
                    widthFull="true"
                    required="true"
                />
                <base-select
                    v-model="newTimeSlot.time_slot_id_end"
                    :options="validEndTimes"
                    label="Thời gian kết thúc"
                    placeholder="Chọn giờ kết thúc"
                    widthFull="true"
                    required="true"
                />
            </div>
            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showAddTimeSlotModal = false">Hủy</button>
                <button class="btn-md btn-primary" @click="addTimeSlot">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                    <span>Thêm khung giờ</span>
                </button>
            </div>
        </div>
    </base-modal>
    <!-- Modal xác nhận xóa -->
    <base-modal
        :is-open="showDeleteConfirmModal"
        title="Xác nhận xóa"
        size="small"
        @close="showDeleteConfirmModal = false"
    >
        <div class="modal-content">
            <div class="selected-time-slot">
                <div class="time-slot-preview">
                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4"></path>
                        <path d="M16 2v4"></path>
                        <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                        <path d="M3 10h18"></path>
                    </svg>
                    <span>Thứ {{ selectedTimeSlot.day_of_week_id }}</span>
                    <i class="far fa-clock"></i>
                    <span>{{ selectedTimeSlot.formattedTime }}</span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showDeleteConfirmModal = false">Hủy</button>
                <button class="btn-md btn-primary" @click="confirmDelete">Xóa</button>
            </div>
        </div>
    </base-modal>
    <!-- Modal chỉnh sửa khung giờ -->
    <base-modal
        :is-open="showEditTimeSlotModal"
        title="Chỉnh sửa khung giờ"
        size="small"
        @close="showEditTimeSlotModal = false"
    >
        <div class="modal-content">
            <base-select
                v-model="selectedTimeSlot.day_of_week_id"
                :options="weekDayOptions"
                placeholder="Chọn ngày trong tuần"
                label="Ngày trong tuần"
                required="true"
            />
            <div class="form-group-container">
                <base-select
                    v-model="selectedTimeSlot.time_slot_id_start"
                    :options="timeSlotOptions"
                    label="Thời gian bắt đầu"
                    placeholder="Chọn giờ bắt đầu"
                    widthFull="true"
                    required="true"
                />
                <base-select
                    v-model="selectedTimeSlot.time_slot_id_end"
                    :options="endTimeOptions"
                    label="Thời gian kết thúc"
                    placeholder="Chọn giờ kết thúc"
                    widthFull="true"
                    required="true"
                />
            </div>
            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showEditTimeSlotModal = false">Hủy</button>
                <button class="btn-md btn-primary" @click="updateTimeSlot">Cập nhật</button>
            </div>
        </div>
    </base-modal>
</div>

<!--
    <div class="content-card">
        <div class="subject-wrapper">
            <div class="icon-wrapper">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <span class="subject-name">Toán cao cấp</span>
        </div>
        <div class="content-detail">
            <span class="user-name">Nguyễn tiến thành</span>
            <span>07:00</span> - <span>08:00</span>
        </div>
    </div>
 -->
<!-- End language-section -->
</template>

<script setup>
import { ref, computed, getCurrentInstance } from 'vue';
import BaseSelect from '../BaseSelect.vue';
import { useStore } from 'vuex';
const { proxy } = getCurrentInstance();
const store = useStore();

const props = defineProps({
    userDataDetail: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update-data']);

const scheduleSlots = ref(props.userDataDetail.user_weekly_time_slots);
const showAddTimeSlotModal = ref(false);
const showDeleteConfirmModal = ref(false);
const showEditTimeSlotModal = ref(false);
const selectedTimeSlotId = ref(null);
const selectedTimeSlot = ref({
    day_of_week_id: '',
    time_slot_id_start: '',
    time_slot_id_end: '',
    repeat_weekly: true
});

const weekDayOptions = computed(() => store.state.configuration.dayOfWeeks);
const timeSlotOptions = computed(() => store.state.configuration.timeSlots);

const newTimeSlot = ref({
    day_of_week_id: '',
    time_slot_id_start: '',
    time_slot_id_end: '',
    repeat_weekly: true
});

const endTimeOptions = computed(() => {
    if (!newTimeSlot.value.time_slot_id_start) return timeSlotOptions.value;
    const startIndex = timeSlotOptions.value.findIndex(opt => opt.id === newTimeSlot.value.time_slot_id_start);
    return timeSlotOptions.value.slice(startIndex + 1);
});

const selectedDaySlots = computed(() => {
    if (!newTimeSlot.value.day_of_week_id) return [];
    return scheduleSlots.value.filter(
        slot => slot.day_of_week_id == newTimeSlot.value.day_of_week_id
    );
});

const validStartTimes = computed(() => {
    return timeSlotOptions.value.map(point => {
        const isOverlap = selectedDaySlots.value.some(slot => {
            const slotStart = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_start);
            const slotEnd = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_end);
            return (
                point.time >= slotStart.time &&
                point.time <= slotEnd.time
            );
        });
        return { ...point, disabled: isOverlap };
    });
});

const validEndTimes = computed(() => {
    if (!newTimeSlot.value.time_slot_id_start) return timeSlotOptions.value.map(p => ({ ...p, disabled: true }));
    const startObj = timeSlotOptions.value.find(t => t.id === newTimeSlot.value.time_slot_id_start);
    return timeSlotOptions.value.map(point => {
        const afterStart = point.time > startObj.time;
        const isOverlap = selectedDaySlots.value.some(slot => {
             const slotStart = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_start);
             const slotEnd = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_end);
            return (
                 startObj.time < slotEnd.time && point.time > slotStart.time
            );
        });
        return { ...point, disabled: !(afterStart && !isOverlap) };
    });
});

const addTimeSlot = async () => {
    try {
        const response = await proxy.$api.apiPost('me/schedule', newTimeSlot.value);
        if (response.data) {
            const newSlot = response.data;
            const timeSlotStart = timeSlotOptions.value.find(item => item.id == newSlot.time_slot_id_start);
            const timeSlotEnd = timeSlotOptions.value.find(item => item.id == newSlot.time_slot_id_end);

            scheduleSlots.value.push({
                ...newSlot,
                time_slot_start_name: timeSlotStart.name,
                time_slot_end_name: timeSlotEnd.name
            });

            const updateData = {
                ...props.userDataDetail,
                user_weekly_time_slots: scheduleSlots.value
            }

            emit('update-data', updateData);

            proxy.$notification.success('Thêm khung giờ thành công!');
        }
        showAddTimeSlotModal.value = false;
        resetNewTimeSlot();
    } catch (error) {
        proxy.$notification.error(error?.message || 'Thêm khung giờ thất bại!');
        console.error('Failed to add time slot:', error);
    }
};

const deleteTimeSlot = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/schedule/${id}`);
        scheduleSlots.value = scheduleSlots.value.filter(slot => slot.id !== id);

        const updateData = {
            ...props.userDataDetail,
            user_weekly_time_slots: scheduleSlots.value
        }

        emit('update-data', updateData);

        proxy.$notification.success('Xóa khung giờ thành công!');
    } catch (error) {
        proxy.$notification.error(error?.message || 'Xóa khung giờ thất bại!');
        console.error('Failed to delete time slot:', error);
    }
};

const resetNewTimeSlot = () => {
    newTimeSlot.value = {
        day_of_week_id: '',
        time_slot_id_start: '',
        time_slot_id_end: '',
        repeat_weekly: true
    };
};

const hasTimeSlot = (day, slot) => {
    if (!scheduleSlots.value || scheduleSlots.value.length == 0) {
        return false;
    }
    return scheduleSlots.value.some(s => s.day_of_week_id == day.id && s.time_slot_id_start == slot.id);
};

const getTimeSlotId = (day, slot) => {
    if (!scheduleSlots.value || scheduleSlots.value.length == 0) {
        return null;
    }
    const s = scheduleSlots.value.find(s => s.day_of_week_id == day.id && s.time_slot_id_start == slot.id);
    return s ? s.id : null;
};

const getSlotTimeRange = (day, slot) => {
    if (!scheduleSlots.value || scheduleSlots.value.length == 0) {
        return null;
    }
    const s = scheduleSlots.value.find(s => s.day_of_week_id == day.id && s.time_slot_id_start == slot.id);
    if (s) {
        return `${s.time_slot_start_name} - ${s.time_slot_end_name}`;
    }
    return null;
};

const handleDeleteTimeSlot = (id) => {
    selectedTimeSlotId.value = id;
    const slot = scheduleSlots.value.find(s => s.id === id);
    if (slot) {
        const dayName = weekDayOptions.value.find(day => day.id === slot.day_of_week_id)?.name;
        selectedTimeSlot.value = {
            ...slot,
            dayName,
            formattedTime: `${slot.time_slot_start_name} - ${slot.time_slot_end_name}`
        };
        showDeleteConfirmModal.value = true;
    }
};

const confirmDelete = () => {
    deleteTimeSlot(selectedTimeSlotId.value);
    showDeleteConfirmModal.value = false;
};

const handleEditTimeSlot = (id) => {
    console.log(id)
    const slot = scheduleSlots.value.find(s => s.id === id);
    if (slot) {
        selectedTimeSlotId.value = id;
        selectedTimeSlot.value = { ...slot };
        showEditTimeSlotModal.value = true;
    }
};

const updateTimeSlot = async () => {
    try {
        const response = await proxy.$api.apiPut(`me/schedule/${selectedTimeSlotId.value}`, {
            day_of_week_id: selectedTimeSlot.value.day_of_week_id,
            time_slot_id_start: selectedTimeSlot.value.time_slot_id_start,
            time_slot_id_end: selectedTimeSlot.value.time_slot_id_end,
        });

        if (response.data) {
            const updatedSlot = response.data;
            const timeSlotStart = timeSlotOptions.value.find(item => item.id == updatedSlot.time_slot_id_start);
            const timeSlotEnd = timeSlotOptions.value.find(item => item.id == updatedSlot.time_slot_id_end);

            const index = scheduleSlots.value.findIndex(s => s.id === selectedTimeSlotId.value);
            if (index !== -1) {
                scheduleSlots.value[index] = {
                    ...updatedSlot,
                    time_slot_start_name: timeSlotStart.name,
                    time_slot_end_name: timeSlotEnd.name
                };
            }
            proxy.$notification.success('Cập nhật khung giờ thành công!');
        }
        showEditTimeSlotModal.value = false;
    } catch (error) {
        proxy.$notification.error(error?.response?.data?.message || 'Cập nhật khung giờ thất bại!');
        console.error('Failed to update time slot:', error);
    }
};

const onAddSlot = (day, slot) => {
    newTimeSlot.value.day_of_week_id = day.id;
    newTimeSlot.value.time_slot_id_start = slot.id;
    newTimeSlot.value.time_slot_id_end = '';
    newTimeSlot.value.repeat_weekly = true;
    showAddTimeSlotModal.value = true;
};
</script>

<style scoped>
@import url('@css/profileNew.css');

.main-content {
    overflow: auto;
}

.schedule-main {
    width: 100%;
    height: 500px;
    overflow: auto;
    --time-width: 117px;
    --time-height: 117px;
}

.schedule-grid .list-days,
.schedule-grid .list-time {
    display: grid;
    grid-auto-flow: column;
    grid-template-columns: calc(var(--time-width) - 1rem) repeat(7, var(--time-width));
    min-width: max-content;
}

.schedule-grid .list-days .time-header {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: var(--font-size-base) !important;
}

.schedule-grid .list-days .day {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--gray-600);
    background: #f9fafb;
    border-right: 1px solid var(--gray-200);
    border-top: 1px solid var(--gray-200);
}

.schedule-grid .list-days .day {
    border-right: 1px solid var(--gray-200);
    border-right-color: var(--gray-200);
    border-right-width: 1px;
    padding: 1rem;
}

.schedule-grid .list-days .day:first-child {
    border-top-left-radius: 8px;
}

.schedule-grid .list-days .day:last-child {
    border-top-right-radius: 8px;
}

.schedule-grid .list-days .day:not(.time-header) span:first-child {
    font-size: var(--font-size-base);
    color: black;
    font-weight: 600;
}

.schedule-grid .list-days .day:not(.time-header) span:last-child {
    font-size: var(--font-size-mini);
    color: var(--gray-600);
}

.schedule-grid .list-time {
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    animation-duration: 200ms;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.schedule-grid .list-time:hover {
    background: #f9fafb80;
}

.schedule-grid .list-time:hover .add-new {
    opacity: 1;
}

.schedule-grid .time-header {
    background: #f9fafb80;
    border-left: 1px solid var(--gray-200);
}

.schedule-grid .list-time .time {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-right: 1px solid var(--gray-200);
    border-top: 1px solid var(--gray-200);
    height: calc(var(--time-height) - 1rem);
    font-size: 0.8rem;
    padding: 1rem;
    color: black;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-grid .list-time .add-new {
    border: 1.6px dashed var(--gray-400);
    border-radius: 0.7rem;
    background: transparent;
    padding: 0.5rem 0.7rem;
    color: var(--gray-400);
    opacity: 0;
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    animation-duration: 200ms;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.schedule-grid .list-time .add-new:hover {
    border: 1.6px dashed var(--color-primary-light);
    background: var(--color-primary-transparent);
}

.schedule-grid .list-time .content-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    border-radius: 10px;
    padding: 0.3rem 0.6rem;
    transition: all 0.3s ease;
    width: calc(var(--time-width) - 1rem);
    height: calc(var(--time-height) - 1.5rem);
    background: var(--color-primary-transparent);
}

.schedule-grid .list-time .content-card:not(.free-time) {
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    background: var(--color-primary-transparent);
}

.schedule-grid .list-time .free-time {
    border: 1.9px dashed var(--gray-400);
    background: var(--gray-50);
}
.schedule-grid .list-time .free-time .content-detail {
    display: flex;
    align-items: center;
    justify-content: center;
}

.schedule-grid .list-time .content-card .subject-wrapper {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-weight: 600;
    color: var(--color-primary);
}

.schedule-grid .list-time .content-card .subject-name, .user-name {
    display: inline-block;
    width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.schedule-grid .list-time .content-card .content-detail {
    color: var(--gray-700);
    font-size: var(--font-size-mini);
    display: flex;
    flex-wrap: wrap;
    gap: 0.2rem;
}

.schedule-grid .list-time .content-card .icon-wrapper {
    background: var(--color-primary-transparent);
    border-radius: 2rem;
    min-width: 1.5rem;
    min-height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary);
}

.current-week {
    padding: 1rem 0;
    border: 1px solid var(--gray-100);
    border-radius: 0.4rem;
    padding: 1rem;
}

.current-week-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-radius: 8px;
}

.current-week-header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.current-week-header-left-title {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.4rem;
    line-height: 1;
}

.current-week-header-left-title .current-week-title {
    font-size: var(--font-size-medium);
    font-weight: bold;
}

.current-week-header-left-title .sub-title {
    font-size: var(--font-size-base);
    color: var(--gray-600);
}

.status-wrapper {
    font-size: var(--font-size-mini);
    color: var(--color-primary);
    background: var(--blue-50);
    border: 1px solid var(--blue-200);
    border-radius: 2rem;
    padding: 0.2rem 1rem;
    font-weight: 600;
}

.slot-actions {
    opacity: 0;
    position: absolute;
    right: 10px;
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.schedule-grid .list-time .content-card .slot-actions:hover {
    opacity: 1;
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    animation-duration: 200ms;
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

.slot-actions button {
    background: #fffc;
    padding: 0.2rem 0.3rem;
    border-radius: 8px;
    border: none;
}

.selected-time-slot {
    border: 1px solid var(--gray-200);
    border-left-color: var(--color-primary);
    border-left-width: 4px;
    border-radius: 10px;
    padding: 1rem;
}

.time-slot-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #374151;
    font-size: var(--font-size-base);
}

/* .select-day {
    display: grid;
    gap: 0.7rem;
}
.select-day_header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.select-day_header-right {
    font-size: var(--font-size-heading-6);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.select-day_header-left {
    font-size: var(--font-size-small);
    color: var(--gray-500);
}
.select-day-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}
.select-day-grid .day-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 1rem 1rem;
    border: 1px solid var(--gray-200);
    border-radius: 0.6rem;
    cursor: pointer;
}
.select-day-grid .day-item.selected {
    background: var(--color-primary);
}
.select-day-grid .day-item.selected .title {
    color: white !important;
}
.select-day-grid .day-item.selected .sub-title {
    color: var(--gray-300) !important;
}

.select-day-grid .day-item .title {
    font-size: var(--font-size-small);
    color: var(--gray-700);
}
.select-day-grid .day-item .sub-title{
    font-size: var(--font-size-mini);
    color: var(--gray-500);
} */
</style>

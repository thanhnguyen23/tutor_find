<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { getCurrentInstance } from 'vue';
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

// Helper: so sánh thời gian dạng 'HH:mm'
// function compareTime(a, b) {
//     return a.localeCompare(b);
// }

// Lấy các slot đã có cho ngày đang chọn
const selectedDaySlots = computed(() => {
    if (!newTimeSlot.value.day_of_week_id) return [];
    return scheduleSlots.value.filter(
        slot => slot.day_of_week_id == newTimeSlot.value.day_of_week_id
    );
});

// Giờ bắt đầu: chỉ enable các mốc không nằm trong bất kỳ slot đã có
const validStartTimes = computed(() => {
    return timeSlotOptions.value.map(point => {
        const isOverlap = selectedDaySlots.value.some(slot => {
            const slotStart = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_start);
            const slotEnd = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_end);
            // Một mốc bị disable nếu nó nằm trong hoặc trùng với điểm cuối của slot đã có
            return (
                point.time >= slotStart.time &&
                point.time <= slotEnd.time
            );
        });
        return { ...point, disabled: isOverlap };
    });
});

// Giờ kết thúc: chỉ enable các mốc sau giờ bắt đầu, không giao với slot đã có
const validEndTimes = computed(() => {
    if (!newTimeSlot.value.time_slot_id_start) return timeSlotOptions.value.map(p => ({ ...p, disabled: true }));
    const startObj = timeSlotOptions.value.find(t => t.id === newTimeSlot.value.time_slot_id_start);
    return timeSlotOptions.value.map(point => {
        // Mốc kết thúc phải lớn hơn giờ bắt đầu đã chọn
        const afterStart = point.time > startObj.time;
        // Kiểm tra xem mốc kết thúc có nằm trong hoặc trùng với slot đã có không
        const isOverlap = selectedDaySlots.value.some(slot => {
             const slotStart = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_start);
             const slotEnd = timeSlotOptions.value.find(t => t.id == slot.time_slot_id_end);
            // Mốc kết thúc mới giao với slot cũ nếu:
            // (start mới < end cũ VÀ end mới > start cũ)
            return (
                 startObj.time < slotEnd.time && point.time > slotStart.time
            );
        });
        // Mốc kết thúc được enable nếu sau giờ bắt đầu VÀ không giao với slot đã có
        return { ...point, disabled: !(afterStart && !isOverlap) };
    });
});

const addTimeSlot = async () => {
    try {
        const response = await proxy.$api.apiPost('me/schedule', newTimeSlot.value);
        // Add the new time slot to the schedule
        if (response.data) {
            const newSlot = response.data;
            const timeSlotStart = timeSlotOptions.value.find(item => item.id == newSlot.time_slot_id_start);
            const timeSlotEnd = timeSlotOptions.value.find(item => item.id == newSlot.time_slot_id_end);
            scheduleSlots.value.push({
                ...newSlot,
                time_slot_start_name: timeSlotStart.name,
                time_slot_end_name: timeSlotEnd.name
            });
        }
        showAddTimeSlotModal.value = false;
        resetNewTimeSlot();
    } catch (error) {
        console.error('Failed to add time slot:', error);
    }
};

const deleteTimeSlot = async (id) => {
    try {
        await proxy.$api.apiDelete(`me/schedule/${id}`);
        scheduleSlots.value = scheduleSlots.value.filter(slot => slot.id !== id);
    } catch (error) {
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
        }
        showEditTimeSlotModal.value = false;
    } catch (error) {
        console.error('Failed to update time slot:', error);
    }
};

// Add computed properties for time overview statistics
const totalTimeSlots = computed(() => {
    return scheduleSlots.value?.length || 0;
});

const unavailableDays = computed(() => {
    if (!scheduleSlots.value || scheduleSlots.value.length == 0) return 7;
    const availableDays = new Set(scheduleSlots.value.map(slot => slot.day_of_week_id));
    return 7 - availableDays.size;
});

const popularTimeSlots = computed(() => {
    if (!scheduleSlots.value || scheduleSlots.value.length == 0) return [];

    // Group by time range
    const timeRangeGroups = {};

    scheduleSlots.value.forEach(slot => {
        const timeRange = `${slot.time_slot_start_name} - ${slot.time_slot_end_name}`;
        if (!timeRangeGroups[timeRange]) {
            timeRangeGroups[timeRange] = 0;
        }
        timeRangeGroups[timeRange]++;
    });

    const sortedTimeRanges = Object.entries(timeRangeGroups)
        .map(([timeRange, count]) => ({ timeRange, count }))
        .sort((a, b) => b.count - a.count);

    return sortedTimeRanges.slice(0, 3);
});
</script>

<template>
    <div class="schedule-section section">
        <div class="schedule-container">
            <div class="section-header">
                <div class="header-left">
                    <i class="far fa-clock"></i>
                    <h3>Lịch trình hàng tuần</h3>
                </div>
                <button class="btn-add" @click="showAddTimeSlotModal = true">
                    <i class="fas fa-plus"></i>
                    Thêm khung giờ
                </button>
            </div>
            <div class="schedule-grid">
                <div class="time-header">Thời gian</div>
                <div class="day-header" v-for="day in weekDayOptions" :key="day.id">
                    {{ day.name }}
                </div>

                <template v-for="slot in timeSlotOptions" :key="slot.id">
                    <div class="time-slot">
                        {{ slot.name }}
                    </div>
                    <div
                        v-for="day in weekDayOptions"
                        :key="day.id + slot.id"
                        class="schedule-cell"
                        :class="{ 'has-slot': hasTimeSlot(day, slot) }"
                    >
                        <div v-if="hasTimeSlot(day, slot)" class="slot-content">
                            <span>{{ getSlotTimeRange(day, slot) }}</span>
                            <div class="slot-actions">
                                <img src="/images/edit.svg" alt="edit" @click.stop="handleEditTimeSlot(getTimeSlotId(day, slot))" />
                                <img src="/images/delete.svg" alt="delete" @click.stop="handleDeleteTimeSlot(getTimeSlotId(day, slot))" />
                            </div>
                        </div>
                        <div v-else class="empty-slot">
                            ·
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Teaching Time Info -->
        <div class="teaching-time-group">
            <div class="time-info-container">
                <div class="section-header">
                    <div class="header-left">
                        <div class="logo-wrapper">
                            <i class="far fa-clock"></i>
                        </div>
                        <div class="title-wrapper">
                            <h3>Tổng quan thời gian</h3>
                            <p class="subtitle">Khung giờ thường xuyên dạy</p>
                        </div>
                    </div>
                </div>

                <div class="time-overview">
                    <div class="overview-item">
                        <div class="overview-label">Tổng số khung giờ:</div>
                        <div class="overview-value">{{ totalTimeSlots }} khung giờ</div>
                    </div>
                    <div class="overview-item">
                        <div class="overview-label">Ngày không rảnh:</div>
                        <div class="overview-value red">{{ unavailableDays }} ngày</div>
                    </div>
                </div>

                <div class="popular-times">
                    <h4>Khung giờ phổ biến:</h4>
                    <div class="time-slots-grid">
                        <div class="time-slot-row" v-for="slot in popularTimeSlots" :key="slot.timeRange">
                            <div class="time-range">{{ slot.timeRange }}</div>
                            <div class="days-count">{{ slot.count }} ngày</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <base-modal
            :is-open="showAddTimeSlotModal"
            title="Thêm khung giờ"
            description="Thêm khung giờ mới vào lịch trình hàng tuần của bạn."
            size="small"
            @close="showAddTimeSlotModal = false"
        >
            <div class="modal-edit">
                <div class="form-group">
                    <label>Ngày trong tuần</label>
                    <base-select
                        v-model="newTimeSlot.day_of_week_id"
                        :options="weekDayOptions"
                        placeholder="Chọn ngày trong tuần"
                    />
                </div>

                <div class="time-inputs">
                    <div class="form-group">
                        <label>Thời gian bắt đầu</label>
                        <base-select
                            v-model="newTimeSlot.time_slot_id_start"
                            :options="validStartTimes"
                            placeholder="Chọn giờ bắt đầu"
                        />
                    </div>

                    <div class="form-group">
                        <label>Thời gian kết thúc</label>
                        <base-select
                            v-model="newTimeSlot.time_slot_id_end"
                            :options="validEndTimes"
                            placeholder="Chọn giờ kết thúc"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showAddTimeSlotModal = false">Hủy</button>
                    <button class="btn-md btn-primary" @click="addTimeSlot">Thêm</button>
                </div>
            </div>
        </base-modal>

        <base-modal
            :is-open="showDeleteConfirmModal"
            title="Xác nhận xóa"
            description="Bạn có chắc chắn muốn xóa khung giờ này không?"
            size="small"
            @close="showDeleteConfirmModal = false"
        >
            <div class="delete-confirmation">
                <div class="selected-time-slot">
                    <div class="time-slot-preview">
                        <i class="far fa-calendar"></i>
                        <span>Thứ {{ selectedTimeSlot.day_of_week_id }}</span>
                        <i class="far fa-clock"></i>
                        <span>{{ selectedTimeSlot.formattedTime }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showDeleteConfirmModal = false">Hủy</button>
                    <button class="btn-md btn-primary delete" @click="confirmDelete">Xóa</button>
                </div>
            </div>
        </base-modal>

        <base-modal
            :is-open="showEditTimeSlotModal"
            title="Chỉnh sửa khung giờ"
            description="Chỉnh sửa thông tin khung giờ"
            size="small"
            @close="showEditTimeSlotModal = false"
        >
            <div class="modal-edit">
                <div class="form-group">
                    <label>Ngày trong tuần</label>
                    <base-select
                        v-model="selectedTimeSlot.day_of_week_id"
                        :options="weekDayOptions"
                        placeholder="Chọn ngày trong tuần"
                    />
                </div>

                <div class="time-inputs">
                    <div class="form-group">
                        <label>Thời gian bắt đầu</label>
                        <base-select
                            v-model="selectedTimeSlot.time_slot_id_start"
                            :options="timeSlotOptions"
                            placeholder="Chọn giờ bắt đầu"
                        />
                    </div>

                    <div class="form-group">
                        <label>Thời gian kết thúc</label>
                        <base-select
                            v-model="selectedTimeSlot.time_slot_id_end"
                            :options="endTimeOptions"
                            placeholder="Chọn giờ kết thúc"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showEditTimeSlotModal = false">Hủy</button>
                    <button class="btn-md btn-primary" @click="updateTimeSlot">Cập nhật</button>
                </div>
            </div>
        </base-modal>
    </div>
</template>

<style scoped>
@import url('@css/profile.css');
.schedule-section {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.teaching-time-group {
    display: grid;
    /* grid-template-columns: 2fr 2fr; */
    gap: 1.5rem;
}

.time-info-container {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    border: 1px solid #E5E7EB;
}

.time-info-container .section-header {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-left i {
    color: #374151;
    font-size: 1.25rem;
}

.header-left h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.logo-wrapper {
    width: 40px;
    height: 40px;
    background: #e3e3e5;
    border-radius: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-add {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border: 1px solid #D1D5DB;
    border-radius: 0.375rem;
    font-size: var(--font-size-mini);
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-add:hover {
    background: #F9FAFB;
    border-color: #9CA3AF;
}

.btn-add i {
    font-size: 0.75rem;
}

.schedule-grid {
    display: grid;
    grid-template-columns: 100px repeat(7, 1fr);
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    overflow: hidden;
}

.time-header, .day-header {
    padding: 1rem;
    text-align: center;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    font-size: var(--font-size-mini);
    border-bottom: 1px solid #E5E7EB;
    background: #F9FAFB;
}

.time-slot {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    font-size: var(--font-size-mini);
    font-weight: 600;
    color: #374151;
    text-align: center;
    border-bottom: 1px solid #E5E7EB;
}

.slot-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
}

.slot-actions img {
    width: 1rem;
    height: 1rem;
    cursor: pointer;
    opacity: 0.8;
}

.schedule-cell {
    padding: 0.35rem;
    min-height: 40px;
    border-bottom: 1px solid #E5E7EB;
    cursor: pointer;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.empty-slot {
    color: #D1D5DB;
    font-size: 1.5rem;
}

.slot-content {
    background: #EAEAEA;
    padding: 0.5rem;
    border-radius: 4px;
    text-align: center;
    font-size: 0.7rem;
    color: #374151;
    position: relative;
    width: max-content;
}

.edit-button {
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.edit-button i {
    color: #6B7280;
    font-size: 0.75rem;
}

.has-slot:hover .edit-button {
    opacity: 1;
}

.section-header .subtitle {
    color: #6B7280;
    font-size: var(--font-size-mini);
    margin: 0;
}

.time-overview {
    margin-bottom: 1.5rem;
}

.overview-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.overview-label {
    color: #374151;
    font-size: var(--font-size-small);
}

.overview-value {
    background: #F3F4F6;
    border: 1px solid #c6c6c6;
    padding: 0.20rem 0.75rem;
    border-radius: 1rem;
    font-size: var(--font-size-mini);
    font-weight: 600;
    color: #374151;
}

.overview-value.red {
    color: #DC2626;
    background: #fbf1f1;
    border: 1px solid #ffc6c6;
}

.popular-times h4 {
    color: #374151;
    font-size: var(--font-size-mini);
    font-weight: 600;
    margin-bottom: 1rem;
}

.time-slots-grid {
    display: grid;
    gap: 0.75rem;
}

.time-slot-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgb(251 251 252);
    padding:.75rem 1rem;
    border-radius: 0.4rem;
}

.time-range {
    color: #374151;
    font-size: var(--font-size-mini);
}

.days-count {
    font-weight: 600;
    font-size: var(--font-size-mini);
    color: #374151;
}

/* Modal Styles */
.modal-edit {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #111827;
}

.form-select {
    width: 100%;
    padding: 0.625rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: var(--font-size-mini);
    color: #111827;
    background-color: white;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.5rem center;
    background-size: 1.5em 1.5em;
}

.time-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.selected-time-slot {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.time-slot-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #374151;
    font-size: var(--font-size-small);
}

.time-slot-preview i {
    color: #6B7280;
}
</style>



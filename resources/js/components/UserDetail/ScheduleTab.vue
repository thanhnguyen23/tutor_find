<template>
    <div class="content-section">
        <div class="content-card">
            <div class="schedule-header">
                <h3 class="section-title">Lịch dạy</h3>
                <button class="btn-md btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    Múi giờ của bạn
                </button>
            </div>

            <!-- Lịch hàng tuần -->
            <div class="weekly-schedule">
                <h4>Lịch hàng tuần</h4>
                <div class="schedule-grid">
                    <div class="schedule-day" v-for="day in groupedTimeSlots" :key="day.day_of_week_id">
                        <div class="day-name">{{ day.day_of_week }}</div>
                        <div class="time-slots-list">
                            <div
                                v-for="(timeSlot, index) in day.time_slots"
                                :key="index"
                                class="day-hours"
                            >
                                {{ timeSlot.time_slot_start_name }} - {{ timeSlot.time_slot_end_name }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đặt lịch học -->
            <div class="booking-schedule">
                <h4>Đặt lịch học</h4>
                <div class="time-slots">
                    <h5>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Khung giờ có sẵn
                    </h5>
                    <div class="time-slots-grid">
                        <button
                            v-for="slot in timeSlots"
                            :key="slot"
                            class="btn-md btn-secondary"
                        >
                            {{ slot }}
                        </button>
                    </div>
                    <p class="time-note">Tất cả thời gian đều theo múi giờ của bạn</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, getCurrentInstance } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const {proxy} = getCurrentInstance();

const groupedTimeSlots = computed(() => {
    if (!props.user.user_weekly_time_slots) return [];

    // Group time slots by day_of_week_id
    const grouped = props.user.user_weekly_time_slots.reduce((acc, slot) => {
        const dayId = slot.day_of_week_id;
        if (!acc[dayId]) {
            acc[dayId] = {
                day_of_week_id: dayId,
                day_of_week: slot.day_of_week || '',
                time_slots: []
            };
        }
        acc[dayId].time_slots.push({
            time_slot_start_name: slot.time_slot_start_name,
            time_slot_end_name: slot.time_slot_end_name
        });
        return acc;
    }, {});

    // Convert to array and sort by day_of_week_id
    return Object.values(grouped).sort((a, b) => a.day_of_week_id - b.day_of_week_id);
});

const timeSlots = computed(() => {
    if (!props.user.user_weekly_time_slots) return [];

    // Flatten all time slots from all days
    return props.user.user_weekly_time_slots.map(slot => {
        return `${slot.time_slot_start_name} - ${slot.time_slot_end_name}`;
    });
});
</script>

<style scoped>
@import url('@css/UserDetail.css');

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.btn-md {
    padding: 0.75rem 1rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    background: white;
    color: #18181b;
    font-size: var(--font-size-small);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-md:hover {
    background: #f4f4f5;
}

.btn-md svg {
    color: #71717a;
}

.weekly-schedule {
    margin-bottom: 2rem;
}

.weekly-schedule h4 {
    font-size: var(--font-size-small);
    font-weight: 600;
    color: #18181b;
    margin-bottom: 1rem;
}

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.schedule-day {
    padding: 1rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    background: white;
    min-height: 100px;
}

.day-name {
    font-weight: 500;
    color: #18181b;
    margin-bottom: 0.3rem;
}

.time-slots-list {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}

.day-hours {
    color: #71717a;
    font-size: var(--font-size-small);
}

.booking-schedule h4 {
    font-size: var(--font-size-small);
    font-weight: 600;
    color: #18181b;
    margin-bottom: 1rem;
}

.time-slots h5 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: var(--font-size-small);
    font-weight: 500;
    color: #18181b;
    margin-bottom: 1rem;
}

.time-slots h5 svg {
    color: #71717a;
}

.time-slots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.time-note {
    color: #71717a;
    font-size: var(--font-size-small);
    text-align: center;
}
</style>

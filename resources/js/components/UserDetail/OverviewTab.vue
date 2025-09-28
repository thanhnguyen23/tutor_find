<template>
    <div class="content-section">
        <div class="content-card">
            <h3 class="section-title">Về {{ user.full_name }}</h3>
            <p class="section-paragraph">
                {{ user.about_you }}
            </p>
        </div>

        <!-- Học vấn -->
        <div class="content-card">
            <h3 class="section-title">
                <div class="icon-wrapper">
                    <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                Học vấn
            </h3>
            <div class="timeline">
                <div class="timeline-item" v-for="edu in user.user_educations" :key="edu.id">
                    <div class="timeline-bullet"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">{{ edu.time }}</div>
                        <div class="timeline-title">{{ edu.school_name }} ({{ edu.major }})</div>
                        <div class="timeline-subtitle">{{ edu.description }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kinh nghiệm làm việc -->
        <div class="content-card">
            <h3 class="section-title">
                <div class="icon-wrapper">
                    <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                Kinh nghiệm
            </h3>
            <div class="timeline">
                <div class="timeline-item" v-for="exp in user.user_experiences" :key="exp.id">
                    <div class="timeline-bullet"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">{{ exp.time }}</div>
                        <div class="timeline-title">{{ exp.name }} ({{ exp.position }})</div>
                        <div class="timeline-subtitle">{{ exp.description }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- <div class="content-card">
            <h3 class="section-title">
                <div class="icon-wrapper">
                    <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                </div>
                Ngôn ngữ
            </h3>
            <div class="language-tag" v-for="item in user.user_languages" :key="item.id">
                <span :class="['flag-icon border-sm', `flag-icon-${item.emoji}`]"></span>
                {{ item.language }}
            </div>
        </div> -->

        <!-- <div class="content-card">
            <h3 class="section-title">Phương pháp giảng dạy</h3>
            <div class="teaching-methods">
                <div class="method-item" v-for="(method, index) in teachingMethods" :key="index">
                    <div class="method-check">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><path d="m9 11 3 3L22 4"></path></svg>
                    </div>
                    <div class="method-text">{{ method }}</div>
                </div>
            </div>
        </div> -->

        <!-- Lịch dạy -->
        <div class="content-card">
            <h3 class="section-title">
                <div class="icon-wrapper">
                    <svg  class="icon-lg"xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <span>Lịch hàng tuần</span>
            </h3>

            <!-- Lịch hàng tuần -->
            <div class="weekly-schedule">

                <!-- Desktop / Tablet: columns by day with available times -->
                <div class="schedule-desktop">
                    <div class="columns">
                        <div class="day-column" v-for="day in weekDayOptions" :key="'d-' + day.id">
                            <div class="day-header">{{ day.name }}</div>
                            <div class="time-list">
                                <template v-for="slot in timeSlotOptions" :key="'t-' + day.id + '-' + (slot ? slot.id : '')">
                                    <div
                                        class="time-item"
                                        v-if="slot && hasTimeSlot(day.id, slot.id)"
                                    >
                                        {{ slot.name }}
                                    </div>
                                </template>
                                <div class="time-empty" v-if="!(timeSlotOptions || []).some(s => s && hasTimeSlot(day.id, s.id))">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile: day tabs + list of available times -->
                <div class="schedule-mobile">
                    <div class="mobile-day-tabs">
                        <button
                            v-for="day in weekDayOptions"
                            :key="'m-' + day.id"
                            class="day-tab"
                            :class="{ active: day.id === selectedMobileDayId }"
                            @click="selectedMobileDayId = day.id"
                        >
                            {{ day.name }}
                        </button>
                    </div>
                    <div class="mobile-time-list">
                        <button
                            class="mobile-time-chip"
                            v-for="slot in availableSlotsForDay(selectedMobileDayId)"
                            :key="'m-av-' + selectedMobileDayId + '-' + slot.id"
                        >
                            {{ slot.name }}
                        </button>
                        <div class="mobile-empty" v-if="availableSlotsForDay(selectedMobileDayId).length === 0">Không có khung giờ</div>
                    </div>
                </div>
            </div>
        </div>

        <button class="btn-lg btn-secondary">Xem thêm</button>
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance, computed, watch } from 'vue';
import { useStore } from 'vuex';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const store = useStore();

const weekDayOptions = computed(() => store.state.configuration.dayOfWeeks || []);
const timeSlotOptions = computed(() => store.state.configuration.timeSlots || []);

const hasTimeSlot = (dayId, slotId) => {
    const slots = props.user?.user_weekly_time_slots || [];
    return slots.some(s => s.day_of_week_id == dayId && s.time_slot_id == slotId);
};

// Mobile state: default to first day
const selectedMobileDayId = ref(weekDayOptions.value?.[0]?.id || 1);

// Helpers
const availableSlotsForDay = (dayId) => {
    const list = Array.isArray(timeSlotOptions.value) ? timeSlotOptions.value : [];
    return list.filter((slot) => slot && hasTimeSlot(dayId, slot.id));
};

</script>

<style scoped>
@import url('@css/UserDetail.css');
.section-paragraph {
    color: #71717a;
    line-height: 1.6;
    margin-bottom: 0;
}

.teaching-methods {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.method-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.method-check {
    background-color: #18181b1a;
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 100%;
}

.method-check svg {
    width: 1rem;
    height: 1rem;
}

.method-text {
    color: #71717a;
}

.timeline {
    position: relative;
    padding-left: 4rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 2rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-bullet {
    position: absolute;
    left: -2rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    background: #18181b;
    transform: translateX(-50%);
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -2rem;
    top: 0.75rem;
    width: 1px;
    height: calc(100% - 0.75rem);
    background: #e4e4e7;
    transform: translateX(-50%);
}

.timeline-content {
    position: relative;
}

.timeline-year {
    font-size: 0.875rem;
    color: #71717a;
    margin-bottom: 0.25rem;
}

.timeline-title {
    font-weight: 600;
    color: #18181b;
    margin-bottom: 0.25rem;
}

.timeline-subtitle {
    color: #71717a;
    font-size: 0.875rem;
}

/* Schedule styles */
.weekly-schedule {
    margin-bottom: 2rem;
}

/* desktop columns */
.schedule-desktop .columns {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 24px;
}
.schedule-desktop .day-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    border-top: 6px solid var(--color-primary);
    border-radius: 0.5rem;
    padding-top: 0.5rem;
}
.schedule-desktop .day-header {
    font-weight: 500;
    color: var(--gray-700);
    text-align: center;
}
.schedule-desktop .time-list {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
}
.schedule-desktop .time-item {
    color: #111827;
    text-decoration: underline;
    text-underline-offset: 2px;
    cursor: default;
    font-size: var(--font-size-small);
}
.schedule-desktop .time-empty { color: #9ca3af; text-align: center; padding: 4px 0; }

/* remove old grid table styles */

.availability-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    border-radius: 14px;
    padding: 15px 15px;
    color: black;
    background: none;
    font-weight: 600;
    font-size: var(--font-size-mini);
    /* border: 1px solid var(--gray-400); */
}
.availability-badge .icon-dot {
    background: #009966;
    width: 0.8rem;
    height: 0.8rem;
    border-radius: 100%;
}
.availability-badge.free {
    color: #16a34a;
    background: #ebfff2;
}
.availability-badge.busy {
    color: #ef4444;
    background: #ffeef0;
}

/* Mobile layout */
.schedule-mobile { display: none; }
.schedule-mobile .mobile-day-tabs {
    position: sticky;
    top: 0;
    z-index: 2;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding: 8px 4px 10px 4px;
    background: #ffffffea;
    backdrop-filter: blur(2px);
}
.schedule-mobile .day-tab {
    border: 1px solid #e5e7eb;
    background: #fff;
    border-radius: 5px;
    padding: 8px 14px;
    font-weight: 600;
    color: #374151;
    font-size: var(--font-size-small);
    white-space: nowrap;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.schedule-mobile .day-tab.active {
    background: #111827;
    color: #fff;
    border-color: #111827;
}
.schedule-mobile .mobile-time-list { display: grid; gap: 10px; padding: 8px 2px; }
.schedule-mobile .mobile-time-chip {
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #111827;
    padding: 8px 12px;
    border-radius: 10px;
    text-align: left;
}
.schedule-mobile .mobile-empty { color: #9ca3af; padding: 6px 2px; }

@media (max-width: 768px) {
    .schedule-desktop { display: none; }
    .schedule-mobile { display: grid; gap: 0.7rem; }
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

@media (max-width: 1024px) {
}

@media (max-width: 868px) {
}

@media (max-width: 640px) {
    .schedule-grid {
        grid-template-columns: 1fr 1fr;
    }

    .time-slots-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>



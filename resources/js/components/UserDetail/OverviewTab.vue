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
                <span>Lịch học</span>
            </h3>

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

        <button class="btn-lg btn-secondary">Xem thêm</button>
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance, computed, watch } from 'vue';

const teachingMethods = [
    'Interactive and engaging lessons',
    'Real-world examples and applications',
    'Homework and practice materials provided',
    'Personalized learning plans',
    'Regular progress assessments'
];

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const selectedDate = ref(null);

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

.weekly-schedule h4 {
    font-size: var(--font-size-small);
    font-weight: 600;
    color: #18181b;
    margin-bottom: 1rem;
}

.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    gap: 1rem;
}

.schedule-day {
    padding: 1rem;
    border: 1px solid #e4e4e7;
    border-radius: 0.5rem;
    background: white;
}

.day-name {
    font-weight: 500;
    color: #18181b;
    margin-bottom: 0.3rem;
    font-size: var(--font-size-small);
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

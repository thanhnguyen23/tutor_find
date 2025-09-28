<template>
<div class="date-picker">
    <div class="header">
        <button @click="prevWeek" :disabled="currentWeekIndex === 0">‹</button>
        <span>{{ weekRangeLabel }}</span>
        <button @click="nextWeek" :disabled="currentWeekIndex === weeks.length - 1">›</button>
    </div>
    <div class="days">
        <div v-for="(day, idx) in weeks[currentWeekIndex]" :key="day.date" :class="['day', { selected: isSelected(day.date), today: isToday(day.date), disabled: isPastDate(day.date) }]" @click="!isPastDate(day.date) && selectDate(day.date)">
            <div class="day-name" :class="{ selected: isSelected(day.date) }">{{ day.shortDay }}</div>
            <div class="day-number" :class="{ selected: isSelected(day.date) }">{{ day.day }}</div>
        </div>
    </div>
</div>
</template>

<script setup>
import {
    ref,
    computed,
    watch
} from 'vue'

const props = defineProps({
    modelValue: String, // YYYY-MM-DD
})
const emit = defineEmits(['update:modelValue'])

const today = new Date()

// Helper: format date thành YYYY-MM-DD (local)
function toLocalISODate(d) {
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`
}

// Helper: đầu tuần (Chủ Nhật)
function startOfWeek(date) {
    const d = new Date(date)
    d.setDate(d.getDate() - d.getDay())
    d.setHours(0, 0, 0, 0)
    return d
}

// Lấy 7 ngày từ đầu tuần
function getWeekDays(startDate) {
    const days = []
    for (let i = 0; i < 7; i++) {
        const d = new Date(startDate)
        d.setDate(d.getDate() + i)
        days.push({
            date: toLocalISODate(d), // YYYY-MM-DD local
            day: d.getDate(),
            shortDay: d.toLocaleDateString(undefined, {
                weekday: 'short'
            }), // ngôn ngữ theo browser
        })
    }
    return days
}

const weeks = ref([])
const currentWeekIndex = ref(0)

function initWeeks() {
    const firstWeekStart = startOfWeek(today)
    const secondWeekStart = new Date(firstWeekStart)
    secondWeekStart.setDate(secondWeekStart.getDate() + 7)
    weeks.value = [getWeekDays(firstWeekStart), getWeekDays(secondWeekStart)]
}
initWeeks()

// Label hiển thị khoảng tuần
const weekRangeLabel = computed(() => {
    const week = weeks.value[currentWeekIndex.value]
    if (!week) return ''
    const start = new Date(week[0].date)
    const end = new Date(week[6].date)
    return `${start.toLocaleDateString(undefined, { month: 'short', day: 'numeric' })} – ${end.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })}`
})

function prevWeek() {
    if (currentWeekIndex.value > 0) currentWeekIndex.value--
}

function nextWeek() {
    if (currentWeekIndex.value < weeks.value.length - 1) currentWeekIndex.value++
}

const selectedDate = ref(props.modelValue || '')

function selectDate(date) {
    selectedDate.value = date
    emit('update:modelValue', date) // YYYY-MM-DD
}

function isSelected(date) {
    return selectedDate.value === date
}

function isToday(date) {
    return date === toLocalISODate(today)
}

function isPastDate(date) {
    const dateObj = new Date(date)
    const todayObj = new Date(today)
    todayObj.setHours(0, 0, 0, 0)
    dateObj.setHours(0, 0, 0, 0)
    return dateObj < todayObj
}

watch(
    () => props.modelValue,
    (val) => {
        selectedDate.value = val
    }
)
</script>

<style scoped>
.date-picker {
    display: grid;
    gap: 1.7rem;
    /* border: 1px solid #eee;
    border-radius: 8px;
    background: #fff;
    font-family: inherit; */
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    /* padding: 12px 16px; */
    font-weight: 600;
    font-size: 16px;
}

.header button {
    background: #f5f5f5;
    border: none;
    border-radius: 6px;
    width: 36px;
    height: 36px;
    font-size: var(--font-size-meidum);
    cursor: pointer;
    color: #888;
}

.header button:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.days {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    /* padding: 0 8px 12px 8px; */
}

.day {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 50px;
    cursor: pointer;
    border-radius: 8px;
    padding: 4px 0;
    transition: background 0.2s;
}

.day-name {
    font-size: 13px;
    color: #888;
    margin-bottom: 2px;
}

.day-name.selected {
    color: var(--color-primary);
    font-weight: bold;
}

.day-number {
    font-size: var(--font-size-base);
    font-weight: 500;
    color: #222;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.4rem 0.8rem;
}

.day-number.selected {
    background: var(--color-primary-transparent);
    color: var(--color-primary);
    ;
    border: 1.5px solid var(--color-primary);
    ;
}

.day.today .day-number {
    border: 1.5px solid #888;
}

.day.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.day.disabled .day-number {
    color: #ccc;
    background: #f9f9f9;
}

.day.disabled .day-name {
    color: #ccc;
}

@media (max-width: 868px) {
    .header {
        font-size: var(--font-size-base);
    }

    .day-number {
        font-size: var(--font-size-small);
    }
}
</style>

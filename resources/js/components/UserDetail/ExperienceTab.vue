<template>
    <div class="content-section">
        <!-- Học vấn -->
        <div class="content-card">
            <h3 class="section-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-md"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
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
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance, computed, watch } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});
const education = [
    {
        year: '2015',
        degree: 'Ph.D. in Applied Mathematics',
        school: 'Vietnam National University'
    },
    {
        year: '2010',
        degree: 'M.Sc. in Physics',
        school: 'Hanoi University of Science'
    },
    {
        year: '2008',
        degree: 'B.Sc. in Mathematics',
        school: 'Hanoi University of Science'
    }
];

const experience = [
    {
        period: '2015 - Present',
        position: 'Senior Lecturer',
        company: 'Vietnam National University'
    },
    {
        period: '2010 - 2015',
        position: 'Research Assistant',
        company: 'Institute of Mathematics'
    }
];
</script>

<style scoped>
@import url('@css/UserDetail.css');

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
</style>

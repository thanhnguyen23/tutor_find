<template>
    <div class="booking-page">
        <div class="container">
            <div class="lesson-stepper">
                <div class="step-item" :class="{ active: currentStep === 1 }">
                    <div class="step-circle">
                        <svg class="step-icon icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path data-v-6304ec4f="" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path data-v-6304ec4f="" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                    </div>
                    <div class="step-info">
                        <div class="step-label">Bước 1</div>
                        <div class="step-title">Thông tin buổi học</div>
                    </div>
                </div>
                <div class="step-item" :class="{ active: currentStep === 2 }">
                    <div class="step-circle">
                        <svg class="step-icon icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                    </div>
                    <div class="step-info">
                        <div class="step-label">Bước 2</div>
                        <div class="step-title">Chọn gói học</div>
                    </div>
                </div>
                <div class="step-item" :class="{ active: currentStep === 3 }">
                    <div class="step-circle">
                        <svg class="step-icon icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <div class="step-info">
                        <div class="step-label">Bước 3</div>
                        <div class="step-title">Xác nhận đặt lịch</div>
                    </div>
                </div>
            </div>
        </div>

        <LessonInformation v-if="currentStep === 1 && Object.keys(tutorInfo).length > 0" @next="nextStep" :tutor-info="tutorInfo" />
        <PackageSelection v-if="currentStep === 2 && Object.keys(tutorInfo).length > 0" @back="prevStep" @next="nextStep" :tutor-info="tutorInfo" />
        <ConfirmBooking v-if="currentStep === 3 && Object.keys(tutorInfo).length > 0" @back="prevStep" @submit="submitBooking" :tutor-info="tutorInfo" />
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { useRoute } from 'vue-router'
import LessonInformation from '@/components/Booking/LessonInformation.vue'
import PackageSelection from '@/components/Booking/PackageSelection.vue'
import ConfirmBooking from '@/components/Booking/ConfirmBooking.vue'

const { proxy } = getCurrentInstance();
const route = useRoute();
const currentStep = ref(1);
const tutorInfo = ref({});

const fetchTutorInfo = async () => {
    try {
        const tutorUid = route.params.uid
        const response = await proxy.$api.apiGet(`tutor/${tutorUid}`)
        tutorInfo.value = response
    } catch (error) {
        console.log('Error fetching tutor data', error)
    }
}

const nextStep = () => {
    if (currentStep.value < 3) {
        currentStep.value++

        saveCurrentStep();
    }
}

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--

        saveCurrentStep();
    }
}

const saveCurrentStep = () => {
    const bookingData = JSON.parse(sessionStorage.getItem('bookingData'));

    const bookingDataNew = {
        ...bookingData,
        currentStep: currentStep.value,
    }

    sessionStorage.setItem('bookingData', JSON.stringify(bookingDataNew));
}

const getCurrentStep = () => {
    const bookingData = JSON.parse(sessionStorage.getItem('bookingData'));

    currentStep.value = bookingData?.currentStep ?? 1;
}

const submitBooking = () => {
    // Handle booking submission
    console.log('Booking submitted')
}

onMounted(() => {
    fetchTutorInfo();
    getCurrentStep();
})
</script>

<style scoped>
.booking-page {
    min-height: 100vh;
    background-color: #f9fafb;
    padding: 2rem 0;
}

.lesson-stepper {
    display: flex;
    align-items: flex-start;
    gap: 8vw;
    background-image: linear-gradient(171deg, #eaeaea, transparent);
    padding: 1rem 1rem;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 8px;
    justify-content: flex-start;
}
.step-item {
    display: flex;
    align-items: center;
    gap: 1.1rem;
    width: 100%;
}
.step-item:nth-child(2) {
    justify-content: center !important;
}
.step-item:last-child {
    justify-content: end !important;
}
.step-circle {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    border: 2px solid #e5e7eb;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: border 0.2s;
}
.step-icon {
    color: #a1a1aa;
}
.step-info {
    display: flex;
    flex-direction: column;
    gap: 0.1rem;
}
.step-label {
    font-size: var(--font-size-base);
    color: #6b7280;
    font-weight: 400;
}
.step-title {
    font-size: var(--font-size-heading-6);
    font-weight: 600;
    color: #6b7280;
    margin-top: -2px;
}
.step-item.active .step-circle {
    border: 2px solid #222;
}
.step-item.active .step-icon {
    color: #222;
}
.step-item.active .step-title {
    color: #222;
}

@media (max-width: 868px) {
    .step-title {
        display: none;
    }

    .step-circle {
        margin: auto;
    }
    .step-item {
        display: grid;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        text-align: center;
        justify-content: center;
    }

    .step-item:last-child {
        justify-content: center !important;
    }
}
</style>

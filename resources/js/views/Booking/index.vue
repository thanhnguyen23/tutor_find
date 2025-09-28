<template>
    <div>
        <!-- Loading overlay -->
        <base-loading v-if="loading" />

        <!-- Special Offer Modal -->
        <base-modal v-if="showSpecialOfferModal && !loading" :is-open="showSpecialOfferModal" @close="closeSpecialOfferModal" size="small" :header="false">
            <div class="special-offer-header">
                <h3 class="header-title">🎉 Ưu đãi đặc biệt</h3>
                <button class="close-btn" @click="closeSpecialOfferModal">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>

            <div class="special-offer-content">
                <div class="gift-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="8" width="18" height="4" rx="1"/>
                        <path d="M12 8v13"/>
                        <path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"/>
                        <path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"/>
                    </svg>
                </div>
                <h2 class="main-title">Buổi học thử đầu tiên hoàn toàn miễn phí!</h2>
                <p class="description">Trải nghiệm chất lượng giảng dạy mà không mất phí. Bạn có muốn tiếp tục với buổi học thử không?</p>
            </div>

            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="chooseRegularBooking">
                    Không, đặt lịch thường
                </button>
                <button class="btn-md btn-black" @click="chooseTrialBooking">
                    Có, học thử miễn phí
                </button>
            </div>
        </base-modal>


        <RealBooking v-if="!loading && selectedMode" :tutor-info="tutorInfo" :selected-mode="selectedMode" :current-user="currentUser" />
    </div>
</template>

<script setup>
import { ref, onMounted, getCurrentInstance, computed } from 'vue'
import { useRoute } from 'vue-router'
import TrialBooking from '@/views/booking/TrialBooking.vue'
import RealBooking from '@/views/booking/RealBooking.vue'
import { useStore } from 'vuex'

const { proxy } = getCurrentInstance();
const route = useRoute();
const store = useStore();

const loading = ref(true);
const tutorInfo = ref({});
const showSpecialOfferModal = ref(true);
const selectedMode = ref(null);
const currentUser = ref(null);

const fetchCurrentUser = async () => {
    try {
        const res = await proxy.$api.apiGet('me');
        currentUser.value = res?.data || res || null;
    } catch (e) {
        currentUser.value = null;
    }
}

const fetchTutorInfoAndInit = async () => {
    try {
        const tutorUid = route.params.uid
        const tutor = await proxy.$api.apiGet(`tutor/${tutorUid}`)
        tutorInfo.value = tutor

        const bookingData = JSON.parse(sessionStorage.getItem('bookingData')) || {}

        // Determine eligibility for free trial based on remaining quota
        const hasFreeTrial = (currentUser.value?.free_study || 0) > 0

        if (hasFreeTrial) {
            // If user has previously chosen a mode, respect it; otherwise show special offer
            if (bookingData.mode === 'trial' || bookingData.mode === 'official') {
                selectedMode.value = bookingData.mode
                showSpecialOfferModal.value = false
            } else {
                showSpecialOfferModal.value = true
            }
        } else {
            selectedMode.value = 'official'
            showSpecialOfferModal.value = false
        }
    } catch (e) {
        selectedMode.value = 'official'
        showSpecialOfferModal.value = false
    } finally {
        loading.value = false
    }
}

const chooseTrialBooking = () => {
    selectedMode.value = 'trial'
    showSpecialOfferModal.value = false
    const bookingData = JSON.parse(sessionStorage.getItem('bookingData')) || {}
    bookingData.mode = 'trial'
    bookingData.currentStep = 1
    sessionStorage.setItem('bookingData', JSON.stringify(bookingData))
}

const chooseRegularBooking = () => {
    selectedMode.value = 'official'
    showSpecialOfferModal.value = false
    const bookingData = JSON.parse(sessionStorage.getItem('bookingData')) || {}
    bookingData.mode = 'official'
    bookingData.currentStep = 1
    sessionStorage.setItem('bookingData', JSON.stringify(bookingData))
}

const closeSpecialOfferModal = () => {
    // If closed without choice, default to official
    if (!selectedMode.value) chooseRegularBooking()
}

onMounted(async () => {
    await fetchCurrentUser();
    await fetchTutorInfoAndInit();
})
</script>

<style scoped>
/* Special Offer Modal Styles */
.special-offer-header {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 1.5rem 0 0 0;
}

.header-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.close-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border: none;
    background: none;
    color: #6b7280;
    cursor: pointer;
    border-radius: 0.375rem;
    transition: all 0.2s;
    position: absolute;
    right: 1.5rem;
}

.close-btn:hover {
    background-color: #f3f4f6;
    color: #374151;
}

.special-offer-content {
    background-color: #f9fafb;
    border-radius: 0.75rem;
    padding: 2rem;
    text-align: center;
}

.gift-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    color: #1f2937;
}

.main-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 1rem 0;
    line-height: 1.3;
}

.description {
    font-size: 0.875rem;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
}
</style>


<script setup>
import { ref, onMounted, onUnmounted, getCurrentInstance, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import OverviewTab from '@/components/UserDetail/OverviewTab.vue';
import ExperienceTab from '@/components/UserDetail/ExperienceTab.vue';
import ReviewsTab from '@/components/UserDetail/ReviewsTab.vue';
import PricingTab from '@/components/UserDetail/PricingTab.vue';
import { useStore } from 'vuex';
import SendMessage from '../components/SendMessage.vue';

const { proxy } = getCurrentInstance();
const route = useRoute();
const router = useRouter();
const user = ref({});
const activeTab = ref('overview');
const store = useStore();
const showSendMessageModal = ref(false);
const selectedUser = ref(null);
const myUid = computed(() => store.state.auth?.user?.uid || localStorage.getItem('uid'));

const tabs = [
    { id: 'overview', name: 'Giới thiệu', icon: 'fa-regular fa-user' },
    { id: 'pricing', name: 'Học phí', icon: 'fa-regular fa-money-bill' },
    { id: 'reviews', name: 'Đánh giá', icon: 'fa-regular fa-comment' },
];

const getTwoNameSubject = computed(() => {
    if (user.value.user_subjects.length > 2) {
        return user.value.user_subjects.slice(0, 2).map(item => item.subject_name).join(' & ');
    }
    return user.value.user_subjects.map(item => item.subject_name).join(' & ');
});

const fetchUser = async () => {
    try {
        const userUid = route.params.uid;
        const response = await proxy.$api.apiGet(`tutor/${userUid}`);
        user.value = response;
    } catch (error) {
        console.log('Error fetching user data', error);
    }
};

const getAllNameLanguage = computed(() => {
    return user.value.user_languages.map(lang => lang.language).join(', ');
});

const redirectToBooking = () => {
    return router.push(`/booking/${route.params.uid}`);
}

const openSendMessageModal = () => {
    selectedUser.value = user.value;
    showSendMessageModal.value = true;
};

const handleMessageSent = () => {
    showSendMessageModal.value = false;
    proxy.$notification.success('Gửi tin nhắn thành công!');
};

onMounted(() => {
    store.dispatch('updateHiddenFooter', true);

    fetchUser();
});
onUnmounted(() => {
    store.dispatch('updateHiddenFooter', false);
});
</script>

<template>
<div class="user-detail-header_mobile" v-if="Object.keys(user).length > 0">
    <div class="container">
        <div class="avatar-wrapper_mobile">
            <img :src="user.avatar || '/images/default-avatar.png'" />
        </div>
        <h1 class="user-name_mobile">
            <span>{{ user.full_name }}</span>
            <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                <g id="Stars 3">
                <path id="Vector" fill-rule="evenodd" clip-rule="evenodd" d="M14.1027 3.76073C13.3007 3.50073 12.6673 2.86873 12.408 2.06806C12.3193 1.79339 11.8627 1.79339 11.774 2.06806C11.5147 2.86873 10.8813 3.50073 10.0793 3.76073C9.942 3.80539 9.84866 3.93339 9.84866 4.07806C9.84866 4.22206 9.942 4.35006 10.0793 4.39473C10.88 4.65406 11.5133 5.28939 11.774 6.09406C11.818 6.23139 11.9467 6.32473 12.0907 6.32473C12.2353 6.32473 12.364 6.23139 12.408 6.09406C12.6687 5.28939 13.302 4.65406 14.1027 4.39473C14.24 4.35006 14.3333 4.22206 14.3333 4.07806C14.3333 3.93339 14.24 3.80539 14.1027 3.76073Z" fill="#5D5DEC"/>
                <path id="Vector_2" fill-rule="evenodd" clip-rule="evenodd" d="M11.716 8.26755L8.56066 7.24288L7.53666 4.08818C7.44866 3.81485 7.20466 3.63818 6.91666 3.63818C6.62799 3.63818 6.38466 3.81485 6.29599 4.08818L5.28066 7.24088L2.11932 8.26688C1.84866 8.35288 1.66666 8.60222 1.66666 8.88822C1.66666 9.17355 1.84866 9.42288 2.11799 9.50822L5.27066 10.5275L6.29599 13.6876C6.38466 13.9609 6.62799 14.1382 6.91666 14.1382C7.20466 14.1382 7.44866 13.9609 7.53666 13.6876L8.55332 10.5349L11.714 9.50888C11.9847 9.42288 12.1673 9.17355 12.1673 8.88822C12.1673 8.60222 11.9847 8.35288 11.716 8.26755Z" fill="#5D5DEC"/>
                </g>
            </svg>
        </h1>

        <span class="user-address_mobile">{{ user.provinces_name }}, {{ user.districts_name }}</span>

        <div class="header-actions_mobile">
            <button class="btn-sm btn-secondary">
                <i class="fa-regular fa-heart"></i>
            </button>
            <button class="btn-sm btn-secondary">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
            </button>
        </div>

        <div class="user-detail-content">
            <div class="language-tags">
                <span class="language-tag">
                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                    {{ getAllNameLanguage }}
                </span>
            </div>

            <div class="subject-tags">
                <span class="subject-tag" v-for="item in user.user_subjects" :key="item.id">{{ item.subject_name }}</span>
            </div>

            <div class="information-detail-gird">
                <div class="information-item">
                    <span>
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#f9ce69" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(249, 206, 105);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        4.9
                    </span>
                    <span>125 Đánh giá</span>
                </div>
                <div class="information-item">
                    <span>{{ $helper.getPriceRange(user.user_subjects) }}</span>
                    <span>Trên giờ</span>
                </div>
                <div class="information-item">
                    <span>5h</span>
                    <span>Thời gian trả lời</span>
                </div>
            </div>
        </div>
    </div>

    <div class="user-contact">
        <div class="user-contact_info">
            <div class="user-contact_avatar">
                <img :src="user.avatar || '/images/default-avatar.png'" />
            </div>
            <div class="user-contact_info_detail">
                <div>
                    <div class="user-contact_review">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#f9ce69" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(249, 206, 105);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>4.9</span>
                        (<span class="text-small gray-500">125 Đánh giá</span>)
                    </div>
                    <div class="user-contact_price">
                        <span>
                            274.000d/h
                        </span>
                    </div>
                </div>
                <div class="user-contact_heart">
                    <i class="fa-regular fa-heart"></i>
                </div>
            </div>
        </div>
        <button class="btn-xl btn-primary w-100 border-r-2" @click="redirectToBooking()" :disabled="user.uid === myUid">
            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" data-preply-ds-component="SvgTokyoUIIcon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="white"><path fill-rule="evenodd" d="m14 10 1-8L2 14h9l-1 8 12-12z" clip-rule="evenodd"></path></svg>
            Đặt lịch
        </button>
    </div>
</div>

<div class="user-detail-page" v-if="Object.keys(user).length > 0">
    <div class="container">
        <!-- Top profile card -->
        <div class="user-detail-header_desktop">
            <div class="user-detail-header">
                <div class="header-avatar-block">
                    <div class="avatar-wrapper">
                        <img :src="user.avatar || '/images/default-avatar.png'" />
                    </div>
                </div>
                <div class="header-main-block">
                    <div>
                        <h1 class="user-name">{{ user.full_name }}</h1>
                        <div class="user-title">{{ getTwoNameSubject }}</div>
                    </div>

                    <div class="user-stats-row-v2">
                        <div class="stat-item">
                            <div class="stat-group">
                                <div class="stat-icon">
                                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#f9ce69" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: rgb(249, 206, 105);"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                                </div>
                                <div class="stat-value">4.9</div>
                            </div>
                            <div class="stat-label">124 Reviews</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-group">
                                <div class="stat-icon">
                                    <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                                </div>
                                <div class="stat-value">{{ getAllNameLanguage }}</div>
                            </div>
                            <div class="stat-label">Ngôn ngữ</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-group">
                                <div class="stat-icon"><img class="icon-md" src="/images/location.svg" alt="Location" /></div>
                                <div class="stat-value">{{ user.provinces_name }}, {{ user.districts_name }}</div>
                            </div>
                            <div class="stat-label">Địa chỉ</div>
                        </div>
                    </div>

                    <div class="subject-tags">
                        <span class="subject-tag" v-for="item in user.user_subjects" :key="item.id">{{ item.subject_name }}</span>
                    </div>
                </div>

                <div class="header-actions">
                    <button class="btn-sm btn-secondary">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <button class="btn-sm btn-secondary">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
                    </button>
                </div>
            </div>

            <!-- Price bar -->
            <div class="price-bar">
                <div class="price-info">
                    <span class="price-label">Học phí:</span>
                    <span class="price-value">{{ $helper.getPriceRange(user.user_subjects) }}</span>
                    <span class="price-unit">VND/giờ</span>
                </div>
                <div class="price-actions">
                    <button class="btn-md btn-secondary border-r-2" @click="openSendMessageModal">
                        <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
                        Nhắn tin
                    </button>
                    <button class="btn-md btn-primary border-r-2" @click="redirectToBooking()">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" data-preply-ds-component="SvgTokyoUIIcon" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="white"><path fill-rule="evenodd" d="m14 10 1-8L2 14h9l-1 8 12-12z" clip-rule="evenodd"></path></svg>
                        Đặt lịch
                    </button>
                </div>
            </div>
        </div>

        <!-- Content section -->
        <div class="user-detail-main-v2">
            <!-- Tabs -->
            <div class="type-selector">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    :class="['type-button', { active: activeTab === tab.id }]"
                    @click="activeTab = tab.id"
                >
                    {{ tab.name }}
                </button>
            </div>

            <!-- Tab content -->
            <div class="tab-content">
                <OverviewTab v-if="activeTab === 'overview'" :user="user" />
                <ExperienceTab v-if="activeTab === 'experience'" :user="user" />
                <ReviewsTab v-if="activeTab === 'reviews'" :user="user" />
                <ScheduleTab v-if="activeTab === 'schedule'" :user="user" />
                <PricingTab v-if="activeTab === 'pricing'" :user="user" />
            </div>
        </div>
    </div>
    <base-modal
        v-if="showSendMessageModal"
        :isOpen="showSendMessageModal"
        @close="showSendMessageModal = false"
        size="small"
    >
        <div class="model-reject">
            <SendMessage
                :user="selectedUser"
                @messageSent="handleMessageSent"
                @close="showSendMessageModal = false"
            ></SendMessage>
        </div>
    </base-modal>
</div>
</template>

<style scoped>
@import url('@css/UserDetail.css');

.user-detail-page {
    --height-user-contact: 150px;
    background: #ffff;
    min-height: 100vh;
    padding: 1.5rem 0;
}

.container {
    margin: 0 auto;
}

/* user-detail-header_mobile */
.user-detail-header_mobile {
    position: relative;
    text-align: center;
    display: grid;
    align-items: center;
    /* justify-content: center; */
    gap: 0.7rem;
    background: var(--color-primary-transparent);
    border-bottom-left-radius: 1rem;
    border-bottom-right-radius: 1rem;
    padding: 2rem;
    display: none;
}

.avatar-wrapper_mobile {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #f3f4f6;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid white;
    margin: auto;
}

.avatar-wrapper_mobile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.user-name_mobile {
    text-align: center;
    margin: 1rem 0 0;
    font-size: var(--font-size-heading-4);
    font-weight: 700;
    color: #111827;
    line-height: 1.2;
    display: flex;
    justify-content: center;
    gap: 0.5rem;
}
.user-address_mobile {
    display: block;
    margin-bottom: 1rem;
    font-size: var(--font-size-small);
    color: var(--gray-500);
}
.user-detail-header_mobile .subject-tags {
    margin: auto;
}
.user-detail-header_mobile .subject-tag,
.user-detail-header_mobile .language-tag {
    background: white;
}

.header-actions_mobile {
    display: grid;
    position: absolute;
    top: 1rem;
    right: 1rem;
    gap: 0.5rem;
}

.header-actions button,
.header-actions_mobile button {
    border-radius: 1rem;
    width: 40px;
    height: 40px;
    padding: 0;
}

.user-detail-content {
    display: grid;
    gap: 0.7rem;
    width: 100%;
}

.information-detail-gird {
    display: flex;
    background: white;
    padding: 1rem;
    border-radius: 5rem;
    justify-content: space-around;
}

.information-item {
    display: grid;
    gap: 0.2rem;
}
.information-item span:first-child {
    font-size: 1rem;
    font-weight: 600;
}
.information-item span:last-child {
    font-size: var(--font-size-small);
}

.user-contact {
    position: fixed;
    z-index: 1000;
    bottom: 0;
    width: 100%;
    background: white;
    padding: 1.5rem;
    display: grid;
    align-items: start;
    gap: 0.7rem;
    height: var(--height-user-contact);
}
.user-contact_avatar img {
    width: 3.3rem;
    height: 3.3rem;
    border-radius: 100%;
}

.user-contact_info {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.user-contact_info_detail {
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: left;
    width: 100%;
    font-size: var(--font-size-small);
}
.user-contact_review {
    display: flex;
    align-items: center;
    gap: 0.2rem;
}
.user-contact_heart {
    margin-right: 1rem;
}
/* end user-detail-header_mobile */

/* Top profile card */
.user-detail-header_desktop {
    display: block;
}
.user-detail-header {
    display: flex;
    background: #fff;
    border-radius: 1.7rem 1.7rem 0 0;
    border: 1px solid #e4e4e7;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    padding: 2rem;
    position: relative;
}

.header-avatar-block {
    margin-right: 2rem;
}

.avatar-wrapper {
    width: 148px;
    height: 148px;
    border-radius: 50%;
    background: #f3f4f6;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid white;
}

.avatar-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.header-main-block {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.8rem;
    padding-top: 0.5rem;
}

.user-name {
    font-size: var(--font-size-heading-4);
    font-weight: 700;
    margin: 0;
    color: #111827;
    line-height: 1.2;
    display: flex;
    gap: 0.5rem;
}

.user-title {
    color: #6B7280;
    font-size: 1rem;
    /* margin-bottom: 1.75rem; */
}

.user-stats-row-v2 {
    display: flex;
    gap: 1rem;
    border-radius: 0.5rem;
    /* margin-bottom: 1.5rem; */
}

.stat-group {
    display: flex;
    align-items: center;
}

.stat-item {
    gap: 0.3rem;
    display: flex;
    flex-direction: column;
    background: #f4f4f580;
    padding: 0.8rem 1rem;
    width: 100%;
    border-radius: 0.4rem;
}

.stat-icon {
    margin-right: 0.5rem;
    display: flex;
    align-items: center;
}

.star-icon {
    color: #facc15;
}

.stat-value {
    font-weight: 500;
    margin-right: 0.35rem;
}

.stat-label {
    font-size: var(--font-size-mini);
    color: #6B7280;
}

.subject-tags {
    display: flex;
    gap: 0.5rem;
    /* margin-bottom: 1.25rem; */
    flex-wrap: wrap;
}

.language-tags {
    display: flex;
    align-items: center;
    justify-content: center;
}

.subject-tag, .language-tag {
    padding: 0.25rem 0.7rem;
    background: #f4f4f5;
    border-radius: 9999px;
    font-size: var(--font-size-mini);
    font-weight: 600;
    color: #374151;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    width: max-content;
}

.user-location {
    color: #6B7280;
    font-size: var(--font-size-small);
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.user-location img {
    opacity: 0.6;
    width: 1rem;
    height: 1rem;
}

.header-actions {
    position: absolute;
    top: 2rem;
    right: 2rem;
    display: flex;
    gap: 0.5rem;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: var(--font-size-mini);
    font-weight: 500;
    color: #374151;
    cursor: pointer;
}

/* Price bar */
.price-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 0 0 1.7rem 1.7rem;
    padding: 1.2rem 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e4e4e7;
    border-top: 0;
}

.price-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.price-label {
    font-size: var(--font-size-mini);
    color: #374151;
}

.price-value {
    font-weight: 700;
    font-size: 1.125rem;
}

.price-unit {
    color: #6B7280;
    font-size: var(--font-size-mini);
}

.price-type {
    color: #6B7280;
    font-size: var(--font-size-mini);
    margin-left: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.price-actions {
    display: flex;
    gap: 0.75rem;
}

/* Content section */
.user-detail-main-v2 {
    background: #fff;
    border-radius: 0.75rem;
    overflow: hidden;
}

.tab-content {
    background: #fff;
}

@media (max-width: 1024px) {
}

@media (max-width: 868px) {
    .user-detail-header_mobile {
        padding: 1rem;
        display: grid;
    }
    .user-detail-header_desktop {
        display: none;
    }

    .user-detail-page {
        padding: 1.5rem 0 calc(var(--height-user-contact) + 1.5rem) 0;
    }
}

@media (max-width: 640px) {
    .user-detail-header_mobile {
        padding: 1rem;
        display: grid;
    }
    .user-detail-header_desktop {
        display: none;
    }

    .information-item span:first-child {
        font-size: var(--font-size-small);
        font-weight: 600;
    }
    .information-item span:last-child {
        font-size: var(--font-size-mini);
    }
}
</style>

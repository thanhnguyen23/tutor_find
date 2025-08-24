<template>
<div class="booking-manager-page">
    <div class="container">
        <h1 class="title-header">Danh sách đặt lịch học</h1>
        <p class="desc">Quản lý và theo dõi tất cả các buổi học của bạn</p>
        <div class="booking-manager-toolbar">
            <base-input v-model="search" placeholder="Tìm kiếm theo tên gia sư, môn học hoặc mã đặt lịch..." />
            <button class="btn-lg btn-secondary filter-btn" @click="showFilter = !showFilter">
                <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414A1 1 0 0 0 14 13.414V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17V13.414a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z" /></svg>
                Lọc theo trạng thái
            </button>
        </div>
        <div class="booking-status-tabs">
            <button v-for="tab in tabs" :key="tab.value" :class="{active: status === tab.value}" @click="status = tab.value">
                {{ tab.label }} <span v-if="tab.count !== undefined">({{ tab.count }})</span>
            </button>
        </div>
        <div v-if="bookings.length === 0" class="empty-list">Không có lịch học nào.</div>
        <div v-for="booking in bookings" :key="booking.id" class="booking-card">
            <div class="booking-card-left">
                <img :src="booking.user?.avatar || '/images/default-avatar.png'" class="avatar" />
                <div class="content">
                    <div class="tutor-name">
                        {{ booking.user?.full_name }}
                        <span class="star">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#eab308" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,12748,12778"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            4.9
                        </span>
                    </div>
                    <div class="booking-code">Mã: {{ booking.request_code }}</div>
                    <div class="booking-date">
                        <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2" />
                            <path d="M16 2v4" />
                            <path d="M8 2v4" />
                            <path d="M3 10h18" /></svg>
                        {{ booking.date }}
                    </div>

                    <div class="note" v-if="booking.note">Ghi chú từ học viên: {{ booking.note }}</div>
                    <div class="note-cancelled" v-if="booking.note_cancelled">Lý do hủy: {{ booking.note_cancelled }}</div>

                    <button class="status-btn" :class="booking.statusClass">{{ booking.statusText }}</button>
                </div>
            </div>
            <div class="booking-card-center">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-v-904f91fb=""><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" data-v-904f91fb=""></path></svg>
                    {{ booking.subject?.name }} - {{ booking.education_level?.name }}
                </div>
                <div>
                    <svg width="16" height="16" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>

                    {{ booking.time_slot_start?.name }} - {{ booking.time_slot_end?.name }} ({{ booking.duration }}h)
                </div>
                <div class="booking-location" v-if="booking.study_location">
                    <span class="icon-location" v-html="booking.study_location.icon"></span>
                    <div>
                        <span class="location-name">{{ booking.study_location.name }}</span>
                        <br>
                        <span>{{ booking.location }}</span>
                    </div>
                </div>
            </div>
            <div class="booking-card-right">
                <div class="total-price">{{ $helper.formatCurrency(booking.total_price) }}</div>
                <div v-if="!booking.is_package" class="price-per-hour">
                    {{ $helper.formatCurrency(booking.hourly_rate) }}/giờ
                </div>
                <div v-else class="price-per-hour">
                    <span>
                        {{ booking.package.name }}<template v-if="booking.package.number_of_lessons"> ({{ booking.package.number_of_lessons }} buổi)</template>
                    </span>
                    <span>{{ booking.package?.duration }} phút/buổi</span>
                </div>
                <div class="action">
                    <template v-if="booking.status === 'pending'">
                        <button class="btn-sm btn-secondary" @click="openRejectModal(booking.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m4.9 4.9 14.2 14.2"></path>
                            </svg>
                            Hủy
                        </button>
                    </template>
                    <button class="btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,16234,16243"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </button>
                    <button class="btn-sm btn-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,16582,16591"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                    </button>
                </div>
            </div>
        </div>

        <base-pagination :meta="dataPaginate" :current-page="currentPage" @changePage="changePage"></base-pagination>
    </div>
</div>

<base-modal
    v-if="showRejectModal"
    :isOpen="showRejectModal"
    title="Hủy yêu cầu đặt lịch"
    description="Vui lòng cho biết lý do hủy để gia sư hiểu rõ tình hình."
    @close="showRejectModal = false"
>
    <div class="model-reject">
        <base-input type="textarea" v-model="noteCancelled" placeholder="Nhập lý do hủy (ví dụ: Bận đột xuất, không còn nhu cầu...)"></base-input>

        <div class="modal-footer">
            <button class="btn-md btn-secondary" @click="showRejectModal = false">Hủy</button>
            <button class="btn-md btn-primary" :disabled="!noteCancelled.trim()" @click="submitReject">Hủy đặt lịch</button>
        </div>
    </div>
</base-modal>
</template>

<script setup>
import {
    ref,
    computed,
    onMounted,
    getCurrentInstance,
    watch
} from 'vue';
import { useStore } from 'vuex';

const { proxy } = getCurrentInstance();
const store = useStore();

const search = ref('')
const status = ref('all')
const showFilter = ref(false)
const statusMap = ref({})
const currentPage = ref(1)
const showRejectModal = ref(false);
const noteCancelled = ref('');
const rejectBookingId = ref(null);

// FAKE DATA
const bookings = ref([]);
const dataPaginate = ref({});
const userData = store.state.userData;

const tabs = computed(() => {
    const arr = [
        { value: 'all', label: 'Tất cả' }
    ]
    for (const key in statusMap.value) {
        arr.push({ value: key, label: statusMap.value[key] })
    }
    return arr
})

const changePage = async (page) => {
    currentPage.value = page;
    const params = { page: currentPage.value };
    if (status.value !== 'all') {
        params.status = status.value;
    }
    if (search.value.trim()) {
        params.code = search.value.trim();
    }
    const res = await proxy.$api.apiGet('bookings', params);

    if (res.success) {
        bookings.value = res.data;
        dataPaginate.value = res.meta;
        statusMap.value = res.list_status || {};
    }
}

watch(status, () => {
    changePage(1);
});

watch(search, () => {
    changePage(1);
});

onMounted(() => {
    changePage(1);
})

const openRejectModal = (id) => {
    rejectBookingId.value = id;
    noteCancelled.value = '';
    showRejectModal.value = true;
}

const submitReject = async () => {
    if (!noteCancelled.value.trim()) return;
    await changeBookingStatus(rejectBookingId.value, 'cancelled', noteCancelled.value);
    showRejectModal.value = false;
}

const changeBookingStatus = async (id, status, note_cancelled = null) => {
    try {
        const payload = { id, status };
        if (status === 'cancelled' && note_cancelled) payload.note_cancelled = note_cancelled;
        const res = await proxy.$api.apiPost('bookings/change-status', payload);
        if (res.success) {
            proxy.$notification.success(res.message || 'Cập nhật trạng thái thành công!');
            changePage(currentPage.value);
        } else {
            proxy.$notification.error(res.message || 'Có lỗi xảy ra!');
        }
    } catch (e) {
        proxy.$notification.error('Có lỗi xảy ra!');
    }
}
</script>

<style scoped>
.booking-manager-page {
    padding: 2rem 0;
}

.container {
    margin: 0 auto;
}

.title-header {
    font-weight: 900;
    font-size: var(--font-size-heading-3);
    margin: 0;
}

.desc {
    color: #6b7280;
    margin-bottom: 1.5rem;
}

.booking-manager-toolbar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.2rem;
}


.filter-btn {
   white-space: nowrap;
}

.booking-status-tabs {
    display: flex;
    gap: 0;
    background: #f3f4f6;
    padding: 4px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.2rem;
}

.booking-status-tabs button {
    flex: 1;
    padding: 0.4rem 1.5rem;
    border: none;
    background: transparent;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 1rem;
    border-radius: 0;
    border-bottom: 2.5px solid transparent;
}

.booking-status-tabs button.active {
    background: white;
    color: black;
    border-radius: 6px 6px 0 0;
    border-bottom: 2.5px solid #18181b;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

.empty-list {
    color: #6b7280;
    text-align: center;
    margin: 2rem 0;
}

.booking-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.2rem;
    gap: 1.5rem;
}

.booking-card-left svg,
.booking-card-center svg {
    width: 1rem;
    height: 1rem;
    color: #6b7280;
}

.booking-card-right svg {
    width: 0.9rem;
    height: 0.9rem;
    color: #6b7280;
}

.booking-card-left {
    max-width: 350px;
    display: flex;
    align-items: center;
    gap: 1.1rem;
}

.booking-card-left .content {
    display: grid;
    gap: 0.2rem;
    color: #6b7280;
    font-size: var(--font-size-small);
}

.avatar {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    object-fit: cover;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
}

.tutor-name {
    font-weight: 700;
    font-size: 1.08rem;
    color: #222;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.star {
    font-weight: 500;
    font-size: var(--font-size-small);
    display: flex;
    align-items: center;
    gap: 0.2rem;
}

.star svg {
    margin-right: 0.1em;
}

.booking-date {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.booking-location {
    display: flex;
    align-content: start;
    gap: 0.3rem;
}

::v-deep .icon-location svg {
    width: 1rem;
    height: 1rem;
    color: #6b7280;
}

.status-btn {
    width: max-content;
    padding: 0.2rem 0.7rem;
    border-radius: 2rem;
    font-size: 0.7rem;
    font-weight: 500;
    border: none;
    background: #18181b;
    color: #fff;
    margin-top: 0.2rem;
}

/* .status-btn.pending {
    background: #f59e42;
    color: #fff;
}

.status-btn.confirmed {
    background: #6366f1;
    color: #fff;
}

.status-btn.completed {
    background: #16a34a;
    color: #fff;
}

.status-btn.cancelled {
    background: #e11d48;
    color: #fff;
} */

.booking-card-center {
    width: 300px;
    color: #6b7280;
    font-size: var(--font-size-small);
    display: flex;
    align-content: start;
    flex-direction: column;
    gap: 0.2rem;
}

.booking-card-right {
    min-width: 120px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.booking-card-right .action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.total-price {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1;
    color: #18181b;
}

.price-per-hour {
    color: #6b7280;
    font-size: 0.97rem;
    display: flex;
    align-items: end;
    flex-direction: column;
}

.model-reject {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
</style>

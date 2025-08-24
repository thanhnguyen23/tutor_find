<template>
<div class="booking-manager-page">
    <div class="container">
        <h1 class="title-header">Danh sách đặt lịch học</h1>
        <p class="desc">Quản lý và theo dõi tất cả các buổi học của bạn</p>
        <!-- <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-title">Tổng thu nhập</div>
                    <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path d="M3 17l6-6 4 4 8-8"/></svg>
                </div>
                <div class="stat-value">2.400.000đ</div>
                <div class="stat-desc">Từ 4 buổi học</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-title">Tổng giờ dạy</div>
                    <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="stat-value">8h</div>
                <div class="stat-desc">Đã hoàn thành</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-title">Tổng học viên</div>
                    <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M5.5 21a7.5 7.5 0 0 1 13 0"/></svg>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-desc">Học viên đã dạy</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-title">Tỷ lệ hoàn thành</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"></path><circle cx="12" cy="8" r="6"></circle></svg>
                </div>
                <div class="stat-value">20%</div>
                <div class="stat-desc">Buổi học hoàn thành</div>
            </div>
        </div> -->
        <div class="booking-manager-toolbar">
            <base-input v-model="search" placeholder="Tìm kiếm theo tên gia sư, môn học hoặc mã đặt lịch..." />
            <button class="btn-lg btn-secondary filter-btn" @click="showFilter = !showFilter">
                <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414A1 1 0 0 0 14 13.414V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17V13.414a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z" /></svg>
                Lọc
            </button>
        </div>
        <div class="booking-status-tabs">
            <button v-for="tab in tabs" :key="tab.value" :class="{active: status === tab.value}" @click="status = tab.value">
                {{ tab.label }} <span v-if="tab.count !== undefined">({{ tab.count }})</span>
            </button>
        </div>
        <div v-if="bookings.length === 0" class="empty-list">Không có lịch học nào.</div>
        <div class="booking-card">
            <div v-for="booking in bookings" :key="booking.id" class="booking-card-wrapper" @click="openLogsModal(booking.user_booking_logs)">
                <div class="booking-card-left">
                    <img :src="booking.user?.avatar || '/images/default-avatar.png'" class="avatar" />
                    <div class="content">
                        <div class="tutor-name">
                            <span>{{ booking.user?.full_name }}</span>
                            <span class="star">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#f9ce69" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" __v0_r="0,12748,12778">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                4.9
                            </span>
                        </div>
                        <div class="booking-code">Mã: {{ booking.request_code }}</div>
                        <div class="booking-date">
                            <svg class="icon-lg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path d="M16 2v4" />
                                <path d="M8 2v4" />
                                <path d="M3 10h18" />
                            </svg>
                            {{ booking.date }}
                        </div>

                        <button class="status-btn" :class="booking.statusClass">{{ booking.statusText }}</button>
                    </div>
                </div>
                <div class="booking-card-center">
                    <div class="booking-card-center_content">
                        <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"></path>
                        </svg>
                        <span>{{ booking.subject?.name }} - {{ booking.education_level?.name }}</span>
                    </div>
                    <div class="booking-card-center_content">
                        <svg class="icon-md" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <span>{{ booking.time_slot_start?.name }} - {{ booking.time_slot_end?.name }} ({{ booking.duration }}h)</span>
                    </div>
                    <div class="booking-card-center_content booking-location" v-if="booking.study_location">
                        <div>
                            <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <div class="booking-location-wrapper">
                            <span class="location-name">{{ booking.study_location.name }} -> {{ booking.location }}</span>
                            <!-- <span class="text-base text-gray-500">{{ booking.location }}</span> -->
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
                            <button class="btn-sm btn-primary" @click="changeBookingStatus(booking.id, 'confirmed')">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                                Xác nhận
                            </button>
                            <button class="btn-sm btn-secondary" @click="openRejectModal(booking.id)">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m4.9 4.9 14.2 14.2"></path>
                                </svg>
                                Từ chối
                            </button>
                        </template>
                        <template v-else-if="booking.status === 'confirmed'">
                            <button class="btn-sm btn-primary" @click="changeBookingStatus(booking.id, 'completed')">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                                Hoàn thành
                            </button>
                        </template>
                        <button class="btn-sm btn-secondary" @click="openSendMessageModal(booking.user)">
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- <div class="separation" v-if="booking.note || booking.note_cancelled"></div> -->

            <!-- <div class="note-wrapper note-user" v-if="booking.note">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>
                <div class="note_content">
                    <span class="note_label">Ghi chú từ học viên:</span>
                    <span>{{ booking.note }}</span>
                </div>
            </div>
            <div class="note-wrapper note-cancelled" v-if="booking.note_cancelled">
                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="12"></line><line x1="12" x2="12.01" y1="16" y2="16"></line></svg>
                <div class="note_content">
                    <span class="note_label">Ghi chú từ học viên:</span>
                    <span>{{ booking?.user_cancelled?.role_name }} hủy: {{ booking.note_cancelled }}</span>
                </div>
            </div> -->
        </div>

        <base-pagination :meta="dataPaginate" :current-page="currentPage" @changePage="changePage"></base-pagination>
    </div>

    <base-modal
        v-if="showRejectModal"
        :isOpen="showRejectModal"
        title="Từ chối yêu cầu đặt lịch"
        @close="showRejectModal = false"
    >
        <div class="model-reject">
            <base-input type="textarea" v-model="noteCancelled" placeholder="Nhập lý do từ chối (ví dụ: Lịch đã kín, không phù hợp chuyên môn...)"></base-input>

            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showRejectModal = false">Hủy</button>
                <button class="btn-md btn-primary" :disabled="!noteCancelled.trim()" @click="submitReject">Từ chối</button>
            </div>
        </div>
    </base-modal>

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

    <base-modal
        v-if="showLogsModal"
        :isOpen="showLogsModal"
        @close="showLogsModal = false"
        title="Lịch sử trạng thái booking"
        size="medium"
    >
        <div class="booking-logs-list">
            <div v-if="selectedLogs.length === 0">Không có lịch sử thay đổi.</div>
            <table v-else class="table table-logs">
                <thead>
                    <tr>
                        <th>Người thay đổi</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in selectedLogs" :key="log.id">
                        <td>{{ log.user?.full_name }}</td>
                        <td>{{ $helper.formatDateTime(log.created_at) }}</td>
                        <td>{{ log.statusText }}</td>
                        <td>{{ log.note || '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </base-modal>
</div>
</template>


<script setup>
import {
    ref,
    computed,
    onMounted,
    getCurrentInstance,
    watch
} from 'vue';
import {
    useStore
} from 'vuex';
import SendMessage from '../components/SendMessage.vue';

const {
    proxy
} = getCurrentInstance();
const store = useStore();

const search = ref('')
const status = ref('all')
const showFilter = ref(false)
const statusMap = ref({})
const currentPage = ref(1);
const showRejectModal = ref(false);
const showSendMessageModal = ref(false);
const noteCancelled = ref('')
const rejectBookingId = ref(null)
const selectedUser = ref(null)
const selectedLogs = ref([]);
const showLogsModal = ref(false);

// FAKE DATA
const bookings = ref([]);
const dataPaginate = ref({});

const tabs = computed(() => {
    const arr = [{
        value: 'all',
        label: 'Tất cả'
    }]
    for (const key in statusMap.value) {
        arr.push({
            value: key,
            label: statusMap.value[key]
        })
    }
    return arr
})

const changePage = async (page) => {
    currentPage.value = page;
    const params = {
        page: currentPage.value
    };
    if (status.value !== 'all') {
        params.status = status.value;
    }
    if (search.value.trim()) {
        params.code = search.value.trim();
    }
    const res = await proxy.$api.apiGet('bookings', params);

    if (res.success) {
        bookings.value = res.data;
        // bookings.value = Array(6).fill().flatMap(() => res.data);
        dataPaginate.value = res.meta;
        statusMap.value = res.list_status || {};
    }
}

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

const changeBookingStatus = async (id, status, note = null) => {
    try {
        const payload = { id, status };
        if (note) payload.note = note;
        const res = await proxy.$api.apiPost('bookings/change-status', payload);
        if (res.success) {
            proxy.$notification.success(res.message);
            changePage(currentPage.value);
        } else {
            proxy.$notification.error(res.message);
        }
    } catch (e) {
        proxy.$notification.error('Có lỗi xảy ra!');
    }
}

const openSendMessageModal = (user) => {
    selectedUser.value = user;
    showSendMessageModal.value = true;
}

const handleMessageSent = () => {
    showSendMessageModal.value = false;
    proxy.$notification.success('Gửi tin nhắn thành công!');
};

const openLogsModal = (logs) => {
    selectedLogs.value = logs || [];
    showLogsModal.value = true;
};

watch(status, () => {
    changePage(1);
});

watch(search, () => {
    changePage(1);
});

onMounted(() => {
    changePage(1);
})
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
    color: var(--gray-500);
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
    font-size: 0.97rem;
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
    color: var(--gray-500);
    text-align: center;
    margin: 2rem 0;
}

.booking-card {
    /* background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem; */
    display: grid;
    gap: 1.5rem;
}

.booking-card-left,
.booking-card-center,
.booking-card-right {
    width: -webkit-fill-available;
}

.booking-card-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
}

.booking-card-left {
    display: flex;
    gap: 1.1rem;
}

.booking-card-left .content {
    display: grid;
    gap: 0.2rem;
    font-size: 0.97rem;
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
    font-size: var(--font-size-medium);
    color: #222;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.star {
    font-weight: 500;
    font-size: var(--font-size-base);
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

.status-btn {
    width: max-content;
    padding: 0.1rem 0.7rem;
    border-radius: 2rem;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
    font-size: var(--font-size-mini);
    font-weight: 500;
    color: black;
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
    font-size: 0.97rem;
    display: flex;
    align-content: start;
    flex-direction: column;
    gap: 0.56rem;
}

.booking-card-center .booking-card-center_content {
    display: flex;
    align-items: start;
    line-height: 1;
    gap: 0.5rem;
}

.booking-card-right {
    min-width: 120px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
    position: relative;
}

.booking-card-right .action {
    width: max-content;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.booking-location-wrapper {
    display: grid;
    gap: 0.4rem;
}

.total-price {
    font-size: 1.35rem;
    font-weight: 800;
    line-height: 1;
    color: #18181b;
}

.price-per-hour {
    color: var(--gray-500);
    font-size: var(--font-size-base);
    display: flex;
    align-items: end;
    flex-direction: column;
}
.dashboard-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.2rem;
}
.stat-card {
    flex: 1;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1.3rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-width: 200px;
}
.stat-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.stat-title {
    color: var(--gray-500);
    font-size: 0.97rem;
    font-weight: 500;
}
.stat-value {
    font-size: var(--font-size-heading-4);
    font-weight: 900;
    color: #18181b;
}
.stat-desc {
    color: var(--gray-500);
    font-size: 0.97rem;
}
.model-reject {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.note-wrapper {
    margin-top: 1.2rem;
    padding: 1rem;
    border-radius: 0.5rem;
    display: flex;
    align-items: start;
    gap: 0.5rem;
    font-size: var(--font-size-small);
}

.note-user {
    background: var(--blue-50);
    border: 1px solid var(--blue-100);
    color: var(--color-primary);
}
.note-cancelled {
    background: var(--red-50);
    border: 1px solid var(--red-100);
    color: var(--red-600);
}

.note-wrapper .note_content {
    display: grid;
    gap: 0.5rem;
    line-height: 1;
}
.note-wrapper .note_content .note_label {
    font-size: var(--font-size-heading-6);
    font-weight: 500;
}

.booking-logs-list table th,
.booking-logs-list table td {
    padding: 1rem;
}

@media (max-width: 768px) {
    .booking-status-tabs {
        display: grid;
    }

    .booking-card-wrapper {
        display: grid;
        gap: 2.5rem;
    }

    .booking-card-center {
        gap: 0.8rem;
    }

    /* .booking-card-right {
        align-items: start;
    }

    .price-per-hour {
        align-items: start;
    } */

    .avatar {
        width:5rem;
        height:5rem;
    }
}
</style>

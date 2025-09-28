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
import { useRouter } from "vue-router"

// import ClassRoomCustom from './ClassRoomCustom.vue';

const {
    proxy
} = getCurrentInstance();
const store = useStore();
const router = useRouter()

// Reactive data
const search = ref('');
const status = ref('all');
const showFilter = ref(false);
const showCreateModal = ref(false);
const currentPage = ref(1);
const classrooms = ref([]);
const dataPaginate = ref({});
const statusMap = ref({});
const availableBookings = ref([]);
const selectedBooking = ref(null);
const isLoadingBookings = ref(false);
const showDetailModal = ref(false);
const selectedClassroom = ref(null);
const isLoading = ref(false);
const userData = computed(() => store.state.userData);

// Create form
const createForm = ref({
    booking_id: '',
    topic: '',
    agenda: '',
    scheduled_duration: '',
    learning_method: 'online'
});

const learningMethodOptions = ref([
    { value: 'online', name: 'Online' }
]);

// Computed
const tabs = computed(() => {
    const arr = [{
        value: 'all',
        label: 'Tất cả',
        count: classrooms.value.length
    }];

    const statusCounts = {};
    classrooms.value.forEach(classroom => {
        statusCounts[classroom.status] = (statusCounts[classroom.status] || 0) + 1;
    });

    for (const key in statusMap.value) {
        arr.push({
            value: key,
            label: statusMap.value[key],
            count: statusCounts[key] || 0
        });
    }
    return arr;
});

const filteredClassrooms = computed(() => {
    let filtered = classrooms.value;
    // Filter by status
    if (status.value !== 'all') {
        filtered = filtered.filter(classroom => classroom.status === status.value);
    }

    // Filter by search
    if (search.value.trim()) {
        const searchTerm = search.value.toLowerCase();
        filtered = filtered.filter(classroom =>
            classroom.topic?.toLowerCase().includes(searchTerm) ||
            classroom.booking?.request_code?.toLowerCase().includes(searchTerm) ||
            classroom.booking?.student?.full_name?.toLowerCase().includes(searchTerm) ||
            classroom.booking?.tutor?.full_name?.toLowerCase().includes(searchTerm)
        );
    }

    return filtered;
});

const initEcho = () => {
    try {
        if (window.Echo && userData.value?.uid) {
            window.Echo.private(`classroom.${userData.value.uid}`)
                .listen('.created', (e) => {
                    changePage(currentPage.value);
                })
                .listen('.updated', (e) => {
                    if (e.classroom_id) {
                        const classroomId = e.classroom_id;
                        const index = classrooms.value.findIndex(item => item.id === classroomId);

                        if (index !== -1) {
                            classrooms.value[index] = {
                                ...classrooms.value[index],
                                participants_count: e.participants_count,
                            };
                        }
                    }
                })
                .listen('.started', (e) => {
                    if (e.classroom_id) {
                        const classroomId = e.classroom_id;
                        const index = classrooms.value.findIndex(item => item.id === classroomId);

                        if (index !== -1) {
                            classrooms.value[index] = {
                                ...classrooms.value[index],
                                status: 'started',
                            };
                        }
                    }
                })
                .listen('.ended', (e) => {
                    if (e.classroom_id) {
                        const classroomId = e.classroom_id;
                        const index = classrooms.value.findIndex(item => item.id === classroomId);

                        if (index !== -1) {
                            classrooms.value[index] = {
                                ...classrooms.value[index],
                                status: 'ended',
                            };
                        }
                    }
                });
        }
    } catch (e) {
        console.error('Echo listen classroom.created failed', e)
    }
};

const canCreateClassroom = computed(() => {
    return createForm.value.booking_id &&
           createForm.value.topic &&
           createForm.value.scheduled_duration;
});

// Methods
const changePage = async (page) => {
    isLoading.value = true;
    try {
        currentPage.value = page;
        const params = { page: currentPage.value };
        if (status.value !== 'all') params.status = status.value;
        if (search.value.trim()) params.search = search.value.trim();

        const response = await proxy.$api.apiGet('classrooms', params);
        if (response?.success) {
            classrooms.value = (response.data || []).map(item => ({
                ...item,
            }));

            console.log(classrooms.value)
            dataPaginate.value = response.meta || {};
            statusMap.value = response.list_status || {};
        } else {
            proxy.$notification?.error(response?.message || 'Tải danh sách lớp học thất bại');
        }
    } catch (e) {
        proxy.$notification?.error('Có lỗi xảy ra khi tải danh sách lớp học');
    } finally {
        isLoading.value = false;
    }
};

const openCreateClassModal = async () => {
    showCreateModal.value = true;
    resetCreateForm();
    await loadAvailableBookings();
};

const loadAvailableBookings = async () => {
    isLoadingBookings.value = true;
    try {
        const response = await proxy.$api.apiGet('bookings/available-for-classroom');

        if (response) {
            availableBookings.value = (response.data || []).map(item => ({
                id: item.id,
                name: `${item.request_code} - ${item.user?.full_name || item.user?.full_name || '-'} - ${item.subject?.name || '-'}`,
                id: item.id,
                request_code: item.request_code,
                tutor: item.tutor,
                user: item.user,
                subject: item.subject,
                education_level: item.education_level,
                time_slot: item.time_slot,
                end_time_text: item.end_time_text,
                date: item.date,
                duration: item.duration,
                start_time: item.start_time,
                end_time: item.end_time,
                tutor_uid: item.tutor.uid,
                student_uid: item.user.uid,
            }));
        } else {
            proxy.$notification?.error(res.message || 'Tải danh sách booking thất bại');
        }
    } catch (e) {
        console.log(e)
        proxy.$notification?.error('Có lỗi xảy ra khi tải danh sách booking');
    } finally {
        isLoadingBookings.value = false;
    }
};

const onBookingChange = (bookingId) => {
    const raw = availableBookings.value.find(b => b.id === bookingId || b.value === bookingId) || null;

    if (!raw) {
        selectedBooking.value = null;
        return;
    }

    // Dùng trực tiếp start_time và end_time đã là datetime từ backend
    const scheduled_start_time = raw.start_time || null;
    const scheduled_end_time = raw.end_time || null;

    selectedBooking.value = {
        ...raw,
        scheduled_start_time,
        scheduled_end_time
    };

    // Autofill form
    createForm.value.scheduled_duration = (raw.duration || 0) * 60;
    createForm.value.topic = `${raw.subject?.name || ''}${raw.education_level?.name ? ' - ' + raw.education_level?.name : ''}`;
};

const resetCreateForm = () => {
    createForm.value = {
        booking_id: '',
        topic: '',
        agenda: '',
        scheduled_duration: '',
        learning_method: 'online'
    };
    selectedBooking.value = null;
};

const createClassroom = async () => {
    if (!canCreateClassroom.value || !selectedBooking.value) return;

    try {
        const payload = {
            booking_id: selectedBooking.value.id,
            topic: createForm.value.topic,
            agenda: createForm.value.agenda,
            scheduled_duration: createForm.value.scheduled_duration,
            learning_method: createForm.value.learning_method,
            scheduled_start_time: selectedBooking.value.scheduled_start_time,
            scheduled_end_time: selectedBooking.value.scheduled_end_time,
            tutor_uid: selectedBooking.value.tutor_uid,
            student_uid: selectedBooking.value.student_uid
        };

        const res = await proxy.$api.apiPost('classrooms', payload);
        if (res?.success) {
            proxy.$notification?.success(res?.message || 'Tạo lớp học thành công!');
            showCreateModal.value = false;
            changePage(currentPage.value);
        } else {
            proxy.$notification?.error(res?.message || 'Tạo lớp học thất bại');
        }
    } catch (e) {
        proxy.$notification?.error('Có lỗi xảy ra khi tạo lớp học');
    }
};

const startClassroom = async (classroom) => {
    // Kiểm tra thời gian và hết hạn trước khi gọi API
    if (classroom?.time_info?.is_missed) {
        proxy.$notification?.warning('Buổi học đã lỡ, không thể bắt đầu');
        return;
    }
    if (!proxy.$helper.canStartClassroom(classroom)) {
        proxy.$notification?.warning(classroom.time_info.time_status_text || 'Chưa đến giờ học');
        return;
    }

    try {
        // Gọi API để bắt đầu lớp học
        const res = await proxy.$api.apiPost(`classrooms/${classroom.id}/start`);
        if (res?.success) {
            proxy.$notification?.success(res?.message || 'Bắt đầu lớp học thành công!');

            changePage(currentPage.value);
        } else {
            proxy.$notification?.error(res?.message || 'Không thể bắt đầu lớp học');
        }
    } catch (e) {
        proxy.$notification?.error('Có lỗi xảy ra khi bắt đầu lớp học');
    }

    const routeData = router.resolve(({ name: 'classroom-room', params: { id: classroom.id } }));
    window.open(routeData.href, "_blank");
};

const endClassroom = async (classroomId) => {
    try {
        const res = await proxy.$api.apiPost(`classrooms/${classroomId}/end`);
        if (res?.success) {
            proxy.$notification?.success(res?.message || 'Kết thúc lớp học thành công!');
            changePage(currentPage.value);
        } else {
            proxy.$notification?.error(res?.message || 'Không thể kết thúc lớp học');
        }
    } catch (e) {
        proxy.$notification?.error('Có lỗi xảy ra khi kết thúc lớp học');
    }
};

const joinClassroom = (classroom) => {
    // Kiểm tra thời gian trước khi tham gia
    if (classroom?.time_info?.is_missed) {
        proxy.$notification?.warning('Buổi học đã lỡ, không thể tham gia');
        return;
    }
    if (!proxy.$helper.canStartClassroom(classroom)) {
        proxy.$notification?.warning(classroom.time_info.time_status_text || 'Chưa đến giờ học');
        return;
    }

    const routeData = router.resolve(({ name: 'classroom-room', params: { id: classroom.id } }));
    window.open(routeData.href, "_blank");
};

// openWebrtc removed; navigation via router

const retryClassroom = async (classroomId) => {
    try {
        const res = await proxy.$api.apiPost(`classrooms/${classroomId}/retry`);
        if (res?.success) {
            proxy.$notification?.success(res?.message || 'Thử lại tạo lớp học thành công!');
            changePage(currentPage.value);
        } else {
            proxy.$notification?.error(res?.message || 'Không thể thử lại tạo lớp học');
        }
    } catch (e) {
        proxy.$notification?.error('Có lỗi xảy ra khi thử lại tạo lớp học');
    }
};

const shareClassroom = async (classroom) => {
    try {
        const classroomUrl = `${window.location.origin}/classroom/${classroom.id}`;

        if (navigator.share) {
            await navigator.share({
                title: `Lớp học: ${classroom.topic || 'Chưa có chủ đề'}`,
                text: `Tham gia lớp học ${classroom.topic || 'Chưa có chủ đề'} - Mã booking: ${classroom.booking?.request_code}`,
                url: classroomUrl
            });
        } else {
            // Fallback: copy to clipboard
            await copyClassroomLink(classroom);
        }
    } catch (e) {
        if (e.name !== 'AbortError') {
            proxy.$notification?.error('Không thể chia sẻ lớp học');
        }
    }
};

const copyClassroomLink = async (classroom) => {
    try {
        const classroomUrl = `${window.location.origin}/classroom/${classroom.id}`;
        await navigator.clipboard.writeText(classroomUrl);
        proxy.$notification?.success('Đã sao chép link lớp học!');
    } catch (e) {
        proxy.$notification?.error('Không thể sao chép link');
    }
};

const openClassroomDetail = (classroom) => {
    selectedClassroom.value = classroom;
    showDetailModal.value = true;
};


// Watchers
watch(status, () => {
    // Filter is handled by computed property
});

watch(search, () => {
    // Filter is handled by computed property
});

watch(createForm.value.booking_id, (val) => {
    onBookingChange(val);
})

watch(() => createForm.value.booking_id, (val) => {
    onBookingChange(val);
});

// Lifecycle
onMounted(async () => {
    changePage(1);
    initEcho();
    // Load available bookings để check quyền tạo classroom
    await loadAvailableBookings();
});
</script>

<template>
<!-- Loading overlay -->
<base-loading v-if="isLoading" />

<div class="classroom-manager-page" v-if="!isLoading">
    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <h1 class="title-header">Quản lý lớp học</h1>
                <p class="desc">Tạo và quản lý các lớp học trực tuyến</p>
            </div>
            <button class="btn-md btn-primary" @click="openCreateClassModal">
                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="M12 5v14"></path>
                </svg>
                Tạo lớp học
            </button>
        </div>

        <!-- Filter và Search -->
        <div class="classroom-manager-toolbar">
            <base-input v-model="search" placeholder="Tìm kiếm theo chủ đề, mã booking hoặc tên học viên..." />
            <button class="btn-lg btn-secondary filter-btn" @click="showFilter = !showFilter">
                <svg width="20" height="20" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v2a1 1 0 0 1-.293.707l-6.414 6.414A1 1 0 0 0 14 13.414V19a1 1 0 0 1-1.447.894l-4-2A1 1 0 0 1 8 17V13.414a1 1 0 0 0-.293-.707L1.293 6.707A1 1 0 0 1 1 6V4z" />
                </svg>
                Lọc
            </button>
        </div>

        <!-- Status Tabs -->
        <div class="classroom-status-tabs">
            <button v-for="tab in tabs" :key="tab.value" :class="{active: status === tab.value}" @click="status = tab.value">
                {{ tab.label }} <span v-if="tab.count !== undefined">({{ tab.count }})</span>
            </button>
        </div>

        <!-- Empty State -->
        <div v-if="filteredClassrooms.length === 0" class="empty-list">
            <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
            </svg>
            <p>Chưa có lớp học nào</p>
            <button class="btn-md btn-primary" @click="openCreateClassModal">Tạo lớp học đầu tiên</button>
        </div>

        <!-- Classroom List -->
        <div class="classroom-card" v-else>
            <div v-for="classroom in filteredClassrooms" :key="classroom.id" class="classroom-card-wrapper" @click="openClassroomDetail(classroom)">
                <div class="classroom-card-left">
                    <div class="classroom-avatar">
                        <svg class="icon-lg" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>

                    <div class="content">
                        <div class="classroom-title">
                            <span>{{ classroom.topic || 'Chưa có chủ đề' }}</span>
                        </div>
                        <div class="classroom-code">Mã booking: {{ classroom.booking?.request_code }}</div>
                        <div class="classroom-duration">
                            <svg class="icon-sm" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ classroom.scheduled_duration }} phút
                        </div>
                        <div class="classroom-participants" v-if="classroom.status !== 'ended'">
                            <svg class="icon-sm" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span :class="$helper.getParticipantsClass(classroom)">
                                {{ classroom.participants_count || 0 }}/{{ classroom.max_participants || 2 }}
                            </span>
                        </div>
                        <span class="status-badge">{{ classroom.status_text }}</span>
                    </div>
                </div>

                <div class="classroom-card-center">
                    <div class="classroom-info">
                        <div class="info-row">
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span><span>Giáo viên:</span> {{ classroom.booking?.tutor?.full_name }}</span>
                        </div>
                        <div class="info-row">
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <span><span>Học viên:</span> {{ classroom.booking?.user?.full_name }}</span>
                        </div>
                        <div class="info-row">
                            <svg class="icon-sm" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path d="M16 2v4" />
                                <path d="M8 2v4" />
                                <path d="M3 10h18" />
                            </svg>
                            <span><span>Thời gian:</span> {{ $helper.formatDateTime(classroom.scheduled_start_time) }}</span>
                        </div>
                        <div v-if="classroom.time_info" class="info-row time-status">
                            <svg class="icon-sm" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            <span>{{ classroom.time_info.time_status_text }}</span>
                        </div>
                        <div class="info-row" v-if="classroom.agenda">
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
                                <path d="M14 2v6h6"></path>
                                <path d="M16 13H8"></path>
                                <path d="M16 17H8"></path>
                                <path d="M10 9H8"></path>
                            </svg>
                            <span><span>Chi tiết:</span> {{ classroom.agenda }}</span>
                        </div>
                    </div>
                </div>

                <div class="classroom-card-right">
                    <div class="classroom-actions">
                        <template v-if="classroom.status === 'pending' || classroom.status === 'scheduled'">
                            <button
                                class="btn-sm btn-primary"
                                :disabled="!$helper.canStartClassroom(classroom)"
                                @click.stop="startClassroom(classroom)"
                            >
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                                Bắt đầu
                            </button>
                        </template>
                        <template v-else-if="classroom.status === 'started'">
                            <button
                                class="btn-sm btn-secondary"
                                :disabled="!$helper.canStartClassroom(classroom)"
                                @click.stop="classroom.status === 'started' ? joinClassroom(classroom) : startClassroom(classroom)"
                            >
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <polyline points="10,17 15,12 10,7"></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                                Tham gia
                            </button>
                            <button class="btn-sm btn-danger" @click="endClassroom(classroom.id)">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="6" y="6" width="12" height="12" rx="2"></rect>
                                </svg>
                                Kết thúc
                            </button>
                        </template>
                        <template v-else-if="classroom.status === 'ended'">
                            <button class="btn-sm btn-secondary" disabled>
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"></path>
                                </svg>
                                Đã kết thúc
                            </button>
                        </template>
                        <template v-else-if="classroom.status === 'cancelled'">
                            <button class="btn-sm btn-secondary" disabled>
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m4.9 4.9 14.2 14.2"></path>
                                </svg>
                                Đã hủy
                            </button>
                        </template>
                        <template v-else-if="classroom.status === 'error'">
                            <button class="btn-sm btn-danger" @click="retryClassroom(classroom.id)">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                    <path d="M21 3v5h-5"></path>
                                    <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                    <path d="M3 21v-5h5"></path>
                                </svg>
                                Thử lại
                            </button>
                        </template>
                    </div>
                        <!-- More Menu -->
                        <div class="more-menu-container" @click.stop>
                            <base-more-menu>
                                <template #activator="{ toggle }">
                                    <div class="more-btn" @click.stop="toggle">
                                        <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="1"></circle>
                                            <circle cx="19" cy="12" r="1"></circle>
                                            <circle cx="5" cy="12" r="1"></circle>
                                        </svg>
                                    </div>
                                </template>
                            <template #default="{ close }">
                                <button class="more-menu-item" @click="shareClassroom(classroom); close()">
                                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"></path>
                                        <polyline points="16,6 12,2 8,6"></polyline>
                                        <line x1="12" y1="2" x2="12" y2="15"></line>
                                    </svg>
                                    Chia sẻ
                                </button>
                                <button class="more-menu-item" @click="copyClassroomLink(classroom); close()">
                                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                        <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                    </svg>
                                    Sao chép link
                                </button>
                            </template>
                        </base-more-menu>
                    </div>
                </div>
            </div>
        </div>

        <base-pagination :meta="dataPaginate" :current-page="currentPage" @changePage="changePage"></base-pagination>
    </div>

    <!-- WebRTC Modal removed: navigation uses dedicated route now -->

    <!-- Create Classroom Modal -->
    <base-modal v-if="showCreateModal" :isOpen="showCreateModal" title="Tạo lớp học" @close="showCreateModal = false">
        <div class="create-classroom-form">
            <div class="form-section">
                <base-select
                    v-model="createForm.booking_id"
                    :options="availableBookings"
                    label="Booking"
                    :required="true"
                    :loading="isLoadingBookings"
                    placeholder="Chọn booking"
                />
            </div>

            <div v-if="selectedBooking" class="form-section">
                <div class="booking-info-card">
                    <div class="booking-info-row">
                        <span class="label">Giáo viên:</span>
                        <span class="value">{{ selectedBooking.tutor?.full_name }}</span>
                    </div>
                    <div class="booking-info-row">
                        <span class="label">Học viên:</span>
                        <span class="value">{{ selectedBooking.user?.full_name }}</span>
                    </div>
                    <div class="booking-info-row">
                        <span class="label">Môn học:</span>
                        <span class="value">{{ selectedBooking.subject?.name }} - {{ selectedBooking.education_level?.name }}</span>
                    </div>
                    <div class="booking-info-row">
                        <span class="label">Thời gian học:</span>
                        <span class="value">{{ selectedBooking.time_slot?.name }} - {{ selectedBooking.end_time_text }}</span>
                    </div>
                    <div class="booking-info-row">
                        <span class="label">Ngày học:</span>
                        <span class="value">{{ selectedBooking.date }}</span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <base-input
                    v-model="createForm.topic"
                    label="Chủ đề lớp học"
                    placeholder="Nhập chủ đề lớp học..."
                    :required="true"
                />
                <base-select
                    v-model="createForm.learning_method"
                    :options="learningMethodOptions"
                    label="Phương thức học (mặc định)"
                    :disabled="true"
                    placeholder="Online"
                />
                <base-input
                    v-model="createForm.agenda"
                    type="textarea"
                    label="Chi tiết lớp học"
                    placeholder="Mô tả chi tiết nội dung sẽ học trong buổi này..."
                />
                <base-input
                    v-model="createForm.scheduled_duration"
                    type="number"
                    label="Thời lượng (phút)"
                    placeholder="Nhập thời lượng lớp học..."
                    :required="true"
                />
            </div>

            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showCreateModal = false">Hủy</button>
                <button class="btn-md btn-primary" :disabled="!canCreateClassroom" @click="createClassroom">
                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"></path>
                        <path d="M12 5v14"></path>
                    </svg>
                    Tạo lớp học
                </button>
            </div>
        </div>
    </base-modal>

    <!-- Classroom Detail Modal -->
    <base-modal v-if="showDetailModal" :isOpen="showDetailModal" title="Chi tiết lớp học" @close="showDetailModal = false" size="small">
        <div v-if="selectedClassroom" class="classroom-detail-content">
            <!-- Header Section -->
            <div class="detail-header">
                <div class="classroom-avatar-large">
                    <svg class="icon-xl" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <div class="header-info">
                    <h2 class="classroom-title-large">{{ selectedClassroom.topic || 'Chưa có chủ đề' }}</h2>
                    <div class="classroom-meta">
                        <span class="status-badge-large" :class="$helper.getStatusClass(selectedClassroom.status)">
                            {{ selectedClassroom.status_text }}
                        </span>
                        <span class="classroom-code-large">Mã booking: {{ selectedClassroom.booking?.request_code }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="detail-content">
                <!-- Basic Information -->
                <div class="detail-section">
                    <h3 class="section-title">Thông tin cơ bản</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Thời lượng dự kiến
                            </div>
                            <div class="info-value">{{ selectedClassroom.scheduled_duration }} phút</div>
                        </div>
                        <div v-if="selectedClassroom.actual_duration" class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Thời lượng thực tế
                            </div>
                            <div class="info-value">{{ selectedClassroom.actual_duration }} phút</div>
                        </div>
                        <div class="info-item" v-if="selectedClassroom.status !== 'ended'">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Số lượng tham gia
                            </div>
                            <div class="info-value" :class="$helper.getParticipantsClass(selectedClassroom)">
                                {{ selectedClassroom.participants_count || 0 }}/{{ selectedClassroom.max_participants || 2 }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                                Thời gian bắt đầu dự kiến
                            </div>
                            <div class="info-value">{{ $helper.formatDateTime(selectedClassroom.scheduled_start_time) }}</div>
                        </div>
                        <div v-if="selectedClassroom.actual_start_time" class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                                Thời gian bắt đầu thực tế
                            </div>
                            <div class="info-value">{{ $helper.formatDateTime(selectedClassroom.actual_start_time) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                                Thời gian kết thúc dự kiến
                            </div>
                            <div class="info-value">{{ $helper.formatDateTime(selectedClassroom.scheduled_end_time) }}</div>
                        </div>
                        <div v-if="selectedClassroom.actual_end_time" class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                                Thời gian kết thúc thực tế
                            </div>
                            <div class="info-value">{{ $helper.formatDateTime(selectedClassroom.actual_end_time) }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">
                                <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" />
                                    <path d="M16 2v4" />
                                    <path d="M8 2v4" />
                                    <path d="M3 10h18" />
                                </svg>
                                Ngày tạo
                            </div>
                            <div class="info-value">{{ $helper.formatDateTime(selectedClassroom.created_at) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Participants Information -->
                <div class="detail-section">
                    <h3 class="section-title">Thông tin người tham gia</h3>
                    <div class="participants-info">
                        <div class="participant-card">
                            <div class="participant-avatar">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M4 21v-2a4 4 0 0 1 3-3.87"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="participant-details">
                                <div class="participant-name">{{ selectedClassroom.booking?.tutor?.full_name }}</div>
                                <div class="participant-role">Giáo viên</div>
                            </div>
                        </div>
                        <div class="participant-card">
                            <div class="participant-avatar">
                                <svg class="icon-md" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="participant-details">
                                <div class="participant-name">{{ selectedClassroom.booking?.user?.full_name }}</div>
                                <div class="participant-role">Học viên</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject Information -->
                <div class="detail-section">
                    <h3 class="section-title">Thông tin môn học</h3>
                    <div class="subject-info">
                        <div class="subject-item">
                            <div class="subject-label">Môn học</div>
                            <div class="subject-value">{{ selectedClassroom.booking?.subject?.name }}</div>
                        </div>
                        <div class="subject-item">
                            <div class="subject-label">Cấp độ</div>
                            <div class="subject-value">{{ selectedClassroom.booking?.education_level?.name }}</div>
                        </div>
                        <div class="subject-item">
                            <div class="subject-label">Phương thức học</div>
                            <div class="subject-value">{{ selectedClassroom.learning_method || 'Zoom' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Agenda -->
                <div v-if="selectedClassroom.agenda" class="detail-section">
                    <h3 class="section-title">Chi tiết lớp học</h3>
                    <div class="agenda-content">
                        {{ selectedClassroom.agenda }}
                    </div>
                </div>

                <!-- Zoom Links -->
                <div v-if="selectedClassroom.zoom_start_url || selectedClassroom.zoom_join_url || selectedClassroom.zoom_password" class="detail-section">
                    <h3 class="section-title">Liên kết Zoom</h3>
                    <div class="zoom-links">
                        <div v-if="selectedClassroom.zoom_start_url" class="zoom-link-item">
                            <div class="zoom-link-label">Link bắt đầu (Host)</div>
                            <div class="zoom-link-value">{{ selectedClassroom.zoom_start_url }}</div>
                        </div>
                        <div v-if="selectedClassroom.zoom_join_url" class="zoom-link-item">
                            <div class="zoom-link-label">Link tham gia</div>
                            <div class="zoom-link-value">{{ selectedClassroom.zoom_join_url }}</div>
                        </div>
                        <div v-if="selectedClassroom.zoom_password" class="zoom-link-item">
                            <div class="zoom-link-label">Mật khẩu Zoom</div>
                            <div class="zoom-link-value">{{ selectedClassroom.zoom_password }}</div>
                        </div>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="selectedClassroom.error_message" class="detail-section">
                    <h3 class="section-title">Thông báo lỗi</h3>
                    <div class="error-message">
                        <div class="error-icon">
                            <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="m4.9 4.9 14.2 14.2"></path>
                            </svg>
                        </div>
                        <div class="error-content">
                            {{ selectedClassroom.error_message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button class="btn-md btn-secondary" @click="showDetailModal = false">Đóng</button>
                <button v-if="selectedClassroom.status === 'pending' || selectedClassroom.status === 'scheduled'"
                        class="btn-md btn-primary"
                        :disabled="$helper.canStartClassroom(selectedClassroom)"
                        @click="startClassroom(selectedClassroom)"
                        :title="$helper.canStartClassroom(selectedClassroom) ? (selectedClassroom.time_info.time_status_text || '') : ''">
                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                    Bắt đầu lớp học
                </button>
                <button v-else-if="selectedClassroom.status === 'started'"
                        class="btn-md btn-secondary"
                        :disabled="$helper.canStartClassroom(selectedClassroom)"
                        @click="joinClassroom(selectedClassroom)"
                        :title="$helper.canStartClassroom(selectedClassroom) ? (selectedClassroom.time_info.time_status_text || '') : ''">
                    <svg class="icon-sm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10,17 15,12 10,7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                    Tham gia lớp học
                </button>
            </div>
        </div>
    </base-modal>
</div>
</template>

<style scoped>
.classroom-manager-page {
    padding: 2rem 0;
}

.container {
    margin: 0 auto;
}

.page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-content {
    flex: 1;
}

.title-header {
    font-weight: 900;
    font-size: var(--font-size-heading-3);
    margin: 0;
}

.desc {
    color: var(--gray-500);
    margin: 0.5rem 0 0 0;
}

.classroom-manager-toolbar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.2rem;
}

.filter-btn {
    white-space: nowrap;
}

.classroom-status-tabs {
    display: flex;
    gap: 0;
    background: #f3f4f6;
    padding: 4px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1.2rem;
}

.classroom-status-tabs button {
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

.classroom-status-tabs button.active {
    background: white;
    color: black;
    border-radius: 6px 6px 0 0;
    border-bottom: 2.5px solid #18181b;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

.empty-list {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-500);
}

.empty-icon {
    margin-bottom: 1rem;
}

.empty-list p {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.classroom-card {
    display: grid;
    gap: 1.5rem;
}

.classroom-card-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.classroom-card-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.classroom-card-left {
    display: flex;
    gap: 1.1rem;
    flex: 1;
}

.classroom-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.content {
    display: grid;
    gap: 0.2rem;
    font-size: 0.97rem;
}

.classroom-title {
    font-weight: 700;
    font-size: var(--font-size-medium);
    color: #222;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.status-badge {
    width: max-content;
    padding: 0.1rem 0.7rem;
    border-radius: 2rem;
    font-size: var(--font-size-mini);
    font-weight: 500;
    background: var(--gray-100);
    border: 1px solid var(--gray-300);
}

.status-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #f87171;
}

.classroom-code {
    color: var(--gray-600);
    font-size: var(--font-size-small);
}

.classroom-duration {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: var(--gray-600);
    font-size: var(--font-size-small);
}

.classroom-participants {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    color: var(--gray-600);
    font-size: var(--font-size-small);
}

.participants-available {
    color: #dc2626;
    font-weight: 500;
}

.participants-full {
    color: #059669;
    font-weight: 500;
}

/* Time status styles */

.classroom-card-center {
    flex: 2;
    font-size: 0.97rem;
}

.classroom-info {
    display: grid;
    gap: 0.5rem;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    line-height: 1.4;
}

.info-row svg {
    flex-shrink: 0;
    color: #6b7280;
}

.classroom-card-right {
    height: 100%;
    position: relative;
    min-width: 200px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: center;
    gap: 0.5rem;
}

.classroom-actions {
    display: flex;
    align-items: center;
    flex-direction: column;
    gap: 0.5rem;
}

.more-menu-container {
    position: absolute;
    top: 0;
    right: 0;
}

.more-btn {
    cursor: pointer;
}

/* More menu styles are provided by BaseMoreMenu.vue */

/* Classroom Detail Modal Styles */

.detail-header {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.classroom-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    flex-shrink: 0;
}

.header-info {
    flex: 1;
}

.classroom-title-large {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 0.5rem 0;
}

.classroom-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.status-badge-large {
    padding: 0.1rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    color: #374151;
}

.status-badge-large.status-pending {
    background: #fef3c7;
    color: #92400e;
    border-color: #f59e0b;
}

.status-badge-large.status-scheduled {
    background: #dbeafe;
    color: #1e40af;
    border-color: #3b82f6;
}

.status-badge-large.status-started {
    background: #dcfce7;
    color: #166534;
    border-color: #22c55e;
}

.status-badge-large.status-ended {
    background: #f3f4f6;
    color: #6b7280;
    border-color: #9ca3af;
}

.status-badge-large.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
    border-color: #ef4444;
}

.status-badge-large.status-error {
    background: #fef2f2;
    color: #dc2626;
    border-color: #f87171;
}

.classroom-code-large {
    color: #6b7280;
    font-size: 0.875rem;
}

.detail-content {
    display: grid;
    gap: 2rem;
}

.detail-section {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
}

.section-title {
    font-size: var(--font-size-medium);
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1rem 0;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
}

.info-value {
    font-size: var(--font-size-base);
    color: #1f2937;
    font-weight: 500;
}

.participants-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.participant-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.participant-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    flex-shrink: 0;
}

.participant-details {
    flex: 1;
}

.participant-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.participant-role {
    font-size: 0.875rem;
    color: #6b7280;
}

.subject-info {
    display: grid;
}

.subject-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
}

.subject-item:last-child {
    border-bottom: none;
}

.subject-label {
    font-weight: 500;
    color: #6b7280;
    font-size: var(--font-size-base);
}

.subject-value {
    color: #1f2937;
    font-weight: 500;
    font-size: var(--font-size-small);
}

.agenda-content {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1rem;
    color: #374151;
    line-height: 1.6;
    white-space: pre-wrap;
}

.zoom-links {
    display: grid;
    gap: 1rem;
}

.zoom-link-item {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 1rem;
}

.zoom-link-label {
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.zoom-link-value {
    color: #1f2937;
    font-family: monospace;
    font-size: 0.875rem;
    word-break: break-all;
    background: #f8fafc;
    padding: 0.5rem;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
}

.error-message {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 1rem;
}

.error-icon {
    color: #dc2626;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.error-content {
    color: #991b1b;
    font-size: 0.875rem;
    line-height: 1.5;
    flex: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .detail-header {
        flex-direction: column;
        text-align: center;
    }

    .classroom-meta {
        justify-content: center;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .participants-info {
        grid-template-columns: 1fr;
    }
}

/* Create Classroom Modal Styles */
.create-classroom-form {
    display: grid;
    gap: 2rem;
}

.form-section {
    display: grid;
    gap: 1rem;
}

.booking-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    display: grid;
    gap: 0.5rem;
}

.booking-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.25rem 0;
    font-size: var(--font-size-small);
}

.booking-info-row .label {
    font-weight: 500;
    color: #374151;
}

.booking-info-row .value {
    color: #6b7280;
}

.method-row {
    display: grid;
    gap: 0.4rem;
}

.method-label {
    font-weight: 500;
    color: #374151;
}

.method-options {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.method-option {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    font-size: var(--font-size-small);
}

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .classroom-status-tabs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .classroom-card-wrapper {
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;
    }

    .classroom-card-center {
        width: 100%;
    }

    .classroom-card-right {
        width: 100%;
        align-items: flex-start;
    }

    .classroom-actions {
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .classroom-status-tabs {
        grid-template-columns: 1fr;
    }

    .classroom-actions {
        flex-direction: column;
        width: 100%;
    }

    .classroom-actions button {
        width: 100%;
        justify-content: center;
    }
}
</style>

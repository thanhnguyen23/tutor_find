<template>
<div class="profile-info-form">
    <!-- Thông tin cá nhân -->
    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Thông Tin Cá Nhân</span>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <base-input v-model="form.first_name" label="Họ *" placeholder="Nguyễn" />
            </div>
            <div class="form-group">
                <base-input v-model="form.last_name" label="Tên *" placeholder="Văn A" />
            </div>
            <div class="form-group">
                <base-select v-model="form.sex" :options="genderOptions" label="Giới tính *" placeholder="Chọn giới tính" />
            </div>
            <div class="form-group">
                <base-input v-model="form.phone" label="Số điện thoại" placeholder="0123456789" />
            </div>
            <div class="form-group">
                <base-input v-model="form.email" label="Email" placeholder="example@email.com" type="email" />
            </div>
            <div class="form-group">
                <base-input v-model="form.address" label="Địa chỉ" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố" />
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><rect width="20" height="14" x="2" y="5" rx="2"></rect><line x1="2" x2="22" y1="10" y2="10"></line></svg>
                <span>Thông Tin CCCD</span>
            </div>
            <span class="section-desc">Link video YouTube giới thiệu bản thân</span>
        </div>
        <div class="form-grid">
            <div class="form-group full-width">
                <base-input v-model="form.cccd" label="Số CCCD *" placeholder="001234567890" />
            </div>
            <div class="form-group half-width file-group">
                <span class="file-label-top">Ảnh CCCD mặt trước *</span>
                <div v-if="cccdFrontPreview" class="img-preview-box">
                    <img :src="cccdFrontPreview" alt="Preview CCCD mặt trước" />
                    <div class="img-preview-actions">
                        <button type="button" class="btn-mini btn-secondary border-md" @click.prevent="triggerFile('cccdFront')">Thay đổi</button>
                        <button type="button" class="btn-mini btn-secondary border-md" @click.prevent="removeFile('cccd_front')">Xóa</button>
                    </div>
                </div>
                <div v-else class="file-upload-box">
                    <input type="file" id="cccdFront" accept="image/*" @change="onFileChange($event, 'cccd_front')" />
                    <label class="file-upload-label" for="cccdFront">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" x2="12" y1="3" y2="15"></line></svg>
                        <span>Mặt trước CCCD<br />JPG, PNG (tối đa 5MB)</span>
                        <button type="button" class="btn-md btn-secondary border-md">Chọn file</button>
                        <span v-if="form.cccd_front" class="file-chosen">{{ form.cccd_front.name }}</span>
                    </label>
                </div>
            </div>
            <div class="form-group half-width file-group">
                <span class="file-label-top">Ảnh CCCD mặt sau *</span>
                <div v-if="cccdBackPreview" class="img-preview-box">
                    <img :src="cccdBackPreview" alt="Preview CCCD mặt sau" />
                    <div class="img-preview-actions">
                        <button type="button" class="btn-mini btn-secondary border-md" @click.prevent="triggerFile('cccdBack')">Thay đổi</button>
                        <button type="button" class="btn-mini btn-secondary border-md" @click.prevent="removeFile('cccd_back')">Xóa</button>
                    </div>
                </div>
                <div v-else class="file-upload-box">
                    <input type="file" id="cccdBack" accept="image/*" @change="onFileChange($event, 'cccd_back')" />
                    <label class="file-upload-label" for="cccdBack">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" x2="12" y1="3" y2="15"></line></svg>
                        <span>Mặt sau CCCD<br />JPG, PNG (tối đa 5MB)</span>
                        <button type="button" class="btn-md btn-secondary border-md">Chọn file</button>
                        <span v-if="form.cccd_back" class="file-chosen">{{ form.cccd_back.name }}</span>
                    </label>
                </div>
            </div>
        </div>
        <div class="note-box">
            <div class="note-content">
                <span class="note-label">Lưu ý khi chụp ảnh CCCD:</span>
                <ul>
                    <li>Ảnh rõ nét, không bị mờ hoặc chói sáng</li>
                    <li>Chụp toàn bộ thẻ, không bị cắt góc</li>
                    <li>Thông tin trên thẻ phải đọc được rõ ràng</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"></path><rect x="2" y="6" width="14" height="12" rx="2"></rect></svg>
                Video Giới Thiệu
            </div>
            <span class="section-desc">Link video YouTube giới thiệu bản thân</span>
        </div>
        <div class="form-group full-width">
            <base-input v-model="form.referral_link" label="Link YouTube *" placeholder="https://www.youtube.com/watch?v=..." />
        </div>
        <div v-if="youtubeEmbedUrl" class="youtube-preview">
            <iframe :src="youtubeEmbedUrl" width="100%" height="315" frameborder="0" allowfullscreen></iframe>
        </div>
        <div class="note-box">
            <div class="note-content">
                <span class="note-label">Video nên dài 2-5 phút, giới thiệu về bản thân, kinh nghiệm và phương pháp giảng dạy</span>
                <ul>
                    <li>Giới thiệu bản thân và kinh nghiệm</li>
                    <li>Phương pháp giảng dạy của bạn</li>
                    <li>Thành tích và chứng chỉ (nếu có)</li>
                    <li>Cam kết với học sinh và phụ huynh</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-header">
            <div class="header-left">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-xl"><rect x="3" y="8" width="18" height="4" rx="1"></rect><path d="M12 8v13"></path><path d="M19 12v7a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-7"></path><path d="M7.5 8a2.5 2.5 0 0 1 0-5A4.8 8 0 0 1 12 8a4.8 8 0 0 1 4.5-5 2.5 2.5 0 0 1 0 5"></path></svg>
                Buổi Học Miễn Phí
            </div>
            <span class="section-desc">Cài đặt buổi học thử miễn phí để thu hút học sinh</span>
        </div>
        <div class="switch-group">
            <div>
                <span>Bật buổi học miễn phí</span>
                <br>
                <span class="switch-desc">Cho phép học sinh học thử miễn phí buổi đầu tiên</span>
            </div>
            <div>
                <input type="checkbox" id="freeTrial" class="switch-input" v-model="form.free_trial" />
                <label for="freeTrial" class="switch-label"></label>
            </div>
        </div>
        <div v-if="form.free_trial" class="select-time">
            <base-select
                v-model="form.free_study_time"
                :options="freeTrialDurationOptions"
                placeholder="Chọn thời gian"
                label="Thời gian buổi học miễn phí"
            />
        </div>
    </div>

    <div class="form-actions">
        <!-- <button class="btn-md btn-secondary">H</button> -->
        <button class="btn-md btn-primary" @click="saveProfile">Hoàn thành hồ sơ</button>
    </div>
</div>
</template>

<script setup>
import {
    ref,
    computed,
    watch,
    getCurrentInstance
} from 'vue';

import BaseInput from '@/components/BaseInput.vue';
import BaseSelect from '@/components/BaseSelect.vue';

// Props
const props = defineProps({
    userDataDetail: {
        type: Object,
        required: true
    }
});

const {proxy} = getCurrentInstance();
const emit = defineEmits(['update-data']);

const form = ref({
    first_name: '',
    last_name: '',
    sex: '',
    birth_year: '',
    phone: '',
    email: '',
    address: '',
    cccd: '',
    cccd_front: null,
    cccd_back: null,
    referral_link: '',
    free_trial: false,
    free_study_time: '',
});

const cccdFrontPreview = ref(null);
const cccdBackPreview = ref(null);

const genderOptions = [{
        name: 'Nam',
        value: 1
    },
    {
        name: 'Nữ',
        value: 2
    },
];

const freeTrialDurationOptions = [
    { value: 15, name: '15 phút' },
    { value: 30, name: '30 phút' },
    { value: 45, name: '45 phút' },
    { value: 60, name: '60 phút' },
];

// Load user profile data from props
const loadUserProfile = () => {
    const userData = props.userDataDetail;

    // Map API data to form
    form.value = {
        first_name: userData.first_name,
        last_name: userData.last_name,
        sex: userData.sex,
        birth_year: '',
        phone: userData.phone || '',
        email: userData.email || '',
        address: userData.address || '',
        cccd: userData.cccd || '',
        cccd_front: userData.cccd_front,
        cccd_back: userData.cccd_back,
        referral_link: userData.referral_link,
        free_trial: userData.is_free_study,
        free_study_time: userData.free_study_time || '',
    };

    console.log(form.value)

    // Load existing CCCD images if they exist
    if (userData.cccd_front) {
        cccdFrontPreview.value = userData.cccd_front;
    }
    if (userData.cccd_back) {
        cccdBackPreview.value = userData.cccd_back;
    }
};

// Save user profile data
const saveProfile = async () => {
    try {
        const formData = new FormData();

        formData.append('first_name', form.value.first_name);
        formData.append('last_name', form.value.last_name);
        formData.append('phone', form.value.phone);
        formData.append('email', form.value.email);
        formData.append('address', form.value.address);
        formData.append('cccd', form.value.cccd);
        formData.append('referral_link', form.value.referral_link);
        formData.append('sex', form.value.sex ? 1 : 0);
        formData.append('is_free_study', form.value.free_trial ? 1 : 0);
        formData.append('free_study_time', form.value.free_study_time);

        // Add files if they exist
        if (form.value.cccd_front instanceof File) {
            formData.append('cccd_front', form.value.cccd_front);
        }
        if (form.value.cccd_back instanceof File) {
            formData.append('cccd_back', form.value.cccd_back);
        }

        const response = await proxy.$api.apiPostFormData('me/profile-info', formData);
        if (response.success) {
            emit('update-data', response.data);
            proxy.$notification.success(res.message);
        }
    } catch (error) {
        console.error('Error saving profile:', error);
    }
};

function onFileChange(event, key) {
    const file = event.target.files[0];
    if (file) {
        form.value[key] = file;
        if (key === 'cccd_front') {
            cccdFrontPreview.value && URL.revokeObjectURL(cccdFrontPreview.value);
            cccdFrontPreview.value = URL.createObjectURL(file);
        }
        if (key === 'cccd_back') {
            cccdBackPreview.value && URL.revokeObjectURL(cccdBackPreview.value);
            cccdBackPreview.value = URL.createObjectURL(file);
        }
    }
}

watch(() => form.value.cccd_front, (file) => {
    if (!file && cccdFrontPreview.value) {
        URL.revokeObjectURL(cccdFrontPreview.value);
        cccdFrontPreview.value = null;
    }
});
watch(() => form.value.cccd_back, (file) => {
    if (!file && cccdBackPreview.value) {
        URL.revokeObjectURL(cccdBackPreview.value);
        cccdBackPreview.value = null;
    }
});

// Watch for changes in props to reload data
watch(() => props.userDataDetail, () => {
    loadUserProfile();
}, { immediate: true });

const youtubeEmbedUrl = computed(() => {
    if (!form.value.referral_link) return '';
    // Hỗ trợ cả dạng full URL và share URL
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([\w-]{11})/;
    const match = form.value.referral_link.match(regex);
    if (match && match[1]) {
        return `https://www.youtube.com/embed/${match[1]}`;
    }
    return '';
});

const triggerFile = (id) => {
    document.getElementById(id)?.click();
}
const removeFile = (key) => {
    form.value[key] = null;
}
</script>

<style scoped>
@import url('@css/profile.css');
.profile-info-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.section-title-row {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    margin-bottom: 1.2rem;
}

.section-title {
    font-size: 1.15rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-desc {
    color: #71717a;
    font-size: var(--font-size-base);
}

.form-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1.2rem 2%;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
    flex: 1 1 48%;
}

.form-group.full-width {
    flex: 1 1 100%;
}

.form-group.half-width {
    flex: 1 1 48%;
}

input[type="text"],
input[type="email"],
select {
    padding: 0.5rem 0.8rem;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 1rem;
    background: #f9fafb;
}

input[type="text"]:focus,
input[type="email"]:focus,
select:focus {
    outline: none;
    border-color: #6366f1;
}

.file-group {
    margin-bottom: 0;
}
.file-label-top {
    font-weight: 500;
    font-size: var(--font-size-small);
    margin-bottom: 0.5rem;
    color: #222;
}
.file-upload-box {
    width: 100%;
    min-height: 160px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    position: relative;
    cursor: pointer;
    padding: 1.2rem 0.5rem;
}
.file-upload-label {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #71717a;
    cursor: pointer;
    font-size: var(--font-size-small);
    text-align: center;
}
.btn-file {
    margin-top: 0.5rem;
    background: #e5e7eb;
    border: none;
    border-radius: 6px;
    padding: 0.2rem 1.1rem;
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
}
input[type="file"] {
    display: none;
}
.file-chosen {
    color: black;
    font-size: var(--font-size-small);
    margin-top: 0.2rem;
    word-break: break-all;
}
.note-box {
    display: flex;
    align-items: flex-start;
    gap: 0.7rem;
    background: #f9fafb;
    border: 1px solid #e4e4e7;
    border-radius: 8px;
    padding: 1rem 1.2rem;
    color: #4b5563;
    font-size: 0.98rem;
    margin-top: 1.2rem;
}
.note-content ul {
    margin: 0.3rem 0 0 1.2rem;
    padding: 0;
    font-size: var(--font-size-small);
}
.note-label {
    color: #374151;
    font-weight: 500;
    font-size: var(--font-size-base);
}
.video-guide {
    background: #f0fdf4;
    border-radius: 8px;
    padding: 0.7rem 1rem;
    color: #15803d;
    font-size: 0.98rem;
    margin-top: 1rem;
}

.video-guide ul {
    margin: 0.3rem 0 0 1.2rem;
    padding: 0;
    color: #15803d;
    font-size: 0.97rem;
}

.switch-group {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 1.5rem;
}

.switch-input {
    display: none;
}

.switch-label {
    width: 44px;
    height: 24px;
    background: #e5e7eb;
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: background 0.2s;
    margin: 0 0.5rem;
}

.switch-input:checked+.switch-label {
    background: black;
}

.switch-label:before {
    content: '';
    position: absolute;
    left: 4px;
    top: 4px;
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    transition: left 0.2s;
}

.switch-input:checked+.switch-label:before {
    left: 24px;
}

.switch-desc {
    color: #71717a;
    font-size: var(--font-size-small);
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.select-time {
    border-top: 1px solid #e4e4e7;
    padding: 1rem 0 0;
    margin-top: 1rem;
}

.img-preview {
    margin-top: 0.5rem;
    text-align: center;
}
.img-preview img {
    max-width: 100%;
    max-height: 120px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    object-fit: contain;
}
.youtube-preview {
    margin-top: 1rem;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.img-preview-box {
    position: relative;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 0.5rem;
    background: #fff;
    border: 1px solid #e5e7eb;
}
.img-preview-box img {
    width: 100%;
    max-height: 160px;
    object-fit: contain;
    display: block;
    background: #f9fafb;
}
.img-preview-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 0.1rem;
    z-index: 2;
}
.img-preview-actions button {
    background: #ffffffe6;
}

@media (max-width: 700px) {
    .profile-info-form {
        gap: 1.2rem;
        padding: 0.5rem;
    }

    .section {
        padding: 1rem;
    }

    .form-grid {
        gap: 0.7rem 2%;
    }
}
</style>

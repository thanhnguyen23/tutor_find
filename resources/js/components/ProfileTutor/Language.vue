<template>
    <div class="language-section">
        <div class="section-header">
            <div class="header-left">
                <i class="fas fa-language"></i>
                <h3>Ngôn ngữ</h3>
            </div>
            <button class="btn-add" @click="showAddLanguageModal = true">
                <img class="icon-base" src="/images/plus.svg" alt="add" />
                Thêm ngôn ngữ
            </button>
        </div>

        <div class="language-list">
            <div v-for="lang in languages" :key="lang.id" class="language-item">
                <div class="language-info">
                    <div class="language-name">{{ lang.language }}</div>
                    <div class="language-level">
                        <span class="level-badge">{{ lang.level }}</span>
                        <span v-if="lang.is_native" class="native-badge">Bản ngữ</span>
                    </div>
                </div>
                <div class="language-actions">
                    <img src="/images/edit.svg" alt="edit" @click="handleEditLanguage(lang)" />
                    <img src="/images/delete.svg" alt="delete" @click="handleDeleteLanguage(lang.id)" />
                </div>
            </div>
        </div>

        <!-- Add Language Modal -->
        <base-modal
            :is-open="showAddLanguageModal"
            title="Thêm ngôn ngữ"
            description="Thêm ngôn ngữ mới"
            size="small"
            @close="showAddLanguageModal = false"
        >
            <div class="modal-edit">
                <div class="form-group">
                    <label>Ngôn ngữ</label>
                    <base-select
                        v-model="newLanguage.language"
                        :options="languageOptions"
                        placeholder="Chọn ngôn ngữ"
                    />
                </div>

                <div class="form-group">
                    <label>Trình độ</label>
                    <base-select
                        v-model="newLanguage.level"
                        :options="levelOptions"
                        placeholder="Chọn trình độ"
                    />
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            v-model="newLanguage.is_native"
                        >
                        Đây là ngôn ngữ bản ngữ của tôi
                    </label>
                </div>

                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showAddLanguageModal = false">Hủy</button>
                    <button class="btn-md btn-primary" @click="addLanguage">Thêm</button>
                </div>
            </div>
        </base-modal>

        <!-- Edit Language Modal -->
        <base-modal
            :is-open="showEditLanguageModal"
            title="Chỉnh sửa ngôn ngữ"
            description="Chỉnh sửa thông tin ngôn ngữ"
            size="small"
            @close="showEditLanguageModal = false"
        >
            <div class="modal-edit">
                <div class="form-group">
                    <label>Ngôn ngữ</label>
                    <base-select
                        v-model="selectedLanguage.language"
                        :options="languageOptions"
                        placeholder="Chọn ngôn ngữ"
                    />
                </div>

                <div class="form-group">
                    <label>Trình độ</label>
                    <base-select
                        v-model="selectedLanguage.level"
                        :options="levelOptions"
                        placeholder="Chọn trình độ"
                    />
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            v-model="selectedLanguage.is_native"
                        >
                        Đây là ngôn ngữ bản ngữ của tôi
                    </label>
                </div>

                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showEditLanguageModal = false">Hủy</button>
                    <button class="btn-md btn-primary" @click="updateLanguage">Cập nhật</button>
                </div>
            </div>
        </base-modal>

        <!-- Delete Confirmation Modal -->
        <base-modal
            :is-open="showDeleteConfirmModal"
            title="Xác nhận xóa"
            description="Bạn có chắc chắn muốn xóa ngôn ngữ này không?"
            size="small"
            @close="showDeleteConfirmModal = false"
        >
            <div class="delete-confirmation">
                <div class="selected-language">
                    <div class="language-preview">
                        <i class="fas fa-language"></i>
                        <span>{{ selectedLanguage.language }}</span>
                        <span class="level-badge">{{ selectedLanguage.level }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn-md btn-secondary" @click="showDeleteConfirmModal = false">Hủy</button>
                    <button class="btn-md btn-primary" @click="confirmDelete">Xóa</button>
                </div>
            </div>
        </base-modal>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { getCurrentInstance } from 'vue';

const { proxy } = getCurrentInstance();

const props = defineProps({
    userDataDetail: {
        type: Object,
        default: () => ({})
    }
});

const languages = ref([]);
const showAddLanguageModal = ref(false);
const showEditLanguageModal = ref(false);
const showDeleteConfirmModal = ref(false);
const selectedLanguageId = ref(null);

const newLanguage = ref({
    language: '',
    level: '',
    is_native: false
});

const selectedLanguage = ref({
    language: '',
    level: '',
    is_native: false
});

const languageOptions = [
    { id: 'Tiếng Việt', name: 'Tiếng Việt' },
    { id: 'Tiếng Anh', name: 'Tiếng Anh' },
    { id: 'Tiếng Pháp', name: 'Tiếng Pháp' },
    { id: 'Tiếng Nhật', name: 'Tiếng Nhật' },
    { id: 'Tiếng Hàn', name: 'Tiếng Hàn' },
    { id: 'Tiếng Trung', name: 'Tiếng Trung' }
];

const levelOptions = [
    { id: 'Sơ cấp (A1)', name: 'Sơ cấp (A1)' },
    { id: 'Sơ trung cấp (A2)', name: 'Sơ trung cấp (A2)' },
    { id: 'Trung cấp (B1)', name: 'Trung cấp (B1)' },
    { id: 'Trung cao cấp (B2)', name: 'Trung cao cấp (B2)' },
    { id: 'Cao cấp (C1)', name: 'Cao cấp (C1)' },
    { id: 'Thành thạo (C2)', name: 'Thành thạo (C2)' }
];

const getUserLanguages = async () => {
    try {
        const response = await proxy.$api.apiGet('me/languages');
        languages.value = response;
    } catch (error) {
        console.error('Failed to fetch languages:', error);
    }
};

const addLanguage = async () => {
    try {
        await proxy.$api.apiPost('me/languages', newLanguage.value);
        await getUserLanguages();
        showAddLanguageModal.value = false;
        resetNewLanguage();
    } catch (error) {
        console.error('Failed to add language:', error);
    }
};

const handleEditLanguage = (language) => {
    selectedLanguageId.value = language.id;
    selectedLanguage.value = { ...language };
    showEditLanguageModal.value = true;
};

const updateLanguage = async () => {
    try {
        await proxy.$api.apiPut(`me/languages/${selectedLanguageId.value}`, selectedLanguage.value);
        await getUserLanguages();
        showEditLanguageModal.value = false;
    } catch (error) {
        console.error('Failed to update language:', error);
    }
};

const handleDeleteLanguage = (id) => {
    selectedLanguageId.value = id;
    const language = languages.value.find(lang => lang.id === id);
    if (language) {
        selectedLanguage.value = { ...language };
        showDeleteConfirmModal.value = true;
    }
};

const confirmDelete = async () => {
    try {
        await proxy.$api.apiDelete(`me/languages/${selectedLanguageId.value}`);
        await getUserLanguages();
        showDeleteConfirmModal.value = false;
    } catch (error) {
        console.error('Failed to delete language:', error);
    }
};

const resetNewLanguage = () => {
    newLanguage.value = {
        language: '',
        level: '',
        is_native: false
    };
};

onMounted(getUserLanguages);
</script>

<style scoped>
@import url('@css/profile.css');

.language-section {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #E5E7EB;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.header-left i {
    color: #374151;
    font-size: 1.25rem;
}

.header-left h3 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.language-list {
    display: grid;
    gap: 1rem;
}

.language-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #F9FAFB;
    border-radius: 8px;
}

.language-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.language-name {
    font-weight: 500;
    color: #111827;
}

.language-level {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.level-badge {
    background: #E5E7EB;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: var(--font-size-mini);
    color: #374151;
}

.native-badge {
    background: #FEF3C7;
    color: #92400E;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: var(--font-size-mini);
}

.language-actions {
    display: flex;
    gap: 1rem;
}

.language-actions img {
    width: 1rem;
    height: 1rem;
    cursor: pointer;
    opacity: 0.8;
}

.language-actions img:hover {
    opacity: 1;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    user-select: none;
}

.checkbox-label input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
}

.selected-language {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.language-preview {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #374151;
    font-size: var(--font-size-mini);
}

.language-preview i {
    color: #6B7280;
}


.modal-edit {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: #111827;
}

</style>

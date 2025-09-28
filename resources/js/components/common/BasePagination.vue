<script setup>
    defineProps({
        meta: Object
    })

    defineEmits(['changePage'])
</script>

<template>
    <!-- Desktop/Tablet version -->
    <div class="pagination-bar pagination-desktop" v-if="meta">
        <div class="pagination-left">
            <div class="pagination-info">
                Hiển thị {{ meta.from }} - {{ meta.to }} trên {{ meta.total }}
            </div>
            <div class="action">
                <button class="btn-md btn-secondary" :disabled="meta.current_page === 1" @click="$emit('changePage', meta.current_page - 1)">
                    Trang trước
                </button>
                <button class="btn-md btn-secondary" :disabled="meta.current_page === meta.last_page" @click="$emit('changePage', meta.current_page + 1)">
                    Trang sau
                </button>
            </div>
        </div>
        <div class="pagination-controls">
            <div class="pagination-pages">
                <button v-for="page in meta.last_page" :key="page" :class="['btn-md', page === meta.current_page ? 'btn-black' : 'btn-secondary']" @click="$emit('changePage', page)">
                    {{ page }}
                </button>
            </div>
        </div>
        <div class="pagination-right">
            <div class="pagination-summary">
                Trang {{ meta.current_page }} trên {{ meta.last_page }}
            </div>
            <div class="action">
                <button class="btn-md btn-secondary" :disabled="meta.current_page === 1" @click="$emit('changePage', 1)">
                    Trang đầu
                </button>
                <button class="btn-md btn-secondary" :disabled="meta.current_page === meta.last_page" @click="$emit('changePage', meta.last_page)">
                    Trang cuối
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile version -->
    <div class="pagination-bar pagination-mobile" v-if="meta">
        <div class="pagination-left">
            <div class="pagination-info">
                Hiển thị {{ meta.from }} - {{ meta.to }} trên {{ meta.total }}
            </div>
            <div class="action action-prev">
                <button class="btn-md btn-secondary" :disabled="meta.current_page === 1" @click="$emit('changePage', meta.current_page - 1)">
                    Trang trước
                </button>
            </div>
        </div>
        <div class="pagination-controls">
            <div class="pagination-pages">
                <button v-for="page in meta.last_page" :key="page" :class="['btn-md', page === meta.current_page ? 'btn-black' : 'btn-secondary']" @click="$emit('changePage', page)">
                    {{ page }}
                </button>
            </div>
        </div>
        <div class="pagination-right">
            <div class="pagination-summary">
                Trang {{ meta.current_page }} trên {{ meta.last_page }}
            </div>
            <div class="action action-next">
                <button class="btn-md btn-secondary" :disabled="meta.current_page === meta.last_page" @click="$emit('changePage', meta.current_page + 1)">
                    Trang sau
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pagination-bar {
    display: flex;
    align-items: end;
    justify-content: space-between;
    margin-top: 1rem;
    gap: 1.5rem;
}

.pagination-info,
.pagination-summary {
    color: #6b7280;
    font-size: var(--font-size-small);
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}


.pagination-pages {
    display: flex;
    gap: 0.2rem;
}

.pagination-right .action,
.pagination-left .action {
    display: flex;
    gap: 0.5rem;
}

.pagination-right {
    display: flex;
    flex-direction: column;
    align-items: end;
    gap: 0.5rem;
}

.pagination-left {
    display: flex;
    flex-direction: column;
    align-items: start;
    gap: 0.5rem;
}

/* Ẩn/hiện từng phiên bản theo kích thước màn hình */
.pagination-desktop { display: flex; }
.pagination-mobile { display: none; }
@media (max-width: 768px) {
    .pagination-desktop { display: none !important; }
    .pagination-mobile {
        display: flex !important;
        flex-direction: row;
        align-items: end;
        gap: 0.5rem;
    }
    .pagination-left .action:not(.action-prev),
    .pagination-right .action:not(.action-next) {
        display: none !important;
    }
    .pagination-left .action.action-prev {
        display: flex;
    }
    .pagination-right .action.action-next {
        display: flex;
    }
}
</style>

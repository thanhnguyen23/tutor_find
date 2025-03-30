<template>
<section class="search-section">
    <div class="container">
        <div class="search-title-wrapper">
            <div class="search-badge">Đội ngũ chuyên nghiệp</div>
            <h2 class="search-title">Tìm gia sư phù hợp với bạn</h2>
            <p class="search-description">
                Khám phá đội ngũ gia sư chất lượng cao của chúng tôi và tìm người phù hợp nhất với nhu cầu học tập của bạn
            </p>
        </div>

        <div class="search-form">
            <div class="search-row">
                <div class="form-group">
                    <label>Môn học</label>
                    <select class="form-select">
                        <option>Tất cả môn học</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Cấp độ</label>
                    <select class="form-select">
                        <option>Tất cả cấp độ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Kinh nghiệm</label>
                    <select class="form-select">
                        <option>Tất cả</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Đánh giá</label>
                    <select class="form-select">
                        <option>Tất cả đánh giá</option>
                    </select>
                </div>
            </div>

            <div class="search-filters">
                <div class="filter-label">Sắp xếp theo:</div>
                <select class="filter-select">
                    <option>Đánh giá cao nhất</option>
                </select>
                <button class="search-button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Tìm kiếm
                </button>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
</script>

<style scoped>
.search-section {
    padding-top: 60px;
    background: #fff;
}

.search-title-wrapper {
    margin-bottom: 3rem;
    text-align: center;
}

.search-badge {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    border-radius: 2rem;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
}

.search-title {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 12px;
}

.search-description {
    font-size: 16px;
    color: #6b7280;
    margin-bottom: 32px;
    max-width: 700px;
    margin: auto;
}

.search-form {
    max-width: 66rem;
    margin: auto;
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.search-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.form-select {
    width: 100%;
    padding: 11px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: white;
    cursor: pointer;
    transition: all 0.2s;
}

.form-select:hover {
    border-color: #d1d5db;
}

.form-select:focus {
    outline: none;
    border-color: #2563eb;
    ring: 2px solid rgba(37, 99, 235, 0.2);
}

.search-filters {
    display: flex;
    align-items: center;
    gap: 16px;
    padding-top: 16px;
    border-top: 1px solid #e5e7eb;
}

.filter-label {
    font-size: 14px;
    color: #6b7280;
}

.filter-select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background: white;
    cursor: pointer;
    min-width: 180px;
}

.search-button {
    margin-left: auto;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #000;
    color: white;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.search-button:hover {
    background: #1a1a1a;
    transform: translateY(-1px);
}

.search-button svg {
    width: 20px;
    height: 20px;
}

@media (max-width: 1024px) {
    .search-row {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .search-section {
        padding: 48px 0;
    }

    .search-title {
        font-size: 30px;
    }

    .search-filters {
        flex-wrap: wrap;
    }

    .search-button {
        width: 100%;
        justify-content: center;
        margin-top: 16px;
    }
}

@media (max-width: 640px) {
    .search-row {
        grid-template-columns: 1fr;
    }

    .container {
        padding: 0 16px;
    }

    .search-form {
        padding: 16px;
    }
}
</style>

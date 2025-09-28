<template>
<section class="faq-section">
    <div class="container">
        <div class="section-badge">
            <span>Câu hỏi thường gặp</span>
        </div>
        <h2 class="section-title">Giải đáp thắc mắc của bạn</h2>
        <p class="section-description">
            Những câu hỏi thường gặp về dịch vụ gia sư của chúng tôi
        </p>

        <div class="faq-list">
            <div class="faq-item" v-for="(faq, index) in faqs" :key="index">
                <button class="faq-question" @click="toggleFaq(index)" :class="{ 'active': openFaq === index }">
                    {{ faq.question }}
                    <svg class="arrow-icon" :class="{ 'rotated': openFaq === index }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="faq-answer" :class="{ 'active': openFaq === index }">
                    {{ faq.answer }}
                </div>
            </div>
        </div>
    </div>
</section>
</template>

<script setup>
import { ref } from 'vue';

const openFaq = ref(null);

const toggleFaq = (index) => {
    openFaq.value = openFaq.value === index ? null : index;
};

const faqs = [
    {
        question: 'Làm thế nào để tìm gia sư phù hợp?',
        answer: 'Bạn có thể dễ dàng tìm gia sư phù hợp bằng cách sử dụng công cụ tìm kiếm trên trang web của chúng tôi. Chỉ cần chọn môn học, cấp độ và các yêu cầu khác, hệ thống sẽ gợi ý những gia sư phù hợp nhất với nhu cầu của bạn.'
    },
    {
        question: 'Chi phí cho một buổi học là bao nhiêu?',
        answer: 'Chi phí mỗi buổi học phụ thuộc vào trình độ của gia sư, môn học và thời lượng. Thông thường, học phí dao động từ 200.000đ đến 500.000đ cho một buổi học 2 tiếng. Bạn có thể xem chi tiết học phí trong hồ sơ của từng gia sư.'
    },
    {
        question: 'Làm thế nào để đăng ký buổi học đầu tiên?',
        answer: 'Để đăng ký buổi học đầu tiên, bạn cần chọn gia sư phù hợp, chọn thời gian học trong lịch trống của gia sư và thanh toán đặt cọc. Sau khi hoàn tất, gia sư sẽ liên hệ với bạn để xác nhận và bắt đầu buổi học.'
    },
    {
        question: 'Nếu tôi không hài lòng với gia sư thì sao?',
        answer: 'Nếu bạn không hài lòng với gia sư, bạn có thể phản hồi cho chúng tôi sau buổi học đầu tiên. Chúng tôi sẽ tìm gia sư khác phù hợp hơn hoặc hoàn lại tiền đặt cọc của bạn trong vòng 24 giờ.'
    },
    {
        question: 'Gia sư có thể dạy trực tuyến không?',
        answer: 'Có, hầu hết gia sư của chúng tôi đều có thể dạy trực tuyến thông qua các nền tảng học trực tuyến phổ biến như Zoom, Google Meet. Bạn có thể lựa chọn hình thức học phù hợp khi đăng ký.'
    }
];
</script>

<style scoped>
.faq-section {
    background: white;
}

.section-badge {
    margin: auto;
    width: 100%;
    text-align: center;
}

.section-badge span {
    display: inline-block;
    padding: 6px 16px;
    background: #f3f4f6;
    border-radius: 9999px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 16px;
    text-align: center;
}

.section-title {
    font-size: 36px;
    font-weight: 700;
    text-align: center;
    margin-bottom: 16px;
    color: #111827;
}

.section-description {
    font-size: 16px;
    color: #6b7280;
    text-align: center;
    max-width: 800px;
    margin: 0 auto 48px;
}

.faq-list {
    max-width: 800px;
    margin: auto;
}

.faq-item {
    margin-bottom: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}

.faq-question {
    width: 100%;
    padding: 20px;
    background: white;
    border: none;
    text-align: left;
    font-size: 16px;
    font-weight: 500;
    color: #111827;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: all 0.3s ease;
}

.faq-question:hover {
    background: #f9fafb;
}

.faq-question.active {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.arrow-icon {
    width: 20px;
    height: 20px;
    transition: transform 0.3s ease;
}

.arrow-icon.rotated {
    transform: rotate(180deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background: white;
    padding: 0 20px;
    color: #4b5563;
    line-height: 1.6;
}

.faq-answer.active {
    max-height: 300px;
    padding: 20px;
}

@media (max-width: 768px) {
    .faq-section {
        padding: 3rem 0;
    }

    .section-title {
        font-size: 30px;
    }

    .faq-question {
        font-size: 15px;
        padding: 16px;
    }

    .faq-answer.active {
        padding: 16px;
    }
}

@media (max-width: 640px) {
    .container {
        padding: 0 16px;
    }

    .section-title {
        font-size: 24px;
    }
}
</style>

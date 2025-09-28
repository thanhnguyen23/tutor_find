<template>
<!-- Loading overlay -->
<base-loading v-if="isLoading" />

<div class="home-container" v-if="!isLoading">
    <!-- Hero Section -->
    <section class="hero-section">
        <HeroSection />
    </section>

    <div>
        <!-- Search Section -->
        <section class="search-section">
            <SearchSection />
        </section>

        <!-- Featured Tutors Section -->
        <section class="tutors-section">
            <TutorsSection />
        </section>
    </div>

    <!-- <section class="why-choose-us-section">
        <WhyChooseUsSection />
    </section> -->

    <!-- Student Reviews Section -->
    <section class="reviews-section">
        <ReviewsSection />
    </section>

    <section class="register-section">
        <RegisterSection />
    </section>
</div>
</template>

<script setup>
import {
    ref,
    onMounted
} from 'vue'
import HeroSection from '../Components/Home/HeroSection.vue';
import TutorsSection from '../components/Home/TutorsSection.vue';
import SearchSection from '../components/Home/SearchSection.vue';
import WhyChooseUsSection from '../Components/Home/WhyChooseUsSection.vue';
import ReviewsSection from '../Components/Home/ReviewsSection.vue';
import RegisterSection from '../Components/Home/RegisterSection.vue';

const isLoading = ref(true);

onMounted(() => {
    // Simulate loading time for home page
    setTimeout(() => {
        isLoading.value = false;
    }, 1000);
});
</script>

<style>
:root {
    --icon-base-size: 1.2rem;
}
</style>

<style scoped>
section {
    padding: 2rem 0;
}
.home-container {
    display: grid;
    gap: 3rem;
}
/* Updated Hero Section Styles */
.hero-section {
    width: 100%;
    background: white;
}
.hero-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4rem 2rem;
    position: relative;
    overflow: hidden;
    margin: 0 auto;
}

.hero-content {
    flex: 1;
    max-width: 600px;
    z-index: 2;
}

.hero-title {
    font-size: var(--font-size-heading-1);
    line-height: 1.2;
    font-weight: 800;
    color: #111827;
    margin-bottom: 1.5rem;
}

.hero-subtitle {
    font-size: 1.125rem;
    color: #6b7280;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-buttons {
    display: flex;
    gap: 1rem;
}

.btn-find-tutor {
    background: #000;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s;
}

.btn-become-tutor {
    color: #000;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    background: white;
}

.hero-image {
    flex: 1;
    position: relative;
    height: 500px;
}

.circle-bg {
    position: absolute;
    width: 400px;
    height: 400px;
    background: #e5e7eb;
    border-radius: 50%;
    right: 0;
    top: 50%;
    left: 100%;
    transform: translate(-100%, -50%);
}

.tutor-bubble {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: white;
    padding: 1rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: absolute;
    z-index: 3;
}

.tutor-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #000;
}

.tutor-info {
    display: flex;
    flex-direction: column;
}

.tutor-title {
    font-weight: 600;
    color: #111827;
}

.tutor-exp {
    font-size: 0.875rem;
    color: #6b7280;
}

.tutor-bubble.english {
    top: -1%;
    right: 1%;
}

.tutor-bubble.math {
    top: 39%;
    right: 28%;
}

.tutor-bubble.science {
    top: 90%;
    right: 7%;
}

.reviews-section {
    margin: 0 auto;
}

.register-section {
    background: #1a1a1a;
    color: white;
}

.tutors-section {
    padding: 0 0 1rem;
}
.tutors-section, .search-section {
    margin: 0 auto;
    background: #fbfbfc;
}

@media (max-width: 640px) {
    .home-container {
        gap: 2rem;
    }

    section {
        padding: 1rem 0;
    }
}
</style>

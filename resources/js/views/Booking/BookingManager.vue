<script setup>
import {
    ref,
    computed,
    onMounted,
    getCurrentInstance,
    watch
} from 'vue';
import { useStore } from 'vuex';
import BookingManagerUser from './BookingManagerUser.vue';
import BookingManagerTutor from './BookingManagerTutor.vue';

const store = useStore();
const userData = store.state.userData;
</script>

<template>
    <BookingManagerUser v-if="userData.role === 0"></BookingManagerUser>
    <BookingManagerTutor v-if="userData.role === 1"></BookingManagerTutor>
</template>

<style scoped>
</style>

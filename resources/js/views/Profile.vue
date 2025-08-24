<script setup>
import {
    ref,
    computed,
    onMounted,
    getCurrentInstance,
    watch
} from 'vue';
import { useStore } from 'vuex';
import ProfileUser from './ProfileUser.vue';
import ProfileTutor from './ProfileTutor.vue';

const store = useStore();
const userData = store.state.userData;
</script>

<template>
    <ProfileUser v-if="userData.role === 0"></ProfileUser>
    <ProfileTutor v-if="userData.role === 1"></ProfileTutor>
</template>

<style scoped>
</style>

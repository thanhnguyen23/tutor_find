<script setup>
import Footer from "./Footer.vue";
import Header from "./Header.vue";
import { useRoute } from "vue-router";
import { getCurrentInstance, onMounted } from "vue";
import { useStore } from "vuex";
import Notification from "../Notification.vue";

const store = useStore();
const route = useRoute();
const {proxy} = getCurrentInstance();

const getConfiguration = async () => {
    const configuration = proxy.$helper.getDataLocalStorage('configuration');

    if (configuration) {
        store.commit('setConfiguration', configuration);
        return;
    }

    const response = await proxy.$api.apiGet('configurations');
    proxy.$helper.setDataLocalStorage('configuration', response);
}

onMounted(() => {
    getConfiguration();
});
</script>

<template>
<div>
    <layout class="layout-main" v-if="!route.meta.noLayout">
        <Notification />

        <Header />

        <main>
            <router-view />
        </main>

        <Footer />
    </layout>

    <router-view v-else />
</div>
</template>
<style>
@import url(@css/app.css);
</style>

import './bootstrap';


import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';
import DefaultLayout from '@/components/layout/Main.vue';
import Antd from 'ant-design-vue';
import store from './store';
import api from './api/index';
import helper from './utils/helper';
import baseModal from '@/components/BaseModal.vue';
// import 'ant-design-vue/dist/antd.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'flag-icon-css/css/flag-icons.min.css';

const app = createApp(App);

app.config.globalProperties.$api = api;
app.config.globalProperties.$helper = helper;
app.use(store); // Sử dụng Vuex store
app.component("layout-default", DefaultLayout);
app.component("base-modal", baseModal);
app.use(Antd);
app.use(router);

app.mount("#app")

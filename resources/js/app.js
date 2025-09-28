import './bootstrap';


import { createApp } from 'vue';
import App from './App.vue';
import router from './router/index';
import DefaultLayout from '@/components/layout/Main.vue';
// import Antd from 'ant-design-vue';
import store from './store';
import api from './api/index';
import helper from './utils/helper';
import baseModal from '@/components/common/BaseModal.vue';
import BaseInput from '@/components/common/BaseInput.vue';
import BaseSelect from '@/components/common/BaseSelect.vue';
import BasePagination from './components/common/BasePagination.vue';
import BaseDatePicker from './components/common/BaseDatePicker.vue';
import BaseMoreMenu from './components/common/BaseMoreMenu.vue';
import BaseLoading from './components/common/BaseLoading.vue';
import notification from './utils/notification';
import { Readable } from 'readable-stream';
// import 'ant-design-vue/dist/antd.css';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'flag-icon-css/css/flag-icons.min.css';
const app = createApp(App);

app.config.globalProperties.$api = api;
app.config.globalProperties.$helper = helper;
app.config.globalProperties.$notification = notification;

app.use(store); // Sử dụng Vuex store
app.component("layout-default", DefaultLayout);
app.component("base-modal", baseModal);
app.component("base-input", BaseInput);
app.component("base-select", BaseSelect);
app.component("base-pagination", BasePagination);
app.component("base-datepicker", BaseDatePicker);
app.component("base-more-menu", BaseMoreMenu);
app.component("base-loading", BaseLoading);
// app.use(Antd);
app.use(router);
app.use(Readable);

app.mount("#app")

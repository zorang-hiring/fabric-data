import { createApp } from 'vue'
import App from './App.vue'

import mitt from 'mitt';
const emitter = mitt();

import axios from 'axios';

import VueLoading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';

const app = createApp(App)
app.config.globalProperties.emitter = emitter;
app.config.globalProperties.exios = axios;
app.use(VueLoading);
app.mount('#app');

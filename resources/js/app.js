import './bootstrap';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import { useAutoPaperStore } from './stores/autoPaperStore';

const app = createApp(App);
const pinia = createPinia();
app.use(pinia);
app.mount('#app');

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// frontend/src/app.js
import { createApp } from 'vue';
import App from './AdminApp.vue';
import router from './router/routes-admin';
import axios from './utils/axios';

// Делаем axios доступным глобально
window.axios = axios;

const app = createApp(App);
app.use(router);

router.isReady().then(() => {
  app.mount('#app');
});

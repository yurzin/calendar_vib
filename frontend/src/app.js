// frontend/src/app.js
import { createApp } from 'vue';
import App from './App.vue';
import router from './router/routes';
import axios from './utils/axios';
import clickOutside from './directives/clickOutside';

// Делаем axios доступным глобально
window.axios = axios;

const app = createApp(App);
app.use(router);
app.directive('click-outside', clickOutside);

router.isReady().then(() => {
  app.mount('#app');
});

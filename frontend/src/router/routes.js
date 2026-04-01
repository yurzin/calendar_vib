import { createRouter, createWebHistory } from 'vue-router';
import axios from '../utils/axios';

const routes = [
  {
    path: '/',
    component: () => import('../views/Pages/View/Welcome.vue'),
    name: 'welcome'
  },
  {
    path: '/login',
    component: () => import('../views/Pages/Auth/Login.vue'),
    name: 'login'
  },
  {
    path: '/register',
    component: () => import('../views/Pages/Auth/Register.vue'),
    name: 'register'
  },
  {
    path: '/dashboard',
    component: () => import('../views/Pages/Admin/Dashboard.vue'),
    name: 'dashboard'
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Пока убираем все редиректы
router.beforeEach(async (to, from) => {
  // Просто логируем переходы для отладки
  console.log('Переход с:', from.path, 'на:', to.path);

  // Пока пропускаем всё без проверок
  return true;
});

export default router;

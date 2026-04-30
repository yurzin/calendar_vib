import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@/composable/useAuth.js';

const routes = [
  {
    path: '/login',
    component: () => import('../views/Pages/Auth/Login.vue'),
    name: 'login',
    meta: { public: true }
  },
  {
    path: '/',
    component: () => import('../views/Pages/Admin/Dashboard.vue'),
    name: 'admin',
    meta: { requiresAuth: true }
  },
  {
    path: '/persons',
    component: () => import('../views/Pages/Admin/Persons.vue'),
    name: 'persons',
    meta: { requiresAuth: true }
  },
  {
    path: '/partners',
    component: () => import('../views/Pages/Admin/Partners.vue'),
    name: 'partners',
    meta: { requiresAuth: true }
  },
  {
    path: '/calendar',
    beforeEnter: () => {
      window.location.href = 'http://calendar.local';
    },
    component: { template: '<div></div>' },
  },
  {
    path: '/user',
    component: { template: '<div></div>' },
    name: 'user'
  },
  {
    path: '/dashboard',
    component: { template: '<div></div>' },
    name: 'dashboard'
  },
  {
    path: '/profile',
    component: { template: '<div></div>' },
    name: 'profile'
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
  // Публичные маршруты пропускаем
  if (to.meta.public) {
    return true;
  }

  // Только защищённые маршруты проверяем
  if (to.meta.requiresAuth) {
    const { checkAuth, user } = useAuth();

    if (!user.value) {
      await checkAuth();
    }

    if (!user.value) {
      return { name: 'login' };
    }
  }

  return true;
});

export default router;

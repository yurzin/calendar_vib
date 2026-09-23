import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '@/composable/useAuth';
import { getMainSiteUrl } from '@/utils/site';

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
    path: '/archive',
    component: () => import('../views/Pages/Admin/Archive.vue'),
    name: 'archive',
    meta: { requiresAuth: true }
  },
  {
    path: '/calendar',
    beforeEnter: () => {
      window.location.href = getMainSiteUrl();
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
  if (to.meta.public) {
    // Уже залогинен — не показываем страницу логина
    if (to.name === 'login') {
      const { checkAuth, user } = useAuth();
      if (!user.value) await checkAuth();

      if (user.value) {
        const allowedRoles = ['admin', 'editor'];
        const hasAccess = user.value?.roles?.some(r => allowedRoles.includes(r));

        // Есть доступ — в дашборд, нет — на основной сайт
        return hasAccess
          ? { name: 'admin' }
          : { path: window.location.href = getMainSiteUrl() };
      }
    }
    return true;
  }

  if (to.meta.requiresAuth) {
    const { checkAuth, user } = useAuth();
    if (!user.value) await checkAuth();

    if (!user.value) {
      return { name: 'login' };
    }

    const allowedRoles = ['admin', 'editor'];
    const hasAccess = user.value?.roles?.some(r => allowedRoles.includes(r));

    if (!hasAccess) {
      // Залогинен, но не админ — на основной сайт, не на логин
      window.location.href = getMainSiteUrl();
      return false;
    }
  }

  return true;
});

export default router;

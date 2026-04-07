import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    component: () => import('../views/Pages/Admin/Dashboard.vue'),
    name: 'admin'
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
    path: '/persons',
      component: () => import('../views/Pages/Admin/Persons.vue'),
    name: 'persons'
  },
  {
    path: '/partners',
    component: () => import('../views/Pages/Admin/Partners.vue'),
    name: 'partners'
  },
  {
    path: '/profile',
    component: { template: '<div></div>' },
    name: 'profile'
  },
  {
    path: '/calendar',
    beforeEnter: () => {
      window.location.href = 'http://calendar.local';
    },
    component: { template: '<div></div>' },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from) => {
  return true;
});

export default router;

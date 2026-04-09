import { createRouter, createWebHistory } from 'vue-router';

const routes = [
  {
    path: '/',
    component: () => import('../views/Pages/View/Welcome.vue'),
    name: 'welcome'
  },
  {
    path: '/user',
    component: { template: '<div></div>' },
    name: 'user'
  },
  {
    path: '/main',
    component: () => import('../views/Pages/View/Welcome.vue'),
    name: 'main'
  },
  {
    path: '/members',
    component: () => import('../views/Pages/View/Members.vue'),
    name: 'members'
  },
  {
    path: '/grid',
    component: () => import('../views/Pages/View/Components/Grid.vue'),
    name: 'grid'
  },
  {
    path: '/general-partner',
    component: () => import('../views/Pages/View/GeneralPartner.vue'),
    name: 'general-partner'
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
    path: '/logout',
    component: () => import('../views/Pages/View/Welcome.vue'),
    name: 'logout'
  },
  {
    path: '/admin',
    beforeEnter: () => {
      window.location.href = 'http://admin.calendar.local';
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

import { createRouter, createWebHistory } from 'vue-router';
import { getAdminSiteUrl } from '@/utils/site';

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
    path: '/official-partner',
    component: () => import('../views/Pages/View/OfficialPartner.vue'),
    name: 'official-partner'
  },
  {
    path: '/business-partner',
    component: () => import('../views/Pages/View/BusinessPartner.vue'),
    name: 'business-partner'
  },
  {
    path: '/project-participant',
    component: () => import('../views/Pages/View/ProjectParticipant.vue'),
    name: 'project-participant'
  },
  {
    path: '/consent-personal-data',
    component: () => import('../views/Pages/View/ConsentPersonalData.vue'),
    name: 'consent-personal-data'
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
      window.location.href = getAdminSiteUrl();
    },
    component: { template: '<div></div>' },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to) {
    if (to.hash) {
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve({ el: to.hash, behavior: 'smooth' });
        }, 300);
      });
    }
    return { top: 0 };
  },
});

router.beforeEach(() => {
  return true;
});

export default router;

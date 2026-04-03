<script setup>
import {computed, defineAsyncComponent } from 'vue';
import { useRoute } from 'vue-router';

const router = useRoute();

const layouts = {
  GuestLayout: defineAsyncComponent(() => import('@/views/Pages/View/LandingPage.vue')),
  AuthenticatedLayout: defineAsyncComponent(() => import('@/views/Layouts/AuthenticatedLayout.vue')),
};

const layout = computed(() => {
  const layoutName = router.meta.layout || 'AuthenticatedLayout';
  return layouts[layoutName] || layouts.DefaultLayout;
});
</script>

<template>
  <div id="app">
    <router-view v-slot="{ Component }">
      <component :is="Component" />
    </router-view>
  </div>
</template>

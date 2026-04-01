<script setup>
import {computed, defineAsyncComponent } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const layouts = {
  GuestLayout: defineAsyncComponent(() => import('./views/Pages/Layouts/GuestLayout.vue')),
  AuthenticatedLayout: defineAsyncComponent(() => import('./views/Pages/Layouts/AuthenticatedLayout.vue')),
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

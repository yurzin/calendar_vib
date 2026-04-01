<script setup>
import { onMounted } from 'vue';
import { useAuth } from '@/composable/useAuth.js';
import GuestLayout from '../Layouts/GuestLayout.vue';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';

const { user, checkAuth, loading } = useAuth();

onMounted(async () => {
  await checkAuth();
});
</script>

<template>
  <div>
    <div v-if="loading">Loading...</div>
    <AuthenticatedLayout v-else-if="user?.name">
      <h1>Welcome, {{ user.name }}</h1>
    </AuthenticatedLayout>
    <GuestLayout v-else>
      <h1>Хочу увидеть себя<span class="gl-stat-num">в календаре</span></h1>
    </GuestLayout>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');
.gl-stat-num {
  display: block;
  font-family: 'Cormorant Garamond', serif;
  font-size: 34px; font-weight: 700;
  color: #93c5fd; line-height: 1; letter-spacing: -0.02em;
  margin-bottom: 10px;
}

</style>

<script setup>
import ScrollTopButton from '@/views/Pages/View/Components/ScrollTopButton.vue'
</script>

<template>
    <router-view v-slot="{ Component }">
      <component :is="Component" />
    </router-view>
    <ScrollTopButton />
</template>



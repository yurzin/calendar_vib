<script setup lang="ts">
import AdGeo from './AdGeo.vue'
import type { AdLayout } from '@/composable/tiers'

defineProps<{
  cover: boolean
  layout: AdLayout
  tooltipTitle: string
  tooltipText: string
  startYear: number
}>()
</script>

<template>
  <div
    class="ad-page"
    :class="{
      'ad-page--placement': !cover && layout === 'full',
      'ad-page--half': !cover && layout === 'half',
    }"
  >
    <div v-if="!cover && layout === 'half'" class="ad-half-free" aria-hidden="true" />

    <div class="ad-right">
      <div v-if="cover" class="ad-cover-title">
        Власть и Бизнес<br><span class="ad-headline-accent">{{ startYear }}</span>
      </div>
      <div v-else class="ad-placement-marker">
        <div class="placement-tooltip">
          <span class="placement-tooltip-count">{{ tooltipTitle }}</span>
          {{ tooltipText }}
        </div>
      </div>

      <AdGeo />

      <!-- ad-logo-area и ad-stats: блоки из прежних файлов без изменений -->
    </div>
  </div>
</template>

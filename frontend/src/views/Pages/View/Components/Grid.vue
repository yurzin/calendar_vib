<script setup lang="ts">
import { ref, computed } from 'vue'
import { formatNumber } from '@/utils/format'
import { START_YEAR, MONTH_NAMES } from '@/composable/useMonthCalendar'
import type { Participation } from '@/composable/useParticipation'
import type { TierConfig } from '@/composable/tiers'
import PartnerTiersNav from '@/views/Pages/View/Components/PartnerTiersNav.vue'
import AdPage from './AdPage.vue'
import CalendarGrid from './CalendarGrid.vue'
import './tier-grid.css'

const props = defineProps<{ participation: Participation; tier: TierConfig }>()

const coverOffset = props.tier.hasCover ? 1 : 0
const totalPages = coverOffset + MONTH_NAMES.length * 2
const currentPage = ref(0)

type Page =
  | { type: 'cover'; title: string }
  | { type: 'ad'; title: string }
  | { type: 'calendar'; title: string; month: number }

const page = computed<Page>(() => {
  if (props.tier.hasCover && currentPage.value === 0) {
    return { type: 'cover', title: 'Обложка - место для рекламного макета генерального партнёра' }
  }
  const offset = currentPage.value - coverOffset
  const month = Math.floor(offset / 2)
  if (offset % 2 === 1) return { type: 'ad', title: props.tier.adPageName }
  return { type: 'calendar', title: `${MONTH_NAMES[month]} ${START_YEAR}`, month }
})

const prevPage = () => { if (currentPage.value > 0) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages - 1) currentPage.value++ }
</script>

<template>
  <div class="gl-section gl-section--dark" :id="tier.slug">
    <div class="gl-container">
      <div class="gl-section-head">
        <router-link to="/#integration" class="gl-back-link">← Все форматы участия</router-link>
        <div class="gl-section-head-top">
          <div class="gl-section-head-main">
            <h2 class="gl-section-title">{{ participation.name }}</h2>
          </div>
          <div class="gl-section-price">
            <span class="gl-section-price-lbl">Стоимость участия</span>
            <span class="gl-section-price-val">{{ formatNumber(participation.price) }} ₽</span>
          </div>
          <div class="gl-section-headline">
            {{ tier.headline }} <span class="gl-section-headline-accent">{{ tier.headlineAccent }}</span>
          </div>
        </div>
        <p class="gl-section-body">{{ tier.body }}</p>
      </div>

      <div class="grid-wrapper">
        <div class="grid-header">
          <div class="month-selector">
            <button class="month-nav" :disabled="currentPage === 0" @click="prevPage">←</button>
            <span class="current-month">{{ page.title }}</span>
            <button class="month-nav" :disabled="currentPage === totalPages - 1" @click="nextPage">→</button>
          </div>
          <span class="page-indicator">Стр. {{ currentPage + 1 }} / {{ totalPages }}</span>
        </div>

        <CalendarGrid
          v-if="page.type === 'calendar'"
          :year="START_YEAR"
          :month="page.month"
          :tier="tier"
        />
        <AdPage
          v-else
          :cover="page.type === 'cover'"
          :layout="tier.adLayout"
          :tooltip-title="tier.adTooltipTitle"
          :tooltip-text="tier.adTooltipText"
          :start-year="START_YEAR"
        />
      </div>

      <PartnerTiersNav :current="tier.slug" />
    </div>
  </div>
</template>

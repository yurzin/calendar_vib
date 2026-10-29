<script setup lang="ts">
import { useMonthCalendar } from '@/composable/useMonthCalendar'
import { placementsLabel } from '@/utils/format'
import type { TierConfig } from '@/composable/tiers'

const props = defineProps<{ year: number; month: number; tier: TierConfig }>()

const { columns, weekdays, isPlacementFirst, cellClasses } =
  useMonthCalendar(() => props.year, () => props.month)
</script>

<template>
  <div class="calendar-grid">
    <div class="weekdays-vertical">
      <div v-for="(wd, idx) in weekdays" :key="idx" class="weekday-vertical">{{ wd }}</div>
    </div>

    <div class="calendar-days-vertical">
      <div v-for="(col, ci) in columns" :key="ci" class="calendar-col">
        <div
          v-for="(day, rowIdx) in col"
          :key="rowIdx"
          class="calendar-day"
          :class="cellClasses(day, rowIdx, ci)"
        >
          <span v-if="day" class="day-number">{{ day.day }}</span>

          <div v-if="day && cellClasses(day, rowIdx, ci)['is-special']" class="special-tooltip">
            <span class="special-tooltip-count">{{ placementsLabel(tier.specialDays) }}</span>
            Знаменательная дата — день рождения компании, руководителя, сотрудников,
            профессионального праздника
          </div>

          <div v-if="!day && isPlacementFirst(rowIdx, ci)" class="placement-tooltip">
            <span class="placement-tooltip-count">{{ placementsLabel(tier.gridPlacements) }}</span>
            {{ tier.gridPlacementText }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<template>
  <div class="gl-section gl-section--dark" id="general-partner">
    <div class="gl-container">
      <div class="gl-section-head">
        <div class="gl-section-line" />
        <h2 class="gl-section-title">Генеральный партнер</h2>
        <p class="gl-section-sub">Настольный календарь-справочник - {{ new Date().getFullYear() + 1 }}</p>
      </div>

      <div class="grid-wrapper">
        <div class="grid-header">
          <div class="month-selector">
            <button @click="prevPage" class="month-nav" :disabled="currentPage === 0">←</button>
            <span class="current-month">{{ pageLabel }}</span>
            <button @click="nextPage" class="month-nav" :disabled="currentPage === totalPages - 1">→</button>
          </div>
          <span class="page-indicator">Стр. {{ currentPage + 1 }} / {{ totalPages }}</span>
        </div>

        <!-- Рекламная страница-заглушка -->
        <div v-if="currentPageInfo.type === 'ad'" class="ad-page">
          <div class="ad-badge">Рекламная страница</div>
          <div class="ad-icon">
            <div class="ad-icon-inner">▲</div>
          </div>
          <div class="ad-title">{{ currentPageInfo.adName }}</div>
          <div class="ad-sub">Место для рекламного макета партнёра. Размер полосы — A4 (210 × 297 мм).</div>
          <div class="ad-dims">210 × 297 мм · 300 dpi · CMYK</div>
        </div>

        <!-- Страница с календарём -->
        <div v-else class="calendar-grid">
          <!-- Дни недели — реальные названия для каждой строки текущего месяца -->
          <div class="weekdays-vertical">
            <div
              class="weekday-vertical"
              v-for="(wd, idx) in activeWeekdays"
              :key="idx"
            >{{ wd }}</div>
          </div>

          <div class="calendar-days-vertical">
            <div class="calendar-col" v-for="(col, ci) in columns" :key="ci">
              <div
                v-for="(day, rowIdx) in col"
                :key="rowIdx"
                class="calendar-day"
                :class="{
                  'is-today': day && isToday(day.date),
                  'is-weekend': day && isWeekend(day.date),
                  'empty': !day
                }"
              >
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'

interface CalendarDay {
  day: number
  date: Date
}

type PageType = 'ad' | 'calendar'

interface PageInfo {
  type: PageType
  label: string
  adName?: string
  month?: number
  year?: number
}

const START_YEAR = 2027
const TOTAL_MONTHS = 12

const monthNames = [
  'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
]

// Пн–Вс в порядке отображения строк (индекс 0 = верхняя строка)
const weekdayNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье']

// Структура страниц:
// 0            — реклама «Обложка»
// 1            — Январь (календарь)
// 2            — реклама «Январь»
// 3            — Февраль (календарь)
// 4            — реклама «Февраль»
// ...
// итого 1 + 12×2 = 25 страниц
const totalPages = 1 + TOTAL_MONTHS * 2

const currentPage = ref(0)

const currentPageInfo = computed<PageInfo>(() => {
  const page = currentPage.value
  if (page === 0) {
    return { type: 'ad', label: 'Обложка', adName: 'Обложка' }
  }
  const idx = page - 1
  const monthIdx = Math.floor(idx / 2)
  const isAd = idx % 2 === 1
  if (isAd) {
    return { type: 'ad', label: monthNames[monthIdx], adName: monthNames[monthIdx] }
  }
  return {
    type: 'calendar',
    label: `${monthNames[monthIdx]} ${START_YEAR}`,
    month: monthIdx,
    year: START_YEAR
  }
})

const pageLabel = computed(() => currentPageInfo.value.label)

// Строит 6 колонок по 7 строк (колонка = неделя, строка = день недели пн–вс)
const getMonthColumns = (year: number, month: number): (CalendarDay | null)[][] => {
  const firstDay = new Date(year, month, 1)
  const daysInMonth = new Date(year, month + 1, 0).getDate()

  // смещение: сколько пустых строк в первой колонке (пн=0, вс=6)
  let startOffset = firstDay.getDay()
  if (startOffset === 0) startOffset = 7
  startOffset--

  const cols: (CalendarDay | null)[][] = Array.from({ length: 6 }, () => Array(7).fill(null))

  let day = 1
  for (let c = 0; c < 6; c++) {
    for (let r = 0; r < 7; r++) {
      if (c === 0 && r < startOffset) continue
      if (day <= daysInMonth) {
        cols[c][r] = { day, date: new Date(year, month, day) }
        day++
      }
    }
  }
  return cols
}

const columns = computed(() => {
  const info = currentPageInfo.value
  if (info.type !== 'calendar') return []
  return getMonthColumns(info.year!, info.month!)
})

// Динамические названия дней недели: для каждой строки (0–6) берём реальный
// день недели первой непустой даты в этой строке — корректно для любого месяца
const activeWeekdays = computed(() => {
  const cols = columns.value
  if (!cols.length) return weekdayNames

  return Array.from({ length: 7 }, (_, rowIdx) => {
    for (const col of cols) {
      if (col[rowIdx]) {
        const jsDay = col[rowIdx]!.date.getDay() // 0=вс, 1=пн, ..., 6=сб
        const monBased = jsDay === 0 ? 6 : jsDay - 1  // 0=пн, ..., 6=вс
        return weekdayNames[monBased]
      }
    }
    return weekdayNames[rowIdx]
  })
})

const isToday = (date: Date): boolean => {
  const today = new Date()
  return (
    date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
  )
}

// Суббота (6) и воскресенье (0)
const isWeekend = (date: Date): boolean => {
  const d = date.getDay()
  return d === 0 || d === 6
}

const prevPage = () => { if (currentPage.value > 0) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages - 1) currentPage.value++ }
</script>

<style scoped>
.gl-container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 64px;
  width: 100%;
}

@media (max-width: 900px) { .gl-container { padding: 0 32px; } }
@media (max-width: 480px) { .gl-container { padding: 0 20px; } }

/* Соотношение сторон 3:5 (ширина:высота) */
.grid-wrapper {
  aspect-ratio: 5 / 3;
  background: rgba(13, 21, 48, 0.4);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 16px;
  padding: 24px;
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.grid-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-shrink: 0;
  flex-wrap: wrap;
  gap: 12px;
}

.page-indicator {
  font-size: 12px;
  color: #64748b;
}

.month-selector {
  display: flex;
  align-items: center;
  gap: 16px;
  background: rgba(13, 21, 48, 0.6);
  border: 1px solid rgba(96, 165, 250, 0.15);
  border-radius: 40px;
  padding: 6px 16px;
}

.month-nav {
  background: transparent;
  border: none;
  color: #93c5fd;
  font-size: 18px;
  cursor: pointer;
  padding: 4px 12px;
  border-radius: 20px;
  transition: all 0.2s;
}

.month-nav:hover:not(:disabled) {
  background: rgba(147, 197, 253, 0.1);
  color: #bfdbfe;
}

.month-nav:disabled {
  opacity: 0.3;
  cursor: default;
}

.current-month {
  font-size: 15px;
  font-weight: 500;
  color: #dce8f5;
  min-width: 170px;
  text-align: center;
}

/* ─── Рекламная заглушка ─────────────────────────── */
.ad-page {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 20px;
  border: 2px dashed rgba(96, 165, 250, 0.25);
  border-radius: 16px;
  background: rgba(13, 21, 48, 0.3);
}

.ad-badge {
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #3b82f6;
  background: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 20px;
  padding: 4px 14px;
}

.ad-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 2px dashed rgba(96, 165, 250, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
}

.ad-icon-inner {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  background: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  color: #3b82f6;
}

.ad-title {
  font-size: 22px;
  font-weight: 500;
  color: #dce8f5;
  text-align: center;
}

.ad-sub {
  font-size: 13px;
  color: #64748b;
  text-align: center;
  max-width: 280px;
  line-height: 1.6;
}

.ad-dims {
  font-size: 12px;
  color: #475569;
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 8px;
  padding: 6px 16px;
  letter-spacing: 0.05em;
}

/* ─── Календарная сетка ──────────────────────────── */
.calendar-grid {
  flex: 1;
  display: flex;
  gap: 8px;
  min-height: 0;
}

.weekdays-vertical {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex-shrink: 0;
  width: 110px;
}

.weekday-vertical {
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 1;
  text-align: center;
  font-size: 11px;
  font-weight: 600;
  color: #3b82f6;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  background: rgba(59, 130, 246, 0.08);
  border-radius: 10px;
  padding: 6px 8px;
  word-break: break-word;
}

.calendar-days-vertical {
  display: flex;
  gap: 6px;
  flex: 1;
  min-width: 0;
}

.calendar-col {
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex: 1;
  min-width: 0;
}

.calendar-day {
  flex: 1;
  background: rgba(13, 21, 48, 0.5);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 10px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-day:hover:not(.empty) {
  border-color: rgba(96, 165, 250, 0.3);
  background: rgba(13, 21, 48, 0.7);
  transform: translateY(-1px);
}

.calendar-day.is-today {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.15);
  box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.2);
}

.calendar-day.is-weekend .day-number {
  color: #f87171;
}

.calendar-day.empty {
  flex: 1;
  background: rgba(13, 21, 48, 0.5);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 10px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.day-number {
  font-size: 15px;
  font-weight: 500;
  color: #dce8f5;
  text-align: center;
}

/* ─── Адаптивность ───────────────────────────────── */
@media (max-width: 900px) {
  .grid-wrapper { padding: 16px; }
  .weekdays-vertical { width: 88px; }
  .weekday-vertical { font-size: 10px; }
  .day-number { font-size: 13px; }
}

@media (max-width: 768px) {
  .weekdays-vertical { width: 76px; }
  .weekday-vertical { font-size: 9px; }
  .day-number { font-size: 12px; }
}

@media (max-width: 640px) {
  .weekdays-vertical { width: 64px; }
  .weekday-vertical { font-size: 8px; border-radius: 8px; }
  .calendar-grid { gap: 4px; }
  .calendar-days-vertical { gap: 4px; }
  .calendar-col { gap: 4px; }
  .day-number { font-size: 11px; }
}

@media (max-width: 480px) {
  .grid-wrapper { padding: 10px; }
  .weekdays-vertical { width: 54px; }
  .weekday-vertical { font-size: 7px; padding: 4px; }
  .day-number { font-size: 10px; }
  .month-nav { font-size: 14px; padding: 2px 8px; }
  .current-month { font-size: 13px; min-width: 130px; }
}
</style>

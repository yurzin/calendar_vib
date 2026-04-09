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
          <div class="ad-sub">Размер полосы — A4 (145 × 255 мм).</div>
        </div>

        <!-- Страница с календарём -->
        <div v-else class="calendar-grid">
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
                  'is-today':     day && isToday(day.date),
                  'is-weekend':   day && isWeekend(day.date),
                  'is-special':   day && isSpecialDay(day.date),
                  'is-placement': !day && placementKey(rowIdx, ci) !== null,
                  'placement-first':  !day && placementKey(rowIdx, ci) !== null && isPlacementFirst(rowIdx, ci),
                  'placement-last':   !day && placementKey(rowIdx, ci) !== null && isPlacementLast(rowIdx, ci),
                  'placement-middle': !day && placementKey(rowIdx, ci) !== null && !isPlacementFirst(rowIdx, ci) && !isPlacementLast(rowIdx, ci),
                  'empty': !day && placementKey(rowIdx, ci) === null && activeWeekdays[rowIdx] !== '',
                  'spacer': activeWeekdays[rowIdx] === ''
                }"
              >
                <span v-if="day" class="day-number">{{ day.day }}</span>

                <div v-if="day && isSpecialDay(day.date)" class="special-tooltip">
                  <span class="special-tooltip-count">10 размещений</span>
                  Знаменательная дата — день рождения компании, руководителя, сотрудников, профессионального праздника
                </div>

                <!-- Тултип только на первой ячейке группы -->
                <div v-if="!day && isPlacementFirst(rowIdx, ci)" class="placement-tooltip">
                  <span class="placement-tooltip-count">3 размещения</span>
                  Макет компании на страницах календаря
                </div>
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

// Группа размещения: колонка + непрерывный диапазон строк
interface PlacementGroup {
  colIdx: number
  rows: number[] // строки входящие в группу, по порядку
}

const START_YEAR = 2027
const TOTAL_MONTHS = 12

const monthNames = [
  'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
]

const weekdayNames = ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье']

const totalPages = 1 + TOTAL_MONTHS * 2
const currentPage = ref(0)

const currentPageInfo = computed<PageInfo>(() => {
  const page = currentPage.value
  if (page === 0) {
    return { type: 'ad', label: 'Обложка', adName: 'Обложка - место для рекламного макета генерального партнёра' }
  }
  const idx = page - 1
  const monthIdx = Math.floor(idx / 2)
  const isAd = idx % 2 === 1
  if (isAd) {
    return { type: 'ad', label: `${monthNames[monthIdx]} ${START_YEAR}`, adName: 'Размещение 3 макетов компании на страницах месяца' }
  }
  return { type: 'calendar', label: `${monthNames[monthIdx]} ${START_YEAR}`, month: monthIdx, year: START_YEAR }
})

const pageLabel = computed(() => currentPageInfo.value.label)

const getMonthColumns = (year: number, month: number): (CalendarDay | null)[][] => {
  const firstDay = new Date(year, month, 1)
  const daysInMonth = new Date(year, month + 1, 0).getDate()

  let startOffset = firstDay.getDay()
  if (startOffset === 0) startOffset = 7
  startOffset--

  const TOTAL_CELLS = 42
  const usedCells = startOffset + daysInMonth
  const freeCells = TOTAL_CELLS - usedCells

  let colOffset = 0
  while (freeCells - colOffset * 7 > startOffset + colOffset * 7 + 6) {
    colOffset++
  }

  const cols: (CalendarDay | null)[][] = Array.from({ length: 6 }, () => Array(7).fill(null))
  let day = 1
  for (let c = 0; c < 6; c++) {
    for (let r = 0; r < 7; r++) {
      if (c < colOffset) continue
      if (c === colOffset && r < startOffset) continue
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

const activeWeekdays = computed(() => {
  const cols = columns.value
  if (!cols.length) return weekdayNames
  return Array.from({ length: 7 }, (_, rowIdx) => {
    for (const col of cols) {
      if (col[rowIdx]) {
        const jsDay = col[rowIdx]!.date.getDay()
        const monBased = jsDay === 0 ? 6 : jsDay - 1
        return weekdayNames[monBased]
      }
    }
    return weekdayNames[rowIdx]
  })
})

const isToday = (date: Date): boolean => {
  const t = new Date()
  return date.getDate() === t.getDate() && date.getMonth() === t.getMonth() && date.getFullYear() === t.getFullYear()
}

const isWeekend = (date: Date): boolean => {
  const d = date.getDay()
  return d === 0 || d === 6
}

// ─── Знаменательная дата ──────────────────────────────────────────────────
const specialDayCache = new Map<string, number>()

const specialDay = computed(() => {
  const info = currentPageInfo.value
  if (info.type !== 'calendar') return null
  const key = `${info.year}-${info.month}`
  if (specialDayCache.has(key)) return specialDayCache.get(key)!
  const daysInMonth = new Date(info.year!, info.month! + 1, 0).getDate()
  let candidate = 1
  let attempts = 0
  do {
    candidate = Math.floor(Math.random() * daysInMonth) + 1
    const dow = new Date(info.year!, info.month!, candidate).getDay()
    if (dow !== 0 && dow !== 6) break
    attempts++
  } while (attempts < 50)
  specialDayCache.set(key, candidate)
  return candidate
})

const isSpecialDay = (date: Date): boolean => {
  const info = currentPageInfo.value
  if (info.type !== 'calendar') return false
  return date.getDate() === specialDay.value && date.getMonth() === info.month && date.getFullYear() === info.year
}

// ─── Размещения макета ────────────────────────────────────────────────────
//
// Алгоритм:
// 1. Смотрим на первую и последнюю колонки (columns[0] и columns[5]).
// 2. Собираем непрерывные группы пустых ячеек в каждой из них.
// 3. Из всех найденных групп случайно выбираем одну с размером 3 или 4.
//    Если группы с нужным размером нет — берём ближайшую по размеру.
// 4. Сохраняем выбранную группу в кэш.
//
// "Группа" — это непрерывная цепочка null-ячеек в одной колонке.
// Объединённая рамка реализована через CSS-классы:
//   placement-first  → верхняя ячейка группы  (скруглены только верхние углы, нет нижней границы)
//   placement-middle → средние ячейки          (нет скруглений, нет верхней и нижней границы)
//   placement-last   → нижняя ячейка группы    (скруглены только нижние углы, нет верхней границы)

const placementCache = new Map<string, PlacementGroup | null>()

function getEmptyGroups(cols: (CalendarDay | null)[][], colIdx: number): number[][] {
  const col = cols[colIdx]
  if (!col) return []

  const groups: number[][] = []
  let current: number[] = []

  for (let r = 0; r < col.length; r++) {
    if (col[r] === null) {
      current.push(r)
    } else {
      if (current.length >= 2) groups.push([...current])
      current = []
    }
  }
  if (current.length >= 2) groups.push(current)
  return groups
}

const placementGroup = computed<PlacementGroup | null>(() => {
  const info = currentPageInfo.value
  if (info.type !== 'calendar') return null

  const key = `${info.year}-${info.month}`
  if (placementCache.has(key)) return placementCache.get(key)!

  const cols = columns.value
  // Кандидаты: пустые группы из первой (0) и последней (5) колонок
  const candidates: PlacementGroup[] = []
  for (const ci of [0, 5]) {
    const groups = getEmptyGroups(cols, ci)
    for (const g of groups) {
      candidates.push({ colIdx: ci, rows: g })
    }
  }

  if (!candidates.length) {
    placementCache.set(key, null)
    return null
  }

  // Предпочитаем группы размером 3 или 4, иначе берём любую максимальную
  const preferred = candidates.filter(g => g.rows.length === 3 || g.rows.length === 4)
  const pool = preferred.length ? preferred : candidates

  // Из подходящих берём случайную
  const chosen = pool[Math.floor(Math.random() * pool.length)]

  // Если группа длиннее 4 — обрезаем до 3 или 4 (случайно), начиная с начала
  const targetSize = chosen.rows.length >= 4 ? (Math.random() < 0.5 ? 3 : 4) : chosen.rows.length
  const result: PlacementGroup = { colIdx: chosen.colIdx, rows: chosen.rows.slice(0, targetSize) }

  placementCache.set(key, result)
  return result
})

// Возвращает индекс ячейки внутри группы (0, 1, 2, …) или null если не в группе
const placementKey = (rowIdx: number, colIdx: number): number | null => {
  const g = placementGroup.value
  if (!g || g.colIdx !== colIdx) return null
  const pos = g.rows.indexOf(rowIdx)
  return pos === -1 ? null : pos
}

const isPlacementFirst = (rowIdx: number, colIdx: number): boolean => placementKey(rowIdx, colIdx) === 0
const isPlacementLast  = (rowIdx: number, colIdx: number): boolean => {
  const g = placementGroup.value
  if (!g) return false
  return placementKey(rowIdx, colIdx) === g.rows.length - 1
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
.month-nav:hover:not(:disabled) { background: rgba(147, 197, 253, 0.1); color: #bfdbfe; }
.month-nav:disabled { opacity: 0.3; cursor: default; }

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
  line-height: 1.6;
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
  gap: 0; /* gap убран — нужен для слияния рамок placement */
  flex: 1;
  min-width: 0;
}

/* Обычные ячейки получают gap через margin-bottom */
.calendar-day {
  flex: 1;
  background: rgba(13, 21, 48, 0.5);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 10px;
  margin-bottom: 6px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.calendar-day:last-child { margin-bottom: 0; }

.calendar-day:hover:not(.empty):not(.spacer):not(.is-placement) {
  border-color: rgba(96, 165, 250, 0.3);
  background: rgba(13, 21, 48, 0.7);
  transform: translateY(-1px);
}

.calendar-day.is-today {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.15);
  box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.2);
}

.calendar-day.is-weekend .day-number { color: #f87171; }

.calendar-day.empty {
  opacity: 0.15;
  background: rgba(13, 21, 48, 0.2);
}

.calendar-day.spacer {
  opacity: 0;
  background: transparent;
  border-color: transparent;
  pointer-events: none;
}

/* ─── Знаменательная дата ────────────────────────── */
@keyframes special-pulse {
  0%   { box-shadow: 0 0 0 0px rgba(139, 92, 246, 0.6), 0 0 0 1px rgba(139, 92, 246, 0.4); }
  60%  { box-shadow: 0 0 0 6px rgba(139, 92, 246, 0),   0 0 0 1px rgba(139, 92, 246, 0.4); }
  100% { box-shadow: 0 0 0 0px rgba(139, 92, 246, 0),   0 0 0 1px rgba(139, 92, 246, 0.4); }
}

.calendar-day.is-special {
  border-color: #8b5cf6;
  background: rgba(139, 92, 246, 0.15);
  overflow: visible;
  z-index: 1;
  animation: special-pulse 2s ease-out infinite;
}

.calendar-day.is-special .day-number { color: #c4b5fd; font-weight: 500; }

.calendar-day.is-special::after {
  content: '★';
  position: absolute;
  top: 2px; right: 4px;
  font-size: 9px;
  color: #8b5cf6;
  line-height: 1;
}

.special-tooltip {
  display: none;
  position: absolute;
  bottom: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%);
  width: 200px;
  background: #1e1b4b;
  border: 1px solid rgba(139, 92, 246, 0.4);
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 11px;
  line-height: 1.5;
  color: #cbd5e1;
  z-index: 100;
  pointer-events: none;
  text-align: left;
}

.special-tooltip::after {
  content: '';
  position: absolute;
  top: 100%; left: 50%;
  transform: translateX(-50%);
  border: 6px solid transparent;
  border-top-color: rgba(139, 92, 246, 0.4);
}

.special-tooltip-count {
  display: block;
  font-size: 10px;
  font-weight: 600;
  color: #a78bfa;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 5px;
}

.calendar-day.is-special:hover { animation-play-state: paused; }
.calendar-day.is-special:hover .special-tooltip { display: block; }

/* ─── Размещение макета компании ─────────────────── */
/*
  Объединённая рамка: gap между ячейками внутри группы убран через margin-bottom: 0,
  а border-radius и border-top/bottom управляются по позиции в группе.
*/

@keyframes placement-pulse {
  0%   { box-shadow: 0 0 0 0px rgba(236, 72, 153, 0.5); }
  60%  { box-shadow: 0 0 0 6px rgba(236, 72, 153, 0); }
  100% { box-shadow: 0 0 0 0px rgba(236, 72, 153, 0); }
}

/* Все ячейки группы */
.calendar-day.is-placement {
  background: rgba(236, 72, 153, 0.1);
  border-color: #ec4899;
  border-radius: 0;
  margin-bottom: 0;         /* убираем зазор внутри группы */
  overflow: visible;
  z-index: 1;
  cursor: default;
  /*animation: placement-pulse 2.4s ease-out infinite;
  animation-delay: 0.4s;*/
}

/* Верхняя ячейка группы */
.calendar-day.placement-first {
  border-radius: 10px 10px 0 0;
  border-bottom: none;      /* граница между first и middle/last убрана */
  margin-top: 0;
}

/* Средние ячейки */
.calendar-day.placement-middle {
  border-top: none;
  border-bottom: none;
}

/* Нижняя ячейка группы */
.calendar-day.placement-last {
  border-radius: 0 0 10px 10px;
  border-top: none;
  margin-bottom: 6px;        /* восстанавливаем зазор после группы */
}

/* Если группа из 2 ячеек — first одновременно не last */
/* Если группа из 1 ячейки — не бывает (минимум 2) */

.calendar-day.is-placement::after {
  content: '◆';
  position: absolute;
  top: 2px; right: 4px;
  font-size: 8px;
  color: #ec4899;
  line-height: 1;
}

/* Маркер только на первой ячейке, не на остальных */
.calendar-day.placement-middle::after,
.calendar-day.placement-last::after { display: none; }

.placement-tooltip {
  display: none;
  position: absolute;
  bottom: calc(100% + 8px);
  left: 50%;
  transform: translateX(-50%);
  width: 200px;
  background: #2d1b2e;
  border: 1px solid rgba(236, 72, 153, 0.4);
  border-radius: 10px;
  padding: 10px 12px;
  font-size: 11px;
  line-height: 1.5;
  color: #cbd5e1;
  z-index: 100;
  pointer-events: none;
  text-align: left;
}

.placement-tooltip::after {
  content: '';
  position: absolute;
  top: 100%; left: 50%;
  transform: translateX(-50%);
  border: 6px solid transparent;
  border-top-color: rgba(236, 72, 153, 0.4);
}

.placement-tooltip-count {
  display: block;
  font-size: 10px;
  font-weight: 600;
  color: #f472b6;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  margin-bottom: 5px;
}

.calendar-day.placement-first:hover { animation-play-state: paused; }
.calendar-day.placement-first:hover .placement-tooltip { display: block; }

/* ─── Числа ──────────────────────────────────────── */
.day-number {
  font-size: 15px;
  font-weight: 500;
  color: #dce8f5;
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

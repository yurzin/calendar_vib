<template>
  <div class="gl-section gl-section--dark" id="calendar-2027">
    <div class="gl-container">
      <div class="gl-section-head">
        <div class="gl-section-line" />
        <h2 class="gl-section-title">Календарь 2027</h2>
        <p class="gl-section-sub">Настольный календарь-справочник</p>
      </div>

      <div class="grid-wrapper">
        <div class="grid-header">
          <div class="month-selector">
            <button @click="prevMonth" class="month-nav">←</button>
            <span class="current-month">{{ currentMonthName }} {{ currentYear }}</span>
            <button @click="nextMonth" class="month-nav">→</button>
          </div>
        </div>

        <div class="calendar-grid">
          <!-- Дни недели сверху вниз - растянуты по высоте -->
          <div class="weekdays-vertical">
            <div class="weekday-vertical">ПОНЕДЕЛЬНИК</div>
            <div class="weekday-vertical">ВТОРНИК</div>
            <div class="weekday-vertical">СРЕДА</div>
            <div class="weekday-vertical">ЧЕТВЕРГ</div>
            <div class="weekday-vertical">ПЯТНИЦА</div>
            <div class="weekday-vertical">СУББОТА</div>
            <div class="weekday-vertical">ВОСКРЕСЕНЬЕ</div>
          </div>

          <!-- Календарные ячейки - числа идут сверху вниз -->
          <div class="calendar-days-vertical">
            <!-- Колонка 1 (неделя 1) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column1" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
            <!-- Колонка 2 (неделя 2) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column2" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
            <!-- Колонка 3 (неделя 3) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column3" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
            <!-- Колонка 4 (неделя 4) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column4" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
            <!-- Колонка 5 (неделя 5) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column5" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
                <span v-if="day" class="day-number">{{ day.day }}</span>
              </div>
            </div>
            <!-- Колонка 6 (неделя 6) -->
            <div class="calendar-col">
              <div v-for="(day, idx) in column6" :key="idx" class="calendar-day" :class="{ 'is-today': day && isToday(day.date), 'is-weekend': day && (getDayOfWeekIndex(idx) === 5 || getDayOfWeekIndex(idx) === 6) }">
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

const currentYear = ref(2027)
const currentMonth = ref(0)

const monthNames = [
  'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'
]

const currentMonthName = computed(() => monthNames[currentMonth.value])

// Получение индекса дня недели (0=пн, 6=вс)
const getDayOfWeekIndex = (rowIndex: number) => rowIndex

// Получение всех дней месяца, распределенных по колонкам (сверху вниз)
const getMonthColumns = (year: number, month: number): (CalendarDay | null)[][] => {
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const daysInMonth = lastDay.getDate()

  // Определяем, с какого дня недели начинается месяц (пн=0, вс=6)
  let startWeekday = firstDay.getDay()
  if (startWeekday === 0) startWeekday = 7
  const startOffset = startWeekday - 1 // сколько пустых ячеек в первой колонке

  // Создаем 6 колонок по 7 строк
  const columns: (CalendarDay | null)[][] = Array(6).fill(null).map(() => Array(7).fill(null))

  let dayCounter = 1

  // Заполняем колонки сверху вниз
  for (let col = 0; col < 6; col++) {
    for (let row = 0; row < 7; row++) {
      // Пропускаем пустые ячейки в первой колонке
      if (col === 0 && row < startOffset) {
        continue
      }

      if (dayCounter <= daysInMonth) {
        columns[col][row] = {
          day: dayCounter,
          date: new Date(year, month, dayCounter)
        }
        dayCounter++
      }
    }
  }

  return columns
}

const columns = computed(() => getMonthColumns(currentYear.value, currentMonth.value))

const column1 = computed(() => columns.value[0])
const column2 = computed(() => columns.value[1])
const column3 = computed(() => columns.value[2])
const column4 = computed(() => columns.value[3])
const column5 = computed(() => columns.value[4])
const column6 = computed(() => columns.value[5])

// Проверка, является ли дата сегодняшней
const isToday = (date: Date): boolean => {
  const today = new Date()
  return date.getDate() === today.getDate() &&
    date.getMonth() === today.getMonth() &&
    date.getFullYear() === today.getFullYear()
}

// Навигация по месяцам
const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
}
</script>

<style scoped>
.gl-container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 64px;
  width: 100%;
}

@media (max-width: 900px) {
  .gl-container {
    padding: 0 32px;
  }
}

@media (max-width: 480px) {
  .gl-container {
    padding: 0 20px;
  }
}

.grid-wrapper {
  background: rgba(13, 21, 48, 0.4);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 16px;
  padding: 24px;
  margin-top: 20px;
}

.grid-header {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 24px;
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

.month-nav:hover {
  background: rgba(147, 197, 253, 0.1);
  color: #bfdbfe;
}

.current-month {
  font-size: 16px;
  font-weight: 500;
  color: #dce8f5;
  min-width: 130px;
  text-align: center;
}

/* Основная сетка */
.calendar-grid {
  display: flex;
  gap: 8px;
}

/* Дни недели вертикально - растянуты по высоте */
.weekdays-vertical {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex-shrink: 0;
  width: 110px;
}

.weekday-vertical {
  display: flex;
  align-items: center;
  justify-content: center;
  flex: 1;
  min-height: 50px;
  text-align: center;
  font-size: 12px;
  font-weight: 600;
  color: #3b82f6;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  background: rgba(59, 130, 246, 0.08);
  border-radius: 10px;
  padding: 8px;
  word-break: break-word;
}

/* Контейнер с колонками дат */
.calendar-days-vertical {
  display: flex;
  gap: 8px;
  flex: 1;
  overflow-x: auto;
}

/* Каждая колонка - 7 строк сверху вниз */
.calendar-col {
  display: flex;
  flex-direction: column;
  gap: 8px;
  flex: 1;
  min-width: 60px;
}

/* Ячейка с датой */
.calendar-day {
  flex: 1;
  min-height: 50px;
  background: rgba(13, 21, 48, 0.5);
  border: 1px solid rgba(96, 165, 250, 0.1);
  border-radius: 10px;
  padding: 8px;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-day:hover {
  border-color: rgba(96, 165, 250, 0.3);
  background: rgba(13, 21, 48, 0.7);
  transform: translateY(-2px);
}

.calendar-day.is-today {
  border-color: #3b82f6;
  background: rgba(59, 130, 246, 0.15);
  box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.2);
}

.calendar-day.is-weekend .day-number {
  color: #fbbf24;
}

.day-number {
  font-size: 16px;
  font-weight: 500;
  color: #dce8f5;
  text-align: center;
}

/* Пустые ячейки */
.calendar-day:has(:not(.day-number)) {
  opacity: 0.3;
  background: rgba(13, 21, 48, 0.2);
}

/* Адаптивность */
@media (max-width: 900px) {
  .grid-wrapper {
    padding: 16px;
  }

  .weekdays-vertical {
    width: 90px;
  }

  .weekday-vertical {
    font-size: 10px;
    min-height: 44px;
  }

  .calendar-col {
    min-width: 50px;
  }

  .calendar-day {
    min-height: 44px;
    padding: 6px;
  }

  .day-number {
    font-size: 14px;
  }
}

@media (max-width: 768px) {
  .weekdays-vertical {
    width: 80px;
  }

  .weekday-vertical {
    font-size: 9px;
    min-height: 40px;
    padding: 6px;
  }

  .calendar-col {
    min-width: 45px;
  }

  .calendar-day {
    min-height: 40px;
    padding: 4px;
  }

  .day-number {
    font-size: 13px;
  }
}

@media (max-width: 640px) {
  .weekdays-vertical {
    width: 70px;
  }

  .weekday-vertical {
    font-size: 8px;
    min-height: 36px;
  }

  .calendar-col {
    min-width: 40px;
  }

  .calendar-day {
    min-height: 36px;
    padding: 3px;
  }

  .day-number {
    font-size: 12px;
  }

  .calendar-days-vertical {
    gap: 4px;
  }
}

@media (max-width: 480px) {
  .grid-wrapper {
    padding: 12px;
  }

  .weekdays-vertical {
    width: 60px;
  }

  .weekday-vertical {
    font-size: 7px;
    min-height: 32px;
    padding: 4px;
  }

  .calendar-col {
    min-width: 35px;
  }

  .calendar-day {
    min-height: 32px;
    padding: 2px;
  }

  .day-number {
    font-size: 10px;
  }

  .month-nav {
    font-size: 14px;
    padding: 2px 8px;
  }

  .current-month {
    font-size: 14px;
    min-width: 100px;
  }

  .calendar-grid {
    gap: 4px;
  }
}
</style>

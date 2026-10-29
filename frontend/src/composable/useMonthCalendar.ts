// composable/useMonthCalendar.ts
import { computed, toValue, type MaybeRefOrGetter } from 'vue'

export interface CalendarDay { day: number; date: Date }
interface PlacementGroup { colIdx: number; rows: number[] }

export const START_YEAR = 2027
export const MONTH_NAMES = [
  'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
]
const WEEKDAY_NAMES = ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс']

const specialDayCache = new Map<string, number>()
const placementCache = new Map<string, PlacementGroup | null>()

const getMonthColumns = (year: number, month: number): (CalendarDay | null)[][] => {
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  let startOffset = new Date(year, month, 1).getDay()
  if (startOffset === 0) startOffset = 7
  startOffset--

  const freeCells = 42 - (startOffset + daysInMonth)
  let colOffset = 0
  while (freeCells - colOffset * 7 > startOffset + colOffset * 7 + 6) colOffset++

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

const getEmptyGroups = (cols: (CalendarDay | null)[][], colIdx: number): number[][] => {
  const col = cols[colIdx]
  if (!col) return []
  const groups: number[][] = []
  let current: number[] = []
  for (let r = 0; r < col.length; r++) {
    if (col[r] === null) {
      current.push(r)
    } else {
      if (current.length >= 2) groups.push(current)
      current = []
    }
  }
  if (current.length >= 2) groups.push(current)
  return groups
}

const pickSpecialDay = (year: number, month: number): number => {
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  let candidate = 1
  for (let attempts = 0; attempts < 50; attempts++) {
    candidate = Math.floor(Math.random() * daysInMonth) + 1
    const dow = new Date(year, month, candidate).getDay()
    if (dow !== 0 && dow !== 6) break
  }
  return candidate
}

const pickPlacement = (cols: (CalendarDay | null)[][]): PlacementGroup | null => {
  const candidates: PlacementGroup[] = []
  for (const colIdx of [0, 5]) {
    for (const rows of getEmptyGroups(cols, colIdx)) candidates.push({ colIdx, rows })
  }
  if (!candidates.length) return null

  const preferred = candidates.filter(g => g.rows.length === 3 || g.rows.length === 4)
  const pool = preferred.length ? preferred : candidates
  const chosen = pool[Math.floor(Math.random() * pool.length)]

  let size = chosen.rows.length
  if (size >= 5) size = Math.random() < 0.5 ? 3 : 4
  return { colIdx: chosen.colIdx, rows: chosen.rows.slice(0, size) }
}

export function useMonthCalendar(year: MaybeRefOrGetter<number>, month: MaybeRefOrGetter<number>) {
  const key = computed(() => `${toValue(year)}-${toValue(month)}`)
  const columns = computed(() => getMonthColumns(toValue(year), toValue(month)))

  const weekdays = computed(() =>
    Array.from({ length: 7 }, (_, row) => {
      for (const col of columns.value) {
        const cell = col[row]
        if (cell) return WEEKDAY_NAMES[(cell.date.getDay() + 6) % 7]
      }
      return WEEKDAY_NAMES[row]
    })
  )

  const specialDay = computed(() => {
    if (!specialDayCache.has(key.value)) {
      specialDayCache.set(key.value, pickSpecialDay(toValue(year), toValue(month)))
    }
    return specialDayCache.get(key.value)!
  })

  const placement = computed(() => {
    if (!placementCache.has(key.value)) placementCache.set(key.value, pickPlacement(columns.value))
    return placementCache.get(key.value) ?? null
  })

  const placementPos = (row: number, col: number): number | null => {
    const g = placement.value
    if (!g || g.colIdx !== col) return null
    const pos = g.rows.indexOf(row)
    return pos === -1 ? null : pos
  }

  const isPlacementFirst = (row: number, col: number) => placementPos(row, col) === 0

  const isToday = (d: Date) => {
    const t = new Date()
    return d.getDate() === t.getDate() && d.getMonth() === t.getMonth() && d.getFullYear() === t.getFullYear()
  }
  const isWeekend = (d: Date) => d.getDay() === 0 || d.getDay() === 6

  // Заменяет огромное выражение :class в шаблоне
  const cellClasses = (day: CalendarDay | null, row: number, col: number) => {
    const pos = placementPos(row, col)
    const inGroup = !day && pos !== null
    const lastPos = placement.value ? placement.value.rows.length - 1 : -1
    return {
      'is-today': !!day && isToday(day.date),
      'is-weekend': !!day && isWeekend(day.date),
      'is-special': !!day && day.day === specialDay.value,
      'is-placement': inGroup,
      'placement-first': inGroup && pos === 0,
      'placement-last': inGroup && pos === lastPos,
      'placement-middle': inGroup && pos !== 0 && pos !== lastPos,
      empty: !day && pos === null,
    }
  }

  return { columns, weekdays, specialDay, isPlacementFirst, cellClasses }
}

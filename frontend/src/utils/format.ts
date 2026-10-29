// src/utils/format.ts
const numberFormatter = new Intl.NumberFormat('ru-RU', {
  maximumFractionDigits: 2,
})

export const formatNumber = (val: number | string | null | undefined): string => {
  if (val === null || val === undefined || val === '') return ''
  const num = Number(val)
  return Number.isNaN(num) ? '' : numberFormatter.format(num)
}

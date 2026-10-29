// src/utils/format.ts
const numberFormatter = new Intl.NumberFormat('ru-RU', {
  maximumFractionDigits: 2,
})

export const formatNumber = (val: number | string | null | undefined): string => {
  if (val === null || val === undefined || val === '') return ''
  const num = Number(val)
  return Number.isNaN(num) ? '' : numberFormatter.format(num)
}

export const plural = (n: number, forms: [string, string, string]): string => {
  const m10 = n % 10
  const m100 = n % 100
  if (m10 === 1 && m100 !== 11) return forms[0]
  if (m10 >= 2 && m10 <= 4 && (m100 < 12 || m100 > 14)) return forms[1]
  return forms[2]
}

export const placementsLabel = (n: number): string =>
  `${n} ${plural(n, ['размещение', 'размещения', 'размещений'])}`

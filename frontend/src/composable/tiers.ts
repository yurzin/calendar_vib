// composable/tiers.ts
export type AdLayout = 'full' | 'half'

export interface TierConfig {
  slug: string
  label: string
  hasCover: boolean
  headline: string
  headlineAccent: string
  body: string
  specialDays: number
  gridPlacements: number
  gridPlacementText: string
  adLayout: AdLayout
  adPageName: string
  adTooltipTitle: string
  adTooltipText: string
}

const AD_NAME_FULL = 'Размещение макета компании на странице месяца'

export const tiers: Record<string, TierConfig> = {
  'general-partner': {
    slug: 'general-partner',
    label: 'Генеральный партнёр',
    hasCover: true,
    headline: 'Ваш бренд на каждой',
    headlineAccent: 'странице',
    body: '12 месяцев присутствия в рабочей зоне вашей аудитории.',
    specialDays: 10,
    gridPlacements: 3,
    gridPlacementText: 'Макет компании на страницах календаря',
    adLayout: 'full',
    adPageName: AD_NAME_FULL,
    adTooltipTitle: '3 размещения',
    adTooltipText: 'Макет компании на странице месяца',
  },
  'official-partner': {
    slug: 'official-partner',
    label: 'Официальный партнёр',
    hasCover: false,
    headline: 'Ваш бренд в',
    headlineAccent: 'ключевые даты',
    body: '6 знаменательных дат, 2 размещения макета в календарной сетке и 1 макет на странице месяца.',
    specialDays: 6,
    gridPlacements: 2,
    gridPlacementText: 'Макет компании на странице календаря',
    adLayout: 'full',
    adPageName: AD_NAME_FULL,
    adTooltipTitle: '1 размещение',
    adTooltipText: 'Макет компании на странице месяца',
  },
  'business-partner': {
    slug: 'business-partner',
    label: 'Деловой партнёр',
    hasCover: false,
    headline: 'Ваш бренд',
    headlineAccent: 'в деловой среде',
    body: '4 знаменательные даты, 1 размещение макета в календарной сетке и 1 макет на странице месяца.',
    specialDays: 4,
    gridPlacements: 1,
    gridPlacementText: 'Макет компании на странице календаря',
    adLayout: 'full',
    adPageName: AD_NAME_FULL,
    adTooltipTitle: '1 размещение',
    adTooltipText: 'Макет компании на странице месяца',
  },
  'project-participant': {
    slug: 'project-participant',
    label: 'Участник проекта',
    hasCover: false,
    headline: 'Ваше имя',
    headlineAccent: 'в указателе года',
    body: '2 знаменательные даты, 1 мини-макет в календарной сетке и 1 макет на странице месяца.',
    specialDays: 2,
    gridPlacements: 1,
    gridPlacementText: 'Мини-макет компании на странице календаря',
    adLayout: 'half',
    adPageName: 'Размещение макета компании на 1/2 полосы страницы месяца',
    adTooltipTitle: '1 размещение · 1/2 полосы',
    adTooltipText: 'Макет компании на половине страницы месяца',
  },
}

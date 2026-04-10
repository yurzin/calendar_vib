import { ref } from 'vue'

const galleryItems = [
  { src: '/gallery/cal-cover.png',    label: 'Обложка календаря 2026',                      size: 'lg' },
  { src: '/gallery/cal-spread11.png', label: 'Дни рождения — ноябрь',                        size: 'sm' },
  { src: '/gallery/cal-cover2.png',   label: 'Форзац — календарь на следующий год',           size: 'sm' },
  { src: '/gallery/cal-stand.png',    label: 'Основание календаря',                           size: 'md' },
  { src: '/gallery/cal-stand2.png',   label: 'Основание календаря',                           size: 'md' },
  { src: '/gallery/cal-back.png',     label: 'Оборот полосы — информация о ведущих компаниях', size: 'sm' },
  { src: '/gallery/cal-detail.png',   label: 'Алфавитный указатель',                          size: 'sm' },
]

const lightboxIndex = ref<number | null>(null)

const openLightbox  = (i: number) => { lightboxIndex.value = i }
const closeLightbox = () => { lightboxIndex.value = null }
const prevPhoto     = () => {
  if (lightboxIndex.value === null) return
  lightboxIndex.value = (lightboxIndex.value - 1 + galleryItems.length) % galleryItems.length
}
const nextPhoto     = () => {
  if (lightboxIndex.value === null) return
  lightboxIndex.value = (lightboxIndex.value + 1) % galleryItems.length
}

export const useLightbox = () => ({
  galleryItems,
  lightboxIndex,
  openLightbox,
  closeLightbox,
  prevPhoto,
  nextPhoto,
})

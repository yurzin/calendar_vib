<script setup lang="ts">
import {computed, onMounted, ref} from 'vue'
import { useRouter } from 'vue-router';
import { useAuth } from '@/composable/useAuth';

const { user, logout, checkAuth } = useAuth();
const router = useRouter();

const mobileOpen = ref(false)
const isAdmin = computed(() => user.value?.roles?.includes('admin'));
const error     = ref('');

const lightboxIndex = ref<number | null>(null)

// Модальное окно формы регистрации
const modalOpen = ref(false)
const formSent = ref(false)
const form = ref({ name: '', company: '', phone: '' })

// Обработчик выхода
const handleLogout = async () => {
  try {
    await logout();
    router.push('/');
    mobileOpen.value = false;
  } catch (error) {
    console.error('Logout failed:', error);
  }
};


function openModal() { modalOpen.value = true; formSent.value = false }
function closeModal() { modalOpen.value = false }
function submitForm() {
  // Здесь можно добавить отправку на сервер
  formSent.value = true
}

// Галерея
const galleryItems = [
  { src: '/gallery/cal-cover.png',    label: 'Обложка календаря 2026',        size: 'lg' },
  { src: '/gallery/cal-spread11.png', label: 'Дни рождения — ноябрь', size: 'sm' },
  { src: '/gallery/cal-cover2.png',   label: 'Форзац — календарь на следующий год', size: 'sm' },
  { src: '/gallery/cal-stand.png',  label: 'Основание календаря', size: 'md' },
  { src: '/gallery/cal-stand2.png',  label: 'Основание календаря', size: 'md' },
  { src: '/gallery/cal-back.png',     label: 'Оборот полосы — информация о ведущих компаниях', size: 'sm' },
  { src: '/gallery/cal-detail.png',   label: 'Алфавитный указатель',          size: 'sm' },
]

// Участники
const members = [
  { name: 'Новая стоматология',         role: 'Государственное управление',    logo: '/partners/newstomatology.jpg', initials: 'АК' },
  { name: 'КузбассРазрезУголь',         role: 'Горнодобывающая промышленность', logo: '/partners/inarm.jpg',          initials: 'КРУ' },
  { name: 'Банк «Кузнецкий»',           role: 'Финансовый сектор',             logo: '/partners/yurproject.jpg',     initials: 'БК' },
  { name: 'СГК — Сибирская генерация',  role: 'Энергетика',                    logo: '/partners/sgk.jpg',            initials: 'СГК' },
  { name: 'Кемеровская ГРЭС',           role: 'Электроэнергетика',             logo: '',                             initials: 'КГ' },
  { name: 'ТЦ «Лапландия»',             role: 'Торговля и услуги',             logo: '',                             initials: 'ТЛ' },
  { name: 'МКБ Инвестиции',             role: 'Инвестиционный бизнес',         logo: '',                             initials: 'МКБ' },
  { name: 'РЖД — Западно-Сибирская',    role: 'Транспорт и логистика',         logo: '',                             initials: 'РЖД' },
  { name: 'Кузбасская ТПП',             role: 'Деловое сообщество',            logo: '',                             initials: 'ТПП' },
  { name: 'Novaplast',                  role: 'Производство',                  logo: '',                             initials: 'NP' },
  { name: 'Медицинский центр «Авиценна»', role: 'Здравоохранение',             logo: '',                             initials: 'АВ' },
  { name: 'Кузбасс FM',                 role: 'Медиа и реклама',               logo: '',                             initials: 'КФМ' },
]

const calendarPages = [
  { src: '/calendar/vib_01.jpg', label: 'Обложка календаря 2026', size: 'lg' },
  { src: '/calendar/vib_02.jpg', label: 'Разворот календаря', size: 'sm' },
  { src: '/calendar/vib_03.jpg', label: 'Внутренний разворот', size: 'sm' },
  { src: '/calendar/vib_04.jpg', label: 'Календарная сетка', size: 'md' },
  { src: '/calendar/vib_05.jpg', label: 'Страница с датами', size: 'md' },
  { src: '/calendar/vib_06.jpg', label: 'Информационный блок', size: 'sm' },
  { src: '/calendar/vib_07.jpg', label: 'Дополнительная страница', size: 'sm' },
  { src: '/calendar/vib_08.jpg', label: 'Завершающий разворот', size: 'sm' },
  { src: '/calendar/vib_09.jpg', label: 'Обложка с разных ракурсов', size: 'md' },
  { src: '/calendar/vib_10.jpg', label: 'Календарь в интерьере', size: 'lg' },
  { src: '/calendar/vib_11.jpg', label: 'Детали оформления', size: 'sm' },
  { src: '/calendar/vib_12.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_13.jpg', label: 'Обложка календаря 2026', size: 'lg' },
  { src: '/calendar/vib_14.jpg', label: 'Разворот календаря', size: 'sm' },
  { src: '/calendar/vib_15.jpg', label: 'Внутренний разворот', size: 'sm' },
  { src: '/calendar/vib_16.jpg', label: 'Календарная сетка', size: 'md' },
  { src: '/calendar/vib_17.jpg', label: 'Страница с датами', size: 'md' },
  { src: '/calendar/vib_18.jpg', label: 'Информационный блок', size: 'sm' },
  { src: '/calendar/vib_19.jpg', label: 'Дополнительная страница', size: 'sm' },
  { src: '/calendar/vib_20.jpg', label: 'Завершающий разворот', size: 'sm' },
  { src: '/calendar/vib_21.jpg', label: 'Обложка с разных ракурсов', size: 'md' },
  { src: '/calendar/vib_22.jpg', label: 'Календарь в интерьере', size: 'lg' },
  { src: '/calendar/vib_23.jpg', label: 'Детали оформления', size: 'sm' },
  { src: '/calendar/vib_24.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_25.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_26.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_27.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_28.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_29.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_30.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_31.jpg', label: 'Финальный вид', size: 'md' },
  { src: '/calendar/vib_32.jpg', label: 'Финальный вид', size: 'md' },
]

// Архив
const archiveIssues = [
  { year: 2025, cover: '/calendar/cover/2025.jpg', members: 312, pdfUrl: 'https://drive.google.com/file/d/1yq9CQw8OS1naDWqpQsgCAEH9-AAiYyOF/view', pageUrl: '/archive/2025' },
  { year: 2024, cover: '/calendar/cover/2024.jpg', members: 298, pdfUrl: 'https://drive.google.com/file/d/1D4rDBCU0MDG9qA8T6Vk4xF4rBpWI6ItR/view', pageUrl: '/archive/2024' },
  { year: 2023, cover: '/calendar/cover/2023.jpg', members: 276, pdfUrl: 'https://drive.google.com/file/d/1RkW-WLvkOZ_Gpq2wmyQLxQaKntUVyH4T/view', pageUrl: '/archive/2023' },
  { year: 2022, cover: '/calendar/cover/2022.jpg', members: 251, pdfUrl: 'https://drive.google.com/file/d/1n4G2Ffs__fJh09cqIUYg0kkbBKv-BIs9/view', pageUrl: '/archive/2022' },
  { year: 2021, cover: '/calendar/cover/2021.jpg', members: 234, pdfUrl: 'https://drive.google.com/file/d/185vPOVFO3byG2ZjI77q7VVAv-6UM5dIw/view', pageUrl: '/archive/2021' },
  { year: 2020, cover: '/calendar/cover/2020.jpg', members: 218, pdfUrl: 'https://drive.google.com/file/d/1D3t1uVi-Cyn3-1rPrPhXjWVXrDlOhdef/view', pageUrl: '/archive/2020' },
]

const openLightbox  = (i: number) => { lightboxIndex.value = i }
const closeLightbox = () => { lightboxIndex.value = null }
const prevPhoto = () => {
  if (lightboxIndex.value === null) return
  lightboxIndex.value = (lightboxIndex.value - 1 + galleryItems.length) % galleryItems.length
}
const nextPhoto = () => {
  if (lightboxIndex.value === null) return
  lightboxIndex.value = (lightboxIndex.value + 1) % galleryItems.length
}

// Карусель галереи
const currentSlide = ref(0)
const isTransitioning = ref(false)
let autoplayInterval = null

const totalSlides = computed(() => calendarPages.length)

const nextSlide = () => {
  if (isTransitioning.value) return
  isTransitioning.value = true
  currentSlide.value = (currentSlide.value + 1) % totalSlides.value
  setTimeout(() => {
    isTransitioning.value = false
  }, 500)
}

const prevSlide = () => {
  if (isTransitioning.value) return
  isTransitioning.value = true
  currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value
  setTimeout(() => {
    isTransitioning.value = false
  }, 500)
}

const goToSlide = (index) => {
  if (isTransitioning.value || index === currentSlide.value) return
  isTransitioning.value = true
  currentSlide.value = index
  setTimeout(() => {
    isTransitioning.value = false
  }, 500)
}

// Автоплей
const startAutoplay = () => {
  if (autoplayInterval) clearInterval(autoplayInterval)
  autoplayInterval = setInterval(() => {
    nextSlide()
  }, 5000)
}

const stopAutoplay = () => {
  if (autoplayInterval) {
    clearInterval(autoplayInterval)
    autoplayInterval = null
  }
}

// Загружаем изображения при монтировании
onMounted(() => {
  startAutoplay()
})

// Очищаем интервал при размонтировании
import { onUnmounted } from 'vue'
import axios from "axios";
onUnmounted(() => {
  stopAutoplay()
})

const loadData = async () => {
  error.value = '';

  try {
    if (!user.value) await checkAuth();
    console.log('User after checkAuth:', user.value);
    console.log('Cookies:', document.cookie);

    const res = await axios.post('/api/main');
    console.log(res.data);
  } catch (e: any) {
    console.error('Error status:', e.response?.status);
    console.error('Error data:', e.response?.data);
    error.value = e.response?.data?.message || 'Ошибка загрузки';

  } finally {
  }
};

onMounted(loadData);

</script>

<template>
  <div class="gl-wrap">

    <!-- ─── Фон ──────────────────────────────────────────── -->
    <div class="gl-bg" aria-hidden="true">
      <div class="gl-orb gl-orb1" />
      <div class="gl-orb gl-orb2" />
      <div class="gl-orb gl-orb3" />
      <div class="gl-grid" />
    </div>

    <!-- ─── Превью-блок (fixed) — клик открывает модалку ── -->
    <div class="gl-card-abs" @click="openModal" role="button" tabindex="0" @keydown.enter="openModal">
      <div class="gl-card-abs-glow" aria-hidden="true" />
      <div class="gl-card-abs-content">
        <div class="gl-card-abs-eyebrow">Деловой календарь · {{ new Date().getFullYear() + 1 }}</div>
        <p class="gl-card-abs-title">Хочу увидеть<br>себя в календаре</p>
        <p class="gl-card-abs-sub">Оставьте заявку — мы свяжемся и подберём формат участия</p>
        <div class="gl-card-abs-cta">
          <span class="gl-card-abs-btn">Оставить заявку</span>
          <svg viewBox="0 0 16 16" fill="none" width="14" height="14" class="gl-card-abs-arrow">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- ─── Header ───────────────────────────────────────── -->
    <header class="gl-header">
      <router-link to="/" class="gl-brand">
        <div class="gl-brand-icon">
          <img src="/logo.svg" alt="" class="gl-logo">
        </div>
        <div class="gl-brand-texts">
          <span class="gl-brand-name">Власть и Бизнес</span>
          <span class="gl-brand-tagline">Деловой календарь</span>
        </div>
      </router-link>

      <nav class="gl-nav">
        <div class="gl-nav-menu">
          <a href="#integration" class="gl-nav-item">Интеграция в проект</a>
          <a href="#members"     class="gl-nav-item">Участники проекта</a>
          <a href="#about"       class="gl-nav-item">О проекте</a>
          <a href="#archive"     class="gl-nav-item">Архив</a>
          <a href="#contacts"    class="gl-nav-item">Контакты</a>
        </div>
        <div class="gl-nav-divider" />
        <router-link v-if="!user" to="/login" class="gl-nav-accent">Войти</router-link>
        <button v-else @click="handleLogout" class="gl-nav-accent">Выйти</button>
        <router-link v-if="!user" to="/register" class="gl-nav-plain">
          <span>Регистрация</span>
          <svg viewBox="0 0 16 16" fill="none" width="11" height="11">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </router-link>
        <router-link v-if="isAdmin" to="/admin" class="gl-nav-plain">Админка</router-link>
      </nav>

      <button class="gl-burger" @click="mobileOpen = !mobileOpen" :class="{ 'is-open': mobileOpen }" aria-label="Меню">
        <span /><span /><span />
      </button>
    </header>

    <!-- Мобильная навигация -->
    <div class="gl-mobile-nav" :class="{ 'is-open': mobileOpen }">
      <router-link to="#about"       class="gl-mobile-item" @click="mobileOpen = false">О проекте</router-link>
      <router-link to="#integration" class="gl-mobile-item" @click="mobileOpen = false">Интеграция в проект</router-link>
      <router-link to="#members"     class="gl-mobile-item" @click="mobileOpen = false">Участники проекта</router-link>
      <router-link to="#archive"     class="gl-mobile-item" @click="mobileOpen = false">Архив</router-link>
      <router-link to="#contacts"    class="gl-mobile-item" @click="mobileOpen = false">Контакты</router-link>
      <div class="gl-mobile-divider" />
      <router-link to="/login"    class="gl-mobile-item gl-mobile-item--plain"  @click="mobileOpen = false">Войти</router-link>
      <router-link to="/register" class="gl-mobile-item gl-mobile-item--accent" @click="mobileOpen = false">Регистрация</router-link>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section">
      <div class="gl-container">
        <div class="gl-hero-eyebrow">Деловой календарь · {{ new Date().getFullYear() + 1 }}</div>
        <h1 class="gl-hero-title">
          Информация —<br>это власть.<br>
          <span class="gl-hero-title-accent">Календарь —<br>это стратегия.</span>
        </h1>
        <p class="gl-hero-lead">
          Настольный справочник для деловых людей Кузбасса: дни рождения чиновников, руководителей, топ-менеджеров и праздничные даты в одном издании.
        </p>
        <div class="gl-hero-cta">
          <a href="#integration" class="gl-btn-primary">Интеграция в проект</a>
          <a href="#about"       class="gl-btn-ghost">Узнать больше</a>
        </div>
      </div>

      <div class="gl-hero-visual" aria-hidden="true">
        <img src="/calendar.svg" alt="" class="gl-calendar-img" />
      </div>

      <div class="gl-scroll-hint" aria-hidden="true"><span>↓</span></div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
             СТАТИСТИКА — разделитель
        ═══════════════════════════════════════════════════════════ -->
    <div class="gl-stats-bar">
      <div class="gl-stats-inner">
        <div class="gl-stat" @click="openModal">
          <span class="gl-stat-num">3 000</span>
          <span class="gl-stat-label">экземпляров тираж</span>
        </div>
        <div class="gl-stat-divider" />
        <div class="gl-stat">
          <span class="gl-stat-num">300+</span>
          <span class="gl-stat-label">участников ежегодно</span>
        </div>
        <div class="gl-stat-divider" />
        <div class="gl-stat">
          <span class="gl-stat-num">7+</span>
          <span class="gl-stat-label">лет издаётся</span>
        </div>
        <div class="gl-stat-divider" />
        <div class="gl-stat gl-stat--cta" @click="openModal">
          <span class="gl-stat-cta-text">Хочу в календарь</span>
          <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
            <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 1 — О проекте
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section" id="about">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">О проекте</h2>
          <p class="gl-section-sub">Как всё устроено</p>
        </div>
        <div class="gl-features">
          <div class="gl-feature">
            <!-- 🆕 Иконка -->
            <div class="gl-feature-icon">
              <svg viewBox="0 0 40 40" fill="none" width="40" height="40">
                <rect x="6" y="8" width="28" height="24" rx="4" stroke="#3b82f6" stroke-width="1.4"/>
                <line x1="6" y1="15" x2="34" y2="15" stroke="#3b82f6" stroke-width="0.8" opacity="0.5"/>
                <line x1="14" y1="5" x2="14" y2="11" stroke="#3b82f6" stroke-width="1.4" stroke-linecap="round"/>
                <line x1="26" y1="5" x2="26" y2="11" stroke="#3b82f6" stroke-width="1.4" stroke-linecap="round"/>
                <path d="M13 24 L18 29 L27 20" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <span class="gl-feature-num">01</span>
            <span class="gl-feature-title">Удобно</span>
            <p class="gl-feature-text">На лицевой странице полосы на соответствующих датах размещаются имена и фотографии родившихся в этом месяце, корпоративные даты и праздники.</p>
          </div>
          <div class="gl-feature">
            <!-- 🆕 Иконка -->
            <div class="gl-feature-icon">
              <svg viewBox="0 0 40 40" fill="none" width="40" height="40">
                <rect x="8" y="6" width="24" height="28" rx="3" stroke="#3b82f6" stroke-width="1.4"/>
                <line x1="13" y1="14" x2="27" y2="14" stroke="#93c5fd" stroke-width="1.4" stroke-linecap="round"/>
                <line x1="13" y1="19" x2="24" y2="19" stroke="#3b82f6" stroke-width="1" stroke-linecap="round" opacity="0.6"/>
                <line x1="13" y1="24" x2="27" y2="24" stroke="#3b82f6" stroke-width="1" stroke-linecap="round" opacity="0.6"/>
                <text x="20" y="31" text-anchor="middle" font-family="DM Sans,sans-serif" font-size="6" font-weight="600" fill="#3b82f6" letter-spacing="0.08em">А–Я</text>
              </svg>
            </div>
            <span class="gl-feature-num">02</span>
            <span class="gl-feature-title">Алфавитный указатель</span>
            <p class="gl-feature-text">В конце календаря размещён алфавитный указатель полных имён с должностями, названиями компаний и контактными данными.</p>
          </div>
          <div class="gl-feature">
            <!-- 🆕 Иконка -->
            <div class="gl-feature-icon">
              <svg viewBox="0 0 40 40" fill="none" width="40" height="40">
                <rect x="6" y="10" width="28" height="7" rx="2.5" stroke="#3b82f6" stroke-width="1.2" fill="rgba(29,58,110,0.5)"/>
                <text x="20" y="16" text-anchor="middle" font-family="DM Sans,sans-serif" font-size="5.5" font-weight="500" fill="#93c5fd">2026</text>
                <rect x="6" y="19" width="5" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="13" y="19" width="5" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="20" y="19" width="5" height="5" rx="1" fill="rgba(59,130,246,0.55)"/>
                <rect x="27" y="19" width="7" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="6" y="26" width="5" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="13" y="26" width="5" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="20" y="26" width="5" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
                <rect x="27" y="26" width="7" height="5" rx="1" fill="rgba(147,197,253,0.3)"/>
              </svg>
            </div>
            <span class="gl-feature-num">03</span>
            <span class="gl-feature-title">Годовой календарь</span>
            <p class="gl-feature-text">На обороте обложки — сводный годовой календарь на {{ new Date().getFullYear() + 1 }} год, на задней стойке — на {{ new Date().getFullYear() + 2 }} год с указанием выходных и праздников.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 3 — Галерея
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section gl-section--dark" id="gallery">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">Внешний вид</h2>
          <p class="gl-section-sub">Как выглядит готовый календарь</p>
        </div>
        <div class="gl-gallery">
          <button
            v-for="(item, i) in galleryItems" :key="i"
            class="gl-gallery-item" :class="`gl-gallery-item--${item.size}`"
            @click="openLightbox(i)"
          >
            <div class="gl-gallery-img-wrap">
              <img :src="item.src" :alt="item.label" class="gl-gallery-img" />
              <div class="gl-gallery-overlay">
                <span class="gl-gallery-zoom">⤢</span>
                <span class="gl-gallery-label">{{ item.label }}</span>
              </div>
            </div>
          </button>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 2 — Полезно + Распространение
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section gl-section--alt">
      <div class="gl-container">
        <div class="gl-two-col">
          <div class="gl-two-col-block">
            <div class="gl-section-line" />
            <!-- 🆕 Иконка перед заголовком -->
            <div class="gl-block-icon-row">
              <svg viewBox="0 0 24 24" fill="none" width="22" height="22" class="gl-block-icon">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="#3b82f6" stroke-width="1.2"/>
                <path d="M8 12l3 3 5-6" stroke="#93c5fd" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h2 class="gl-section-title">Полезно</h2>
            </div>
            <h3 class="gl-block-title">Всё необходимое для деловых связей</h3>
            <div class="gl-text-stack">
              <p>«Власть и бизнес» — это настольный календарь-справочник, в котором собраны дни рождения высших чиновников, депутатов, руководителей государственных служб и ведомств, организаций, ведущих бизнесменов и топ-менеджеров Кузбасского региона, а также государственные, профессиональные праздники и памятные даты.</p>
              <p>Наш календарь — это рабочий инструмент для того, чтобы налаживать или укреплять добрые человеческие отношения между руководителями и топ-менеджерами региона и бизнеса.</p>
              <p>Это эффективный PR-инструмент для повышения узнаваемости любого публичного политика, руководителя, топ-менеджера высокого ранга или компании. Наконец, это оригинальный и полезный бизнес-подарок.</p>
            </div>
          </div>
          <div class="gl-two-col-block">
            <div class="gl-section-line" />
            <!-- 🆕 Иконка — стопка экземпляров -->
            <div class="gl-block-icon-row">
              <svg viewBox="0 0 24 24" fill="none" width="22" height="22" class="gl-block-icon">
                <rect x="3" y="13" width="18" height="7" rx="2" stroke="#3b82f6" stroke-width="1.2"/>
                <rect x="5" y="9" width="14" height="5" rx="2" stroke="#3b82f6" stroke-width="1" opacity="0.7"/>
                <rect x="7" y="5" width="10" height="5" rx="2" stroke="#3b82f6" stroke-width="0.8" opacity="0.45"/>
                <text x="12" y="18" text-anchor="middle" font-family="DM Sans,sans-serif" font-size="5" font-weight="600" fill="#93c5fd">3000</text>
              </svg>
              <h2 class="gl-section-title">Действенно</h2>
            </div>
            <h3 class="gl-block-title">Тиражи и каналы распространения</h3>
            <div class="gl-text-stack">
              <p>Тираж календаря на {{ new Date().getFullYear() + 1 }} год составит <strong>3 000 экземпляров</strong>. В качестве подарков календари получают все «герои» календаря, а также рекламодатели (не менее 20 шт.) и партнёры проекта.</p>
              <p>С помощью Курьерской службы «Всё про Всё» календари доставляются в администрацию области, муниципалитеты, по офисам компаний и организаций. Многие компании и ведомства используют календари в качестве своих бизнес-подарков на Новый год.</p>
              <p>В результате мы уверены, что такое распространение гарантирует присутствие календаря в подавляющем большинстве кабинетов и приёмных представителей региональной власти и бизнеса.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 4 — Интеграция
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section" id="integration">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">Интеграция в проект</h2>
          <p class="gl-section-sub">Выберите формат участия</p>
        </div>
        <a href="https://xn--80acbojebux9agf4k.xn--p1ai/general-partner" class="gl-pkg gl-pkg--general">
          <div class="gl-pkg-left">
            <span class="gl-pkg-badge">Топ выбор</span>
            <span class="gl-pkg-name">Генеральный партнёр</span>
            <span class="gl-pkg-desc">Максимальная представленность во всех форматах издания. Логотип на обложке, в колонтитулах каждого разворота, эксклюзивная статья, приоритетное размещение.</span>
          </div>
          <div class="gl-pkg-right">
            <span class="gl-pkg-price">199 000 ₽</span>
            <span class="gl-pkg-cta">Подробнее →</span>
          </div>
        </a>
        <div class="gl-pkg-grid">
          <a href="https://xn--80acbojebux9agf4k.xn--p1ai/oficial-partner" class="gl-pkg gl-pkg--card">
            <span class="gl-pkg-name">Официальный партнёр</span>
            <span class="gl-pkg-desc">Представительский формат с приоритетным размещением и расширенным блоком информации.</span>
            <span class="gl-pkg-price">45 900 ₽</span>
            <span class="gl-pkg-cta">Подробнее →</span>
          </a>
          <a href="https://xn--80acbojebux9agf4k.xn--p1ai/delovoy-partner" class="gl-pkg gl-pkg--card">
            <span class="gl-pkg-name">Деловой партнёр</span>
            <span class="gl-pkg-desc">Оптимальный пакет для уверенного присутствия в календаре среди ключевых персон региона.</span>
            <span class="gl-pkg-price">25 900 ₽</span>
            <span class="gl-pkg-cta">Подробнее →</span>
          </a>
          <a href="https://xn--80acbojebux9agf4k.xn--p1ai/uchastnik-proekta" class="gl-pkg gl-pkg--card gl-pkg--member">
            <span class="gl-pkg-name">Участник проекта</span>
            <span class="gl-pkg-desc">Базовое включение в календарь. Имя, должность и контактные данные в алфавитном указателе.</span>
            <span class="gl-pkg-price">5 990 ₽</span>
            <span class="gl-pkg-cta">Подробнее →</span>
          </a>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 5 — Участники
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section gl-section--alt" id="members">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">Участники проекта</h2>
          <p class="gl-section-sub">Компании и организации в календаре {{ new Date().getFullYear() + 1 }}</p>
        </div>
        <div class="gl-members-track-wrap">
          <div class="gl-members-track">
            <div v-for="(member, i) in [...members, ...members]" :key="`m-${i}`" class="gl-member">
              <div class="gl-member-logo-wrap">
                <img v-if="member.logo" :src="member.logo" :alt="member.name" class="gl-member-logo" />
                <span v-else class="gl-member-initials">{{ member.initials }}</span>
              </div>
              <span class="gl-member-name">{{ member.name }}</span>
            </div>
          </div>
        </div>
        <div class="gl-members-grid">
          <div v-for="(member, i) in members" :key="`mg-${i}`" class="gl-member-card">
            <div class="gl-member-logo-wrap gl-member-logo-wrap--lg">
              <img v-if="member.logo" :src="member.logo" :alt="member.name" class="gl-member-logo" />
              <span v-else class="gl-member-initials">{{ member.initials }}</span>
            </div>
            <div class="gl-member-info">
              <span class="gl-member-company">{{ member.name }}</span>
              <span class="gl-member-role">{{ member.role }}</span>
            </div>
          </div>
        </div>
        <div class="gl-members-cta">
          <router-link to="#members" class="gl-btn-ghost">Все участники →</router-link>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
     СЕКЦИЯ — Галерея (карусель)
═══════════════════════════════════════════════════════════ -->
    <section class="gl-section gl-section--dark" id="calendar">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">Календарь 2026</h2>
          <p class="gl-section-sub">Страницы календаря</p>
        </div>

        <div class="gl-carousel"
             @mouseenter="stopAutoplay"
             @mouseleave="startAutoplay">

          <!-- Основной слайдер -->
          <div class="gl-carousel-container">
            <div class="gl-carousel-track" :style="{ transform: `translateX(-${currentSlide * 100}%)` }">
              <div v-for="(image, i) in calendarPages" :key="i" class="gl-carousel-slide">
                <div class="gl-carousel-image-wrapper">
                  <img :src="image.src" :alt="image.alt" class="gl-carousel-image" />
                  <div class="gl-carousel-overlay">
                    <div class="gl-carousel-caption">
                      <span class="gl-carousel-caption-number">{{ i + 1 }}/{{ totalSlides }}</span>
                      <h3 class="gl-carousel-caption-title">{{ image.caption }}</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Кнопки навигации -->
            <button class="gl-carousel-btn gl-carousel-btn--prev" @click="prevSlide" aria-label="Предыдущий">
              <svg viewBox="0 0 24 24" fill="none" width="24" height="24">
                <path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <button class="gl-carousel-btn gl-carousel-btn--next" @click="nextSlide" aria-label="Следующий">
              <svg viewBox="0 0 24 24" fill="none" width="24" height="24">
                <path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
          </div>

          <!-- Индикаторы (точки) -->
          <div class="gl-carousel-dots">
            <button
              v-for="(_, idx) in calendarPages"
              :key="idx"
              class="gl-carousel-dot"
              :class="{ 'is-active': idx === currentSlide }"
              @click="goToSlide(idx)"
              :aria-label="`Перейти к слайду ${idx + 1}`"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════
         СЕКЦИЯ 6 — Архив
    ═══════════════════════════════════════════════════════════ -->
    <section class="gl-section gl-section--dark" id="archive">
      <div class="gl-container">
        <div class="gl-section-head">
          <div class="gl-section-line" />
          <h2 class="gl-section-title">Архив номеров</h2>
          <p class="gl-section-sub">Все выпуски делового календаря</p>
        </div>
        <div class="gl-archive-grid">
          <div v-for="issue in archiveIssues" :key="issue.year" class="gl-archive-card">
            <div class="gl-archive-cover">
              <img v-if="issue.cover" :src="issue.cover" :alt="`Обложка ${issue.year}`" class="gl-archive-cover-img" />
              <div v-else class="gl-archive-cover-placeholder">
                <span class="gl-archive-cover-year-bg">{{ issue.year }}</span>
                <div class="gl-archive-cover-logo">В&amp;Б</div>
              </div>
              <div class="gl-archive-cover-shine" aria-hidden="true" />
            </div>
            <div class="gl-archive-info">
              <div class="gl-archive-meta">
<!--                <span class="gl-archive-year">{{ issue.year }}</span>-->
                <span v-if="issue.members" class="gl-archive-members">{{ issue.members }} участников</span>
              </div>
              <span class="gl-archive-title">Власть и Бизнес · {{ issue.year }}</span>
            </div>
            <div class="gl-archive-actions">
              <a :href="issue.pdfUrl || '#'" class="gl-archive-btn gl-archive-btn--primary" target="_blank">
                <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
                  <path d="M8 3v10M3 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Скачать PDF
              </a>
              <a :href="issue.pageUrl || `/archive/${issue.year}`" class="gl-archive-btn gl-archive-btn--ghost">Смотреть →</a>
            </div>
          </div>
        </div>
        <div class="gl-members-cta" style="margin-top: 48px;">
          <router-link to="#archive" class="gl-btn-ghost">Весь архив →</router-link>
        </div>
      </div>
    </section>

    <!-- ─── Footer ───────────────────────────────────────── -->
    <footer class="gl-footer">
      <div class="gl-footer-inner">
        <div class="gl-footer-contacts">
          <a href="tel:+73842755555" class="gl-footer-contact">
            <div class="gl-footer-contact-icon">
              <svg viewBox="0 0 24 24" fill="none" width="16" height="16">
                <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="gl-footer-contact-text">
              <span class="gl-footer-contact-label">Звоните нам</span>
              <span class="gl-footer-contact-value">+7 3842 75-55-55</span>
            </div>
          </a>
          <a href="mailto:pr@vse42.ru" class="gl-footer-contact">
            <div class="gl-footer-contact-icon">
              <svg viewBox="0 0 24 24" fill="none" width="16" height="16">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="gl-footer-contact-text">
              <span class="gl-footer-contact-label">Пишите нам</span>
              <span class="gl-footer-contact-value">pr@vse42.ru</span>
            </div>
          </a>
        </div>
        <div class="gl-footer-copy">
          <router-link to="/" class="gl-footer-brand">
            <span class="gl-footer-brand-name">Власть и Бизнес</span>
          </router-link>
          <span class="gl-footer-copy-text">© 2017 – {{ new Date().getFullYear() }}</span>
        </div>
        <div class="gl-footer-links">
          <a href="#" class="gl-footer-link">Политика конфиденциальности</a>
          <span class="gl-sep">·</span>
          <a href="#" class="gl-footer-link">Поддержка</a>
        </div>
      </div>
    </footer>

    <!-- ═══════════════════════════════════════════════════════
         LIGHTBOX
    ═══════════════════════════════════════════════════════════ -->
    <Transition name="lb">
      <div v-if="lightboxIndex !== null" class="gl-lightbox" @click.self="closeLightbox">
        <button class="gl-lb-close" @click="closeLightbox" aria-label="Закрыть">✕</button>
        <button class="gl-lb-prev" @click="prevPhoto" aria-label="Назад">‹</button>
        <button class="gl-lb-next" @click="nextPhoto" aria-label="Вперёд">›</button>
        <div class="gl-lb-inner">
          <img :src="galleryItems[lightboxIndex].src" :alt="galleryItems[lightboxIndex].label" class="gl-lb-img" />
          <p class="gl-lb-caption">{{ galleryItems[lightboxIndex].label }}</p>
        </div>
        <div class="gl-lb-dots">
          <button v-for="(_, i) in galleryItems" :key="i" class="gl-lb-dot" :class="{ 'is-active': i === lightboxIndex }" @click="lightboxIndex = i" />
        </div>
      </div>
    </Transition>

    <!-- ═══════════════════════════════════════════════════════
         МОДАЛЬНОЕ ОКНО — Форма регистрации
    ═══════════════════════════════════════════════════════════ -->
    <Transition name="modal">
      <div v-if="modalOpen" class="gl-modal-overlay" @click.self="closeModal">
        <div class="gl-modal">
          <button class="gl-modal-close" @click="closeModal" aria-label="Закрыть">✕</button>

          <!-- Успех -->
          <Transition name="fade" mode="out-in">
            <div v-if="formSent" class="gl-modal-success">
              <div class="gl-modal-success-icon">
                <svg viewBox="0 0 24 24" fill="none" width="32" height="32">
                  <path d="M20 6L9 17l-5-5" stroke="#93c5fd" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h3 class="gl-modal-success-title">Заявка отправлена!</h3>
              <p class="gl-modal-success-text">Мы свяжемся с вами в ближайшее время и расскажем о форматах участия в календаре.</p>
              <button class="gl-modal-success-btn" @click="closeModal">Закрыть</button>
            </div>

            <!-- Форма -->
            <div v-else class="gl-modal-body">
              <div class="gl-modal-head">
                <div class="gl-section-line" />
                <h2 class="gl-modal-title">Хочу в календарь</h2>
                <p class="gl-modal-sub">Оставьте контакты — мы подберём подходящий формат участия и свяжемся с вами</p>
              </div>

              <div class="gl-modal-form">
                <div class="gl-field">
                  <label class="gl-field-label">Имя и фамилия *</label>
                  <input
                    v-model="form.name"
                    type="text"
                    class="gl-field-input"
                    placeholder="Иванов Иван"
                    autocomplete="name"
                  />
                </div>
                <div class="gl-field">
                  <label class="gl-field-label">Компания / организация *</label>
                  <input
                    v-model="form.company"
                    type="text"
                    class="gl-field-input"
                    placeholder="ООО «Название»"
                    autocomplete="organization"
                  />
                </div>
                <div class="gl-field">
                  <label class="gl-field-label">Телефон *</label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="gl-field-input"
                    placeholder="+7 (___) ___-__-__"
                    autocomplete="tel"
                  />
                </div>

                <button
                  class="gl-modal-submit"
                  :disabled="!form.name || !form.company || !form.phone"
                  @click="submitForm"
                >
                  Отправить заявку
                  <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
                    <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                  </svg>
                </button>

                <p class="gl-modal-hint">
                  Нажимая «Отправить», вы соглашаетесь с
                  <a href="#" class="gl-modal-link">политикой конфиденциальности</a>
                </p>
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>

  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; }

/* ═══════════════════════════════════════
   ОСНОВА
═══════════════════════════════════════ */
.gl-wrap {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #06091a;
  color: #dce8f5;
  display: flex;
  flex-direction: column;
  position: relative;
}

/* ═══════════════════════════════════════
   ФОН
═══════════════════════════════════════ */
.gl-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.gl-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
.gl-orb1 { width: 700px; height: 700px; top: -300px; left: -200px; background: radial-gradient(circle, rgba(37,99,235,0.28), transparent 65%); }
.gl-orb2 { width: 500px; height: 500px; top: 30%; right: -150px; background: radial-gradient(circle, rgba(29,78,216,0.2), transparent 65%); }
.gl-orb3 { width: 400px; height: 400px; bottom: -100px; left: 30%; background: radial-gradient(circle, rgba(96,165,250,0.08), transparent 65%); }
.gl-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px); background-size: 56px 56px; }

/* ═══════════════════════════════════════
   ПРЕВЬЮ-БЛОК (fixed, кликабельный)
═══════════════════════════════════════ */
.gl-card-abs {
  position: fixed;
  right: 32px;
  bottom: 32px;
  z-index: 50;
  width: 250px;
  background: linear-gradient(150deg, #0d1530, #09101f);
  border: 1px solid rgba(96,165,250,0.2);
  border-radius: 16px;
  padding: 24px 28px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 24px 64px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
  overflow: hidden;
  cursor: pointer;
  animation: card-in 0.7s cubic-bezier(0.16,1,0.3,1) 0.5s both;
  transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
  user-select: none;
}
@keyframes card-in {
  from { opacity: 0; transform: translateY(32px); }
  to   { opacity: 1; transform: translateY(0); }
}
.gl-card-abs:hover {
  border-color: rgba(147,197,253,0.4);
  transform: translateY(-3px);
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.04),
    0 32px 80px rgba(0,0,0,0.8),
    inset 0 1px 0 rgba(147,197,253,0.12);
}
.gl-card-abs:active { transform: translateY(-1px); }

.gl-card-abs-glow {
  position: absolute;
  top: -50px; left: 50%; transform: translateX(-50%);
  width: 200px; height: 120px;
  background: radial-gradient(ellipse, rgba(59,130,246,0.15), transparent 70%);
  pointer-events: none;
}
.gl-card-abs-content {
  position: relative; z-index: 1;
  display: flex; flex-direction: column; gap: 10px;
}
.gl-card-abs-eyebrow {
  font-size: 9px; letter-spacing: 0.16em; text-transform: uppercase;
  color: #3b82f6; font-weight: 400;
  display: flex; align-items: center; gap: 8px;
}
.gl-card-abs-eyebrow::before {
  content: ''; display: inline-block;
  width: 20px; height: 1px; background: #3b82f6; flex-shrink: 0;
}
.gl-card-abs-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px; font-weight: 600; line-height: 1.15;
  color: #e2edf8; margin: 0; letter-spacing: -0.01em;
}
.gl-card-abs-sub {
  font-size: 11px; line-height: 1.6;
  color: #3d5a8a; font-weight: 300; margin: 0;
}
.gl-card-abs-cta {
  display: flex; align-items: center; gap: 8px;
  margin-top: 4px;
}
.gl-card-abs-btn {
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #93c5fd; font-weight: 500;
}
.gl-card-abs-arrow {
  color: #93c5fd;
  transition: transform 0.2s;
}
.gl-card-abs:hover .gl-card-abs-arrow { transform: translateX(4px); }

/* ═══════════════════════════════════════
   HEADER
═══════════════════════════════════════ */
.gl-header {
  position: sticky; top: 0; z-index: 10;
  display: flex; justify-content: space-between; align-items: stretch;
  border-bottom: 1px solid rgba(96,165,250,0.1);
  background: rgba(6,9,26,0.82);
  backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
}
.gl-brand {
  display: flex; align-items: center; gap: 14px;
  text-decoration: none; color: #dce8f5;
  padding: 14px 32px 14px 40px;
  border-right: 1px solid rgba(96,165,250,0.1);
  flex-shrink: 0;
  transition: opacity 0.2s;
}
.gl-brand:hover { opacity: 0.8; }
.gl-brand-icon {
  width: 75px; height: 85px;
  border-radius: 10px;
  background: rgba(37,99,235,0.15);
  /*border: 1px solid rgba(96,165,250,0.2);*/
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
}
.gl-logo { width: 66px; height: 66px; object-fit: contain; }
.gl-brand-texts { display: flex; flex-direction: column; gap: 3px; }
.gl-brand-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px; font-weight: 600;
  color: #e2edf8; letter-spacing: 0.01em; line-height: 1;
}
.gl-brand-tagline {
  font-size: 9px; font-weight: 300;
  color: #3b82f6; letter-spacing: 0.14em; text-transform: uppercase;
  display: flex; align-items: center; gap: 7px;
}
.gl-brand-tagline::before {
  content: ''; display: inline-block;
  width: 16px; height: 1px; background: #3b82f6; flex-shrink: 0;
}
.gl-nav {
  display: flex; align-items: center; gap: 6px;
  padding: 0 24px;
  flex: 1; justify-content: flex-end;
}
.gl-nav-menu { display: flex; align-items: center; gap: 2px; margin-right: 4px; }
.gl-nav-item {
  font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase;
  color: #4a6fa5; padding: 8px 12px; border-radius: 8px;
  border: 1px solid transparent; text-decoration: none; white-space: nowrap;
  transition: color 0.2s, background 0.2s; position: relative;
}
.gl-nav-item::after {
  content: ''; position: absolute; bottom: 5px; left: 12px; right: 12px;
  height: 1px; background: #3b82f6;
  transform: scaleX(0); transform-origin: left;
  transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
}
.gl-nav-item:hover { color: #a8c4e8; background: rgba(96,165,250,0.06); }
.gl-nav-item:hover::after, .gl-nav-item.router-link-active::after { transform: scaleX(1); }
.gl-nav-item.router-link-active { color: #93c5fd; }
.gl-nav-divider { width: 1px; height: 20px; background: rgba(96,165,250,0.15); margin: 0 4px; flex-shrink: 0; }
.gl-nav-plain {
  font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase;
  color: #3d5a8a; padding: 8px 14px; border-radius: 8px;
  border: 1px solid transparent; text-decoration: none; white-space: nowrap;
  transition: all 0.2s;
}
.gl-nav-plain:hover { color: #a8c4e8; background: rgba(96,165,250,0.08); border-color: rgba(96,165,250,0.15); }
.gl-nav-accent {
  display: flex; align-items: center; gap: 6px;
  font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase;
  color: #93c5fd; padding: 8px 16px; border-radius: 8px;
  border: 1px solid rgba(147,197,253,0.25); background: rgba(147,197,253,0.07);
  text-decoration: none; white-space: nowrap; transition: all 0.2s;
}
.gl-nav-accent:hover { color: #06091a; background: #93c5fd; border-color: #93c5fd; }
.gl-nav-accent svg { transition: transform 0.2s; }
.gl-nav-accent:hover svg { transform: translateX(3px); }
.gl-burger {
  display: none; flex-direction: column; gap: 5px;
  background: none; border: none; cursor: pointer;
  padding: 0 20px; border-radius: 0; transition: background 0.2s;
  border-left: 1px solid rgba(96,165,250,0.1);
}
.gl-burger:hover { background: rgba(96,165,250,0.05); }
.gl-burger span { display: block; width: 22px; height: 1.5px; background: #4a6fa5; border-radius: 2px; transition: transform 0.25s, opacity 0.25s; }
.gl-burger.is-open span:nth-child(1) { transform: translateY(6.5px) rotate(45deg); }
.gl-burger.is-open span:nth-child(2) { opacity: 0; }
.gl-burger.is-open span:nth-child(3) { transform: translateY(-6.5px) rotate(-45deg); }
.gl-mobile-nav {
  display: none; position: sticky; top: 72px; z-index: 12;
  flex-direction: column; background: rgba(6,9,26,0.97);
  backdrop-filter: blur(16px); border-bottom: 1px solid rgba(96,165,250,0.12);
  padding: 0 24px; gap: 2px; max-height: 0; overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1), padding 0.35s;
}
.gl-mobile-nav.is-open { max-height: 480px; padding: 12px 24px 20px; }
.gl-mobile-item {
  font-size: 12px; letter-spacing: 0.07em; text-transform: uppercase;
  color: #4a6fa5; padding: 11px 12px; border-radius: 8px;
  text-decoration: none; transition: color 0.2s, background 0.2s;
}
.gl-mobile-item:hover { color: #a8c4e8; background: rgba(96,165,250,0.07); }
.gl-mobile-item.router-link-active { color: #93c5fd; }
.gl-mobile-divider { height: 1px; background: rgba(96,165,250,0.1); margin: 8px 0; }
.gl-mobile-item--plain { color: #3d5a8a; }
.gl-mobile-item--accent { color: #93c5fd; border: 1px solid rgba(147,197,253,0.2); background: rgba(147,197,253,0.05); margin-top: 4px; }

/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.gl-hero-visual { position: absolute; right: 250px; top: 60%; transform: translateY(-50%); z-index: 1; animation: fade-in 1s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
@keyframes fade-in { from { opacity: 0; transform: translateY(calc(-50% + 20px)); } to { opacity: 1; transform: translateY(-50%); } }
.gl-calendar-img { width: 700px; opacity: 0.75; filter: drop-shadow(0 40px 80px rgba(37,99,235,0.35)); transition: opacity 0.4s, transform 0.5s; }
.gl-scroll-hint { position: absolute; bottom: 24px; left: 64px; font-size: 16px; color: rgba(74,111,165,0.45); animation: bounce 2.2s ease-in-out infinite; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(7px); } }
.gl-hero-eyebrow { font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #3b82f6; font-weight: 400; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.gl-hero-eyebrow::before { content: ''; display: inline-block; width: 28px; height: 1px; background: #3b82f6; }
.gl-hero-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(40px, 4.2vw, 66px); font-weight: 600; line-height: 1.1; color: #e2edf8; letter-spacing: -0.01em; margin: 0 0 20px; }
.gl-hero-title-accent { color: #93c5fd; font-style: italic; }
.gl-hero-lead { font-size: 18px; line-height: 1.7; color: #4a6fa5; font-weight: 300; margin: 0 0 28px; max-width: 460px; }
.gl-hero-cta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

/* ═══════════════════════════════════════
   КНОПКИ
═══════════════════════════════════════ */
.gl-btn-primary { font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #06091a; font-weight: 500; padding: 13px 28px; border-radius: 8px; background: #93c5fd; text-decoration: none; transition: background 0.2s, transform 0.15s; }
.gl-btn-primary:hover { background: #bfdbfe; transform: translateY(-1px); }
.gl-btn-ghost { font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #4a6fa5; padding: 13px 24px; border-radius: 8px; border: 1px solid rgba(96,165,250,0.2); text-decoration: none; transition: all 0.2s; display: inline-block; }
.gl-btn-ghost:hover { color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

/* ═══════════════════════════════════════
   СЕКЦИИ
═══════════════════════════════════════ */
.gl-section { position: relative; z-index: 9; padding: 96px 0; border-top: 1px solid rgba(96,165,250,0.08); }
.gl-section--alt  { background: rgba(10,14,32,0.55); }
.gl-section--dark { background: rgba(7,10,24,0.72); }
.gl-container { max-width: 1180px; margin: 0 auto; padding: 0 64px; width: 100%; }
.gl-section-head { display: flex; flex-direction: column; gap: 8px; margin-bottom: 56px; }
.gl-section-line { width: 36px; height: 2px; background: linear-gradient(90deg, #3b82f6, transparent); margin-bottom: 6px; }
.gl-section-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(32px, 3vw, 48px); font-weight: 600; line-height: 1.1; color: #e2edf8; letter-spacing: -0.01em; margin: 0; }
.gl-section-sub { font-size: 11px; font-weight: 300; color: #3d5a8a; letter-spacing: 0.14em; text-transform: uppercase; margin: 0; }
.gl-features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.gl-feature { display: flex; flex-direction: column; gap: 12px; padding: 36px 32px; border: 1px solid rgba(96,165,250,0.1); background: rgba(13,21,48,0.4); transition: background 0.2s, border-color 0.2s; }
.gl-feature:first-child { border-radius: 12px 0 0 12px; }
.gl-feature:last-child  { border-radius: 0 12px 12px 0; }
.gl-feature:hover { background: rgba(13,21,48,0.75); border-color: rgba(96,165,250,0.22); cursor: pointer; }
.gl-feature-num { font-family: 'Cormorant Garamond', serif; font-size: 13px; font-weight: 600; color: #3b82f6; letter-spacing: 0.08em; }
.gl-feature-title { font-family: 'Cormorant Garamond', serif; font-size: 26px; font-weight: 600; color: #93c5fd; line-height: 1.1; }
.gl-feature-text { font-size: 18px; line-height: 1.7; color: #3d5a8a; font-weight: 300; margin: 0; }
.gl-two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; }
.gl-two-col-block { display: flex; flex-direction: column; gap: 24px; }
.gl-block-title { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 600; color: #dce8f5; line-height: 1.2; margin: 0; }
.gl-text-stack { display: flex; flex-direction: column; gap: 14px; padding-left: 20px; border-left: 1px solid rgba(96,165,250,0.15); }
.gl-text-stack p { font-size: 18px; line-height: 1.75; color: #3d5a8a; font-weight: 300; margin: 0; }
.gl-text-stack strong { color: #93c5fd; font-weight: 500; }

/* ═══════════════════════════════════════
   ГАЛЕРЕЯ
═══════════════════════════════════════ */
.gl-gallery { display: grid; grid-template-columns: repeat(4, 1fr); grid-template-rows: auto auto; gap: 10px; }
.gl-gallery-item--lg { grid-column: span 2; grid-row: span 2; }
.gl-gallery-item--md { grid-column: span 1; grid-row: span 1; }
.gl-gallery-item--sm { grid-column: span 1; grid-row: span 1; }
.gl-gallery-item { background: none; border: none; padding: 0; cursor: pointer; border-radius: 10px; overflow: hidden; position: relative; }
.gl-gallery-img-wrap { position: relative; width: 100%; height: 100%; min-height: 180px; overflow: hidden; border-radius: 10px; border: 1px solid rgba(96,165,250,0.1); background: rgba(13,21,48,0.6); }
.gl-gallery-img { width: 100%; height: 100%; transform: scale(0.8); object-fit: contain; display: block; transition: transform 0.4s cubic-bezier(0.4,0,0.2,1), filter 0.3s; filter: brightness(0.85) saturate(0.8); }
.gl-gallery-item:hover .gl-gallery-img { transform: scale(0.90); filter: brightness(1) saturate(1); }
.gl-gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(6,9,26,0.75) 0%, transparent 50%); display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start; padding: 16px; opacity: 0; transition: opacity 0.25s; }
.gl-gallery-item:hover .gl-gallery-overlay { opacity: 1; }
.gl-gallery-zoom { font-size: 22px; color: #93c5fd; position: absolute; top: 12px; right: 14px; }
.gl-gallery-label { font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #a8c4e8; font-weight: 300; }

/* ═══════════════════════════════════════
   ПАКЕТЫ
═══════════════════════════════════════ */
.gl-pkg--general { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 48px; padding: 44px 52px; border-radius: 16px; border: 1px solid rgba(147,197,253,0.28); background: linear-gradient(135deg, rgba(13,21,48,0.9) 0%, rgba(17,27,62,0.85) 100%); text-decoration: none; margin-bottom: 16px; transition: border-color 0.22s, transform 0.18s; position: relative; overflow: hidden; }
.gl-pkg--general::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(59,130,246,0.07), transparent 55%); opacity: 0; transition: opacity 0.22s; }
.gl-pkg--general:hover { border-color: rgba(147,197,253,0.5); transform: translateY(-2px); }
.gl-pkg--general:hover::before { opacity: 1; }
.gl-pkg--general:hover .gl-pkg-cta { color: #93c5fd; }
.gl-pkg-left  { display: flex; flex-direction: column; gap: 12px; }
.gl-pkg-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
.gl-pkg-badge { font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase; color: #93c5fd; background: rgba(147,197,253,0.12); padding: 4px 12px; border-radius: 20px; display: inline-block; }
.gl-pkg-name { font-family: 'Cormorant Garamond', serif; font-size: 36px; font-weight: 600; color: #e2edf8; line-height: 1.1; letter-spacing: -0.01em; }
.gl-pkg-desc { font-size: 18px; line-height: 1.65; color: #3d5a8a; font-weight: 300; max-width: 520px; }
.gl-pkg-price { font-family: 'Cormorant Garamond', serif; font-size: 52px; font-weight: 700; color: #93c5fd; letter-spacing: -0.03em; line-height: 1; }
.gl-pkg-cta { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(147,197,253,0.4); transition: color 0.2s; }
.gl-pkg-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.gl-pkg--card { display: flex; flex-direction: column; gap: 14px; padding: 32px 28px; border-radius: 14px; border: 1px solid rgba(96,165,250,0.13); background: rgba(13,21,48,0.5); text-decoration: none; transition: border-color 0.22s, background 0.22s, transform 0.18s; position: relative; overflow: hidden; }
.gl-pkg--card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(59,130,246,0.05), transparent 55%); opacity: 0; transition: opacity 0.22s; }
.gl-pkg--card:hover { border-color: rgba(96,165,250,0.3); background: rgba(13,21,48,0.85); transform: translateY(-3px); }
.gl-pkg--card:hover::before { opacity: 1; }
.gl-pkg--card:hover .gl-pkg-cta { color: #93c5fd; }
.gl-pkg--card .gl-pkg-name { font-size: 24px; font-weight: 600; }
.gl-pkg--card .gl-pkg-desc { font-size: 12px; flex: 1; }
.gl-pkg--card .gl-pkg-price { font-size: 40px; margin-top: 8px; }
.gl-pkg--card .gl-pkg-cta { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(147,197,253,0.35); transition: color 0.2s; }
.gl-pkg--member { border-color: rgba(96,165,250,0.08); background: rgba(10,14,32,0.35); }
.gl-pkg--member .gl-pkg-price { color: #6b9fd4; }

/* ═══════════════════════════════════════
   УЧАСТНИКИ
═══════════════════════════════════════ */
.gl-members-track-wrap { overflow: hidden; margin-bottom: 48px; mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent); -webkit-mask-image: linear-gradient(90deg, transparent, black 10%, black 90%, transparent); }
.gl-members-track { display: flex; gap: 0; animation: marquee 36s linear infinite; width: max-content; }
@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
.gl-members-track-wrap:hover .gl-members-track { animation-play-state: paused; }
.gl-member { display: flex; align-items: center; gap: 10px; padding: 0 28px; border-right: 1px solid rgba(96,165,250,0.1); white-space: nowrap; flex-shrink: 0; }
.gl-members-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
.gl-member-card { display: flex; align-items: center; gap: 14px; padding: 18px 20px; border: 1px solid rgba(96,165,250,0.1); border-radius: 12px; background: rgba(13,21,48,0.4); transition: border-color 0.2s, background 0.2s; }
.gl-member-card:hover { border-color: rgba(96,165,250,0.25); background: rgba(13,21,48,0.7); }
.gl-member-logo-wrap { width: 36px; height: 36px; border-radius: 8px; background: rgba(37,99,235,0.12); border: 1px solid rgba(96,165,250,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.gl-member-logo-wrap--lg { width: 75px; height: 75px; border-radius: 10px; padding: 10px; background: #ffffff; }
.gl-member-logo { width: 100%; height: 100%; object-fit: contain; }
.gl-member-initials { font-family: 'Cormorant Garamond', serif; font-size: 12px; font-weight: 700; color: #93c5fd; letter-spacing: 0.03em; }
.gl-member-info { display: flex; flex-direction: column; gap: 2px; overflow: hidden; }
.gl-member-company { font-size: 12px; font-weight: 500; color: #dce8f5; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gl-member-role { font-size: 10px; font-weight: 300; color: #2e4a73; letter-spacing: 0.06em; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.gl-member-name { font-size: 12px; color: #4a6fa5; font-weight: 300; }
.gl-members-cta { margin-top: 40px; display: flex; justify-content: center; }

/* ═══════════════════════════════════════
   АРХИВ
═══════════════════════════════════════ */
.gl-archive-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; }
.gl-archive-card { display: flex; flex-direction: column; gap: 14px; background: rgba(13,21,48,0.5); border: 1px solid rgba(96,165,250,0.12); border-radius: 14px; padding: 20px; transition: border-color 0.22s, background 0.22s, transform 0.18s; position: relative; overflow: hidden; }
.gl-archive-card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(59,130,246,0.05), transparent 55%); opacity: 0; transition: opacity 0.22s; }
.gl-archive-card:hover { border-color: rgba(96,165,250,0.28); background: rgba(13,21,48,0.8); transform: translateY(-4px); }
.gl-archive-card:hover::before { opacity: 1; }

.gl-archive-cover {
  position: relative;
  width: 100%;
  aspect-ratio: 16/9;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.gl-archive-cover-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s cubic-bezier(0.4,0,0.2,1); }
.gl-archive-card:hover .gl-archive-cover-img { transform: scale(1.03); }
.gl-archive-cover-placeholder { width: 100%; height: 100%; background: linear-gradient(145deg, #0d1836, #091228); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; border: 1px solid rgba(96,165,250,0.1); border-radius: 8px; position: relative; overflow: hidden; }
.gl-archive-cover-placeholder::before { content: ''; position: absolute; inset: 0; background: linear-gradient(rgba(96,165,250,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.03) 1px, transparent 1px); background-size: 20px 20px; }
.gl-archive-cover-year-bg { font-family: 'Cormorant Garamond', serif; font-size: 52px; font-weight: 700; color: rgba(147,197,253,0.07); line-height: 1; position: absolute; bottom: 8px; right: 8px; letter-spacing: -0.04em; }

.gl-archive-cover-logo {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  font-weight: 700;
  color: #93c5fd;
  letter-spacing: 0.08em;
  z-index: 1;
  padding: 6px 12px;
  border: 1px solid rgba(147, 197, 253, 0.2);
  border-radius: 6px;
  background: rgba(147, 197, 253, 0.06);
}

.gl-archive-cover-shine {
  position: absolute;
  top: 0;
  left: -40%;
  width: 30%;
  height: 100%;
  background: linear-gradient(105deg, transparent, rgba(255, 255, 255, 0.04), transparent);
  transition: left 0.5s ease;
  pointer-events: none;
}

.gl-archive-card:hover .gl-archive-cover-shine { left: 130%; }
.gl-archive-info { display: flex; flex-direction: column; gap: 4px; }
.gl-archive-meta { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
.gl-archive-year { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; color: #93c5fd; line-height: 1; letter-spacing: -0.02em; }
.gl-archive-members { font-size: 10px; font-weight: 300; color: #2e4a73; letter-spacing: 0.08em; text-transform: uppercase; white-space: nowrap; }
.gl-archive-title { font-size: 11px; font-weight: 300; color: #3d5a8a; letter-spacing: 0.06em; text-transform: uppercase; }
.gl-archive-actions { display: flex; flex-direction: column; gap: 6px; margin-top: auto; }
.gl-archive-btn { z-index: 19; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 11px; letter-spacing: 0.07em; text-transform: uppercase; padding: 9px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s; font-weight: 400; }
.gl-archive-btn--primary { background: rgba(147,197,253,0.1); border: 1px solid rgba(147,197,253,0.2); color: #93c5fd; }
.gl-archive-btn--primary:hover { background: #93c5fd; border-color: #93c5fd; color: #06091a; }
.gl-archive-btn--ghost { background: transparent; border: 1px solid rgba(96,165,250,0.12); color: #3d5a8a; }
.gl-archive-btn--ghost:hover { border-color: rgba(96,165,250,0.28); color: #93c5fd; background: rgba(96,165,250,0.05); }

/* ═══════════════════════════════════════
   FOOTER
═══════════════════════════════════════ */
.gl-footer { position: relative; z-index: 10; border-top: 1px solid rgba(96,165,250,0.08); background: rgba(6,9,26,0.6); backdrop-filter: blur(12px); }
.gl-footer-inner { max-width: 1180px; margin: 0 auto; padding: 28px 64px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 24px; }
.gl-footer-contacts { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
.gl-footer-contact { display: flex; align-items: center; gap: 12px; text-decoration: none; transition: opacity 0.2s; }
.gl-footer-contact:hover { opacity: 0.75; }
.gl-footer-contact-icon { width: 36px; height: 36px; border-radius: 9px; background: rgba(59,130,246,0.1); border: 1px solid rgba(96,165,250,0.18); display: flex; align-items: center; justify-content: center; color: #93c5fd; flex-shrink: 0; }
.gl-footer-contact-text { display: flex; flex-direction: column; gap: 1px; }
.gl-footer-contact-label { font-size: 9px; letter-spacing: 0.12em; text-transform: uppercase; color: #2e4a73; font-weight: 300; }
.gl-footer-contact-value { font-size: 14px; font-weight: 500; color: #a8c4e8; letter-spacing: 0.02em; }
.gl-footer-copy { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.gl-footer-brand { text-decoration: none; }
.gl-footer-brand-name { font-family: 'Cormorant Garamond', serif; font-size: 18px; font-weight: 600; color: #4a6fa5; letter-spacing: 0.04em; transition: color 0.2s; }
.gl-footer-brand:hover .gl-footer-brand-name { color: #93c5fd; }
.gl-footer-copy-text { font-size: 10px; color: #1e2d4a; letter-spacing: 0.08em; }
.gl-footer-links { display: flex; align-items: center; justify-content: flex-end; gap: 10px; font-size: 11px; color: #2a3f65; letter-spacing: 0.04em; }
.gl-sep { color: #1e2d4a; }
.gl-footer-link { color: #2a3f65; text-decoration: none; transition: color 0.2s; }
.gl-footer-link:hover { color: #4a7fd4; }

/* ═══════════════════════════════════════
   LIGHTBOX
═══════════════════════════════════════ */
.gl-lightbox { position: fixed; inset: 0; z-index: 100; background: rgba(3,5,16,0.96); backdrop-filter: blur(12px); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
.gl-lb-inner { display: flex; flex-direction: column; align-items: center; gap: 16px; max-width: 900px; width: 100%; }
.gl-lb-img { max-width: 100%; max-height: 70vh; object-fit: contain; border-radius: 10px; box-shadow: 0 40px 100px rgba(0,0,0,0.7); animation: lb-zoom 0.3s cubic-bezier(0.16,1,0.3,1) both; }
@keyframes lb-zoom { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
.gl-lb-caption { font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; color: #3d5a8a; }
.gl-lb-close { position: absolute; top: 24px; right: 32px; background: rgba(13,21,48,0.7); border: 1px solid rgba(96,165,250,0.15); color: #4a6fa5; font-size: 16px; width: 40px; height: 40px; border-radius: 10px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
.gl-lb-close:hover { color: #dce8f5; border-color: rgba(96,165,250,0.35); background: rgba(13,21,48,0.95); }
.gl-lb-prev, .gl-lb-next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(13,21,48,0.7); border: 1px solid rgba(96,165,250,0.15); color: #4a6fa5; font-size: 28px; width: 48px; height: 48px; border-radius: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; line-height: 1; }
.gl-lb-prev { left: 20px; }
.gl-lb-next { right: 20px; }
.gl-lb-prev:hover, .gl-lb-next:hover { color: #dce8f5; border-color: rgba(96,165,250,0.35); background: rgba(13,21,48,0.95); }
.gl-lb-dots { display: flex; gap: 8px; margin-top: 8px; }
.gl-lb-dot { width: 6px; height: 6px; border-radius: 50%; border: none; background: rgba(74,111,165,0.3); cursor: pointer; transition: background 0.2s, transform 0.2s; padding: 0; }
.gl-lb-dot.is-active { background: #93c5fd; transform: scale(1.3); }
.lb-enter-active, .lb-leave-active { transition: opacity 0.25s; }
.lb-enter-from, .lb-leave-to { opacity: 0; }

/* ═══════════════════════════════════════
   МОДАЛЬНОЕ ОКНО
═══════════════════════════════════════ */
.gl-modal-overlay {
  position: fixed; inset: 0; z-index: 200;
  background: rgba(3,5,16,0.88);
  backdrop-filter: blur(14px);
  display: flex; align-items: center; justify-content: center;
  padding: 24px;
}
.gl-modal {
  position: relative;
  width: 100%; max-width: 480px;
  background: linear-gradient(150deg, #0d1530 0%, #091220 100%);
  border: 1px solid rgba(96,165,250,0.18);
  border-radius: 20px;
  padding: 48px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 40px 100px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
  overflow: hidden;
}
.gl-modal::before {
  content: '';
  position: absolute; top: -80px; left: 50%; transform: translateX(-50%);
  width: 320px; height: 200px;
  background: radial-gradient(ellipse, rgba(59,130,246,0.14), transparent 70%);
  pointer-events: none;
}
.gl-modal-close {
  position: absolute; top: 18px; right: 20px;
  background: rgba(13,21,48,0.6); border: 1px solid rgba(96,165,250,0.12);
  color: #3d5a8a; font-size: 14px; width: 34px; height: 34px;
  border-radius: 9px; cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
}
.gl-modal-close:hover { color: #dce8f5; border-color: rgba(96,165,250,0.3); background: rgba(13,21,48,0.9); }

/* Заголовок */
.gl-modal-head { display: flex; flex-direction: column; gap: 10px; margin-bottom: 36px; }
.gl-modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 38px; font-weight: 600; line-height: 1.1;
  color: #e2edf8; letter-spacing: -0.01em; margin: 0;
}
.gl-modal-sub { font-size: 13px; line-height: 1.6; color: #3d5a8a; font-weight: 300; margin: 0; }

/* Форма */
.gl-modal-form { display: flex; flex-direction: column; gap: 16px; }
.gl-field { display: flex; flex-direction: column; gap: 6px; }
.gl-field-label {
  font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
  color: #2e4a73; font-weight: 400;
}
.gl-field-input {
  width: 100%;
  background: rgba(10,16,40,0.6);
  border: 1px solid rgba(96,165,250,0.15);
  border-radius: 10px;
  padding: 13px 16px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; font-weight: 400;
  color: #dce8f5;
  outline: none;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.gl-field-input::placeholder { color: #2a3f65; }
.gl-field-input:focus {
  border-color: rgba(147,197,253,0.4);
  background: rgba(10,16,40,0.85);
  box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.gl-modal-submit {
  width: 100%;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  margin-top: 8px;
  padding: 14px 28px;
  font-family: 'DM Sans', sans-serif;
  font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase;
  font-weight: 500;
  color: #06091a;
  background: #93c5fd;
  border: none; border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s, transform 0.15s, opacity 0.2s;
}
.gl-modal-submit:hover:not(:disabled) { background: #bfdbfe; transform: translateY(-1px); }
.gl-modal-submit:disabled { opacity: 0.35; cursor: not-allowed; }

.gl-modal-hint {
  font-size: 11px; color: #2a3f65; text-align: center;
  margin: 0; line-height: 1.5;
}
.gl-modal-link { color: #3d5a8a; text-decoration: none; transition: color 0.2s; }
.gl-modal-link:hover { color: #93c5fd; }

/* Успех */
.gl-modal-success {
  display: flex; flex-direction: column; align-items: center;
  gap: 16px; text-align: center; padding: 16px 0;
}
.gl-modal-success-icon {
  width: 64px; height: 64px;
  border-radius: 50%;
  background: rgba(147,197,253,0.1);
  border: 1px solid rgba(147,197,253,0.25);
  display: flex; align-items: center; justify-content: center;
  animation: pop 0.4s cubic-bezier(0.16,1,0.3,1) both;
}
@keyframes pop { from { transform: scale(0.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.gl-modal-success-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px; font-weight: 600;
  color: #e2edf8; margin: 0;
}
.gl-modal-success-text { font-size: 14px; line-height: 1.65; color: #3d5a8a; margin: 0; max-width: 320px; }
.gl-modal-success-btn {
  margin-top: 8px;
  padding: 11px 28px;
  font-family: 'DM Sans', sans-serif;
  font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #4a6fa5;
  background: transparent;
  border: 1px solid rgba(96,165,250,0.2);
  border-radius: 9px; cursor: pointer;
  transition: all 0.2s;
}
.gl-modal-success-btn:hover { color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

/* Анимации модалки */
.modal-enter-active, .modal-leave-active { transition: opacity 0.25s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-active .gl-modal { animation: modal-in 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.modal-leave-active .gl-modal { animation: modal-out 0.2s ease-in both; }
@keyframes modal-in  { from { opacity: 0; transform: translateY(24px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
@keyframes modal-out { from { opacity: 1; transform: translateY(0) scale(1); } to { opacity: 0; transform: translateY(12px) scale(0.98); } }

.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* ─── Иконки фич ─── */
.gl-feature-icon {
  width: 48px;
  height: 48px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(13,21,48,0.6);
  border: 1px solid rgba(59,130,246,0.2);
  border-radius: 12px;
  margin-bottom: 4px;
  flex-shrink: 0;
}
.gl-feature-icon svg {
  display: block;
}

/* ─── Иконки у заголовков секций ─── */
.gl-block-icon-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.gl-block-icon {
  flex-shrink: 0;
  opacity: 0.85;
}

/* ═══════════════════════════════════════
   СТАТИСТИКА — разделитель
═══════════════════════════════════════ */
.gl-stats-bar {
  position: relative; z-index: 9;
  border-top: 1px solid rgba(96,165,250,0.08);
  border-bottom: 1px solid rgba(96,165,250,0.08);
  background: rgba(10,14,32,0.6);
  backdrop-filter: blur(8px);
  overflow: hidden;
}
.gl-stats-bar::before {
  content: '';
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(147,197,253,0.04), transparent);
  animation: stats-sweep 4s ease-in-out infinite;
}
@keyframes stats-sweep {
  0%   { left: -60%; }
  100% { left: 140%; }
}
.gl-stats-inner {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 64px;
  display: flex;
  align-items: stretch;
}
.gl-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 4px;
  padding: 28px 48px;
  cursor: default;
  transition: background 0.2s;
  position: relative;
}
.gl-stat::after {
  content: '';
  position: absolute;
  bottom: 0; left: 48px; right: 48px;
  height: 1px;
  background: #3b82f6;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.gl-stat:hover { background: rgba(96,165,250,0.03); }
.gl-stat:hover::after { transform: scaleX(1); }
.gl-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 36px; font-weight: 700;
  color: #93c5fd; letter-spacing: -0.02em; line-height: 1;
  animation: count-in 0.8s cubic-bezier(0.16,1,0.3,1) both;
}
.gl-stat:nth-child(1) .gl-stat-num { animation-delay: 0.1s; }
.gl-stat:nth-child(3) .gl-stat-num { animation-delay: 0.2s; }
.gl-stat:nth-child(5) .gl-stat-num { animation-delay: 0.3s; }
@keyframes count-in {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.gl-stat-label {
  font-size: 9px; font-weight: 300;
  color: #2e4a73; letter-spacing: 0.14em; text-transform: uppercase;
}
.gl-stat-divider {
  width: 1px;
  background: rgba(96,165,250,0.1);
  margin: 16px 0;
  flex-shrink: 0;
}
.gl-stat--cta {
  cursor: pointer;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  padding: 28px 0 28px 48px;
}
.gl-stat--cta:hover { background: none; }
.gl-stat--cta::after { display: none; }
.gl-stat-cta-text {
  font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;
  color: #3b82f6; font-weight: 400;
  transition: color 0.2s;
}
.gl-stat--cta svg {
  color: #3b82f6;
  transition: transform 0.2s, color 0.2s;
  flex-shrink: 0;
}
.gl-stat--cta:hover .gl-stat-cta-text { color: #93c5fd; }
.gl-stat--cta:hover svg { transform: translateX(4px); color: #93c5fd; }

@media (max-width: 900px) {
  .gl-stats-inner { padding: 0 32px; }
  .gl-stat { padding: 20px 24px; }
  .gl-stat::after { left: 24px; right: 24px; }
  .gl-stat-num { font-size: 28px; }
  .gl-stat--cta { padding: 20px 0 20px 24px; }
}
@media (max-width: 600px) {
  .gl-stats-inner { flex-wrap: wrap; }
  .gl-stat-divider { display: none; }
  .gl-stat { padding: 16px 20px; flex: 1; min-width: 120px; }
  .gl-stat--cta { width: 100%; margin-left: 0; border-top: 1px solid rgba(96,165,250,0.08); justify-content: center; }
}

/* ═══════════════════════════════════════
   КАРУСЕЛЬ-ГАЛЕРЕЯ
═══════════════════════════════════════ */
.gl-carousel {
  position: relative;
  width: 100%;
  max-width: 1100px;
  margin: 0 auto;
  overflow: hidden;
  background: rgba(13,21,48,0.4);
  border: 1px solid rgba(96,165,250,0.15);
}

.gl-carousel-container {
  position: relative;
  overflow: hidden;
}

.gl-carousel-track {
  display: flex;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform;
}

.gl-carousel-slide {
  flex-shrink: 0;
  width: 100%;
  position: relative;
}

.gl-carousel-image-wrapper {
  position: relative;
  width: 100%;
  padding-top: 60%;
  overflow: hidden;
}

.gl-carousel-image {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  background: #0a0f24;
  transition: transform 0.3s ease;
}

.gl-carousel-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(to top, rgba(6,9,26,0.9) 0%, transparent 100%);
  padding: 40px 32px 24px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.gl-carousel-image-wrapper:hover .gl-carousel-overlay {
  opacity: 1;
}

.gl-carousel-caption {
  color: #e2edf8;
}

.gl-carousel-caption-number {
  font-size: 11px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: #93c5fd;
  display: block;
  margin-bottom: 8px;
}

.gl-carousel-caption-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px;
  font-weight: 600;
  margin: 0;
  line-height: 1.2;
}

/* Кнопки навигации */
.gl-carousel-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(13,21,48,0.8);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(96,165,250,0.2);
  color: #93c5fd;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  z-index: 10;
}

.gl-carousel-btn:hover {
  background: #3b82f6;
  color: #06091a;
  border-color: #3b82f6;
  transform: translateY(-50%) scale(1.05);
}

.gl-carousel-btn--prev {
  left: 20px;
}

.gl-carousel-btn--next {
  right: 20px;
}

/* Индикаторы (точки) */
.gl-carousel-dots {
  display: flex;
  justify-content: center;
  gap: 8px;
  padding: 20px 0;
  flex-wrap: wrap;
}

.gl-carousel-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(74,111,165,0.4);
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  padding: 0;
}

.gl-carousel-dot:hover {
  background: rgba(147,197,253,0.6);
  transform: scale(1.2);
}

.gl-carousel-dot.is-active {
  background: #93c5fd;
  width: 24px;
  border-radius: 4px;
}

/* Миниатюры */
.gl-carousel-thumbnails {
  display: flex;
  gap: 8px;
  padding: 0 20px 20px;
  overflow-x: auto;
  scrollbar-width: thin;
  scrollbar-color: #3b82f6 rgba(96,165,250,0.2);
}

.gl-carousel-thumbnails::-webkit-scrollbar {
  height: 4px;
}

.gl-carousel-thumbnails::-webkit-scrollbar-track {
  background: rgba(96,165,250,0.1);
  border-radius: 4px;
}

.gl-carousel-thumbnails::-webkit-scrollbar-thumb {
  background: #3b82f6;
  border-radius: 4px;
}

.gl-carousel-thumb {
  flex-shrink: 0;
  height: 300px;
  border-radius: 8px;
  overflow: hidden;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
  background: rgba(13,21,48,0.6);
  padding: 0;
}

.gl-carousel-thumb:hover {
  transform: translateY(-2px);
  border-color: rgba(147,197,253,0.5);
}

.gl-carousel-thumb.is-active {
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59,130,246,0.3);
}

.gl-carousel-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Анимация загрузки */
.gl-carousel-loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #93c5fd;
  font-size: 14px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
}

/* Адаптивность */
@media (max-width: 768px) {
  .gl-carousel-btn {
    width: 40px;
    height: 40px;
  }

  .gl-carousel-btn--prev {
    left: 12px;
  }

  .gl-carousel-btn--next {
    right: 12px;
  }

  .gl-carousel-caption-title {
    font-size: 18px;
  }

  .gl-carousel-overlay {
    padding: 24px 20px 16px;
  }

  .gl-carousel-thumb {
    width: 60px;
    height: 60px;
  }

  .gl-carousel-thumbnails {
    display: none; /* Скрываем миниатюры на мобильных */
  }
}

@media (max-width: 480px) {
  .gl-carousel-caption-title {
    font-size: 16px;
  }

  .gl-carousel-caption-number {
    font-size: 9px;
  }

  .gl-carousel-btn {
    width: 36px;
    height: 36px;
  }
}

/* ═══════════════════════════════════════
   АДАПТИВ
═══════════════════════════════════════ */
@media (max-width: 1100px) {
  .gl-nav-menu { display: none; }
  .gl-nav-divider { display: none; }
  .gl-burger { display: flex; }
  .gl-mobile-nav { display: flex; }
}
@media (max-width: 900px) {
  .gl-header { padding: 16px 24px; }
  .gl-brand-sub { display: none; }
  .gl-features { grid-template-columns: 1fr; }
  .gl-feature:first-child { border-radius: 12px 12px 0 0; }
  .gl-feature:last-child  { border-radius: 0 0 12px 12px; }
  .gl-two-col { grid-template-columns: 1fr; gap: 48px; }
  .gl-pkg--general { grid-template-columns: 1fr; gap: 20px; }
  .gl-pkg-right { align-items: flex-start; }
  .gl-pkg-grid { grid-template-columns: 1fr; }
  .gl-gallery { grid-template-columns: repeat(2, 1fr); }
  .gl-gallery-item--lg { grid-column: span 2; }
  .gl-members-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-archive-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-container { padding: 0 32px; }
  .gl-section { padding: 64px 0; }
  .gl-card-abs { width: 260px; right: 16px; bottom: 16px; padding: 20px 22px; }
  .gl-card-abs-title { font-size: 22px; }
  .gl-footer-inner { grid-template-columns: 1fr; justify-items: center; padding: 28px 32px; gap: 20px; text-align: center; }
  .gl-footer-links { justify-content: center; }
}
@media (max-width: 480px) {
  .gl-container { padding: 0 20px; }
  .gl-gallery { grid-template-columns: 1fr; }
  .gl-gallery-item--lg { grid-column: span 1; }
  .gl-members-grid { grid-template-columns: 1fr; }
  .gl-archive-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-nav-accent span { display: none; }
  .gl-card-abs { display: none; }
  .gl-modal { padding: 32px 24px; }
  .gl-footer-inner { padding: 24px 20px; }
}
</style>

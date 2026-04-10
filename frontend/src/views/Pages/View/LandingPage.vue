<script setup lang="ts">
import {computed, onMounted, onUnmounted, ref} from 'vue'
import { useAuth } from '@/composable/useAuth';
import axios from "axios";
import Footer from "@/views/Pages/View/Components/Footer.vue";
import Header from "@/views/Pages/View/Components/Header.vue";
import Statistics from "@/views/Pages/View/Components/Statistics.vue";
import Hero from "@/views/Pages/View/Components/Hero.vue";
import Modal from "@/views/Pages/View/Components/Modal.vue";
import WantSee from "@/views/Pages/View/Components/WantSee.vue";
import About from "@/views/Pages/View/Components/About.vue";
import Integration from "@/views/Pages/View/Components/Integration.vue";
import Archive from "@/views/Pages/View/Components/Archive.vue";
import Face from "@/views/Pages/View/Components/Face.vue";
import Effectively from "@/views/Pages/View/Components/Effectively.vue";
import Lightbox from "@/views/Pages/View/Components/Lightbox.vue";

const { user, checkAuth } = useAuth();

const error     = ref('');

// Модальное окно формы регистрации
const modalOpen = ref(false)
const formSent = ref(false)
const membersExpanded = ref(false)
const members    = ref<Member[]>([]);
const membersLoading  = ref(false);

function openModal() { modalOpen.value = true; formSent.value = false }

// ─── Типы ──────────────────────────────────────────────────────────────────
interface PersonPreview {
  id: number;
  short_name: string;
  first_name: string;
  last_name: string;
  middle_name: string | null;
  photo_path: string | null;
  photo_thumb_path: string | null;
  position_short: string | null;
  phone: string | null;
  email: string | null;
  birth_day: number | null;
  birth_month: string | null;
}

interface Member {
  id: number;
  name: string;
  logo: string | null;
  site: string | null;
  initials: string;
  role?: string;
  persons?: PersonPreview[];
}

// ─── Модалка партнёра ──────────────────────────────────────────────────────
const partnerModalOpen = ref(false);
const selectedMember   = ref<Member | null>(null);
const memberPersons    = ref<PersonPreview[]>([]);
const memberPersonsLoading = ref(false);

const openPartnerModal = async (member: Member) => {
  selectedMember.value = member;
  partnerModalOpen.value = true;

  // Данные уже есть в member.persons
  memberPersons.value = Array.isArray(member.persons) ? member.persons : [];
};

const closePartnerModal = () => {
  partnerModalOpen.value = false;
  selectedMember.value = null;
  memberPersons.value = [];
};

const personInitials = (p: PersonPreview) =>
  ((p.first_name?.[0] || '') + (p.last_name?.[0] || '')).toUpperCase() || '?';

const monthNames = ['янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек'];
const formatBirthday = (day: number | null, month: string | null) => {
  if (!day || !month) return null;
  const m = parseInt(String(month));
  return `${day} ${monthNames[m - 1] || ''}`;
};

const loadMembers = async () => {
  try {
    const { data } = await axios.post('/api/main');
    members.value = Array.isArray(data?.partners) ? data.partners : [];
  } catch {
    members.value = [];
  } finally {
    membersLoading.value = false;
  }
};

const loadAllMembers = async () => {
  try {
    if (membersExpanded.value) {
      // Свернуть — загрузить обратно краткий список
      await loadMembers()
      membersExpanded.value = false
      return
    }
    const { data } = await axios.post('/api/members')
    members.value = Array.isArray(data?.partners) ? data.partners : []
    membersExpanded.value = true
  } catch {
    members.value = []
  } finally {
    membersLoading.value = false
  }
}

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

// Очищаем интервал при размонтировании
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
  } catch (e: any) {
    console.error('Error status:', e.response?.status);
    console.error('Error data:', e.response?.data);
    error.value = e.response?.data?.message || 'Ошибка загрузки';

  } finally {
  }
};

onMounted(() => {
  startAutoplay();
  loadData();
  loadMembers();
})

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

    <WantSee @open-modal="openModal" />

    <Header/>

    <Hero/>

    <Statistics @open-modal="openModal" />

    <About/>

    <Face/>

    <Effectively/>

    <Integration/>

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
            <div v-for="(member, i) in [...members, ...members]" :key="`m-${i}`" class="gl-member"
                 @click="i < members.length && openPartnerModal(member)"
                 style="cursor: pointer">
            <div class="gl-member-logo-wrap">
                <img v-if="member.logo" :src="member.logo" :alt="member.name" class="gl-member-logo" />
                <span v-else class="gl-member-initials">{{ member.initials }}</span>
              </div>
              <span class="gl-member-name">{{ member.name }}</span>
            </div>
          </div>
        </div>
        <div class="gl-members-grid">
          <div v-for="(member, i) in members" :key="`mg-${i}`" class="gl-member-card"
               :class="{ 'gl-member-card-paid': member.is_paid }"
               @click="openPartnerModal(member)"
               style="cursor: pointer">
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
          <button @click="loadAllMembers()" class="gl-btn-ghost gl-members-btn">
            <span>{{ membersExpanded ? 'Свернуть' : 'Все участники' }}</span>
            <svg
              viewBox="0 0 16 16" fill="none" width="12" height="12"
              :style="{ transform: membersExpanded ? 'rotate(180deg)' : 'rotate(0deg)', transition: 'transform 0.3s ease' }"
            >
              <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
          </button>
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

    <Archive/>

    <Footer/>

    <Lightbox/>

    <!-- ═══════════════════════════════════════════════════════
     МОДАЛКА ПАРТНЁРА
═══════════════════════════════════════════════════════════ -->
    <Transition name="modal">
      <div v-if="partnerModalOpen" class="gl-modal-overlay" @click.self="closePartnerModal">
        <div class="gl-partner-modal">
          <button class="gl-modal-close" @click="closePartnerModal" aria-label="Закрыть">✕</button>

          <!-- Шапка партнёра -->
          <div class="gl-pm-header">
            <div class="gl-pm-logo-wrap">
              <img
                v-if="selectedMember?.logo"
                :src="selectedMember.logo"
                :alt="selectedMember?.name"
                class="gl-pm-logo"
              />
              <span v-else class="gl-pm-initials">{{ selectedMember?.initials }}</span>
            </div>
            <div class="gl-pm-meta">
              <h2 class="gl-pm-name">{{ selectedMember?.name }}</h2>
              <a
              v-if="selectedMember?.site"
              :href="selectedMember.site.startsWith('http') ? selectedMember.site : 'https://' + selectedMember.site"
              target="_blank"
              rel="noopener"
              class="gl-pm-site"
              >
              {{ (selectedMember?.site || '').replace(/^https?:\/\//, '') }}
              <svg viewBox="0 0 12 12" fill="none" width="10" height="10">
                <path d="M2 10L10 2M10 2H5M10 2v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
              </svg>
              </a>
            </div>
          </div>

          <!-- Персоны -->
          <div class="gl-pm-persons-head">
            <span class="gl-pm-section-label">Персоны организации</span>
          </div>

          <!-- Загрузка -->
          <div v-if="memberPersonsLoading" class="gl-pm-loading">
            <span class="gl-pm-spinner" />
            <span>Загрузка…</span>
          </div>

          <!-- Пусто -->
          <div v-else-if="memberPersons.length === 0" class="gl-pm-empty">
            <svg viewBox="0 0 48 48" fill="none" width="40" height="40">
              <circle cx="24" cy="18" r="8" stroke="#3b82f6" stroke-width="1.3"/>
              <path d="M8 40c0-8.837 7.163-14 16-14s16 5.163 16 14" stroke="#3b82f6" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>Персоны не добавлены</span>
          </div>

          <!-- Список персон -->
          <div v-else class="gl-pm-persons">
            <div
              v-for="person in memberPersons"
              :key="person.id"
              class="gl-pm-person"
            >
              <!-- Аватар -->
              <div class="gl-pm-avatar">
                <img
                  v-if="person.photo_thumb_path || person.photo_path"
                  :src="person.photo_thumb_path || person.photo_path"
                  :alt="person.short_name"
                  class="gl-pm-avatar-img"
                  @error="($event.target as HTMLImageElement).style.display='none'"
                />
                <span v-else class="gl-pm-avatar-initials">{{ personInitials(person) }}</span>
              </div>

              <!-- Инфо -->
              <div class="gl-pm-person-info">
                <span class="gl-pm-person-name">{{ person.short_name }}</span>
                <span v-if="person.position_short" class="gl-pm-person-pos">{{ person.position_short }}</span>
                <div class="gl-pm-person-contacts">
                </div>
              </div>
              <div>
              <span v-if="formatBirthday(person.birth_day, person.birth_month)" class="gl-pm-birthday">
                <svg viewBox="0 0 14 14" fill="none" width="11" height="11">
                  <rect x="1" y="2.5" width="12" height="10" rx="1.5" stroke="currentColor" stroke-width="1"/>
                  <path d="M4 1v3M10 1v3M1 6h12" stroke="currentColor" stroke-width="1" stroke-linecap="round"/>
                </svg>
                {{ formatBirthday(person.birth_day, person.birth_month) }}
              </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <Modal v-model="modalOpen" />

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

.gl-card-abs:hover .gl-card-abs-arrow { transform: translateX(4px); }

.gl-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #4a6fa5;
  padding: 13px 24px;
  border-radius: 8px;
  border: 1px solid rgba(96, 165, 250, 0.2);
  text-decoration: none;
  transition: all 0.2s;
  background: transparent;
}

.gl-btn-ghost:hover { cursor: pointer; color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

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
.gl-text-stack p { font-size: 18px; line-height: 1.75; color: #3d5a8a; font-weight: 300; margin: 0; }
.gl-text-stack strong { color: #93c5fd; font-weight: 500; }

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
.gl-member-card-paid { animation: shimmer 3s infinite; }
@keyframes shimmer { 0%,100% { opacity:.6; } 50% { opacity:1; } }
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
   МОДАЛКА ПАРТНЁРА
═══════════════════════════════════════ */
.gl-partner-modal {
  position: relative;
  width: 100%; max-width: 560px;
  max-height: 85vh;
  background: linear-gradient(150deg, #0d1530 0%, #091220 100%);
  border: 1px solid rgba(96,165,250,0.18);
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 40px 100px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
}

/* Шапка партнёра */
.gl-pm-header {
  display: flex;
  align-items: center;
  gap: 18px;
  padding: 28px 28px 20px;
  border-bottom: 1px solid rgba(96,165,250,0.1);
  flex-shrink: 0;
}
.gl-pm-logo-wrap {
  width: 64px; height: 64px;
  border-radius: 14px;
  background: #ffffff;
  border: 1px solid rgba(96,165,250,0.15);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; overflow: hidden;
  padding: 8px;
}
.gl-pm-logo { width: 100%; height: 100%; object-fit: contain; }
.gl-pm-initials {
  font-family: 'Cormorant Garamond', serif;
  font-size: 20px; font-weight: 700;
  color: #93c5fd; letter-spacing: 0.04em;
}
.gl-pm-meta { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
.gl-pm-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px; font-weight: 600;
  color: #e2edf8; margin: 0; line-height: 1.1;
}
.gl-pm-site {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 12px; color: #3b82f6;
  text-decoration: none; transition: color 0.2s;
}
.gl-pm-site:hover { color: #93c5fd; }

/* Заголовок секции персон */
.gl-pm-persons-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 28px 10px;
  flex-shrink: 0;
}
.gl-pm-section-label {
  font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase;
  color: #3b82f6; font-weight: 400;
}

/* Загрузка */
.gl-pm-loading {
  display: flex; align-items: center; justify-content: center; gap: 10px;
  padding: 40px;
  font-size: 13px; color: #2e4a73;
  flex-shrink: 0;
}
.gl-pm-spinner {
  display: inline-block; width: 16px; height: 16px;
  border: 2px solid rgba(96,165,250,0.2);
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Пусто */
.gl-pm-empty {
  display: flex; flex-direction: column; align-items: center;
  gap: 12px; padding: 40px 28px;
  font-size: 13px; color: #2e4a73;
  flex-shrink: 0;
}
.gl-pm-empty svg { opacity: 0.4; }

/* Список персон */
.gl-pm-persons {
  overflow-y: auto;
  padding: 0 16px 16px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  scrollbar-width: thin;
  scrollbar-color: rgba(96,165,250,0.2) transparent;
}
.gl-pm-persons::-webkit-scrollbar { width: 4px; }
.gl-pm-persons::-webkit-scrollbar-track { background: transparent; }
.gl-pm-persons::-webkit-scrollbar-thumb { background: rgba(96,165,250,0.2); border-radius: 4px; }

/* Карточка персоны */
.gl-pm-person {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 16px;
  border: 1px solid rgba(96,165,250,0.08);
  border-radius: 12px;
  background: rgba(13,21,48,0.4);
  transition: border-color 0.2s, background 0.2s;
}
.gl-pm-person:hover {
  border-color: rgba(96,165,250,0.2);
  background: rgba(13,21,48,0.7);
}

/* Аватар */
.gl-pm-avatar {
  width: 48px; height: 48px;
  border-radius: 50%;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  border: 1px solid rgba(96,165,250,0.2);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0; overflow: hidden;
  box-shadow: 0 0 12px rgba(37,99,235,0.2);
}
.gl-pm-avatar-img { width: 100%; height: 100%; object-fit: cover; }
.gl-pm-avatar-initials {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px; font-weight: 600;
  color: #bfdbfe; letter-spacing: 0.02em;
}

/* Инфо персоны */
.gl-pm-person-info {
  display: flex; flex-direction: column; gap: 4px;
  min-width: 0; flex: 1;
}
.gl-pm-person-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 17px; font-weight: 600;
  color: #e2edf8; white-space: nowrap;
  overflow: hidden; text-overflow: ellipsis;
}
.gl-pm-person-pos {
  display: block;
  font-size: 11px; color: #3d5a8a;
  overflow: hidden; text-overflow: ellipsis;
}
.gl-pm-person-contacts {
  display: flex; align-items: center; gap: 12px;
  flex-wrap: wrap; margin-top: 2px;
}

.gl-pm-birthday {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 14px; color: #ffffff; white-space: nowrap;
}

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
.gl-modal-close {
  position: absolute; top: 18px; right: 20px;
  background: rgba(13,21,48,0.6); border: 1px solid rgba(96,165,250,0.12);
  color: #3d5a8a; font-size: 14px; width: 34px; height: 34px;
  border-radius: 9px; cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; justify-content: center;
}
.gl-modal-close:hover { color: #dce8f5; border-color: rgba(96,165,250,0.3); background: rgba(13,21,48,0.9); }

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
@media (max-width: 900px) {
  .gl-two-col { grid-template-columns: 1fr; gap: 48px; }
  .gl-pkg--general { grid-template-columns: 1fr; gap: 20px; }
  .gl-pkg-right { align-items: flex-start; }
  .gl-pkg-grid { grid-template-columns: 1fr; }
  .gl-gallery { grid-template-columns: repeat(2, 1fr); }
  .gl-members-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-archive-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-container { padding: 0 32px; }
  .gl-section { padding: 64px 0; }
}
@media (max-width: 480px) {
  .gl-container { padding: 0 20px; }
  .gl-gallery { grid-template-columns: 1fr; }
  .gl-members-grid { grid-template-columns: 1fr; }
  .gl-archive-grid { grid-template-columns: repeat(2, 1fr); }
  .gl-nav-accent span { display: none; }
}
</style>

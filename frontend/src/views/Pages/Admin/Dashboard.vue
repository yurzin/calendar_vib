<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAuth } from '@/composable/useAuth';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const { user, checkAuth } = useAuth();
const message   = ref('');
const loading   = ref(true);
const error     = ref('');

const greeting = computed(() => {
  const h = new Date().getHours();
  if (h < 6)  return 'Доброй ночи';
  if (h < 12) return 'Доброе утро';
  if (h < 18) return 'Добрый день';
  return 'Добрый вечер';
});

const initials = computed(() => {
  const name = user.value?.name || '';
  return name.split(' ').map((w: string) => w[0]).join('').toUpperCase().slice(0, 2) || '?';
});

const loadData = async () => {
  loading.value = true;
  error.value = '';
  try {
    if (!user.value) await checkAuth();
    const res = await axios.get('/api/dashboard');
    message.value = res.data.message;
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка загрузки';
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);
</script>

<template>
  <AuthenticatedLayout>
    <div class="db-wrap">

      <!-- Скелетон загрузки -->
      <template v-if="loading">
        <div class="db-skeleton">
          <div class="sk-bar sk-bar--wide" />
          <div class="sk-bar sk-bar--mid" />
          <div class="sk-cards">
            <div class="sk-card" v-for="i in 4" :key="i" />
          </div>
        </div>
      </template>

      <!-- Контент -->
      <template v-else>

        <!-- Приветствие -->
        <div class="db-hero">
          <div class="db-hero-left">
            <div class="db-avatar">{{ initials }}</div>
            <div>
              <p class="db-greeting">{{ greeting }},</p>
              <h1 class="db-name">{{ user?.name || 'Пользователь' }}</h1>
              <p class="db-message">{{ message }}</p>
            </div>
          </div>
          <div class="db-hero-meta">
            <span class="db-role" v-for="role in (user?.roles || [])" :key="role">
              {{ role }}
            </span>
            <span class="db-date">{{ new Date().toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
          </div>
        </div>

        <!-- Метрики -->
        <div class="db-metrics">
          <div class="db-metric">
            <div class="db-metric-icon db-metric-icon--blue">
              <svg viewBox="0 0 20 20" fill="none"><path d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h0a2 2 0 002-2M9 5a2 2 0 012-2h0a2 2 0 012 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="db-metric-body">
              <span class="db-metric-val">2 418</span>
              <span class="db-metric-label">Деловых событий</span>
            </div>
            <span class="db-metric-badge db-metric-badge--up">+12%</span>
          </div>

          <div class="db-metric">
            <div class="db-metric-icon db-metric-icon--teal">
              <svg viewBox="0 0 20 20" fill="none"><path d="M17 20H3a2 2 0 01-2-2V6a2 2 0 012-2h3.586a1 1 0 01.707.293l1.414 1.414A1 1 0 0010.414 6H17a2 2 0 012 2v10a2 2 0 01-2 2z" stroke="currentColor" stroke-width="1.3"/></svg>
            </div>
            <div class="db-metric-body">
              <span class="db-metric-val">180</span>
              <span class="db-metric-label">Городов</span>
            </div>
            <span class="db-metric-badge db-metric-badge--neu">0%</span>
          </div>

          <div class="db-metric">
            <div class="db-metric-icon db-metric-icon--indigo">
              <svg viewBox="0 0 20 20" fill="none"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM6 8a2 2 0 11-4 0 2 2 0 014 0zM15.992 17.016A5 5 0 0010 13a5 5 0 00-5.992 4.016M18 17a4 4 0 00-4-4M6 17a4 4 0 014-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="db-metric-body">
              <span class="db-metric-val">{{ user?.id ? '1 204' : '—' }}</span>
              <span class="db-metric-label">Участников</span>
            </div>
            <span class="db-metric-badge db-metric-badge--up">+5%</span>
          </div>

          <div class="db-metric">
            <div class="db-metric-icon db-metric-icon--sky">
              <svg viewBox="0 0 20 20" fill="none"><rect x="2" y="3" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M2 7h16M6 3v4M14 3v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
            </div>
            <div class="db-metric-body">
              <span class="db-metric-val">12</span>
              <span class="db-metric-label">Лет на рынке</span>
            </div>
            <span class="db-metric-badge db-metric-badge--up">Стабильно</span>
          </div>
        </div>

        <!-- Нижний блок -->
        <div class="db-grid">
          <div class="db-panel">
            <div class="db-panel-head">
              <h2 class="db-panel-title">Ближайшие события</h2>
              <a href="#" class="db-panel-link">Все события →</a>
            </div>
            <div class="db-events">
              <div class="db-event" v-for="(ev, i) in events" :key="i">
                <div class="db-event-dot" :style="{ background: ev.color }" />
                <div class="db-event-body">
                  <p class="db-event-title">{{ ev.title }}</p>
                  <p class="db-event-meta">{{ ev.date }} · {{ ev.city }}</p>
                </div>
                <span class="db-event-type">{{ ev.type }}</span>
              </div>
            </div>
          </div>

          <div class="db-panel">
            <div class="db-panel-head">
              <h2 class="db-panel-title">Аккаунт</h2>
            </div>
            <div class="db-profile">
              <div class="db-profile-row">
                <span class="db-profile-label">Имя</span>
                <span class="db-profile-val">{{ user?.name }}</span>
              </div>
              <div class="db-profile-row">
                <span class="db-profile-label">Email</span>
                <span class="db-profile-val">{{ user?.email }}</span>
              </div>
              <div class="db-profile-row">
                <span class="db-profile-label">Роль</span>
                <span class="db-profile-val">
                  <span class="db-role" v-for="r in (user?.roles || ['user'])" :key="r">{{ r }}</span>
                </span>
              </div>
              <div class="db-profile-row">
                <span class="db-profile-label">ID</span>
                <span class="db-profile-val db-profile-mono">#{{ user?.id }}</span>
              </div>
            </div>
          </div>
        </div>

      </template>
    </div>
  </AuthenticatedLayout>
</template>

<script lang="ts">
// Статичные данные для демонстрации
export default {
  setup() {
    const events = [
      { title: 'Форум «Бизнес и Власть»',    date: '28 марта',  city: 'Москва',          type: 'Форум',    color: '#3b82f6' },
      { title: 'Инвестиционный саммит',       date: '2 апреля',  city: 'Санкт-Петербург', type: 'Саммит',   color: '#6366f1' },
      { title: 'Конференция ТЭК',             date: '7 апреля',  city: 'Екатеринбург',    type: 'Конф.',    color: '#0ea5e9' },
      { title: 'Выставка промышленности',     date: '14 апреля', city: 'Казань',           type: 'Выставка', color: '#14b8a6' },
    ];
    return { events };
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

.db-wrap {
  font-family: 'DM Sans', sans-serif;
  display: flex; flex-direction: column; gap: 24px;
  color: #dce8f5;
}

/* ─── Скелетон ──────────────────────── */
.db-skeleton { display: flex; flex-direction: column; gap: 16px; }
.sk-bar {
  height: 18px; border-radius: 6px;
  background: linear-gradient(90deg, rgba(96,165,250,0.08) 25%, rgba(96,165,250,0.14) 50%, rgba(96,165,250,0.08) 75%);
  background-size: 200% 100%;
  animation: sk-shimmer 1.4s infinite;
}
.sk-bar--wide { width: 55%; }
.sk-bar--mid  { width: 35%; }
.sk-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-top: 8px; }
.sk-card  { height: 90px; border-radius: 12px; background: rgba(96,165,250,0.06); animation: sk-shimmer 1.4s infinite; }
@keyframes sk-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* ─── Ошибка ────────────────────────── */
.db-error {
  display: flex; align-items: center; gap: 10px;
  padding: 14px 18px;
  background: rgba(239,68,68,0.08);
  border: 1px solid rgba(239,68,68,0.2);
  border-radius: 10px;
  font-size: 14px; color: #fca5a5;
}
.db-retry {
  margin-left: auto;
  background: none; border: 1px solid rgba(239,68,68,0.3);
  border-radius: 6px; padding: 5px 12px;
  color: #fca5a5; font-size: 12px; cursor: pointer;
  transition: all 0.2s;
}
.db-retry:hover { background: rgba(239,68,68,0.1); }

/* ─── Hero ──────────────────────────── */
.db-hero {
  display: flex; align-items: center; justify-content: space-between;
  padding: 28px 32px;
  background: linear-gradient(135deg, #0d1530, #0a1020);
  border: 1px solid rgba(96,165,250,0.14);
  border-radius: 14px;
  position: relative; overflow: hidden;
}
.db-hero::before {
  content: '';
  position: absolute; top: -60px; right: -60px;
  width: 220px; height: 220px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(59,130,246,0.12), transparent 70%);
}
.db-hero-left { display: flex; align-items: center; gap: 18px; position: relative; }
.db-avatar {
  width: 52px; height: 52px; border-radius: 14px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center;
  font-family: 'Cormorant Garamond', serif;
  font-size: 20px; font-weight: 600; color: #bfdbfe;
  flex-shrink: 0;
  box-shadow: 0 0 20px rgba(37,99,235,0.3);
}
.db-greeting { font-size: 12px; color: #4a6fa5; margin: 0 0 2px; letter-spacing: 0.04em; }
.db-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px; font-weight: 600; color: #e2edf8;
  margin: 0 0 4px; line-height: 1.1;
}
.db-message { font-size: 13px; color: #4a6fa5; margin: 0; }
.db-hero-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; position: relative; }
.db-date { font-size: 12px; color: #3d5a8a; letter-spacing: 0.04em; }

/* ─── Роль-бейдж ────────────────────── */
.db-role {
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #93c5fd; background: rgba(147,197,253,0.1);
  border: 1px solid rgba(147,197,253,0.2);
  padding: 3px 10px; border-radius: 20px;
}

/* ─── Метрики ───────────────────────── */
.db-metrics {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
}
.db-metric {
  display: flex; align-items: center; gap: 14px;
  padding: 18px 20px;
  background: #0d1530;
  border: 1px solid rgba(96,165,250,0.12);
  border-radius: 12px;
  transition: border-color 0.2s;
}
.db-metric:hover { border-color: rgba(96,165,250,0.25); }
.db-metric-icon {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.db-metric-icon svg { width: 18px; height: 18px; }
.db-metric-icon--blue   { background: rgba(59,130,246,0.15); color: #60a5fa; }
.db-metric-icon--teal   { background: rgba(20,184,166,0.15); color: #2dd4bf; }
.db-metric-icon--indigo { background: rgba(99,102,241,0.15); color: #818cf8; }
.db-metric-icon--sky    { background: rgba(14,165,233,0.15); color: #38bdf8; }
.db-metric-body { flex: 1; min-width: 0; }
.db-metric-val {
  display: block;
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px; font-weight: 700; color: #e2edf8; line-height: 1;
}
.db-metric-label { font-size: 11px; color: #3d5a8a; letter-spacing: 0.06em; text-transform: uppercase; }
.db-metric-badge {
  font-size: 11px; font-weight: 500; padding: 3px 8px; border-radius: 6px; white-space: nowrap;
}
.db-metric-badge--up  { background: rgba(34,197,94,0.1); color: #86efac; }
.db-metric-badge--neu { background: rgba(96,165,250,0.1); color: #93c5fd; }

/* ─── Нижний грид ───────────────────── */
.db-grid {
  display: grid; grid-template-columns: 1fr 380px; gap: 16px;
}
.db-panel {
  background: #0d1530;
  border: 1px solid rgba(96,165,250,0.12);
  border-radius: 12px;
  padding: 24px;
}
.db-panel-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 20px;
}
.db-panel-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px; font-weight: 600; color: #c7daf0; margin: 0;
}
.db-panel-link { font-size: 12px; color: #3b82f6; text-decoration: none; }
.db-panel-link:hover { color: #93c5fd; }

/* ─── События ───────────────────────── */
.db-events { display: flex; flex-direction: column; gap: 4px; }
.db-event {
  display: flex; align-items: center; gap: 12px;
  padding: 10px 12px; border-radius: 8px;
  transition: background 0.15s;
}
.db-event:hover { background: rgba(96,165,250,0.05); }
.db-event-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.db-event-body { flex: 1; min-width: 0; }
.db-event-title { font-size: 14px; color: #c7daf0; margin: 0 0 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.db-event-meta  { font-size: 12px; color: #3d5a8a; margin: 0; }
.db-event-type  {
  font-size: 11px; color: #4a6fa5;
  background: rgba(96,165,250,0.08);
  padding: 3px 8px; border-radius: 5px;
  white-space: nowrap;
}

/* ─── Профиль ───────────────────────── */
.db-profile { display: flex; flex-direction: column; }
.db-profile-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid rgba(96,165,250,0.07);
}
.db-profile-row:last-child { border-bottom: none; }
.db-profile-label { font-size: 12px; color: #3d5a8a; letter-spacing: 0.04em; }
.db-profile-val   { font-size: 13px; color: #a8c4e8; }
.db-profile-mono  { font-family: monospace; font-size: 12px; color: #4a6fa5; }

/* ─── Адаптив ───────────────────────── */
@media (max-width: 1024px) {
  .db-metrics { grid-template-columns: repeat(2,1fr); }
  .db-grid    { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
  .db-metrics { grid-template-columns: 1fr 1fr; }
  .db-hero    { flex-direction: column; align-items: flex-start; gap: 16px; }
  .db-hero-meta { align-items: flex-start; }
}
</style>

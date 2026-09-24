<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuth } from '@/composable/useAuth';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { getErrorMessage } from '@/lib/errors';

const { user, checkAuth } = useAuth();

// ─── Типы ──────────────────────────────────────────────────────────────────
interface Lead {
  id: number;
  name: string;
  company: string;
  phone: string;
  source: string | null;
  created_at: string | null;
}

// ─── Глобальное состояние ──────────────────────────────────────────────────
const loading = ref(true);
const error   = ref('');
const leads   = ref<Lead[]>([]);

// ─── Загрузка ──────────────────────────────────────────────────────────────
const loadData = async () => {
  loading.value = true;
  error.value   = '';
  try {
    if (!user.value) await checkAuth();
    const { data } = await axios.get('/api/leads');
    leads.value = Array.isArray(data?.leads) ? data.leads : [];
  } catch (e) {
    error.value = getErrorMessage(e, 'Ошибка загрузки');
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);

const formatDate = (iso: string | null) => {
  if (!iso) return '—';
  return new Date(iso).toLocaleString('ru-RU', {
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit',
  });
};

// ─── Удаление ──────────────────────────────────────────────────────────────
const deleteConfirmId = ref<number | null>(null);
const deleting         = ref(false);

const confirmDelete = (id: number) => { deleteConfirmId.value = id; };
const cancelDelete  = ()           => { deleteConfirmId.value = null; };

const doDelete = async (id: number) => {
  deleting.value = true;
  try {
    await axios.delete(`/api/leads/${id}`);
    leads.value = leads.value.filter(l => l.id !== id);
    deleteConfirmId.value = null;
  } catch (e) {
    error.value = getErrorMessage(e, 'Ошибка удаления');
  } finally {
    deleting.value = false;
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="pl-wrap">

      <!-- ── Шапка ─────────────────────────────────────────────── -->
      <div class="pl-header">
        <div>
          <h1 class="pl-title">Заявки</h1>
          <p class="pl-subtitle">Заявки из формы «Хочу в календарь»</p>
        </div>
        <button class="pl-btn pl-btn--ghost" :disabled="loading" @click="loadData">Обновить</button>
      </div>

      <!-- ── Ошибка ─────────────────────────────────────────────── -->
      <div v-if="error" class="pl-alert">
        <svg viewBox="0 0 20 20" fill="none">
          <circle cx="10" cy="10" r="8" stroke="#fca5a5" stroke-width="1.3"/>
          <path d="M10 6v5M10 14h.01" stroke="#fca5a5" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        {{ error }}
        <button class="pl-retry" @click="loadData">Повторить</button>
      </div>

      <!-- ── Скелетон ───────────────────────────────────────────── -->
      <div v-if="loading" class="pl-skeleton">
        <div class="sk-row" v-for="i in 5" :key="i" />
      </div>

      <!-- ── Контент ────────────────────────────────────────────── -->
      <template v-else>

        <!-- Пустое состояние -->
        <div v-if="leads.length === 0" class="pl-empty">
          <svg viewBox="0 0 48 48" fill="none" class="pl-empty-icon">
            <rect x="6" y="10" width="36" height="28" rx="4" stroke="#3b82f6" stroke-width="1.5"/>
            <path d="M6 14l18 12 18-12" stroke="#3b82f6" stroke-width="1.5" stroke-linejoin="round"/>
          </svg>
          <p class="pl-empty-text">Заявок пока нет</p>
        </div>

        <!-- Таблица -->
        <div v-else class="ld-table-wrap">
          <table class="ld-table">
            <thead>
              <tr>
                <th>Дата</th>
                <th>Имя</th>
                <th>Компания</th>
                <th>Телефон</th>
                <th>Страница</th>
                <th />
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads" :key="lead.id">
                <td class="ld-date" data-label="Дата">{{ formatDate(lead.created_at) }}</td>
                <td data-label="Имя">{{ lead.name }}</td>
                <td data-label="Компания">{{ lead.company }}</td>
                <td data-label="Телефон"><a :href="`tel:${lead.phone}`" class="ld-phone">{{ lead.phone }}</a></td>
                <td class="ld-source" data-label="Страница">{{ lead.source || '—' }}</td>
                <td><div class="ld-actions">
                  <template v-if="deleteConfirmId === lead.id">
                    <span class="pl-confirm-text">Удалить?</span>
                    <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(lead.id)" title="Подтвердить">
                      <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    </button>
                    <button class="pl-icon-btn" @click="cancelDelete" title="Отмена">
                      <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    </button>
                  </template>
                  <button v-else class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(lead.id)" title="Удалить">
                    <svg viewBox="0 0 20 20" fill="none">
                      <path d="M4 6h12M8 6V4h4v2M9 10v5M11 10v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                      <rect x="5" y="6" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                    </svg>
                  </button>
                </div></td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap');

/* ── Переменные ────────────────────────────────────────────────── */
:root {
  --bg-card:   #0d1530;
  --border:    rgba(96,165,250,0.12);
  --border-hi: rgba(96,165,250,0.28);
  --text-h:    #e2edf8;
  --text-b:    #a8c4e8;
  --text-mute: #3d5a8a;
  --accent:    #3b82f6;
  --accent-hi: #60a5fa;
  --danger:    #ef4444;
  --danger-bg: rgba(239,68,68,0.1);
}

.pl-wrap { font-family: 'DM Sans', sans-serif; display: flex; flex-direction: column; gap: 24px; color: var(--text-b); }

.pl-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
.pl-title { font-family: 'Cormorant Garamond', serif; font-size: 32px; font-weight: 700; color: var(--text-h); margin: 0 0 4px; line-height: 1.1; }
.pl-subtitle { font-size: 13px; color: var(--text-mute); margin: 0; }

.pl-btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500; cursor: pointer; border: none; outline: none; transition: all 0.18s; white-space: nowrap; }
.pl-btn--ghost { background: rgba(96,165,250,0.07); color: var(--text-b); border: 1px solid var(--border); }
.pl-btn--ghost:hover { background: rgba(96,165,250,0.13); border-color: var(--border-hi); }
.pl-btn:disabled { opacity: 0.55; cursor: not-allowed; }

.pl-icon-btn { width: 32px; height: 32px; padding: 0; border: none; outline: none; border-radius: 7px; cursor: pointer; display: flex; align-items: center; justify-content: center; background: rgba(96,165,250,0.07); color: var(--text-b); transition: all 0.15s; text-decoration: none; }
.pl-icon-btn svg { width: 15px; height: 15px; }
.pl-icon-btn:hover { background: rgba(96,165,250,0.15); color: var(--text-h); }
.pl-icon-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pl-icon-btn--danger  { background: var(--danger-bg); color: var(--danger); }
.pl-icon-btn--danger:hover  { background: rgba(239,68,68,0.2); }

.pl-alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: var(--danger-bg); border: 1px solid rgba(239,68,68,0.2); border-radius: 10px; font-size: 13px; color: #fca5a5; }
.pl-alert svg { width: 18px; height: 18px; flex-shrink: 0; }
.pl-retry { margin-left: auto; background: none; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; padding: 4px 10px; color: #fca5a5; font-size: 12px; cursor: pointer; }
.pl-retry:hover { background: rgba(239,68,68,0.08); }

.pl-skeleton { display: flex; flex-direction: column; gap: 8px; }
.sk-row { height: 48px; border-radius: 10px; background: rgba(96,165,250,0.05); animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0%,100% { opacity:.6; } 50% { opacity:1; } }

.pl-empty { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 60px 24px; background: var(--bg-card); border: 1px dashed var(--border); border-radius: 14px; text-align: center; }
.pl-empty-icon { width: 48px; height: 48px; opacity: 0.4; }
.pl-empty-text { font-size: 15px; color: var(--text-mute); margin: 0; }

.pl-confirm-text { font-size: 12px; color: var(--danger); white-space: nowrap; }

/* ── Таблица заявок ────────────────────────────────────────────── */
.ld-table-wrap { background: rgba(96,165,250,0.03); border: 1px solid var(--border); border-radius: 12px; overflow-x: auto; }
.ld-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.ld-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 500; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-mute); border-bottom: 1px solid var(--border); white-space: nowrap; }
.ld-table td { padding: 12px 16px; color: var(--text-h); border-bottom: 1px solid var(--border); vertical-align: middle; }
.ld-table tbody tr:last-child td { border-bottom: none; }
.ld-table tbody tr:hover td { background: rgba(96,165,250,0.04); }
.ld-date { white-space: nowrap; color: var(--text-b) !important; }
.ld-phone { color: var(--accent-hi); text-decoration: none; white-space: nowrap; }
.ld-phone:hover { text-decoration: underline; }
.ld-source { color: var(--text-mute) !important; font-size: 12px; }
.ld-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }

@media (max-width: 768px) {
  .pl-header { flex-direction: column; align-items: flex-start; }

  /* На мобильных — карточки вместо таблицы */
  .ld-table thead { display: none; }
  .ld-table, .ld-table tbody, .ld-table tr, .ld-table td { display: block; width: 100%; }
  .ld-table tr { padding: 12px 16px; border-bottom: 1px solid var(--border); }
  .ld-table tbody tr:last-child { border-bottom: none; }
  .ld-table td { padding: 3px 0; border-bottom: none; }
  .ld-table td[data-label]::before { content: attr(data-label) ': '; color: var(--text-mute); font-size: 12px; }
  .ld-actions { justify-content: flex-start; margin-top: 8px; }
}
</style>

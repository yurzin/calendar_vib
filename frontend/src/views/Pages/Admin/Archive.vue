<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composable/useAuth';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { getErrorMessage, getValidationErrors } from '@/lib/errors';

const { user, checkAuth } = useAuth();

// ─── Типы ──────────────────────────────────────────────────────────────────
interface Issue {
  id: number;
  year: number;
  cover: string | null;
  pdf_url: string | null;
  pdf_name: string | null;
  page_url: string | null;
}

// ─── Глобальное состояние ──────────────────────────────────────────────────
const loading = ref(true);
const error   = ref('');
const issues  = ref<Issue[]>([]);

const sorted = computed(() => [...issues.value].sort((a, b) => b.year - a.year));

// ─── Загрузка ──────────────────────────────────────────────────────────────
const loadData = async () => {
  loading.value = true;
  error.value   = '';
  try {
    if (!user.value) await checkAuth();
    const { data } = await axios.get('/api/archive');
    issues.value = Array.isArray(data?.issues) ? data.issues : [];
  } catch (e) {
    error.value = getErrorMessage(e, 'Ошибка загрузки');
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);

// ─── Форма ─────────────────────────────────────────────────────────────────
type FormState = {
  id: number | null;
  year: number | null;
  page_url: string;
};

const emptyForm = (): FormState => ({ id: null, year: new Date().getFullYear() + 1, page_url: '' });

const modalOpen   = ref(false);
const modalMode   = ref<'create' | 'edit'>('create');
const form        = ref<FormState>(emptyForm());
const formErrors  = ref<Record<string, string>>({});
const saving      = ref(false);

// Обложка
const coverFile    = ref<File | null>(null);
const coverPreview = ref<string>('');

const onCoverChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  coverFile.value = file;
  const reader = new FileReader();
  reader.onload = ev => { coverPreview.value = ev.target?.result as string; };
  reader.readAsDataURL(file);
};

const removeCover = () => {
  coverFile.value    = null;
  coverPreview.value = '';
};

// PDF
const pdfFile      = ref<File | null>(null);
const pdfFileName  = ref('');
const existingPdfName = ref('');

const onPdfChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  pdfFile.value     = file;
  pdfFileName.value = file.name;
};

const removePdf = () => {
  pdfFile.value     = null;
  pdfFileName.value = '';
};

// ─── Модалка ───────────────────────────────────────────────────────────────
const resetModal = () => {
  formErrors.value     = {};
  coverFile.value      = null;
  coverPreview.value   = '';
  pdfFile.value        = null;
  pdfFileName.value    = '';
  existingPdfName.value = '';
};

const openCreate = () => {
  form.value = emptyForm();
  modalMode.value = 'create';
  resetModal();
  modalOpen.value = true;
};

const openEdit = (issue: Issue) => {
  form.value = {
    id: issue.id,
    year: issue.year,
    page_url: issue.page_url || '',
  };
  resetModal();
  coverPreview.value    = issue.cover || '';
  existingPdfName.value = issue.pdf_name || '';
  modalMode.value = 'edit';
  modalOpen.value = true;
};

const closeModal = () => {
  modalOpen.value = false;
  resetModal();
};

// ─── Валидация ─────────────────────────────────────────────────────────────
const validate = (): boolean => {
  const e: Record<string, string> = {};
  if (!form.value.year || form.value.year < 2000 || form.value.year > 2100) {
    e.year = 'Укажите корректный год';
  }
  formErrors.value = e;
  return !Object.keys(e).length;
};

// ─── Сохранение ────────────────────────────────────────────────────────────
const save = async () => {
  if (!validate()) return;
  saving.value = true;
  try {
    const fd = new FormData();
    fd.append('year', String(form.value.year));
    if (form.value.page_url.trim()) fd.append('page_url', form.value.page_url.trim());
    if (coverFile.value) fd.append('cover', coverFile.value);
    if (pdfFile.value)   fd.append('pdf', pdfFile.value);

    const headers = { 'Content-Type': 'multipart/form-data' };

    if (modalMode.value === 'create') {
      const { data } = await axios.post('/api/archive', fd, { headers });
      issues.value.unshift(data);
    } else {
      fd.append('_method', 'PUT');
      const { data } = await axios.post(`/api/archive/${form.value.id}`, fd, { headers });
      const idx = issues.value.findIndex(i => i.id === form.value.id);
      if (idx !== -1) issues.value[idx] = data;
    }
    closeModal();
  } catch (e) {
    const errs = getValidationErrors(e);
    if (errs) {
      formErrors.value = Object.fromEntries(Object.entries(errs).map(([k, v]) => [k, v[0]]));
    } else {
      formErrors.value.global = getErrorMessage(e, 'Ошибка сохранения');
    }
  } finally {
    saving.value = false;
  }
};

// ─── Удаление ──────────────────────────────────────────────────────────────
const deleteConfirmId = ref<number | null>(null);
const deleting         = ref(false);

const confirmDelete = (id: number) => { deleteConfirmId.value = id; };
const cancelDelete  = ()           => { deleteConfirmId.value = null; };

const doDelete = async (id: number) => {
  deleting.value = true;
  try {
    await axios.delete(`/api/archive/${id}`);
    issues.value = issues.value.filter(i => i.id !== id);
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
          <h1 class="pl-title">Архив номеров</h1>
          <p class="pl-subtitle">Обложки и PDF выпусков календаря</p>
        </div>
        <button class="pl-btn pl-btn--primary" @click="openCreate">
          <svg viewBox="0 0 20 20" fill="none" class="pl-btn-icon">
            <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          Добавить выпуск
        </button>
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
      <template v-if="loading">
        <div class="pl-skeleton">
          <div class="sk-grid">
            <div class="sk-card" v-for="i in 6" :key="i" />
          </div>
        </div>
      </template>

      <!-- ── Контент ────────────────────────────────────────────── -->
      <template v-else>

        <!-- Пустое состояние -->
        <div v-if="sorted.length === 0" class="pl-empty">
          <svg viewBox="0 0 48 48" fill="none" class="pl-empty-icon">
            <rect x="6" y="10" width="36" height="28" rx="4" stroke="#3b82f6" stroke-width="1.5"/>
            <path d="M16 24h16M16 30h10" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <p class="pl-empty-text">Выпусков пока нет</p>
          <button class="pl-btn pl-btn--primary" @click="openCreate">Добавить первый выпуск</button>
        </div>

        <!-- Сетка карточек -->
        <div v-else class="ar-grid">
          <div v-for="issue in sorted" :key="issue.id" class="ar-card">
            <div class="ar-cover">
              <img v-if="issue.cover" :src="issue.cover" :alt="`Обложка ${issue.year}`" class="ar-cover-img" />
              <div v-else class="ar-cover-placeholder">{{ issue.year }}</div>
            </div>
            <div class="ar-card-body">
              <h2 class="ar-card-year">{{ issue.year }}</h2>
              <span v-if="issue.pdf_url" class="ar-pdf-badge ar-pdf-badge--ok">
                <svg viewBox="0 0 16 16" fill="none" width="12" height="12"><path d="M3 8l3.5 3.5L13 4.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                PDF загружен
              </span>
              <span v-else class="ar-pdf-badge ar-pdf-badge--empty">PDF не загружен</span>
            </div>
            <div class="ar-card-actions">
              <template v-if="deleteConfirmId === issue.id">
                <span class="pl-confirm-text">Удалить?</span>
                <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(issue.id)" title="Подтвердить">
                  <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <button class="pl-icon-btn" @click="cancelDelete" title="Отмена">
                  <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
              </template>
              <template v-else>
                <a v-if="issue.pdf_url" :href="issue.pdf_url" target="_blank" rel="noopener" class="pl-icon-btn" title="Открыть PDF">
                  <svg viewBox="0 0 20 20" fill="none"><path d="M10 4v10M5 9l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <button class="pl-icon-btn" @click="openEdit(issue)" title="Редактировать">
                  <svg viewBox="0 0 20 20" fill="none">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828L7 15.828 3 17l1.172-4L13.586 3.586z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <button class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(issue.id)" title="Удалить">
                  <svg viewBox="0 0 20 20" fill="none">
                    <path d="M4 6h12M8 6V4h4v2M9 10v5M11 10v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
                    <rect x="5" y="6" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
                  </svg>
                </button>
              </template>
            </div>
          </div>
        </div>
      </template>

      <!-- ── Модальное окно ─────────────────────────────────────── -->
      <Transition name="modal">
        <div v-if="modalOpen" class="pl-overlay" @click.self="closeModal">
          <div class="pl-modal">

            <div class="pl-modal-head">
              <h2 class="pl-modal-title">
                {{ modalMode === 'create' ? 'Новый выпуск' : 'Редактирование выпуска' }}
              </h2>
              <button class="pl-icon-btn" @click="closeModal">
                <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              </button>
            </div>

            <div class="pl-modal-form">

              <!-- Год -->
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.year }">
                <label class="pl-label">Год *</label>
                <input v-model.number="form.year" type="number" class="pl-input" placeholder="2027" />
                <span v-if="formErrors.year" class="pl-field-err">{{ formErrors.year }}</span>
              </div>

              <!-- Обложка -->
              <div class="pl-field">
                <label class="pl-label">Обложка</label>
                <label class="pl-upload" :class="{ 'pl-upload--has': coverPreview }">
                  <img v-if="coverPreview" :src="coverPreview" class="pl-upload-preview" alt="preview" />
                  <div v-else class="pl-upload-placeholder">
                    <svg viewBox="0 0 24 24" fill="none">
                      <path d="M12 16V8M12 8l-3 3M12 8l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor" stroke-width="1.3"/>
                    </svg>
                    <span>Нажмите или перетащите файл</span>
                    <span class="pl-upload-hint">JPG, PNG, WEBP · до 4 МБ</span>
                  </div>
                  <input type="file" accept="image/jpeg,image/png,image/webp" class="pl-upload-input" @change="onCoverChange" />
                </label>
                <button v-if="coverPreview" class="pl-upload-clear" type="button" @click="removeCover">
                  × Убрать обложку
                </button>
                <span v-if="formErrors.cover" class="pl-field-err">{{ formErrors.cover }}</span>
              </div>

              <!-- PDF -->
              <div class="pl-field">
                <label class="pl-label">PDF-файл</label>
                <label class="pl-upload pl-upload--pdf" :class="{ 'pl-upload--has': pdfFileName || existingPdfName }">
                  <div class="pl-upload-placeholder">
                    <svg viewBox="0 0 24 24" fill="none">
                      <path d="M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                      <path d="M15 3v5h5" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
                    </svg>
                    <span v-if="pdfFileName">{{ pdfFileName }}</span>
                    <span v-else-if="existingPdfName">{{ existingPdfName }} <em>(текущий файл)</em></span>
                    <span v-else>Нажмите или перетащите PDF</span>
                    <span class="pl-upload-hint">PDF · до 50 МБ</span>
                  </div>
                  <input type="file" accept="application/pdf" class="pl-upload-input" @change="onPdfChange" />
                </label>
                <button v-if="pdfFileName" class="pl-upload-clear" type="button" @click="removePdf">
                  × Отменить выбор нового файла
                </button>
                <span v-if="formErrors.pdf" class="pl-field-err">{{ formErrors.pdf }}</span>
              </div>

              <!-- Ссылка на страницу выпуска -->
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.page_url }">
                <label class="pl-label">Ссылка «Смотреть» (необязательно)</label>
                <input v-model="form.page_url" class="pl-input" placeholder="https://…" />
                <span v-if="formErrors.page_url" class="pl-field-err">{{ formErrors.page_url }}</span>
                <span class="pl-field-hint">Если не указана — кнопка «Смотреть» не показывается</span>
              </div>

              <div v-if="formErrors.global" class="pl-alert pl-alert--sm">{{ formErrors.global }}</div>

            </div>

            <div class="pl-modal-foot">
              <button class="pl-btn pl-btn--ghost" @click="closeModal">Отмена</button>
              <button class="pl-btn pl-btn--primary" :disabled="saving" @click="save">
                <span v-if="saving" class="pl-spinner" />
                {{ modalMode === 'create' ? 'Добавить' : 'Сохранить' }}
              </button>
            </div>

          </div>
        </div>
      </Transition>

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
.pl-btn--primary { background: var(--accent); color: #fff; box-shadow: 0 0 20px rgba(59,130,246,0.25); }
.pl-btn--primary:hover { background: #2563eb; box-shadow: 0 0 28px rgba(59,130,246,0.35); }
.pl-btn--primary:disabled { opacity: 0.55; cursor: not-allowed; }
.pl-btn--ghost { background: rgba(96,165,250,0.07); color: var(--text-b); border: 1px solid var(--border); }
.pl-btn--ghost:hover { background: rgba(96,165,250,0.13); border-color: var(--border-hi); }
.pl-btn-icon { width: 16px; height: 16px; }

.pl-icon-btn { width: 32px; height: 32px; padding: 0; border: none; outline: none; border-radius: 7px; cursor: pointer; display: flex; align-items: center; justify-content: center; background: rgba(96,165,250,0.07); color: var(--text-b); transition: all 0.15s; text-decoration: none; }
.pl-icon-btn svg { width: 15px; height: 15px; }
.pl-icon-btn:hover { background: rgba(96,165,250,0.15); color: var(--text-h); }
.pl-icon-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pl-icon-btn--danger  { background: var(--danger-bg); color: var(--danger); }
.pl-icon-btn--danger:hover  { background: rgba(239,68,68,0.2); }

.pl-alert { display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: var(--danger-bg); border: 1px solid rgba(239,68,68,0.2); border-radius: 10px; font-size: 13px; color: #fca5a5; }
.pl-alert--sm { padding: 10px 14px; }
.pl-alert svg { width: 18px; height: 18px; flex-shrink: 0; }
.pl-retry { margin-left: auto; background: none; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; padding: 4px 10px; color: #fca5a5; font-size: 12px; cursor: pointer; }
.pl-retry:hover { background: rgba(239,68,68,0.08); }

.pl-skeleton { display: flex; flex-direction: column; gap: 16px; }
.sk-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
.sk-card { height: 220px; border-radius: 12px; background: rgba(96,165,250,0.05); animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0%,100% { opacity:.6; } 50% { opacity:1; } }

.pl-empty { display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 60px 24px; background: var(--bg-card); border: 1px dashed var(--border); border-radius: 14px; text-align: center; }
.pl-empty-icon { width: 48px; height: 48px; opacity: 0.4; }
.pl-empty-text { font-size: 15px; color: var(--text-mute); margin: 0; }

.pl-confirm-text { font-size: 12px; color: var(--danger); white-space: nowrap; margin-right: auto; }

/* ── Сетка выпусков ────────────────────────────────────────────── */
.ar-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.ar-card { display: flex; flex-direction: column; gap: 12px; padding: 14px; background: rgba(96,165,250,0.05); border: 1px solid var(--border); border-radius: 12px; transition: border-color 0.2s, box-shadow 0.2s; }
.ar-card:hover { border-color: var(--border-hi); box-shadow: 0 4px 24px rgba(59,130,246,0.08); }
.ar-cover { width: 100%; aspect-ratio: 16/9; border-radius: 8px; overflow: hidden; background: linear-gradient(145deg, #0d1836, #091228); display: flex; align-items: center; justify-content: center; }
.ar-cover-img { width: 100%; height: 100%; object-fit: cover; }
.ar-cover-placeholder { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 700; color: rgba(147,197,253,0.25); }
.ar-card-body { display: flex; flex-direction: column; gap: 6px; }
.ar-card-year { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 700; color: var(--text-h); margin: 0; }
.ar-pdf-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; letter-spacing: 0.04em; padding: 3px 9px; border-radius: 20px; width: fit-content; }
.ar-pdf-badge--ok { color: #86efac; background: rgba(34,197,94,0.1); }
.ar-pdf-badge--empty { color: var(--text-mute); background: rgba(96,165,250,0.06); }
.ar-card-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; border-top: 1px solid var(--border); padding-top: 10px; }

/* ── Модалка ───────────────────────────────────────────────────── */
.pl-overlay { position: fixed; inset: 0; z-index: 200; background: rgba(5,10,24,0.8); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; padding: 16px; }
.pl-modal { width: 100%; max-width: 480px; background: #0a1228; border: 1px solid var(--border-hi); border-radius: 16px; overflow: hidden; box-shadow: 0 24px 80px rgba(0,0,0,0.6); max-height: 90vh; overflow-y: auto; }
.pl-modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--border); position: sticky; top: 0; background: #0a1228; z-index: 1; }
.pl-modal-title { font-family: 'Cormorant Garamond', serif; font-size: 22px; font-weight: 600; color: var(--text-h); margin: 0; }

.pl-modal-form { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }
.pl-field { display: flex; flex-direction: column; gap: 6px; }
.pl-field--err .pl-input { border-color: rgba(239,68,68,0.5); }
.pl-label { font-size: 11px; color: var(--text-mute); letter-spacing: 0.07em; text-transform: uppercase; }
.pl-input { padding: 10px 14px; background: rgba(255,255,255,0.04); border: 1px solid var(--border); border-radius: 8px; color: var(--text-h); font-size: 14px; font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.2s; width: 100%; box-sizing: border-box; }
.pl-input::placeholder { color: var(--text-mute); opacity: 0.5; }
.pl-input:focus { border-color: var(--border-hi); background: rgba(255,255,255,0.06); }
.pl-field-err  { font-size: 12px; color: #fca5a5; }
.pl-field-hint { font-size: 11px; color: var(--text-mute); }

.pl-modal-foot { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border); position: sticky; bottom: 0; background: #0a1228; }

/* ── Загрузка файла ────────────────────────────────────────────── */
.pl-upload { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; padding: 20px; border: 1.5px dashed var(--border-hi); border-radius: 10px; cursor: pointer; background: rgba(59,130,246,0.03); transition: all 0.2s; position: relative; overflow: hidden; min-height: 90px; }
.pl-upload:hover { border-color: var(--accent); background: rgba(59,130,246,0.07); }
.pl-upload--has { padding: 0; border-style: solid; min-height: 110px; }
.pl-upload--pdf.pl-upload--has { padding: 20px; border-style: dashed; min-height: 90px; }
.pl-upload-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.pl-upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center; }
.pl-upload-placeholder svg { width: 28px; height: 28px; color: var(--accent); opacity: 0.7; }
.pl-upload-placeholder span { font-size: 13px; color: var(--text-b); word-break: break-all; }
.pl-upload-placeholder em { color: var(--text-mute); font-style: italic; }
.pl-upload-hint { font-size: 11px; color: var(--text-mute) !important; }
.pl-upload-preview { width: 100%; height: 110px; object-fit: cover; }
.pl-upload-clear { background: none; border: none; font-size: 12px; color: var(--danger); cursor: pointer; padding: 0; text-align: left; }
.pl-upload-clear:hover { text-decoration: underline; }

.pl-spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from,   .modal-leave-to     { opacity: 0; }
.modal-enter-active .pl-modal, .modal-leave-active .pl-modal { transition: transform 0.2s; }
.modal-enter-from   .pl-modal { transform: translateY(14px); }
.modal-leave-to     .pl-modal { transform: translateY(8px); }

@media (max-width: 1024px) { .ar-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 768px)  { .ar-grid { grid-template-columns: repeat(2,1fr); } .pl-header { flex-direction: column; align-items: flex-start; } }
@media (max-width: 480px)  { .ar-grid { grid-template-columns: 1fr; } .sk-grid { grid-template-columns: 1fr; } }
</style>

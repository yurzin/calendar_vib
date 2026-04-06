<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composable/useAuth';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const { user, checkAuth } = useAuth();

// ─── Типы ──────────────────────────────────────────────────────────────────
interface Profile {
  id: number;
  name: string;
}

interface Partner {
  id: number;
  name: string;
  logo: string | null;
  site: string;
  checked: boolean;       // false = soft deleted
  profile_id: number | null;
  profile: Profile | null;
}

// ─── Глобальное состояние ──────────────────────────────────────────────────
const loading = ref(true);
const error   = ref('');
const partners    = ref<Partner[]>([]);
const searchQuery = ref('');

const filtered = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return partners.value;
  return partners.value.filter(p =>
    (p.name  || '').toLowerCase().includes(q) ||
    (p.site  || '').toLowerCase().includes(q) ||
    (p.profile?.name || '').toLowerCase().includes(q)
  );
});

// ─── Загрузка ──────────────────────────────────────────────────────────────
const loadData = async () => {
  loading.value = true;
  error.value   = '';
  try {
    if (!user.value) await checkAuth();
    const { data } = await axios.get('/api/partners');
    partners.value = Array.isArray(data?.partners) ? data.partners : [];
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка загрузки';
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);

// ─── Хелперы ───────────────────────────────────────────────────────────────
const initials = (name?: string | null) =>
  (name || '').split(' ').map(w => w[0]).filter(Boolean).join('').toUpperCase().slice(0, 2) || '?';

const normalizeUrl = (url?: string | null) =>
  url && !url.startsWith('http') ? 'https://' + url : (url || '');

const stripProtocol = (url?: string | null) =>
  (url || '').replace(/^https?:\/\//, '');

// ─── Форма ─────────────────────────────────────────────────────────────────
type FormState = {
  id: number | null;
  name: string;
  site: string;
  logo: string | null;
  profile_id: number | null;
};

const emptyForm = (): FormState => ({ id: null, name: '', site: '', logo: null, profile_id: null });

const modalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const form      = ref<FormState>(emptyForm());
const formErrors = ref<Record<string, string>>({});
const saving     = ref(false);

// Логотип
const logoFile    = ref<File | null>(null);
const logoPreview = ref<string>('');

// ─── Профили ───────────────────────────────────────────────────────────────
const profiles        = ref<Profile[]>([]);
const profileSearch   = ref('');
const profileLoading  = ref(false);
const profileDropOpen = ref(false);
const selectedProfile = ref<Profile | null>(null);
const creatingProfile = ref(false);

const profileExactMatch = computed(() =>
  profiles.value.some(
    p => p.name.toLowerCase() === profileSearch.value.trim().toLowerCase()
  )
);

let profileTimer: ReturnType<typeof setTimeout>;

const fetchProfiles = async (q = '') => {
  profileLoading.value = true;
  try {
    const { data } = await axios.get('/api/profiles/search', { params: { q } });
    profiles.value = data.profiles ?? [];
  } catch {
    profiles.value = [];
  } finally {
    profileLoading.value = false;
  }
};

const onProfileInput = () => {
  profileDropOpen.value = true;
  clearTimeout(profileTimer);
  profileTimer = setTimeout(() => fetchProfiles(profileSearch.value), 300);
};

const onProfileFocus = () => {
  profileDropOpen.value = true;
  fetchProfiles(profileSearch.value);
};

const selectProfile = (p: Profile) => {
  selectedProfile.value = p;
  form.value.profile_id = p.id;
  profileSearch.value   = p.name;
  profileDropOpen.value = false;
};

const clearProfile = () => {
  selectedProfile.value = null;
  form.value.profile_id = null;
  profileSearch.value   = '';
};

const createAndSelectProfile = async () => {
  const name = profileSearch.value.trim();
  if (!name || creatingProfile.value) return;
  creatingProfile.value = true;
  try {
    const { data } = await axios.post('/api/profiles', { name });
    profiles.value.unshift(data);
    selectProfile(data);
  } catch {
    /* ignore */
  } finally {
    creatingProfile.value = false;
  }
};

// ─── Файл ──────────────────────────────────────────────────────────────────
const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  logoFile.value = file;
  const reader = new FileReader();
  reader.onload = ev => { logoPreview.value = ev.target?.result as string; };
  reader.readAsDataURL(file);
};

const removeLogo = () => {
  logoFile.value    = null;
  logoPreview.value = '';
};

// ─── Модалка ───────────────────────────────────────────────────────────────
const resetModal = () => {
  formErrors.value  = {};
  logoFile.value    = null;
  logoPreview.value = '';
  clearProfile();
};

const openCreate = () => {
  form.value  = emptyForm();
  modalMode.value = 'create';
  resetModal();
  fetchProfiles();
  modalOpen.value = true;
};

const openEdit = (p: Partner) => {
  form.value = {
    id:         p.id,
    name:       p.name,
    site:       p.site,
    logo:       p.logo,
    profile_id: p.profile_id,
  };
  logoPreview.value     = p.logo || '';
  selectedProfile.value = p.profile ?? null;
  profileSearch.value   = p.profile?.name ?? '';
  modalMode.value = 'edit';
  formErrors.value = {};
  logoFile.value   = null;
  fetchProfiles();
  modalOpen.value = true;
};

const closeModal = () => {
  modalOpen.value = false;
  resetModal();
};

// ─── Валидация ─────────────────────────────────────────────────────────────
const validate = (): boolean => {
  const e: Record<string, string> = {};
  if (!form.value.name.trim()) e.name = 'Укажите название';
  if (!form.value.site.trim()) e.site = 'Укажите сайт';
  formErrors.value = e;
  return !Object.keys(e).length;
};

// ─── Сохранение ────────────────────────────────────────────────────────────
const save = async () => {
  if (!validate()) return;
  saving.value = true;
  try {
    const fd = new FormData();
    fd.append('name', form.value.name.trim());
    fd.append('url',  normalizeUrl(form.value.site.trim()));
    if (logoFile.value)          fd.append('logo',       logoFile.value);
    if (form.value.profile_id)   fd.append('profile_id', String(form.value.profile_id));

    const headers = { 'Content-Type': 'multipart/form-data' };

    if (modalMode.value === 'create') {
      const { data } = await axios.post('/api/partners', fd, { headers });
      partners.value.unshift(data);
    } else {
      fd.append('_method', 'PUT');
      const { data } = await axios.post(`/api/partners/${form.value.id}`, fd, { headers });
      const idx = partners.value.findIndex(p => p.id === form.value.id);
      if (idx !== -1) partners.value[idx] = data;
    }
    closeModal();
  } catch (e: any) {
    const errs = e.response?.data?.errors;
    if (errs) {
      formErrors.value = Object.fromEntries(
        Object.entries(errs).map(([k, v]) => [k === 'url' ? 'site' : k, (v as string[])[0]])
      );
    } else {
      formErrors.value.global = e.response?.data?.message || 'Ошибка сохранения';
    }
  } finally {
    saving.value = false;
  }
};

// ─── Удаление ──────────────────────────────────────────────────────────────
const deleteConfirmId = ref<number | null>(null);
const deleting        = ref(false);

const confirmDelete = (id: number) => { deleteConfirmId.value = id; };
const cancelDelete  = ()           => { deleteConfirmId.value = null; };

const doDelete = async (id: number) => {
  deleting.value = true;
  try {
    await axios.delete(`/api/partners/${id}`);
    const p = partners.value.find(p => p.id === id);
    if (p) p.checked = false;
    deleteConfirmId.value = null;
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка удаления';
  } finally {
    deleting.value = false;
  }
};

// ─── Восстановление ────────────────────────────────────────────────────────
const restoring = ref<number | null>(null);

const doRestore = async (id: number) => {
  restoring.value = id;
  try {
    await axios.post(`/api/partners/${id}/restore`);
    const p = partners.value.find(p => p.id === id);
    if (p) p.checked = true;
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка восстановления';
  } finally {
    restoring.value = null;
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="pl-wrap">

      <!-- ── Шапка ─────────────────────────────────────────────── -->
      <div class="pl-header">
        <div>
          <h1 class="pl-title">Участники</h1>
          <p class="pl-subtitle">Управление записями проекта</p>
        </div>
        <button class="pl-btn pl-btn--primary" @click="openCreate">
          <svg viewBox="0 0 20 20" fill="none" class="pl-btn-icon">
            <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
          </svg>
          Добавить участника
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
          <div class="sk-bar sk-bar--search" />
          <div class="sk-grid">
            <div class="sk-card" v-for="i in 8" :key="i" />
          </div>
        </div>
      </template>

      <!-- ── Контент ────────────────────────────────────────────── -->
      <template v-else>

        <!-- Поиск -->
        <div class="pl-toolbar">
          <div class="pl-search-wrap">
            <svg class="pl-search-icon" viewBox="0 0 20 20" fill="none">
              <circle cx="8.5" cy="8.5" r="5.5" stroke="#4a6fa5" stroke-width="1.3"/>
              <path d="M13 13l3.5 3.5" stroke="#4a6fa5" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <input v-model="searchQuery" class="pl-search" placeholder="Поиск по названию, сайту, профилю…" />
          </div>
          <span class="pl-count">{{ filtered.length }} из {{ partners.length }}</span>
        </div>

        <!-- Пустое состояние -->
        <div v-if="filtered.length === 0" class="pl-empty">
          <svg viewBox="0 0 48 48" fill="none" class="pl-empty-icon">
            <rect x="6" y="10" width="36" height="28" rx="4" stroke="#3b82f6" stroke-width="1.5"/>
            <path d="M16 24h16M16 30h10" stroke="#3b82f6" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="24" cy="20" r="4" stroke="#3b82f6" stroke-width="1.5"/>
          </svg>
          <p class="pl-empty-text">{{ searchQuery ? 'Ничего не найдено' : 'Участников пока нет' }}</p>
          <button v-if="!searchQuery" class="pl-btn pl-btn--primary" @click="openCreate">
            Добавить первого участника
          </button>
        </div>

        <!-- Сетка карточек -->
        <div v-else class="pl-grid">
          <div
            v-for="partner in filtered"
            :key="partner.id"
            class="pl-card"
            :class="{ 'pl-card--deleted': !partner.checked }"
          >
            <!-- Шапка карточки: лого + инфо -->
            <div class="pl-card-top">
              <div class="pl-card-logo-wrap">
                <img
                  v-if="partner.logo"
                  :src="partner.logo"
                  :alt="partner.name"
                  class="pl-card-logo"
                  @error="($event.target as HTMLImageElement).style.display = 'none'"
                />
                <span v-else class="pl-card-initials">{{ initials(partner.name) }}</span>
              </div>

              <div class="pl-card-body">
                <h2 class="pl-card-name">{{ partner.name }}</h2>
                <a :href="normalizeUrl(partner.site)" target="_blank" rel="noopener" class="pl-card-site">
                  <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                    <circle cx="8" cy="8" r="6.5" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M8 1.5C8 1.5 5.5 4 5.5 8s2.5 6.5 2.5 6.5M8 1.5C8 1.5 10.5 4 10.5 8S8 14.5 8 14.5M1.5 8h13" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                  </svg>
                  {{ stripProtocol(partner.site) }}
                </a>
                <span v-if="partner.profile" class="pl-card-profile">
                  <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                    <circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M2 14c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                  </svg>
                  {{ partner.profile.name }}
                </span>
              </div>
            </div>

            <!-- Действия -->
            <div class="pl-card-actions">
              <!-- Подтверждение удаления -->
              <template v-if="deleteConfirmId === partner.id">
                <span class="pl-confirm-text">Удалить?</span>
                <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(partner.id)" title="Подтвердить">
                  <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
                <button class="pl-icon-btn" @click="cancelDelete" title="Отмена">
                  <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
              </template>

              <!-- Удалённая запись — только restore -->
              <template v-else-if="!partner.checked">
                <span class="pl-deleted-badge">удалён</span>
                <button
                  class="pl-icon-btn pl-icon-btn--restore"
                  :disabled="restoring === partner.id"
                  @click="doRestore(partner.id)"
                  title="Восстановить"
                >
                  <svg viewBox="0 0 20 20" fill="none">
                    <path d="M4 10a6 6 0 1 0 1.5-3.9M4 10V6M4 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </template>

              <!-- Активная запись -->
              <template v-else>
                <button class="pl-icon-btn" @click="openEdit(partner)" title="Редактировать">
                  <svg viewBox="0 0 20 20" fill="none">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828L7 15.828 3 17l1.172-4L13.586 3.586z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
                <button class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(partner.id)" title="Удалить">
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

            <!-- Заголовок -->
            <div class="pl-modal-head">
              <h2 class="pl-modal-title">
                {{ modalMode === 'create' ? 'Новый участник' : 'Редактирование' }}
              </h2>
              <button class="pl-icon-btn" @click="closeModal">
                <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              </button>
            </div>

            <!-- Превью -->
            <div class="pl-modal-preview">
              <div class="pl-preview-logo">
                <img v-if="logoPreview" :src="logoPreview" alt="preview" class="pl-preview-img" @error="($event.target as HTMLImageElement).style.display='none'" />
                <span v-else>{{ initials(form.name) }}</span>
              </div>
              <div>
                <p class="pl-preview-name">{{ form.name || 'Название участника' }}</p>
                <p class="pl-preview-meta">{{ stripProtocol(form.site) || 'site.ru' }}</p>
                <p v-if="selectedProfile" class="pl-preview-meta">{{ selectedProfile.name }}</p>
              </div>
            </div>

            <!-- Форма -->
            <div class="pl-modal-form">

              <!-- Название -->
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.name }">
                <label class="pl-label">Название *</label>
                <input v-model="form.name" class="pl-input" placeholder="ООО «Компания»" />
                <span v-if="formErrors.name" class="pl-field-err">{{ formErrors.name }}</span>
              </div>

              <!-- Логотип -->
              <div class="pl-field">
                <label class="pl-label">Логотип</label>
                <label class="pl-upload" :class="{ 'pl-upload--has': logoPreview }">
                  <img v-if="logoPreview" :src="logoPreview" class="pl-upload-preview" alt="preview" />
                  <div v-else class="pl-upload-placeholder">
                    <svg viewBox="0 0 24 24" fill="none">
                      <path d="M12 16V8M12 8l-3 3M12 8l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                      <rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor" stroke-width="1.3"/>
                    </svg>
                    <span>Нажмите или перетащите файл</span>
                    <span class="pl-upload-hint">JPG, PNG, WEBP · до 2 МБ</span>
                  </div>
                  <input type="file" accept="image/jpeg,image/png,image/webp" class="pl-upload-input" @change="onFileChange" />
                </label>
                <button v-if="logoPreview" class="pl-upload-clear" type="button" @click="removeLogo">
                  × Удалить логотип
                </button>
              </div>

              <!-- Сайт -->
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.site }">
                <label class="pl-label">Сайт *</label>
                <input v-model="form.site" class="pl-input" placeholder="https://example.com" />
                <span v-if="formErrors.site" class="pl-field-err">{{ formErrors.site }}</span>
              </div>

              <!-- Профиль -->
              <div class="pl-field">
                <label class="pl-label">Профиль</label>
                <div class="pl-profile-wrap">
                  <div class="pl-profile-input-wrap">
                    <input
                      v-model="profileSearch"
                      class="pl-input pl-profile-input"
                      placeholder="Найти или создать профиль…"
                      @input="onProfileInput"
                      @focus="onProfileFocus"
                      @blur="setTimeout(() => { profileDropOpen = false }, 150)"
                    />
                    <button v-if="selectedProfile" class="pl-profile-clear" type="button" @click="clearProfile">×</button>
                  </div>

                  <Transition name="drop">
                    <div v-if="profileDropOpen" class="pl-profile-drop">
                      <div v-if="profileLoading" class="pl-profile-item pl-profile-item--mute">
                        <span class="pl-spinner pl-spinner--sm" /> Поиск…
                      </div>
                      <template v-else>
                        <button
                          v-for="p in profiles"
                          :key="p.id"
                          class="pl-profile-item"
                          :class="{ 'pl-profile-item--active': selectedProfile?.id === p.id }"
                          type="button"
                          @mousedown.prevent="selectProfile(p)"
                        >
                          <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs"><circle cx="8" cy="6" r="3" stroke="currentColor" stroke-width="1.2"/><path d="M2 14c0-3.314 2.686-5 6-5s6 1.686 6 5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
                          {{ p.name }}
                        </button>
                        <div v-if="!profiles.length && !profileSearch.trim()" class="pl-profile-item pl-profile-item--mute">
                          Начните вводить имя…
                        </div>
                        <div v-if="!profiles.length && profileSearch.trim() && profileExactMatch" class="pl-profile-item pl-profile-item--mute">
                          Профилей не найдено
                        </div>
                        <button
                          v-if="profileSearch.trim() && !profileExactMatch"
                          class="pl-profile-item pl-profile-item--create"
                          type="button"
                          :disabled="creatingProfile"
                          @mousedown.prevent="createAndSelectProfile"
                        >
                          <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs"><path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                          {{ creatingProfile ? 'Создание…' : `Создать «${profileSearch.trim()}»` }}
                        </button>
                      </template>
                    </div>
                  </Transition>
                </div>
                <span class="pl-field-hint">Привяжите существующий профиль или создайте новый</span>
              </div>

              <!-- Глобальная ошибка -->
              <div v-if="formErrors.global" class="pl-alert pl-alert--sm">{{ formErrors.global }}</div>

            </div>

            <!-- Футер -->
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

/* ── Обёртка ───────────────────────────────────────────────────── */
.pl-wrap {
  font-family: 'DM Sans', sans-serif;
  display: flex; flex-direction: column; gap: 24px;
  color: var(--text-b);
}

/* ── Шапка ─────────────────────────────────────────────────────── */
.pl-header {
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; flex-wrap: wrap;
}
.pl-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px; font-weight: 700; color: var(--text-h);
  margin: 0 0 4px; line-height: 1.1;
}
.pl-subtitle { font-size: 13px; color: var(--text-mute); margin: 0; }

/* ── Кнопки ────────────────────────────────────────────────────── */
.pl-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 9px 18px; border-radius: 8px;
  font-family: 'DM Sans', sans-serif; font-size: 13px; font-weight: 500;
  cursor: pointer; border: none; outline: none;
  transition: all 0.18s; white-space: nowrap;
}
.pl-btn--primary { background: var(--accent); color: #fff; box-shadow: 0 0 20px rgba(59,130,246,0.25); }
.pl-btn--primary:hover { background: #2563eb; box-shadow: 0 0 28px rgba(59,130,246,0.35); }
.pl-btn--primary:disabled { opacity: 0.55; cursor: not-allowed; }
.pl-btn--ghost { background: rgba(96,165,250,0.07); color: var(--text-b); border: 1px solid var(--border); }
.pl-btn--ghost:hover { background: rgba(96,165,250,0.13); border-color: var(--border-hi); }
.pl-btn-icon { width: 16px; height: 16px; }

.pl-icon-btn {
  width: 32px; height: 32px; padding: 0; border: none; outline: none;
  border-radius: 7px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  background: rgba(96,165,250,0.07); color: var(--text-b);
  transition: all 0.15s;
}
.pl-icon-btn svg { width: 15px; height: 15px; }
.pl-icon-btn:hover { background: rgba(96,165,250,0.15); color: var(--text-h); }
.pl-icon-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pl-icon-btn--danger  { background: var(--danger-bg); color: var(--danger); }
.pl-icon-btn--danger:hover  { background: rgba(239,68,68,0.2); }
.pl-icon-btn--restore { background: rgba(34,197,94,0.1); color: #86efac; }
.pl-icon-btn--restore:hover { background: rgba(34,197,94,0.2); }

.pl-icon-xs { width: 12px; height: 12px; flex-shrink: 0; }

/* ── Алерт ─────────────────────────────────────────────────────── */
.pl-alert {
  display: flex; align-items: center; gap: 10px;
  padding: 12px 16px;
  background: var(--danger-bg); border: 1px solid rgba(239,68,68,0.2);
  border-radius: 10px; font-size: 13px; color: #fca5a5;
}
.pl-alert--sm { padding: 10px 14px; }
.pl-alert svg { width: 18px; height: 18px; flex-shrink: 0; }
.pl-retry {
  margin-left: auto; background: none;
  border: 1px solid rgba(239,68,68,0.3); border-radius: 6px;
  padding: 4px 10px; color: #fca5a5; font-size: 12px; cursor: pointer;
}
.pl-retry:hover { background: rgba(239,68,68,0.08); }

/* ── Скелетон ──────────────────────────────────────────────────── */
.pl-skeleton { display: flex; flex-direction: column; gap: 16px; }
.sk-bar { border-radius: 6px; background: rgba(96,165,250,0.07); animation: shimmer 1.4s infinite; }
.sk-bar--search { height: 40px; width: 360px; }
.sk-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
.sk-card { height: 130px; border-radius: 12px; background: rgba(96,165,250,0.05); animation: shimmer 1.4s infinite; }
@keyframes shimmer { 0%,100% { opacity:.6; } 50% { opacity:1; } }

/* ── Тулбар ────────────────────────────────────────────────────── */
.pl-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.pl-search-wrap { position: relative; flex: 1; max-width: 400px; }
.pl-search-icon {
  position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
  width: 16px; height: 16px; pointer-events: none;
}
.pl-search {
  width: 100%; padding: 9px 14px 9px 36px; box-sizing: border-box;
  background: var(--bg-card); border: 1px solid var(--border);
  border-radius: 8px; color: var(--text-h); font-size: 13px;
  font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.2s;
}
.pl-search::placeholder { color: var(--text-mute); }
.pl-search:focus { border-color: var(--border-hi); }
.pl-count { font-size: 12px; color: var(--text-mute); white-space: nowrap; }

/* ── Пустое ────────────────────────────────────────────────────── */
.pl-empty {
  display: flex; flex-direction: column; align-items: center; gap: 16px;
  padding: 60px 24px; background: var(--bg-card);
  border: 1px dashed var(--border); border-radius: 14px; text-align: center;
}
.pl-empty-icon { width: 48px; height: 48px; opacity: 0.4; }
.pl-empty-text { font-size: 15px; color: var(--text-mute); margin: 0; }

/* ── Сетка ─────────────────────────────────────────────────────── */
.pl-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 16px;
}

/* ── Карточка ──────────────────────────────────────────────────── */
.pl-card {
  display: flex; flex-direction: column; justify-content: space-between;
  gap: 14px; padding: 18px 16px;
  background: var(--bg-card);
  border: 1px solid var(--border); border-radius: 12px;
  transition: border-color 0.2s, box-shadow 0.2s, opacity 0.2s;
}
.pl-card:hover { border-color: var(--border-hi); box-shadow: 0 4px 24px rgba(59,130,246,0.08); }
.pl-card--deleted { opacity: 0.38; filter: grayscale(0.4); border-style: dashed; }
.pl-card--deleted:hover { opacity: 0.55; box-shadow: none; }

.pl-card-top { display: flex; align-items: flex-start; gap: 12px; }

.pl-card-logo-wrap {
  width: 44px; height: 44px; flex-shrink: 0; border-radius: 10px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center;
  overflow: hidden;
  box-shadow: 0 0 12px rgba(37,99,235,0.2);
}
.pl-card-logo { width: 100%; height: 100%; object-fit: contain; background: #fff; }
.pl-card-initials {
  font-family: 'Cormorant Garamond', serif;
  font-size: 17px; font-weight: 600; color: #bfdbfe;
}

.pl-card-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
.pl-card-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px; font-weight: 600; color: var(--text-h);
  margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pl-card-site {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 12px; color: var(--accent); text-decoration: none;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  transition: color 0.15s;
}
.pl-card-site:hover { color: var(--accent-hi); }
.pl-card-profile {
  display: inline-flex; align-items: center; gap: 4px;
  font-size: 11px; color: var(--text-mute);
  background: rgba(96,165,250,0.07); border: 1px solid var(--border);
  padding: 2px 8px; border-radius: 20px; width: fit-content;
}

.pl-card-actions {
  display: flex; align-items: center; justify-content: flex-end; gap: 6px;
  border-top: 1px solid var(--border); padding-top: 10px;
}
.pl-confirm-text { font-size: 12px; color: var(--danger); white-space: nowrap; margin-right: auto; }
.pl-deleted-badge {
  font-size: 11px; color: var(--text-mute); letter-spacing: 0.06em;
  text-transform: uppercase; margin-right: auto;
}

/* ── Модалка ───────────────────────────────────────────────────── */
.pl-overlay {
  position: fixed; inset: 0; z-index: 200;
  background: rgba(5,10,24,0.8); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center; padding: 16px;
}
.pl-modal {
  width: 100%; max-width: 480px;
  background: #0a1228; border: 1px solid var(--border-hi);
  border-radius: 16px; overflow: hidden;
  box-shadow: 0 24px 80px rgba(0,0,0,0.6);
  max-height: 90vh; overflow-y: auto;
}
.pl-modal-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px; border-bottom: 1px solid var(--border);
  position: sticky; top: 0; background: #0a1228; z-index: 1;
}
.pl-modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px; font-weight: 600; color: var(--text-h); margin: 0;
}

/* Превью в модалке */
.pl-modal-preview {
  display: flex; align-items: center; gap: 14px;
  padding: 14px 24px;
  background: rgba(59,130,246,0.04); border-bottom: 1px solid var(--border);
}
.pl-preview-logo {
  width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center; overflow: hidden;
}
.pl-preview-img { width: 100%; height: 100%; object-fit: contain; background: #fff; }
.pl-preview-logo span {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px; font-weight: 600; color: #bfdbfe;
}
.pl-preview-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px; font-weight: 600; color: var(--text-h); margin: 0 0 2px;
}
.pl-preview-meta { font-size: 12px; color: var(--text-mute); margin: 0; }

/* Форма */
.pl-modal-form { padding: 20px 24px; display: flex; flex-direction: column; gap: 16px; }
.pl-field { display: flex; flex-direction: column; gap: 6px; }
.pl-field--err .pl-input { border-color: rgba(239,68,68,0.5); }
.pl-label { font-size: 11px; color: var(--text-mute); letter-spacing: 0.07em; text-transform: uppercase; }
.pl-input {
  padding: 10px 14px;
  background: rgba(255,255,255,0.04); border: 1px solid var(--border);
  border-radius: 8px; color: var(--text-h); font-size: 14px;
  font-family: 'DM Sans', sans-serif; outline: none; transition: border-color 0.2s;
  width: 100%; box-sizing: border-box;
}
.pl-input::placeholder { color: var(--text-mute); opacity: 0.5; }
.pl-input:focus { border-color: var(--border-hi); background: rgba(255,255,255,0.06); }
.pl-field-err  { font-size: 12px; color: #fca5a5; }
.pl-field-hint { font-size: 11px; color: var(--text-mute); }

.pl-modal-foot {
  display: flex; align-items: center; justify-content: flex-end; gap: 10px;
  padding: 16px 24px; border-top: 1px solid var(--border);
  position: sticky; bottom: 0; background: #0a1228;
}

/* ── Загрузка файла ────────────────────────────────────────────── */
.pl-upload {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 6px; padding: 20px;
  border: 1.5px dashed var(--border-hi); border-radius: 10px; cursor: pointer;
  background: rgba(59,130,246,0.03);
  transition: all 0.2s; position: relative; overflow: hidden; min-height: 90px;
}
.pl-upload:hover { border-color: var(--accent); background: rgba(59,130,246,0.07); }
.pl-upload--has { padding: 0; border-style: solid; min-height: 110px; }
.pl-upload-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
.pl-upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.pl-upload-placeholder svg { width: 28px; height: 28px; color: var(--accent); opacity: 0.7; }
.pl-upload-placeholder span { font-size: 13px; color: var(--text-b); }
.pl-upload-hint { font-size: 11px; color: var(--text-mute) !important; }
.pl-upload-preview { width: 100%; height: 110px; object-fit: contain; padding: 8px; box-sizing: border-box; }
.pl-upload-clear {
  background: none; border: none; font-size: 12px; color: var(--danger); cursor: pointer; padding: 0;
}
.pl-upload-clear:hover { text-decoration: underline; }

/* ── Профиль в форме ───────────────────────────────────────────── */
.pl-profile-wrap { position: relative; }
.pl-profile-input-wrap { position: relative; }
.pl-profile-input { padding-right: 30px !important; }
.pl-profile-clear {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: var(--text-mute);
  font-size: 18px; line-height: 1; cursor: pointer; padding: 0;
}
.pl-profile-clear:hover { color: var(--danger); }

.pl-profile-drop {
  position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 50;
  background: #0d1a3a; border: 1px solid var(--border-hi); border-radius: 8px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.5); max-height: 200px; overflow-y: auto;
}
.pl-profile-item {
  display: flex; align-items: center; gap: 8px;
  width: 100%; padding: 10px 14px;
  background: none; border: none; border-bottom: 1px solid rgba(96,165,250,0.06);
  color: var(--text-b); font-size: 13px; font-family: 'DM Sans', sans-serif;
  cursor: pointer; text-align: left; transition: background 0.15s;
}
.pl-profile-item:last-child { border-bottom: none; }
.pl-profile-item:hover { background: rgba(96,165,250,0.08); color: var(--text-h); }
.pl-profile-item--active { background: rgba(59,130,246,0.12); color: var(--accent-hi); }
.pl-profile-item--mute { color: var(--text-mute); cursor: default; font-style: italic; }
.pl-profile-item--mute:hover { background: none; }
.pl-profile-item--create { color: #86efac; }
.pl-profile-item--create:hover { background: rgba(34,197,94,0.08); }
.pl-profile-item--create:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Спиннер ───────────────────────────────────────────────────── */
.pl-spinner {
  display: inline-block; width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
  border-radius: 50%; animation: spin 0.6s linear infinite;
}
.pl-spinner--sm { width: 11px; height: 11px; border-width: 1.5px; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Переходы ──────────────────────────────────────────────────── */
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from,   .modal-leave-to     { opacity: 0; }
.modal-enter-active .pl-modal,
.modal-leave-active .pl-modal           { transition: transform 0.2s; }
.modal-enter-from   .pl-modal           { transform: translateY(14px); }
.modal-leave-to     .pl-modal           { transform: translateY(8px); }

.drop-enter-active, .drop-leave-active { transition: opacity 0.15s, transform 0.15s; }
.drop-enter-from,   .drop-leave-to     { opacity: 0; transform: translateY(-4px); }

/* ── Адаптив ───────────────────────────────────────────────────── */
@media (max-width: 1024px) { .pl-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 768px)  { .pl-grid { grid-template-columns: repeat(2,1fr); } .pl-header { flex-direction: column; align-items: flex-start; } }
@media (max-width: 480px)  { .pl-grid { grid-template-columns: 1fr; } .sk-grid { grid-template-columns: 1fr; } }
</style>

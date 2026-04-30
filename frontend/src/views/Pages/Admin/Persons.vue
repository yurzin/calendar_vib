<script setup lang="ts">
import {ref, computed, onMounted} from 'vue';
import {useAuth} from '@/composable/useAuth';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';

const {user, checkAuth} = useAuth();

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
  checked: boolean;
  profile_id: number | null;
  profile: Profile | null;
}

interface Person {
  id: number;
  last_name: string;
  first_name: string;
  middle_name: string | null;
  photo_path: string | null;
  photo_thumb_path: string | null;
  birth_day: string | null;   // ISO date
  birth_month: string | null;   // ISO date
  position_short: string;
  position_full: string;
  phone: string | null;
  email: string | null;
  web: string | null;
  checked: boolean;            // false = soft deleted
  partner_id: number | null;
  partner: Partner | null;
}

// ─── Глобальное состояние ──────────────────────────────────────────────────
const loading = ref(true);
const error = ref('');
const persons = ref<Person[]>([]);
const searchQuery = ref('');
// ─── Фото ──────────────────────────────────────────────────────────────────
const photoFile = ref<File | null>(null);
const photoPreview = ref<string | null>(null);

const onFileChange = (e: Event) => {
  const file = (e.target as HTMLInputElement).files?.[0];
  if (!file) return;
  photoFile.value = file;
  photoPreview.value = URL.createObjectURL(file);
};

const removePhoto = () => {
  photoFile.value = null;
  photoPreview.value = null;
  form.value.photo_path = null;
};


const filtered = computed(() => {
  const q = searchQuery.value.trim().toLowerCase();
  if (!q) return persons.value;
  return persons.value.filter(p =>
    (p.last_name || '').toLowerCase().includes(q) ||
    (p.email || '').toLowerCase().includes(q) ||
    (p.web || '').toLowerCase().includes(q) ||
    (p.phone || '').toLowerCase().includes(q) ||
    (p.partner?.name || '').toLowerCase().includes(q)
  );
});

// Группировка по первой букве фамилии
const grouped = computed(() => {
  const map = new Map<string, Person[]>();
  for (const p of filtered.value) {
    const letter = (p.last_name?.[0] ?? '').toUpperCase() || '#';
    if (!map.has(letter)) map.set(letter, []);
    map.get(letter)!.push(p);
  }
  // Отсортировать буквы: кириллица → латиница → прочее
  return [...map.entries()].sort(([a], [b]) =>
    a.localeCompare(b, 'ru', {sensitivity: 'base'})
  );
});

// ─── Загрузка ──────────────────────────────────────────────────────────────
const loadData = async () => {
  loading.value = true;
  error.value = '';
  try {
    if (!user.value) await checkAuth();
    const {data} = await axios.get('/api/persons');
    persons.value = Array.isArray(data?.persons) ? data.persons : [];
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка загрузки';
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);

// ─── Хелперы ───────────────────────────────────────────────────────────────
const initials = (p?: Pick<Person, 'last_name' | 'first_name'> | null) =>
  p ? ((p.first_name?.[0] || '') + (p.last_name?.[0] || '')).toUpperCase() || '?' : '?';

const formatDate = (day, month) => {
  if (isNaN(day) || isNaN(month) || day < 1 || day > 31 || month < 1 || month > 12) {
    return '';
  }
  const formattedDay = day < 10 ? '0' + day : day;
  const formattedMonth = month < 10 ? '0' + month : month;
  return `${formattedDay}.${formattedMonth}`;
};

const normalizeUrl = (url?: string | null) =>
  url && !url.startsWith('http') ? 'https://' + url : (url || '');

const stripProtocol = (url?: string | null) =>
  (url || '').replace(/^https?:\/\//, '');

// ─── Форма ─────────────────────────────────────────────────────────────────
type FormState = {
  id: number | null;
  last_name: string;
  first_name: string;
  middle_name: string;
  photo_path: string | null;
  birth_day: string;
  birth_month: string;
  position_short: string;
  position_full: string;
  phone: string;
  email: string;
  web: string;
  partner_id: number | null;
};

const emptyForm = (): FormState => ({
  id: null, last_name: '', first_name: '', middle_name: '',
  photo_path: null, position_short: '', position_full: '',
  birth_day: '', birth_month: '', phone: '', email: '', web: '', partner_id: null,
});

const modalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const form = ref<FormState>(emptyForm());
const formErrors = ref<Record<string, string>>({});
const saving = ref(false);

// ─── Партнёры (autocomplete) ────────────────────────────────────────────────
const partners = ref<Partner[]>([]);
const partnerSearch = ref('');
const partnerLoading = ref(false);
const partnerDropOpen = ref(false);
const selectedPartner = ref<Partner | null>(null);
const creatingPartner = ref(false);

// ─── Экспорт календаря ────────────────────────────────────────────────────
const exportOpen      = ref(false);
const exportMonths    = ref<{month:number, name:string, short_name:string, count:number}[]>([]);
const exportLoading   = ref(false);
const exportDownloading = ref<number | null>(null);

const partnerExactMatch = computed(() =>
  partners.value.some(
    o => o.name.toLowerCase() === partnerSearch.value.trim().toLowerCase()
  )
);

let partnerTimer: ReturnType<typeof setTimeout>;

const fetchPartners = async (q = '') => {
  partnerLoading.value = true;
  try {
    const {data} = await axios.get('/api/partners', {params: {q}});
    partners.value = (Array.isArray(data?.partners) ? data.partners : []).filter((p: Partner) => p.checked);
  } catch {
    partners.value = [];
  } finally {
    partnerLoading.value = false;
  }
};

const onPartnerInput = () => {
  partnerDropOpen.value = true;
  clearTimeout(partnerTimer);
  partnerTimer = setTimeout(() => fetchPartners(partnerSearch.value), 300);
};

// ─── Группировка ──────────────────────────────────────────────────────────
const activeLetter = ref<string | null>(null);
const groupMode = ref<'alpha' | 'month'>('alpha');

const allLetters = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ'.split('');

const availableLetters = computed(() =>
  new Set(grouped.value.map(([letter]) => letter))
);

const visibleGroups = computed(() =>
  activeLetter.value
    ? grouped.value.filter(([letter]) => letter === activeLetter.value)
    : grouped.value
);

const MONTH_NAMES = [
  'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
  'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь',
];

const MONTH_NAMES_SHORT = [
  'ЯНВ', 'ФЕВ', 'МАР', 'АПР', 'МАЙ', 'ИЮН',
  'ИЮЛ', 'АВГ', 'СЕН', 'ОКТ', 'НОЯ', 'ДЕК',
];

// Строгие SVG-иконки для каждого месяца (путь внутри viewBox 0 0 16 16)
const MONTH_SVG_PATHS: Record<number, string> = {
  1:  `<path d="M8 2v12M5 5l3-3 3 3M5 11l3 3 3-3M2 8h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>`, // снежинка-стрелки
  2:  `<path d="M8 3a5 5 0 1 0 0 10A5 5 0 0 0 8 3zM8 6v2l1.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // часы
  3:  `<path d="M8 13V7M5 10l3-3 3 3M4 13h8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>`, // росток
  4:  `<circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 2v1.5M8 12.5V14M2 8h1.5M12.5 8H14M3.9 3.9l1.1 1.1M11 11l1.1 1.1M3.9 12.1l1.1-1.1M11 5l1.1-1.1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // солнце
  5:  `<path d="M5 9c0-3 6-3 6 0M4 12c0-5 8-5 8 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M8 4v3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // дерево
  6:  `<circle cx="8" cy="8" r="3.5" stroke="currentColor" stroke-width="1.2"/><path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.1 3.1l1.4 1.4M11.5 11.5l1.4 1.4M3.1 12.9l1.4-1.4M11.5 4.5l1.4-1.4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // большое солнце
  7:  `<path d="M5 3h6l-1 5h-4L5 3zM6 8l-2 5h8l-2-5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>`, // песочные часы/лето
  8:  `<path d="M4 13l2-4 2 2 2-5 2 7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 13h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // диаграмма/урожай
  9:  `<path d="M5 4c0 5 6 5 6 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M8 9v5M6 14h4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // листок
  10: `<rect x="3" y="3" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.2"/><path d="M6 3v10M10 3v10M3 7h10M3 10h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>`, // сетка/календарь
  11: `<path d="M4 14V6l4-4 4 4v8M6 14v-4h4v4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>`, // дом
  12: `<path d="M8 2L8 14M5 5L8 2L11 5M5 11L8 14L11 11M2 8L14 8M2 8L5 5M2 8L5 11M14 8L11 5M14 8L11 11" stroke="currentColor" stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round"/>`, // снежинка
};

const activeMonth = ref<number | null>(null);

const groupedByMonth = computed(() => {
  const map = new Map<number, Person[]>();
  for (const p of filtered.value) {
    const m = Number(p.birth_month);
    if (!m || m < 1 || m > 12) continue;
    if (!map.has(m)) map.set(m, []);
    map.get(m)!.push(p);
  }
  return [...map.entries()]
    .sort(([a], [b]) => a - b)
    .map(([month, list]) => ({
      month,
      name: MONTH_NAMES[month - 1],
      short: MONTH_NAMES_SHORT[month - 1],
      svgPath: MONTH_SVG_PATHS[month],
      persons: list.slice().sort((a, b) => Number(a.birth_day) - Number(b.birth_day)),
    }));
});

const availableMonths = computed(() =>
  new Set(groupedByMonth.value.map(g => g.month))
);

const visibleMonthGroups = computed(() =>
  activeMonth.value
    ? groupedByMonth.value.filter(g => g.month === activeMonth.value)
    : groupedByMonth.value
);

const personsWithoutMonth = computed(() =>
  filtered.value.filter(p => !p.birth_month || Number(p.birth_month) < 1 || Number(p.birth_month) > 12)
);

const switchGroupMode = (mode: 'alpha' | 'month') => {
  groupMode.value = mode;
  activeLetter.value = null;
  activeMonth.value = null;
};

// ─── Партнёр методы ───────────────────────────────────────────────────────
const onPartnerFocus = () => {
  partnerDropOpen.value = true;
  fetchPartners(partnerSearch.value);
};

const selectPartner = (p: Partner) => {
  selectedPartner.value = p;
  form.value.partner_id = p.id;
  partnerSearch.value = p.name;
  partnerDropOpen.value = false;
};

const clearPartner = () => {
  selectedPartner.value = null;
  form.value.partner_id = null;
  partnerSearch.value = '';
};

const createAndSelectPartner = async () => {
  const name = partnerSearch.value.trim();
  if (!name || creatingPartner.value) return;
  creatingPartner.value = true;
  try {
    const fd = new FormData();
    fd.append('name', name);
    fd.append('url', 'https://');
    const {data} = await axios.post('/api/partners', fd, {
      headers: {'Content-Type': 'multipart/form-data'},
    });
    partners.value.unshift(data);
    selectPartner(data);
  } catch { /* ignore */
  } finally {
    creatingPartner.value = false;
  }
};

// ─── Модалка ───────────────────────────────────────────────────────────────
const resetModal = () => {
  formErrors.value = {};
  clearPartner();
  photoFile.value = null;
  photoPreview.value = null;
};

const openCreate = () => {
  form.value = emptyForm();
  modalMode.value = 'create';
  resetModal();
  fetchPartners();
  modalOpen.value = true;
};

const openEdit = (p: Person) => {
  form.value = {
    id: p.id,
    last_name: p.last_name,
    first_name: p.first_name,
    middle_name: p.middle_name ?? '',
    photo_path: p.photo_path ?? null,
    birth_day: p.birth_day ?? '',
    birth_month: p.birth_month ?? '',
    position_short: p.position_short,
    position_full: p.position_full,
    phone: p.phone ?? '',
    email: p.email ?? '',
    web: p.web ?? '',
    partner_id: p.partner_id,
  };
  photoFile.value = null;
  photoPreview.value = p.photo_path ?? null;
  selectedPartner.value = p.partner ?? null;
  partnerSearch.value = p.partner?.name ?? '';
  modalMode.value = 'edit';
  formErrors.value = {};
  fetchPartners();
  modalOpen.value = true;
};

const closeModal = () => {
  modalOpen.value = false;
  resetModal();
};

// ─── Валидация ─────────────────────────────────────────────────────────────
const validate = (): boolean => {
  const e: Record<string, string> = {};
  if (!form.value.last_name.trim()) e.last_name = 'Укажите фамилию';
  if (!form.value.first_name.trim()) e.first_name = 'Укажите имя';
  if (form.value.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email))
    e.email = 'Некорректный e-mail';
  formErrors.value = e;
  return !Object.keys(e).length;
};

// ─── Сохранение ────────────────────────────────────────────────────────────
const save = async () => {
  if (!validate()) return;
  saving.value = true;
  try {
    const fd = new FormData();
    fd.append('last_name', form.value.last_name.trim());
    fd.append('first_name', form.value.first_name.trim());
    fd.append('middle_name', form.value.middle_name.trim() || '');
    fd.append('birth_day', String(form.value.birth_day || ''));
    fd.append('birth_month', String(form.value.birth_month || ''));
    fd.append('position_short', form.value.position_short.trim());
    fd.append('position_full', form.value.position_full.trim());
    fd.append('phone', form.value.phone.trim() || '');
    fd.append('email', form.value.email.trim() || '');
    fd.append('web', form.value.web.trim() || '');
    if (form.value.partner_id != null)
      fd.append('partner_id', String(form.value.partner_id));
    if (photoFile.value)
      fd.append('photo', photoFile.value);
    if (!photoPreview.value && !photoFile.value && modalMode.value === 'edit')
      fd.append('remove_photo', '1');

    const headers = {'Content-Type': 'multipart/form-data'};

    if (modalMode.value === 'create') {
      const {data} = await axios.post('/api/persons', fd, {headers});
      persons.value.unshift(data);
    } else {
      fd.append('_method', 'PUT');
      const {data} = await axios.post(`/api/persons/${form.value.id}`, fd, {headers});
      const idx = persons.value.findIndex(p => p.id === form.value.id);
      if (idx !== -1) persons.value[idx] = data;
    }
    closeModal();
  } catch (e: any) {
    const errs = e.response?.data?.errors;
    if (errs) {
      formErrors.value = Object.fromEntries(
        Object.entries(errs).map(([k, v]) => [k, (v as string[])[0]])
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
const deleting = ref(false);

const confirmDelete = (id: number) => { deleteConfirmId.value = id; };
const cancelDelete = () => { deleteConfirmId.value = null; };

const doDelete = async (id: number) => {
  deleting.value = true;
  try {
    await axios.delete(`/api/persons/${id}`);
    const p = persons.value.find(p => p.id === id);
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
    await axios.post(`/api/persons/${id}/restore`);
    const p = persons.value.find(p => p.id === id);
    if (p) p.checked = true;
  } catch (e: any) {
    error.value = e.response?.data?.message || 'Ошибка восстановления';
  } finally {
    restoring.value = null;
  }
};

const loadExportStats = async () => {
  if (exportMonths.value.length) return;
  exportLoading.value = true;
  try {
    const { data } = await axios.get('/api/export/calendar/stats');
    exportMonths.value = data.months;
  } finally {
    exportLoading.value = false;
  }
};

const openExport = () => {
  exportOpen.value = true;
  loadExportStats();
};

const downloadMonth = (month: number | null) => {
  exportDownloading.value = month ?? -1;
  const url = month
    ? `/api/export/calendar/download?month=${month}`
    : '/api/export/calendar/download';

  const a = document.createElement('a');
  a.href = url;
  a.download = '';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);

  setTimeout(() => { exportDownloading.value = null; }, 1500);
};
</script>

<template>
  <AuthenticatedLayout>
    <div class="pl-wrap">

      <!-- ── Шапка ───────────────────────────────────────────────── -->
      <div class="pl-header">
        <div>
          <h1 class="pl-title">Персоны</h1>
          <p class="pl-subtitle">Управление контактами и физическими лицами</p>
        </div>
        <button class="pl-btn pl-btn--primary" @click="openCreate">
          <svg viewBox="0 0 20 20" fill="none" class="pl-btn-icon">
            <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8"
                  stroke-linecap="round"/>
          </svg>
          Добавить персону
        </button>
        <div class="pl-export-wrap">
          <button class="pl-btn pl-btn--export" @click="openExport">
            <svg viewBox="0 0 20 20" fill="none" class="pl-btn-icon">
              <path d="M10 3v10M10 13l-3-3M10 13l3-3" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M3 17h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Экспорт JSON
          </button>

          <Transition name="drop">
            <div v-if="exportOpen" class="pl-export-drop" v-click-outside="() => exportOpen = false">
              <div class="pl-export-head">
                <span>Выгрузка для InDesign</span>
                <button class="pl-icon-btn" @click="exportOpen = false">
                  <svg viewBox="0 0 20 20" fill="none">
                    <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round"/>
                  </svg>
                </button>
              </div>

              <button class="pl-export-item pl-export-item--all"
                      :disabled="exportDownloading === -1"
                      @click="downloadMonth(null)">
                <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                  <rect x="2" y="2" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.2"/>
                  <path d="M8 5v6M5 8l3 3 3-3" stroke="currentColor" stroke-width="1.2"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Все месяцы</span>
                <span v-if="exportDownloading === -1" class="pl-spinner pl-spinner--sm"/>
              </button>

              <div class="pl-export-divider"/>

              <template v-if="exportLoading">
                <div class="pl-export-item pl-export-item--skeleton" v-for="i in 12" :key="i"/>
              </template>

              <template v-else>
                <button
                  v-for="m in exportMonths"
                  :key="m.month"
                  class="pl-export-item"
                  :class="{ 'pl-export-item--empty': m.count === 0 }"
                  :disabled="exportDownloading === m.month || m.count === 0"
                  @click="downloadMonth(m.month)"
                >
                  <span class="pl-export-month-num">{{ String(m.month).padStart(2, '0') }}</span>
                  <span class="pl-export-month-name">{{ m.name }}</span>
                  <span class="pl-export-badge" :class="{ 'pl-export-badge--zero': m.count === 0 }">
                    {{ m.count }}
                  </span>
                  <span v-if="exportDownloading === m.month" class="pl-spinner pl-spinner--sm"/>
                </button>
              </template>
            </div>
          </Transition>
        </div>
      </div>

      <!-- ── Ошибка ──────────────────────────────────────────────── -->
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
          <div class="sk-bar sk-bar--search"/>
          <div class="sk-grid">
            <div class="sk-card" v-for="i in 8" :key="i"/>
          </div>
        </div>
      </template>

      <!-- ── Контент ────────────────────────────────────────────── -->
      <template v-else>

        <!-- Поиск + переключатель режима -->
        <div class="pl-toolbar">
          <div class="pl-search-wrap">
            <svg class="pl-search-icon" viewBox="0 0 20 20" fill="none">
              <circle cx="8.5" cy="8.5" r="5.5" stroke="#4a6fa5" stroke-width="1.3"/>
              <path d="M13 13l3.5 3.5" stroke="#4a6fa5" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <input v-model="searchQuery" class="pl-search"
                   placeholder="Поиск по имени, email, телефону, организации…"/>
          </div>

          <!-- Переключатель группировки -->
          <div class="pl-group-toggle">
            <button
              class="pl-group-toggle-btn"
              :class="{ 'pl-group-toggle-btn--active': groupMode === 'alpha' }"
              @click="switchGroupMode('alpha')"
            >
              <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                <path d="M3 12L6 4l3 8M4.5 9.5h3M10 4v8M13 4h-3M13 12h-3" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              По алфавиту
            </button>
            <button
              class="pl-group-toggle-btn"
              :class="{ 'pl-group-toggle-btn--active': groupMode === 'month' }"
              @click="switchGroupMode('month')"
            >
              <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                <path d="M5 1v4M11 1v4M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                <circle cx="8" cy="10.5" r="1" fill="currentColor"/>
              </svg>
              По месяцам
            </button>
          </div>

          <span class="pl-count">{{ filtered.length }} из {{ persons.length }}</span>
        </div>

        <!-- Пустое состояние -->
        <div v-if="filtered.length === 0" class="pl-empty">
          <svg viewBox="0 0 48 48" fill="none" class="pl-empty-icon">
            <circle cx="24" cy="18" r="8" stroke="#3b82f6" stroke-width="1.5"/>
            <path d="M8 40c0-8.837 7.163-14 16-14s16 5.163 16 14" stroke="#3b82f6"
                  stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <p class="pl-empty-text">{{ searchQuery ? 'Ничего не найдено' : 'Персон пока нет' }}</p>
          <button v-if="!searchQuery" class="pl-btn pl-btn--primary" @click="openCreate">
            Добавить первую персону
          </button>
        </div>

        <!-- ── Режим: по алфавиту ───────────────────────────────── -->
        <template v-if="groupMode === 'alpha' && filtered.length > 0">
          <!-- Алфавитная навигация -->
          <div class="pl-alpha-nav">
            <button
              class="pl-alpha-nav-btn"
              :class="{ 'pl-alpha-nav-btn--active': activeLetter === null }"
              @click="activeLetter = null"
            >Все</button>
            <button
              v-for="letter in allLetters"
              :key="letter"
              class="pl-alpha-nav-btn"
              :class="{
                'pl-alpha-nav-btn--active': activeLetter === letter,
                'pl-alpha-nav-btn--disabled': !availableLetters.has(letter)
              }"
              :disabled="!availableLetters.has(letter)"
              @click="activeLetter = letter"
            >{{ letter }}</button>
          </div>

          <!-- Группы по буквам -->
          <div class="pl-groups">
            <template v-for="[letter, group] in visibleGroups" :key="letter">
              <div class="pl-alpha-header">
                <span class="pl-alpha-letter">{{ letter }}</span>
                <span class="pl-alpha-count">{{ group.length }}</span>
              </div>
              <div class="pl-grid">
                <div
                  v-for="person in group"
                  :key="person.id"
                  class="pl-card"
                  :class="{ 'pl-card--deleted': !person.checked }"
                >
                  <div class="pl-card-top">
                    <div class="pl-card-avatar">
                      <img
                        v-if="person.photo_thumb_path"
                        :src="person.photo_thumb_path"
                        :alt="initials(person)"
                        class="pl-card-avatar-img"
                        @error="($event.target as HTMLImageElement).style.display='none'"
                      />
                      <span v-else>{{ initials(person) }}</span>
                    </div>
                    <div class="pl-card-body">
                      <h2 class="pl-card-name">{{ person.last_name }} {{ person.first_name }}</h2>
                      <span v-if="person.partner" class="pl-card-partner">
                        <img
                          v-if="person.partner.logo"
                          :src="person.partner.logo"
                          :alt="person.partner.name"
                          class="pl-card-partner-logo"
                          @error="($event.target as HTMLImageElement).style.display = 'none'"
                        />
                        <svg v-else viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                          <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        {{ person.partner.name }}
                      </span>
                    </div>
                  </div>

                  <div class="pl-card-bottom">
                    <div class="pl-card-details">
                      <div v-if="person.birth_day" class="pl-card-detail">
                        <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                          <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M5 1v4M11 1v4M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        <span>{{ formatDate(person.birth_day, person.birth_month) }}</span>
                      </div>
                    </div>
                    <div class="pl-card-actions">
                      <template v-if="deleteConfirmId === person.id">
                        <span class="pl-confirm-text">Удалить?</span>
                        <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                        <button class="pl-icon-btn" @click="cancelDelete">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                      </template>
                      <template v-else-if="!person.checked">
                        <span class="pl-deleted-badge">удалён</span>
                        <button class="pl-icon-btn pl-icon-btn--restore" :disabled="restoring === person.id" @click="doRestore(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 10a6 6 0 1 0 1.5-3.9M4 10V6M4 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                      </template>
                      <template v-else>
                        <button class="pl-icon-btn" @click="openEdit(person)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M13.586 3.586a2 2 0 112.828 2.828L7 15.828 3 17l1.172-4L13.586 3.586z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 6h12M8 6V4h4v2M9 10v5M11 10v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><rect x="5" y="6" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                        </button>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </template>

        <!-- ── Режим: по месяцам ───────────────────────────────── -->
        <template v-if="groupMode === 'month' && filtered.length > 0">

          <!-- Навигация по месяцам -->
          <div class="pl-month-nav">
            <button
              class="pl-month-nav-btn"
              :class="{ 'pl-month-nav-btn--active': activeMonth === null }"
              @click="activeMonth = null"
            >
              <svg viewBox="0 0 16 16" fill="none" class="pl-month-nav-icon">
                <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                <path d="M5 1v4M11 1v4M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
              </svg>
              <span>Все</span>
            </button>
            <button
              v-for="n in 12"
              :key="n"
              class="pl-month-nav-btn"
              :class="{
                'pl-month-nav-btn--active': activeMonth === n,
                'pl-month-nav-btn--empty': !availableMonths.has(n),
              }"
              :disabled="!availableMonths.has(n)"
              @click="activeMonth = n"
            >
              <svg viewBox="0 0 16 16" fill="none" class="pl-month-nav-icon" v-html="MONTH_SVG_PATHS[n]"/>
              <span>{{ MONTH_NAMES_SHORT[n - 1] }}</span>
            </button>
          </div>

          <div class="pl-groups">
            <!-- Месяцы с днями рождения -->
            <template v-for="{ month, name, svgPath, persons: monthPersons } in visibleMonthGroups" :key="month">
              <div class="pl-month-header">
                <div class="pl-month-header-left">
                  <div class="pl-month-header-icon">
                    <svg viewBox="0 0 16 16" fill="none" v-html="svgPath"/>
                  </div>
                  <span class="pl-month-name">{{ name }}</span>
                  <span class="pl-month-num">{{ String(month).padStart(2, '0') }}</span>
                </div>
                <span class="pl-alpha-count">{{ monthPersons.length }}</span>
              </div>
              <div class="pl-grid">
                <div
                  v-for="person in monthPersons"
                  :key="person.id"
                  class="pl-card"
                  :class="{ 'pl-card--deleted': !person.checked }"
                >
                  <div class="pl-card-top">
                    <div class="pl-card-avatar">
                      <img
                        v-if="person.photo_thumb_path"
                        :src="person.photo_thumb_path"
                        :alt="initials(person)"
                        class="pl-card-avatar-img"
                        @error="($event.target as HTMLImageElement).style.display='none'"
                      />
                      <span v-else>{{ initials(person) }}</span>
                    </div>
                    <div class="pl-card-body">
                      <h2 class="pl-card-name">{{ person.last_name }} {{ person.first_name }}</h2>
                      <span v-if="person.partner" class="pl-card-partner">
                        <img
                          v-if="person.partner.logo"
                          :src="person.partner.logo"
                          :alt="person.partner.name"
                          class="pl-card-partner-logo"
                          @error="($event.target as HTMLImageElement).style.display = 'none'"
                        />
                        <svg v-else viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                          <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        {{ person.partner.name }}
                      </span>
                    </div>
                  </div>

                  <div class="pl-card-bottom">
                    <div class="pl-card-details">
                      <div v-if="person.birth_day" class="pl-card-detail pl-card-detail--day">
                        <span class="pl-card-day-badge">{{  `${ String(person.birth_day).padStart(2,0)}.${ String(person.birth_month).padStart(2,0)}`  }}</span>
                      </div>
                    </div>
                    <div class="pl-card-actions">
                      <template v-if="deleteConfirmId === person.id">
                        <span class="pl-confirm-text">Удалить?</span>
                        <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                        <button class="pl-icon-btn" @click="cancelDelete">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                      </template>
                      <template v-else-if="!person.checked">
                        <span class="pl-deleted-badge">удалён</span>
                        <button class="pl-icon-btn pl-icon-btn--restore" :disabled="restoring === person.id" @click="doRestore(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 10a6 6 0 1 0 1.5-3.9M4 10V6M4 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                      </template>
                      <template v-else>
                        <button class="pl-icon-btn" @click="openEdit(person)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M13.586 3.586a2 2 0 112.828 2.828L7 15.828 3 17l1.172-4L13.586 3.586z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 6h12M8 6V4h4v2M9 10v5M11 10v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><rect x="5" y="6" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                        </button>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </template>

            <!-- Персоны без месяца рождения (только в режиме "Все") -->
            <template v-if="activeMonth === null && personsWithoutMonth.length > 0">
              <div class="pl-month-header pl-month-header--unknown">
                <div class="pl-month-header-left">
                  <div class="pl-month-header-icon pl-month-header-icon--mute">
                    <svg viewBox="0 0 16 16" fill="none">
                      <rect x="2" y="3" width="12" height="11" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                      <path d="M5 1v4M11 1v4M2 7h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                      <path d="M7 10h2M8 10v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                  </div>
                  <span class="pl-month-name">Без даты рождения</span>
                </div>
                <span class="pl-alpha-count">{{ personsWithoutMonth.length }}</span>
              </div>
              <div class="pl-grid">
                <div
                  v-for="person in personsWithoutMonth"
                  :key="person.id"
                  class="pl-card"
                  :class="{ 'pl-card--deleted': !person.checked }"
                >
                  <div class="pl-card-top">
                    <div class="pl-card-avatar">
                      <img
                        v-if="person.photo_thumb_path"
                        :src="person.photo_thumb_path"
                        :alt="initials(person)"
                        class="pl-card-avatar-img"
                        @error="($event.target as HTMLImageElement).style.display='none'"
                      />
                      <span v-else>{{ initials(person) }}</span>
                    </div>
                    <div class="pl-card-body">
                      <h2 class="pl-card-name">{{ person.last_name }} {{ person.first_name }}</h2>
                      <span v-if="person.partner" class="pl-card-partner">
                        <img v-if="person.partner.logo" :src="person.partner.logo" :alt="person.partner.name" class="pl-card-partner-logo" @error="($event.target as HTMLImageElement).style.display = 'none'"/>
                        <svg v-else viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                          <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                          <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                        </svg>
                        {{ person.partner.name }}
                      </span>
                    </div>
                  </div>
                  <div class="pl-card-bottom">
                    <div class="pl-card-details"/>
                    <div class="pl-card-actions">
                      <template v-if="deleteConfirmId === person.id">
                        <span class="pl-confirm-text">Удалить?</span>
                        <button class="pl-icon-btn pl-icon-btn--danger" :disabled="deleting" @click="doDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M5 10h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        </button>
                        <button class="pl-icon-btn" @click="cancelDelete">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                        </button>
                      </template>
                      <template v-else-if="!person.checked">
                        <span class="pl-deleted-badge">удалён</span>
                        <button class="pl-icon-btn pl-icon-btn--restore" :disabled="restoring === person.id" @click="doRestore(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 10a6 6 0 1 0 1.5-3.9M4 10V6M4 10H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                      </template>
                      <template v-else>
                        <button class="pl-icon-btn" @click="openEdit(person)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M13.586 3.586a2 2 0 112.828 2.828L7 15.828 3 17l1.172-4L13.586 3.586z" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button class="pl-icon-btn pl-icon-btn--danger" @click="confirmDelete(person.id)">
                          <svg viewBox="0 0 20 20" fill="none"><path d="M4 6h12M8 6V4h4v2M9 10v5M11 10v5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/><rect x="5" y="6" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/></svg>
                        </button>
                      </template>
                    </div>
                  </div>
                </div>
              </div>
            </template>

          </div>
        </template>

      </template>

      <!-- ── Модальное окно ─────────────────────────────────────── -->
      <Transition name="modal">
        <div v-if="modalOpen" class="pl-overlay" @click.self="closeModal">
          <div class="pl-modal">

            <div class="pl-modal-head">
              <h2 class="pl-modal-title">
                {{ modalMode === 'create' ? 'Новая персона' : 'Редактирование' }}
              </h2>
              <button class="pl-icon-btn" @click="closeModal">
                <svg viewBox="0 0 20 20" fill="none">
                  <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round"/>
                </svg>
              </button>
            </div>

            <div class="pl-modal-preview">
              <div class="pl-photo">
                <img
                  v-if="photoPreview"
                  :src="photoPreview"
                  alt="preview"
                  class="pl-preview-img"
                  @error="($event.target as HTMLImageElement).style.display='none'"
                />
                <span v-else>{{ initials(form.value) }}</span>
              </div>
            </div>

            <div class="pl-modal-form">
              <div class="pl-field">
                <label class="pl-label">Фото</label>
                <label class="pl-upload" :class="{ 'pl-upload--has': photoPreview }">
                  <img v-if="photoPreview" :src="photoPreview" class="pl-upload-preview"
                       alt="preview"/>
                  <div v-else class="pl-upload-placeholder">
                    <svg viewBox="0 0 24 24" fill="none">
                      <path d="M12 16V8M12 8l-3 3M12 8l3 3" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"/>
                      <rect x="3" y="3" width="18" height="18" rx="4" stroke="currentColor"
                            stroke-width="1.3"/>
                    </svg>
                    <span>Нажмите или перетащите файл</span>
                    <span class="pl-upload-hint">JPG, PNG, WEBP · до 2 МБ</span>
                  </div>
                  <input type="file" accept="image/jpeg,image/png,image/webp"
                         class="pl-upload-input" @change="onFileChange"/>
                </label>
                <button v-if="photoPreview" class="pl-upload-clear" type="button"
                        @click="removePhoto">
                  × Удалить фото
                </button>
              </div>

              <div class="pl-row">
                <div class="pl-field pl-field--grow"
                     :class="{ 'pl-field--err': formErrors.last_name }">
                  <label class="pl-label">Фамилия *</label>
                  <input v-model="form.last_name" class="pl-input" placeholder="Иванов"/>
                  <span v-if="formErrors.last_name" class="pl-field-err">{{ formErrors.last_name }}</span>
                </div>
                <div class="pl-field pl-field--grow"
                     :class="{ 'pl-field--err': formErrors.first_name }">
                  <label class="pl-label">Имя *</label>
                  <input v-model="form.first_name" class="pl-input" placeholder="Иван"/>
                  <span v-if="formErrors.first_name" class="pl-field-err">{{ formErrors.first_name }}</span>
                </div>
              </div>
              <div class="pl-field">
                <label class="pl-label">Отчество</label>
                <input v-model="form.middle_name" class="pl-input" placeholder="Иванович"/>
              </div>

              <p class="pl-section-label">Личные данные</p>
              <div class="pl-field">
                <label class="pl-label">День рождения</label>
                <div style="display: flex; gap: 10px;">
                  <select v-model="form.birth_day" class="pl-input" style="flex: 1;">
                    <option value="">День</option>
                    <option v-for="day in 31" :key="day" :value="day">{{ day }}</option>
                  </select>
                  <select v-model="form.birth_month" class="pl-input" style="flex: 1;">
                    <option value="">Месяц</option>
                    <option v-for="month in [
                      { value: 1, name: 'Январь' },{ value: 2, name: 'Февраль' },{ value: 3, name: 'Март' },
                      { value: 4, name: 'Апрель' },{ value: 5, name: 'Май' },{ value: 6, name: 'Июнь' },
                      { value: 7, name: 'Июль' },{ value: 8, name: 'Август' },{ value: 9, name: 'Сентябрь' },
                      { value: 10, name: 'Октябрь' },{ value: 11, name: 'Ноябрь' },{ value: 12, name: 'Декабрь' }
                    ]" :key="month.value" :value="month.value">{{ month.name }}</option>
                  </select>
                </div>
              </div>
              <div class="pl-field pl-field--grow" :class="{ 'pl-field--err': formErrors.position_short }">
                <label class="pl-label">Краткое описание должности *</label>
                <input v-model="form.position_short" class="pl-input" placeholder="Руководитель компании"/>
                <span v-if="formErrors.position_short" class="pl-field-err">{{ formErrors.position_short }}</span>
              </div>
              <div class="pl-field pl-field--grow" :class="{ 'pl-field--err': formErrors.position_full }">
                <label class="pl-label">Полное описание должности *</label>
                <input v-model="form.position_full" class="pl-input" placeholder="Генеральный директор ООО 'Название компании'"/>
                <span v-if="formErrors.position_full" class="pl-field-err">{{ formErrors.position_full }}</span>
              </div>

              <p class="pl-section-label">Контакты</p>
              <div class="pl-field">
                <label class="pl-label">Телефон</label>
                <div class="pl-input-icon-wrap">
                  <svg viewBox="0 0 16 16" fill="none" class="pl-input-icon">
                    <path d="M3 2h3l1.5 3.5-1.8 1.1a8 8 0 0 0 3.7 3.7l1.1-1.8L14 10v3c0 .6-.4 1-1 1C6 14 2 10 2 3c0-.6.4-1 1-1z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <input v-model="form.phone" class="pl-input pl-input--padded" placeholder="+7 (999) 000-00-00" type="tel"/>
                </div>
              </div>
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.email }">
                <label class="pl-label">E-mail</label>
                <div class="pl-input-icon-wrap">
                  <svg viewBox="0 0 16 16" fill="none" class="pl-input-icon">
                    <rect x="2" y="4" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M2 5l6 5 6-5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                  </svg>
                  <input v-model="form.email" class="pl-input pl-input--padded" placeholder="ivan@example.ru" type="email"/>
                </div>
                <span v-if="formErrors.email" class="pl-field-err">{{ formErrors.email }}</span>
              </div>
              <div class="pl-field" :class="{ 'pl-field--err': formErrors.web }">
                <label class="pl-label">Веб сайт</label>
                <div class="pl-input-icon-wrap">
                  <svg viewBox="0 0 16 16" fill="none" class="pl-input-icon">
                    <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.2"/>
                    <path d="M8 2c0 0-2.5 2.5-2.5 6s2.5 6 2.5 6M8 2c0 0 2.5 2.5 2.5 6S8 14 8 14M2 8h12" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                  </svg>
                  <input v-model="form.web" class="pl-input pl-input--padded" placeholder="example.ru" type="url"/>
                </div>
                <span v-if="formErrors.web" class="pl-field-err">{{ formErrors.web }}</span>
              </div>

              <p class="pl-section-label">Организация</p>
              <div class="pl-field">
                <label class="pl-label">Привязать к партнёру</label>
                <div class="pl-partner-wrap">
                  <div class="pl-partner-input-wrap">
                    <div class="pl-partner-preview" v-if="selectedPartner">
                      <img v-if="selectedPartner.logo" :src="selectedPartner.logo" :alt="selectedPartner.name" class="pl-partner-preview-logo" @error="($event.target as HTMLImageElement).style.display = 'none'"/>
                      <svg v-else viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                        <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                        <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                      </svg>
                    </div>
                    <svg v-else viewBox="0 0 16 16" fill="none" class="pl-input-icon">
                      <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                      <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                    </svg>
                    <input
                      v-model="partnerSearch"
                      class="pl-input pl-input--padded pl-partner-input"
                      placeholder="Найти или создать партнёра…"
                      @input="onPartnerInput"
                      @focus="onPartnerFocus"
                      @blur="setTimeout(() => { partnerDropOpen = false }, 150)"
                    />
                    <button v-if="selectedPartner" class="pl-partner-clear" type="button" @click="clearPartner">×</button>
                  </div>

                  <Transition name="drop">
                    <div v-if="partnerDropOpen" class="pl-partner-drop">
                      <div v-if="partnerLoading" class="pl-drop-item pl-drop-item--mute">
                        <span class="pl-spinner pl-spinner--sm"/> Поиск…
                      </div>
                      <template v-else>
                        <button v-for="p in partners" :key="p.id" class="pl-drop-item" :class="{ 'pl-drop-item--active': selectedPartner?.id === p.id }" type="button" @mousedown.prevent="selectPartner(p)">
                          <span class="pl-drop-logo">
                            <img v-if="p.logo" :src="p.logo" :alt="p.name" class="pl-drop-logo-img" @error="($event.target as HTMLImageElement).style.display = 'none'"/>
                            <svg v-else viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                              <rect x="2" y="7" width="12" height="8" rx="1" stroke="currentColor" stroke-width="1.2"/>
                              <path d="M5 7V5a3 3 0 0 1 6 0v2" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
                            </svg>
                          </span>
                          <span class="pl-drop-info">
                            <span class="pl-drop-name">{{ p.name }}</span>
                            <span class="pl-drop-site">{{ stripProtocol(p.site) }}</span>
                          </span>
                        </button>
                        <div v-if="!partners.length && !partnerSearch.trim()" class="pl-drop-item pl-drop-item--mute">Начните вводить название…</div>
                        <div v-if="!partners.length && partnerSearch.trim() && partnerExactMatch" class="pl-drop-item pl-drop-item--mute">Партнёров не найдено</div>
                        <button v-if="partnerSearch.trim() && !partnerExactMatch" class="pl-drop-item pl-drop-item--create" type="button" :disabled="creatingPartner" @mousedown.prevent="createAndSelectPartner">
                          <svg viewBox="0 0 16 16" fill="none" class="pl-icon-xs">
                            <path d="M8 3v10M3 8h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                          </svg>
                          {{ creatingPartner ? 'Создание…' : `Создать «${partnerSearch.trim()}»` }}
                        </button>
                      </template>
                    </div>
                  </Transition>
                </div>
                <span class="pl-field-hint">Привяжите существующего партнёра или создайте нового</span>
              </div>

              <div v-if="formErrors.global" class="pl-alert pl-alert--sm">{{ formErrors.global }}</div>
            </div>

            <div class="pl-modal-foot">
              <button class="pl-btn pl-btn--ghost" @click="closeModal">Отмена</button>
              <button class="pl-btn pl-btn--primary" :disabled="saving" @click="save">
                <span v-if="saving" class="pl-spinner"/>
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

:root {
  --bg-card: #0d1530;
  --border: rgba(96, 165, 250, 0.12);
  --border-hi: rgba(96, 165, 250, 0.28);
  --text-h: #e2edf8;
  --text-b: #a8c4e8;
  --text-mute: #3d5a8a;
  --accent: #3b82f6;
  --accent-hi: #60a5fa;
  --danger: #ef4444;
  --danger-bg: rgba(239, 68, 68, 0.1);
}

.pl-wrap {
  font-family: 'DM Sans', sans-serif;
  display: flex;
  flex-direction: column;
  gap: 24px;
  color: var(--text-b);
}

/* Шапка */
.pl-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.pl-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 32px;
  font-weight: 700;
  color: var(--text-h);
  margin: 0 0 4px;
  line-height: 1.1;
}

.pl-subtitle {
  font-size: 13px;
  color: var(--text-mute);
  margin: 0;
}

.pl-card-avatar img.pl-card-avatar-img {
  width: 65px;
  height: 65px;
  border-radius: 50%;
  object-fit: cover;
}

.pl-photo {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
}

.pl-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.pl-photo span {
  font-family: 'Cormorant Garamond', serif;
  font-size: 20px;
  font-weight: 600;
  color: #bfdbfe;
}

.pl-upload {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px dashed var(--border);
  border-radius: 10px;
  padding: 16px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: border-color 0.2s;
  min-height: 90px;
}

.pl-upload:hover { border-color: var(--border-hi); }
.pl-upload--has { border-style: solid; padding: 6px; }

.pl-upload-preview {
  max-height: 80px;
  max-width: 100%;
  border-radius: 6px;
  object-fit: contain;
}

.pl-upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: var(--text-mute);
}

.pl-upload-placeholder svg { width: 28px; height: 28px; }
.pl-upload-placeholder span { font-size: 13px; }
.pl-upload-hint { font-size: 11px !important; opacity: 0.6; }

.pl-upload-input {
  position: absolute;
  inset: 0;
  opacity: 0;
  cursor: pointer;
}

.pl-upload-clear {
  background: none;
  border: none;
  color: var(--danger);
  font-size: 13px;
  cursor: pointer;
  padding: 4px 0;
}

.pl-upload-clear:hover { text-decoration: underline; }

/* Кнопки */
.pl-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 9px 18px;
  border-radius: 8px;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  outline: none;
  transition: all 0.18s;
  white-space: nowrap;
}

.pl-btn--primary {
  background: var(--accent);
  color: #fff;
  box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
}

.pl-btn--primary:hover { background: #2563eb; box-shadow: 0 0 28px rgba(59, 130, 246, 0.35); }
.pl-btn--primary:disabled { opacity: 0.55; cursor: not-allowed; }

.pl-btn--ghost {
  background: rgba(96, 165, 250, 0.07);
  color: var(--text-b);
  border: 1px solid var(--border);
}

.pl-btn--ghost:hover { background: rgba(96, 165, 250, 0.13); border-color: var(--border-hi); }
.pl-btn-icon { width: 16px; height: 16px; }

.pl-icon-btn {
  width: 32px;
  height: 32px;
  padding: 0;
  border: none;
  outline: none;
  border-radius: 7px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(96, 165, 250, 0.07);
  color: var(--text-b);
  transition: all 0.15s;
}

.pl-icon-btn svg { width: 15px; height: 15px; }
.pl-icon-btn:hover { background: rgba(96, 165, 250, 0.15); color: var(--text-h); }
.pl-icon-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.pl-icon-btn--danger { background: var(--danger-bg); color: var(--danger); }
.pl-icon-btn--danger:hover { background: rgba(239, 68, 68, 0.2); }
.pl-icon-btn--restore { background: rgba(34, 197, 94, 0.1); color: #86efac; }
.pl-icon-btn--restore:hover { background: rgba(34, 197, 94, 0.2); }
.pl-icon-xs { width: 12px; height: 12px; flex-shrink: 0; }

/* Алерт */
.pl-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: var(--danger-bg);
  border: 1px solid rgba(239, 68, 68, 0.2);
  border-radius: 10px;
  font-size: 13px;
  color: #fca5a5;
}

.pl-alert--sm { padding: 10px 14px; }
.pl-alert svg { width: 18px; height: 18px; flex-shrink: 0; }

.pl-retry {
  margin-left: auto;
  background: none;
  border: 1px solid rgba(239, 68, 68, 0.3);
  border-radius: 6px;
  padding: 4px 10px;
  color: #fca5a5;
  font-size: 12px;
  cursor: pointer;
}

.pl-retry:hover { background: rgba(239, 68, 68, 0.08); }

/* Скелетон */
.pl-skeleton { display: flex; flex-direction: column; gap: 16px; }

.sk-bar {
  border-radius: 6px;
  background: rgba(96, 165, 250, 0.07);
  animation: shimmer 1.4s infinite;
}

.sk-bar--search { height: 40px; width: 360px; }

.sk-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.sk-card {
  height: 170px;
  border-radius: 12px;
  background: rgba(96, 165, 250, 0.05);
  animation: shimmer 1.4s infinite;
}

@keyframes shimmer {
  0%, 100% { opacity: .6; }
  50% { opacity: 1; }
}

/* Тулбар */
.pl-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.pl-search-wrap {
  position: relative;
  flex: 1;
  max-width: 460px;
  min-width: 200px;
}

.pl-search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  pointer-events: none;
}

.pl-search {
  width: 100%;
  padding: 9px 14px 9px 36px;
  box-sizing: border-box;
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-h);
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border-color 0.2s;
}

.pl-search::placeholder { color: var(--text-mute); }
.pl-search:focus { border-color: var(--border-hi); }

/* Переключатель группировки */
.pl-group-toggle {
  display: flex;
  gap: 4px;
  background: rgba(96, 165, 250, 0.05);
  border: 1px solid var(--border);
  border-radius: 8px;
  padding: 3px;
}

.pl-group-toggle-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 6px;
  border: none;
  outline: none;
  background: none;
  color: var(--text-mute);
  font-family: 'DM Sans', sans-serif;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s;
  white-space: nowrap;
}

.pl-group-toggle-btn:hover {
  color: var(--text-b);
  background: rgba(96, 165, 250, 0.06);
}

.pl-group-toggle-btn--active {
  background: var(--accent);
  color: #fff;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.pl-group-toggle-btn--active:hover {
  background: #2563eb;
  color: #fff;
}

.pl-count {
  font-size: 12px;
  color: var(--text-mute);
  white-space: nowrap;
}

/* Пустое */
.pl-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 60px 24px;
  background: var(--bg-card);
  border: 1px dashed var(--border);
  border-radius: 14px;
  text-align: center;
}

.pl-empty-icon { width: 48px; height: 48px; opacity: 0.4; }
.pl-empty-text { font-size: 15px; color: var(--text-mute); margin: 0; }

/* Сетка */
.pl-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 16px;
}

.pl-groups { display: flex; flex-direction: column; gap: 16px; }

/* Алфавитная навигация */
.pl-alpha-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 12px 0;
  border-bottom: 1px solid var(--border);
}

.pl-alpha-nav-btn {
  min-width: 32px;
  height: 32px;
  padding: 0 6px;
  border: 1px solid var(--border);
  border-radius: 6px;
  background: rgba(96,165,250,0.04);
  color: var(--text-b);
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  line-height: 1;
}

.pl-alpha-nav-btn:hover:not(:disabled) {
  background: rgba(96,165,250,0.12);
  border-color: var(--border-hi);
  color: var(--text-h);
}

.pl-alpha-nav-btn--active {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
}

.pl-alpha-nav-btn--disabled { opacity: 0.2; cursor: default; }

/* Заголовки групп (алфавит) */
.pl-alpha-header {
  display: flex;
  align-items: baseline;
  gap: 10px;
  padding: 4px 0 8px;
  border-bottom: 1px solid var(--border);
}

.pl-alpha-letter {
  font-family: 'Cormorant Garamond', serif;
  font-size: 28px;
  font-weight: 700;
  color: var(--accent);
  line-height: 1;
}

.pl-alpha-count { font-size: 12px; color: var(--text-mute); }

/* Навигация по месяцам */
.pl-month-nav {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
  padding: 12px 0;
  border-bottom: 1px solid var(--border);
}

.pl-month-nav-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  min-width: 44px;
  padding: 7px 6px 5px;
  border: 1px solid var(--border);
  border-radius: 8px;
  background: rgba(96, 165, 250, 0.04);
  color: var(--text-b);
  font-family: 'DM Sans', sans-serif;
  font-size: 9px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  cursor: pointer;
  transition: all 0.15s;
  line-height: 1;
}

.pl-month-nav-btn:hover:not(:disabled) {
  background: rgba(96, 165, 250, 0.12);
  border-color: var(--border-hi);
  color: var(--text-h);
}

.pl-month-nav-btn--active {
  background: var(--accent);
  border-color: var(--accent);
  color: #fff;
  box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
}

.pl-month-nav-btn--active:hover {
  background: #2563eb;
  color: #fff;
}

.pl-month-nav-btn--empty {
  opacity: 0.22;
  cursor: default;
}

.pl-month-nav-icon {
  width: 15px;
  height: 15px;
  flex-shrink: 0;
}

/* Заголовки месяцев */
.pl-month-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 16px;
  background: rgba(59, 130, 246, 0.05);
  border: 1px solid var(--border);
  border-left: 3px solid var(--accent);
  border-radius: 8px;
}

.pl-month-header--unknown {
  background: rgba(96, 165, 250, 0.02);
  border-left-color: var(--text-mute);
  border-style: dashed;
  border-left-style: solid;
}

.pl-month-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.pl-month-header-icon {
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.2);
  border-radius: 6px;
  color: var(--accent-hi);
  flex-shrink: 0;
}

.pl-month-header-icon svg {
  width: 14px;
  height: 14px;
}

.pl-month-header-icon--mute {
  background: rgba(96, 165, 250, 0.04);
  border-color: var(--border);
  color: var(--text-mute);
}

.pl-month-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 700;
  color: var(--text-h);
  line-height: 1;
}

.pl-month-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 13px;
  font-weight: 400;
  color: var(--accent);
  background: rgba(59, 130, 246, 0.12);
  padding: 2px 8px;
  border-radius: 20px;
  line-height: 1.4;
}

/* Бейдж дня в режиме месяцев */
.pl-card-day-badge {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 700;
  color: var(--accent-hi);
  line-height: 1;
  opacity: 0.85;
}

.pl-card-detail--day {
  font-size: inherit;
}

/* Карточка */
.pl-card {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 18px 16px;
  background: rgba(96, 165, 250, 0.05);
  border: 1px solid var(--border);
  border-radius: 12px;
  transition: border-color 0.2s, box-shadow 0.2s, opacity 0.2s;
}

.pl-card:hover {
  border-color: var(--border-hi);
  box-shadow: 0 4px 24px rgba(59, 130, 246, 0.08);
}

.pl-card--deleted {
  opacity: 0.38;
  filter: grayscale(0.4);
  border-style: dashed;
}

.pl-card--deleted:hover { opacity: 0.55; box-shadow: none; }

.pl-card-top { display: flex; align-items: flex-start; gap: 12px; }

.pl-card-avatar {
  width: 44px;
  height: 44px;
  flex-shrink: 0;
  border-radius: 50%;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 12px rgba(37, 99, 235, 0.2);
}

.pl-card-avatar span {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  color: #bfdbfe;
  letter-spacing: 0.02em;
}

.pl-card-body {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.pl-card-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 16px;
  font-weight: 600;
  color: var(--text-h);
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.pl-card-partner {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: var(--text-mute);
  background: rgba(96, 165, 250, 0.07);
  border: 1px solid var(--border);
  padding: 2px 7px 2px 4px;
  border-radius: 20px;
  width: fit-content;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  max-width: 100%;
}

.pl-card-partner-logo {
  width: 14px;
  height: 14px;
  object-fit: contain;
  border-radius: 2px;
  background: rgba(255, 255, 255, 0.1);
  flex-shrink: 0;
}

.pl-card-details { display: flex; justify-content: flex-end; }

.pl-card-detail {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 18px;
  color: var(--text-mute);
}

.pl-card-detail .pl-icon-xs { opacity: 0.6; flex-shrink: 0; }

.pl-card-link {
  color: var(--accent);
  text-decoration: none;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  transition: color 0.15s;
  min-width: 0;
}

.pl-card-link:hover { color: var(--accent-hi); }

.pl-card-bottom {
  margin-top: auto;
  display: flex;
  justify-content: space-between;
  align-items: self-end;
}

.pl-card-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 6px;
  border-top: 1px solid var(--border);
  padding-top: 10px;
}

.pl-confirm-text { font-size: 12px; color: var(--danger); white-space: nowrap; margin-right: auto; }
.pl-deleted-badge { font-size: 11px; color: var(--text-mute); letter-spacing: 0.06em; text-transform: uppercase; margin-right: auto; }

/* Модалка */
.pl-overlay {
  position: fixed;
  inset: 0;
  z-index: 200;
  background: rgba(5, 10, 24, 0.8);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}

.pl-modal {
  width: 100%;
  max-width: 520px;
  background: #0a1228;
  border: 1px solid var(--border-hi);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 24px 80px rgba(0, 0, 0, 0.6);
  max-height: 90vh;
  overflow-y: auto;
}

.pl-modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  background: #0a1228;
  z-index: 1;
}

.pl-modal-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 22px;
  font-weight: 600;
  color: var(--text-h);
  margin: 0;
}

.pl-modal-preview {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 14px 24px;
  background: rgba(59, 130, 246, 0.04);
  border-bottom: 1px solid var(--border);
}

.pl-modal-form {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.pl-section-label {
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--accent);
  margin: 6px 0 2px;
  border-bottom: 1px solid rgba(59, 130, 246, 0.15);
  padding-bottom: 6px;
}

.pl-row { display: flex; gap: 12px; }
.pl-field { display: flex; flex-direction: column; gap: 6px; }
.pl-field--grow { flex: 1; min-width: 0; }
.pl-field--err .pl-input { border-color: rgba(239, 68, 68, 0.5); }

.pl-label {
  font-size: 11px;
  color: var(--text-mute);
  letter-spacing: 0.07em;
  text-transform: uppercase;
}

.pl-input {
  padding: 10px 14px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid var(--border);
  border-radius: 8px;
  color: var(--text-h);
  font-size: 14px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border-color 0.2s;
  width: 100%;
  box-sizing: border-box;
}

.pl-input::placeholder { color: var(--text-mute); opacity: 0.5; }
.pl-input:focus { border-color: var(--border-hi); background: rgba(255, 255, 255, 0.06); }
.pl-input--padded { padding-left: 36px; }
.pl-field-err { font-size: 12px; color: #fca5a5; }
.pl-field-hint { font-size: 11px; color: var(--text-mute); }

.pl-input-icon-wrap { position: relative; }

.pl-input-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  width: 14px;
  height: 14px;
  color: var(--text-mute);
  pointer-events: none;
}

.pl-modal-foot {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 10px;
  padding: 16px 24px;
  border-top: 1px solid var(--border);
  position: sticky;
  bottom: 0;
  background: #0a1228;
}

/* Партнёр */
.pl-partner-wrap { position: relative; }
.pl-partner-input-wrap { position: relative; }

.pl-partner-preview {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

.pl-partner-preview-logo { width: 16px; height: 16px; object-fit: contain; border-radius: 2px; }
.pl-partner-input { padding-right: 30px !important; }

.pl-partner-clear {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-mute);
  font-size: 18px;
  line-height: 1;
  cursor: pointer;
  padding: 0;
}

.pl-partner-clear:hover { color: var(--danger); }

.pl-partner-drop {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  z-index: 50;
  background: #0d1a3a;
  border: 1px solid var(--border-hi);
  border-radius: 8px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
  max-height: 220px;
  overflow-y: auto;
}

.pl-drop-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 8px 14px;
  background: none;
  border: none;
  border-bottom: 1px solid rgba(96, 165, 250, 0.06);
  color: var(--text-b);
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  text-align: left;
  transition: background 0.15s;
}

.pl-drop-item:last-child { border-bottom: none; }
.pl-drop-item:hover { background: rgba(96, 165, 250, 0.08); color: var(--text-h); }
.pl-drop-item--active { background: rgba(59, 130, 246, 0.12); color: var(--accent-hi); }
.pl-drop-item--mute { color: var(--text-mute); cursor: default; font-style: italic; }
.pl-drop-item--mute:hover { background: none; }
.pl-drop-item--create { color: #86efac; }
.pl-drop-item--create:hover { background: rgba(34, 197, 94, 0.08); }
.pl-drop-item--create:disabled { opacity: 0.6; cursor: not-allowed; }

.pl-drop-logo {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  border-radius: 5px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.pl-drop-logo-img { width: 100%; height: 100%; object-fit: contain; background: #fff; }

.pl-drop-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.pl-drop-name { font-size: 13px; color: var(--text-h); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pl-drop-site { font-size: 11px; color: var(--text-mute); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

/* Спиннер */
.pl-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

.pl-spinner--sm { width: 11px; height: 11px; border-width: 1.5px; }

@keyframes spin { to { transform: rotate(360deg); } }

/* Переходы */
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
.modal-enter-active .pl-modal, .modal-leave-active .pl-modal { transition: transform 0.2s; }
.modal-enter-from .pl-modal { transform: translateY(14px); }
.modal-leave-to .pl-modal { transform: translateY(8px); }

.drop-enter-active, .drop-leave-active { transition: opacity 0.15s, transform 0.15s; }
.drop-enter-from, .drop-leave-to { opacity: 0; transform: translateY(-4px); }

/* Экспорт */
.pl-btn--export {
  background: rgba(96, 165, 250, 0.1);
  color: var(--accent-hi);
  border: 1px solid var(--border-hi);
}
.pl-btn--export:hover { background: rgba(96, 165, 250, 0.18); }

.pl-export-wrap { position: relative; }

.pl-export-drop {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  z-index: 100;
  width: 260px;
  background: #0d1a3a;
  border: 1px solid var(--border-hi);
  border-radius: 12px;
  box-shadow: 0 12px 40px rgba(0,0,0,0.5);
  overflow: hidden;
}

.pl-export-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 14px;
  font-size: 12px;
  font-weight: 500;
  color: var(--text-mute);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  border-bottom: 1px solid var(--border);
}

.pl-export-divider { height: 1px; background: var(--border); margin: 4px 0; }

.pl-export-item {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 8px 14px;
  background: none;
  border: none;
  color: var(--text-b);
  font-size: 13px;
  font-family: 'DM Sans', sans-serif;
  cursor: pointer;
  text-align: left;
  transition: background 0.15s;
}

.pl-export-item:hover:not(:disabled) { background: rgba(96, 165, 250, 0.08); color: var(--text-h); }
.pl-export-item:disabled { cursor: default; }
.pl-export-item--all { font-weight: 500; color: var(--accent-hi); }
.pl-export-item--empty { opacity: 0.35; }
.pl-export-item--skeleton {
  height: 34px;
  background: rgba(96, 165, 250, 0.05);
  animation: shimmer 1.4s infinite;
  border-radius: 4px;
  margin: 2px 8px;
  width: auto;
  padding: 0;
}

.pl-export-month-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px;
  font-weight: 700;
  color: var(--accent);
  min-width: 24px;
  line-height: 1;
}

.pl-export-month-name { flex: 1; }

.pl-export-badge {
  font-size: 11px;
  font-weight: 500;
  padding: 2px 7px;
  border-radius: 20px;
  background: rgba(59,130,246,0.15);
  color: var(--accent-hi);
  min-width: 24px;
  text-align: center;
}

.pl-export-badge--zero { background: rgba(96, 165, 250, 0.05); color: var(--text-mute); }

/* Адаптив */
@media (max-width: 1200px) {
  .pl-grid { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 900px) {
  .pl-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
  .pl-header { flex-direction: column; align-items: flex-start; }
  .pl-toolbar { flex-wrap: wrap; }
  .pl-search-wrap { max-width: 100%; }
}

@media (max-width: 480px) {
  .pl-grid { grid-template-columns: 1fr; }
  .sk-grid { grid-template-columns: 1fr; }
  .pl-row { flex-direction: column; }
  .pl-group-toggle-btn span { display: none; }
}
</style>

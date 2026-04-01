<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '../../../composable/useAuth.js';
import { useRouter } from 'vue-router';

const { user, logout, loading } = useAuth();
const router = useRouter();
const menuOpen = ref(false);

const handleLogout = async () => {
  try {
    await logout();
    await router.push({ name: 'Login' });
  } catch (error) {
    console.error('Logout error:', error);
  }
};

const initials = (name: string = '') =>
  name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2) || '?';
</script>

<template>
  <div class="al-wrap">

    <!-- Фоновые орбы -->
    <div class="al-bg" aria-hidden="true">
      <div class="al-orb al-orb1" />
      <div class="al-orb al-orb2" />
      <div class="al-grid" />
    </div>

    <!-- Сайдбар -->
    <aside class="al-sidebar">
      <div class="al-sidebar-top">

        <!-- Лого -->
        <router-link to="/dashboard" class="al-brand">
          <div class="al-brand-icon">
            <svg viewBox="0 0 24 24" fill="none">
              <polygon points="12,2 22,7 22,17 12,22 2,17 2,7" stroke="white" stroke-width="1.5" fill="none"/>
              <circle cx="12" cy="12" r="2.5" fill="white"/>
            </svg>
          </div>
          <div class="al-brand-texts">
            <span class="al-brand-name">Власть и Бизнес</span>
            <span class="al-brand-sub">Деловой календарь</span>
          </div>
        </router-link>

        <div class="al-divider" />

        <!-- Навигация -->
        <nav class="al-nav">
          <router-link to="/dashboard" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <rect x="2" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="11" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="2" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="11" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
            </svg>
            <span>Дашборд</span>
          </router-link>

          <router-link to="/calendar" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <rect x="2" y="3" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.3"/>
              <path d="M2 7h16M6 3v4M14 3v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>Календарь</span>
          </router-link>

          <router-link to="/events" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <path d="M9 5H7a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h0a2 2 0 002-2M9 5a2 2 0 012-2h0a2 2 0 012 2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>События</span>
          </router-link>

          <router-link to="/profile" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.3"/>
              <path d="M3 17a7 7 0 0114 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>Профиль</span>
          </router-link>

          <!-- Admin секция -->
          <template v-if="user?.roles?.includes('admin')">
            <div class="al-nav-section">Администрирование</div>
            <router-link to="/admin" class="al-nav-item" active-class="al-nav-item--active">
              <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
                <path d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947z" stroke="currentColor" stroke-width="1.3"/>
                <circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.3"/>
              </svg>
              <span>Настройки</span>
            </router-link>
          </template>
        </nav>
      </div>

      <!-- Профиль внизу сайдбара -->
      <div class="al-sidebar-bottom">
        <div class="al-divider" />
        <div class="al-user" @click="menuOpen = !menuOpen">
          <div class="al-user-avatar">{{ initials(user?.name) }}</div>
          <div class="al-user-info">
            <span class="al-user-name">{{ user?.name }}</span>
            <span class="al-user-role">{{ user?.roles?.[0] || 'user' }}</span>
          </div>
          <svg viewBox="0 0 16 16" fill="none" width="14" height="14" class="al-user-chevron" :class="{ 'al-user-chevron--open': menuOpen }">
            <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
          </svg>
        </div>

        <!-- Дропдаун -->
        <div v-if="menuOpen" class="al-dropdown">
          <router-link to="/profile" class="al-dropdown-item" @click="menuOpen = false">
            <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
              <circle cx="8" cy="6" r="2.5" stroke="currentColor" stroke-width="1.2"/>
              <path d="M2 14a6 6 0 0112 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
            Мой профиль
          </router-link>
          <button class="al-dropdown-item al-dropdown-item--danger" @click="handleLogout" :disabled="loading">
            <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
              <path d="M6 2H3a1 1 0 00-1 1v10a1 1 0 001 1h3M10 11l3-3-3-3M13 8H6" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            {{ loading ? 'Выход...' : 'Выйти' }}
          </button>
        </div>
      </div>
    </aside>

    <!-- Основной контент -->
    <div class="al-body">

      <!-- Топбар -->
      <header class="al-topbar">
        <div class="al-topbar-left">
          <div class="al-breadcrumb">
            <span class="al-breadcrumb-root">Главная</span>
            <svg viewBox="0 0 16 16" fill="none" width="12" height="12"><path d="M6 4l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>
            <span class="al-breadcrumb-current">Дашборд</span>
          </div>
        </div>
        <div class="al-topbar-right">
          <button class="al-topbar-btn">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <path d="M10 2a6 6 0 016 6c0 2.21-.6 4.1-1.72 5.28L15 14H5l.72-.72A8.98 8.98 0 014 8a6 6 0 016-6zM8 16a2 2 0 004 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
          </button>
          <div class="al-topbar-avatar">{{ initials(user?.name) }}</div>
        </div>
      </header>

      <!-- Слот -->
      <main class="al-main">
        <slot />
      </main>
    </div>

  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap');

/* ─── Основа ────────────────────────── */
.al-wrap {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #06091a;
  color: #dce8f5;
  display: flex;
  position: relative;
  overflow: hidden;
}

/* ─── Фон ───────────────────────────── */
.al-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
.al-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
.al-orb1 {
  width: 600px; height: 600px; top: -250px; left: -100px;
  background: radial-gradient(circle, rgba(29,78,216,0.2), transparent 65%);
}
.al-orb2 {
  width: 400px; height: 400px; bottom: -100px; right: 10%;
  background: radial-gradient(circle, rgba(37,99,235,0.12), transparent 65%);
}
.al-grid {
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px);
  background-size: 56px 56px;
}

/* ─── Сайдбар ───────────────────────── */
.al-sidebar {
  position: fixed; left: 0; top: 0; bottom: 0;
  width: 240px;
  background: rgba(8,12,28,0.85);
  border-right: 1px solid rgba(96,165,250,0.1);
  backdrop-filter: blur(12px);
  display: flex; flex-direction: column; justify-content: space-between;
  z-index: 100;
  padding: 24px 0;
}
.al-sidebar-top { display: flex; flex-direction: column; flex: 1; overflow-y: auto; }

/* Лого */
.al-brand {
  display: flex; align-items: center; gap: 10px;
  padding: 0 20px 0;
  text-decoration: none; color: #dce8f5;
  transition: opacity 0.2s;
}
.al-brand:hover { opacity: 0.8; }
.al-brand-icon {
  width: 32px; height: 32px;
  background: linear-gradient(135deg, #1e40af, #3b82f6);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 0 16px rgba(59,130,246,0.3);
}
.al-brand-icon svg { width: 18px; height: 18px; }
.al-brand-texts { display: flex; flex-direction: column; gap: 0; }
.al-brand-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 15px; font-weight: 600; color: #e2edf8; line-height: 1.2;
}
.al-brand-sub {
  font-size: 9px; font-weight: 300;
  color: #3d5a8a; letter-spacing: 0.1em; text-transform: uppercase;
}

.al-divider { height: 1px; background: rgba(96,165,250,0.08); margin: 20px 0; }

/* Навигация */
.al-nav { display: flex; flex-direction: column; gap: 2px; padding: 0 12px; }
.al-nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px; border-radius: 8px;
  font-size: 13px; color: #4a6fa5;
  text-decoration: none;
  transition: all 0.15s;
}
.al-nav-item:hover { color: #a8c4e8; background: rgba(96,165,250,0.07); }
.al-nav-item--active { color: #93c5fd !important; background: rgba(147,197,253,0.1) !important; }
.al-nav-section {
  font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase;
  color: #2a3f65; padding: 12px 12px 4px;
}

/* Юзер внизу */
.al-sidebar-bottom { padding: 0 12px; }
.al-user {
  display: flex; align-items: center; gap: 10px;
  padding: 10px 12px; border-radius: 8px; cursor: pointer;
  transition: background 0.15s; position: relative;
}
.al-user:hover { background: rgba(96,165,250,0.07); }
.al-user-avatar {
  width: 32px; height: 32px; border-radius: 8px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 600; color: #bfdbfe; flex-shrink: 0;
}
.al-user-info { flex: 1; min-width: 0; }
.al-user-name { display: block; font-size: 13px; color: #c7daf0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.al-user-role { display: block; font-size: 10px; color: #3d5a8a; text-transform: uppercase; letter-spacing: 0.06em; }
.al-user-chevron { color: #3d5a8a; transition: transform 0.2s; }
.al-user-chevron--open { transform: rotate(180deg); }

/* Дропдаун */
.al-dropdown {
  position: absolute; bottom: calc(100% + 4px); left: 12px; right: 12px;
  background: #0d1530;
  border: 1px solid rgba(96,165,250,0.15);
  border-radius: 10px; overflow: hidden;
  box-shadow: 0 -8px 32px rgba(0,0,0,0.4);
}
.al-dropdown-item {
  display: flex; align-items: center; gap: 8px;
  width: 100%; padding: 10px 14px;
  font-size: 13px; color: #7a9cc5;
  background: none; border: none; cursor: pointer;
  text-decoration: none;
  transition: all 0.15s; text-align: left;
  font-family: 'DM Sans', sans-serif;
}
.al-dropdown-item:hover { color: #dce8f5; background: rgba(96,165,250,0.07); }
.al-dropdown-item--danger { color: #f87171; }
.al-dropdown-item--danger:hover { background: rgba(239,68,68,0.08); }
.al-dropdown-item:disabled { opacity: 0.5; cursor: not-allowed; }

/* ─── Правая часть ──────────────────── */
.al-body {
  margin-left: 240px;
  flex: 1;
  display: flex; flex-direction: column;
  position: relative; z-index: 10;
  min-height: 100vh;
}

/* Топбар */
.al-topbar {
  position: sticky; top: 0; z-index: 50;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 32px;
  height: 56px;
  background: rgba(6,9,26,0.7);
  border-bottom: 1px solid rgba(96,165,250,0.08);
  backdrop-filter: blur(10px);
}
.al-breadcrumb { display: flex; align-items: center; gap: 6px; }
.al-breadcrumb-root { font-size: 13px; color: #3d5a8a; }
.al-breadcrumb svg { color: #2a3f65; }
.al-breadcrumb-current { font-size: 13px; color: #7a9cc5; }
.al-topbar-right { display: flex; align-items: center; gap: 10px; }
.al-topbar-btn {
  width: 34px; height: 34px; border-radius: 8px;
  background: rgba(96,165,250,0.07);
  border: 1px solid rgba(96,165,250,0.12);
  display: flex; align-items: center; justify-content: center;
  color: #4a6fa5; cursor: pointer; transition: all 0.15s;
}
.al-topbar-btn:hover { color: #93c5fd; background: rgba(96,165,250,0.12); }
.al-topbar-avatar {
  width: 32px; height: 32px; border-radius: 8px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  display: flex; align-items: center; justify-content: center;
  font-size: 12px; font-weight: 600; color: #bfdbfe;
  box-shadow: 0 0 12px rgba(37,99,235,0.25);
}

/* Слот */
.al-main { flex: 1; padding: 32px; }

/* ─── Адаптив ───────────────────────── */
@media (max-width: 768px) {
  .al-sidebar { display: none; }
  .al-body    { margin-left: 0; }
  .al-main    { padding: 20px 16px; }
}
</style>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuth } from '@/composable/useAuth';
import { useRouter } from 'vue-router';
import Header from "@/views/Components/Header.vue";

const { user, logout, loading } = useAuth();
const router = useRouter();
const menuOpen = ref(false);
const mobileOpen = ref(false)

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
  <div class="gl-wrap">

    <!-- ─── Фон ──────────────────────────────────────────── -->
    <div class="gl-bg" aria-hidden="true">
      <div class="gl-orb gl-orb1" />
      <div class="gl-orb gl-orb2" />
      <div class="gl-orb gl-orb3" />
      <div class="gl-grid" />
    </div>

    <!-- ─── Header ───────────────────────────────────────── -->
    <Header @click="mobileOpen" />

    <!-- Мобильная навигация -->
    <div class="gl-mobile-nav" :class="{ 'is-open': mobileOpen }">

      <div class="gl-mobile-divider" />
    </div>

    <section class="gl-section">
      <div class="gl-container">

    <!-- Сайдбар -->
    <aside class="al-sidebar">
      <div class="al-sidebar-top">

        <div class="al-divider" />

        <!-- Навигация -->
        <nav class="al-nav">
          <router-link to="/persons" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <rect x="2" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="11" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="2" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
              <rect x="11" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.3"/>
            </svg>
            <span>Персоны</span>
          </router-link>

          <router-link to="/partners" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <rect x="2" y="3" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.3"/>
              <path d="M2 7h16M6 3v4M14 3v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>Участники</span>
          </router-link>

          <router-link to="/profile" class="al-nav-item" active-class="al-nav-item--active">
            <svg viewBox="0 0 20 20" fill="none" width="16" height="16">
              <circle cx="10" cy="7" r="3" stroke="currentColor" stroke-width="1.3"/>
              <path d="M3 17a7 7 0 0114 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
            </svg>
            <span>Профиль</span>
          </router-link>

        </nav>
      </div>

      <!-- Профиль внизу сайдбара -->
      <div class="al-sidebar-bottom">
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
      <!-- Слот -->
      <main class="al-main">
        <slot />
      </main>
    </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap');
.gl-section { position: relative; z-index: 9; padding: 0; border-top: 1px solid rgba(96,165,250,0.08); }
.gl-container {
  width: 100%;
  display: flex;
  justify-content: space-between;
}
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
  width: 350px;
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
/* ═══════════════════════════════════════
   HEADER
═══════════════════════════════════════ */
.gl-header {
  width: 100%;
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
   ФОН
═══════════════════════════════════════ */
.gl-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.gl-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
.gl-orb1 { width: 700px; height: 700px; top: -300px; left: -200px; background: radial-gradient(circle, rgba(37,99,235,0.28), transparent 65%); }
.gl-orb2 { width: 500px; height: 500px; top: 30%; right: -150px; background: radial-gradient(circle, rgba(29,78,216,0.2), transparent 65%); }
.gl-orb3 { width: 400px; height: 400px; bottom: -100px; left: 30%; background: radial-gradient(circle, rgba(96,165,250,0.08), transparent 65%); }
.gl-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px); background-size: 56px 56px; }

/* ═══════════════════════════════════════
   ОСНОВА
═══════════════════════════════════════ */
.gl-wrap {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #06091a;
  color: #dce8f5;
  display: flex;
  flex-wrap: wrap;
  position: relative;
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

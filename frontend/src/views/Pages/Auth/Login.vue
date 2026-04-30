<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '@/composable/useAuth';
import GuestLayout from '../../Layouts/GuestLayout.vue';

const router  = useRouter();
const route   = useRoute();
const { login, loading, errors } = useAuth();

const form = ref({ email: '', password: '', remember: false });
const showPass = ref(false);

const handleLogin = async () => {
  try {
    await login(form.value);
    const redirectTo = route.query.redirect as string || '/';
    await router.push(redirectTo);
  } catch (error) {
    console.error('Login failed:', error);
  }
};
</script>

<template>
  <GuestLayout>

    <div class="lf-head">
      <div class="lf-icon">
        <svg viewBox="0 0 24 24" fill="none">
          <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      <h1 class="lf-title">Вход в систему</h1>
      <p class="lf-subtitle">Войдите в свой аккаунт</p>
    </div>

    <form class="lf-form" @submit.prevent="handleLogin" novalidate>

      <!-- Глобальная ошибка -->
      <div v-if="errors.message" class="lf-alert">
        <svg viewBox="0 0 16 16" fill="none" width="14" height="14">
          <circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="1.2"/>
          <path d="M8 5v3.5M8 11h.01" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
        </svg>
        {{ errors.message }}
      </div>

      <!-- Email -->
      <div class="lf-field">
        <label class="lf-label">Email</label>
        <div class="lf-input-wrap" :class="{ 'lf-input-wrap--err': errors.email }">
          <svg class="lf-input-icon" viewBox="0 0 20 20" fill="none">
            <path d="M2 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5z" stroke="currentColor" stroke-width="1.2"/>
            <path d="M2 5l8 6 8-6" stroke="currentColor" stroke-width="1.2"/>
          </svg>
          <input
            v-model="form.email"
            type="email"
            class="lf-input"
            placeholder="your@email.com"
            autocomplete="email"
            required
          />
        </div>
        <p v-if="errors.email" class="lf-err-text">{{ errors.email[0] }}</p>
      </div>

      <!-- Пароль -->
      <div class="lf-field">
        <label class="lf-label">Пароль</label>
        <div class="lf-input-wrap" :class="{ 'lf-input-wrap--err': errors.password }">
          <svg class="lf-input-icon" viewBox="0 0 20 20" fill="none">
            <rect x="3" y="9" width="14" height="9" rx="2" stroke="currentColor" stroke-width="1.2"/>
            <path d="M7 9V6a3 3 0 0 1 6 0v3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
          <input
            v-model="form.password"
            :type="showPass ? 'text' : 'password'"
            class="lf-input"
            placeholder="••••••••"
            autocomplete="current-password"
            required
          />
          <button type="button" class="lf-eye" @click="showPass = !showPass" tabindex="-1">
            <svg v-if="!showPass" viewBox="0 0 20 20" fill="none" width="16" height="16">
              <path d="M1 10s3-6 9-6 9 6 9 6-3 6-9 6-9-6-9-6z" stroke="currentColor" stroke-width="1.2"/>
              <circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.2"/>
            </svg>
            <svg v-else viewBox="0 0 20 20" fill="none" width="16" height="16">
              <path d="M3 3l14 14M8.5 8.6A2.5 2.5 0 0 0 12 12M6 5.3C3.5 6.7 2 9 2 9s2.5 5 8 5c1.3 0 2.5-.3 3.5-.8" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
              <path d="M11 4.1C14 5 17 8 18 10c-.4.8-1 1.7-1.8 2.4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <p v-if="errors.password" class="lf-err-text">{{ errors.password[0] }}</p>
      </div>

      <!-- Remember + Forgot -->
      <div class="lf-row">
        <label class="lf-check">
          <input v-model="form.remember" type="checkbox" class="lf-check-input" />
          <span class="lf-check-box" />
          <span class="lf-check-label">Запомнить меня</span>
        </label>
        <a href="#" class="lf-forgot">Забыли пароль?</a>
      </div>

      <!-- Кнопка -->
      <button type="submit" class="lf-btn" :disabled="loading">
        <span v-if="!loading">Войти</span>
        <span v-else class="lf-spinner-wrap">
          <span class="lf-spinner" />
          Входим...
        </span>
      </button>

    </form>

    <!-- Ссылка на регистрацию -->
    <div class="lf-footer">
      <span>Нет аккаунта?</span>
      <a href="http://calendar.local/register" class="lf-link">Зарегистрироваться →</a>
    </div>

  </GuestLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&family=DM+Sans:wght@300;400;500&display=swap');

/* ─── Шапка формы ───────────────────── */
.lf-head {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  margin-bottom: 32px;
  gap: 8px;
}
.lf-icon {
  width: 44px; height: 44px;
  background: linear-gradient(135deg, #1e3a8a, #2563eb);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  color: #93c5fd;
  margin-bottom: 4px;
  box-shadow: 0 0 20px rgba(37,99,235,0.35);
}
.lf-icon svg { width: 22px; height: 22px; }
.lf-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px; font-weight: 600;
  color: #e2edf8; margin: 0;
  letter-spacing: 0.01em;
}
.lf-subtitle {
  font-size: 13px; font-weight: 300;
  color: #4a6fa5; margin: 0;
}

/* ─── Форма ─────────────────────────── */
.lf-form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

/* ─── Алерт ─────────────────────────── */
.lf-alert {
  display: flex; align-items: center; gap: 8px;
  padding: 11px 14px;
  background: rgba(239,68,68,0.1);
  border: 1px solid rgba(239,68,68,0.25);
  border-radius: 8px;
  font-size: 13px; color: #fca5a5;
}

/* ─── Поле ──────────────────────────── */
.lf-field { display: flex; flex-direction: column; gap: 6px; }
.lf-label {
  font-size: 11px; font-weight: 400;
  letter-spacing: 0.08em; text-transform: uppercase;
  color: #4a6fa5;
}
.lf-input-wrap {
  position: relative;
  display: flex; align-items: center;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(96,165,250,0.18);
  border-radius: 8px;
  transition: border-color 0.2s;
}
.lf-input-wrap:focus-within {
  border-color: rgba(96,165,250,0.5);
  background: rgba(255,255,255,0.05);
}
.lf-input-wrap--err {
  border-color: rgba(239,68,68,0.4) !important;
}
.lf-input-icon {
  width: 16px; height: 16px;
  color: #3d5a8a;
  flex-shrink: 0;
  margin-left: 13px;
}
.lf-input {
  flex: 1;
  padding: 11px 13px;
  background: transparent;
  border: none; outline: none;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; font-weight: 400;
  color: #dce8f5;
}
.lf-input::placeholder { color: #2a3f65; }
.lf-eye {
  background: none; border: none; cursor: pointer;
  padding: 0 12px;
  color: #3d5a8a;
  display: flex; align-items: center;
  transition: color 0.2s;
}
.lf-eye:hover { color: #93c5fd; }
.lf-err-text {
  font-size: 12px; color: #fca5a5;
  margin: 0; padding-left: 2px;
}

/* ─── Remember + Forgot ─────────────── */
.lf-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: -4px;
}
.lf-check {
  display: flex; align-items: center; gap: 8px;
  cursor: pointer;
}
.lf-check-input { display: none; }
.lf-check-box {
  width: 16px; height: 16px;
  border: 1px solid rgba(96,165,250,0.25);
  border-radius: 4px;
  background: rgba(255,255,255,0.03);
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
  transition: all 0.15s;
}
.lf-check-input:checked + .lf-check-box {
  background: #2563eb;
  border-color: #2563eb;
}
.lf-check-input:checked + .lf-check-box::after {
  content: '';
  display: block;
  width: 9px; height: 5px;
  border-left: 1.5px solid white;
  border-bottom: 1.5px solid white;
  transform: rotate(-45deg) translateY(-1px);
}
.lf-check-label { font-size: 13px; color: #4a6fa5; }
.lf-forgot {
  font-size: 13px; color: #3d5a8a;
  text-decoration: none;
  transition: color 0.2s;
}
.lf-forgot:hover { color: #93c5fd; }

/* ─── Кнопка ────────────────────────── */
.lf-btn {
  width: 100%;
  padding: 13px;
  margin-top: 4px;
  background: linear-gradient(135deg, #1d4ed8, #2563eb);
  border: none; border-radius: 8px;
  color: #e2edf8;
  font-family: 'DM Sans', sans-serif;
  font-size: 13px; font-weight: 500;
  letter-spacing: 0.07em; text-transform: uppercase;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(37,99,235,0.35);
  transition: all 0.2s;
}
.lf-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #3b82f6);
  box-shadow: 0 4px 26px rgba(59,130,246,0.45);
  transform: translateY(-1px);
}
.lf-btn:disabled {
  opacity: 0.55; cursor: not-allowed; transform: none;
}

/* ─── Спиннер ───────────────────────── */
.lf-spinner-wrap {
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.lf-spinner {
  width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,0.2);
  border-top-color: #e2edf8;
  border-radius: 50%;
  animation: lf-spin 0.7s linear infinite;
  flex-shrink: 0;
}
@keyframes lf-spin {
  to { transform: rotate(360deg); }
}

/* ─── Ссылка регистрации ────────────── */
.lf-footer {
  display: flex; align-items: center; justify-content: center;
  gap: 6px;
  margin-top: 24px;
  font-size: 13px; color: #3d5a8a;
}
.lf-link {
  color: #4a7fd4;
  text-decoration: none;
  transition: color 0.2s;
}
.lf-link:hover { color: #93c5fd; }
</style>

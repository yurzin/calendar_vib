<script setup lang="ts">

</script>

<template>
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
</template>

<style scoped>
@import '@/assets/sections.css';

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

/* ═══════════════════════════════════════
   АДАПТИВ
═══════════════════════════════════════ */
@media (max-width: 900px) {
  .gl-features { grid-template-columns: 1fr; }
  .gl-feature:first-child { border-radius: 12px 12px 0 0; }
  .gl-feature:last-child  { border-radius: 0 0 12px 12px; }
  .gl-container { padding: 0 32px; }
  .gl-section { padding: 64px 0; }

}
@media (max-width: 480px) {
  .gl-container { padding: 0 20px; }
  .gl-nav-accent span { display: none; }
}

</style>

<script setup lang="ts">

</script>

<template>
  <!-- ═══════════════════════════════════════════════════════
          HERO
     ═══════════════════════════════════════════════════════════ -->
  <section class="gl-section">
    <div class="gl-container">
      <div class="gl-hero-eyebrow">Деловой календарь · {{ new Date().getFullYear() + 1 }}</div>
      <h1 class="gl-hero-title">
        Информация —<br>это власть.<br>
        <span class="gl-hero-title-accent">Календарь —<br>это стратегия.</span>
      </h1>
      <p class="gl-hero-lead">
        Настольный справочник для деловых людей Кузбасса: дни рождения чиновников, руководителей, топ-менеджеров и праздничные даты в одном издании.
      </p>
      <div class="gl-hero-cta">
        <a href="#integration" class="gl-btn-primary">Интеграция в проект</a>
        <a href="#about"       class="gl-btn-ghost">Узнать больше</a>
      </div>
    </div>

    <div class="gl-hero-visual" aria-hidden="true">
      <img src="/calendar.svg" alt="" class="gl-calendar-img" />
    </div>

    <div class="gl-scroll-hint" aria-hidden="true"><span>↓</span></div>
  </section>
</template>

<style scoped>
/* ═══════════════════════════════════════
   HERO
═══════════════════════════════════════ */
.gl-hero-visual { position: absolute; right: 250px; top: 60%; transform: translateY(-50%); z-index: 1; animation: fade-in 1s cubic-bezier(0.16,1,0.3,1) 0.15s both; }
@keyframes fade-in { from { opacity: 0; transform: translateY(calc(-50% + 20px)); } to { opacity: 1; transform: translateY(-50%); } }
.gl-calendar-img { width: 700px; opacity: 0.75; filter: drop-shadow(0 40px 80px rgba(37,99,235,0.35)); transition: opacity 0.4s, transform 0.5s; }
.gl-scroll-hint { position: absolute; bottom: 24px; left: 64px; font-size: 16px; color: rgba(74,111,165,0.45); animation: bounce 2.2s ease-in-out infinite; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(7px); } }
.gl-hero-eyebrow { font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: #3b82f6; font-weight: 400; display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
.gl-hero-eyebrow::before { content: ''; display: inline-block; width: 28px; height: 1px; background: #3b82f6; }
.gl-hero-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(40px, 4.2vw, 66px); font-weight: 600; line-height: 1.1; color: #e2edf8; letter-spacing: -0.01em; margin: 0 0 20px; }
.gl-hero-title-accent { color: #93c5fd; font-style: italic; }
.gl-hero-lead { font-size: 18px; line-height: 1.7; color: #4a6fa5; font-weight: 300; margin: 0 0 28px; max-width: 460px; }
.gl-hero-cta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

/* ═══════════════════════════════════════
   КНОПКИ
═══════════════════════════════════════ */
.gl-btn-primary { font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #06091a; font-weight: 500; padding: 13px 28px; border-radius: 8px; background: #93c5fd; text-decoration: none; transition: background 0.2s, transform 0.15s; }
.gl-btn-primary:hover { background: #bfdbfe; transform: translateY(-1px); }
.gl-btn-ghost { font-size: 12px; letter-spacing: 0.08em; text-transform: uppercase; color: #4a6fa5; padding: 13px 24px; border-radius: 8px; border: 1px solid rgba(96,165,250,0.2); text-decoration: none; transition: all 0.2s; display: inline-block; }
.gl-btn-ghost:hover { color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

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
</style>

<script setup lang="ts">

</script>

<template>
  <section class="gl-section" id="integration">
    <div class="gl-container">
      <div class="gl-section-head">
        <div class="gl-section-line" />
        <h2 class="gl-section-title">Интеграция в проект</h2>
        <p class="gl-section-sub">Выберите формат участия</p>
      </div>
      <router-link to="/general-partner" class="gl-pkg gl-pkg--general">
        <div class="gl-pkg-left">
          <span class="gl-pkg-badge">Топ выбор</span>
          <span class="gl-pkg-name">Генеральный партнёр</span>
          <span class="gl-pkg-desc">Максимальная представленность во всех форматах издания. Логотип на обложке, в колонтитулах каждого разворота, эксклюзивная статья, приоритетное размещение.</span>
        </div>
        <div class="gl-pkg-right">
          <span class="gl-pkg-price">199 000 ₽</span>
          <span class="gl-pkg-cta">Подробнее →</span>
        </div>
      </router-link>
      <div class="gl-pkg-grid">
        <a href="https://xn--80acbojebux9agf4k.xn--p1ai/oficial-partner" class="gl-pkg gl-pkg--card">
          <span class="gl-pkg-name">Официальный партнёр</span>
          <span class="gl-pkg-desc">Представительский формат с приоритетным размещением и расширенным блоком информации.</span>
          <span class="gl-pkg-price">45 900 ₽</span>
          <span class="gl-pkg-cta">Подробнее →</span>
        </a>
        <a href="https://xn--80acbojebux9agf4k.xn--p1ai/delovoy-partner" class="gl-pkg gl-pkg--card">
          <span class="gl-pkg-name">Деловой партнёр</span>
          <span class="gl-pkg-desc">Оптимальный пакет для уверенного присутствия в календаре среди ключевых персон региона.</span>
          <span class="gl-pkg-price">25 900 ₽</span>
          <span class="gl-pkg-cta">Подробнее →</span>
        </a>
        <a href="https://xn--80acbojebux9agf4k.xn--p1ai/uchastnik-proekta" class="gl-pkg gl-pkg--card gl-pkg--member">
          <span class="gl-pkg-name">Участник проекта</span>
          <span class="gl-pkg-desc">Базовое включение в календарь. Имя, должность и контактные данные в алфавитном указателе.</span>
          <span class="gl-pkg-price">5 990 ₽</span>
          <span class="gl-pkg-cta">Подробнее →</span>
        </a>
      </div>
    </div>
  </section>
</template>

<style scoped>
@import '@/assets/sections.css';

/* ═══════════════════════════════════════
   ПАКЕТЫ
═══════════════════════════════════════ */
.gl-pkg--general { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 48px; padding: 44px 52px; border-radius: 16px; border: 1px solid rgba(147,197,253,0.28); background: linear-gradient(135deg, rgba(13,21,48,0.9) 0%, rgba(17,27,62,0.85) 100%); text-decoration: none; margin-bottom: 16px; transition: border-color 0.22s, transform 0.18s; position: relative; overflow: hidden; }
.gl-pkg--general::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(59,130,246,0.07), transparent 55%); opacity: 0; transition: opacity 0.22s; }
.gl-pkg--general:hover { border-color: rgba(147,197,253,0.5); transform: translateY(-2px); }
.gl-pkg--general:hover::before { opacity: 1; }
.gl-pkg--general:hover .gl-pkg-cta { color: #93c5fd; }
.gl-pkg-left  { display: flex; flex-direction: column; gap: 12px; }
.gl-pkg-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; flex-shrink: 0; }
.gl-pkg-badge { font-size: 9px; letter-spacing: 0.14em; text-transform: uppercase; color: #93c5fd; background: rgba(147,197,253,0.12); padding: 4px 12px; border-radius: 20px; display: inline-block; }
.gl-pkg-name { font-family: 'Cormorant Garamond', serif; font-size: 36px; font-weight: 600; color: #e2edf8; line-height: 1.1; letter-spacing: -0.01em; }
.gl-pkg-desc { font-size: 18px; line-height: 1.65; color: #3d5a8a; font-weight: 300; max-width: 520px; }
.gl-pkg-price { font-family: 'Cormorant Garamond', serif; font-size: 52px; font-weight: 700; color: #93c5fd; letter-spacing: -0.03em; line-height: 1; }
.gl-pkg-cta { font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(147,197,253,0.4); transition: color 0.2s; }
.gl-pkg-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.gl-pkg--card { display: flex; flex-direction: column; gap: 14px; padding: 32px 28px; border-radius: 14px; border: 1px solid rgba(96,165,250,0.13); background: rgba(13,21,48,0.5); text-decoration: none; transition: border-color 0.22s, background 0.22s, transform 0.18s; position: relative; overflow: hidden; }
.gl-pkg--card::before { content: ''; position: absolute; inset: 0; background: linear-gradient(160deg, rgba(59,130,246,0.05), transparent 55%); opacity: 0; transition: opacity 0.22s; }
.gl-pkg--card:hover { border-color: rgba(96,165,250,0.3); background: rgba(13,21,48,0.85); transform: translateY(-3px); }
.gl-pkg--card:hover::before { opacity: 1; }
.gl-pkg--card:hover .gl-pkg-cta { color: #93c5fd; }
.gl-pkg--card .gl-pkg-name { font-size: 24px; font-weight: 600; }
.gl-pkg--card .gl-pkg-desc { font-size: 12px; flex: 1; }
.gl-pkg--card .gl-pkg-price { font-size: 40px; margin-top: 8px; }
.gl-pkg--card .gl-pkg-cta { font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: rgba(147,197,253,0.35); transition: color 0.2s; }
.gl-pkg--member { border-color: rgba(96,165,250,0.08); background: rgba(10,14,32,0.35); }
.gl-pkg--member .gl-pkg-price { color: #6b9fd4; }

</style>

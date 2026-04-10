<script setup lang="ts">

</script>

<template>
  <section class="gl-section gl-section--alt">
    <div class="gl-container">
      <div class="gl-two-col">
        <div class="gl-two-col-block">
          <div class="gl-section-line" />
          <!-- 🆕 Иконка перед заголовком -->
          <div class="gl-block-icon-row">
            <svg viewBox="0 0 24 24" fill="none" width="22" height="22" class="gl-block-icon">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" stroke="#3b82f6" stroke-width="1.2"/>
              <path d="M8 12l3 3 5-6" stroke="#93c5fd" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2 class="gl-section-title">Полезно</h2>
          </div>
          <h3 class="gl-block-title">Всё необходимое для деловых связей</h3>
          <div class="gl-text-stack">
            <p>«Власть и бизнес» — это настольный календарь-справочник, в котором собраны дни рождения высших чиновников, депутатов, руководителей государственных служб и ведомств, организаций, ведущих бизнесменов и топ-менеджеров Кузбасского региона, а также государственные, профессиональные праздники и памятные даты.</p>
            <p>Наш календарь — это рабочий инструмент для того, чтобы налаживать или укреплять добрые человеческие отношения между руководителями и топ-менеджерами региона и бизнеса.</p>
            <p>Это эффективный PR-инструмент для повышения узнаваемости любого публичного политика, руководителя, топ-менеджера высокого ранга или компании. Наконец, это оригинальный и полезный бизнес-подарок.</p>
          </div>
        </div>
        <div class="gl-two-col-block">
          <div class="gl-section-line" />
          <!-- 🆕 Иконка — стопка экземпляров -->
          <div class="gl-block-icon-row">
            <svg viewBox="0 0 24 24" fill="none" width="22" height="22" class="gl-block-icon">
              <rect x="3" y="13" width="18" height="7" rx="2" stroke="#3b82f6" stroke-width="1.2"/>
              <rect x="5" y="9" width="14" height="5" rx="2" stroke="#3b82f6" stroke-width="1" opacity="0.7"/>
              <rect x="7" y="5" width="10" height="5" rx="2" stroke="#3b82f6" stroke-width="0.8" opacity="0.45"/>
              <text x="12" y="18" text-anchor="middle" font-family="DM Sans,sans-serif" font-size="5" font-weight="600" fill="#93c5fd">3000</text>
            </svg>
            <h2 class="gl-section-title">Действенно</h2>
          </div>
          <h3 class="gl-block-title">Тиражи и каналы распространения</h3>
          <div class="gl-text-stack">
            <p>Тираж календаря на {{ new Date().getFullYear() + 1 }} год составит <strong>3 000 экземпляров</strong>. В качестве подарков календари получают все «герои» календаря, а также рекламодатели (не менее 20 шт.) и партнёры проекта.</p>
            <p>С помощью Курьерской службы «Всё про Всё» календари доставляются в администрацию области, муниципалитеты, по офисам компаний и организаций. Многие компании и ведомства используют календари в качестве своих бизнес-подарков на Новый год.</p>
            <p>В результате мы уверены, что такое распространение гарантирует присутствие календаря в подавляющем большинстве кабинетов и приёмных представителей региональной власти и бизнеса.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
@import '@/assets/sections.css';

</style>

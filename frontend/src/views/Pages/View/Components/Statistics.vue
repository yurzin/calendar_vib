<script setup lang="ts">
const emit = defineEmits<{
  (e: 'open-modal'): void
}>()
</script>

<template>
  <!-- ═══════════════════════════════════════════════════════
               СТАТИСТИКА — разделитель
          ═══════════════════════════════════════════════════════════ -->
  <div class="gl-stats-bar">
    <div class="gl-stats-inner">
      <div class="gl-stat" @click="emit('open-modal')">
        <span class="gl-stat-num">3 000</span>
        <span class="gl-stat-label">экземпляров тираж</span>
      </div>
      <div class="gl-stat-divider" />
      <div class="gl-stat">
        <span class="gl-stat-num">300+</span>
        <span class="gl-stat-label">участников ежегодно</span>
      </div>
      <div class="gl-stat-divider" />
      <div class="gl-stat">
        <span class="gl-stat-num">7+</span>
        <span class="gl-stat-label">лет издаётся</span>
      </div>
      <div class="gl-stat-divider" />
      <div class="gl-stat gl-stat--cta" @click="emit('open-modal')">
        <span class="gl-stat-cta-text">Хочу в календарь</span>
        <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
          <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ═══════════════════════════════════════
   СТАТИСТИКА — разделитель
═══════════════════════════════════════ */
.gl-stats-bar {
  position: relative; z-index: 9;
  border-top: 1px solid rgba(96,165,250,0.08);
  border-bottom: 1px solid rgba(96,165,250,0.08);
  background: rgba(10,14,32,0.6);
  backdrop-filter: blur(8px);
  overflow: hidden;
}
.gl-stats-bar::before {
  content: '';
  position: absolute;
  top: 0; left: -100%;
  width: 60%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(147,197,253,0.04), transparent);
  animation: stats-sweep 4s ease-in-out infinite;
}
@keyframes stats-sweep {
  0%   { left: -60%; }
  100% { left: 140%; }
}
.gl-stats-inner {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 64px;
  display: flex;
  align-items: stretch;
}
.gl-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 4px;
  padding: 28px 48px;
  cursor: default;
  transition: background 0.2s;
  position: relative;
}
.gl-stat::after {
  content: '';
  position: absolute;
  bottom: 0; left: 48px; right: 48px;
  height: 1px;
  background: #3b82f6;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
}
.gl-stat:hover { background: rgba(96,165,250,0.03); }
.gl-stat:hover::after { transform: scaleX(1); }
.gl-stat-num {
  font-family: 'Cormorant Garamond', serif;
  font-size: 36px; font-weight: 700;
  color: #93c5fd; letter-spacing: -0.02em; line-height: 1;
  animation: count-in 0.8s cubic-bezier(0.16,1,0.3,1) both;
}
.gl-stat:nth-child(1) .gl-stat-num { animation-delay: 0.1s; }
.gl-stat:nth-child(3) .gl-stat-num { animation-delay: 0.2s; }
.gl-stat:nth-child(5) .gl-stat-num { animation-delay: 0.3s; }
@keyframes count-in {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.gl-stat-label {
  font-size: 9px; font-weight: 300;
  color: #2e4a73; letter-spacing: 0.14em; text-transform: uppercase;
}
.gl-stat-divider {
  width: 1px;
  background: rgba(96,165,250,0.1);
  margin: 16px 0;
  flex-shrink: 0;
}
.gl-stat--cta {
  cursor: pointer;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  margin-left: auto;
  padding: 28px 0 28px 48px;
}
.gl-stat--cta:hover { background: none; }
.gl-stat--cta::after { display: none; }
.gl-stat-cta-text {
  font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase;
  color: #3b82f6; font-weight: 400;
  transition: color 0.2s;
}
.gl-stat--cta svg {
  color: #3b82f6;
  transition: transform 0.2s, color 0.2s;
  flex-shrink: 0;
}
.gl-stat--cta:hover .gl-stat-cta-text { color: #93c5fd; }
.gl-stat--cta:hover svg { transform: translateX(4px); color: #93c5fd; }

@media (max-width: 900px) {
  .gl-stats-inner { padding: 0 32px; }
  .gl-stat { padding: 20px 24px; }
  .gl-stat::after { left: 24px; right: 24px; }
  .gl-stat-num { font-size: 28px; }
  .gl-stat--cta { padding: 20px 0 20px 24px; }
}
@media (max-width: 600px) {
  .gl-stats-inner { flex-wrap: wrap; }
  .gl-stat-divider { display: none; }
  .gl-stat { padding: 16px 20px; flex: 1; min-width: 120px; }
  .gl-stat--cta { width: 100%; margin-left: 0; border-top: 1px solid rgba(96,165,250,0.08); justify-content: center; }
}
</style>

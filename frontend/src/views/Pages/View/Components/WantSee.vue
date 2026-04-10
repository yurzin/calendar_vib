<script setup lang="ts">
const emit = defineEmits<{
  (e: 'open-modal'): void
}>()
</script>

<template>
  <!-- ─── Превью-блок (fixed) — клик открывает модалку ── -->
  <div class="gl-card-abs"  @click="emit('open-modal')" role="button" tabindex="0" @keydown.enter="emit('open-modal')" >
    <div class="gl-card-abs-glow" aria-hidden="true" />
    <div class="gl-card-abs-content">
      <div class="gl-card-abs-eyebrow">Деловой календарь · {{ new Date().getFullYear() + 1 }}</div>
      <p class="gl-card-abs-title">Хочу увидеть<br>себя в календаре</p>
      <p class="gl-card-abs-sub">Оставьте заявку — мы свяжемся и подберём формат участия</p>
      <div class="gl-card-abs-cta">
        <span class="gl-card-abs-btn">Оставить заявку</span>
        <svg viewBox="0 0 16 16" fill="none" width="14" height="14" class="gl-card-abs-arrow">
          <path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* ═══════════════════════════════════════
   ПРЕВЬЮ-БЛОК (fixed, кликабельный)
═══════════════════════════════════════ */
.gl-card-abs {
  position: fixed;
  right: 32px;
  bottom: 32px;
  z-index: 50;
  width: 250px;
  background: linear-gradient(150deg, #0d1530, #09101f);
  border: 1px solid rgba(96,165,250,0.2);
  border-radius: 16px;
  padding: 24px 28px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 24px 64px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
  overflow: hidden;
  cursor: pointer;
  animation: card-in 0.7s cubic-bezier(0.16,1,0.3,1) 0.5s both;
  transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
  user-select: none;
}
@keyframes card-in {
  from { opacity: 0; transform: translateY(32px); }
  to   { opacity: 1; transform: translateY(0); }
}
.gl-card-abs:hover {
  border-color: rgba(147,197,253,0.4);
  transform: translateY(-3px);
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.04),
    0 32px 80px rgba(0,0,0,0.8),
    inset 0 1px 0 rgba(147,197,253,0.12);
}
.gl-card-abs:active { transform: translateY(-1px); }

.gl-card-abs-glow {
  position: absolute;
  top: -50px; left: 50%; transform: translateX(-50%);
  width: 200px; height: 120px;
  background: radial-gradient(ellipse, rgba(59,130,246,0.15), transparent 70%);
  pointer-events: none;
}
.gl-card-abs-content {
  position: relative; z-index: 1;
  display: flex; flex-direction: column; gap: 10px;
}
.gl-card-abs-eyebrow {
  font-size: 9px; letter-spacing: 0.16em; text-transform: uppercase;
  color: #3b82f6; font-weight: 400;
  display: flex; align-items: center; gap: 8px;
}
.gl-card-abs-eyebrow::before {
  content: ''; display: inline-block;
  width: 20px; height: 1px; background: #3b82f6; flex-shrink: 0;
}
.gl-card-abs-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 26px; font-weight: 600; line-height: 1.15;
  color: #e2edf8; margin: 0; letter-spacing: -0.01em;
}
.gl-card-abs-sub {
  font-size: 11px; line-height: 1.6;
  color: #3d5a8a; font-weight: 300; margin: 0;
}
.gl-card-abs-cta {
  display: flex; align-items: center; gap: 8px;
  margin-top: 4px;
}
.gl-card-abs-btn {
  font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase;
  color: #93c5fd; font-weight: 500;
}
.gl-card-abs-arrow {
  color: #93c5fd;
  transition: transform 0.2s;
}
.gl-card-abs:hover .gl-card-abs-arrow { transform: translateX(4px); }

</style>

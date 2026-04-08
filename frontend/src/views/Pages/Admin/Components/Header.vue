<script setup lang="ts">
import { ref } from 'vue';

const emit = defineEmits<{
  (e: 'click'): void
}>();

const mobileOpen = ref(false);

</script>

<template>
  <header class="gl-header">
    <router-link to="/calendar" class="gl-brand">
      <div class="gl-brand-icon">
        <img src="/logo.svg" alt="" class="gl-logo">
      </div>
      <div class="gl-brand-texts">
        <span class="gl-brand-name">Власть и Бизнес</span>
        <span class="gl-brand-tagline">Деловой календарь</span>
      </div>
    </router-link>

    <nav class="gl-nav">
      <div class="gl-nav-menu">
      </div>
      <div class="gl-nav-divider"/>
      <router-link to="/calendar" class="gl-nav-accent">Сайт</router-link>
    </nav>

    <button class="gl-burger"
            @click="emit('click')"
            :class="{ 'is-open': mobileOpen }"
            aria-label="Меню">
      <span/><span/><span/>
    </button>
  </header>
</template>

<style scoped>

.gl-header {
  position: sticky;
  top: 0;
  z-index: 10;
  display: flex;
  justify-content: space-between;
  align-items: stretch;
  border-bottom: 1px solid rgba(96, 165, 250, 0.1);
  background: rgba(6, 9, 26, 0.82);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
}

.gl-brand {
  display: flex;
  align-items: center;
  gap: 14px;
  text-decoration: none;
  color: #dce8f5;
  padding: 14px 32px 14px 40px;
  flex-shrink: 0;
  transition: opacity 0.2s;
}

.gl-brand:hover {
  opacity: 0.8;
}

.gl-brand-icon {
  width: 75px;
  height: 85px;
  border-radius: 10px;
  background: rgba(37, 99, 235, 0.15);
  /*border: 1px solid rgba(96,165,250,0.2);*/
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  overflow: hidden;
}

.gl-logo {
  width: 66px;
  height: 66px;
  object-fit: contain;
}

.gl-brand-texts {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.gl-brand-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px;
  font-weight: 600;
  color: #e2edf8;
  letter-spacing: 0.01em;
  line-height: 1;
}

.gl-brand-tagline {
  font-size: 9px;
  font-weight: 300;
  color: #3b82f6;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 7px;
}

.gl-brand-tagline::before {
  content: '';
  display: inline-block;
  width: 16px;
  height: 1px;
  background: #3b82f6;
  flex-shrink: 0;
}

.gl-nav {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 24px;
  flex: 1;
  justify-content: flex-end;
}

.gl-nav-menu {
  display: flex;
  align-items: center;
  gap: 2px;
  margin-right: 4px;
}

.gl-nav-item {
  font-size: 11px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: #4a6fa5;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid transparent;
  text-decoration: none;
  white-space: nowrap;
  transition: color 0.2s, background 0.2s;
  position: relative;
}

.gl-nav-item::after {
  content: '';
  position: absolute;
  bottom: 5px;
  left: 12px;
  right: 12px;
  height: 1px;
  background: #3b82f6;
  transform: scaleX(0);
  transform-origin: left;
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.gl-nav-item:hover {
  color: #a8c4e8;
  background: rgba(96, 165, 250, 0.06);
}

.gl-nav-item:hover::after, .gl-nav-item.router-link-active::after {
  transform: scaleX(1);
}

.gl-nav-item.router-link-active {
  color: #93c5fd;
}

.gl-nav-divider {
  width: 1px;
  height: 20px;
  background: rgba(96, 165, 250, 0.15);
  margin: 0 4px;
  flex-shrink: 0;
}

.gl-nav-plain {
  font-size: 12px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #3d5a8a;
  padding: 8px 14px;
  border-radius: 8px;
  border: 1px solid transparent;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s;
}

.gl-nav-plain:hover {
  color: #a8c4e8;
  background: rgba(96, 165, 250, 0.08);
  border-color: rgba(96, 165, 250, 0.15);
}

.gl-nav-accent {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #93c5fd;
  padding: 8px 16px;
  border-radius: 8px;
  border: 1px solid rgba(147, 197, 253, 0.25);
  background: rgba(147, 197, 253, 0.07);
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s;
}

.gl-nav-accent:hover {
  color: #06091a;
  background: #93c5fd;
  border-color: #93c5fd;
}

.gl-nav-accent svg {
  transition: transform 0.2s;
}

.gl-nav-accent:hover svg {
  transform: translateX(3px);
}

.gl-burger {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0 20px;
  border-radius: 0;
  transition: background 0.2s;
  border-left: 1px solid rgba(96, 165, 250, 0.1);
}

.gl-burger:hover {
  background: rgba(96, 165, 250, 0.05);
}

.gl-burger span {
  display: block;
  width: 22px;
  height: 1.5px;
  background: #4a6fa5;
  border-radius: 2px;
  transition: transform 0.25s, opacity 0.25s;
}

.gl-burger.is-open span:nth-child(1) {
  transform: translateY(6.5px) rotate(45deg);
}

.gl-burger.is-open span:nth-child(2) {
  opacity: 0;
}

.gl-burger.is-open span:nth-child(3) {
  transform: translateY(-6.5px) rotate(-45deg);
}

.gl-mobile-nav {
  display: none;
  position: sticky;
  top: 72px;
  z-index: 12;
  flex-direction: column;
  background: rgba(6, 9, 26, 0.97);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(96, 165, 250, 0.12);
  padding: 0 24px;
  gap: 2px;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.35s cubic-bezier(0.4, 0, 0.2, 1), padding 0.35s;
}

.gl-mobile-nav.is-open {
  max-height: 480px;
  padding: 12px 24px 20px;
}

.gl-mobile-item {
  font-size: 12px;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #4a6fa5;
  padding: 11px 12px;
  border-radius: 8px;
  text-decoration: none;
  transition: color 0.2s, background 0.2s;
}

.gl-mobile-item:hover {
  color: #a8c4e8;
  background: rgba(96, 165, 250, 0.07);
}

.gl-mobile-item.router-link-active {
  color: #93c5fd;
}

.gl-mobile-divider {
  height: 1px;
  background: rgba(96, 165, 250, 0.1);
  margin: 8px 0;
}

.gl-mobile-item--plain {
  color: #3d5a8a;
}

.gl-mobile-item--accent {
  color: #93c5fd;
  border: 1px solid rgba(147, 197, 253, 0.2);
  background: rgba(147, 197, 253, 0.05);
  margin-top: 4px;
}

</style>

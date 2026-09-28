<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

// Кнопка появляется, когда страница прокручена ниже первого экрана
const visible = ref(false)

const onScroll = () => {
  visible.value = window.scrollY > window.innerHeight * 0.6
}

const scrollTop = () => {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  onScroll()
  window.addEventListener('scroll', onScroll, { passive: true })
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
})
</script>

<template>
  <Transition name="st-fade">
    <button v-if="visible" type="button" class="st-btn" aria-label="Наверх" title="Наверх" @click="scrollTop">
      <svg viewBox="0 0 24 24" fill="none" width="20" height="20">
        <path d="M12 19V5M5 12l7-7 7 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </button>
  </Transition>
</template>

<style scoped>
.st-btn {
  position: fixed;
  right: 24px;
  bottom: 24px;
  z-index: 50;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #93c5fd;
  background: rgba(13, 21, 48, 0.85);
  border: 1px solid rgba(96, 165, 250, 0.3);
  backdrop-filter: blur(8px);
  cursor: pointer;
  transition: border-color 0.2s, background 0.2s, color 0.2s, transform 0.2s;
}

.st-btn:hover {
  border-color: rgba(147, 197, 253, 0.6);
  background: rgba(13, 21, 48, 1);
  color: #bfdbfe;
  transform: translateY(-2px);
}

.st-fade-enter-active, .st-fade-leave-active { transition: opacity 0.25s, transform 0.25s; }
.st-fade-enter-from, .st-fade-leave-to { opacity: 0; transform: translateY(8px); }

@media (max-width: 480px) {
  .st-btn { right: 16px; bottom: 16px; width: 44px; height: 44px; }
}
</style>

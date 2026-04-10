<script setup lang="ts">
import { useLightbox } from '@/composable/useLightbox'
const { galleryItems, lightboxIndex, closeLightbox, prevPhoto, nextPhoto } = useLightbox()

</script>

<template>
  <Transition name="lb">
    <div v-if="lightboxIndex !== null" class="gl-lightbox" @click.self="closeLightbox">
      <button class="gl-lb-close" @click="closeLightbox" aria-label="Закрыть">✕</button>
      <button class="gl-lb-prev" @click="prevPhoto" aria-label="Назад">‹</button>
      <button class="gl-lb-next" @click="nextPhoto" aria-label="Вперёд">›</button>
      <div class="gl-lb-inner">
        <img :src="galleryItems[lightboxIndex].src" :alt="galleryItems[lightboxIndex].label" class="gl-lb-img" />
        <p class="gl-lb-caption">{{ galleryItems[lightboxIndex].label }}</p>
      </div>
      <div class="gl-lb-dots">
        <button v-for="(_, i) in galleryItems" :key="i" class="gl-lb-dot" :class="{ 'is-active': i === lightboxIndex }" @click="lightboxIndex = i" />
      </div>
    </div>
  </Transition>
</template>

<style scoped>
@import '@/assets/sections.css';

.gl-lightbox { position: fixed; inset: 0; z-index: 100; background: rgba(3,5,16,0.96); backdrop-filter: blur(12px); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
.gl-lb-inner { display: flex; flex-direction: column; align-items: center; gap: 16px; max-width: 900px; width: 100%; }
.gl-lb-img { max-width: 100%; max-height: 70vh; object-fit: contain; border-radius: 10px; box-shadow: 0 40px 100px rgba(0,0,0,0.7); animation: lb-zoom 0.3s cubic-bezier(0.16,1,0.3,1) both; }
@keyframes lb-zoom { from { opacity: 0; transform: scale(0.96); } to { opacity: 1; transform: scale(1); } }
.gl-lb-caption { font-size: 12px; letter-spacing: 0.1em; text-transform: uppercase; color: #3d5a8a; }
.gl-lb-close { position: absolute; top: 24px; right: 32px; background: rgba(13,21,48,0.7); border: 1px solid rgba(96,165,250,0.15); color: #4a6fa5; font-size: 16px; width: 40px; height: 40px; border-radius: 10px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
.gl-lb-close:hover { color: #dce8f5; border-color: rgba(96,165,250,0.35); background: rgba(13,21,48,0.95); }
.gl-lb-prev, .gl-lb-next { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(13,21,48,0.7); border: 1px solid rgba(96,165,250,0.15); color: #4a6fa5; font-size: 28px; width: 48px; height: 48px; border-radius: 12px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; line-height: 1; }
.gl-lb-prev { left: 20px; }
.gl-lb-next { right: 20px; }
.gl-lb-prev:hover, .gl-lb-next:hover { color: #dce8f5; border-color: rgba(96,165,250,0.35); background: rgba(13,21,48,0.95); }
.gl-lb-dots { display: flex; gap: 8px; margin-top: 8px; }
.gl-lb-dot { width: 6px; height: 6px; border-radius: 50%; border: none; background: rgba(74,111,165,0.3); cursor: pointer; transition: background 0.2s, transform 0.2s; padding: 0; }
.gl-lb-dot.is-active { background: #93c5fd; transform: scale(1.3); }

</style>

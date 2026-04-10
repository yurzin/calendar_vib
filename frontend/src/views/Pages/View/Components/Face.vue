<script setup lang="ts">

// Галерея
import { useLightbox } from '@/composable/useLightbox'
const { galleryItems, openLightbox } = useLightbox()

</script>

<template>
  <section class="gl-section gl-section--dark" id="gallery">
    <div class="gl-container">
      <div class="gl-section-head">
        <div class="gl-section-line" />
        <h2 class="gl-section-title">Внешний вид</h2>
        <p class="gl-section-sub">Как выглядит готовый календарь</p>
      </div>
      <div class="gl-gallery">
        <button
          v-for="(item, i) in galleryItems" :key="i"
          class="gl-gallery-item" :class="`gl-gallery-item--${item.size}`"
          @click="openLightbox(i)"
        >
          <div class="gl-gallery-img-wrap">
            <img :src="item.src" :alt="item.label" class="gl-gallery-img" />
            <div class="gl-gallery-overlay">
              <span class="gl-gallery-zoom">⤢</span>
              <span class="gl-gallery-label">{{ item.label }}</span>
            </div>
          </div>
        </button>
      </div>
    </div>
  </section>
</template>

<style scoped>
@import '@/assets/sections.css';

.gl-gallery { display: grid; grid-template-columns: repeat(4, 1fr); grid-template-rows: auto auto; gap: 10px; }
.gl-gallery-item { background: none; border: none; padding: 0; cursor: pointer; border-radius: 10px; overflow: hidden; position: relative; }
.gl-gallery-img-wrap { position: relative; width: 100%; height: 100%; min-height: 180px; overflow: hidden; border-radius: 10px; border: 1px solid rgba(96,165,250,0.1); background: rgba(13,21,48,0.6); }
.gl-gallery-img { width: 100%; height: 100%; transform: scale(0.8); object-fit: contain; display: block; transition: transform 0.4s cubic-bezier(0.4,0,0.2,1), filter 0.3s; filter: brightness(0.85) saturate(0.8); }
.gl-gallery-item:hover .gl-gallery-img { transform: scale(0.90); filter: brightness(1) saturate(1); }
.gl-gallery-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(6,9,26,0.75) 0%, transparent 50%); display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start; padding: 16px; opacity: 0; transition: opacity 0.25s; }
.gl-gallery-item:hover .gl-gallery-overlay { opacity: 1; }
.gl-gallery-zoom { font-size: 22px; color: #93c5fd; position: absolute; top: 12px; right: 14px; }
.gl-gallery-label { font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: #a8c4e8; font-weight: 300; }

</style>

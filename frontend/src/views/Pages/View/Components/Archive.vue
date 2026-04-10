<script setup lang="ts">
// Архив
const archiveIssues = [
  { year: 2025, cover: '/calendar/cover/2025.jpg', members: 312, pdfUrl: 'https://drive.google.com/file/d/1yq9CQw8OS1naDWqpQsgCAEH9-AAiYyOF/view', pageUrl: '/archive/2025' },
  { year: 2024, cover: '/calendar/cover/2024.jpg', members: 298, pdfUrl: 'https://drive.google.com/file/d/1D4rDBCU0MDG9qA8T6Vk4xF4rBpWI6ItR/view', pageUrl: '/archive/2024' },
  { year: 2023, cover: '/calendar/cover/2023.jpg', members: 276, pdfUrl: 'https://drive.google.com/file/d/1RkW-WLvkOZ_Gpq2wmyQLxQaKntUVyH4T/view', pageUrl: '/archive/2023' },
  { year: 2022, cover: '/calendar/cover/2022.jpg', members: 251, pdfUrl: 'https://drive.google.com/file/d/1n4G2Ffs__fJh09cqIUYg0kkbBKv-BIs9/view', pageUrl: '/archive/2022' },
  { year: 2021, cover: '/calendar/cover/2021.jpg', members: 234, pdfUrl: 'https://drive.google.com/file/d/185vPOVFO3byG2ZjI77q7VVAv-6UM5dIw/view', pageUrl: '/archive/2021' },
  { year: 2020, cover: '/calendar/cover/2020.jpg', members: 218, pdfUrl: 'https://drive.google.com/file/d/1D3t1uVi-Cyn3-1rPrPhXjWVXrDlOhdef/view', pageUrl: '/archive/2020' },
]
</script>

<template>
  <section class="gl-section gl-section--dark" id="archive">
    <div class="gl-container">
      <div class="gl-section-head">
        <div class="gl-section-line" />
        <h2 class="gl-section-title">Архив номеров</h2>
        <p class="gl-section-sub">Все выпуски делового календаря</p>
      </div>
      <div class="gl-archive-grid">
        <div v-for="issue in archiveIssues" :key="issue.year" class="gl-archive-card">
          <div class="gl-archive-cover">
            <img v-if="issue.cover" :src="issue.cover" :alt="`Обложка ${issue.year}`" class="gl-archive-cover-img" />
            <div v-else class="gl-archive-cover-placeholder">
              <span class="gl-archive-cover-year-bg">{{ issue.year }}</span>
              <div class="gl-archive-cover-logo">В&amp;Б</div>
            </div>
            <div class="gl-archive-cover-shine" aria-hidden="true" />
          </div>
          <div class="gl-archive-info">
            <div class="gl-archive-meta">
              <span v-if="issue.members" class="gl-archive-members">{{ issue.members }} участников</span>
            </div>
            <span class="gl-archive-title">Власть и Бизнес · {{ issue.year }}</span>
          </div>
          <div class="gl-archive-actions">
            <a :href="issue.pdfUrl || '#'" class="gl-archive-btn gl-archive-btn--primary" target="_blank">
              <svg viewBox="0 0 16 16" fill="none" width="13" height="13">
                <path d="M8 3v10M3 8l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Скачать PDF
            </a>
            <a :href="issue.pageUrl || `/archive/${issue.year}`" class="gl-archive-btn gl-archive-btn--ghost">Смотреть →</a>
          </div>
        </div>
      </div>
      <div class="gl-members-cta" style="margin-top: 48px;">
        <router-link to="#archive" class="gl-btn-ghost">Весь архив →</router-link>
      </div>
    </div>
  </section>
</template>

<style scoped>
@import '@/assets/sections.css';

.gl-archive-cover {
  position: relative;
  width: 100%;
  aspect-ratio: 16/9;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.gl-archive-cover-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.4s cubic-bezier(0.4,0,0.2,1); }
.gl-archive-card:hover .gl-archive-cover-img { transform: scale(1.03); }
.gl-archive-cover-placeholder { width: 100%; height: 100%; background: linear-gradient(145deg, #0d1836, #091228); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; border: 1px solid rgba(96,165,250,0.1); border-radius: 8px; position: relative; overflow: hidden; }
.gl-archive-cover-placeholder::before { content: ''; position: absolute; inset: 0; background: linear-gradient(rgba(96,165,250,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.03) 1px, transparent 1px); background-size: 20px 20px; }
.gl-archive-cover-year-bg { font-family: 'Cormorant Garamond', serif; font-size: 52px; font-weight: 700; color: rgba(147,197,253,0.07); line-height: 1; position: absolute; bottom: 8px; right: 8px; letter-spacing: -0.04em; }

.gl-archive-cover-logo {
  font-family: 'Cormorant Garamond', serif;
  font-size: 18px;
  font-weight: 700;
  color: #93c5fd;
  letter-spacing: 0.08em;
  z-index: 1;
  padding: 6px 12px;
  border: 1px solid rgba(147, 197, 253, 0.2);
  border-radius: 6px;
  background: rgba(147, 197, 253, 0.06);
}

.gl-archive-cover-shine {
  position: absolute;
  top: 0;
  left: -40%;
  width: 30%;
  height: 100%;
  background: linear-gradient(105deg, transparent, rgba(255, 255, 255, 0.04), transparent);
  transition: left 0.5s ease;
  pointer-events: none;
}

.gl-archive-card:hover .gl-archive-cover-shine { left: 130%; }
.gl-archive-info { display: flex; flex-direction: column; gap: 4px; }
.gl-archive-meta { display: flex; align-items: center; justify-content: space-between; gap: 6px; }
.gl-archive-members { font-size: 10px; font-weight: 300; color: #2e4a73; letter-spacing: 0.08em; text-transform: uppercase; white-space: nowrap; }
.gl-archive-title { font-size: 11px; font-weight: 300; color: #3d5a8a; letter-spacing: 0.06em; text-transform: uppercase; }
.gl-archive-actions { display: flex; flex-direction: column; gap: 6px; margin-top: auto; }
.gl-archive-btn { z-index: 19; display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 11px; letter-spacing: 0.07em; text-transform: uppercase; padding: 9px 14px; border-radius: 8px; text-decoration: none; transition: all 0.2s; font-weight: 400; }
.gl-archive-btn--primary { background: rgba(147,197,253,0.1); border: 1px solid rgba(147,197,253,0.2); color: #93c5fd; }
.gl-archive-btn--primary:hover { background: #93c5fd; border-color: #93c5fd; color: #06091a; }
.gl-archive-btn--ghost { background: transparent; border: 1px solid rgba(96,165,250,0.12); color: #3d5a8a; }
.gl-archive-btn--ghost:hover { border-color: rgba(96,165,250,0.28); color: #93c5fd; background: rgba(96,165,250,0.05); }

.gl-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #4a6fa5;
  padding: 13px 24px;
  border-radius: 8px;
  border: 1px solid rgba(96, 165, 250, 0.2);
  text-decoration: none;
  transition: all 0.2s;
  background: transparent;
}

.gl-btn-ghost:hover { cursor: pointer; color: #93c5fd; border-color: rgba(147,197,253,0.4); background: rgba(147,197,253,0.05); }

</style>

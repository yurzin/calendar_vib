<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Header from '@/views/Pages/View/Components/Header.vue'
import Footer from '@/views/Pages/View/Components/Footer.vue'

// Текст положения лежит в public/consent-personal-data.txt — правится без пересборки
type Block = { type: 'title' | 'heading' | 'text'; text: string }

const blocks = ref<Block[]>([])
const loading = ref(true)
const error = ref(false)

// Заголовок раздела: «1. ОБЩИЕ ПОЛОЖЕНИЯ» — номер и текст заглавными буквами
const isHeading = (line: string) => /^\d+\.\s/.test(line) && line === line.toUpperCase()

function parse(raw: string): Block[] {
  const lines = raw.replace(/\r/g, '').split('\n').map(l => l.trim())
  const firstEmpty = lines.indexOf('')
  // Всё до первой пустой строки — название документа
  const title = lines.slice(0, firstEmpty).join(' ')
  const body = lines.slice(firstEmpty).filter(Boolean)

  return [
    { type: 'title', text: title },
    ...body.map(text => ({ type: isHeading(text) ? 'heading' : 'text', text }) as Block),
  ]
}

onMounted(async () => {
  try {
    const res = await fetch('/consent-personal-data.txt')
    if (!res.ok) throw new Error(String(res.status))
    blocks.value = parse(await res.text())
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="gl-wrap">
    <div class="gl-bg" aria-hidden="true">
      <div class="gl-orb gl-orb1" />
      <div class="gl-orb gl-orb2" />
      <div class="gl-grid" />
    </div>

    <Header />

    <main class="cp-main">
      <article class="cp-doc">
        <p v-if="loading" class="cp-note">Загрузка…</p>
        <p v-else-if="error" class="cp-note">Не удалось загрузить документ. Попробуйте обновить страницу.</p>
        <template v-else>
          <template v-for="(b, i) in blocks" :key="i">
            <h1 v-if="b.type === 'title'" class="cp-title">{{ b.text }}</h1>
            <h2 v-else-if="b.type === 'heading'" class="cp-heading">{{ b.text }}</h2>
            <p v-else class="cp-text">{{ b.text }}</p>
          </template>
        </template>
      </article>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; }

.gl-wrap {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #06091a;
  color: #dce8f5;
  display: flex;
  flex-direction: column;
  position: relative;
}

.gl-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.gl-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
.gl-orb1 { width: 700px; height: 700px; top: -300px; left: -200px; background: radial-gradient(circle, rgba(37,99,235,0.28), transparent 65%); }
.gl-orb2 { width: 500px; height: 500px; top: 30%; right: -150px; background: radial-gradient(circle, rgba(29,78,216,0.2), transparent 65%); }
.gl-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px); background-size: 56px 56px; }

.cp-main { position: relative; z-index: 1; flex: 1; padding: 48px 24px 80px; }
.cp-doc {
  max-width: 860px; margin: 0 auto;
  padding: 48px;
  background: linear-gradient(150deg, rgba(13,21,48,0.85) 0%, rgba(9,18,32,0.85) 100%);
  border: 1px solid rgba(96,165,250,0.15);
  border-radius: 20px;
}
.cp-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 36px; font-weight: 600; line-height: 1.15;
  color: #e2edf8; margin: 0 0 32px;
}
.cp-heading {
  font-size: 14px; font-weight: 500; letter-spacing: 0.08em;
  color: #93c5fd; margin: 32px 0 12px;
}
.cp-text { font-size: 15px; line-height: 1.7; color: #a8c4e8; margin: 0 0 10px; }
.cp-note { font-size: 15px; color: #7a93b8; margin: 0; }

@media (max-width: 768px) {
  .cp-main { padding: 24px 16px 48px; }
  .cp-doc { padding: 28px 20px; }
  .cp-title { font-size: 26px; margin-bottom: 24px; }
  .cp-text { font-size: 14px; }
}
</style>

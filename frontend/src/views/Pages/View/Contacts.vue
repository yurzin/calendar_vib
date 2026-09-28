<script setup lang="ts">
import Header from '@/views/Pages/View/Components/Header.vue'
import Footer from '@/views/Pages/View/Components/Footer.vue'

// Адрес офиса и координаты метки на карте (долгота, широта)
const ADDRESS = 'г. Кемерово, ул. Кузбасская, 33А'
const COORDS: [number, number] = [86.072012, 55.360507]

// Виджет Яндекс.Карт работает без API-ключа
const [lon, lat] = COORDS
const mapSrc = `https://yandex.ru/map-widget/v1/?ll=${lon},${lat}&z=16&pt=${lon},${lat},pm2rdm`
</script>

<template>
  <div class="gl-wrap">
    <div class="gl-bg" aria-hidden="true">
      <div class="gl-orb gl-orb1" />
      <div class="gl-orb gl-orb2" />
      <div class="gl-grid" />
    </div>

    <Header />

    <main class="ct-main">
      <section class="ct-card">
        <h1 class="ct-title">Контакты</h1>

        <div class="ct-list">
          <a href="tel:+73842755555" class="ct-item">
            <div class="ct-icon">
              <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="ct-text">
              <span class="ct-label">Телефон</span>
              <span class="ct-value">+7 3842 75-55-55</span>
            </div>
          </a>

          <a href="mailto:pr@vse42.ru" class="ct-item">
            <div class="ct-icon">
              <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="ct-text">
              <span class="ct-label">E-mail</span>
              <span class="ct-value">pr@vse42.ru</span>
            </div>
          </a>

          <a href="https://vk.ru/governmentandbusiness" target="_blank" rel="noopener noreferrer" class="ct-item">
            <div class="ct-icon ct-icon--vk">VK</div>
            <div class="ct-text">
              <span class="ct-label">ВКонтакте</span>
              <span class="ct-value">vk.ru/governmentandbusiness</span>
            </div>
          </a>

          <div class="ct-item">
            <div class="ct-icon">
              <svg viewBox="0 0 24 24" fill="none" width="18" height="18">
                <path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                <circle cx="12" cy="9.5" r="2.5" stroke="currentColor" stroke-width="1.5"/>
              </svg>
            </div>
            <div class="ct-text">
              <span class="ct-label">Адрес</span>
              <span class="ct-value">{{ ADDRESS }}</span>
            </div>
          </div>
        </div>

        <div class="ct-map">
          <iframe :src="mapSrc" title="Карта: местонахождение" loading="lazy" allowfullscreen />
        </div>
      </section>
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

.ct-main { position: relative; z-index: 1; flex: 1; padding: 48px 24px 80px; }
.ct-card {
  max-width: 1080px; margin: 0 auto;
  padding: 48px;
  background: linear-gradient(150deg, rgba(13,21,48,0.85) 0%, rgba(9,18,32,0.85) 100%);
  border: 1px solid rgba(96,165,250,0.15);
  border-radius: 20px;
}
.ct-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 40px; font-weight: 600; line-height: 1.15;
  color: #e2edf8; margin: 0 0 32px;
}

.ct-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-bottom: 32px; }
.ct-item {
  display: flex; align-items: center; gap: 14px; min-width: 0;
  padding: 16px 18px;
  border: 1px solid rgba(96,165,250,0.12);
  border-radius: 14px;
  background: rgba(59,130,246,0.04);
  text-decoration: none;
  transition: border-color 0.2s, background 0.2s;
}
a.ct-item:hover { border-color: rgba(96,165,250,0.35); background: rgba(59,130,246,0.08); }
.ct-icon {
  width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0;
  background: rgba(59,130,246,0.1); border: 1px solid rgba(96,165,250,0.18);
  display: flex; align-items: center; justify-content: center; color: #93c5fd;
}
.ct-icon--vk { font-size: 13px; font-weight: 500; letter-spacing: 0.02em; }
.ct-text { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ct-label { font-size: 10px; letter-spacing: 0.12em; text-transform: uppercase; color: #4a6fa5; }
.ct-value { font-size: 16px; font-weight: 500; color: #a8c4e8; overflow-wrap: anywhere; }

.ct-map {
  height: 440px;
  border-radius: 14px;
  overflow: hidden;
  border: 1px solid rgba(96,165,250,0.15);
}
.ct-map iframe { width: 100%; height: 100%; border: 0; display: block; }

@media (max-width: 768px) {
  .ct-main { padding: 24px 16px 48px; }
  .ct-card { padding: 28px 20px; }
  .ct-title { font-size: 30px; margin-bottom: 24px; }
  .ct-list { grid-template-columns: 1fr; gap: 12px; margin-bottom: 24px; }
  .ct-value { font-size: 15px; }
  .ct-map { height: 320px; }
}
</style>

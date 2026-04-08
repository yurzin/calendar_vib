<script setup lang="ts">

</script>

<template>
  <!-- ─── Footer ───────────────────────────────────────── -->
  <footer class="gl-footer">
    <div class="gl-footer-inner">
      <div class="gl-footer-contacts">
        <a href="tel:+73842755555" class="gl-footer-contact">
          <div class="gl-footer-contact-icon">
            <svg viewBox="0 0 24 24" fill="none" width="16" height="16">
              <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="gl-footer-contact-text">
            <span class="gl-footer-contact-label">Звоните нам</span>
            <span class="gl-footer-contact-value">+7 3842 75-55-55</span>
          </div>
        </a>
        <a href="mailto:pr@vse42.ru" class="gl-footer-contact">
          <div class="gl-footer-contact-icon">
            <svg viewBox="0 0 24 24" fill="none" width="16" height="16">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M22 6l-10 7L2 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <div class="gl-footer-contact-text">
            <span class="gl-footer-contact-label">Пишите нам</span>
            <span class="gl-footer-contact-value">pr@vse42.ru</span>
          </div>
        </a>
      </div>
      <div class="gl-footer-copy">
        <router-link to="/" class="gl-footer-brand">
          <span class="gl-footer-brand-name">Власть и Бизнес</span>
        </router-link>
        <span class="gl-footer-copy-text">© 2017 – {{ new Date().getFullYear() }}</span>
      </div>
      <div class="gl-footer-links">
        <a href="#" class="gl-footer-link">Политика конфиденциальности</a>
        <span class="gl-sep">·</span>
        <a href="#" class="gl-footer-link">Поддержка</a>
      </div>
    </div>
  </footer>
</template>

<style scoped>

/* ═══════════════════════════════════════
   FOOTER
═══════════════════════════════════════ */
.gl-footer { position: relative; z-index: 10; border-top: 1px solid rgba(96,165,250,0.08); background: rgba(6,9,26,0.6); backdrop-filter: blur(12px); }
.gl-footer-inner { max-width: 1180px; margin: 0 auto; padding: 28px 64px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 24px; }
.gl-footer-contacts { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
.gl-footer-contact { display: flex; align-items: center; gap: 12px; text-decoration: none; transition: opacity 0.2s; }
.gl-footer-contact:hover { opacity: 0.75; }
.gl-footer-contact-icon { width: 36px; height: 36px; border-radius: 9px; background: rgba(59,130,246,0.1); border: 1px solid rgba(96,165,250,0.18); display: flex; align-items: center; justify-content: center; color: #93c5fd; flex-shrink: 0; }
.gl-footer-contact-text { display: flex; flex-direction: column; gap: 1px; }
.gl-footer-contact-label { font-size: 9px; letter-spacing: 0.12em; text-transform: uppercase; color: #2e4a73; font-weight: 300; }
.gl-footer-contact-value { font-size: 14px; font-weight: 500; color: #a8c4e8; letter-spacing: 0.02em; }
.gl-footer-copy { display: flex; flex-direction: column; align-items: center; gap: 4px; }
.gl-footer-brand { text-decoration: none; }
.gl-footer-brand-name { font-family: 'Cormorant Garamond', serif; font-size: 18px; font-weight: 600; color: #4a6fa5; letter-spacing: 0.04em; transition: color 0.2s; }
.gl-footer-brand:hover .gl-footer-brand-name { color: #93c5fd; }
.gl-footer-copy-text { font-size: 10px; color: #1e2d4a; letter-spacing: 0.08em; }
.gl-footer-links { display: flex; align-items: center; justify-content: flex-end; gap: 10px; font-size: 11px; color: #2a3f65; letter-spacing: 0.04em; }
.gl-sep { color: #1e2d4a; }
.gl-footer-link { color: #2a3f65; text-decoration: none; transition: color 0.2s; }
.gl-footer-link:hover { color: #4a7fd4; }

</style>

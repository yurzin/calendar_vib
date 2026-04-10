<script setup lang="ts">
import Grid from "@/views/Pages/View/Components/Grid.vue";
import Header from "@/views/Pages/View/Components/Header.vue";
import Footer from "@/views/Pages/View/Components/Footer.vue";
import Statistics from "@/views/Pages/View/Components/Statistics.vue";
import Hero from "@/views/Pages/View/Components/Hero.vue";
import Modal from "@/views/Pages/View/Components/Modal.vue";
import WantSee from "@/views/Pages/View/Components/WantSee.vue";
import {ref} from "vue";

const modalOpen = ref(false)
const formSent = ref(false)
function openModal() { modalOpen.value = true; formSent.value = false }

</script>

<template>
  <div class="gl-wrap">

    <!-- ─── Фон ──────────────────────────────────────────── -->
    <div class="gl-bg" aria-hidden="true">
      <div class="gl-orb gl-orb1" />
      <div class="gl-orb gl-orb2" />
      <div class="gl-orb gl-orb3" />
      <div class="gl-grid" />
    </div>

    <WantSee @open-modal="openModal" />

    <Header/>

    <Hero/>

    <Statistics @open-modal="openModal" />

    <Grid/>

    <Footer/>

    <Modal v-model="modalOpen" />

  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap');

*, *::before, *::after { box-sizing: border-box; }

/* ═══════════════════════════════════════
   ОСНОВА
═══════════════════════════════════════ */
.gl-wrap {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  background: #06091a;
  color: #dce8f5;
  display: flex;
  flex-direction: column;
  position: relative;
}

/* ═══════════════════════════════════════
   ФОН
═══════════════════════════════════════ */
.gl-bg { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
.gl-orb { position: absolute; border-radius: 50%; filter: blur(90px); }
.gl-orb1 { width: 700px; height: 700px; top: -300px; left: -200px; background: radial-gradient(circle, rgba(37,99,235,0.28), transparent 65%); }
.gl-orb2 { width: 500px; height: 500px; top: 30%; right: -150px; background: radial-gradient(circle, rgba(29,78,216,0.2), transparent 65%); }
.gl-orb3 { width: 400px; height: 400px; bottom: -100px; left: 30%; background: radial-gradient(circle, rgba(96,165,250,0.08), transparent 65%); }
.gl-grid { position: absolute; inset: 0; background-image: linear-gradient(rgba(96,165,250,0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(96,165,250,0.04) 1px, transparent 1px); background-size: 56px 56px; }

</style>

<template>
  <div class="guest-wrap">
    <div class="guest-card">
      <slot />
    </div>
  </div>
</template>

<style scoped>
.guest-wrap {
  min-height: 100vh;
  background: #06091a;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background-image:
    radial-gradient(ellipse at 20% 20%, rgba(37,99,235,0.15) 0%, transparent 50%),
    radial-gradient(ellipse at 80% 80%, rgba(29,78,216,0.1) 0%, transparent 50%);
}

.guest-card {
  width: 100%;
  max-width: 420px;
  background: linear-gradient(150deg, #0d1530 0%, #091220 100%);
  border: 1px solid rgba(96,165,250,0.18);
  border-radius: 20px;
  padding: 48px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,0.03),
    0 40px 100px rgba(0,0,0,0.7),
    inset 0 1px 0 rgba(147,197,253,0.08);
}
</style>

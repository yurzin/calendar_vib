<script setup lang="ts">
interface Tier {
  key: string
  path: string
  label: string
}

const props = defineProps<{
  current: string
}>()

const tiers: Tier[] = [
  {key: 'general-partner', path: '/general-partner#general-partner', label: 'Генеральный партнёр'},
  {key: 'official-partner', path: '/official-partner#official-partner', label: 'Официальный партнёр'},
  {key: 'business-partner', path: '/business-partner#business-partner', label: 'Деловой партнёр'},
  {key: 'project-participant', path: '/project-participant#project-participant', label: 'Участник проекта'}
]

const otherTiers = tiers.filter(t => t.key !== props.current)
</script>

<template>
  <nav class="tier-nav" aria-label="Другие форматы участия">
    <span class="tier-nav-label">Другие форматы участия:</span>
    <router-link
      v-for="tier in otherTiers"
      :key="tier.key"
      :to="tier.path"
      class="tier-nav-link"
    >
      {{ tier.label }} →
    </router-link>
  </nav>
</template>

<style scoped>
.tier-nav {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  margin-top: 24px;
  margin-bottom: 40px;
}

.tier-nav-label {
  font-size: 12px;
  color: #64748b;
  margin-right: 4px;
}

.tier-nav-link {
  font-size: 13px;
  color: #93c5fd;
  text-decoration: none;
  padding: 10px 18px;
  border-radius: 30px;
  border: 1px solid rgba(96, 165, 250, 0.2);
  background: rgba(13, 21, 48, 0.5);
  transition: border-color 0.2s, background 0.2s, color 0.2s;
}

.tier-nav-link:hover {
  border-color: rgba(147, 197, 253, 0.5);
  background: rgba(13, 21, 48, 0.8);
  color: #bfdbfe;
}
</style>

<script lang="ts">
// Виджет Yandex SmartCaptcha: https://yandex.cloud/ru/docs/smartcaptcha/concepts/widget-methods
// Отдаёт токен через v-model; пустая строка — капча не пройдена или токен истёк

// Скрипт загружаем один раз на всё приложение
let scriptPromise: Promise<SmartCaptchaApi> | null = null

function loadScript(): Promise<SmartCaptchaApi> {
  if (window.smartCaptcha) return Promise.resolve(window.smartCaptcha)
  if (scriptPromise) return scriptPromise
  scriptPromise = new Promise((resolve, reject) => {
    const script = document.createElement('script')
    script.src = 'https://smartcaptcha.yandexcloud.net/captcha.js?render=onload'
    script.async = true
    script.onload = () => window.smartCaptcha ? resolve(window.smartCaptcha) : reject(new Error('smartCaptcha not found'))
    script.onerror = () => {
      scriptPromise = null
      script.remove()
      reject(new Error('smartCaptcha script failed'))
    }
    document.head.appendChild(script)
  })
  return scriptPromise
}
</script>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps<{ sitekey: string }>()
const emit = defineEmits<{ (e: 'update:modelValue', token: string): void }>()

const container = ref<HTMLElement | null>(null)
const loadError = ref(false)
let widgetId: number | undefined

onMounted(async () => {
  try {
    const api = await loadScript()
    if (!container.value) return
    widgetId = api.render(container.value, {
      sitekey: props.sitekey,
      hl: 'ru',
      callback: (token) => emit('update:modelValue', token),
    })
    api.subscribe(widgetId, 'token-expired', () => emit('update:modelValue', ''))
  } catch {
    loadError.value = true
  }
})

onBeforeUnmount(() => {
  if (widgetId !== undefined) window.smartCaptcha?.destroy(widgetId)
})

// Токен одноразовый: после неудачной отправки капчу нужно пройти заново
function reset() {
  emit('update:modelValue', '')
  if (widgetId !== undefined) window.smartCaptcha?.reset(widgetId)
}

defineExpose({ reset })
</script>

<template>
  <div>
    <div ref="container" class="sc-widget" />
    <p v-if="loadError" class="sc-error">Не удалось загрузить проверку «Я не робот». Обновите страницу.</p>
  </div>
</template>

<style scoped>
.sc-widget { min-height: 102px; }
.sc-error { margin: 6px 0 0; font-size: 12px; color: #fca5a5; }
</style>

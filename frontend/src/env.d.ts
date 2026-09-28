/// <reference types="vite/client" />

interface ImportMetaEnv {
  readonly VITE_SMARTCAPTCHA_CLIENT_KEY?: string
}

// API виджета Yandex SmartCaptcha (скрипт captcha.js)
interface SmartCaptchaApi {
  render(container: HTMLElement, params: { sitekey: string; hl?: string; callback?: (token: string) => void }): number
  reset(widgetId?: number): void
  destroy(widgetId?: number): void
  subscribe(widgetId: number, event: string, cb: () => void): () => void
}

interface Window {
  smartCaptcha?: SmartCaptchaApi
}
